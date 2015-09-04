<?php
	
	function sendAccountCreationEmail($userName, $firstName, $lastName, $password) {
		// the message
		$msg = "Hello ".$firstName." ".$lastName. ", \n\n";
		$msg = $msg . "Thanks you for creating an account on Michigan Science Olympiad's Score Center. You will now be able to enter scores for events 
		assigned to you. If you are a score verifier, you will be able to enter scores for entire tournaments. You may access Score Center as the follow 
		address with the user name and password below.\n\n\n";
		
		// Select url from db
		$msg = $msg . "URL: " . "http://www.prestonsproductions.com/scorecenter \n";
		$msg = $msg . "User Name: " .$userName." \n";
		$msg = $msg . "Password: " .$password." \n";

		// use wordwrap() if lines are longer than 70 characters
		//$msg = wordwrap($msg,70);

		// send email
		mail($userName,"Score Center Account Creation",$msg);
	
	
	}

	function emailPasswordReset($address, $name, $userId, $encryptedPassword, $salt) {
		$host = $_SERVER['HTTP_HOST'];
		$msg = "Hello ".$name.", \n\n";
		$msg = $msg . "A password reset for account ". $address . " has been requested from the Science Olympiad Score Center application. To reset your password, select the hyperlink below and update your password on the account screen. If this message was sent in error, please disregard this email. \n\n\n";
		$msg = $msg . "Reset Password Link: \n http://".$host."/scorecenter/controller.php?command=passwordResetProcess&id=".$userId."&ep=".$encryptedPassword."&sa=".$salt."&nn=".uniqid();
	
	
		mail($address,"Score Center Password Reset",$msg);
	}


?>