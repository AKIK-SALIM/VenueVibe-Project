<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Render the landing page
include 'LandingPage.html';
?>
