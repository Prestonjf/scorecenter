<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2016  Preston Frazier
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
 * @version: 1.16.3, 12.07.2016 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
	
session_start(); 
include_once('score_center_objects.php');
include_once('logon_check.php');
	require_once 'login.php';
	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	// Security Level Check
	include_once('role_check.php');
	checkUserRole(0);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
	
  <script type="text/javascript">
  function resetPassword() {
	 	clearError();
	 	clearSuccess();
	 	if (confirm('Are you sure you want to reset this users password?')) {
		  if ($('#resetPasswordText') == null || $('#resetPasswordText').val().trim() === '') {
		  	displayError("<strong>Cannot Reset Password:</strong> No password has been entered.");
			return;  
		  }
		  	xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					if (xmlhttp.responseText.trim() == 'error') {
						//error message	
					}
					else {
						// success message
						$('#resetPasswordText').val('');
						displaySuccess("<strong>Password Reset:</strong> User Password has been reset successfully!");
					}					
				}
			}
		var str = $('#resetPasswordText').val();
		var encStr = window.btoa(str);	
        xmlhttp.open("GET","controller.php?command=resetUserPassword&id="+encStr,true);
        xmlhttp.send();	 
	  }
  }



  $(document).ready(function(){
    	
	});

  
  </script>
    <style>
  	.borderless td {
  			padding-top: 1em;
			padding-right: 2em;
  			border: none;
  	}
	.red {
		color: red;
	}
  
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Edit User</h1>
	 <hr>
	<table width="100%" class="borderless">
	<tr>
	<td width="20%"><label for="userFirstLastName">User Name: </label></td>
	<td width="30%"><?php echo $_SESSION["userFirstLastName"]; ?></td>
	<td width="20%"><label for="userName">Email: </label></td>
	<td width="30%"><?php echo $_SESSION["userName"]; ?></td>
	</tr>
	<tr>
	<td><label for="phoneNumber">Phone Number: </label></td>
	<td><?php echo $_SESSION["userPhoneNumber"]; ?></td>
	<td></td>
	<td></td>
	</tr>
	<tr>
	<td><label for="userRoleCode">User Role: </label></td>
	<td>
			<select class="form-control" name="userRoleCode" id="userRoleCode">
			<option value="SUPERUSER" <?php if ($_SESSION["userRoleCode"] == 'SUPERUSER') echo 'selected'; ?> >Super User</option>
			<option value="ADMIN" <?php if ($_SESSION["userRoleCode"] == 'ADMIN') echo 'selected'; ?> >Admin</option>
			<option value="VERIFIER" <?php if ($_SESSION["userRoleCode"] == 'VERIFIER') echo 'selected'; ?>>Verifier</option>
			<option value="SUPERVISOR" <?php if ($_SESSION["userRoleCode"] == 'SUPERVISOR') echo 'selected'; ?>>Supervisor</option>
			</select>
	</td>
	<td><label for="userActiveFlag">Active User: </label></td>
	<td>
			<select class="form-control" name="userActiveFlag" id="userActiveFlag">
			<option value="0" <?php if ($_SESSION["userActiveFlag"] == 0) echo 'selected'; ?> >No</option>
			<option value="1" <?php if ($_SESSION["userActiveFlag"] == 1) echo 'selected'; ?>>Yes</option>
			</select>
	</td>
	</tr>
	<tr>
	<td><label for="resetPasswordText">Reset Password: </label></td>
	<td>
		<input type="text" class="form-control" name="resetPasswordText" id="resetPasswordText" size="50" />
	</td>
	<td><label for=""></label></td>
	<td>
	</td>
	</tr>

	</table>
	
	<hr>

     <button type="submit" class="btn btn-xs btn-danger" name="saveUser" value="<?php echo $_SESSION["userId"];?>">Save</button>
     <input type="button" class="btn btn-xs btn-danger" name="resetUserPassword" onclick="resetPassword()" value="Reset Password"></button>
 	 <button type="submit" class="btn btn-xs btn-primary" name="cancelUser" value="0">Cancel</button>


      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
  </body>
</html>