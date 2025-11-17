<?php
/**
 * Authentication Configuration and Session Management
 * Handles user session initialization and security
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (30 minutes)
$session_timeout = 30 * 60;

// Check if session has timed out
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Session expired
    session_unset();
    session_destroy();
    
    if (isset($_SESSION['user_id'])) {
        unset($_SESSION['user_id']);
    }
}

// Check if authenticated user's status is still active in database
if (isAuthenticated()) {
    $user_id = getCurrentUserId();
    $user_status = getCurrentUserStatus();
    
    // If user status is inactive, logout immediately
    if ($user_status !== 'active') {
        session_unset();
        session_destroy();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Only redirect if we're not already on the login page
        if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
            $_SESSION['error_message'] = 'Your account has been deactivated. Please contact the administrator.';
            header('Location: ' . getBaseUrl() . '/index.php?logout=deactivated');
            exit();
        }
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current logged-in user username
 */
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Get current logged-in user role
 */
function getCurrentUserRole() {
    return isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
}

/**
 * Get current logged-in user full name
 */
function getCurrentUserFullName() {
    return isset($_SESSION['fullname']) ? $_SESSION['fullname'] : null;
}

/**
 * Get current logged-in user status
 */
function getCurrentUserStatus() {
    return isset($_SESSION['status']) ? $_SESSION['status'] : null;
}

/**
 * Check if user has specific role
 */
function hasRole($roleId) {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == $roleId;
}

/**
 * Check if user is active
 */
function isUserActive() {
    return getCurrentUserStatus() === 'active';
}

/**
 * Get base URL for redirects
 */
function getBaseUrl() {
    /** @var array $_SERVER */
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    
    // If we're in a subdirectory, go up to project root
    if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/App/') !== false) {
        $path = dirname(dirname($path));
    }
    
    return $protocol . '://' . $host . $path;
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isAuthenticated()) {
        header('Location: ../../index.php?auth=required');
        exit();
    }
}

/**
 * Redirect to login with custom message if not authenticated
 */
function requireLoginWithMessage($message = 'Please login to access this page') {
    if (!isAuthenticated()) {
        $_SESSION['error_message'] = $message;
        header('Location: ../../index.php');
        exit();
    }
}

/**
 * Store error message in session
 */
function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * Get and clear error message from session
 */
function getErrorMessage() {
    $message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
    if (isset($_SESSION['error_message'])) {
        unset($_SESSION['error_message']);
    }
    return $message;
}

/**
 * Store success message in session
 */
function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

/**
 * Get and clear success message from session
 */
function getSuccessMessage() {
    $message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
    if (isset($_SESSION['success_message'])) {
        unset($_SESSION['success_message']);
    }
    return $message;
}

/**
 * Logout user and destroy session
 */
function logout() {
    session_unset();
    session_destroy();
    
    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    header('Location: ../../index.php?logout=success');
    exit();
}
?>
