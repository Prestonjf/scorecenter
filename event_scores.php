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
	
	function updatePointsEarned(section, id, type) {
		var element = document.getElementById(section+id);
		var max = <?php echo $_SESSION["tournamentHighestScore"];?>;
		var lowHighFlag = <?php echo $_SESSION["highLowWinFlag"];?>;
		if (lowHighFlag == 0) {
			if (element.value == 0) document.getElementById(type+id).value = max;
			else if (element.value > max) document.getElementById(type+id).value = max;
			else document.getElementById(type+id).value = element.value;			
		}
		else {
			if (element.value == 0) document.getElementById(type+id).value = 0;
			else if ((max + 1 - element.value) > 0) document.getElementById(type+id).value = max + 1 - element.value;
			else document.getElementById(type+id).value = 0;
		}
		
	}
	
	function highlightRawScoreDuplication() {
		var count = 0;
		var scoreArr = [];
		var duplicates = {};
		var pCount = 0;
		var colorPalette = ["#FFD5D5","#FFFFCC","#E1F7D5","#C9C9FF","#F1CBFF","#FFE7CC","#CCFFFD","#EBE8E0","#939393","#CFE4F1"];
		
		while (count < 1000) {
			if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				var score = $('#teamRawScore'+count).val();
				var tier = $('#teamScoreTier'+count).val();
				document.getElementById('teamRawScore'+count).style.backgroundColor = "#FFFFFF";
				scoreArr.forEach(function(entry) {
					//alert(entry);
					if (score == entry[0] && tier == entry[1]) {
						if (entry[0] in duplicates);
						else {
							duplicates[entry[0]] = colorPalette[pCount];
							if (pCount == 9 )pCount = 0;
							else pCount++;
						}
					}
				});
				if (score != '') {
					var obj = [score,tier];
					scoreArr.push(obj);
				}
			}
			else break;		
			count++;
		}
		
		count = 0;
		while (count < 1000) {
			if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				var score = $('#teamRawScore'+count).val();
				if (score in duplicates) {
					document.getElementById('teamRawScore'+count).style.backgroundColor = duplicates[score];			
				}
			}
			else break;
			count++;
		}
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
					break;
				}
				scoreArr.forEach(function(entry) {
					if (score == entry && max != score) exists = true;
				});
				
				if (exists) {
					error = true;
					break;
				}
				else if (score != '' && score != '0') {
					scoreArr.push(score);
				}
			} 
			else { break;}
			count++;
		}
		// Validate Numbers are sequential and no 0 - error2
		scoreArr.sort(sortNumber);
		var sequence = 1;

		scoreArr.forEach(function(entry) {
			if (entry != 0) { // 0 Means Team did not Participate or DQ
				//if (entry == 0) { error2 = true;}
				if (sequence != entry && sequence < max) { error2 = true;}
				if (sequence != entry && max != entry) { error2 = true;}
				sequence++;
			}
		});

		if (error) {
			displayError("<strong>Cannot Save Scores:</strong> Team cannot have the same rank as another.");
			return false;
		}
		if (error2) {
			displayError("<strong>Cannot Save Scores:</strong> Ranks must be sequential (no rank skipped)."); // and cannot be 0
			return false;
		}
		if (error3) {
			displayError("<strong>Cannot Save Scores:</strong> All teams must be ranked to submit or verify scores.");
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
	
	function sortNumber(a,b) {
    	return a - b;
	}
	
	function showInstructions(shID) {
    if (document.getElementById(shID)) {
        if (document.getElementById('showInstructions').style.display != 'none') {
            document.getElementById('showInstructions').style.display = 'none';
            document.getElementById('hideInstructions').style.display = 'inline';
            document.getElementById(shID).style.height = '225px';
        }
        else {
            document.getElementById('showInstructions').style.display = 'inline';
            document.getElementById('hideInstructions').style.display = 'none';
            document.getElementById(shID).style.height = '0px';
        }
    }
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
	
		fieldset.utility-border {
		border: 1px solid #eee !important;
		padding: 0 1.4em 1.4em 1.4em !important;
		margin: 0 0 1.5em 0 !important;
		-webkit-box-shadow:  0px 0px 0px 0px #eee;
        box-shadow:  0px 0px 0px 0px #eee;
	}

	legend.utility-border {
		font-size: 1.2em !important;
		font-weight: bold !important;
		text-align: left !important;
		
		width:inherit;
		 padding:0 10px;
		 border-bottom:none;
	}
	
	a.hideLink {
		display: none;
	}
  
  	#instructionsText {
    	height: 0px;
    	overflow: hidden;
    	overflow-y: visible;
    	transition: height 2s;
    	-moz-transition: height 2s; /* Firefox 4 */
    	-webkit-transition: height 2s; /* Safari and Chrome */
    	-o-transition: height 2s; /* Opera */
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
	 <table>
	 <tr>
     <td><h4>Tournament: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentName"]. ' - ' . $_SESSION["tournamentDate"]; ?></span></h4></td>
	<td></td>
	 </tr>
	 <tr>
     <td><h4>Division: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentDivision"]; ?></span></h4></td>
	 <td></td>
	 </tr>
	 <tr>
	 <td><h4>Event: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["eventName"]; ?></span></h4></td>
	 <td></td>
	 </tr>
	 <tr>
	 <td><h4>Supervisor: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["eventSupervisor"]; ?></span></h4></td>
	 <td></td>
	 </tr>
	 </table>
	 <div class="instructions">
     <h6>* Instructions: 
     <a href="#" id="showInstructions" class="showLink" onclick="showInstructions('instructionsText');return false;">Click to Show</a> 
     <a href="#" id="hideInstructions" class="hideLink" onclick="showInstructions('instructionsText');return false;">Click to Hide</a>
     
     <div id="instructionsText" class="instructionsText"><br /><br />
     1. Enter the team's status. Use the status key below to determine the correct code. Non Participating and Disqualified teams should be ranked with 0 and will receive last place points.<br /><br />
     2. Enter the Raw Score (Exam Score, Calculated Score, Points Earned etc) for each team. Not Required.<br /><br />
     3. Enter the Tier or Rank Group if applicable for each team. Not Required<br /><br />
	 4. If the team has a tie, enter a short description of the tie breaker for the tied teams. Not Required<br /><br />
	 5. Enter the finishing rank each team earned. If the team did not participate or was disqualified, enter 0.
	 -OR- Click the Calculate Ranks button below to allow the system to automatically calculate event ranks. (Calculation algorithm for this event is: <?php echo $_SESSION["scoreSystemText"]; ?> wins.) Tied teams will require manually modification of their rank. All ranks can be modified manually after clicking the Calculate Ranks button. REQUIRED<br /><br />
	 6. Points earned will be calculated automatically. (Max points per event: <?php echo $_SESSION["highestScore"]; ?>. Tournament Winner: <?php echo $_SESSION["pointsSystem"]; ?>.)<br /><br />
     7. Click save to save the event's scores. Event scores can be modified after the initial save if they have not yet been submitted. Once submitted, only a score verifier can modify the scores.
     </div>
     </h6>  
     </div>  
	 <hr>

	 <table width="75%"><tr>
	 <td><label for="submittedFlag">Submitted</label> &nbsp;&nbsp;<input type="checkbox" id="submittedFlag" name="submittedFlag" <?php echo $disable.' '.$submitted; ?>  echo value="1"></td>
	 <td><label for="verifiedFlag">Verified</label> &nbsp;&nbsp;<input type="checkbox" id="verifiedFlag" name="verifiedFlag" <?php echo $disableVerfiy.' '.$verified; ?> value="1"></td>
	 <td align="right">Status Key: P = Participated, N = No Participation, D = Disqualified</td>
	 </tr></table>
	 <hr>
		<?php if ($_SESSION["teamAlternateEventScoreList"] != null) {?>		
		<fieldset class="utility-border"><legend class="utility-border">Primary Teams</legend>
		<?php } ?>
        <table class="table table-hover">
        <thead>
            <tr>
                <th width="6%" data-field="name" data-align="right" data-sortable="true">Team Number</th>
                <th width="20%" data-field="teamNumber" data-align="center" data-sortable="true">Team Name</th>
                <th width="9%" data-field="status" data-align="center" data-sortable="true">Status&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<th width="10%"data-field="score" data-align="center" data-sortable="true">Raw Score&nbsp;&nbsp;</th>
				<th width="5%" data-field="score" data-align="center" data-sortable="true">Tier/Rank Group</th>
				<th width="30%"data-field="score" data-align="center" data-sortable="true">Tie Break</th>			
                <th width="10%"data-field="score" data-align="center" data-sortable="true">Rank<span class="red">*</span></th>
				<th width="10%" data-field="score" data-align="center" data-sortable="true">Points Earned</th>
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
					echo '<td><select class="form-control" name="teamStatus'.$teamCount.'" id="teamStatus'.$teamCount.'" '.$disable.'>
			<option value="P" ';  if($scoreRecord['9'] == "P"){echo("selected");} echo '>P</option>
			<option value="N" ';  if($scoreRecord['9'] == "N"){echo("selected");} echo '>N</option>
			<option value="D" '; if($scoreRecord['9'] == "D"){echo("selected");} echo '>D</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.'    
      						name="teamRawScore'.$teamCount.'" id="teamRawScore'.$teamCount.'" value="'.$scoreRecord['6'].'" onkeyup="highlightRawScoreDuplication()" ></td>';
					echo '<td><select class="form-control" name="teamScoreTier'.$teamCount.'" id="teamScoreTier'.$teamCount.'" '.$disable.' onchange="highlightRawScoreDuplication()">
			<option value="0"></option>
			<option value="1" ';  if($scoreRecord['7'] == "1"){echo("selected");} echo '>I</option>
			<option value="2" '; if($scoreRecord['7'] == "2"){echo("selected");} echo '>II</option>
			<option value="3" ';if($scoreRecord['7'] == "3"){echo("selected");} echo '>III</option>
			<option value="4" '; if($scoreRecord['7'] == "4"){echo("selected");} echo '>IV</option>
			<option value="5" ';  if($scoreRecord['7'] == "5"){echo("selected");} echo '>V</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.'    
      						name="teamTieBreak'.$teamCount.'" id="teamTieBreak'.$teamCount.'" value="'.$scoreRecord['8'].'"></td>';
      				echo '<td style="background-color: #FFCCCC;"><input type="text"  class="form-control" size="4" autocomplete="off" onkeydown="updatePointsEarned(\'teamScore\',\''.$teamCount.'\',\'teamPointsEarned\');" onkeyup="updatePointsEarned(\'teamScore\',\''.$teamCount.'\',\'teamPointsEarned\');" '.$disable.'    
      						name="teamScore'.$teamCount.'" id="teamScore'.$teamCount.'" value="'.$scoreRecord['2'].'">'; // set background color
      				echo '</td>';
      				echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" readonly   
      						name="teamPointsEarned'.$teamCount.'" id="teamPointsEarned'.$teamCount.'" value="'.$scoreRecord['5'].'"></td>';					
					echo '</tr>';
					
					$teamCount++;	
      			}
    		}
    	}
        ?>
        
          </tbody>
          </table>
		  
		<?php if ($_SESSION["teamAlternateEventScoreList"] != null) {?>
		</fieldset>
		<fieldset class="utility-border"><legend class="utility-border">Alternate Teams</legend>
		<table class="table table-hover">
        <thead>
            <tr>
                <th width="6%" data-field="name" data-align="right" data-sortable="true">Team Number</th>
                <th width="20%" data-field="teamNumber" data-align="center" data-sortable="true">Team Name</th>
                <th width="9%" data-field="status" data-align="center" data-sortable="true">Status&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<th width="10%"data-field="score" data-align="center" data-sortable="true">Raw Score&nbsp;&nbsp;</th>
				<th width="5%" data-field="score" data-align="center" data-sortable="true">Tier/Rank Group</th>
				<th width="30%"data-field="score" data-align="center" data-sortable="true">Tie Break</th>	
                <th width="10%"data-field="score" data-align="center" data-sortable="true">Rank</th>
				<th width="10%" data-field="score" data-align="center" data-sortable="true">Points Earned</th>
            </tr>
        </thead>
        <tbody>
         <?php
         if ($_SESSION["teamAlternateEventScoreList"] != null and $_SESSION["teamAlternateEventScoreList"] != '') {			
 			if ($_SESSION["teamAlternateEventScoreList"] ) {
 				$teamCount = 0;
      			foreach ($_SESSION["teamAlternateEventScoreList"] as $scoreRecord) {
      				echo '<tr>';
      				echo '<td>'; echo $scoreRecord['1']; echo '</td>';
					echo '<td>'; echo $scoreRecord['0'];; echo '</td>';
			echo '<td><select class="form-control" name="teamAStatus'.$teamCount.'" id="teamAStatus'.$teamCount.'" '.$disable.'>
			<option value="P" ';  if($scoreRecord['9'] == "P"){echo("selected");} echo '>P</option>
			<option value="N" ';  if($scoreRecord['9'] == "N"){echo("selected");} echo '>N</option>
			<option value="D" '; if($scoreRecord['9'] == "D"){echo("selected");} echo '>D</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.'    
      						name="teamARawScore'.$teamCount.'" id="teamARawScore'.$teamCount.'" value="'.$scoreRecord['6'].'" ></td>';
					echo '<td><select class="form-control" name="teamAScoreTier'.$teamCount.'" id="teamAScoreTier'.$teamCount.'" '.$disable.'>
			<option value="0"></option>
			<option value="1" ';  if($scoreRecord['7'] == "1"){echo("selected");} echo '>I</option>
			<option value="2" '; if($scoreRecord['7'] == "2"){echo("selected");} echo '>II</option>
			<option value="3" ';if($scoreRecord['7'] == "3"){echo("selected");} echo '>III</option>
			<option value="4" '; if($scoreRecord['7'] == "4"){echo("selected");} echo '>IV</option>
			<option value="5" ';  if($scoreRecord['7'] == "5"){echo("selected");} echo '>V</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.'    
      						name="teamATieBreak'.$teamCount.'" id="teamATieBreak'.$teamCount.'" value="'.$scoreRecord['8'].'"></td>';
      				echo '<td style="background-color: #FFCCCC;"><input type="text"  class="form-control" size="4" autocomplete="off"  '.$disable.'    
      						name="teamAScore'.$teamCount.'" id="teamAScore'.$teamCount.'" value="'.$scoreRecord['2'].'" onkeydown="updatePointsEarned(\'teamAScore\',\''.$teamCount.'\',\'teamAPointsEarned\');" onkeyup="updatePointsEarned(\'teamAScore\',\''.$teamCount.'\',\'teamAPointsEarned\');" >'; // set background color
      				echo '</td>'; // 
      				echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" readonly   
      						name="teamAPointsEarned'.$teamCount.'" id="teamAPointsEarned'.$teamCount.'" value="'.$scoreRecord['5'].'"></td>';					
					echo '</tr>';
					
					$teamCount++;	
      			}
    		}
    	}
        ?>
          </tbody>
          </table>
		  </fieldset>
		<?php } ?>
          <label for="eventComments">Supervisor's Comments</label><br />
          <textarea class="form-control"  name="eventComments" id="eventComments" spellcheck="true" rows="5" cols="100"><?php echo $_SESSION["eventComments"];?></textarea>
          <br /> <br />

        <?php if ($disable != 'disabled')   { ?>
		<button type="submit" class="btn btn-xs btn-danger" name="saveEventScores" onclick="return validate()" value=<?php echo '"'.$_SESSION["tournEventId"].'"' ?>>Save</button>
		<button type="button" class="btn btn-xs btn-danger" name="calculateEventScores" onclick="calculateScorez('<?php echo addslashes($_SESSION["eventName"]); ?>','<?php echo $_SESSION["tournamentDivision"]; ?>','<?php echo $_SESSION["scoreSystemCode"]; ?>');" >Calculate Ranks</button>
		<button type="button" class="btn btn-xs btn-danger" name="clearScores" onclick="resetScores();" >Clear Scores</button>
		<?php } ?>
 	 	<button type="submit" class="btn btn-xs btn-primary" name="cancelEventScores" onclick="return confirmCancel()">Cancel</button>

      <hr>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>	
    <script type="text/javascript">
    	highlightRawScoreDuplication();
    </script>
    
  </body>
</html>