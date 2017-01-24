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
	checkUserRole(2);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
	<?php include_once('functions/pagination.php'); ?>
	
  <script type="text/javascript">
  $(document).ready(function(){
    	
    	
	});
  
  function saveMessage(message) {
		document.getElementById('messages').style.display = "block";
		document.getElementById('messages').innerHTML = "<strong>Saved: </strong>"+message+" has been saved successfully!";
		document.body.scrollTop = document.documentElement.scrollTop = 0;						
	}
	
	function clearDates() {
		document.getElementById('fromDate').value = '';
		document.getElementById('toDate').value = '';
		document.getElementById('tournamentsNumber').value = '20';		
	}
  	
  	function confirm2Delete(id) {
  		var name = $('#tournamentName'+id).val();
  		var value = prompt("To delete tournament '"+name+"', type 'DELETE'. You will not be able to undo this action once clicking ok.");
  		if (value == 'DELETE') 
  			return true;
  		return false;
  	}
  
  </script>
    <style>
  	.borderless td {
  			padding-top: 1em;
  			padding-bottom: 1em;
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
     
     <h1>Manage Tournaments</h1>
	 <?php echo getTournamentSearchHeader(); ?>
	 <button type="submit" class="btn btn-xs btn-warning" name="searchTournament">Search</button>
		<button type="button" class="btn btn-xs btn-warning" name="clearSearchTournament" onclick="clearDates()">Clear</button>
	<br>
	<br>
	<script type="text/javascript">
		$(".date-picker").datepicker({
			changeMonth: true,
			changeYear: true
		});
	</script>
	
		<?php $result = $mysqli->query($_SESSION["allTournaments"]);
			$num_rows = $result->num_rows;
			$resultArray = array_fill(0, $num_rows, '');
			paginationHeader($resultArray); ?>
        <table class="table table-hover">
        <thead>
            <tr>
                <th data-field="name" data-align="right" data-sortable="true">Tournament Name</th>
                <th data-field="division" data-align="center" data-sortable="true">Division</th>
                <th data-field="location" data-sortable="true">Location</th>
                <th data-field="location" data-sortable="true">Date</th>
                <th data-field="actions" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody>
         <?php
         if ($_SESSION["allTournaments"] != null and $_SESSION["allTournaments"] != '') {
 			if ($result) {
 				if (mysqli_num_rows($result) == 0) { echo '<tr><td colspan="5">No Tournaments Found</td></tr>';}
				$rowCount = 0;
      			while($row = $result->fetch_array()) {
					paginationRow($rowCount);
					echo '<td width="20%">'; echo $row['1']; echo '</td>';
					echo '<td width="5%">'; echo $row['3']; echo '</td>';
					echo '<td width="20%">'; echo $row['2']; echo '</td>';
					echo '<td width="10%">'; echo $row['4']; echo '</td>';
					echo '<td width="45%">';
					echo '<button type="submit" class="btn btn-xs btn-primary" name="enterScores" value="'.$row['0'].'">Enter Scores</button> &nbsp;'; 				
					echo '<button type="submit" class="btn btn-xs btn-primary" name="printScore" value='.$row['0'].'>View Results</button>&nbsp;';
					if (isUserAccess(1)) echo '<button type="submit" class="btn btn-xs btn-primary" name="loadTournament" value='.$row['0'].'>Edit Tournament</button>&nbsp;';
					if (isUserAccess(1))echo '<button type="submit" class="btn btn-xs btn-primary" name="selfSchedule" value="'.$row['0'].'">Self Schedule</button> &nbsp;'; 	
					if (isUserAccess(1) and ($row['5'] == null or $row['5'] == 0)) echo '<button type="submit" class="btn btn-xs btn-danger" name="deleteTournament" onclick="return confirm2Delete(\''.$row['0'].'\')" value='.$row['0'].'>Delete</button>&nbsp;';
					echo '</td>';				
					echo '</tr>';
					echo '<input type="hidden" value="'.$row['1'].'" id="tournamentName'.$row['0'].'" />';
					$rowCount++;
      			}
    		}
    	}
        ?>
          </tbody>
          </table>
          <?php paginationFooter($resultArray); ?> 
		<?php if (isUserAccess(1)) echo '<button type="submit" class="btn btn-xs btn-primary" name="addTournament">Add Tournament</button>'; ?>

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <?php 
    	if ($_SESSION['savesuccessTournament'] != null and $_SESSION['savesuccessTournament'] == '1') { ?>
    	<script type="text/javascript">saveMessage('Tournament');</script>
   	<?php $_SESSION['savesuccessTournament'] = null; } ?> 	
    <?php 
    	if ($_SESSION['deleteTournamentSuccess'] != null and $_SESSION['deleteTournamentSuccess'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Deleted: </strong>Tournament has been deleted successfully!');</script>
   	<?php $_SESSION['deleteTournamentSuccess'] = null; } ?> 
  </body>
</html>