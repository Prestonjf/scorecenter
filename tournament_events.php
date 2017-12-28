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
	checkUserRole(2);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('functions/head_tags.php'); ?>
	
  <script type="text/javascript">
  $(document).ready(function(){
  
    //	$("#addTournament").click(function(){
     //   	alert("add");
    //	});

    	
    	
	});
  
  function saveMessage(message) {
		document.getElementById('messages').style.display = "block";
		document.getElementById('messages').innerHTML = "<strong>Saved: </strong>"+message+" have been saved successfully!";
		document.body.scrollTop = document.documentElement.scrollTop = 0;						
	}
	
	function clearDates() {
		document.getElementById('fromDate').value = '';
		document.getElementById('toDate').value = '';
		document.getElementById('tournamentsNumber').value = '20';
		
	}
  
  </script>
    <style>
  
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Enter Scores</h1>

	 <?php
	     echo getTournamentHeader();
		 echo' <button type="submit" class="btn btn-xs btn-primary" name="printScore" value='.$_SESSION["tournamentId"].'>View Results</button>&nbsp;';
     ?>
     <br />
      <br />

        <table class="table table-hover">
        <thead>
            <tr> 
                <th data-field="name" data-align="right" data-sortable="true">Event Name</th>
                <th data-field="division" data-align="center" data-sortable="true">Division</th>
                <th data-field="trialEvent" data-align="center" data-sortable="true">Trial Event <img src="img/question_blue.png" alt="check_green" height="10" width="10" data-toggle="tooltip" title="Trial events are excluded from the overall rankings calculation."></th>
                <th data-field="scoresComplete" data-align="center" data-sortable="true">Teams Scored</th>
                <th data-field="completed" data-align="center" data-sortable="true">Submitted <img src="img/question_blue.png" alt="check_green" height="10" width="10" data-toggle="tooltip" title="Supervisor has submitted the completed scores for verification."></th>
                <th data-field="completed" data-align="center" data-sortable="true">Verified <img src="img/question_blue.png" alt="check_green" height="10" width="10" data-toggle="tooltip" title="Verifier has validated the event scores were entered correctly."></th>
                <th data-field="completed" data-align="center" data-sortable="true">Completed <img src="img/question_blue.png" alt="check_green" height="10" width="10" data-toggle="tooltip" title="All team's scores have been submitted and verified for the event."></th>
                <th data-field="actions" data-sortable="true">Actions</th>

            </tr>
        </thead>
        <tbody>
         <?php
         if ($_SESSION["tournamentEventsQuery"] != null and $_SESSION["tournamentEventsQuery"] != '') {
         //echo $_SESSION["allTournaments"];
         	$result = $mysqli->query($_SESSION["tournamentEventsQuery"]);			
 			if ($result) {
      			while($row = $result->fetch_array()) {
      			echo '<tr>';
      			echo '<td>'; echo $row['1']; echo '</td>';
				echo '<td>'; echo $_SESSION["tournamentDivision"]; echo '</td>';
				echo '<td>'; if ($row['2'] == 0)echo 'No'; else echo 'Yes'; echo '</td>';
				echo '<td>'; echo $row['4']."/".$row['5']; echo '</td>';
				echo '<td>'; if ($row['6'] == '1') echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">';	
				echo '</td>';
				echo '<td>'; if ($row['7'] == '1') echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">';
				echo '</td>';
				echo '<td>'; if ($row['4']==$row['5'] and $row['6'] == '1' and $row['7'] == '1') 
				echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">'; 				
				echo '</td>';
				echo '<td>';
				echo '<button type="submit" class="btn btn-xs btn-primary" name="enterEventScores" value="'.$row['3'].'">Enter Scores</button> &nbsp;
				<button type="submit" class="btn btn-xs btn-primary" name="exportEventScores" value="'.$row['3'].'">Results</button>&nbsp;
				<button type="submit" class="btn btn-xs btn-primary" name="exportEventAwards" value="'.$row['3'].'">Awards</button>  '; 				
				echo '</td>';
					
				echo '</tr>';	
      			}
    		}
    	}
        ?>
          </tbody>
          </table>
           
		<button type="submit" class="btn btn-xs btn-primary" name="cancelTournament">Cancel</button>

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
		$(document).ready(function(){
    		$('[data-toggle="tooltip"]').tooltip(); 
		});
	</script>
    
    <?php 
	    displayMsgs();
		displayErrors();
	?>	
    
  </body>
</html>