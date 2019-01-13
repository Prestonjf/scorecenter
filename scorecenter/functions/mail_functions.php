<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2019  Preston Frazier
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 *    
 * @package: Tournament Score Center (TSC) - Tournament scoring web application.
 * @version: 1.19.1, 01.13.2019 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
			
	require 'libs/class.phpmailer.php';
	require 'libs/class.smtp.php';
	require 'libs/class.pop3.php';
	
	function sendAccountCreationEmail($mysqli, $userName, $firstName, $lastName, $password) {
		$result = $mysqli->query("SELECT DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE='EMAILMESSAGE' AND REF_DATA_CODE='ACCOUNTCREATE' ");
		$row = $result->fetch_row();	
	
		$msg = "Hello ".$firstName." ".$lastName. ", \n\n";	
		$msg = $msg . $row['0'];
		$msg = $msg . "\n\n\n";	
		
		$host = $_SERVER['HTTP_HOST'];
		$dir = dirname($_SERVER['PHP_SELF']);
		if ($dir == '/') $dir = '';
		$protocol = (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';		
		
		$msg = $msg . "URL: " . $protocol.$host.$dir." \n";
		$msg = $msg . "User Name: " .$userName." \n";
		$msg = $msg . "Password: " .$password." \n";
		
		$msgHtml = "Hello ".$firstName." ".$lastName. ", <br /><br />";	
		$msgHtml = $msgHtml . $row['0'];
		$msgHtml = $msgHtml . "<br /><br /><br />";
		$msgHtml = $msgHtml . "URL: " . $protocol.$host.$dir."<br />";
		$msgHtml = $msgHtml . "User Name: " .$userName." <br />";
		$msgHtml = $msgHtml . "Password: " .$password." <br />";

		// send email
		sendMail($mysqli, $userName,"Tournament Score Center Account Creation",$msg, $msgHtml);
	}

	function emailPasswordReset($mysqli, $address, $name, $userId, $encryptedPassword, $salt) {
		$result = $mysqli->query("SELECT DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE='EMAILMESSAGE' AND REF_DATA_CODE='PASSWORDRESET' ");
		$row = $result->fetch_row();
	
		$host = $_SERVER['HTTP_HOST'];
		$dir = dirname($_SERVER['PHP_SELF']);
		if ($dir == '/') $dir = '';
		$protocol = (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		$msg = "Hello ".$name.", \n\n";
		$msg .= $row['0'];	
		$msg .= "\n\n\n";
		$msg = str_replace('<account name>',$address,$msg);
		$msg .= "Reset Password Link: \n ".$protocol.$host.$dir."/controller.php?command=passwordResetProcess&id=".$userId."&ep=".$encryptedPassword."&sa=".$salt."&nn=".uniqid();
	
		$msgHtml = "Hello ".$name.", <br /><br />";
		$msgHtml .= $row['0'];	
		$msgHtml .= "<br /><br /><br />";
		$msgHtml = str_replace('<account name>',$address,$msgHtml);
		$msgHtml .= "Reset Password Link: <br /> ".$protocol.$host.$dir."/controller.php?command=passwordResetProcess&id=".$userId."&ep=".$encryptedPassword."&sa=".$salt."&nn=".uniqid();
	
		sendMail($mysqli, $address,"Tournament Score Center Password Reset",$msg, $msgHtml);
	}
	
	function sendTestEmail($mysqli) {
			
	}

	function sendMail($mysqli, $address, $subject, $msg, $msgHtml) {
		// Load Mail Properties from DB	
		$host = '';
		$port = '';
		$username = '';
		$password = '';
		$smtpSecure = '';
		$result = $mysqli->query("SELECT DOMAIN_CODE,REF_DATA_CODE,DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE IN ('MAILSERVER') "); 
 		if ($result) {
			while($utilityRow = $result->fetch_array()) {
				if ($utilityRow != null) {
					if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'HOST') $host = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'PORT') $port = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'USERNAME') $username = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'PASSWORD') $password = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'SMTPSECURE') $smtpSecure = $utilityRow['2'];
				}
			}
		}
					
		if ($host == '' or $port == '' or $username == '' or $password == '' or $smtpSecure == '') {
			mail($address, $subject, $msg);
		}
		else {
			$mail = new PHPMailer;
			$mail->isSMTP();									  // Set mailer to use SMTP
			$mail->Host = $host.';';  							// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = $username;                 		// SMTP username
			$mail->Password = $password;                           // SMTP password
			$mail->SMTPSecure = $smtpSecure;                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = $port;  
			
			$mail->From = $username;
			$mail->FromName = 'Tournament Score Center';
			//$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
			$mail->addAddress($address);               				// Name is optional
			$mail->addReplyTo($username, 'No Reply');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			$mail->isHTML(false);                                  // Set email format to HTML

			$mail->Subject = $subject;
			$mail->Body    = $msgHtml;								// HTML
			$mail->AltBody = $msg;								// Non HTML

			if(!$mail->send()) {
				$_SESSION["mailStatus"] = '0'; // failure
			} else {
				$_SESSION["mailStatus"] = '1'; // success
			}
		}
	}

?>