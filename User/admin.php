<?php
/* ============================================================
   VenueVibe - admin.php
   Session gate for the Admin portal (../Admin/index.php).

   Only logged-in admin accounts may pass through. Everyone
   else is sent back to the login page. The Admin pages are
   static HTML, so this gate is the checkpoint reached right
   after a successful admin login.
   ============================================================ */

require_once 'config.php';

$isAdmin = !empty($_SESSION['is_admin']) || (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin');

if (empty($_SESSION['user_id']) || !$isAdmin) {
    header('Location: login.php');
    exit;
}

header('Location: ../Admin/index.php');
exit;
?>
