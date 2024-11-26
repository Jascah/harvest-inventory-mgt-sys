<?php
	
	require 'connection.php';
	require 'dbconnection.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;


	function sendMail($email,$reset_token){

		require('phpmailer/PHPMailer.php');
		require('phpmailer/SMTP.php');
		require('phpmailer/Exception.php');

		$mail = new PHPMailer(true);

		try {
    //Server settings
                        
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'pvraj74@gmail.com';                     //SMTP username
    $mail->Password   = 'efae qtbo dite daak';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('pvraj74@gmail.com', 'Vraj');
    $mail->addAddress($email);     //Add a recipient
    

    

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Password reset link';
    $mail->Body    = "Request for password reset <br>
    				<a href='http://localhost/teamproject/harvest_inventory_mngt_sys/updatepassword.php?email=$email&reset_token=$reset_token'>Reset</a>";
   
    $mail->send();
    return true;
	}	
	catch (Exception $e) {
    return false;
	}

}
if (isset($_POST['send-reset-link'])) {
    $email = $_POST['email'];
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset_token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $stmt = $con->prepare("UPDATE users SET resettoken = ?, resettokenexpire = ? WHERE email = ?");
        $stmt->bind_param("sss", $reset_token, $expiry, $email);

        if ($stmt->execute() && sendMail($email, $reset_token)) {
            echo "<script>
                    alert('Link successfully sent!');
                    window.location.href='sign_in.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to send reset link. Try again later.');
                    window.location.href='forgot_password.html';
                  </script>";
        }
    } else {
        echo "<script>
                alert('No account found with that email address.');
                window.location.href='forgot_password.html';
              </script>";
    }
}
?>

	

	/*if (isset($_POST['send-reset-link'])) {

		$email =$_POST['email'];
		$query="SELECT * FROM users WHERE email='$email'";
		$result=mysqli_query($con,$query);

		if($result){

			if(mysqli_num_rows($result)==1){

				$reset_token=bin2hex(random_bytes(16));
				date_default_timezone_set('Africa/Nairobi');
				$expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

				$query="UPDATE users SET resettoken='$reset_token',resettokenexpire='$expiry' WHERE email='$email'";

				if (mysqli_query($con, $query) && sendMail($email, $reset_token)) {

					echo"
						<script>
							alert('Link successfully sent!');
							window.location.href='sign_in.html';
						</script>
					";

					
				}
				else{
					echo"
						<script>
							alert('Try again later');
							window.location.href='sign_in.html';
						</script>
					";

				}

			}
			else{
				echo"
				<script>
					alert('This email was not found');
					window.location.href='sign_in.html';
				</script>
			";
			}

		}
		else{
			echo"
				<script>
					alert('cannot run query');
					window.location.href='sign_in.html';
				</script>
			";
		}
	}
?> */