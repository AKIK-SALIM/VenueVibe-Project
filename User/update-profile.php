<?php
require_once 'config.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please log in first.']);
        exit;
    }
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName   = trim($_POST['full_name'] ?? '');
    $studentId  = trim($_POST['student_id'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $email      = trim($_POST['email'] ?? '');

    if ($fullName === '' || $email === '') {
        $error = 'Full name and email are required.';
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? 'user.php';
        header('Location: ' . $referer);
        exit;
    }

    if ($pdo) {
        try {
            $stmt = $pdo->prepare('UPDATE users SET full_name = ?, student_id = ?, department = ?, email = ? WHERE id = ?');
            $stmt->execute([$fullName, $studentId, $department, $email, $userId]);
        } catch (Throwable $e) {
            // DB update fallback
        }
    }

    // Update active session values
    $_SESSION['full_name']  = $fullName;
    $_SESSION['user_name']  = $fullName;
    $_SESSION['student_id'] = $studentId;
    $_SESSION['department'] = $department;
    $_SESSION['email']      = $email;
    $_SESSION['user_email'] = $email;
    // role is intentionally NOT editable from the profile form
    // (prevents privilege escalation); it stays as in the database.

    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'data' => [
                'name' => $fullName,
                'student_id' => $studentId,
                'department' => $department,
                'email' => $email,
                'role' => ucfirst($_SESSION['role'] ?? 'student')
            ]
        ]);
        exit;
    }

    $referer = $_SERVER['HTTP_REFERER'] ?? 'user.php';
    header('Location: ' . $referer);
    exit;
}

header('Location: user.php');
exit;
