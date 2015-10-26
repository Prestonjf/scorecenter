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
	<?php include_once('libs/pagination.php'); ?>
	
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
	 <hr>
	<table width="90%" class="borderless">
	<tr>
	<td width="15"><label for="fromDate">From Date: </label></td>
	<td width="35">
	<div class="controls"><div class="input-group">
	<input type="text" size="20" class="date-picker form-control" readonly="true" name="fromDate" id="fromDate" value=<?php echo '"'.$_SESSION["fromTournamentDate"].'"' ?>>
	<label for="fromDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
	</div></div></td>
	
	<td width="15"><label for="toDate">To Date: </label></td>
	<td width="35">
	<div class="controls"><div class="input-group">
	<input type="text" size="20" class="date-picker form-control" readonly="true" name="toDate" id="toDate" value=<?php echo '"'.$_SESSION["toTournamentDate"].'"' ?>>
	<label for="toDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
	</div></div></td>
	</tr>
	<tr>
	<td><label># of Results: </label></td><td>
	<input type="number" class="form-control" size="10" onkeydown="limit(this);" onkeyup="limit(this);" name="tournamentsNumber" id="tournamentsNumber" min="0" max="999"
		step="1" value=<?php echo '"'.$_SESSION["tournamentsNumber"].'"' ?>>
	</td>
	<td></td>
	<td align="right"><button type="submit" class="btn btn-xs btn-warning" name="searchTournament">Search</button>
		<button type="button" class="btn btn-xs btn-warning" name="clearSearchTournament" onclick="clearDates()">Clear</button>
	</td>
	
	</tr>
	</table>
	
	<script type="text/javascript">
		$(".date-picker").datepicker({
			changeMonth: true,
			changeYear: true
		});
	</script>

<hr>
		<?php $result = $mysqli->query($_SESSION["allTournaments"]); ?>
		<?php paginationHeader($result); ?>
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
					echo '<td>'; echo $row['1']; echo '</td>';
					echo '<td>'; echo $row['3']; echo '</td>';
					echo '<td>'; echo $row['2']; echo '</td>';
					echo '<td>'; echo $row['4']; echo '</td>';
					echo '<td>';
					echo '<button type="submit" class="btn btn-xs btn-primary" name="enterScores" value="'.$row['0'].'">Enter Scores</button> &nbsp;'; 				
					echo '<button type="submit" class="btn btn-xs btn-success" name="printScore" value='.$row['0'].'>View Results</button>&nbsp;';
					echo '<button type="submit" class="btn btn-xs btn-primary" name="loadTournament" value='.$row['0'].'>Edit Tournament</button>&nbsp;';
					if (getCurrentRole() == 'ADMIN') echo '<button type="submit" class="btn btn-xs btn-danger" name="deleteTournament" onclick="return confirm2Delete(\''.$row['0'].'\')" value='.$row['0'].'>Delete</button>&nbsp;';
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
          <?php paginationFooter($result); ?> 
		<?php if (getCurrentRole() == 'ADMIN') echo '<button type="submit" class="btn btn-xs btn-primary" name="addTournament">Add Tournament</button>'; ?>

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