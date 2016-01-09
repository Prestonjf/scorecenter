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
	<?php include_once('libs/head_tags.php'); ?>
	
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
     
     <h1>Enter Scores</h1>
	 <table width="100%">
	 <tr>
     <td><h4>Tournament: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentName"] . ' - ' . $_SESSION["tournamentDate"]; ?></span></h4></td>
	 <td><h4>Overall Points: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["pointsSystem"]; ?></span></h4></td>
	</tr>
	 <tr>
	 <td><h4>Division: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentDivision"]; ?></span></h4></td>
     <td><!--<h4>Max Points Earned Per Event: <span style="font-weight:normal;font-size:14px;"><?php //echo $_SESSION["highestScore"]; ?></span></h4>--></td>	
	</tr>
	</table>
	<br />
     <h6>Events Completed: <?php echo $_SESSION["tournamentEventsCompleted"]; ?></h6>
     <?php
     echo' <button type="submit" class="btn btn-xs btn-success" name="printScore" value='.$_SESSION["tournamentId"].'>View Results</button>&nbsp;';
     //echo '<button type="submit" class="btn btn-xs btn-success" name="viewStatistics" value='.$_SESSION["tournamentId"].'>View Statistics</button>&nbsp;';
     ?>
	 <hr>

        <table class="table table-hover">
        <thead>
            <tr> 
                <th data-field="name" data-align="right" data-sortable="true">Event Name</th>
                <th data-field="division" data-align="center" data-sortable="true">Division</th>
                <th data-field="trialEvent" data-align="center" data-sortable="true">Trial Event?</th>
                <th data-field="scoresComplete" data-align="center" data-sortable="true">Teams Scored</th>
                <th data-field="completed" data-align="center" data-sortable="true">Submitted?</th>
                <th data-field="completed" data-align="center" data-sortable="true">Verified?</th>
                <th data-field="actions" data-sortable="true">Actions</th>
                <th data-field="completed" data-align="center" data-sortable="true">Completed?</th>
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
				echo '<td>';
				echo '<button type="submit" class="btn btn-xs btn-primary" name="enterEventScores" value="'.$row['3'].'">Enter Scores</button> &nbsp;'; 				
				echo '</td>';
				echo '<td>'; if ($row['4']==$row['5'] and $row['6'] == '1' and $row['7'] == '1') 
							echo '<img src="img/check_green.png" alt="check_green" height="20" width="20">';
							else echo '<img src="img/check_red.png" alt="check_red" height="20" width="20">'; 				
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
    
    <?php 
    	if ($_SESSION['savesuccessScore'] != null and $_SESSION['savesuccessScore'] == '1') { ?>
    	<script type="text/javascript">saveMessage('Event scores');</script>
   	<?php $_SESSION['savesuccessScore'] = null; } ?> 	
    
  </body>
</html>