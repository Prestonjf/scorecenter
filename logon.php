<?php session_start(); 
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
	<?php include_once('libs/head_tags.php'); ?>
    
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
   <?php include_once 'navbarLogin.php'; ?>
  	<form action="controller.php" method="POST">
     <div class="container">
      
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
      
     	<div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-12 col-sm-9">
         
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
		<input type="text" size="40" autocomplete="off" class="form-control" name="userName" id="userName">
		</td>
		<td style="padding-left: 2em;"><a href="controller.php?command=createAccount&"><h6>Create Account</h6></a></td>
		</tr>
		<tr>
		<td><label for="password">Password: </label></td>
		<td>
		<input type="password" size="40" class="form-control" name="password" id="password">
		</td>
		<td style="padding-left: 2em;"><a href="#" onclick="forgotPassword()"><h6>Forgot Password?</h6></a></td>
		</tr>
		<tr><td>&nbsp;</td><td></td><td></td></tr>
		</table>
	<button type="submit" class="btn btn-xs btn-danger" name="login" value="1">Login</button>
 	 <!--<button type="submit" class="btn btn-xs btn-primary" name="cancelEvent" value="0">Cancel</button> -->
	</div>
	</div>



        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
          <div class="list-group">
            <a href="#" class="list-group-item active">Quick Links</a>
            <a href="http://miscioly.org/" target="_blank" class="list-group-item">Michigan Science Olympiad Website</a>
            <a href="http://scienceolympiad.msu.edu/" target="_blank" class="list-group-item">Michigan State Tournament Website</a>
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