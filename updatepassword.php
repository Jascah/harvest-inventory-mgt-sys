<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Password Update</title>
	<style>
		*{
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			text-decoration: none;
			font-family: 'poppins', sans-serif;
			}

		body{

			display: flex;
			justify-content: center;
			align-items:center;
			height:100vh;
			background: linear-gradient(120deg,#667eea, #764ba2);



			}
		form{
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%,-50%);
			background-color: #f0f0f0;
			width: 350px;
			border-radius: 5px;
			padding: 20px 25px 30px 25px;
			}
		
			
		form h3{
			margin-bottom: 14px;
			color: blueviolet;
		}
		form input[type="password"]{
			  width: 100%;
			  margin-bottom: 20px;
			  background-color: transparent;
			  border: none;
			  border-bottom: 2px solid #30475e;
			  border-radius: 0;
			  padding: 5px 0;
			  font-weight: 550;
			  font-size: 14px;
			  outline: none;
		}
		form button{
			  font-weight: 550;
			  font-style: 15px;
			  color: white;
			  background-color: #30475e;
			  padding: 4px 10px;
			  border: none;
			  outline: none;
		}
	</style>
</head>
<body>

	<?php

		require("dbconnection.php");
		require 'connection.php';

		if (isset($_GET['email']) && isset($_GET['reset_token'])) {
			$email = mysqli_real_escape_string($con, $_GET['email']);
			$reset_token = mysqli_real_escape_string($con, $_GET['reset_token']);
			
			$current_date = date("Y-m-d H:i:s");

			$query = "SELECT * FROM users WHERE email = '$email' AND resettoken = '$reset_token' AND resettokenexpire > '$current_date'";
			$result = mysqli_query($con, $query);

			if ($result && mysqli_num_rows($result) == 1) {
				echo "
					<form method='POST'>
						<h3>Create new password</h3>
						<input type='password' placeholder='New password' name='Password' required>
						<button type='submit' name='updatepassword'>UPDATE</button>
						<input type='hidden' name='email' value='$email'>
					</form>
				";
			} else {
				echo "
					<script>
						alert('Invalid or expired link.');
						window.location.href='sign_in.html';
					</script>
				";
			}
		}

		if (isset($_POST['updatepassword'])) {
			$new_password = password_hash($_POST['Password'], PASSWORD_BCRYPT);
			$email = mysqli_real_escape_string($con, $_POST['email']);

			$update_query = "UPDATE users SET password = '$new_password', resettoken = NULL, resettokenexpire = NULL WHERE email = '$email'";

			if (mysqli_query($con, $update_query)) {
				echo "
					<script>
						alert('Password Updated!');
						window.location.href='sign_in.html';
					</script>
				";
			} else {
				echo "
					<script>
						alert('Server error. Could not update password.');
						window.location.href='sign_in.html';
					</script>
				";
			}
		}
?>

</body>
</html>