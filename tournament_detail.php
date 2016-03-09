<?php session_start(); 
	include_once('score_center_objects.php');
	include_once('logon_check.php');

	// Security Level Check
	include_once('role_check.php');
	checkUserRole(2);
	
	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
	$userRole = $userSessionInfo->getRole();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include_once('libs/head_tags.php'); ?>
	
  

  <script type="text/javascript">
  $(document).ready(function(){
		// Load link tournament selects
    	loadLinkedTournaments();
	});
	
	function limitNumber(element) {
		var max = 1000;
		if (isNaN(element.value)) element.value = '';
		if (element.value > max || element.value < 0) element.value = '';
	}
	function limitNumberNegatives(element) {
		var max = 1000;
		if (element.value.indexOf('-') == 0 && element.value.length == 1) return;
		if (isNaN(element.value)) element.value = '';
		if (element.value > max) element.value = '';
	}
	
	function validate() {
		clearError();
		clearSuccess();
		var error = false;
		var error2 = false;
		var fields = ["tournamentName", "tournamentDivision", "tournamentLocation","tournamentDate","numberEvents","numberTeams","highestScore","eventsAwarded","overallAwarded", "highestScoreAlt", "pointsForNP","pointsForDQ","eventsAAwarded","overallAAwarded"];
		var str;
		for (str in fields) {
			if (document.getElementById(fields[str]).value.length === 0 || !document.getElementById(fields[str]).value.trim()) {
				error = true;
			}
		}
		if (!document.getElementById("totalPointsWins0").checked && !document.getElementById("totalPointsWins1").checked) error = true;
		if($('#tournamentName').val().indexOf('"') != -1) {	
			error2 = true;
		}
		// validate Unique Team Number
		var scoreArr = [];
		var errorDuplicateNumber = false;
		var count = 0;
		while (count < 1000) {
			if  ($('#teamNumber'+count) != null && $('#teamNumber'+count).val() != null) {
				var score = $('#teamNumber'+count).val();
				
				scoreArr.forEach(function(entry) {
					if (score == entry) errorDuplicateNumber = true;
				});
				if (errorDuplicateNumber) {
					break;
				}
				else if (score != '' && score != '0') {
					scoreArr.push(score);
				}
			} else {
				break;
			}
			count++;
		}
		
		if (errorDuplicateNumber) {
			displayError("<strong>Validation Error:</strong> Teams must have a unique team number.");
			return false;
		}		
		if (error) {
			displayError("<strong>Required Fields:</strong> Please complete the required fields denoted with an ' * '.");
			return false;			
		}
		if (error2) {
			displayError("<strong>Validation Error:</strong> Tournament Name and Location fields cannot contain the symbol ' \" '.");
			return false;	
		}
		else {
			clearError();
			document.getElementById("lockScoresFlag").disabled = false;
			return true;
		}
	}
	
	function validateDeleteEvent(element) {
		if (!confirmDelete('event')) return;
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					clearError();
					clearSuccess();
					if (xmlhttp.responseText.trim() == 'error') {
						//error message
						displayError("<strong>Cannot Delete Event:</strong> Scores have already been entered for this event.")					
					}
					else {
						// success message
						displaySuccess("<strong>Deleted:</strong> Event has been deleted successfully!");
						// remove from table
						document.getElementById('eventTableBody').innerHTML = xmlhttp.responseText;
					}					
				}
			}	
        xmlhttp.open("GET","controller.php?command=validateDeleteEvent&TournEventRowId="+$(element).closest('tr').index()+generateEventParamsString(),true);
        xmlhttp.send();
	}
	
	function validateDeleteTeam(element) {
		if (!confirmDelete('team')) return;
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					clearError();
					clearSuccess();
					if (xmlhttp.responseText.trim() == 'error') {
						//error message
						displayError("<strong>Cannot Delete Team:</strong> Scores have already been entered for this team.")	
					}
					else {
						// success message
						displaySuccess("<strong>Deleted:</strong> Team has been deleted successfully!");
						// remove from table
						document.getElementById('teamTableBody').innerHTML = xmlhttp.responseText;
					}					
				}
			}	
        xmlhttp.open("GET","controller.php?command=validateDeleteTeam&TournTeamRowId="+$(element).closest('tr').index()+generateTeamParamsString,true);
        xmlhttp.send();
	}
	
	function validateDeleteVerifier(element) {
		if (!confirmDelete('verifier')) return;
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					clearError();
					clearSuccess();
					if (xmlhttp.responseText.trim() == 'error') {
						//error message	
					}
					else {
						// success message
						displaySuccess("<strong>Deleted:</strong> Verifier has been deleted successfully!");
						// remove from table
						document.getElementById('verifierTableBody').innerHTML = xmlhttp.responseText;
					}					
				}
			}	
        xmlhttp.open("GET","controller.php?command=validateDeleteVerifier&verifierTournId="+$(element).closest('tr').index(),true);
        xmlhttp.send();	
	}
	
	
	function addTournEvent() {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			clearError();
			clearSuccess();
			if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
				//error message
				if (xmlhttp.responseText.trim() == 'error1') displayError("<strong>Cannot Add Event:</strong> Event already added or no event selected.");
				else if (xmlhttp.responseText.trim() == 'error2') displayError("<strong>Cannot Add Event:</strong> Cannot add more than "+document.getElementById('numberEvents').value+" events.");				
			}
			else {
				// success message
				document.getElementById('eventTableBody').innerHTML = xmlhttp.responseText;

				}					
		}
		}	
        xmlhttp.open("GET","controller.php?command=addEvent&eventAdded="+document.getElementById('eventAdded').value+generateEventParamsString()
						+ getNumberEventsTeams(),true);
        xmlhttp.send();
	}
		
  	function addTournTeam() {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			clearError();
			clearSuccess();
			if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
				//error message 
				if (xmlhttp.responseText.trim() == 'error1') displayError("<strong>Cannot Add Team:</strong> Team already added or no team selected.");
				else if (xmlhttp.responseText.trim() == 'error2') displayError("<strong>Cannot Add Team:</strong> Cannot add more than "+document.getElementById('numberTeams').value+" teams.");
			}
			else {
				// success message
				document.getElementById('teamTableBody').innerHTML = xmlhttp.responseText;

				}					
		}
		}	
        xmlhttp.open("GET","controller.php?command=addTeam&teamAdded="+document.getElementById('teamAdded').value+generateTeamParamsString()
						+getNumberEventsTeams(),true);
        xmlhttp.send();
	}
	
	  function addTournVerifier() {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			clearError();
			clearSuccess();
			if (xmlhttp.responseText.trim() == 'error1' || xmlhttp.responseText.trim() == 'error2') {
				//error message 
				if (xmlhttp.responseText.trim() == 'error1') displayError("<strong>Cannot Add Verifier:</strong> Verifier already added or no verifier selected.");
			}
			else {
				// success message
				document.getElementById('verifierTableBody').innerHTML = xmlhttp.responseText;

				}					
		}
		}	
        xmlhttp.open("GET","controller.php?command=addVerifier&verifierAdded="+document.getElementById('verifierAdded').value+generateTeamParamsString()
						+getNumberEventsTeams(),true);
        xmlhttp.send();
	}
	
	function generateEventParamsString() {
		var str = "";
		var count = 0;
		while (count < 100) {
			if (document.getElementById('trialEvent'+count) != null) {
				str += "&trialEvent"+count+"="+document.getElementById('trialEvent'+count).value;
				str += "&eventSupervisor"+count+"="+document.getElementById('eventSupervisor'+count).value;
			}
			count++;
		}
		return str;
	}
	function generateTeamParamsString() {
		var str = "";
		var count = 0;
		while (count < 100) {
			if (document.getElementById('alternateTeam'+count) != null) {
				str += "&alternateTeam"+count+"="+document.getElementById('alternateTeam'+count).value;
			}
			if (document.getElementById('teamNumber'+count) != null) {
				str += "&teamNumber"+count+"="+document.getElementById('teamNumber'+count).value;
			}
			if (document.getElementById('bestNewTeam'+count) != null && document.getElementById('bestNewTeam'+count).checked) {
				str += "&bestNewTeam="+document.getElementById('bestNewTeam'+count).value;
			}
			if (document.getElementById('mostImprovedTeam'+count) != null && document.getElementById('mostImprovedTeam'+count).checked) {
				str += "&mostImprovedTeam="+document.getElementById('mostImprovedTeam'+count).value;
			}
			count++;
		}
		return str;
	}
	
	function getNumberEventsTeams() {
		return "&numberEvents="+document.getElementById('numberEvents').value+"&numberTeams="+document.getElementById('numberTeams').value;
	}
	
	function loadDivisonTeams() {
		var division = document.getElementById('tournamentDivision').value;
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.responseText.indexOf("teamAdded") > -1) {
				document.getElementById('teamsSelectDiv').innerHTML = xmlhttp.responseText;
			}
		}	
        xmlhttp.open("GET","controller.php?command=loadDivisionTeams&division="+division,true);
        xmlhttp.send();
		
	}
	
	function loadLinkedTournaments() {
		var division = document.getElementById('tournamentDivision').value;
		var tournDate = document.getElementById('tournamentDate').value;
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.responseText.indexOf("teamAdded") == -1) {
				var html = xmlhttp.responseText;
				var lists = html.split('*****');
				document.getElementById('tourn1LinkedDiv').innerHTML = lists[0];
				document.getElementById('tourn2LinkedDiv').innerHTML = lists[1];
			}
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				loadDivisonTeams();
			}
			
		}	
        xmlhttp.open("GET","controller.php?command=loadLinkedTournaments&division="+division+"&date="+tournDate,true);
        xmlhttp.send();
	}
	
	function generateUserLogins() {
		if (confirm('Are you sure you want to create new users for all tournament events? This will save the tournament and overwrite current users.'))
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
 
       <?php
        // Load Events and Teams
        	require_once 'login.php';
		 	$db_server = mysql_connect($db_hostname, $db_username, $db_password);
 			if (!db_server) die("Unable to connect to MySQL: " . mysql_error());			
 			mysql_select_db($db_database);
    		
    		$teams = null;
			$verifiers = mysql_query("SELECT DISTINCT USER_ID, CONCAT(LAST_NAME,', ',FIRST_NAME,' (', USERNAME,')') AS USER
    									 FROM USER WHERE ROLE_CODE='VERIFIER' ORDER BY UPPER(LAST_NAME) ASC");
    		$events = mysql_query("SELECT DISTINCT * FROM EVENT ORDER BY NAME ASC");
    		if ($_SESSION["tournamentDivision"] == null or $_SESSION["tournamentDivision"] == '') $teams = mysql_query("SELECT DISTINCT * FROM TEAM ORDER BY NAME ASC");
    		else $teams = mysql_query("SELECT DISTINCT * FROM TEAM WHERE DIVISION = '".$_SESSION["tournamentDivision"]."' ORDER BY NAME ASC"); 
    		$supervisors = mysql_query("SELECT DISTINCT X.* FROM (
										SELECT DISTINCT U.USER_ID, CONCAT(U.LAST_NAME,', ',U.FIRST_NAME,' (', U.USERNAME,')') AS USER
										FROM USER U WHERE U.ROLE_CODE='SUPERVISOR' AND COALESCE(U.AUTO_CREATED_FLAG,0) <> 1 AND ACCOUNT_ACTIVE_FLAG=1
										UNION
										SELECT DISTINCT U1.USER_ID, CONCAT(U1.LAST_NAME,', ',U1.FIRST_NAME,' (', U1.USERNAME,')') AS USER
										FROM USER U1
										INNER JOIN TOURNAMENT_EVENT TE on TE.USER_ID=U1.USER_ID and TE. TOURNAMENT_ID=".$_SESSION["tournamentId"]."
										WHERE U1.ROLE_CODE='SUPERVISOR'
										) X
										ORDER BY UPPER(X.USER) ASC");
        ?>
  
  	<form action="controller.php" method="GET">
     <div class="container">
	 
	 <div id="errors" class="alert alert-danger" role="alert" style="display: none;"></div>
	 <div id="messages" class="alert alert-success" role="alert" style="display: none;"></div>
	 
     <h1>Edit Tournament</h1>
	 <hr>
	 
	<table width="100%" class="borderless"> 
	<tr>
		<td width="15%"><label for="tournamentName">Tournament Name:<span class="red">*</span></label></td>
		<td width="35%"><input type="text" class="form-control" name="tournamentName" id="tournamentName" size="50" value=<?php echo '"'.$_SESSION["tournamentName"].'"' ?>></td>
		<td width="15%"><label for="tournamentDivision">Division:<span class="red">*</span></label></td>
		<td width="35%"><select class="form-control" name="tournamentDivision" id="tournamentDivision" onchange="javascript: loadLinkedTournaments();">
			<option value=""></option>
			<option value="A" <?php if($_SESSION["tournamentDivision"] == 'A'){echo("selected");}?>>A</option>
			<option value="B" <?php if($_SESSION["tournamentDivision"] == 'B'){echo("selected");}?>>B</option>
			<option value="C" <?php if($_SESSION["tournamentDivision"] == 'C'){echo("selected");}?>>C</option>
			</select>
		</td>

	</tr>
	<tr>
		<td><label for="tournamentLocation">Location:<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="tournamentLocation" id="tournamentLocation" size="50" value=<?php echo '"'.$_SESSION["tournamentLocation"].'"' ?>></td>

		<td><label for="tournamentDate">Date:<span class="red">*</span></label></td>
		<td>
		<div class="controls">
		<div class="input-group">
			<input type="text" class="date-picker form-control" size="20" name="tournamentDate" id="tournamentDate" onchange="javascript: loadLinkedTournaments();" readonly="true" value=<?php echo '"'.$_SESSION["tournamentDate"].'"' ?>>
			<label for="tournamentDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
		</div>
		</div>
					<script type="text/javascript">
				$(".date-picker").datepicker({
					changeMonth: true,
					changeYear: true
				});
			</script>
		</td>
	</tr>
	<tr>
		<td><label for="numberEvents">Number of Events:<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="numberEvents" id="numberEvents" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["numberEvents"].'"' ?>></td>
		<td><label for="numberTeams">Number of Teams:<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="numberTeams" id="numberTeams" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["numberTeams"].'"' ?>></td>
	</tr>
	<tr>
		<td><label for="highestScore">Last/First Place Point (Primary):<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="highestScore" id="highestScore" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["highestScore"].'"' ?>></td>
		<td><label for="highestScore">Total Points:<span class="red">*</span></label></td>
		<td><input type="radio" name="totalPointsWins" id="totalPointsWins0" value="0" <?php if($_SESSION["totalPointsWins"] == '0'){echo("checked");}?>>
			<label for="totalPointsWins0">Low Score Wins</label> &nbsp;&nbsp;
			<input type="radio" name="totalPointsWins" id="totalPointsWins1" value="1" <?php if($_SESSION["totalPointsWins"] == '1'){echo("checked");}?>>
			<label for="totalPointsWins1">High Score Wins</label></td>
	</tr>
	<tr>
	<td><label for="highestScoreAlt">Last/First Place Point (Alternate):<span class="red">*</span></label></td>
	<td><input type="text" class="form-control" name="highestScoreAlt" id="highestScoreAlt" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
		value=<?php echo '"'.$_SESSION["highestScoreAlt"].'"' ?>></td>
	<td>
	<label for="lockScoresFlag">Lock Scores: </label></td><td><input type="checkbox" id="lockScoresFlag" name="lockScoresFlag" <?php if ($_SESSION["lockScoresFlag"] == '1') echo 'checked'; ?> value="1" <?php if ($userRole != 'ADMIN') echo 'disabled'; ?>>
	</td>
	</tr>
	<tr>
	<td><label for="pointsForNP">Points For NP:<span class="red">*</span></label></td>
	<td><input type="text" class="form-control" name="pointsForNP" id="pointsForNP" onkeydown="limitNumberNegatives(this);" onkeyup="limitNumberNegatives(this);"
		value=<?php echo '"'.$_SESSION["pointsForNP"].'"' ?>></td>
	<td><label for="pointsForDQ">Points For DQ:<span class="red">*</span></label></td>
	<td><input type="text" class="form-control" name="pointsForDQ" id="pointsForDQ" onkeydown="limitNumberNegatives(this);" onkeyup="limitNumberNegatives(this);"
		value=<?php echo '"'.$_SESSION["pointsForDQ"].'"' ?>></td>
	<td>
	</tr>
	<tr>
		<td colspan="4"><label for="tournamentDescription">Description: </label></td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea class="form-control"  name="tournamentDescription" id="tournamentDescription" spellcheck="true" rows="5" cols="100"><?php echo $_SESSION["tournamentDescription"];?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="4"><label for="tournamentDescription">Slideshow / Awards Settings: </label></td>
	</tr>
	<tr>
		<td><label for="eventsAwarded">Event Positions Awarded:<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="eventsAwarded" id="eventsAwarded" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["eventsAwarded"].'"' ?>></td>
		<td><label for="overallAwarded">Overall Positions Awarded:<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="overallAwarded" id="overallAwarded" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["overallAwarded"].'"' ?>></td>
	</tr>
	<tr>
		<td><label for="eventsAwarded">Event Positions Awarded (Alternate):<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="eventsAAwarded" id="eventsAAwarded" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["eventsAAwarded"].'"' ?>></td>
		<td><label for="overallAwarded">Overall Positions Awarded (Alternate):<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="overallAAwarded" id="overallAAwarded" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["overallAAwarded"].'"' ?>></td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" id="bestNewTeamFlag" name="bestNewTeamFlag" <?php if ($_SESSION["bestNewTeam"] == '1') echo 'checked'; ?> value="1"> &nbsp;&nbsp;<label for="bestNewTeamFlag">Display Best New Team</label></td>
		
		<td><label for="tourn1Linked">Link Tournament: </label></td>
		<td>
			<div id="tourn1LinkedDiv" style="width: 100%;">
			<select class="form-control" name="tourn1Linked" id="tourn1Linked">
			<option value=""></option>
			<?php
			    if ($linkedTournaments1) {
             		while($linkedTourn1Row = mysql_fetch_array($linkedTournaments1)) {
             			echo '<option value="'.$linkedTourn1Row['0'].'" '; if($_SESSION["tourn1Linked"] == $linkedTourn1Row['0']){echo("selected");} echo '>'.$linkedTourn1Row['1'].'</option>';
             			
             		}
             	}
        	?>
			</select>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" id="improvedTeam" name="improvedTeam" <?php if ($_SESSION["improvedTeam"] == '1') echo 'checked'; ?> value="1">  &nbsp;&nbsp;<label for="improvedTeam">Display Most Improved Team</label></td>
		<td><label for="tourn2Linked">Link Tournament: </label></td>
			<td><div id="tourn2LinkedDiv" style="width: 100%;">
			<select class="form-control" name="tourn2Linked" id="tourn2Linked">
			<option value=""></option>
			<?php
			    if ($linkedTournaments2) {
             		while($linkedTourn2Row = mysql_fetch_array($linkedTournaments1)) {
             			echo '<option value="'.$linkedTourn2Row['0'].'" '; if($_SESSION["tourn2Linked"] == $linkedTourn2Row['0']){echo("selected");} echo '>'.$linkedTourn2Row['1'].'</option>';
             			
             		}
             	}
        	?>
			</select>
			</div>
		</td>
	</tr>
	</table>
	<hr>
	
      <h2>Events</h2>
        <table class="table table-hover" id="eventTable">
        <thead>
            <tr>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Event Name</th>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Supervisor</th>
                <th width="30%" data-field="trial" data-align="center" data-sortable="true">Trial Event?</th>
				<th width="10%" data-field="actions" data-align="center" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody id="eventTableBody">
        <?php
			$eventList = $_SESSION["eventList"];
			$eventCount = 0;
			if ($eventList) {
				foreach ($eventList as $event) {
					echo '<tr>';
      				echo '<td>'; echo $event['1']; echo '</td>';
      				echo '<td>';
      				echo '<select  class="form-control" name="eventSupervisor'.$eventCount.'" id="eventSupervisor'.$eventCount.'">';
      				echo '<option value=""></option>';
      				if ($supervisors) {
             			while($supervisorRow = mysql_fetch_array($supervisors)) {
             				echo '<option value="'.$supervisorRow['0'].'"'; if($supervisorRow['0'] == $event['5']){echo("selected");} echo '>'.$supervisorRow['1'].'</option>';	
             			}
             		}    
             		mysql_data_seek($supervisors, 0);				
      				echo '</select>'; 
      				echo '</td>';
					echo '<td><div class="col-xs-5 col-md-5">'; 
					echo '<select  class="form-control" name="trialEvent'.$eventCount.'" id="trialEvent'.$eventCount.'">';
					echo '<option value="0"'; if($event['2'] == '' or $event['2'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($event['2'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</div></td>';
					echo '<td><button type="button" class="btn btn-xs btn-danger" name="deleteEvent" onclick="validateDeleteEvent(this)" value='.$event['3'].'>Delete</button></td>';
					echo '</tr>';
					
					$eventCount++;
				}
			}        
        ?>
        </tbody>
        </table>
	<div class="input-group">
	<span class="input-group-btn">
	<button type="button" class="btn btn-xs btn-primary" onclick="addTournEvent()" name="addEvent">Add Event</button>
	</span>
	<div class="col-xs-4 col-md-4">
		<select class="form-control" name="eventAdded" id="eventAdded">
			<option value=""></option>
			<?php
			    if ($events) {
             		while($eventRow = mysql_fetch_array($events)) {
             			echo '<option value="'.$eventRow['0'].'">'.$eventRow['1'].'</option>';
             			
             		}
             	}
        	?>
		</select>
		</div>
		</div>
	<?php if ($_SESSION["tournamentId"] != null AND $_SESSION["tournamentId"] != '') { 
		//echo $_SESSION["insertuser"];
	?>
	<br />
	<button type="submit" class="btn btn-xs btn-primary" onclick="return generateUserLogins();" name="generateSupervisorLogins">Generate Supervisor Logins</button>
	<?php } ?>
	<hr>
	
	    <h2>Teams</h2>
        <table class="table table-hover" id="teamTable">
        <thead>
            <tr>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Team Name</th>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Team Number</th>
                <th width="30%" data-field="alternate" data-align="center" data-sortable="true">Alternate Team?</th>
				<th width="2.5%" data-field="alternate" data-align="center" data-sortable="true">Best New Team</th>
				<th width="2.5%" data-field="alternate" data-align="center" data-sortable="true">Most Improved Team</th>
				<th width="5%" data-field="actions" data-align="center" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody id="teamTableBody">
         <?php
			$teamList = $_SESSION["teamList"];
			$teamCount = 0;
			if ($teamList) {
				foreach ($teamList as $team) {
					echo '<tr>';
      				echo '<td>'; echo $team['1']; echo '</td>';
      				echo '<td><div class="col-xs-5 col-md-5">';
      				echo '<input type="text"  class="form-control" size="10" 
      						name="teamNumber'.$teamCount.'" id="teamNumber'.$teamCount.'" autocomplete="off" value="'.$team['2'].'">';
      				echo '</div></td>';
					echo '<td><div class="col-xs-5 col-md-5">'; 
					echo '<select   class="form-control" name="alternateTeam'.$teamCount.'" id="alternateTeam'.$teamCount.'" >';
					echo '<option value="0"'; if($team['3'] == '' or $$team['3'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($team['3'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</div></td>';
					echo '<td><input type="radio" name="bestNewTeam" id="bestNewTeam'.$teamCount.'" value="'.$team['1'].'" '; if($team['5'] == $team['1']){echo("checked");} echo '></td>';
					echo '<td><input type="radio" name="mostImprovedTeam" id="mostImprovedTeam'.$teamCount.'" value="'.$team['1'].'" '; if($team['6'] == $team['1']){echo("checked");} echo '></td>';
					echo '<td><button type="button" class="btn btn-xs btn-danger" name="deleteTeam" onclick="validateDeleteTeam(this)" value='.$team['4'].'>Delete</button></td>';
					echo '</tr>';
					
					$teamCount++;
				}
			}        
        ?>  
        </tbody>
        </table>

	<div class="input-group">
	<span class="input-group-btn">
	<button type="button" class="btn btn-xs btn-primary" onclick="addTournTeam()" name="addTeam">Add Team</button>
	</span>
	<div class="col-xs-4 col-md-4" id="teamsSelectDiv">
		<select class="form-control" name="teamAdded" id="teamAdded">
			<option value=""></option>
			<?php
			    if ($teams) {
             		while($teamRow = mysql_fetch_array($teams)) {
             			echo '<option value="'.$teamRow['0'].'">'.$teamRow['1'].'</option>';
             			
             		}
             	}
        	?>
		</select>
	</div>
	</div>
	<hr>
	
	<h2>Verifiers</h2>
        <table class="table table-hover" id="verifierTable">
        <thead>
            <tr>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Verifier Name</th>
                <th width="60%" data-field="name" data-align="right" data-sortable="true">Verifier Email</th>
				<th width="10%" data-field="actions" data-align="center" data-sortable="true">Actions</th>
            </tr>
        </thead>
        <tbody id="verifierTableBody">
         <?php
			$verifierList = $_SESSION["verifierList"];
			$verifierCount = 0;
			if ($verifierList) {
				foreach ($verifierList as $verifier) {
					echo '<tr>';
      				echo '<td>'; echo $verifier['1']; echo '</td>';
					echo '<td>'; echo $verifier['2']; echo '</td>';
					echo '<td>';
					if (getCurrentRole() != 'VERIFIER') {
						echo '<button type="button" class="btn btn-xs btn-danger" name="deleteVerifier" onclick="validateDeleteVerifier(this)" value='.$verifier['3'].'>Delete</button>';
					}
					echo '</td>';
					echo '</tr>';
					
					$verifierCount++;
				}
			}        
        ?>  
        </tbody>
        </table>
	<?php if (getCurrentRole() != 'VERIFIER') { ?>
	<div class="input-group">
	<span class="input-group-btn">
	<button type="button" class="btn btn-xs btn-primary" onclick="addTournVerifier()" name="addVerifier">Add Verifier</button>
	</span>
	<div class="col-xs-4 col-md-4" id="verifierSelectDiv">
		<select class="form-control" name="verifierAdded" id="verifierAdded">
			<option value=""></option>
			<?php
			    if ($verifiers) {
             		while($verifierRow = mysql_fetch_array($verifiers)) {
             			echo '<option value="'.$verifierRow['0'].'">'.$verifierRow['1'].'</option>';
             			
             		}
             	}
        	?>
		</select>
	</div>
	</div>
	<?php } ?>
	<hr>

     <button type="submit" class="btn btn-xs btn-danger" name="saveTournament" onclick="return validate();" value=<?php echo '"'.$tournamentRow['0'].'"' ?>>Save</button>
 	 <button type="submit" class="btn btn-xs btn-primary" name="cancelTournament">Cancel</button>
      <hr>
	<?php 
		include_once 'footer.php'; 
		
		if ($_SESSION["EXPORT_GENERATED_USERS"] != null) {
			include_once 'export_auto_usr_pwds.php';
		}
	
	?>

    </div><!--/.container-->
    
    </form>
      
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
   <script src="js/jquery-1.11.3.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
    //	$('table tr').click(function() {

 	//   alert( this.rowIndex );  // alert the index number of the clicked row.

	//});
    
    </script>
	
	<?php 
		if ($_SESSION['savesuccessTournament'] != null and $_SESSION['savesuccessTournament'] == '1') { ?>
		<script type="text/javascript">saveMessage('Tournament');</script>
   	<?php $_SESSION['savesuccessTournament'] = null; } ?> 	
	
  </body>
</html>