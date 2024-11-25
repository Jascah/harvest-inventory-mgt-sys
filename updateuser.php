<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Get user ID from session
    $updates = [];
    $params = [];
    $types = "";

    // Update name
    if (!empty($_POST['name'])) {
        $updates[] = "name = ?";
        $params[] = $_POST['name'];
        $types .= "s";
    }

    // Update email
    if (!empty($_POST['email'])) {
        $updates[] = "email = ?";
        $params[] = $_POST['email'];
        $types .= "s";
    }

    // Update password
    if (!empty($_POST['password'])) {
        $updates[] = "password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $types .= "s";
    }

    // Ensure at least one field is being updated
    if (empty($updates)) {
        echo json_encode(['status' => 'error', 'message' => 'No fields to update.']);
        exit();
    }

    // Add user ID for the WHERE clause
    $params[] = $user_id;
    $types .= "i";

    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'User information updated.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user information.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement.']);
    }
}
?>
