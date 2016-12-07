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
 * @version: 1.16.3, 12.07.2016 
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
	checkUserRole(2);
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
  
  function saveMessage(message) {
		document.getElementById('messages').style.display = "block";
		document.getElementById('messages').innerHTML = "<strong>Saved: </strong>"+message+" has been saved successfully!";
		document.body.scrollTop = document.documentElement.scrollTop = 0;						
	}
	
	function clearFilterCriteria() {
		document.getElementById('teamFilterName').value = '';
		document.getElementById('filterDivision').value = '';
		document.getElementById('filterState').value = '';
		document.getElementById('filterRegion').value = '';
		document.getElementById('filterMyTeams').value = 'YES';
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
     
     <h1>Manage Teams</h1>
	 <hr>
	<table width="90%" class="borderless">
	<tr>
	<td width="15%"><label for="teamFilterName">Team Name: </label></td>
	<td width="35%">
	<input type="text" size="20" class="form-control" name="teamFilterName" id="teamFilterName" value=<?php echo '"'.$_SESSION["teamFilterName"].'"' ?>>
	</td>
	
	<td width="15%"><label for="filterDivision">Division: </label></td>
	<td width="35%">	
	<select class="form-control" name="filterDivision" id="filterDivision" >
			<option value=""></option>
			<option value="A" <?php if($_SESSION["filterDivision"] == 'A'){echo("selected");}?>>A</option>
			<option value="B" <?php if($_SESSION["filterDivision"] == 'B'){echo("selected");}?>>B</option>
			<option value="C" <?php if($_SESSION["filterDivision"] == 'C'){echo("selected");}?>>C</option>
	</select>
	</td>
	</tr>
	<tr>
	<td width="15%"><label for="filterState">Team State: </label></td>
	<td width="35%">
	<select class="form-control" name="filterState" id="filterState" >
			<option value=""></option>
			<?php
			if ($_SESSION["stateCodeList"] != null) {	
				$results = $_SESSION["stateCodeList"];
				foreach($results as $row) {	
					echo '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["filterState"] == $row['REF_DATA_CODE']){echo("selected");} echo '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}
			?>
	</select>
	</td>
	
	<td width="15%"><label for="filterRegion">Team Region: </label></td>
	<td width="35%">	
	<select class="form-control" name="filterRegion" id="filterRegion" >
			<option value=""></option>
			<?php
			if ($_SESSION["regionCodeList"] != null) {	
				$results = $_SESSION["regionCodeList"];
				foreach($results as $row) {	
					echo '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["filterRegion"] == $row['REF_DATA_CODE']){echo("selected");} echo '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}
			?>
	</select>
	</td>
	</tr>
	<tr>
	<td><label for="filterMyTeams">Team Filter: </label></td><td>
	<input type="radio"  name="filterMyTeams" id="filterMyTeams1" value="NO" <?php if($_SESSION["filterMyTeams"] == 'NO'){echo("checked");}?>> <label class='radio1' for="filterMyTeams1">All Teams</label>&nbsp;&nbsp;
	<input type="radio" name="filterMyTeams" id="filterMyTeams2" value="YES" <?php if($_SESSION["filterMyTeams"] == 'YES'){echo("checked");}?>> <label class='radio1' for="filterMyTeams2">My Teams</label>&nbsp;&nbsp;
	</td>
	<td></td>
	<td align="right"><button type="submit" class="btn btn-xs btn-warning" name="searchTeam">Search</button>
		<button type="button" class="btn btn-xs btn-warning" name="clearSearchEvent" onclick="clearFilterCriteria()">Clear</button>
	</td>		
	</tr>
	
	</table>

<hr>
		<?php paginationHeader($_SESSION["teamsList"]); ?>
        <table class="table table-hover">
        <thead>
            <tr>
                <th data-field="name" data-align="right" data-sortable="true">Team Name</th>
                <th data-field="division" data-align="right" data-sortable="true">Team Division</th>
                <th data-field="division" data-align="right" data-sortable="true">Team State</th>
                <th data-field="division" data-align="right" data-sortable="true">Team Region</th>
                <th data-field="actions" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody>
         <?php 
         if ($_SESSION["teamsList"] != null) {
			foreach ($_SESSION["teamsList"] as $index => $team) {
					paginationRow($index);
					echo '<td>'; echo $team['1']; echo '</td>';
					echo '<td>'; echo $team['2']; echo '</td>';
					echo '<td>'; echo $team[4]; echo '</td>';
					echo '<td>'; echo $team[5]; echo '</td>';
					echo '<td>';
					if ($team[3] == 1) {
						echo '<button type="submit" class="btn btn-xs btn-primary" name="editTeam" value="'.$team['0'].'">Edit Team</button> &nbsp;'; 				
						echo '<button type="submit" class="btn btn-xs btn-danger" name="deleteTeam" onclick="return confirmDelete(\'team\')" value='.$team['0'].'>Delete</button>&nbsp;';
					} else {
						echo '<button type="submit" class="btn btn-xs btn-primary" name="editTeam" value="'.$team['0'].'">Edit Team</button> &nbsp;'; 
						//echo '<button type="submit" class="btn btn-xs btn-success" name="viewTeam" value="'.$team['0'].'">View Team</button> &nbsp;'; 		
					}
					echo '</td>';	
					echo '</tr>';		
    		}
    	}
        ?>
          </tbody>
          </table>
           <?php paginationFooter($_SESSION["teamsList"]); ?>
		<button type="submit" class="btn btn-xs btn-primary" name="addNewTeam" value="0">Add Team</button>

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['savesuccessTeam'] != null and $_SESSION['savesuccessTeam'] == '1') { ?>
    	<script type="text/javascript">saveMessage('Team');</script>
   	<?php $_SESSION['savesuccessTeam'] = null; } ?>
   	   	<?php if ($_SESSION['deleteTeamSuccess'] != null and $_SESSION['deleteTeamSuccess'] == '1') { ?>
    	<script type="text/javascript">displaySuccess("<strong>Team Deleted:</strong> Team has been deleted.");</script>
   	<?php $_SESSION['deleteTeamSuccess'] = null; } ?>  
   	<?php if ($_SESSION['deleteTeamError'] != null and $_SESSION['deleteTeamError'] == '1') { ?>
    	<script type="text/javascript">displayError("<strong>Cannot Delete Team:</strong> Team is linked to existing tournaments.");</script>
   	<?php $_SESSION['deleteTeamError'] = null; } ?> 
    
  </body>
</html>