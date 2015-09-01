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

	function emailPassword($address, $password) {
		$msg = "Hello, \n\n";
		$msg = $msg . "The password for account ". $address . " has been requested from the Science Olympiad Score Center application. \n\n";
		$msg = $msg . "Your password is: ".$password;
	
	
		mail($address,"Score Center Forgotten Password",$msg);
	}


?>