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
    
		
session_start(); 
	include_once('score_center_objects.php');
	require_once 'login.php';
	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	if($_SESSION["userSessionInfo"] != null) {
		$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
		if ($userSessionInfo->getUserName() != null and $userSessionInfo->getAuthenticatedFlag() != null) {
			header("location: index.php");
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
    
  <script type="text/javascript">
  
	function forgotPassword() {
		if (confirm('Selecting OK will send a password reset email to the email entered in the username field. Do you want to continue?')) {
			document.forms[0].action = document.forms[0].action + "?command=resetPassword&";
			document.forms[0].submit();
		}
	}

  
  </script>
    <style>
  
  
  </style>
  </head>
  
  <body>
   <?php include_once 'navbar.php'; ?>
  	<form action="controller.php" method="POST">
     <div class="container">
      
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
      
     	<div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-12 col-sm-8">
         
	<div width="50%" style="margin-bottom: 2em; background-color: #eee; border-radius: 4px; padding: 1em;">
		<table class="borderless" cellspacing="5">
		<tr>
		<td width="20%"><label for="userName"><h3>Login</h3></label></td>
		<td width="60%"></td>
		<td width="20%"></td>
		</tr>
		<tr>
		<td><label for="userName">User Name: </label></td>
		<td>
		<input type="text" size="40" class="form-control" name="userName" id="userName">
		</td>
		<td style="padding-left: 2em;"><a href="controller.php?command=createAccount&" tabindex="-1"><h6>Create Account</h6></a></td>
		</tr>
		<tr>
		<td><label for="password">Password: </label></td>
		<td>
		<input type="password" size="40" class="form-control" name="password" id="password">
		</td>
		<td style="padding-left: 2em;"><a href="#" onclick="forgotPassword()" tabindex="-1"><h6>Forgot Password?</h6></a></td>
		</tr>
		<tr><td>&nbsp;</td><td></td><td></td></tr>
		</table>
	<button type="submit" class="btn btn-xs btn-danger" name="login" value="1">Login</button>
 	 <!--<button type="submit" class="btn btn-xs btn-primary" name="cancelEvent" value="0">Cancel</button> -->
	</div>
	</div>



        <div class="col-xs-6 col-sm-4 sidebar-offcanvas" id="sidebar">
          <div class="list-group">
            <span class="list-group-item active">Information</span>
            <a href="#" class="list-group-item">Tournament Score Center is an application designed to make managing competitive tournaments easy!</a>
          </div>
        </div><!--/.sidebar-offcanvas-->
        
      </div><!--/row-->
      

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
	<?php if ($_SESSION["loginError1"] != null and $_SESSION["loginError1"] == '1') { ?>
		displayError("<strong>Login Failed:</strong> Username or Password incorrect.");	
	<?php $_SESSION["loginError1"] = null; } ?>
	<?php if ($_SESSION["resetPasswordError"] != null and $_SESSION["resetPasswordError"] == '1') { ?>
		displayError("<strong>Password Notification Failed:</strong> Username is required.");	
	<?php $_SESSION["resetPasswordError"] = null; } ?>
	<?php if ($_SESSION["resetPasswordError"] != null and $_SESSION["resetPasswordError"] == '2') { ?>
		displayError("<strong>Password Notification Failed:</strong> Username is incorrect.");	
	<?php $_SESSION["resetPasswordError"] = null; } ?>
	<?php if ($_SESSION["resetPasswordSuccess"] != null and $_SESSION["resetPasswordSuccess"] == '1') { ?>
		displaySuccess("<strong>Password Notification Sent:</strong> User Password reset link has been sent to your email.");	
	<?php $_SESSION["resetPasswordSuccess"] = null; } ?>
	<?php if ($_SESSION['errorSessionTimeout'] != null and $_SESSION['errorSessionTimeout'] == '1') { ?>
		displayError("<strong>Session Timed Out:</strong> Your session has timed out. Please login to continue working.");	
	<?php $_SESSION['errorSessionTimeout'] = null; } ?>
	
	</script>
    
  </body>
</html>