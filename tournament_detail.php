<?php session_start(); 
	include_once('score_center_objects.php');
	include_once('logon_check.php');

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
	
	function limitNumber(element) {
		var max = 1000;
		if (isNaN(element.value)) element.value = '';
		if (element.value > max || element.value < 1) element.value = '';
	}
	
	function validate() {
		clearError();
		clearSuccess();
		var error = false;
		var fields = ["tournamentName", "tournamentDivision", "tournamentLocation","tournamentDate","numberEvents","numberTeams","highestScore"];
		var str;
		for (str in fields) {
			if (document.getElementById(fields[str]).value.length === 0 || !document.getElementById(fields[str]).value.trim()) {
				error = true;
			}
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
		else {
			clearError();
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
					if (xmlhttp.responseText == 'error') {
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
					if (xmlhttp.responseText == 'error') {
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
	
	
	function addTournEvent() {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			clearError();
			clearSuccess();
			if (xmlhttp.responseText == 'error1' || xmlhttp.responseText == 'error2') {
				//error message
				if (xmlhttp.responseText == 'error1') displayError("<strong>Cannot Add Event:</strong> Event already added or no event selected.");
				else if (xmlhttp.responseText == 'error2') displayError("<strong>Cannot Add Event:</strong> Cannot add more than "+document.getElementById('numberEvents').value+" events.");				
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
			if (xmlhttp.responseText == 'error1' || xmlhttp.responseText == 'error2') {
				//error message 
				if (xmlhttp.responseText == 'error1') displayError("<strong>Cannot Add Team:</strong> Team already added or no team selected.");
				else if (xmlhttp.responseText == 'error2') displayError("<strong>Cannot Add Team:</strong> Cannot add more than "+document.getElementById('numberTeams').value+" teams.");
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
			count++;
		}
		return str;
	}
	
	function getNumberEventsTeams() {
		return "&numberEvents="+document.getElementById('numberEvents').value+"&numberTeams="+document.getElementById('numberTeams').value;
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
    		
    		$events = mysql_query("SELECT DISTINCT * FROM EVENT ORDER BY NAME ASC");
    		$teams = mysql_query("SELECT DISTINCT * FROM TEAM ORDER BY NAME ASC");
    		$supervisors = mysql_query("SELECT DISTINCT USER_ID, CONCAT(LAST_NAME,', ',FIRST_NAME,' (', USERNAME,')') AS USER
    									 FROM USER WHERE ROLE_CODE='SUPERVISOR' ORDER BY UPPER(LAST_NAME) ASC");
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
		<td width="35%"><select class="form-control" name="tournamentDivision" id="tournamentDivision" >
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
			<input type="text" class="date-picker form-control" size="20" name="tournamentDate" id="tournamentDate" readonly="true" value=<?php echo '"'.$_SESSION["tournamentDate"].'"' ?>>
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
		<td><label for="highestScore">Highest Event Score:<span class="red">*</span></label></td>
		<td><input type="text" class="form-control" name="highestScore" id="highestScore" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);"
			value=<?php echo '"'.$_SESSION["highestScore"].'"' ?>></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><label for="tournamentDescription">Description: </label></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea class="form-control"  name="tournamentDescription" id="tournamentDescription" spellcheck="true" rows="5" cols="100"><?php echo $_SESSION["tournamentDescription"];?></textarea>
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
	<hr>
	
	    <h2>Teams</h2>
        <table class="table table-hover" id="teamTable">
        <thead>
            <tr>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Team Name</th>
                <th width="30%" data-field="name" data-align="right" data-sortable="true">Team Number</th>
                <th width="30%" data-field="alternate" data-align="center" data-sortable="true">Alternate Team?</th>
				<th width="10%" data-field="actions" data-align="center" data-sortable="true">Actions</th>
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
      				echo '<input type="text"  class="form-control" size="10" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);" 
      						min="0" max="100" step="1" 
      						name="teamNumber'.$teamCount.'" id="teamNumber'.$teamCount.'" value="'.$team['2'].'">';
      				echo '</div></td>';
					echo '<td><div class="col-xs-5 col-md-5">'; 
					echo '<select   class="form-control" name="alternateTeam'.$teamCount.'" id="alternateTeam'.$teamCount.'" >';
					echo '<option value="0"'; if($team['3'] == '' or $$team['3'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($team['3'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</div></td>';
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
	<div class="col-xs-4 col-md-4">
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

     <button type="submit" class="btn btn-xs btn-danger" name="saveTournament" onclick="return validate();" value=<?php echo '"'.$tournamentRow['0'].'"' ?>>Save</button>
 	 <button type="submit" class="btn btn-xs btn-primary" name="cancelTournament">Cancel</button>
      <hr>
	<?php include_once 'footer.php'; ?>

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
	
  </body>
</html>