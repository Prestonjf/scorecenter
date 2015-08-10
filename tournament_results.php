<?php session_start(); 

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
		height: 140px;
		white-space: nowrap;
	}
	th.rotate > div {
		transform: translate(25px, 0px) rotate(270deg);
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
	 <hr>
<br />
<br />
        <table class="table table-bordered table-hover">
        <thead>
            <tr>
				<th width="20%" class="rotate" data-field="name" data-sortable="true"><div><span></span></div></th>
				<th width="10%" class="rotate" data-field="name" data-sortable="true"><div><span></span></div></th>
				
                <th class="rotate" data-field="name" data-sortable="true"><div><span>Total Score</span></div></th>
                <th class="rotate" data-field="actions" data-sortable="true"><div><span>Final Rank</span></div></th>
            </tr>
        </thead>
        <tbody>
         <?php 
		 	$result = $mysqli->query($_SESSION['tournamentResultsQuery']); 
         if ($result != null) {
			 while($teamResultRow = $result->fetch_array()) {
      			echo '<tr>';
				echo '<td>'.$teamResultRow['0'].'</td><td>'.$teamResultRow['1'].'</td>';
				
				
				
				
				echo '<td>'.$teamResultRow['2'].'</td>';	
				echo '<td>'.$teamResultRow['3'].'</td>';	
				echo '</tr>';
		 }
    	}
        ?>
          </tbody>
          </table>

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