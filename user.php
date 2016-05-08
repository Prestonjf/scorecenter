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
 * @version: 1.16.1, 05.08.2016 
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
	<?php include_once('libs/head_tags.php'); ?>
	<?php include_once('libs/pagination.php'); ?>
	
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
			<option value="SUPERUSER" <?php if ($_SESSION["userRole"] == 'SUPERUSER') echo 'selected'; ?>>Super User</option>
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
				echo '<button type="submit" class="btn btn-xs btn-primary" name="editUser" value="'.$user['0'].'">Edit User</button> &nbsp;'; 				
				echo '</td>';	
				echo '</tr>';	
    		}
    	}
        ?>
          </tbody>
          </table>
		<?php paginationFooter($_SESSION["userList"]); ?>
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