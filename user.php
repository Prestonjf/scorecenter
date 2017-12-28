<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2017  Preston Frazier
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
 * @version: 1.17.1, 12.28.2017 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
		
	session_start(); 
	include_once('score_center_objects.php');
	include_once('logon_check.php');
	include_once('functions/global_functions.php');
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
	
	// Edit or Readonly
	$disabled = '';
	$disable = false;
	if (($_SESSION["pageCommand"] != null and ($_SESSION["pageCommand"] == 'selectCoach' || $_SESSION["pageCommand"] == 'selectVerifier' || $_SESSION["pageCommand"] == 'selectSupervisor')) || getCurrentRole() == 'ADMIN') {
		$disabled = 'disabled';
		$disable = true;	
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
	<?php include_once('functions/pagination.php'); ?>
	
  <script type="text/javascript">
  $(document).ready(function(){
  
    //	$("#addTournament").click(function(){
     //   	alert("add");
    //	});

    	
    	
	});
	
	function clearFilterCriteria() {
		var command = '<?php echo $_SESSION["pageCommand"]; ?>';
		
		document.getElementById('userFirstName').value = '';
		document.getElementById('userLastName').value = '';
		if (command != 'selectCoach')
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
     
     <?php
	     echo getUserSearchHeader($disabled, isUserAccess(0));
	    ?>

		<button type="submit" class="btn btn-xs btn-warning" name="searchUsers">Search</button>
		<button type="button" class="btn btn-xs btn-warning" name="clearSearchEvent" onclick="clearFilterCriteria()">Clear</button>
		<br>
		<br>
		
		<?php paginationHeader($_SESSION["userList"]); ?>
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
			foreach ($_SESSION["userList"] as $index => $user) {
      			paginationRow($index);
      			echo '<td>'; echo $user['2'].', '. $user['1']; echo '</td>'; 
      			echo '<td>'; echo $user['3']; echo '</td>'; 
      			echo '<td>'; echo $user['4']; echo '</td>';
      			echo '<td>'; 
				if (!$disable) { echo '<button type="submit" class="btn btn-xs btn-primary" name="editUser" value="'.$user['0'].'">Edit User</button> &nbsp;'; }	
				else {
					$sValue = $user['0'].'-'.$user['2'].', '. $user['1'].'-'.$user['3'];
					echo '<button type="submit" class="btn btn-xs btn-primary" name="selectUser" value="'.$sValue.'">Select User</button> &nbsp;';
				}		
				echo '</td>';	
				echo '</tr>';	
    		}
    	}
        ?>
          </tbody>
          </table>
		<?php paginationFooter($_SESSION["userList"]); 	
		
		if ($disable) echo '<button type="submit" class="btn btn-xs btn-primary" name="adminCreateUser" value="'.$_SESSION["pageCommand"].'">Create User</button> ';
		echo '<button type="submit" class="btn btn-xs btn-primary" name="cancelSelectUser" value="'.$_SESSION["pageCommand"].'">Cancel</button>';
		?>
		
		
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