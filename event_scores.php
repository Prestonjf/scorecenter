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
	checkUserRole(3);

?>
<?php
	 	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
		$userRole = $userSessionInfo->getRole();
		$disable = '';
		$disableVerfiy = '';
		$submitted = '';
		$verified = '';
        if ($userRole == 'SUPERVISOR' and $_SESSION["submittedFlag"] == '1') $disable = 'disabled';
        if ($userRole == 'SUPERVISOR') $disableVerfiy = 'disabled';
        if ($_SESSION["submittedFlag"] == '1') $submitted = 'checked';
	 	if ($_SESSION["verifiedFlag"] == '1') $verified = 'checked';
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
	
	function limitNumber(element) {
		var max = <?php echo $_SESSION["tournamentHighestScore"];?>;
		if (isNaN(element.value)) element.value = '';
		if (element.value > max || element.value < 1) element.value = '';
	}
	
	function validate() {
		var error = false;
		var error2 = false;
		var error3 = false;
		var error4 = false;
		var max = <?php echo $_SESSION["tournamentHighestScore"];?>;
		var count = 0;
		var maxScore = <?php echo $_SESSION["tournamentHighestScore"];?>;
		var scoreArr = [];
		var exists = false;
		var userRole = '<?php echo $userRole; ?>';
		
		if (document.getElementById('verifiedFlag').checked && !document.getElementById('submittedFlag').checked) {
			error4 = true;
		}
		
		while (count < 1000) {
			exists = false;
			if  ($('#teamScore'+count) != null && $('#teamScore'+count).val() != null) {
				var score = $('#teamScore'+count).val();
				if (score == null || score == '') {
					if(!confirm("A team's score / rank has been left blank. Do you still wish to save?")) return false;
					if (document.getElementById('submittedFlag').checked || document.getElementById('verifiedFlag').checked)
						error3 = true;
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
		if (error3) {
			displayError("<strong>Cannot Save Scores:</strong> All teams must be scored to submit or verify scores.");
			return false;
		}
		if (error4) {
			displayError("<strong>Cannot Save Scores:</strong> Submitted checkbox must be checked to verify scores.");
			return false;
		}
		 if (document.getElementById('submittedFlag').checked) {
			 if (userRole == 'SUPERVISOR') {
				if (!confirm('This event has been marked as submitted. Only a score verifier will be able to modify them once saved. Do you wish to continue?')) return false;
			 }
		 }
		 return true;		
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
     <h4>Tournament: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentName"]. ' - ' . $_SESSION["tournamentDate"]; ?></span></h4>
     <h4>Division: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentDivision"]; ?></span></h4>
     <h4>Event: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["eventName"]; ?></span></h4>
	 <h4>Supervisor: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["eventSupervisor"]; ?></span></h4> 	 
     <br />
     <h6>*Instructions: Enter the finishing position/score for each team on the list below. The maximum score for events at this tournament is <?php echo $_SESSION["tournamentHighestScore"];?>. Select the submitted checkbox to complete the scores. The score verifier can modify the scores after they are submitted.</h6>    
	 <hr>

	 <table width="75%"><tr>
	 <td><label for="submittedFlag">Submitted</label> &nbsp;&nbsp;<input type="checkbox" id="submittedFlag" name="submittedFlag" <?php echo $disable.' '.$submitted; ?>  echo value="1"></td>
	 <td><label for="verifiedFlag">Verified</label> &nbsp;&nbsp;<input type="checkbox" id="verifiedFlag" name="verifiedFlag" <?php echo $disableVerfiy.' '.$verified; ?> value="1"></td>
	 </tr></table>
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
      				echo '<input type="text"  class="form-control" size="10" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);" '.$disable.'    
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
        <?php if ($disable != 'disabled')   { ?>
		<button type="submit" class="btn btn-xs btn-danger" name="saveEventScores" onclick="return validate()" value=<?php echo '"'.$_SESSION["tournEventId"].'"' ?>>Save</button>
		<?php } ?>
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