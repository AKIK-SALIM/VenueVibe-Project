<?php
$checks = [];

function addCheck(string $name, bool $pass, string $detail = ''): void {
    global $checks;
    $checks[] = ['name' => $name, 'pass' => $pass, 'detail' => $detail];
}

// 1. PHP version
$phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');
addCheck('PHP 7.4 or newer', $phpOk, 'Running PHP ' . PHP_VERSION);

// 2. PDO MySQL driver
$driverOk = extension_loaded('pdo_mysql');
addCheck('PDO MySQL driver loaded', $driverOk, $driverOk ? 'pdo_mysql available' : 'Enable pdo_mysql in php.ini');

// 3. Sessions
$sessionOk = false;
$sessionDetail = '';
if (session_status() === PHP_SESSION_NONE) {
    $sessionOk = (bool)@session_start();
} else {
    $sessionOk = true;
}
if ($sessionOk) {
    $_SESSION['vv_setup_check'] = 'ok';
    $sessionOk = ($_SESSION['vv_setup_check'] === 'ok');
    $sessionDetail = $sessionOk ? 'Sessions read/write fine' : 'Session data did not persist';
} else {
    $sessionDetail = 'session_start() failed (check session.save_path is writable)';
}
addCheck('PHP sessions working', $sessionOk, $sessionDetail);

// 4. DB connection (via project config)
require_once 'config.php'; // provides $pdo (or null) and $dbError
$dbOk = ($pdo !== null);
addCheck('Database connection (venuevibe)', $dbOk, $dbOk ? 'Connected via config.php' : ('Failed: ' . ($dbError ?? 'unknown error')));

// 5. Required tables
$expected = ['users', 'venues', 'equipment', 'schedule', 'reviews', 'requests', 'messages', 'notifications', 'announcements'];
if ($dbOk) {
    try {
        $found = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $foundLower = array_map('strtolower', $found);
        $missing = [];
        foreach ($expected as $t) {
            if (!in_array($t, $foundLower, true)) {
                $missing[] = $t;
            }
        }
        addCheck('All 9 tables exist', empty($missing), empty($missing) ? 'users, venues, equipment, schedule, reviews, requests, messages, notifications, announcements' : 'Missing: ' . implode(', ', $missing) . ' — import create_database.sql');
    } catch (Throwable $e) {
        addCheck('All 9 tables exist', false, 'SHOW TABLES failed: ' . $e->getMessage());
    }
} else {
    addCheck('All 9 tables exist', false, 'Skipped — no database connection');
}

// 6. Seed data
if ($dbOk) {
    try {
        $userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $venueCount = (int)$pdo->query('SELECT COUNT(*) FROM venues')->fetchColumn();
        $seedOk = ($userCount > 0 && $venueCount > 0);
        addCheck('Seed data imported', $seedOk, "users: {$userCount}, venues: {$venueCount}" . ($seedOk ? '' : ' — import insert_data.sql'));
    } catch (Throwable $e) {
        addCheck('Seed data imported', false, 'Count query failed: ' . $e->getMessage());
    }
} else {
    addCheck('Seed data imported', false, 'Skipped — no database connection');
}

// 7. Admin test account present
if ($dbOk) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'admin@uiu.ac.bd' AND is_admin = 1");
        $stmt->execute();
        $adminOk = ((int)$stmt->fetchColumn() > 0);
        addCheck('Admin test account exists', $adminOk, $adminOk ? 'admin@uiu.ac.bd is admin-ready' : 'Seed the admin user from insert_data.sql');
    } catch (Throwable $e) {
        addCheck('Admin test account exists', false, 'Query failed: ' . $e->getMessage());
    }
} else {
    addCheck('Admin test account exists', false, 'Skipped — no database connection');
}

// 8. Admin portal files in place
$adminIndex = __DIR__ . '/../Admin/index.php';
$adminOk = is_file($adminIndex);
addCheck('Admin portal present (../Admin/index.php)', $adminOk, $adminOk ? 'Found' : 'Place the Admin folder next to User (capital A)');

$passCount = 0;
foreach ($checks as $c) {
    if ($c['pass']) {
        $passCount++;
    }
}
$total = count($checks);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VenueVibe — Setup Check</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; background: #f3f4fb; color: #1d2939; margin: 0; padding: 32px 16px; }
    .box { max-width: 640px; margin: 0 auto; background: #fff; border: 1px solid #e5e9f2; border-radius: 16px; padding: 28px; }
    h1 { margin: 0 0 4px; font-size: 22px; }
    p.sub { margin: 0 0 20px; color: #64748b; font-size: 13px; }
    .row { display: flex; gap: 10px; align-items: flex-start; padding: 10px 0; border-top: 1px solid #f1f5f9; font-size: 14px; }
    .badge { flex-shrink: 0; font-size: 11px; font-weight: 700; border-radius: 20px; padding: 3px 10px; margin-top: 1px; }
    .ok { background: #e7f8ef; color: #067647; }
    .fail { background: #fdeef0; color: #e63956; }
    .detail { display: block; font-size: 12px; color: #64748b; font-weight: 400; margin-top: 2px; }
    .summary { margin-top: 18px; font-weight: 700; }
  </style>
</head>
<body>
  <div class="box">
    <h1>VenueVibe — Setup Check</h1>
    <p class="sub"><?= $passCount ?> of <?= $total ?> checks passed. Fix anything marked FAIL, then reload.</p>
    <?php foreach ($checks as $c): ?>
      <div class="row">
        <span class="badge <?= $c['pass'] ? 'ok' : 'fail' ?>"><?= $c['pass'] ? 'PASS' : 'FAIL' ?></span>
        <span><?= htmlspecialchars($c['name']) ?><span class="detail"><?= htmlspecialchars($c['detail']) ?></span></span>
      </div>
    <?php endforeach; ?>
    <p class="summary"><?= $passCount === $total ? 'All good — open LandingPage.php to start.' : 'Some checks failed — see the README troubleshooting table.' ?></p>
  </div>
</body>
</html>
