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
include_once('role_check.php');
require_once('login.php');

	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
	
  <script type="text/javascript">
  	function clearDates() {
		document.getElementById('userEventDate').value = '';
		document.getElementById('userTournament').value = '';	
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
  <h1></h1>
  
     <div class="container">
      
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     
      <div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-12 col-sm-9">
		
          <div class="jumbotron" style="overflow: auto;">
		  	<div style="float: left; width: 80%;">
            <h2 style="margin-top:0px;">Welcome!</h2>
            <p style="padding-right: 10px;">Tournament Score Center is a new electronic scoring system designed specifically for Science Olympiad, but usable for most competitions.
            This application allows tournament organizers the ability to manage tournaments, teams, and events
			in a secure, efficient, and flexible process.</p>
			</div>
			<div style="float: left; width: 20%;">
			<h2 style="margin-top:0px;">&nbsp;</h2>
				<img src="img/misologo.png" alt="tsclogo" width="150" height="150">
			</div>
          </div>
          
          <form action="controller.php" method="GET">
        
        <?php
          	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
          	if (isUserAccess(2)) {
          ?>
        <h2>Today's Tournaments</h2>
        <table class="table table-hover">
        <thead>
            <tr>
                <th data-field="name" data-align="right" data-sortable="true">Tournament Name</th>
                <th data-field="division" data-align="center" data-sortable="true">Division</th>
                <th data-field="location" data-sortable="true">Location</th>
                <th data-field="date" data-sortable="true">Date</th>
                <th data-field="actions" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
		$query = "SELECT T.TOURNAMENT_ID, T.NAME, T.LOCATION,T.DIVISION, DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE' FROM TOURNAMENT T ";
		if (getCurrentRole() == 'VERIFIER') {
			$query .= " INNER JOIN TOURNAMENT_VERIFIER TV ON TV.TOURNAMENT_ID=T.TOURNAMENT_ID AND TV.USER_ID =" .getCurrentUserId();
		}
		$query .= " WHERE DATE_FORMAT(T.DATE, '%Y-%m-%d') = DATE_FORMAT(CURDATE(), '%Y-%m-%d') ";
		if (getCurrentRole() == 'ADMIN') {
			$query .= " AND T.ADMIN_USER_ID =" .getCurrentUserId();
		}
 		$result = $mysqli->query($query); 
			

 			if ($result) {
 				if ($result->num_rows == 0) { echo '<tr><td colspan="5">No Tournaments Today</td></tr>';}
 				else {
      				while($row = $result->fetch_array()) {
      					echo '<tr>';
      					echo '<td>'; echo $row['1']; echo '</td>';
						echo '<td>'; echo $row['3']; echo '</td>';
						echo '<td>'; echo $row['2']; echo '</td>';
						echo '<td>'; echo $row['4']; echo '</td>';
						echo '<td>';
						echo '<button type="submit" class="btn btn-xs btn-primary" name="enterScoresIndex" value="'.$row['0'].'">Enter Scores</button> &nbsp;'; 				
						echo '<button type="submit" class="btn btn-xs btn-success" name="printScore" value='.$row['0'].'>View Results</button>';
						echo '</td>';						
						echo '</tr>';	
      				}
      			}
	
    		} else {
    			echo '<tr><td colspan="5">No Tournaments Today</td></tr>';
    		}
        ?>

        </tbody>
    	</table>
    	
    	<?php } else {   	 
 	
    	?>
		<h2>My Events</h2>
		
		<table width="90%" class="borderless">
		<tr>
			<td width="15%"><label for="fromDate">Event Date: </label></td>
			<td width="35%">
			<div class="controls"><div class="input-group">
			<input type="text" size="20" class="date-picker form-control" readonly="true" name="userEventDate" id="userEventDate" value=<?php echo '"'.$_SESSION["userEventDate"].'"' ?>>
			<label for="userEventDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
			</div></div></td>
			<td></td>
		</tr>
		<tr>
			<td width="15%"><label for="fromDate">Tournament: </label></td>
			<td width="35%">
			<select class="form-control" name="userTournament" id="userTournament">
			<option value=""></option>
			<?php
				$query = "SELECT DISTINCT T.TOURNAMENT_ID, T.NAME
					FROM TOURNAMENT T
					INNER JOIN TOURNAMENT_EVENT TE on TE.TOURNAMENT_ID=T.TOURNAMENT_ID 									
					WHERE TE.USER_ID = ".$userSessionInfo->getUserId() . " ORDER BY NAME ASC ";
					$result1 = $mysqli->query($query);	
			    if ($result1) {				 
             		while($tournamentRow = $result->fetch_array()) {
             			echo '<option '; if ($_SESSION["userTournament"] == $tournamentRow['0']) echo ' selected ';
						echo ' value="'.$tournamentRow['0'].'">'.$tournamentRow['1'].'</option>';		
             		}
             	}
        	?>
		</select>
		</td>
			<td align="right"><button type="submit" class="btn btn-xs btn-warning" name="searchUserEvent">Search</button>
			<button type="button" class="btn btn-xs btn-warning" name="clearSearchUserEvents" onclick="clearDates()">Clear</button></td>
		</tr>
		</table>
	<script type="text/javascript">
		$(".date-picker").datepicker({
			changeMonth: true,
			changeYear: true
		});
	</script>

<hr>
		
		<table class="table table-hover">
        <thead>
            <tr>
                <th width="20%" data-field="name" data-align="right" data-sortable="true">Event Name</th>
                <th width="5%" data-field="division" data-align="center" data-sortable="true">Division</th>
                <th width="30%" data-field="tournament" data-align="right" data-sortable="true">Tournament</th>
                <th width="10%" data-field="date" data-align="right" data-sortable="true">Date</th>
                <th width="5%" data-field="trialEvent" data-align="center" data-sortable="true">Trial Event?</th>
                <th width="5%" data-field="scoresComplete" data-align="center" data-sortable="true">Teams Scored</th>
                <th width="5%" data-field="submitted" data-align="center" data-sortable="true">Submitted?</th>
                <th width="5%" data-field="completed" data-align="center" data-sortable="true">Verified?</th>
                <th width="10%" data-field="actions" data-sortable="true">Actions</th>
                <th width="5%" data-field="completed" data-align="center" data-sortable="true">Completed?</th>
            </tr>
        </thead>
        <tbody>
         <?php
 			
			$query = "SELECT TE.EVENT_ID, E.NAME as eName, TE.TRIAL_EVENT_FLAG, TE.TOURN_EVENT_ID, COUNT(TES.TEAM_EVENT_SCORE_ID) as SCORES_COMPLETED, 
					T.NUMBER_TEAMS, DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE1', T.NAME as tName, T.DIVISION, TE.SUBMITTED_FLAG, TE.VERIFIED_FLAG 
					FROM TOURNAMENT_EVENT TE 
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID 
					INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID 
					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TES.SCORE IS NOT NULL									
					WHERE TE.USER_ID = ".$userSessionInfo->getUserId();
					
					if ($_SESSION["userEventDate"] != null and $_SESSION["userEventDate"] != '') { 
					 	$date1 = strtotime($_SESSION["userEventDate"]); 			
 						$date = date('Y-m-d', $date1 );
						$query = $query . " AND T.DATE = '".$date."' ";
					}
					
					if ($_SESSION["userTournament"] != null and $_SESSION["userTournament"] != '') {
						$query = $query . " AND T.TOURNAMENT_ID = " . $_SESSION["userTournament"];
					}
					
					$query = $query ." GROUP BY EVENT_ID,eNAME, TRIAL_EVENT_FLAG,TOURN_EVENT_ID, NUMBER_TEAMS
					ORDER BY UPPER(E.NAME) ASC, T.DATE DESC "; 
	
         	$result = $mysqli->query($query);			
 			if ($result) {
 				if ($result->num_rows == 0) { echo '<tr><td colspan="9">No Events Found</td></tr>';}
      			while($row = $result->fetch_array()) {
      			echo '<tr>';
      			echo '<td>'; echo $row['1']; echo '</td>';
				echo '<td>'; echo $row['8']; echo '</td>';
				echo '<td>'; echo $row['7']; echo '</td>';
				echo '<td>'; echo $row['6']; echo '</td>';
				echo '<td>'; if ($row['2'] == 0)echo 'No'; else echo 'Yes'; echo '</td>';
				echo '<td>'; echo $row['4']."/".$row['5']; echo '</td>';
				echo '<td>'; if ($row['9'] == '1') echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">';	
				echo '</td>';
				echo '<td>'; if ($row['10'] == '1') echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">';
				echo '</td>';
				echo '<td>';
				echo '<button type="submit" class="btn btn-xs btn-primary" name="enterEventScores" value="'.$row['3'].'">Enter Scores</button> &nbsp;'; 				
				echo '</td>';
				echo '<td>'; if ($row['4']==$row['5'] and $row['9'] == '1' and $row['10'] == '1') 
							echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">';			
				echo '</td>';					
				echo '</tr>';	
      			}
    		}
        ?>
          </tbody>
          </table>
		
		
		<?php } ?>
		</form>


        </div><!--/.col-xs-12.col-sm-9-->

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
          <div class="list-group">
            <a href="#" class="list-group-item active">Quick Links</a>
            <a href="#" target="_blank" class="list-group-item">Dynamic Link 1</a>
            <a href="#" target="_blank" class="list-group-item">Dynamic Link 2</a>

          </div>
        </div><!--/.sidebar-offcanvas-->
      </div><!--/row-->

      <hr>

	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    
    
    <?php if ($_SESSION['savesuccessScore'] != null and $_SESSION['savesuccessScore'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Saved: </strong>Event Scores have been saved successfully!');</script>
   	<?php $_SESSION['savesuccessScore'] = null; $_SESSION["accountUpdateSuccess"] = null; } ?> 
   	
   	<?php if ($_SESSION['accountCreationSuccess'] != null and $_SESSION['accountCreationSuccess'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Saved: </strong>Account has been created successfully!');</script>
   	<?php $_SESSION['accountCreationSuccess'] = null; } ?>
   	
   	<?php if ($_SESSION['accountUpdateSuccess'] != null and $_SESSION['accountUpdateSuccess'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Saved: </strong>Account has been updated successfully!');</script>
   	<?php $_SESSION['accountUpdateSuccess'] = null; } ?>
	<?php if ($_SESSION['savesuccessUtilities'] != null and $_SESSION['savesuccessUtilities'] == '1') { ?>
    	<script type="text/javascript">displaySuccess('<strong>Saved: </strong>Utilities have been updated successfully!');</script>
   	<?php $_SESSION['savesuccessUtilities'] = null; } ?>
   	
   	
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>