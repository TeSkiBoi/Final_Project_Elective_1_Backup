<?php
/**
 * Authentication Protection Middleware
 * This file should be included at the beginning of all protected View files
 */

require_once __DIR__ . '/../../Config/Auth.php';

// Check if user is authenticated
if (!isAuthenticated()) {
    // User is not logged in, redirect to login page
    header('Location: ../../index.php?auth=required');
    exit();
}
?>
