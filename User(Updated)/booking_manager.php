<?php
function vvUserPriority(string $role): int {
    switch (strtolower(trim($role))) {
        case 'dean':    return 3;
        case 'faculty': return 2;
        case 'student': return 1;
        case 'admin':   return 99;
        default:        return 0;
    }
}

function vvNotify(PDO $pdo, int $userId, string $title, string $message, string $type) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, time_ago, is_read) VALUES (?, ?, ?, ?, 'Just now', 0)");
        $stmt->execute([$userId, $title, $message, $type]);
    } catch (Throwable $e) {
        // non-critical
    }
}

/**
 * Re-resolve a given slot: pick the winning request (highest user
 * priority, then oldest created_at) and approve it; reject every
 * other *pending* request in the same slot. Running twice is safe.
 */
function vvAutoManageSlot(PDO $pdo, string $venueName, string $bookingDate, string $bookingTime) {
    $stmt = $pdo->prepare("SELECT r.id, r.user_id, r.status, r.created_at, u.role, u.full_name
            FROM requests r LEFT JOIN users u ON u.id = r.user_id
            WHERE r.venue_name = ? AND r.booking_date = ? AND r.booking_time = ?
            ORDER BY r.created_at ASC, r.id ASC");
    $stmt->execute([$venueName, $bookingDate, $bookingTime]);
    $all = $stmt->fetchAll();

    $candidates = [];
    foreach ($all as $r) {
        if (strtolower($r['status']) === 'pending' || strtolower($r['status']) === 'approved') {
            $candidates[] = $r;
        }
    }
    if (count($candidates) === 0) { return; }

    // If a dean is in the competition: dean has no pending period -> approved immediately
    foreach ($candidates as $r) {
        if (strtolower(trim($r['role'] ?? '')) === 'dean' && strtolower($r['status']) === 'pending') {
            return vvResolveSlotByWinner($pdo, $candidates, $r);
        }
    }

    // Otherwise each request keeps a 1-hour pending window; after that, resolve
    $earliest = $candidates[0]['created_at'];
    if ((time() - strtotime($earliest)) < 3600) {
        return; // still inside the 1-hour pending window
    }

    usort($candidates, function ($a, $b) {
        $pa = vvUserPriority($a['role'] ?? '');
        $pb = vvUserPriority($b['role'] ?? '');
        if ($pa !== $pb) { return $pb <=> $pa; }
        if ($a['created_at'] !== $b['created_at']) { return strcmp($a['created_at'], $b['created_at']); }
        return ((int)$a['id']) <=> ((int)$b['id']);
    });

    vvResolveSlotByWinner($pdo, $candidates, $candidates[0]);
}

function vvResolveSlotByWinner(PDO $pdo, array $candidates, array $winner) {
    foreach ($candidates as $r) {
        $newStatus = ($r['id'] === $winner['id']) ? 'Approved' : 'Rejected';
        if (strtolower($r['status']) === strtolower($newStatus)) { continue; }

        $up = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $up->execute([$newStatus, $r['id']]);

        if (!empty($r['user_id'])) {
            $venueName    = NULL;
            $getVenue = $pdo->prepare("SELECT venue_name, booking_date, booking_time FROM requests WHERE id = ?");
            $getVenue->execute([$r['id']]);
            $slot = $getVenue->fetch();
            $venueName  = $slot['venue_name']     ?? 'a venue';
            $bDate      = $slot['booking_date']   ?? '';
            $bTime      = $slot['booking_time']   ?? '';

            if ($newStatus === 'Approved') {
                vvNotify($pdo, (int)$r['user_id'],
                    "Booking Confirmed: {$venueName}",
                    "Your booking request for {$venueName} on {$bDate} ({$bTime}) has been approved by the scheduler.",
                    'approval');
            } else {
                vvNotify($pdo, (int)$r['user_id'],
                    "Booking Request Declined: {$venueName}",
                    "Your request for {$venueName} on {$bDate} ({$bTime}) was declined (priority/first-come rule).",
                    'rejection');
            }
        }
    }
}

/**
 * Resolve all slots with matured requests
 */
function vvResolveExpiredSlots(PDO $pdo) {
    $stmt = $pdo->query("SELECT DISTINCT venue_name, booking_date, booking_time FROM requests WHERE status = 'Pending' AND created_at <= NOW() - INTERVAL 1 HOUR");
    foreach ($stmt->fetchAll() as $s) {
        vvAutoManageSlot($pdo, $s['venue_name'], $s['booking_date'], $s['booking_time']);
    }
}

