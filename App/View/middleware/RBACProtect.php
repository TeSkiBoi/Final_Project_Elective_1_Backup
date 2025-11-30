<?php
/**
 * Role-Based Access Control (RBAC) Middleware
 * Restricts page access based on user roles
 */

require_once __DIR__ . '/../../Config/Auth.php';

/**
 * Define role permissions for pages
 * Format: 'page_name.php' => [allowed_role_ids]
 * Role IDs: 1=Admin, 2=Staff, 3=Student
 */
$pagePermissions = [
    // Admin-only pages (role_id 1)
    'role.php' => [1],
    'user.php' => [1],
    'barangay_officials.php' => [1],
    'blotter.php' => [1],
    'certificate_generator.php' => [1],
    
    // Admin and Staff pages - VIEW ONLY for Staff (role_id 1, 2)
    'household.php' => [1, 2],
    'Resident.php' => [1, 2],
    'children.php' => [1, 2],
    'senior.php' => [1, 2],
    'adult.php' => [1, 2],
    
    // Admin and Staff pages - EDIT ALLOWED for Staff (role_id 1, 2)
    'projects.php' => [1, 2],
    'financial.php' => [1, 2],
    
    // Everyone (all authenticated users)
    'index.php' => [1, 2, 3],
    'activitylog.php' => [1, 2, 3],
    'profilesetting.php' => [1, 2, 3],
];

/**
 * Check if user has access to the current page
 */
function checkPageAccess() {
    // Get the current page filename
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Get user role from session
    $userRole = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
    
    // If page has no restrictions, allow access
    global $pagePermissions;
    if (!isset($pagePermissions[$currentPage])) {
        return true; // No restrictions defined
    }
    
    // Get allowed roles for this page
    $allowedRoles = $pagePermissions[$currentPage];
    
    // Check if user's role is in allowed roles
    if (in_array($userRole, $allowedRoles)) {
        return true; // Access granted
    }
    
    // Access denied
    return false;
}

/**
 * Deny access and redirect with error message
 */
function denyAccess() {
    $_SESSION['error_message'] = 'Access Denied! You do not have permission to access this page.';
    header('Location: index.php');
    exit();
}

/**
 * Main RBAC Check
 * Call this at the top of protected pages
 */
function requireRole(...$allowedRoles) {
    // Check if user is authenticated
    if (!isAuthenticated()) {
        header('Location: ../../index.php?auth=required');
        exit();
    }
    
    // Get user role
    $userRole = getCurrentUserRole();
    
    // Check if user's role is allowed
    if (!in_array($userRole, $allowedRoles)) {
        $_SESSION['error_message'] = 'Access Denied! Your role does not have permission to access this page.';
        //echo 'Access Denied! Your role does not have permission to access this page.';
        header('Location: index.php');
        exit();
    }
}

/**
 * Check multiple role requirements
 */
function requireAnyRole(...$allowedRoles) {
    requireRole(...$allowedRoles);
}

/**
 * Get role name from role ID
 */
function getRoleName($roleId) {
    $roles = [
        1 => 'Admin',
        2 => 'Staff',
        3 => 'Student',
    ];
    
    return isset($roles[$roleId]) ? $roles[$roleId] : 'Unknown';
}

/**
 * Perform automatic RBAC check for current page
 */
if (!checkPageAccess()) {
    denyAccess();
}
?>
