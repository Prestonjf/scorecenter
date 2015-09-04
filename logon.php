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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Score Center - Michigan Science Olympiad</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="js/jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet">
	<!-- 	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
  	<!-- JS -->
  <script src="js/jquery-1.11.3.js"></script>
  <script src="js/jquery-ui-1.11.4/jquery-ui.js"></script>
  <script src="js/scorecenter.js"></script>
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
		<input type="text" size="40" class="form-control" name="userName" id="userName">
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
            <a href="#" class="list-group-item">Michigan Science Olympiad Website</a>
            <a href="#" class="list-group-item">MSU State Tournament Website</a>
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
	</script>
    
  </body>
</html>