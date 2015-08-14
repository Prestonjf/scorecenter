<?php session_start(); 

	require_once 'login.php';
	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	if($_SESSION["loginUserName"] != null and $_SESSION["loginUserName"] != ''){
		header("location: index.php");
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
  $(document).ready(function(){
  
    //	$("#addTournament").click(function(){
     //   	alert("add");
    //	});

    	
    	
	});

  
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
		<td style="padding-left: 2em;"><a href="#"><h6>Create Account</h6></a></td>
		</tr>
		<tr>
		<td><label for="password">Password: </label></td>
		<td>
		<input type="password" size="40" class="form-control" name="password" id="password">
		</td>
		<td style="padding-left: 2em;"><a href="#"><h6>Forgot Password?</h6></a></td>
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
	<?php if ($_SESSION["loginError1"] != null and $_SESSION["loginError1"] == '1') { ?>
	<script type="text/javascript">
		displayError("<strong>Login Failed:</strong> Username or Password incorrect.");
	</script>
	<?php } ?>
    
  </body>
</html>