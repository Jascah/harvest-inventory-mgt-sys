<?php
session_start();
include('connection.php'); // Ensure this file initializes $conn for mysqli connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['Name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : 'staff'; // Default role is 'staff'

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Prepare the SQL query using MySQLi
        $stmt = $con->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User registered successfully.";
            
            header('Location: sign_up.html'); // Redirect to the sign-up page or success page
            exit;
        } else {
            throw new Exception("Failed to register user.");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('Location: sign_up.html'); // Redirect back to the sign-up page with an error
        exit;
    }
} else {
    header('Location: sign_up.html'); // Redirect if accessed directly
    exit;
}
?>
