<?php
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session and redirect
session_destroy();
header('Location: index.php');
exit();
?>
