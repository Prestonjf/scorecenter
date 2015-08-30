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
  $(document).ready(function(){
  
    //	$("#addTournament").click(function(){
     //   	alert("add");
    //	});

    	
    	
	});
	
	function clearFilterCriteria() {
		document.getElementById('userFirstName').value = '';
		document.getElementById('userLastName').value = '';
		document.getElementById('userRole').value = '';
		document.getElementById('userFilterNumber').value = '100';
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
     
     <h1>Manage Users</h1>
	 <hr>
	<table width="90%" class="borderless">
	<tr>
	<td width="15"><label for="userFirstName">First Name: </label></td>
	<td width="35">
	<input type="text" size="20" class="form-control" name="userFirstName" id="userFirstName" value=<?php echo '"'.$_SESSION["userFirstName"].'"' ?>></td>
	
	<td width="15"><label for="userLastName">Last Name: </label></td>
	<td width="35"><input type="text" size="20" class="form-control" name="userLastName" id="userLastName" value=<?php echo '"'.$_SESSION["userLastName"].'"' ?>></td>
	</tr>
	<tr>
	<td width="15"><label for="userRole">Role: </label></td>
	<td width="35">
			<select class="form-control" name="userRole" id="userRole">
			<option value="" <?php if ($_SESSION["userRole"] == null or $_SESSION["userRole"] == '') echo 'selected'; ?> ></option>
			<option value="ADMIN" <?php if ($_SESSION["userRole"] == 'ADMIN') echo 'selected'; ?> >Admin</option>
			<option value="VERIFIER" <?php if ($_SESSION["userRole"] == 'VERIFIER') echo 'selected'; ?>>Verifier</option>
			<option value="SUPERVISOR" <?php if ($_SESSION["userRole"] == 'SUPERVISOR') echo 'selected'; ?>>Supervisor</option>
			</select>
	</td>
	<td width="15"></td>
	<td width="35"></td>
	</tr>
	<tr>
	<td><label># of Results: </label></td><td>
	<input type="number" class="form-control" size="10" onkeydown="limit(this);" onkeyup="limit(this);" name="userFilterNumber" id="userFilterNumber" min="0" 	max="999" step="1" value=<?php echo '"'.$_SESSION["userFilterNumber"].'"' ?>>
	</td>
	<td></td>
	<td align="right"><button type="submit" class="btn btn-xs btn-warning" name="searchUsers">Search</button>
		<button type="button" class="btn btn-xs btn-warning" name="clearSearchEvent" onclick="clearFilterCriteria()">Clear</button>
	</td>
	
	</tr>
	</table>

<hr>
<br />
<br />
        <table class="table table-hover">
        <thead>
            <tr>
                <th data-field="name" data-align="right" data-sortable="true">User Name</th>
                <th data-field="name" data-align="right" data-sortable="true">User Email</th>
                <th data-field="name" data-align="right" data-sortable="true">User Role</th>
                <th data-field="actions" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody>
         <?php 
         if ($_SESSION["userList"] != null) {
			foreach ($_SESSION["userList"] as $user) {
      			echo '<tr>';
      			echo '<td>'; echo $user['2'].', '. $user['1']; echo '</td>'; 
      			echo '<td>'; echo $user['3']; echo '</td>'; 
      			echo '<td>'; echo $user['4']; echo '</td>';
      			echo '<td>'; 
				echo '<button type="submit" class="btn btn-xs btn-primary" name="editUser" value="'.$user['0'].'">Edit User</button> &nbsp;'; 				
				echo '</td>';	
				echo '</tr>';	
    		}
    	}
        ?>
          </tbody>
          </table>

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