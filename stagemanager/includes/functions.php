<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure session is properly configured
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Require login - redirect to login page if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Check user role
function checkRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Require specific role
function requireRole($role) {
    requireLogin();
    if (!checkRole($role)) {
        header('Location: index.php');
        exit();
    }
}

// Set flash message
function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

// Display and clear flash message
function displayFlash() {
    if (isset($_SESSION['flash'])) {
        $alertClass = $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'];
        echo '<div class="alert alert-' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo sanitize($_SESSION['flash']['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        unset($_SESSION['flash']);
    }
}

// Get current user info
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Format date for display
function formatDate($date) {
    return date('d/m/Y à H:i', strtotime($date));
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate secure filename for uploads
function generateSecureFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

// Check if file upload is valid
function isValidUpload($file, $allowedTypes = ['pdf', 'doc', 'docx'], $maxSize = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $fileSize = $file['size'];
    $fileName = $file['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if ($fileSize > $maxSize) {
        return false;
    }
    
    if (!in_array($fileExtension, $allowedTypes)) {
        return false;
    }
    
    return true;
}
?>
