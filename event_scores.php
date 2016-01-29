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
		$disableTier = '';
		$submitted = '';
		$verified = '';
        if ($userRole == 'SUPERVISOR' and $_SESSION["submittedFlag"] == '1') $disable = 'disabled';
        if ($userRole == 'SUPERVISOR') $disableVerfiy = 'disabled';
        if ($_SESSION["submittedFlag"] == '1') $submitted = 'checked';
	 	if ($_SESSION["verifiedFlag"] == '1') $verified = 'checked';
		
		// Global Score Lock
		if ($_SESSION["lockScoresFlag"] == 1) {$disable = 'disabled'; $disableVerfiy = 'disabled'; }
		// Disable Tier Depending on Algorithm		
		if ($_SESSION["scoreSystemText"] == 'High Raw Score' or $_SESSION["scoreSystemText"] == 'Low Raw Score' or $disable == 'disabled') {
			$disableTier = 'disabled';		
		}
		
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
	
	function parseNumber(element) {
		if (isNaN(element.value)) element.value = '';
	}
	function parseRawNumber(element) {
		var str = element.value.trim();
		if (str.length == 1 && str.charAt(0) == '-') return;
		if (isNaN(element.value)) element.value = '';
	}
	
	function updatePointsEarned(section, id, type, status) {
		var element = document.getElementById(section+id);
		var max = <?php echo $_SESSION["tournamentHighestScore"];?>;
		if (section == 'teamAScore') max = <?php echo $_SESSION["highestScoreAlt"];?>;
		var maxA = <?php echo $_SESSION["highestScoreAlt"];?>;
		var lowHighFlag = <?php echo $_SESSION["highLowWinFlag"];?>;
		
		var npPoints = <?php echo $_SESSION["pointsForNP"];?>;
		var dqPoints = <?php echo $_SESSION["pointsForDQ"];?>;
		var pxPoints = 0;
		var extraPoints = 0;
		if (document.getElementById(status+id) != null && document.getElementById(status+id).value == 'N') extraPoints += npPoints;
		else if (document.getElementById(status+id) != null && document.getElementById(status+id).value == 'D') extraPoints += dqPoints;
		else if (document.getElementById(status+id) != null && document.getElementById(status+id).value == 'X') extraPoints += pxPoints;
		
		
		if (lowHighFlag == 0) {
			if (element.value == 0) document.getElementById(type+id).value = (max + extraPoints);
			else if (element.value > max) document.getElementById(type+id).value = max;
			else document.getElementById(type+id).value = element.value;			
		}
		else {
			if (element.value == 0) document.getElementById(type+id).value = (1 + extraPoints);
			else if ((max + 1 - element.value) > 0) document.getElementById(type+id).value = max + 1 - element.value;
			else document.getElementById(type+id).value = 1;
		}
		
	}
	
	function highlightRawScoreDuplication() {
		var count = 0;
		var scoreArr = [];
		var duplicates = {};
		var key = '';
		var pCount = 0;
		var colorPalette = ["#FFD5D5","#FFFFCC","#E1F7D5","#C9C9FF","#F1CBFF","#FFE7CC","#CCFFFD","#EBE8E0","#939393","#CFE4F1"];
		
		while (count < 1000) {
			if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				var score = $('#teamRawScore'+count).val();
				var tier = $('#teamScoreTier'+count).val();
				document.getElementById('teamRawScore'+count).style.backgroundColor = "#FFFFFF";
				scoreArr.forEach(function(entry) {				
					if (score == entry[0] && tier == entry[1]) {
						key = entry[0] + '-' + entry[1];
						if (key in duplicates);
						else {
							duplicates[key] = colorPalette[pCount];
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
				var tier = $('#teamScoreTier'+count).val();
				key = score + '-' + tier;
				if (key in duplicates) {
					document.getElementById('teamRawScore'+count).style.backgroundColor = duplicates[key];			
				}
			}
			else break;
			count++;
		}
	}
	
		function highlightARawScoreDuplication() {
		var count = 0;
		var scoreArr = [];
		var duplicates = {};
		var key = '';
		var pCount = 0;
		var colorPalette = ["#FFD5D5","#FFFFCC","#E1F7D5","#C9C9FF","#F1CBFF","#FFE7CC","#CCFFFD","#EBE8E0","#939393","#CFE4F1"];
		
		while (count < 1000) {
			if  ($('#teamARawScore'+count) != null && $('#teamARawScore'+count).val() != null) {
				var score = $('#teamARawScore'+count).val();
				var tier = $('#teamAScoreTier'+count).val();
				document.getElementById('teamARawScore'+count).style.backgroundColor = "#FFFFFF";
				scoreArr.forEach(function(entry) {				
					if (score == entry[0] && tier == entry[1]) {
						key = entry[0] + '-' + entry[1];
						if (key in duplicates);
						else {
							duplicates[key] = colorPalette[pCount];
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
			if  ($('#teamARawScore'+count) != null && $('#teamARawScore'+count).val() != null) {
				var score = $('#teamARawScore'+count).val();
				var tier = $('#teamAScoreTier'+count).val();
				key = score + '-' + tier;
				if (key in duplicates) {
					document.getElementById('teamARawScore'+count).style.backgroundColor = duplicates[key];			
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
            document.getElementById(shID).style.height = '375px';
        }
        else {
            document.getElementById('showInstructions').style.display = 'inline';
            document.getElementById('hideInstructions').style.display = 'none';
            document.getElementById(shID).style.height = '0px';
        }
    }
	}


    jQuery(document).ready(function($){
    	$('#rankBox').popBox({width:200,height:350}, 'copyPaste');
   		$('#rawBox').popBox({width:200,height:350},'copyPaste');
   		$('#tierBox').popBox({width:200,height:350},'copyPaste');
   		
   		$('#pasteRanks').click(function(){
   		 	$('#rankBox').triggerHandler('focus');
		});
		$('#pasteRaw').click(function(){
   		 	$('#rawBox').triggerHandler('focus');
		});
		$('#pasteTier').click(function(){
   		 	$('#tierBox').triggerHandler('focus');
		});
   		
    });
 
  
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
	
	.blankButton {
	    background:none;
   		border:none;
   	 	margin:0;
    	padding:0;
    	font-size:15px;
    	color: #23527c;

	}
  
  </style>
  </head>
  
  <body>
  <?php include_once 'navbar.php'; ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
     
      <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
      
       <?php 
       $pointsLabel = 'Last Place Points';
       if ($_SESSION["pointsSystem"]=='High Score Wins' )  $pointsLabel = 'First Place Points'; ?>
        
     <h1>Enter Event Scores</h1>
	 <table width="100%">
	 <tr>
     <td width="50%"><h4>Tournament: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentName"]. ' - ' . $_SESSION["tournamentDate"]; ?></span></h4></td>
	 <td width="50%"><h4>Event: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["eventName"]; ?></span></h4></td>
	 </tr>
	 <tr>
     <td><h4>Division: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["tournamentDivision"]; ?></span></h4></td>
	 <td><h4>Supervisor: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["eventSupervisor"]; ?></span></h4></td>
	 </tr>
	 <tr>
	 <td><h4>Event Scoring Algorithm: <span style="font-weight:normal;font-size:14px;"><?php echo $_SESSION["scoreSystemText"]; ?> Wins</span></h4></td>
	 <td></td>
<!--	 <tr>
	 <td><h4><?php //echo $pointsLabel;?> (Alternate Teams): <span style="font-weight:normal;font-size:14px;"><?//php echo $_SESSION["highestScoreAlt"]; ?></span></h4></td>
	 <td><h4><?php //echo $pointsLabel;?> (Primary Teams): <span style="font-weight:normal;font-size:14px;"><?php //echo $_SESSION["highestScore"]; ?></span></h4></td>
	 </tr> -->
	 </tr>
	 </table>
	 <div class="instructions">
     <h6>* Instructions: 
     <a href="#" id="showInstructions" class="showLink" onclick="showInstructions('instructionsText');return false;">Click to Show</a> 
     <a href="#" id="hideInstructions" class="hideLink" onclick="showInstructions('instructionsText');return false;">Click to Hide</a>
     
     <div id="instructionsText" class="instructionsText"><br /><br />
     <table width="100%">
     <tr><td width="1%" valign="top">0.</td>
     <td width="99%">For each Primary and Alternate Team, do the following: <br /><br /></td>
     </tr>
          <tr><td valign="top">1.</td>
     <td><b><u>Status</u></b> Enter the team's status. Use the key below to determine the correct code. <br /><br /></td>
     </tr>
          <tr><td valign="top">2.</td>
     <td><b><u>Raw Score</u></b> Enter the Raw Score (Exam Score, Calculated Score, Points Earned etc).<br /><br /></td>
     </tr>
          <tr><td valign="top">3.</td>
     <td><b><u>Tier</u></b> Enter the Tier or Rank Group if applicable for each team. Drop down may be disabled if this does not apply to your event.<br /><br /></td>
     </tr>
          <tr><td valign="top">4.</td>
     <td><b><u>Calculate</u></b> Click the Calculate Ranks button at the bottom of the screen to allow the system to automatically calculate event ranks. (Calculation algorithm for this event is: <?php echo $_SESSION["scoreSystemText"]; ?> wins.) <br /><br /></td>
     </tr>
          <tr><td valign="top">5.</td>
     <td><b><u>Ties</u></b> Once the raw score and tier (if applicable) have been entered, the raw score text field may change color. This means the team is tied. (Has the same raw score and tier). All ties must be broken. You will manually break the tie by changing the team's 'Rank' (After the calculate ranks button has been clicked) By default, the tied teams will be given the same rank. For example, if two teams tie for fourth place, they will each be assigned 4 as there rank. The loser of the tie breaker should have their rank set to 5. You may write a description in the Tie Break field as to how the tie was broken.<br /><br /></td>
     </tr>
               <tr><td valign="top">6.</td>
     <td><b><u>Primary vs. Alternates</u></b> Primary teams and Alternate teams will be ranked independent of each other. These teams do not compete against one another. The Primary teams will be ranked starting at 1 and the Alternate teams will be ranked starting at 1. <br /><br /></td>
     </tr>
          <tr><td valign="top">7.</td>
     <td><b><u>Rank Errors</u></b> If the ranks are not calculated correctly, you can manually set all the team's ranks. Enter 0 if the team's status is PX, NP, or DQ. Two team's cannot have the same rank unless the rank is 0. All ranks must be sequential starting at 1. (Clicking the Calculate rank button will overwrite the rank values)<br /><br /></td>
     </tr>
          <tr><td valign="top">8.</td>
     <td><b><u>Points Earned</u></b> Points earned will be calculated automatically and used for the overall tournament rankings. Tournament Winner: <?php echo $_SESSION["pointsSystem"]; ?>.) Participating teams will earn points corresponding to their rank value. Teams with a status of PX will earn last place points. Teams with a status of NP will earn last place points + <?php echo $_SESSION["pointsForNP"]; ?>. Teams with a status of DQ will earn last place points + <?php echo $_SESSION["pointsForDQ"]; ?>.<br /><br /></td>
     </tr>
               <tr><td valign="top">9.</td>
     <td><b><u>Save</u></b> Click the save button to save the event scores. Event scores can be modified after the initial save if they have not yet been submitted. Once submitted, only a score verifier can modify the scores.<br /><br /></td>
     </tr>
	 </table>



     </div>
     </h6>  
     </div>  
	 <hr>

	 <table width="100%"><tr>
	 <td><label for="submittedFlag">Submitted</label> &nbsp;&nbsp;<input type="checkbox" id="submittedFlag" name="submittedFlag" <?php echo $disable.' '.$submitted; ?>  value="1"></td>
	 <td><label for="verifiedFlag">Verified</label> &nbsp;&nbsp;<input type="checkbox" id="verifiedFlag" name="verifiedFlag" <?php echo $disableVerfiy.' '.$verified; ?> value="1"></td>
	 <?php if ($_SESSION["lockScoresFlag"] == '1')  echo '<td>(LOCKED)</td>'; ?>
	 <td align="right">Status Key: <b>P</b> = Participated, <b>PX</b> = Participated (Unable to Score), <b>NP</b> = No Participation, <b>DQ</b> = Disqualified</td>
	 </tr></table>
	 <hr>
		<?php if ($_SESSION["teamEventScoreList"] != null) {?>		
		<fieldset class="utility-border"><legend class="utility-border">Primary Teams</legend>
		<?php } ?>
        <table class="table table-hover" id="primaryTeamTable">
        <thead>
            <tr>
                <th width="6%" data-field="name" data-align="right" data-sortable="true">Team Number</th>
                <th width="20%" data-field="teamNumber" data-align="center" data-sortable="true">Team Name</th>
                <th width="9%" data-field="status" data-align="center" data-sortable="true">Status&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<th width="10%"data-field="score" data-align="center" data-sortable="true">Raw Score 
					<?php if ($disable != 'disabled') { ?><input class="blankButton" type="button" id="pasteRaw" value='+' /><?php } ?></th>
				<th width="5%" data-field="score" data-align="center" data-sortable="true">Tier/Rank Group 
					<?php if ($disableTier != 'disabled') { ?><input class="blankButton"type="button" id="pasteTier"value='+' /><?php } ?></th>
				<th width="30%"data-field="score" data-align="center" data-sortable="true">Tie Break</th>			
                <th width="10%"data-field="score" data-align="center" data-sortable="true">Rank<span class="red">*</span> 
                	<?php if ($disable != 'disabled') { ?><input class="blankButton" type="button" id="pasteRanks"value='+' /><?php } ?></th>
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
			<option value="X" ';  if($scoreRecord['9'] == "X"){echo("selected");} echo '>PX</option>
			<option value="N" ';  if($scoreRecord['9'] == "N"){echo("selected");} echo '>NP</option>
			<option value="D" '; if($scoreRecord['9'] == "D"){echo("selected");} echo '>DQ</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.'    
      						name="teamRawScore'.$teamCount.'" id="teamRawScore'.$teamCount.'" value="'.$scoreRecord['6'].'" onkeyup="javascript: parseRawNumber(this); highlightRawScoreDuplication();" ></td>';
					echo '<td><select class="form-control" name="teamScoreTier'.$teamCount.'" id="teamScoreTier'.$teamCount.'" '.$disableTier.' onchange="javascript: parseRawNumber(this); highlightRawScoreDuplication();">
			<option value="0"></option>
			<option value="1" ';  if($scoreRecord['7'] == "1"){echo("selected");} echo '>I</option>
			<option value="2" '; if($scoreRecord['7'] == "2"){echo("selected");} echo '>II</option>
			<option value="3" ';if($scoreRecord['7'] == "3"){echo("selected");} echo '>III</option>
			<option value="4" '; if($scoreRecord['7'] == "4"){echo("selected");} echo '>IV</option>
			<option value="5" ';  if($scoreRecord['7'] == "5"){echo("selected");} echo '>V</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.'    
      						name="teamTieBreak'.$teamCount.'" id="teamTieBreak'.$teamCount.'" value="'.$scoreRecord['8'].'"></td>';
      				echo '<td style="background-color: #FFCCCC;"><input type="text"  class="form-control" size="4" autocomplete="off" onkeydown="javascript: parseNumber(this); updatePointsEarned(\'teamScore\',\''.$teamCount.'\',\'teamPointsEarned\',\'teamStatus\');" onkeyup="javascript: parseNumber(this); updatePointsEarned(\'teamScore\',\''.$teamCount.'\',\'teamPointsEarned\',\'teamStatus\');" '.$disable.'    
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
		  
		<?php if ($_SESSION["teamEventScoreList"] != null) {?>
		</fieldset>
		<?php } ?>
		<?php if ($_SESSION["teamAlternateEventScoreList"] != null) {?>
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
			<option value="X" ';  if($scoreRecord['9'] == "X"){echo("selected");} echo '>PX</option>
			<option value="N" ';  if($scoreRecord['9'] == "N"){echo("selected");} echo '>NP</option>
			<option value="D" '; if($scoreRecord['9'] == "D"){echo("selected");} echo '>DQ</option>
			</select></td>';
					echo '<td><input type="text"  class="form-control" size="4" autocomplete="off" '.$disable.' onkeyup="javascript: parseRawNumber(this); highlightARawScoreDuplication();"    
      						name="teamARawScore'.$teamCount.'" id="teamARawScore'.$teamCount.'" value="'.$scoreRecord['6'].'" ></td>';
					echo '<td><select class="form-control" name="teamAScoreTier'.$teamCount.'" id="teamAScoreTier'.$teamCount.'" '.$disableTier.' onchange="javascript: parseRawNumber(this); highlightARawScoreDuplication();">
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
      						name="teamAScore'.$teamCount.'" id="teamAScore'.$teamCount.'" value="'.$scoreRecord['2'].'" onkeydown="javascript: parseNumber(this); updatePointsEarned(\'teamAScore\',\''.$teamCount.'\',\'teamAPointsEarned\',\'teamAStatus\');" onkeyup="javascript: parseNumber(this);  updatePointsEarned(\'teamAScore\',\''.$teamCount.'\',\'teamAPointsEarned\',\'teamAStatus\');" >'; // set background color
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
          <textarea class="form-control"  name="eventComments" id="eventComments" spellcheck="true" rows="5" cols="100" <?php echo $disable; ?>><?php echo $_SESSION["eventComments"];?></textarea>
          <br /> <br />

        <?php if ($disable != 'disabled')   { ?>
		<button type="submit" class="btn btn-xs btn-danger" name="saveEventScores" onclick="return validate()" value=<?php echo '"'.$_SESSION["tournEventId"].'"'; ?>>Save</button>
		<button type="button" class="btn btn-xs btn-danger" name="calculateEventScores" onclick="calculateScorez('<?php echo addslashes($_SESSION["eventName"]); ?>','<?php echo $_SESSION["tournamentDivision"]; ?>','<?php echo $_SESSION["scoreSystemCode"]; ?>');" >Calculate Ranks</button>
		<button type="button" class="btn btn-xs btn-danger" name="clearScores" onclick="resetScores();" >Clear Scores</button>
		<button type="submit" class="btn btn-xs btn-primary" name="cancelEventScores" onclick="return confirmCancel()">Cancel</button>
		<?php } else { ?>
 	 	<button type="submit" class="btn btn-xs btn-primary" name="cancelEventScores">Cancel</button>
		<?php } ?>
      <hr>
      <textarea id="rankBox" style="display:none;"></textarea>
      <textarea id="rawBox" style="display: none;"></textarea>
      <textarea id="tierBox" style="display: none;"></textarea>
	<?php include_once 'footer.php'; ?>

    </div><!--/.container-->
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>	
    <script type="text/javascript">
    	highlightRawScoreDuplication();
    	highlightARawScoreDuplication();
    </script>
    
  </body>
</html>