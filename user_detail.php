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
	checkUserRole(2);
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
  $(document).ready(function(){
  
    //	$("#addTournament").click(function(){
     //   	alert("add");
    //	});

    	
    	
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

	</table>
	
	<hr>

     <button type="submit" class="btn btn-xs btn-danger" name="saveUser" value="<?php echo $_SESSION["userId"];?>">Save</button>
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