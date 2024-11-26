<?php
function checkRole($allowed_roles) {
    session_start();

    // Ensure the user's role is in the allowed roles
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
    }
}
?>
