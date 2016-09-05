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
 * @version: 1.16.2, 09.05.2016 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    	
	
	session_start(); 
	include_once('score_center_objects.php');
	include_once('role_check.php');
	require_once 'login.php';
	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	
	if ($_SESSION["accountMode"] != 'create' and $_SESSION["accountMode"] != 'update') {
		header("location: index.php");
		exit(); 
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
  <script type="text/javascript">
  
	function validate() {
	var mode = '<?php echo $_SESSION["accountMode"]; ?>';
		if (mode == 'create') {
			if ($("#firstName").val().trim() == '' || $("#lastName").val().trim() == '' || $("#userName").val().trim() == ''
			|| $("#regCode").val().trim() == '' || $("#password").val().trim() == '' || $("#vPassword").val().trim() == '') {
				displayError("<strong>Required Fields:</strong> First Name, Last Name, Password, User Name, and Registration Code are required.");
				return false;
			}
			
			if ($("#password").val().trim() != $("#vPassword").val().trim()) {
				displayError("<strong>Error:</strong> Verification Password does not match.");
				$("#vPassword").val() = '';
				$("#password").val() = '';
				return false;
			}
			
			document.forms[0].action = document.forms[0].action + "?command=createNewAccount&";
			document.forms[0].submit();
		}
		else if (mode == 'update') {
		
			if ($("#password").val().trim() == '' && $("#vPassword").val().trim() == '') ;
			else if ($("#password").val().trim() != $("#vPassword").val().trim()) {
				displayError("<strong>Error:</strong> Verification Password does not match.");
				$("#vPassword").val() = '';
				$("#password").val() = '';
				return false;
			}
			if ($("#firstName").val().trim() == '' || $("#lastName").val().trim() == '' || $("#userName").val().trim() == '') {
				displayError("<strong>Required Fields:</strong> All fields are required.");
				return false;
			}
		
			document.forms[0].action = document.forms[0].action + "?command=updateExistingAccount&";
			document.forms[0].submit();
		}
	
		return true;
	}
	
	function validatePhoneNumber(ele) {
 		var regex = /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/i;  
 			if (ele.value.match(regex)) {
 				return;
 			}
 			else {
 				ele.value = '';
 			}	
	}
	
	function validateEmail(ele) {
	 	var regex = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;  
 		if (ele.value.match(regex)) {
 			return;
 		}
 		else {
 		//	ele.value = '';
 		}
	}
  
  </script>
        <style>
	.red {
		color: red;
	}
  
  
  </style>
  </head>
  
  <body>
   <?php include_once 'navbarMin.php'; ?>
  	<form action="controller.php" method="POST">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Manage Account</h1>
     <h4>Action: <?php if ($_SESSION["accountMode"] != null and $_SESSION["accountMode"] == 'create') echo 'Create'; else echo 'Update';?></h4>
	<table class="table table-hover"> 
		<tr>
			<td width="25%"><label for="firstName">First Name:<span class="red">*</span></label></td>
			<td width="25%"><input type="text" size="40" class="form-control" name="firstName" id="firstName" value="<?php echo $_SESSION["firstName"]; ?>"></td>
			<td width="25%"><label for="lastName">Last Name:<span class="red">*</span></label></td>
			<td width="25%"><input type="text" size="40" class="form-control" name="lastName" id="lastName" value="<?php echo $_SESSION["lastName"]; ?>"></td>
		</tr>
		<tr>
			<td width="25%"><label for="userName">User Name / Email:<span class="red">*</span></label></td>
			<td width="25%"><input type="text" size="40" class="form-control" name="userName" id="userName" onblur="validateEmail(this)" 
							placeholder="john@doe.com"	value="<?php echo $_SESSION["userName"]; ?>"></td>
			<?php if ($_SESSION["accountMode"] == 'create') { ?>
				<td width="25%"><label for="regCode">Registration Code:<span class="red">*</span></label></td>
				<td width="25%"><input type="text" autocomplete="off" size="40" class="form-control" name="regCode" id="regCode"></td>
			<?php } else { 
				$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
				$userRole = $userSessionInfo->getRole();
				echo '<td width="25%"><label for="regCode">Role:</label></td>';
				echo '<td width="25%">'.$userRole.'</td>';
				} ?>
		</tr>
		<tr>
			<td width="25%"><label for="userName">Phone Number: </label></td>
			<td width="25%"><input type="text" size="40" class="form-control" name="userPhoneNumber" id="userPhoneNumber" onblur="validatePhoneNumber(this)"
							placeholder="xxx-xxx-xxxx" value="<?php echo $_SESSION["userPhoneNumber"]; ?>"></td>
			<td width="25%"><label for="state">State: </label></td>
			<td width="25%"><select class="form-control" name="state" id="state" >
			<option value=""></option>
			<?php
			if ($_SESSION["stateCodeList"] != null) {	
				$results = $_SESSION["stateCodeList"];
				foreach($results as $row) {	
					echo '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["state"] == $row['REF_DATA_CODE']){echo("selected");} echo '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}
			?>
		</select></td>
		</tr>
		<tr>
			<td width="25%"><label for="password">Password:<?php if ($_SESSION["accountMode"] == 'create') { ?><span class="red">*</span><?php } ?></label></td>
			<td width="25%"><input type="password" size="40" class="form-control" name="password" id="password" value="<?php echo $_SESSION["password"]; ?>"></td>
			<td width="25%"><label for="vPassword">Verify Password:<?php if ($_SESSION["accountMode"] == 'create') { ?><span class="red">*</span><?php } ?></label></td>
			<td width="25%"><input type="password" size="40" class="form-control" name="vPassword" id="vPassword" value="<?php echo $_SESSION["vPassword"]; ?>"></td>
		</tr>

		</table>
		
		<?php if ($_SESSION["accountMode"] == 'create') { ?>
			<input type="submit" class="btn btn-xs btn-danger" name="createNewAccount" onclick="return validate();" accesskey="" value="Create Account"/>
		<?php } else { ?>
			<input type="submit" class="btn btn-xs btn-danger" name="updateAccount" onclick="return validate();" value="Update Account"/>
		<?php } ?>
			<button type="submit" class="btn btn-xs btn-primary" name="cancelAccount">Cancel</button>
      

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    	<script type="text/javascript">
		<?php if ($_SESSION["createAccountError"] != null) { ?>
		<?php if ($_SESSION["createAccountError"] == 'error1') { ?>
			displayError("<strong>Create Account Validation:</strong> Email Address has already been registered.");
		<?php } else if ($_SESSION["createAccountError"] == 'error2') {  ?>
			displayError("<strong>Create Account Validation:</strong> Registration code is incorrect.");
		<?php } ?>
	<?php $_SESSION["createAccountError"] = null; } ?>
    	</script>
  </body>
</html>