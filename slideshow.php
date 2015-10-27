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
	<?php include_once('libs/head_tags.php'); ?>
	<?php include_once('libs/pagination.php'); ?>
	
  <script type="text/javascript">
  $(document).ready(function(){
	  
	});
	
	window.addEventListener("keydown", dealWithKeyboard, false);
	window.addEventListener("keypress", dealWithKeyboard, false);
	window.addEventListener("keyup", dealWithKeyboard, false);
	 
	function dealWithKeyboard(e) {
		switch(e.keyCode) {
			case 37:
				alert("Left");
				break;
			case 38:
				alert("Back Up Slide Animation");
				break;
			case 39:
				alert("Right");
				break;
			case 40:
				alert("Forward Slide Animation");
				break;  
			case 81:
				if(confirm('Are you sure you want to exit the slideshow?')) {
				
				}
				break;  
		} 

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
  
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h3><?php echo 'Results: ' . $_SESSION["tournamentName"]; ?></h3>

	 
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