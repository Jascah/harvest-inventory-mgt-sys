<?php
include 'connection.php';
include('rolecheck.php');
checkRole(['admin']); // Only admins can access this page

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $query = "UPDATE users SET role = '$new_role' WHERE id = '$user_id'";
    if (mysqli_query($con, $query)) {
        echo "Role updated successfully.";
    } else {
        echo "Error updating role: " . mysqli_error($con);
    }
}

// Fetch all users
$query = "SELECT id, name, email, role FROM users";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Manage Roles</title>
</head>
<body>
    <h1>Manage User Roles</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                    <select name="role">
                        <option value="staff" <?= $row['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                        <option value="manager" <?= $row['role'] == 'manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <button type="submit">Update Role</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
