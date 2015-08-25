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
		document.getElementById('messages').innerHTML = "<strong>Saved: </strong>"+message+" has been saved successfully!";
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
<br />
<br />
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
         //echo $_SESSION["allTournaments"];
         	$result = $mysqli->query($_SESSION["allTournaments"]);			
 			if ($result) {
      			while($row = $result->fetch_array()) {
      			echo '<tr>';
      			echo '<td>'; echo $row['1']; echo '</td>';
				echo '<td>'; echo $row['3']; echo '</td>';
				echo '<td>'; echo $row['2']; echo '</td>';
				echo '<td>'; echo $row['4']; echo '</td>';
				echo '<td>';
				echo '<button type="submit" class="btn btn-xs btn-primary" name="enterScores" value="'.$row['0'].'">Enter Scores</button> &nbsp;'; 				
				echo '<button type="submit" class="btn btn-xs btn-success" name="printScore" value='.$row['0'].'>View Scores</button>&nbsp;';
				echo '<button type="submit" class="btn btn-xs btn-primary" name="loadTournament" value='.$row['0'].'>Edit Tournament</button>&nbsp;';
				echo '<button type="submit" class="btn btn-xs btn-danger" name="deleteTournament" onclick="return confirmDelete(\'tournament\')" value='.$row['0'].'>Delete</button>&nbsp;';
				echo '</td>';
					
				echo '</tr>';	
      			}
    		}
    	}
        ?>
          </tbody>
          </table>
           
		<button type="submit" class="btn btn-xs btn-primary" name="addTournament">Add Tournament</button>

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
    
  </body>
</html>