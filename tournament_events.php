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
     <h4>Tournament: <?php echo $_SESSION["tournamentName"]; ?></h4>
     <h4>Division: <?php echo $_SESSION["tournamentDivision"]; ?></h4>
     <br />
     <h6>Events Completed: </h6>
     <?php
     echo' <button type="submit" class="btn btn-xs btn-success" name="printScore" value='.$_SESSION["tournamentId"].'>View Scores</button>&nbsp;';
     echo '<button type="submit" class="btn btn-xs btn-success" name="viewStatistics" value='.$_SESSION["tournamentId"].'>View Statistics</button>&nbsp;';
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
				echo '<td>'; echo '</td>';
				echo '<td>'; echo '</td>';
				echo '<td>';
				echo '<button type="submit" class="btn btn-xs btn-primary" name="enterEventScores" value="'.$row['3'].'">Enter Scores</button> &nbsp;'; 				
				echo '</td>';
				echo '<td>'; if ($row['4']==$row['5']) echo'Yes'; else echo 'No'; echo '</td>';					
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