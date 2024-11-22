<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $updates = [];
    $params = [];
    $types = "";

    if (!empty($_POST['name'])) {
        $updates[] = "name = ?";
        $params[] = $_POST['name'];
        $_SESSION['name'] = $_POST['name'];
        $types .= "s";
    }
    if (!empty($_POST['email'])) {
        $updates[] = "email = ?";
        $params[] = $_POST['email'];
        $_SESSION['email'] = $_POST['email'];
        $types .= "s";
    }
    if (!empty($_POST['password'])) {
        $updates[] = "password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $types .= "s";
    }

    if (empty($updates)) {
        echo json_encode(['status' => 'error', 'message' => 'No fields to update.']);
        exit();
    }

    $params[] = $user_id;
    $types .= "i";

    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update settings.']);
    }
}
?>
