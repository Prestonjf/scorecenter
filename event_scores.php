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
	
	function limitNumber(element) {
		var max = <?php echo $_SESSION["tournamentHighestScore"];?>;
		if (isNaN(element.value)) element.value = '';
		if (element.value > max || element.value < 1) element.value = '';
	}
	
	function validate() {
		var error = false;
		var error2 = false;
		var max = <?php echo $_SESSION["tournamentHighestScore"];?>;
		var count = 0;
		var maxScore = <?php echo $_SESSION["tournamentHighestScore"];?>;
		var scoreArr = [];
		var exists = false;
		
		while (count < 1000) {
			exists = false;
			if  ($('#teamScore'+count) != null && $('#teamScore'+count).val() != null) {
				var score = $('#teamScore'+count).val();
				if (score == null || score == '') {
					if(!confirm("A team's score / rank has been left blank. Do you still wish to save?")) return false;
				}
				scoreArr.forEach(function(entry) {
					if (score == entry) exists = true;
				});
				
				if (exists) {
					error = true;
					break;
				}
				else if (score != '' && score != '0' && score != maxScore) {
					scoreArr.push(score);
					if (score > max) error2 = true;
				}
			} 
			else { break;}
			count++;
		}
		if (error) {
			displayError("<strong>Cannot Save Scores:</strong> Team cannot have the same score / rank as another unless the value entered is the maximum allowed score.");
			return false;
		}
		if (error2) {
			displayError("<strong>Cannot Save Scores:</strong> Team cannot have score / rank greater than the maximum score.");
			return false;
		}
		else return true;		
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
     
     <h1>Enter Event Scores</h1>
     <h4>Event: <?php echo $_SESSION["tournamentName"]; ?></h4>
     <h4>Division: <?php echo $_SESSION["tournamentDivision"]; ?></h4>
     <h4>Event: <?php echo $_SESSION["eventName"]; ?></h4> 
     <br />
     <h6>*Instructions: Enter the finishing position/score for each team on the list below. The maximum score for events at this tournament is: <?php echo $_SESSION["tournamentHighestScore"];?></h6>    
	 <hr>

        <table class="table table-hover">
        <thead>
            <tr>
                <th data-field="name" data-align="right" data-sortable="true">Team Number</th>
                <th data-field="teamNumber" data-align="center" data-sortable="true">Team Name</th>
                <th data-field="score" data-align="center" data-sortable="true">Score / Rank</th>
            </tr>
        </thead>
        <tbody>
         <?php
         if ($_SESSION["teamEventScoreList"] != null and $_SESSION["teamEventScoreList"] != '') {			
 			if ($_SESSION["teamEventScoreList"] ) {
 				$teamCount = 0;
      			foreach ($_SESSION["teamEventScoreList"] as $scoreRecord) {
      				echo '<tr>';
      				echo '<td>'; echo $scoreRecord['1']; echo '</td>';
					echo '<td>'; echo $scoreRecord['0'];; echo '</td>';
					echo '<td><div class="col-xs-5 col-md-5">';
      				echo '<input type="text"  class="form-control" size="10" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"  
      						name="teamScore'.$teamCount.'" id="teamScore'.$teamCount.'" value="'.$scoreRecord['2'].'">';
      				echo '</div></td>';					
					echo '</tr>';
					
					$teamCount++;	
      			}
    		}
    	}
        ?>
          </tbody>
          </table>
           
		<button type="submit" class="btn btn-xs btn-danger" name="saveEventScores" onclick="return validate()" value=<?php echo '"'.$_SESSION["tournEventId"].'"' ?>>Save</button>
 	 	<button type="submit" class="btn btn-xs btn-primary" name="cancelEventScores">Cancel</button>

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>	
    
  </body>
</html>