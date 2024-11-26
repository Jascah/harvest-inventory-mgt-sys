<?php
session_start();
include 'connection.php'; // Include your database connection
include 'config.php'; // Additional configurations if any

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Securely capture and sanitize user inputs
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Fetch user details including role
    $query = "SELECT id, name, role, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($con, $query); // Use prepared statements to prevent SQL injection
    mysqli_stmt_bind_param($stmt, "s", $email); // Bind the email parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password securely
        if (password_verify($password, $user['password'])) {
            // Store user details in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role']; // Store role in session
            $_SESSION['logged_in'] = true;

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Redirect back to login with an error
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: sign_in.html');
            exit;
        }
    } else {
        // User not found
        $_SESSION['error'] = "User not found.";
        header('Location: sign_in.html');
        exit;
    }
} else {
    // If accessed directly, redirect to login
    header('Location: sign_in.html');
    exit;
}
