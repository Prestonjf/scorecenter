<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2019 Preston Frazier
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
	<?php include_once('functions/head_tags.php'); ?>
	
  <script type="text/javascript">
  
	function validate() {
		
		if ($('#registerCodeSupervisor').val().trim() == '' || $('#registerCodeVerifier').val().trim() == ''
		|| $('#registerCodeAdmin').val().trim() == '' || $('#resetPassword').val().trim() == '') {			
			displayError("<strong>Validation Error: </strong>Field's with an ' * ' are required.");
			return false;	
		}
		
		return true;
	}
  
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
	
	fieldset.utility-border {
		border: 1px solid #eee !important;
		padding: 0 1.4em 1.4em 1.4em !important;
		margin: 0 0 1.5em 0 !important;
		-webkit-box-shadow:  0px 0px 0px 0px #eee;
        box-shadow:  0px 0px 0px 0px #eee;
	}

	legend.utility-border {
		font-size: 1.2em !important;
		font-weight: bold !important;
		text-align: left !important;
		
		width:inherit;
		 padding:0 10px;
		 border-bottom:none;
	}
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Manage Score Center Utilities</h1>
	 <hr>
	 
	 <fieldset class="utility-border">
	 <legend class="utility-border">User Accounts</legend>
	<table width="100%" class="borderless">
	<tr>
	<td width="15%"><label for="registerCodeSupervisor">Registration Code (Supervisor):<span class="red">*</span></label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="registerCodeSupervisor" id="registerCodeSupervisor" value=<?php echo '"'.$_SESSION["registerCodeSupervisor"].'"' ?>></td>
	<td width="15%"><label for="registerCodeVerifier">Registration Code (Verifier):<span class="red">*</span></label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="registerCodeVerifier" id="registerCodeVerifier" value=<?php echo '"'.$_SESSION["registerCodeVerifier"].'"' ?>></td>
	</tr>
	<tr>
	<td width="15%"><label for="registerCodeAdmin">Registration Code (Admin):<span class="red">*</span></label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="registerCodeAdmin" id="registerCodeAdmin" value=<?php echo '"'.$_SESSION["registerCodeAdmin"].'"' ?>></td>
	<td width="15%"><label for="resetPassword">Reset Password:<span class="red">*</span></label></td>
	<td width="35%"><input type="text"  size="100" class="form-control" name="resetPassword" id="resetPassword" value=<?php echo '"'.$_SESSION["resetPassword"].'"' ?>></td>
	</tr>
	</table>
	</fieldset>
	
	<fieldset class="utility-border">
	<legend class="utility-border">Email</legend>
	<table width="100%" class="borderless">
	<tr>
	<td width="15%"><label for="emailHost">Host: </label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="emailHost" id="emailHost" placeholder="smtp.scorecenter.com" value=<?php echo '"'.$_SESSION["emailHost"].'"' ?>></td>
	<td width="15%"><label for="emailPort">Port: </label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="emailPort" id="emailPort" placeholder="587" value=<?php echo '"'.$_SESSION["emailPort"].'"' ?>></td>
	</tr>
	<tr>
	<td width="15%"><label for="smtpSecure">SMTP Security: </label></td>
	<td width="35%">
			<select class="form-control" name="smtpSecure" id="smtpSecure">
			<option value="tls" <?php if ($_SESSION["smtpSecure"] == 'tls') echo 'selected'; ?> >TLS</option>
			<option value="ssl" <?php if ($_SESSION["smtpSecure"] == 'ssl') echo 'selected'; ?>>SSL</option>
			</select></td>
	<td width="15%"></td>
	<td width="35%"></tr>
	<tr>
	<td width="15%"><label for="emailUsername">Username: </label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="emailUsername" id="emailUsername" placeholder="noreply@scorecenter.com" value=<?php echo '"'.$_SESSION["emailUsername"].'"' ?>></td>
	<td width="15%"><label for="emailPassword">Password: </label></td>
	<td width="35%">
	<input type="password" size="20" class="form-control" name="emailPassword" id="emailPassword" value=<?php echo '"'.$_SESSION["emailPassword"].'"' ?>></td>
	</tr>
	</table>
	<br />
	<label>Note: If mail settings are not configured correctly, mail will be sent by the application server's default mail server.</label>
	</fieldset>
	
	<fieldset class="utility-border">
	<legend class="utility-border">Email Messages</legend>
	<table width="100%" class="borderless">
	<tr><td width="100%"><label for="accountCreationEmail">Account Creation Message: </label></td></tr>
	<tr><td width="100%">
	<textarea class="form-control"  name="accountCreationEmail" id="accountCreationEmail" spellcheck="true" rows="5" cols="100"><?php echo $_SESSION["accountCreationEmail"];?></textarea>
	</td></tr>
	<tr><td width="100%"><label for="passwordResetMessage">Password Reset Message: </label></td></tr>
	<tr><td width="100%">
	<textarea class="form-control"  name="passwordResetMessage" id="passwordResetMessage" spellcheck="true" rows="5" cols="100"><?php echo $_SESSION["passwordResetMessage"];?></textarea>
	</td></tr>
	</table>
	</fieldset>
	
	<br />
	<br />
	
	 <button type="submit" class="btn btn-xs btn-danger" onclick="return validate()" name="saveUtilities" value="1">Save</button>
 	 <button type="submit" class="btn btn-xs btn-primary" name="cancelUtilities">Cancel</button>
	 
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