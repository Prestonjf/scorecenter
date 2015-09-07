<?php session_start(); 
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
	checkUserRole(1);
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
     
     <h1>Manage Score Center Utilities</h1>
	 <hr>
	 
	 <fieldset>
	 <legend>User Account Management</legend>
	<table width="100%" class="borderless">
	<tr>
	<td width="15"><label for="resetPassword">Registration Code: </label></td>
	<td width="35">
	<input type="text" size="20" class="form-control" name="resetPassword" id="resetPassword" value=<?php echo '"'.$_SESSION["registrationCode"].'"' ?>></td>
	
	<td width="15"><label for="userLastName">Reset Password: </label></td>
	<td width="35"><input type="text" size="60" class="form-control" name="userLastName" id="userLastName" value=<?php echo '"'.$_SESSION["resetPassword"].'"' ?>></td>
	</tr>
	</table>
	</fieldset>
	
	<fieldset>
	<legend>Role Management</legend>
	<table width="100%" class="borderless">
	<tr>
	<td width="15"><label for="resetPassword">Active Roles: </label></td>
	<td width="35">
	<input type="text" size="20" class="form-control" name="resetPassword" id="resetPassword" value=<?php echo '"'.$_SESSION["activeRoles"].'"' ?>></td>
	
	<td width="15"></td>
	<td width="35"></td>
	</tr>
	</table>
	</fieldset>
	
	
	<br />
	<br />
	
	 <button type="submit" class="btn btn-xs btn-danger" name="saveUtilities" value="1">Save</button>
 	 <button type="submit" class="btn btn-xs btn-primary" name="cancelUtilities">Cancel</button>
	 
      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['savesuccessUser'] != null and $_SESSION['savesuccessUser'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Saved: </strong>User has been updated successfully!');</script>
   	<?php $_SESSION['savesuccessUser'] = null; } ?> 	
    
  </body>
</html>