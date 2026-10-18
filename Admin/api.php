<?php
require_once __DIR__ . '/guard.php';

header('Content-Type: application/json');

function out($data) {
    echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    exit;
}

function body() {
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) {
        $raw = json_encode($_POST);
    }
    $j = json_decode($raw, true);
    if (is_array($j)) return $j;
    // also accept classic form posts
    if (!empty($_POST) && is_array($_POST)) return $_POST;
    return [];
}

if ($pdo === null) {
    out(['status' => 'error', 'message' => 'Database offline']);
}

$action = $_GET['action'] ?? (body()['action'] ?? '');

// Lazy auto-resolve long-pending bookings so dashboards stay current
try {
    require_once __DIR__ . '/../User/booking_manager.php';
    vvResolveExpiredSlots($pdo);
} catch (Throwable $e) {}

/* ---------------- DASHBOARD ---------------- */
if ($action === 'dashboard') {
    try {
        $totalBookings = (int)$pdo->query("SELECT COUNT(*) FROM requests")->fetchColumn();
        $pendingCount  = (int)$pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'Pending'")->fetchColumn();
        try {
            $totalVenues  = (int)$pdo->query("SELECT COUNT(*) FROM venues")->fetchColumn();
            $activeVenues = (int)$pdo->query("SELECT COUNT(*) FROM venues WHERE admin_status = 'Active'")->fetchColumn();
        } catch (Throwable $ve) {
            $totalVenues = 0;
            $activeVenues = 0;
        }

        $req = $pdo->query(
            "SELECT r.id, r.venue_name AS venue, r.booking_date, r.booking_time AS time_raw, r.status, r.created_at, r.attachment, r.attendee_email, r.attendee_notified,
                    u.full_name AS name, u.role, u.department
             FROM requests r LEFT JOIN users u ON u.id = r.user_id
             ORDER BY r.id DESC"
        )->fetchAll();

        $requests = [];
        foreach ($req as $r) {
            $name = $r['name'] ?: 'Unknown User';
            $parts = preg_split('/\s+/', trim($name));
            $initials = '';
            foreach ($parts as $p) { if ($p !== '') $initials .= strtoupper($p[0]); if (strlen($initials) >= 2) break; }
            $requests[] = [
                'id' => (int)$r['id'],
                'name' => $name,
                'role' => $r['role'] ? ucfirst($r['role']) : 'Guest',
                'initials' => $initials ?: 'U',
                'venue' => $r['venue'],
                'date' => date('M j, Y', strtotime($r['booking_date'])),
                'time' => $r['time_raw'] ?? '',
                'requested' => !empty($r['created_at']) ? date('M j, Y', strtotime($r['created_at'])) : '',
                'attachment' => $r['attachment'] ?? '',
                'attendeeEmail' => $r['attendee_email'] ?? '',
                'attendeeNotified' => (int)($r['attendee_notified'] ?? 0),
                'status' => strtolower($r['status']),
            ];
        }

        $notices = $pdo->query(
            "SELECT id, title, content AS body, time_text AS time FROM announcements ORDER BY id DESC LIMIT 4"
        )->fetchAll();

        // Chat users: everyone the admin has a conversation with, plus recent students
        $chatUsers = [];
        $rows = $pdo->query(
            "SELECT u.id, u.full_name, u.email, u.role
             FROM users u
             WHERE u.is_admin = 0
             ORDER BY u.id ASC LIMIT 8"
        )->fetchAll();
        foreach ($rows as $i => $u) {
            $last = $pdo->prepare(
                "SELECT text, time_sent, is_read FROM messages
                 WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
                 ORDER BY id DESC LIMIT 1"
            );
            $adm = (int)($_SESSION['user_id'] ?? 1);
            $last->execute([$u['id'], $adm, $adm, $u['id']]);
            $m = $last->fetch();
            $chatUsers[] = [
                'id' => (int)$u['id'],
                'name' => $u['full_name'],
                'message' => $m ? $m['text'] : 'No messages yet',
                'time' => $m ? $m['time_sent'] : '',
                'online' => true,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($u['full_name']) . '&background=4f39f6&color=fff&size=120',
            ];
        }

        // Calendar: days of the current month with at least one APPROVED booking
        $booked = [];
        $days = $pdo->query(
            "SELECT DISTINCT booking_date FROM requests
             WHERE status = 'Approved'
               AND booking_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
               AND booking_date <  DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01')"
        )->fetchAll(PDO::FETCH_COLUMN);
        foreach ($days as $d) { $booked[] = (int)date('j', strtotime($d)); }

        out([
            'status' => 'success',
            'totalBookings' => $totalBookings,
            'pendingCount' => $pendingCount,
            'totalVenues' => $totalVenues,
            'activeVenues' => $activeVenues,
            'requests' => $requests,
            'notices' => $notices,
            'chatUsers' => $chatUsers,
            'bookedDays' => $booked,
            'today' => (int)date('j'),
            'month' => (int)date('n') - 1,
        ]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/* ---------------- REQUESTS ---------------- */
if ($action === 'admin_notifications') {
    try {
        $rows = $pdo->query(
            "SELECT r.id, r.venue_name, r.booking_date, r.status, r.created_at, u.full_name AS requester
             FROM requests r LEFT JOIN users u ON u.id = r.user_id
             ORDER BY r.created_at DESC, r.id DESC LIMIT 12"
        )->fetchAll();
        out(['status' => 'success', 'requests' => $rows]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'requests') {
    try {
        $rows = $pdo->query(
            "SELECT r.id, r.venue_name AS venue, r.booking_date, r.booking_time AS time_raw, r.status, r.created_at, r.attachment, r.attendee_email, r.attendee_notified,
                    u.full_name AS name, u.role
             FROM requests r LEFT JOIN users u ON u.id = r.user_id
             ORDER BY r.id DESC"
        )->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $name = $r['name'] ?: 'Unknown User';
            $parts = preg_split('/\s+/', trim($name));
            $initials = '';
            foreach ($parts as $p) { if ($p !== '') $initials .= strtoupper($p[0]); if (strlen($initials) >= 2) break; }
            $out[] = [
                'id' => (int)$r['id'],
                'name' => $name,
                'role' => $r['role'] ? ucfirst($r['role']) : 'Guest',
                'initials' => $initials ?: 'U',
                'venue' => $r['venue'],
                'date' => date('M j, Y', strtotime($r['booking_date'])),
                'time' => $r['time_raw'] ?? '',
                'requested' => !empty($r['created_at']) ? date('M j, Y', strtotime($r['created_at'])) : '',
                'attachment' => $r['attachment'] ?? '',
                'attendeeEmail' => $r['attendee_email'] ?? '',
                'attendeeNotified' => (int)($r['attendee_notified'] ?? 0),
                'status' => strtolower($r['status']),
            ];
        }
        out(['status' => 'success', 'requests' => $out]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'request_status') {
    $in = body();
    $id = (int)($in['id'] ?? 0);
    $status = $in['status'] ?? '';
    if (!$id || !in_array($status, ['Approved', 'Rejected', 'Pending'], true)) {
        out(['status' => 'error', 'message' => 'Invalid input']);
    }
    try {
        $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        $getReq = $pdo->prepare("SELECT user_id, venue_name, booking_date FROM requests WHERE id = ?");
        $getReq->execute([$id]);
        $t = $getReq->fetch();
        if ($t && $t['user_id']) {
            $type = $status === 'Approved' ? 'approval' : 'rejection';
            $title = ($status === 'Approved' ? 'Booking Approved: ' : 'Booking Request Declined: ') . $t['venue_name'];
            $msg = "Your reservation for {$t['venue_name']} on {$t['booking_date']} " .
                   ($status === 'Approved' ? 'has been officially approved by Campus Administration.' : 'was declined by Campus Administration.');
            $n = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, time_ago, is_read) VALUES (?, ?, ?, ?, 'Just now', 0)");
            $n->execute([$t['user_id'], $title, $msg, $type]);
        }
        out(['status' => 'success']);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/* ---------------- VENUES ---------------- */
if ($action === 'venues') {
    try {
        $rows = $pdo->query("SELECT * FROM venues ORDER BY name")->fetchAll();
        $venues = [];
        foreach ($rows as $v) {
            $venues[] = [
                'id' => $v['id'],
                'name' => $v['name'],
                'suiteCode' => $v['suite_code'] ?: $v['name'],
                'quad' => $v['quad'],
                'building' => $v['building'],
                'floor' => $v['floor'],
                'location' => $v['location'],
                'type' => $v['type'],
                'status' => $v['admin_status'],
                'capacity' => $v['capacity'] . ' seats',
                'setup' => $v['setup'],
                'slots' => $v['slots'],
                'image' => $v['thumbnail'],
                'attendeeEmail' => $v['attendee_email'] ?? '',
            ];
        }
        out(['status' => 'success', 'venues' => $venues]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'venue_save') {
    $in = body();
    $id       = trim($in['id'] ?? '');
    $name     = trim($in['name'] ?? '');
    $quad     = trim($in['quad'] ?? 'North Academic Quad');
    $building = trim($in['building'] ?? '');
    $floor    = trim($in['floor'] ?? '');
    $room     = trim($in['room'] ?? '');
    $seated   = (int)($in['seated'] ?? 100);
    $standing = (int)($in['standing'] ?? 0);
    $image    = trim($in['image'] ?? '');
    $attendee = trim($in['attendee_email'] ?? '');
    $type     = trim($in['type'] ?? 'Seminar Room');
    $status   = ($in['status'] ?? 'Active') === 'Active' ? 'Active' : 'Draft';
    $setup    = trim($in['setup'] ?? 'Standard Config');

    if ($name === '') out(['status' => 'error', 'message' => 'Venue name is required']);
    if ($attendee !== '' && !filter_var($attendee, FILTER_VALIDATE_EMAIL)) {
        out(['status' => 'error', 'message' => 'Attendee email is not a valid email address']);
    }

    try {
        if ($id !== '') {
            if ($image !== '') {
                $stmt = $pdo->prepare(
                    "UPDATE venues SET name=?, quad=?, building=?, floor=?, suite_code=?, capacity=?, standing=?, type=?, admin_status=?, setup=?, location=?, image=?, thumbnail=?, attendee_email=? WHERE id=?"
                );
                $stmt->execute([$name, $quad, $building, $floor, $room, $seated, $standing, $type, $status, $setup, trim("$quad, $building", ', '), $image, $image, $attendee, $id]);
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE venues SET name=?, quad=?, building=?, floor=?, suite_code=?, capacity=?, standing=?, type=?, admin_status=?, setup=?, location=?, attendee_email=? WHERE id=?"
                );
                $stmt->execute([$name, $quad, $building, $floor, $room, $seated, $standing, $type, $status, $setup, trim("$quad, $building", ', '), $attendee, $id]);
            }
            out(['status' => 'success', 'id' => $id]);
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        if ($slug === '') $slug = 'venue';
        $newId = $slug;
        $i = 2;
        while (true) {
            $chk = $pdo->prepare("SELECT COUNT(*) FROM venues WHERE id = ?");
            $chk->execute([$newId]);
            if ((int)$chk->fetchColumn() === 0) break;
            $newId = $slug . '-' . $i++;
        }

        $img = $image !== '' ? $image : 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80';
        $stmt = $pdo->prepare(
            "INSERT INTO venues (id, name, type, location, quad, building, floor, suite_code, capacity, standing, infrastructure, setup, slots, rating, status, admin_status, image, thumbnail, about, attendee_email)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Normal', ?, 'Open Access', 0.0, 'Available', ?, ?, ?, '', ?)"
        );
        $stmt->execute([$newId, $name, $type, trim("$quad, $building", ', '), $quad, $building, $floor, $room, $seated, $standing, $setup, $status, $img, $img, $attendee]);
        out(['status' => 'success', 'id' => $newId]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'venue_media_upload') {
    $paths = [];
    $uploadDir = __DIR__ . '/../User/uploads/';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }
    if (!empty($_FILES['files']['tmp_name'])) {
        if (!is_array($_FILES['files']['tmp_name'])) { $_FILES['files']['tmp_name'] = [$_FILES['files']['tmp_name']]; $_FILES['files']['name'] = [$_FILES['files']['name']]; }
        foreach ($_FILES['files']['tmp_name'] as $idx => $tmp) {
            if ($tmp && is_uploaded_file($tmp)) {
                $name = basename($_FILES['files']['name'][$idx]);
                $safe = 'venue_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
                if (move_uploaded_file($tmp, $uploadDir . $safe)) {
                    $paths[] = 'uploads/' . $safe;
                }
            }
        }
    }
    out(['status' => 'success', 'media' => $paths]);
}

if ($action === 'venue_delete') {
    $in = body();
    $id = trim($in['id'] ?? '');
    if ($id === '') out(['status' => 'error', 'message' => 'Missing id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM venues WHERE id = ?");
        $stmt->execute([$id]);
        out(['status' => 'success']);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/* ---------------- ANNOUNCEMENTS ---------------- */
if ($action === 'announcements') {
    try {
        $rows = $pdo->query("SELECT * FROM announcements ORDER BY id DESC")->fetchAll();
        $list = [];
        foreach ($rows as $a) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $a['tags'] ?? ''))));
            if (empty($tags)) $tags = [$a['category'], $a['priority']];
            $list[] = [
                'id' => (string)$a['id'],
                'title' => $a['title'],
                'content' => $a['content'],
                'category' => $a['category'],
                'priority' => $a['priority'],
                'status' => $a['status'],
                'statusLabel' => $a['status_label'],
                'audience' => $a['audience'],
                'time' => $a['time_text'],
                'author' => $a['author'],
                'reads' => $a['reads_count'],
                'delivery' => $a['delivery'],
                'tags' => $tags,
            ];
        }
        out(['status' => 'success', 'announcements' => $list]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'announcement_create') {
    $in = body();
    $title = trim($in['title'] ?? '');
    $content = trim($in['content'] ?? '');
    if ($title === '' || $content === '') out(['status' => 'error', 'message' => 'Title and content required']);
    $category = trim($in['category'] ?? 'Event Notice');
    $priority = trim($in['priority'] ?? 'Normal');
    $audience = trim($in['audience'] ?? 'All Faculty & Students IC');
    $sched    = trim($in['time'] ?? '');
    $status   = $sched !== '' ? 'Scheduled' : 'Active';
    $label    = $sched !== '' ? ('Scheduled: ' . $sched) : 'Active / Live';
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO announcements (title, content, category, priority, status, status_label, audience, time_text, author, reads_count, delivery, tags)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Admin User', '0', '100%', ?)"
        );
        $stmt->execute([$title, $content, $category, $priority, $status, $label, $audience, $sched !== '' ? $sched : date('M j, Y • h:i A'), $category . ',' . $priority]);

        // Also broadcast to every user's notification feed
        $n = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, time_ago, is_read) VALUES (NULL, ?, ?, 'general', 'Just now', 0)");
        $n->execute([$title, $content]);

        out(['status' => 'success', 'id' => (string)$pdo->lastInsertId()]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'announcement_delete') {
    $in = body();
    $id = (int)($in['id'] ?? 0);
    if (!$id) out(['status' => 'error', 'message' => 'Missing id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        out(['status' => 'success']);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

/* ---------------- CHAT ---------------- */
if ($action === 'contacts') {
    $adm = (int)($_SESSION['user_id'] ?? 1);
    try {
        $rows = $pdo->query(
            "SELECT u.id, u.full_name, u.email, u.role, u.department,
                    (SELECT text FROM messages m
                     WHERE (m.sender_id = u.id AND m.receiver_id = {$adm}) OR (m.sender_id = {$adm} AND m.receiver_id = u.id)
                     ORDER BY m.id DESC LIMIT 1) AS last_text,
                    (SELECT time_sent FROM messages m
                     WHERE (m.sender_id = u.id AND m.receiver_id = {$adm}) OR (m.sender_id = {$adm} AND m.receiver_id = u.id)
                     ORDER BY m.id DESC LIMIT 1) AS last_time,
                    (SELECT created_at FROM messages m
                     WHERE (m.sender_id = u.id AND m.receiver_id = {$adm}) OR (m.sender_id = {$adm} AND m.receiver_id = u.id)
                     ORDER BY m.id DESC LIMIT 1) AS last_at,
                    (SELECT COUNT(*) FROM messages m
                     WHERE m.sender_id = u.id AND m.receiver_id = {$adm} AND m.is_read = 0) AS unread_count
             FROM users u
             WHERE u.is_admin = 0
             ORDER BY u.full_name ASC"
        )->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => (int)$r['id'],
                'name' => $r['full_name'],
                'email' => $r['email'],
                'role' => ucfirst($r['role']),
                'department' => $r['department'],
                'last_text' => $r['last_text'] ?: 'No messages yet',
                'last_time' => $r['last_time'] ?: '',
                'last_at' => $r['last_at'] ?: '',
                'unread_count' => (int)$r['unread_count'],
            ];
        }
        out(['status' => 'success', 'contacts' => $out]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'chat_messages') {
    $other = (int)($_GET['user_id'] ?? 0);
    $adm = (int)($_SESSION['user_id'] ?? 1);
    if (!$other) out(['status' => 'error', 'message' => 'Missing user_id']);
    try {
        $stmt = $pdo->prepare(
            "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY id ASC"
        );
        $stmt->execute([$other, $adm, $adm, $other]);
        $msgs = [];
        foreach ($stmt->fetchAll() as $m) {
            $msgs[] = [
                'who' => ((int)$m['sender_id'] === $adm) ? 'me' : 'them',
                'text' => $m['text'],
                'time' => $m['time_sent'],
            ];
        }
        // mark incoming as read
        $u = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        $u->execute([$other, $adm]);
        out(['status' => 'success', 'messages' => $msgs]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'chat_send') {
    $in = body();
    $other = (int)($in['receiver_id'] ?? 0);
    $text = trim($in['text'] ?? '');
    $adm = (int)($_SESSION['user_id'] ?? 1);
    $name = $_SESSION['full_name'] ?? 'Admin';
    if (!$other || $text === '') out(['status' => 'error', 'message' => 'Missing receiver or text']);
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, sender_name, text, time_sent, is_read) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$adm, $other, $name, $text, date('h:i A')]);
        out(['status' => 'success']);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'calendar') {
    try {
        $days = $pdo->query(
            "SELECT DISTINCT booking_date FROM requests
             WHERE status = 'Approved'
               AND booking_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
               AND booking_date <  DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01')"
        )->fetchAll(PDO::FETCH_COLUMN);
        $booked = [];
        foreach ($days as $d) { $booked[] = (int)date('j', strtotime($d)); }
        out(['status' => 'success', 'bookedDays' => $booked, 'today' => (int)date('j'), 'month' => (int)date('n') - 1]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

if ($action === 'day_events') {
    $date = $_GET['date'] ?? '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        out(['status' => 'error', 'message' => 'Invalid date']);
    }
    try {
        $stmt = $pdo->prepare(
            "SELECT r.venue_name, r.booking_time, r.purpose, r.status, u.full_name AS requester
             FROM requests r LEFT JOIN users u ON u.id = r.user_id
             WHERE r.booking_date = ? AND r.status = 'Approved' ORDER BY r.booking_time ASC"
        );
        $stmt->execute([$date]);
        out(['status' => 'success', 'events' => $stmt->fetchAll()]);
    } catch (Throwable $e) {
        out(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

out(['status' => 'error', 'message' => 'Unknown action']);
