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
		document.getElementById('messages').innerHTML = "<strong>Saved: </strong>"+message+" has been saved successfully!";
		document.body.scrollTop = document.documentElement.scrollTop = 0;						
	}
	
	function clearFilterCriteria() {
		document.getElementById('eventName').value = '';
		document.getElementById('eventsNumber').value = '';
	}
  
  </script>
    <style>
	.red {
		color: red;
	}
	
	th.rotate {
		height: 175px;
		white-space: nowrap;
	}
	th.rotate > div {
		transform: translate(0px, 0px) rotate(270deg);
		-webkit-transform: rotate(270deg);
		-ms-transform: rotate(270deg);
		width: 30px;
	}
	
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
     
     <h1>Tournament Results</h1>
	 <h4>Tournament: <?php echo $_SESSION["tournamentName"] . ' - ' . $_SESSION["tournamentDate"]; ?></h4>
     <h4>Division: <?php echo $_SESSION["tournamentDivision"]; ?></h4>
	 <h4>Events Completed: <?php echo $_SESSION["tournamentEventsCompleted"]; ?></h4>
	 <hr>
	 	<button type="submit" class="btn btn-xs btn-success" name="exportResultsCSV" value='.$row['0'].'>Export CSV</button>
	 	<button type="submit" class="btn btn-xs btn-success" name="exportResultsEXCEL" value='.$row['0'].'>Export Excel</button>
	 <hr>

        <table class="table table-bordered table-hover" data-sortable data-sort-name="rank" data-sort-order="desc">
        <thead>
            <tr>
				<th width="20%" class="rotate" data-field="name" data-sortable="true"><div><span></span></div></th>
				<th class="rotate" data-field="number" data-sortable="true"><div><span></span></div></th>
				<?php
				$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
				if ($tournamentResultsHeader != null) {
					foreach ($tournamentResultsHeader as $resultHeader) {
						echo '<th class="rotate" data-field="score" data-align="center" data-sortable="true"><div><span>'.$resultHeader.'</span></div></th>';						
					}
				}
				?>
                <th class="rotate" data-field="total" data-align="center" data-sortable="true"><div><span>Total Score</span></div></th>
                <th class="rotate" data-field="rank" data-align="center" data-sortable="true"><div><span>Final Rank</span></div></th>
            </tr>
        </thead>
        <tbody>
         <?php 
		 $tournamentResults = $_SESSION['tournamentResults'];
         if ($tournamentResults != null) {
			 foreach ($tournamentResults as $resultRow) {
      			echo '<tr>';
				echo '<td><b>'.$resultRow['1'].'</b></td><td><b>'.$resultRow['2'].'</b></td>';
				$i = 3;
				while ($i < sizeof($resultRow)-1) {
					echo '<td>'.$resultRow[$i].'</td>';
					$i++;
				}
				
				echo '</tr>';
		 }
    	}
        ?>
          </tbody>
          </table>

		  * = Trial Event <br />
		  + = Alternate Team <br /><br />
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
    	if ($_SESSION['savesuccessEvent'] != null and $_SESSION['savesuccessEvent'] == '1') { ?>
    	<script type="text/javascript">saveMessage('Event');</script>
   	<?php $_SESSION['savesuccessEvent'] = null; } ?> 	
    
  </body>
</html>