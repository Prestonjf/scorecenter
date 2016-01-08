<?php
session_start();
include_once('score_center_objects.php');
include_once('mail_functions.php');
include_once('role_check.php');
include_once('libs/score_center_global_settings.php');
require_once('libs/PHPExcel.php');


// DB Connection --------------
require_once('login.php');
	$mysqli = mysqli_init();
	mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
	mysqli_real_connect($mysqli, $db_hostname,$db_username,$db_password,$db_database);
	
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
// if user not logged in. forward to login.php

// Begin MAIN METHOD -------------------------->	
if (isset($_POST['login'])) {		
	if (login($mysqli)) {
		loadDefaultSettings();
		header("Location: index.php");
		exit();
	} else {
		header("Location: logon.php");
		exit();
	}
}	
else if (isset($_GET['logout']) or ($_GET['command'] != null and $_GET['command'] == 'logout')) {	
	session_destroy();
	header("Location: logon.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'createAccount') {
	clearAccount();
	$_SESSION["accountMode"] = 'create';
	header("Location: account.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'createNewAccount') {
	if (manageAccount($mysqli,'create')) {
		login($mysqli);
		header("Location: index.php");
		exit();
	}	
	header("Location: account.php");	
	exit();
}
else if (isset($_POST['cancelAccount'])) {
		$_SESSION["accountMode"] = null;
		header("Location: logon.php");
		exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'resetPassword') {
	forgotPassword($mysqli);
	header("Location: logon.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'passwordResetProcess') {
	if (checkPasswordReset($mysqli)) {
		header("Location: account.php");	
		exit();
	}
	header("Location: logon.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'updateExistingAccount') {
	if (manageAccount($mysqli,'update')) {
		login($mysqli);
		header("Location: index.php");
		exit();
	}
	header("Location: account.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'updateResultPage') {
	$_SESSION["resultsPage"] = $_GET['page'];
	exit();
}

// All Commands Below Require An Active Session
// Session Timeout 30 Minutes
if ($_SESSION['sessionTimeout'] + 30 * 60 < time()) {
	session_destroy();
	session_start();
	if ($_GET['command'] != 'loadIndexLogin')
		$_SESSION['errorSessionTimeout'] = '1';
	header("Location: logon.php");	
	exit();
}
else {
	$_SESSION['sessionTimeout'] = time();
}

if ($_GET['command'] != null and ($_GET['command'] == 'loadIndex' or $_GET['command'] =='loadIndexLogin')) {
	header("Location: index.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'updateAccount') {
    clearAccount();
	loadAccount();
	$_SESSION["accountMode"] = 'update';
	header("Location: account.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'loadUtilities') {
	// Load Utilities
	clearUtilities();
	loadUtilities($mysqli);
	header("Location: utilities.php");	
	exit();
}
else if (isset($_GET['saveUtilities'])) {
	saveUtilities($mysqli);
	clearUtilities();
	header("Location: index.php");	
	exit();
}
else if (isset($_GET['cancelUtilities'])) {
	clearUtilities();
	header("Location: index.php");	
	exit();	
}
else if (isset($_GET['searchUserEvent'])) {
		$_SESSION["userEventDate"] = $_GET['userEventDate'];
		$_SESSION["userTournament"] = $_GET['userTournament'];
		header("Location: index.php");
		exit();
}

else if (isset($_GET['addTournament'])) {
	clearTournament();
	header("Location: tournament_detail.php");	
	exit();
}
else if (isset($_GET['deleteTournament'])) {	
	$_SESSION["tournamentId"] = $_GET['deleteTournament'];
	deleteTournament($_GET['deleteTournament'], $mysqli);
	loadAllTournaments($mysqli);
	header("Location: tournament.php");
	exit();
}
else if (isset($_GET['addNewEvent'])) {	
	clearEvent();
	header("Location: event_detail.php");
	exit();
}
else if (isset($_GET['editEvent'])) {
	clearEvent();	
	loadEvent($_GET['editEvent'], $mysqli);
	header("Location: event_detail.php");
	exit();
}
else if (isset($_GET['deleteEvent'])) {
	if(deleteEvent($_GET['deleteEvent'], $mysqli)) 
		loadAllEvents($mysqli);
	header("Location: event.php");
	exit();
}
else if (isset($_GET['saveEvent'])) {
	if (isEventCreated($mysqli)) {
		header("Location: event_detail.php");
		exit();
	}
	saveEvent($mysqli);	
	loadAllEvents($mysqli);
	header("Location: event.php");
	exit();
}

else if (isset($_GET['cancelEvent'])) {	
	header("Location: event.php");
	exit();
}
else if (isset($_GET['saveUser'])) {
	saveUser($mysqli);	
	loadAllUsers($mysqli);
	header("Location: user.php");
	exit();
}

else if (isset($_GET['cancelUser'])) {	
	header("Location: user.php");
	exit();
}
else if (isset($_GET['addNewTeam'])) {	
	clearTeam();
	header("Location: team_detail.php");
	exit();
}
else if (isset($_GET['editTeam'])) {
	clearTeam();	
	loadTeam($_GET['editTeam'], $mysqli);
	header("Location: team_detail.php");
	exit();
}
else if (isset($_GET['deleteTeam'])) {
	if(deleteTeam($_GET['deleteTeam'], $mysqli))
		loadAllTeams($mysqli);
	header("Location: team.php");
	exit();
}
else if (isset($_GET['saveTeam'])) {
	if (isTeamCreated($mysqli)) {
		header("Location: team_detail.php");
		exit();
	}
	saveTeam($mysqli);	
	loadAllTeams($mysqli);
	header("Location: team.php");
	exit();
}

else if (isset($_GET['cancelTeam'])) {	
	clearTeam();
	header("Location: team.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'validateDeleteTeam') {
	cacheTournamnent();
	deleteTournamentTeam($mysqli, $_GET['TournTeamRowId']);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'validateDeleteEvent') {
	cacheTournamnent();
	deleteTournamentEvent($mysqli, $_GET['TournEventRowId']);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'validateDeleteVerifier') {
	cacheTournamnent();
	deleteTournamentVerifier($mysqli, $_GET['verifierTournId']);
	exit();
}
else if (isset($_GET['searchTournament']) or ($_GET['command'] != null and $_GET['command'] == 'loadAllTournaments')) {
	if (isset($_GET['searchTournament'])) {
		$_SESSION["fromTournamentDate"] = $_GET['fromDate'];
		$_SESSION["toTournamentDate"] = $_GET['toDate'];
		$_SESSION["tournamentsNumber"] = $_GET['tournamentsNumber'];
	}
	if ($_SESSION["tournamentsNumber"] == null or $_SESSION["tournamentsNumber"] == '') $_SESSION["tournamentsNumber"] = "20";
	loadAllTournaments($mysqli);
	
	header("Location: tournament.php");
	exit();
}
else if (isset($_GET['searchEvent']) or ($_GET['command'] != null and $_GET['command'] == 'loadAllEvents')) {
	if (isset($_GET['searchEvent'])) {
		$_SESSION["eventFilterNumber"] = $_GET['eventsNumber'];
		$_SESSION["eventFilterName"] = $_GET['eventName'];
	}
	loadAllEvents($mysqli);
	header("Location: event.php");
	exit();	
}
else if (isset($_GET['searchTeam']) or ($_GET['command'] != null and $_GET['command'] == 'loadAllTeams')) {
	if (isset($_GET['searchTeam'])) {
		$_SESSION["teamFilterNumber"] = $_GET['teamNumber'];
		$_SESSION["teamFilterName"] = $_GET['teamName'];
		$_SESSION["filterDivision"] = $_GET['filterDivision'];
	}
	loadAllTeams($mysqli);
	header("Location: team.php");
	exit();	
}
else if (isset($_GET['searchUsers']) or ($_GET['command'] != null and $_GET['command'] == 'loadAllUsers')) {
	$_SESSION["userFirstName"] = $_GET['userFirstName'];
	$_SESSION["userLastName"] = $_GET['userLastName'];
	$_SESSION["userRole"] = $_GET['userRole'];
	$_SESSION["userFilterNumber"] = $_GET['userFilterNumber'];

	loadAllUsers($mysqli);
	header("Location: user.php");
	exit();	
}
else if (isset($_GET['editUser'])) {
	clearUser();	
	loadUser($_GET['editUser'], $mysqli);
	header("Location: user_detail.php");
	exit();
}

else if (isset($_GET['loadTournament'])) {
	clearTournament();
	$_SESSION["tournamentId"] = $_GET['loadTournament'];
	loadTournament($_GET['loadTournament'], $mysqli);
	header("Location: tournament_detail.php");
	exit();
}
else if (isset($_GET['enterScores'])) {
	$_SESSION["tournamentId"] = $_GET['enterScores'];
	$_SESSION["tournamentScoresIndexReturn"] = null;
	loadTournamentEvents($mysqli);
	header("Location: tournament_events.php");
	exit();
}
else if (isset($_GET['enterScoresIndex'])) {
	$_SESSION["tournamentId"] = $_GET['enterScoresIndex'];
	$_SESSION["tournamentScoresIndexReturn"] = '1';
	loadTournamentEvents($mysqli);
	header("Location: tournament_events.php");
	exit();
}
else if (isset($_GET['enterEventScores'])) {
	$_SESSION["tournEventId"] = $_GET['enterEventScores'];
	loadEventScores($mysqli);
	header("Location: event_scores.php");
	exit();
}
else if (isset($_GET['cancelEvent'])) {
		$_SESSION["tournEventId"] = null;
		loadTournamentEvents($mysqli);
		header("Location: tournament_events.php");
		exit();
}
else if (isset($_GET['saveEventScores'])) {
		saveEventScores($mysqli);
		$_SESSION["tournEventId"] = null;
		loadTournamentEvents($mysqli);
		header("Location: tournament_events.php");
		exit();
}
else if (isset($_GET['cancelEventScores'])) {	
	header("Location: tournament_events.php");
	exit();
}

else if (isset($_GET['exportResultsCSV'])) {
	exportResultsCSV($mysqli);
	header("Location: tournament_results.php");
	exit();
}
else if (isset($_GET['exportResultsEXCEL'])) {
	exportResultsEXCEL($mysqli);
	header("Location: tournament_results.php");
	exit();
}
else if (isset($_GET['viewSlideShow'])) {
	$id = $_GET['viewSlideShow'];
	loadSlideShow($id, $mysqli);
	header("Location: slideshow.php");
	exit();
}
else if (isset($_GET['exportSlideShowPDF'])) {
	$id = $_GET['exportSlideShowPDF'];
	loadSlideShow($id, $mysqli);
	exportSlideShowPDF($mysqli);
	//header("Location: tournament_results.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'exitSlideShow') {
	clearSlideshow();
	header("Location: tournament_results.php");
	exit();
}

else if ($_GET['command'] != null and $_GET['command'] == 'updatePRowColor') {
	$_SESSION["primaryRowColor"] = $_GET['color'];
	reloadResults();
	//header("Location: tournament_results.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'updatePColColor') {
	$_SESSION["primaryColumnColor"] = $_GET['color'];
	reloadResults();
	//header("Location: tournament_results.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'updateSRowColor') {
	$_SESSION["secondaryRowColor"] = $_GET['color'];
	reloadResults();
	//header("Location: tournament_results.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'updateSColColor') {
	$_SESSION["secondaryColumnColor"] = $_GET['color'];
	reloadResults();
	//header("Location: tournament_results.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'resetResultsColors') { 
	loadDefaultSettings();
	reloadResults();
	//header("Location: tournament_results.php");
	exit();
}
else if (isset($_GET['printScore'])) {
	$_SESSION["tournamentId"] = $_GET['printScore'];
	generateTournamentResults($_GET['printScore'], $mysqli);
	loadTournamentEvents($mysqli);
	header("Location: tournament_results.php");
	exit();
}
else if (isset($_GET['viewStatistics'])) {
	$_SESSION["tournamentId"] = $_GET['viewStatistics'];
	loadTournamentEvents($mysqli);
	header("Location: tournament_events.php");
	exit();
}
else if (isset($_GET['saveTournament'])) {
	cacheTournamnent();
	saveTournament($mysqli);
	header("Location: tournament.php");
	exit();
}

else if (isset($_GET['cancelTournament'])) {
	clearTournament();
	if ($_SESSION["tournamentScoresIndexReturn"] != null && $_SESSION["tournamentScoresIndexReturn"] == '1') {
		$_SESSION["tournamentScoresIndexReturn"] = null;
		header("Location: index.php");
		exit();
	}
	header("Location: tournament.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'loadDivisionTeams') {
	loadDivisionTeams($_GET['division'], $mysqli);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'loadLinkedTournaments') {
	loadLinkedTournaments($_GET['division'],$_GET['date'], $mysqli);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'addEvent') {
	cacheTournamnent();
	addEvent($_GET['eventAdded'], $mysqli);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'addTeam') {
	cacheTournamnent();
	addTeam($_GET['teamAdded'], $mysqli);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'addVerifier') {
	cacheTournamnent();
	addVerifier($_GET['verifierAdded'], $mysqli);
	exit();
}

// No commands were met. Return to Home Page
else {	
	//header("Location: index.php");
	exit();
}

?>
<!-- END MAIN METHOD -->




<!-- FUNCTIONS -->


<?php


// TOURNAMENT DISPLAY SCREEN ---------------------------------------
	function loadAllTournaments($mysqli) {
		$query = "SELECT T.TOURNAMENT_ID, T.NAME, T.LOCATION,T.DIVISION, DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE1', T.SCORES_LOCKED_FLAG FROM TOURNAMENT T ";
		// Verifier Can only see assigned TOURNAMENTS
		if (getCurrentRole() == 'VERIFIER') {
			$query .= " INNER JOIN TOURNAMENT_VERIFIER TV ON TV.TOURNAMENT_ID=T.TOURNAMENT_ID AND TV.USER_ID =" .getCurrentUserId();
		}
		
		$query .= " WHERE 1=1 ";	
		if ($_SESSION["fromTournamentDate"] !=null and $_SESSION["fromTournamentDate"] != '') {
			$date1 = strtotime($_SESSION["fromTournamentDate"]); $date = date('Y-m-d', $date1 );
			$query = $query . " and T.DATE >= '".$date."' ";
		}
		if ($_SESSION["toTournamentDate"] !=null and $_SESSION["toTournamentDate"] != '') {
			$date1 = strtotime($_SESSION["toTournamentDate"]); $date = date('Y-m-d', $date1 );
			$query = $query . " and T.DATE <= '".$date."' ";
		}
		 
		$query = $query . " ORDER BY T.DATE DESC ";
		
		if ($_SESSION["tournamentsNumber"] !=null and $_SESSION["tournamentsNumber"] != '') {
			$query = $query . " LIMIT ".$_SESSION["tournamentsNumber"];
		}
		
		$_SESSION["resultsPage"] = 1;
		$_SESSION["allTournaments"] = $query;
	}
	
	
	function deleteTournament($id, $mysqli) {
		// Delete From TEAM_EVENT_SCORE
		$result = $mysqli->query("DELETE TES.* FROM TEAM_EVENT_SCORE TES 
										INNER JOIN TOURNAMENT_TEAM TT ON TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID 
 										INNER JOIN TOURNAMENT T ON TT.TOURNAMENT_ID=T.TOURNAMENT_ID 
										WHERE T.TOURNAMENT_ID=".$id);
		
		// delete TOURN_EVENT_LINK
		$result = $mysqli->query("DELETE TE.* FROM TOURNAMENT_EVENT TE INNER JOIN TOURNAMENT T ON TE.TOURNAMENT_ID=T.TOURNAMENT_ID WHERE T.TOURNAMENT_ID=".$id);
		
		// delete TOURN_TEAM_LINK
		$result = $mysqli->query("DELETE TT.* FROM TOURNAMENT_TEAM TT INNER JOIN TOURNAMENT T ON TT.TOURNAMENT_ID=T.TOURNAMENT_ID WHERE T.TOURNAMENT_ID=".$id);
		
		// delete TOURNAMENT_VERIFIER
		$result = $mysqli->query("DELETE FROM TOURNAMENT_VERIFIER WHERE TOURNAMENT_ID=".$id);
		
		// DELETE TOURNAMENT
		$mysqli->query("DELETE FROM TOURNAMENT WHERE TOURNAMENT_ID = " .$id); 
		
		$_SESSION["deleteTournamentSuccess"] = '1';	
	}


// MANAGE TOURNAMENTS SCREEN ---------------------------------------
	function cacheTournamnent() {
		if ($_GET['tournamentName'] != null) $_SESSION["tournamentName"] = $_GET['tournamentName'];
		if ($_GET['tournamentDivision'] != null) $_SESSION["tournamentDivision"] = $_GET['tournamentDivision'];
		if ($_GET['tournamentLocation'] != null) $_SESSION["tournamentLocation"] = $_GET['tournamentLocation'];
		if ($_GET['tournamentDate'] != null) $_SESSION["tournamentDate"] = $_GET['tournamentDate'];
		if ($_GET['numberEvents'] != null) $_SESSION["numberEvents"] = $_GET['numberEvents'];
		if ($_GET['numberTeams'] != null) $_SESSION["numberTeams"] = $_GET['numberTeams'];
		if ($_GET['highestScore'] != null) $_SESSION["highestScore"] = $_GET['highestScore'];
		if ($_GET['tournamentDescription'] != null) $_SESSION["tournamentDescription"] = $_GET['tournamentDescription'];
		if ($_GET['totalPointsWins'] != null) $_SESSION["totalPointsWins"] = $_GET['totalPointsWins'];
		if ($_GET['lockScoresFlag'] != null) $_SESSION["lockScoresFlag"] = $_GET['lockScoresFlag']; else $_SESSION["lockScoresFlag"] = null;
		
		if ($_GET['eventsAwarded'] != null) $_SESSION["eventsAwarded"] = $_GET['eventsAwarded'];
		if ($_GET['overallAwarded'] != null) $_SESSION["overallAwarded"] = $_GET['overallAwarded'];
		if ($_GET['bestNewTeamFlag'] != null) $_SESSION["bestNewTeam"] = $_GET['bestNewTeamFlag']; else $_SESSION["bestNewTeam"] = null;
		if ($_GET['improvedTeam'] != null) $_SESSION["improvedTeam"] = $_GET['improvedTeam']; else $_SESSION["improvedTeam"] = null;
		
		$_SESSION["tourn1Linked"] = $_GET['tourn1Linked'];
		$_SESSION["tourn2Linked"] = $_GET['tourn2Linked'];
		
		
		
		// Team Cache - teamNumber, alternateTeam, bestNewTeam,mostImprovedTeam
		$count = 0;
		$teamList = $_SESSION["teamList"];
		while ($count < 100) {
			$team = $teamList[$count];
			if ($_GET['teamNumber'.$count] != null or $_GET['alternateTeam'.$count] != null) {	
				if ($_GET['teamNumber'.$count] != null) {			
					$team[2] = $_GET['teamNumber'.$count];	
				}
				if ($_GET['alternateTeam'.$count] != null) {			
					$team[3] = $_GET['alternateTeam'.$count];	
				}
				if ($_GET['bestNewTeam'] != null AND $_GET['bestNewTeam'] == $team[1]) $team[5] = $team[1]; else $team[5] = '';
				if ($_GET['mostImprovedTeam'] != null AND $_GET['mostImprovedTeam'] == $team[1]) $team[6] = $team[1]; else $team[6] = '';

				$teamList[$count] = $team;
				$_SESSION["teamList"] = $teamList;	
				$count++;		
			} 
			else {
				break;
			}			
		}
		
		// Events Cache - trialEvent
		$count = 0;
		$eventList = $_SESSION["eventList"];
		
		while ($count < 100) {
			$event = $eventList[$count];
			if ($_GET['trialEvent'.$count] != null) {	
				
				$event['2'] = $_GET['trialEvent'.$count];	
				$event['5'] = $_GET['eventSupervisor'.$count];	
				
				$eventList[$count] = $event;
				$_SESSION["eventList"] = $eventList;							
			} 
			else {
				break;
			}
			$count++;			
		}
		
		// Event Cache - Supervisor
		

	}
	
	function clearTournament() {
		$_SESSION["tournamentName"] = null;
		$_SESSION["tournamentLocation"] = null;
		$_SESSION["tournamentDivision"] = null;
		$_SESSION["tournamentDate"] = null;
		$_SESSION["numberEvents"] = null;
		$_SESSION["numberTeams"] = null;
		$_SESSION["highestScore"] = null;
		$_SESSION["lockScoresFlag"] = null;
		$_SESSION["tournamentDescription"] = null;
		$_SESSION["tournamentId"] = null;
		$_SESSION["eventList"] = null;
		$_SESSION["teamList"] = null;
		$_SESSION["verifierList"] = null;		
		$_SESSION["eventsAwarded"] = null;
		$_SESSION["overallAwarded"] = null;
		$_SESSION["bestNewTeam"] = null;
		$_SESSION["improvedTeam"] = null;
		$_SESSION["tourn1Linked"] = null;
		$_SESSION["tourn2Linked"] = null;
		
	}
	
	function addTeam($selectedTeam,$mysqli) {
		// Validation: cannot add existing Team or blank	
			$teamList = null;
			if ($_SESSION["teamList"] == null) $teamList = array();
			else $teamList = $_SESSION["teamList"];
		
			$error = FALSE;
			$errorStr = "";			
			$count = 0;
			
			if ($selectedTeam == '') { $error = TRUE; $errorStr = 'error1';}	
			if ($teamList) {
				foreach ($teamList as $team) { 
					$count++;
					if ($selectedTeam == $team['0']) { $error = TRUE; $errorStr = 'error1';}	
				}
			}

			if ($_GET['numberTeams'] != null and $_GET['numberTeams'] != '' and $_GET['numberTeams'] < $count+1) {
				$error = TRUE; 
				$errorStr = 'error2';
			}
			
			
		
			if (!$error) {
				//echo $_SERVER['REQUEST_URI'];
				// Load Event Name
				$result = $mysqli->query("SELECT NAME FROM TEAM WHERE TEAM_ID = ".$selectedTeam); 
				$row1 = $result->fetch_row();
	
				$team = array($selectedTeam, $row1['0'], "", "","", "","","1"); // 0: TEAM_ID 1: NAME 2:TEAM_NUMBER 3: ALTERNATE 4: TOURN_TEAM_ID 5: NEW TEAM 0/1
				array_push($teamList, $team);
				$_SESSION["teamList"] = $teamList;
				reloadTournamentTeam();
			}
			else {
				echo $errorStr;
			}
	}
	
		function addVerifier($selectedVerifier,$mysqli) {
		// Validation: cannot add existing Team or blank	
			$verifierList = null;
			if ($_SESSION["verifierList"] == null) $verifierList = array();
			else $verifierList = $_SESSION["verifierList"];
		
			$error = FALSE;
			$errorStr = "";			
			$count = 0;
			
			if ($selectedVerifier == '') { $error = TRUE; $errorStr = 'error1';}	
			if ($verifierList) {
				foreach ($verifierList as $verifier) { 
					$count++;
					if ($selectedVerifier == $verifier['0']) { $error = TRUE; $errorStr = 'error1';}	
				}
			}
			if (!$error) {
				//echo $_SERVER['REQUEST_URI'];
				// Load Event Name
				$result = $mysqli->query("SELECT CONCAT(LAST_NAME,', ',FIRST_NAME) AS USER, USERNAME FROM USER WHERE USER_ID = ".$selectedVerifier); 
				$row1 = $result->fetch_row();
	
				$verifier = array($selectedVerifier, $row1['0'], $row1['1'],""); // 0: USER_ID 1: NAME 2: USERNAME 3: TOURN_VERIFIER_ID
				array_push($verifierList, $verifier);
				$_SESSION["verifierList"] = $verifierList;
				reloadTournamentVerifier();
			}
			else {
				echo $errorStr;
			}
	}
	
	function addEvent($selectedEvent, $mysqli) {
		// Validation: cannot add existing event or blank
			$eventList = null;
			if ($_SESSION["eventList"] == null) $eventList = array();
			else $eventList = $_SESSION["eventList"];
		
			$error = FALSE;
			$errorStr = "";			
			$count = 0;
		
			if ($selectedEvent == '') { $error = TRUE; $errorStr = 'error1';}
			if ($eventList) {
				foreach ($eventList as $event) { 
					$count++;
					if ($selectedEvent == $event['0']) { $error = TRUE; $errorStr = 'error1';}						
				}
			}
			
			if ($_GET['numberEvents'] != null and $_GET['numberEvents'] != '' and $_GET['numberEvents'] < $count+1) {
				$error = TRUE; 
				$errorStr = 'error2';
			}
	
			if (!$error) {
				// Load Event Name
				$result = $mysqli->query("SELECT NAME FROM EVENT WHERE EVENT_ID = ".$selectedEvent); 
				$row1 = $result->fetch_row();
	
				$event = array($selectedEvent, $row1['0'], "","","1", "", ""); // 0: EVENT_ID 1: NAME 2:TRIAL_EVENT 3: TOURN_EVENT_ID 4: New Event 0/1 5: USER_ID 6: USER NAME
				array_push($eventList, $event);
				$_SESSION["eventList"] = $eventList;
				reloadTournamentEvent($mysqli);
			} else {
				echo $errorStr;
			}
	}
	
	function deleteTournamentTeam($mysqli, $row) {
		// Remove From Cache
		$teamList = $_SESSION["teamList"];
		$count = 0;
		if ($teamList) {
			foreach ($teamList as $key => $team) { 
				if ($row == $count) {				
					$result = $mysqli->query("SELECT TES.SCORE FROM TEAM_EVENT_SCORE TES WHERE TES.TOURN_TEAM_ID = " .$team[4]);
					if ($result) {
						$row = $result->fetch_row();
						if ($row['0'] != null and $row['0'] != '') echo 'error';
						else {
							// delete tourn team
							$result = $mysqli->query("DELETE FROM TOURNAMENT_TEAM WHERE TOURN_TEAM_ID = " .$team[4]);
							unset($teamList[$key]);
							$teamList = array_values($teamList);
							$_SESSION["teamList"] = $teamList;
							reloadTournamentTeam();
						}
					}
					else {
						unset($teamList[$key]);
						$teamList = array_values($teamList);
						$_SESSION["teamList"] = $teamList;
						reloadTournamentTeam();
					}	
					break;
				}
				$count++;
			}
		}	
	}
	
	function deleteTournamentEvent($mysqli, $row) {
		// Remove From Cache
		$eventList = $_SESSION["eventList"];
		$count = 0;
		if ($eventList) {
			foreach ($eventList as $key => $event) { 
				if ($row == $count) {
					$result = $mysqli->query("SELECT TES.SCORE FROM TEAM_EVENT_SCORE TES WHERE TES.TOURN_EVENT_ID = " .$event[3]);
					if ($result) {
						$row = $result->fetch_row();
						if ($row['0'] != null and $row['0'] != '') echo 'error';
						else {
							// delete tourn event
							$result = $mysqli->query("DELETE FROM TOURNAMENT_EVENT WHERE TOURN_EVENT_ID = " .$event[3]);
							unset($eventList[$key]);
							$eventList = array_values($eventList);
							$_SESSION["eventList"] = $eventList;
							reloadTournamentEvent($mysqli);
						}
					} 
					else {
						unset($eventList[$key]);
						$eventList = array_values($eventList);
						$_SESSION["eventList"] = $eventList;
						reloadTournamentEvent($mysqli);
					}			
					break;
				}
				$count++;		
			}
		}
	}
	
		function deleteTournamentVerifier($mysqli, $row) {
		// Remove From Cache
		$verifierList = $_SESSION["verifierList"];
		$count = 0;
		if ($verifierList) {
			foreach ($verifierList as $key => $verifier) { 
				if ($row == $count) {
					if ($verifier[3] != null and verifier[3] != "") {
						// delete tourn event
						$result = $mysqli->query("DELETE FROM TOURNAMENT_VERIFIER WHERE TOURN_VERIFIER_ID = " .$verifier[3]);
						unset($verifierList[$key]);
						$verifierList = array_values($verifierList);
						$_SESSION["verifierList"] = $verifierList;
						reloadTournamentVerifier($mysqli);
					} 
					else {
						unset($verifierList[$key]);
						$verifierList = array_values($verifierList);
						$_SESSION["verifierList"] = $verifierList;
						reloadTournamentVerifier($mysqli);
					}			
					break;
				}
				$count++;		
			}
		}
	}
	
	
	
	
	function loadDivisionTeams($division, $mysqli) {
		$query = "SELECT DISTINCT * FROM TEAM ";
		if ($division != null and $division != '') $query .= " WHERE DIVISION = '".$division. "' ";
		$query .= " ORDER BY NAME ASC ";
		$results = $mysqli->query($query);
		echo '<select class="form-control" name="teamAdded" id="teamAdded">';
		echo '<option value=""></option>';
		if ($results) {
        	while($teamRow = $results->fetch_array()) {
            	echo '<option value="'.$teamRow['0'].'">'.$teamRow['1'].'</option>';		
             }
        }
		echo '</select>';
	}
	function loadLinkedTournaments($division, $date, $mysqli) {
		$division1 = null;
		$date1 = strtotime($date); $date = date('Y-m-d', $date1 );
		if (strcmp($division,"A") == 0) $division1 = 'B'; else if (strcmp($division,"B") == 0) $division1 = 'A'; else if (strcmp($division,"C") == 0) $division1 = 'A';

		$linkedTournaments1 = $mysqli->query("SELECT T.TOURNAMENT_ID, CONCAT(T.NAME,' (',T.DIVISION,')') AS NAME FROM TOURNAMENT T WHERE T.DATE='".$date."' AND T.DIVISION='".$division1."' ORDER BY T.NAME ASC ");
		
		echo '<select class="form-control" name="tourn1Linked" id="tourn1Linked"><option value=""></option>';
			    if ($linkedTournaments1) {
             		while($linkedTourn1Row = $linkedTournaments1->fetch_array()) {
             			echo '<option value="'.$linkedTourn1Row['0'].'" '; if($_SESSION["tourn1Linked"] == $linkedTourn1Row['0']){echo("selected");} echo '>'.$linkedTourn1Row['1'].'</option>';
             			
             		}
             	}
		echo '</select>';
		echo '*****';
		
		if (strcmp($division,"A") == 0) $division1 = 'C'; else if (strcmp($division,"B") == 0) $division1 = 'C'; else if (strcmp($division,"C") == 0) $division1 = 'B';
		$linkedTournaments2 = $mysqli->query("SELECT T.TOURNAMENT_ID, CONCAT(T.NAME,' (',T.DIVISION,')') AS NAME FROM TOURNAMENT T WHERE T.DATE='".$date."' AND T.DIVISION='".$division1."' ORDER BY T.NAME ASC ");

		echo '<select class="form-control" name="tourn2Linked" id="tourn2Linked"><option value=""></option>';
				if ($linkedTournaments2) {
             		while($linkedTourn2Row = $linkedTournaments2->fetch_array()) {
             			echo '<option value="'.$linkedTourn2Row['0'].'" '; if($_SESSION["tourn2Linked"] == $linkedTourn2Row['0']){echo("selected");} echo '>'.$linkedTourn2Row['1'].'</option>';
             			
             		}
             	}
		echo '</select>';
	}
	
	function reloadTournamentTeam() {
			$teamList = $_SESSION["teamList"];
			$teamCount = 0;
			if ($teamList) {		
				foreach ($teamList as $team) {
					echo '<tr>';
      				echo '<td>'; echo $team['1']; echo '</td>';
      				echo '<td><div class="col-xs-5 col-md-5">';
      				echo '<input type="number"  class="form-control" size="10" onkeydown="limitNumber(this);" onkeyup="limitNumber(this);" 
      						min="0" max="100" step="1" autocomplete="off"
      						name="teamNumber'.$teamCount.'" id="teamNumber'.$teamCount.'" value="'.$team['2'].'">';
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
	}
		function reloadTournamentVerifier() {
			$verifierList = $_SESSION["verifierList"];
			$verifierCount = 0;
			if ($verifierList) {		
				foreach ($verifierList as $verifier) {
					echo '<tr>';
      				echo '<td>'; echo $verifier['1']; echo '</td>';
					echo '<td>'; echo $verifier['2']; echo '</td>';
					echo '<td><button type="button" class="btn btn-xs btn-danger" name="deleteVerifier" onclick="validateDeleteVerifier(this)" value='.$verifier['0'].'>Delete</button></td>';
					echo '</tr>';
					
					$verifierCount++;
				}
			} 
	}
	
	function reloadTournamentEvent($mysqli) {
	    	$supervisors = $mysqli->query("SELECT DISTINCT USER_ID, CONCAT(LAST_NAME,', ',FIRST_NAME,' (', USERNAME,')') AS USER
    									 FROM USER WHERE ROLE_CODE='SUPERVISOR' ORDER BY UPPER(LAST_NAME) ASC");
    									 
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
             			while($supervisorRow = $supervisors->fetch_array()) {
             				echo '<option value="'.$supervisorRow['0'].'"'; if($supervisorRow['0'] == $event['5']){echo("selected");} echo '>'.$supervisorRow['1'].'</option>';	
             			}
             		}    
             		mysqli_data_seek($supervisors, 0);				
      				echo '</select>'; 
      				echo '</td>';
					echo '<td><div class="col-xs-5 col-md-5">'; 
					echo '<select  class="form-control" name="trialEvent'.$eventCount.'" id="trialEvent'.$eventCount.'">';
					echo '<option value="0"'; if($event['2'] == '' or $event['2'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($event['2'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</div></td>';
					echo '<td><button type="button" class="btn btn-xs btn-danger" name="deleteEvent" onclick="validateDeleteEvent(this)" 
						 value='.$event['3'].'>Delete</button></td>';
					echo '</tr>';
					
					$eventCount++;
				}
			} 
	}
	
	
	
	
		function loadTournament($id, $mysqli) {
	
		 	$tournamentRow = array("","","","","","","","","","","");
 			$result = $mysqli->query("SELECT * FROM TOURNAMENT WHERE TOURNAMENT_ID = " .$id); 
 			if ($result) {
 				$tournamentRow = $result->fetch_row();
 				
 				$_SESSION["tournamentName"] = $tournamentRow['1'];
 				$_SESSION["tournamentDivision"] = $tournamentRow['3'];
 				$_SESSION["tournamentLocation"] = $tournamentRow['2'];
 				$_SESSION["numberEvents"] = $tournamentRow['5'];
 				$_SESSION["numberTeams"] = $tournamentRow['6'];
 				$_SESSION["highestScore"] = $tournamentRow['7'];
 				$_SESSION["tournamentDescription"] = $tournamentRow['8'];
 				$_SESSION["totalPointsWins"] = $tournamentRow['9'];
				$_SESSION["eventsAwarded"] = $tournamentRow['10'];
				$_SESSION["overallAwarded"] = $tournamentRow['11'];
				$_SESSION["bestNewTeam"] = $tournamentRow['12'];
				$_SESSION["improvedTeam"] = $tournamentRow['13'];
				$_SESSION["tourn1Linked"] = $tournamentRow['14'];
				$_SESSION["tourn2Linked"] = $tournamentRow['15'];
				$_SESSION["lockScoresFlag"] = $tournamentRow['16'];
 				
 				$date = strtotime($tournamentRow['4']);
 				$_SESSION["tournamentDate"] = date('m/d/Y', $date);
 				
    		}
	
			// Load Events
			$eventList = array();
			$result = $mysqli->query("SELECT TE.EVENT_ID, E.NAME, TE.TRIAL_EVENT_FLAG, TE.TOURN_EVENT_ID, U.USER_ID, 
									CONCAT(U.LAST_NAME,', ',U.FIRST_NAME,' (', U.USERNAME,')') AS USER 
									FROM TOURNAMENT_EVENT TE 
									INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID 
									INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID 
									LEFT JOIN USER U on U.USER_ID=TE.USER_ID
									WHERE TE.TOURNAMENT_ID= " .$id. " ORDER BY UPPER(E.NAME) ASC "); 
 			if ($result) {
 				while($eventRow = $result->fetch_array()) {
 					$event = array();
 					array_push($event, $eventRow['0']);
 					array_push($event, $eventRow['1']);
 					array_push($event, $eventRow['2']);
 					array_push($event, $eventRow['3']);
 					array_push($event, "0");
 					array_push($event, $eventRow['4']);
 					array_push($event, $eventRow['5']);
				
 					array_push($eventList, $event);
 				}
			}	
			$_SESSION["resultsPage"] = 1;
			$_SESSION["eventList"] = $eventList;
			
			// Load Teams
			$teamList = array();
			$result = $mysqli->query("SELECT TT.TEAM_ID, T.NAME, TT.TEAM_NUMBER, TT.ALTERNATE_FLAG, TT.TOURN_TEAM_ID, TT.BEST_NEW_TEAM_FLAG, TT.MOST_IMPROVED_TEAM_FLAG FROM TOURNAMENT_TEAM TT INNER JOIN TOURNAMENT TR on 		
									TR.TOURNAMENT_ID=TT.TOURNAMENT_ID 
									INNER JOIN TEAM T on T.TEAM_ID=TT.TEAM_ID WHERE TT.TOURNAMENT_ID= " .$id. " ORDER BY TT.TEAM_NUMBER ASC "); 
 			if ($result) {
 				while($teamRow = $result->fetch_array()) {
 					$team = array();
 					array_push($team, $teamRow['0']);
 					array_push($team, $teamRow['1']);
 					array_push($team, $teamRow['2']);
 					array_push($team, $teamRow['3']);
 					array_push($team, $teamRow['4']);
					if ($teamRow['5'] == 1) array_push($team, $teamRow['1']); else array_push($team, '');
					if ($teamRow['6'] == 1) array_push($team, $teamRow['1']); else array_push($team, '');
 					array_push($team, "0");
				
 					array_push($teamList, $team);
 				}
			}			
			$_SESSION["teamList"] = $teamList;
			
			// Load Verifiers
			$verifierList = array();
			$result = $mysqli->query("SELECT TV.USER_ID, TV.TOURN_VERIFIER_ID, U.USERNAME, CONCAT(U.LAST_NAME,', ',U.FIRST_NAME) AS USER 
									FROM TOURNAMENT_VERIFIER TV INNER JOIN TOURNAMENT T on 		
									T.TOURNAMENT_ID=TV.TOURNAMENT_ID 
									INNER JOIN USER U on U.USER_ID=TV.USER_ID WHERE T.TOURNAMENT_ID= " .$id. " ORDER BY UPPER(U.LAST_NAME) ASC "); 
 			if ($result) {
 				while($verifierRow = $result->fetch_array()) {
 					$verifier = array();
 					array_push($verifier, $verifierRow['0']);
 					array_push($verifier, $verifierRow['3']);
 					array_push($verifier, $verifierRow['2']);
 					array_push($verifier, $verifierRow['1']);
				
 					array_push($verifierList, $verifier);
 				}
			}			
			$_SESSION["verifierList"] = $verifierList;
	}
	
	
	
	// SAVE TOURNAMENT
	function saveTournament($mysqli) {

		// Formate
			$date = $_SESSION['tournamentDate'];
			if ($date == '') $date = null;
			else { 
				$date1 = strtotime($date);
				$date = date('Y-m-d H:i:s', $date1 );				
			}
			$name = $_SESSION['tournamentName'];
			$location = $_SESSION["tournamentLocation"];
			$division = $_SESSION['tournamentDivision'];
			$numberEvents = $_SESSION['numberEvents'];
			$numberTeams = $_SESSION['numberTeams'];
			$highestScore = $_SESSION["highestScore"];
			$description = $_SESSION['tournamentDescription'];
			$highLowWins = $_SESSION["totalPointsWins"];
			$lockScoresFlag = $_SESSION["lockScoresFlag"];
			$eventsAwarded = $_SESSION["eventsAwarded"];
			$overallAwarded = $_SESSION["overallAwarded"];
			$bestNewTeam = $_SESSION["bestNewTeam"];
			$improvedTeam = $_SESSION["improvedTeam"];
			$tourn1Linked = $_SESSION["tourn1Linked"]; if ($tourn1Linked == null or $tourn1Linked == '') $tourn1Linked = 'NULL';
			$tourn2Linked = $_SESSION["tourn2Linked"]; if ($tourn2Linked == null or $tourn2Linked == '') $tourn2Linked = 'NULL';
			
		// if Tournament id is null create new
		if ($_SESSION["tournamentId"] == null) { 
			$result = $mysqli->query("select max(TOURNAMENT_ID) + 1 from TOURNAMENT");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null) $id = $row['0'];  
			$_SESSION["tournamentId"] = $id;
			
			$query = $mysqli->prepare("INSERT INTO TOURNAMENT (TOURNAMENT_ID, NAME, LOCATION, DIVISION, DATE, NUMBER_EVENTS, NUMBER_TEAMS, 
			HIGHEST_SCORE_POSSIBLE, DESCRIPTION, HIGH_LOW_WIN_FLAG, EVENTS_AWARDED, OVERALL_AWARDED,BEST_NEW_TEAM_FLAG,MOST_IMPROVED_FLAG,LINKED_TOURN_1,LINKED_TOURN_2,SCORES_LOCKED_FLAG) VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,".$tourn1Linked.",".$tourn2Linked.",?) ");
			
			$query->bind_param('ssssiiisiiiiii',$name,$location, $division,$date, $numberEvents, $numberTeams, $highestScore, $description, $highLowWins, $eventsAwarded, $overallAwarded, $bestNewTeam, $improvedTeam,$lockScoresFlag);
			
			$query->execute();
			$query->free_result();
			//echo $query;
			//$result = mysql_query($query);
		}
		else {
			$query = $mysqli->prepare("UPDATE TOURNAMENT SET NAME=?, LOCATION=?, DIVISION=?, DATE=?, NUMBER_EVENTS=?,NUMBER_TEAMS=?,
			HIGHEST_SCORE_POSSIBLE=?, DESCRIPTION=?, HIGH_LOW_WIN_FLAG=?,EVENTS_AWARDED=?,OVERALL_AWARDED=?,BEST_NEW_TEAM_FLAG=?,MOST_IMPROVED_FLAG=?,LINKED_TOURN_1=".$tourn1Linked.",LINKED_TOURN_2=".$tourn2Linked.",SCORES_LOCKED_FLAG=? WHERE TOURNAMENT_ID=".$_SESSION["tournamentId"]);
			
			$query->bind_param('ssssiiisiiiiii',$name,$location, $division,$date, $numberEvents, $numberTeams, $highestScore, $description, $highLowWins,$eventsAwarded, $overallAwarded, $bestNewTeam, $improvedTeam,$lockScoresFlag);
			$query->execute();
			$query->free_result();
			//$result = mysql_query($query);		

		
		}
	
	
		// save events
		// 0: EVENT_ID 1: NAME 2:TRIAL_EVENT 3: TOURN_EVENT_ID 4: New Event 0/1 5: USER_ID 6: USER NAME
		$eventList = $_SESSION["eventList"];
		if ($eventList) {
			foreach ($eventList as $event) {
			if ($event['5'] == '' || $event['5'] == '0') $event['5'] = null; // USER ID Should be null if blank
			
			if ($event['3'] == null or $event['3'] == '') {
				// Generate Next TOURN_EVENT_ID
				$result = $mysqli->query("select max(TOURN_EVENT_ID) + 1 from TOURNAMENT_EVENT");
				$row = $result->fetch_row();
				$id = 0;
				if ($row['0'] != null) $id = $row['0']; 
				
				$query = $mysqli->prepare("INSERT INTO TOURNAMENT_EVENT (TOURN_EVENT_ID, TOURNAMENT_ID, EVENT_ID, TRIAL_EVENT_FLAG, USER_ID) VALUES (".$id.", ?, ?, ?, ?) ");
				$query->bind_param('iiii',$_SESSION["tournamentId"],$event['0'], $event['2'], $event['5']); 
				$query->execute();
			}
			else {
				$query = $mysqli->prepare("UPDATE TOURNAMENT_EVENT SET TRIAL_EVENT_FLAG=?, USER_ID=? WHERE TOURN_EVENT_ID=".$event['3']);			
				$query->bind_param('ii',$event['2'], $event['5']);
				$query->execute();
			}
			
			
				
			}
		}
		 
		
		
		// save teams
		// 0: TEAM_ID 1: NAME 2:TEAM_NUMBER 3: ALTERNATE 4: TOURN_TEAM_ID
		$teamList = $_SESSION["teamList"];
		if ($teamList) {
			foreach ($teamList as $team) {
				$bestNew = null; if ($team[5] == $team[1]) $bestNew = 1; else $bestNew = 0;
				$mostImproved = null; if ($team[6] == $team[1]) $mostImproved = 1; else $mostImproved = 0;
			
			if ($team['4'] == null or $team['4'] == '') {
				// Generate Next TOURN_TEAM_ID
				$result = $mysqli->query("select max(TOURN_TEAM_ID) + 1 from TOURNAMENT_TEAM");
				$row = $result->fetch_row();
				$id = 0;
				if ($row['0'] != null) $id = $row['0'];
				 
				$query = $mysqli->prepare("INSERT INTO TOURNAMENT_TEAM (TOURN_TEAM_ID, TOURNAMENT_ID, TEAM_ID, TEAM_NUMBER, ALTERNATE_FLAG,BEST_NEW_TEAM_FLAG,MOST_IMPROVED_TEAM_FLAG) VALUES (".$id.", ?, ?, ?,?,?,?) ");
				$query->bind_param('iiiiii',$_SESSION["tournamentId"],$team['0'], $team['2'], $team['3'],$bestNew,$mostImproved); 
				$query->execute();
			}
			else {
				$query = $mysqli->prepare("UPDATE TOURNAMENT_TEAM SET TEAM_NUMBER=?, ALTERNATE_FLAG=?,BEST_NEW_TEAM_FLAG=?,MOST_IMPROVED_TEAM_FLAG=? WHERE TOURN_TEAM_ID=".$team['4']);			
				$query->bind_param('iiii',$team['2'], $team['3'],$bestNew,$mostImproved);
				$query->execute();
			}
			
			
				
			}
		}
		
		// save verifiers
		// 0: USER_ID 1: NAME 2: USERNAME 3: TOURN_VERIFIER_ID
		$verifierList = $_SESSION["verifierList"];
		if ($verifierList) {
			foreach ($verifierList as $verifier) {
				if ($verifier['3'] == null or $verifier['3'] == '') {
					// Generate Next TOURN_VERIFIER_ID
					$result = $mysqli->query("select max(TOURN_VERIFIER_ID) + 1 from TOURNAMENT_VERIFIER");
					$row = $result->fetch_row();
					$id = 0;
					if ($row['0'] != null) $id = $row['0'];
					 
					$query = $mysqli->prepare("INSERT INTO TOURNAMENT_VERIFIER (TOURN_VERIFIER_ID, TOURNAMENT_ID, USER_ID) VALUES (".$id.", ?, ?) ");
					$query->bind_param('ii',$_SESSION["tournamentId"],$verifier['0']); 
					$query->execute();
				}
				else {
					// DO NOTHING
				}	
			}
		}
		
		
		// save Confirmation
		$_SESSION['savesuccessTournament'] = "1";	
	
	}
	
	
	
		
// DISPLAY TOURNAMENT'S EVENTS SCREEN ---------------------------------------	
	function loadTournamentEvents($mysqli) {
	
		$query = "SELECT TE.EVENT_ID, E.NAME, TE.TRIAL_EVENT_FLAG, TE.TOURN_EVENT_ID, COUNT(TES.TEAM_EVENT_SCORE_ID) as SCORES_COMPLETED, 
					T.NUMBER_TEAMS, TE.SUBMITTED_FLAG, TE.VERIFIED_FLAG, T.HIGH_LOW_WIN_FLAG FROM TOURNAMENT_EVENT TE 
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID 
					INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID 
					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TES.SCORE IS NOT NULL									
					WHERE TE.TOURNAMENT_ID=".$_SESSION["tournamentId"]. 
					" GROUP BY EVENT_ID,NAME, TRIAL_EVENT_FLAG,TOURN_EVENT_ID, NUMBER_TEAMS
					ORDER BY UPPER(E.NAME) ASC"; 
					
		$_SESSION["tournamentEventsQuery"] = $query;
		
		 $result = $mysqli->query("SELECT * FROM TOURNAMENT WHERE TOURNAMENT_ID = " .$_SESSION["tournamentId"]); 
 			if ($result) {
 				$tournamentRow = $result->fetch_row();
 				
 				$_SESSION["tournamentName"] = $tournamentRow['1'];
 				$_SESSION["tournamentDivision"] = $tournamentRow['3'];
 				$_SESSION["tournamentLocation"] = $tournamentRow['2'];
 				$_SESSION["numberEvents"] = $tournamentRow['5'];
 				$_SESSION["numberTeams"] = $tournamentRow['6'];
 				$_SESSION["highestScore"] = $tournamentRow['7'];
 				$_SESSION["tournamentDescription"] = $tournamentRow['8'];
				if ($tournamentRow['9'] == '0' ) $_SESSION["pointsSystem"] = 'Low Score Wins';
				else $_SESSION["pointsSystem"] = 'High Score Wins';
				$_SESSION["lockScoresFlag"] = $tournamentRow['16'];
				
 				
 			$result = $mysqli->query($query);
 			$completedCount = 0;
 			if ($result) {
 				while($scoreRow = $result->fetch_array()) { 
 					if ($scoreRow['7'] == 1 and $scoreRow['4'] == $scoreRow['5']) $completedCount++;
 				}
 			}
 			$_SESSION["tournamentEventsCompleted"] = $completedCount.' / '.$_SESSION["numberEvents"];
 				
 				$date = strtotime($tournamentRow['4']);
 				$_SESSION["tournamentDate"] = date('m/d/Y', $date);		
    		}
			
			
		
		
	}
	
	
// MANAGE EVENT SCORES SCREEN ---------------------------------------	
	function loadEventScores($mysqli) {
				
		 $result = $mysqli->query("SELECT E.NAME, T.HIGHEST_SCORE_POSSIBLE, T.TOURNAMENT_ID, T.DIVISION, T.NAME,TE.SUBMITTED_FLAG,TE.VERIFIED_FLAG,
							CONCAT(U.FIRST_NAME, ' ', U.LAST_NAME, ' - ',U.USERNAME,' - ',coalesce(U.PHONE_NUMBER,'')) as supervisor, T.DATE, T.HIGH_LOW_WIN_FLAG, TE.COMMENTS, 
							E.SCORE_SYSTEM_CODE, RD.DISPLAY_TEXT 
		 					FROM EVENT E INNER JOIN TOURNAMENT_EVENT TE ON TE.EVENT_ID=E.EVENT_ID 
		 					INNER JOIN TOURNAMENT T ON T.TOURNAMENT_ID=TE.TOURNAMENT_ID 
							LEFT JOIN USER U ON TE.USER_ID=U.USER_ID
							LEFT JOIN REF_DATA RD on RD.REF_DATA_CODE=E.SCORE_SYSTEM_CODE AND RD.DOMAIN_CODE='SCOREALGORITHM' 
							WHERE TE.TOURN_EVENT_ID = " .$_SESSION["tournEventId"]); 
 			if ($result) {
 				$tournamentRow = $result->fetch_row(); 				
 				$_SESSION["eventName"] = $tournamentRow['0'];
 				$_SESSION["tournamentHighestScore"] = $tournamentRow['1'];
 				$_SESSION["highLowWinFlag"] = $tournamentRow['9'];
 				$_SESSION["tournamentId"] = $tournamentRow['2'];
 				
 				$_SESSION["tournamentDivision"] = $tournamentRow['3'];
 				$_SESSION["tournamentName"] = $tournamentRow['4'];
 				$_SESSION["submittedFlag"] = $tournamentRow['5'];
 				$_SESSION["verifiedFlag"] = $tournamentRow['6'];
				$_SESSION["eventSupervisor"] = $tournamentRow['7'];
				$date = strtotime($tournamentRow['8']);
 				$_SESSION["tournamentDate"] = date('m/d/Y', $date);	
 				$_SESSION["eventComments"] = $tournamentRow['10'];
 				$_SESSION["scoreSystemCode"] = $tournamentRow['11'];
 				$_SESSION["scoreSystemText"] = $tournamentRow['12'];
    		}
    	
		// Primary Teams
    	 $result = $mysqli->query("SELECT T.NAME, TT.TEAM_NUMBER, TES.SCORE, TES.TEAM_EVENT_SCORE_ID, TT.TOURN_TEAM_ID, TES.POINTS_EARNED, TES.RAW_SCORE, TES.TIER_TEXT, 								TES.TIE_BREAK_TEXT, TES.TEAM_STATUS 
    	 					FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID AND TT.ALTERNATE_FLAG = 0 
    	 					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID AND TES.TOURN_EVENT_ID = " .$_SESSION["tournEventId"].
    	 					" WHERE TT.TOURNAMENT_ID = " .$_SESSION["tournamentId"]. " ORDER BY TEAM_NUMBER ASC "); 
 			$teamEventScoreList = array();
 			if ($result) {
 				while($scoreRow = $result->fetch_array()) {
 					$scoreRecord = array();	
 					array_push($scoreRecord, $scoreRow['0']);
 					array_push($scoreRecord, $scoreRow['1']);
 					array_push($scoreRecord, $scoreRow['2']);
 					array_push($scoreRecord, $scoreRow['3']);
 					array_push($scoreRecord, $scoreRow['4']);
 					array_push($scoreRecord, $scoreRow['5']);
 					array_push($scoreRecord, $scoreRow['6']);
 					array_push($scoreRecord, $scoreRow['7']);
 					array_push($scoreRecord, $scoreRow['8']);
 					array_push($scoreRecord, $scoreRow['9']);	
 						
 					array_push($teamEventScoreList, $scoreRecord);
 				}
    		}
    									
    		$_SESSION["teamEventScoreList"] = $teamEventScoreList;
			
		// Alternate Teams
		$_SESSION["teamAlternateEventScoreList"] = null;
		    $result = $mysqli->query("SELECT T.NAME, TT.TEAM_NUMBER, TES.SCORE, TES.TEAM_EVENT_SCORE_ID, TT.TOURN_TEAM_ID, TES.POINTS_EARNED, TES.RAW_SCORE, TES.TIER_TEXT, 								TES.TIE_BREAK_TEXT, TES.TEAM_STATUS 
    	 					FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID AND TT.ALTERNATE_FLAG = 1
    	 					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID AND TES.TOURN_EVENT_ID = " .$_SESSION["tournEventId"].
    	 					" WHERE TT.TOURNAMENT_ID = " .$_SESSION["tournamentId"]. " ORDER BY TEAM_NUMBER ASC "); 
 			$teamAlternateEventScoreList = array();
 			if ($result) {
 				while($scoreRow = $result->fetch_array()) {
 					$scoreRecord = array();	
 					array_push($scoreRecord, $scoreRow['0']);
 					array_push($scoreRecord, $scoreRow['1']);
 					array_push($scoreRecord, $scoreRow['2']);
 					array_push($scoreRecord, $scoreRow['3']);
 					array_push($scoreRecord, $scoreRow['4']);
 					array_push($scoreRecord, $scoreRow['5']);
 					array_push($scoreRecord, $scoreRow['6']);
 					array_push($scoreRecord, $scoreRow['7']);
 					array_push($scoreRecord, $scoreRow['8']);
 					array_push($scoreRecord, $scoreRow['9']);	
 						
 					array_push($teamAlternateEventScoreList, $scoreRecord);
 				}
    		}
    									
    		$_SESSION["teamAlternateEventScoreList"] = $teamAlternateEventScoreList;
	}
	
	function saveEventScores($mysqli) {	
	// Primary Teams
		$scoreList = $_SESSION["teamEventScoreList"];
		if ($scoreList) {
			$teamCount = 0;
			foreach ($scoreList as $score) {
				$value = $_GET['teamScore'.$teamCount];
				$rawScore = $_GET['teamRawScore'.$teamCount];
				$status = $_GET['teamStatus'.$teamCount];
				$tier = $_GET['teamScoreTier'.$teamCount];
				$tieBreak = $_GET['teamTieBreak'.$teamCount];
				$pointsEarned = $_GET['teamPointsEarned'.$teamCount];
				
				
				if ($value == '') $value = null;
				if ($pointsEarned == '') $pointsEarned = null;
					
				if ($score['3'] == null or $score['3'] == '') {
					$result = $mysqli->query("select max(TEAM_EVENT_SCORE_ID) + 1 from TEAM_EVENT_SCORE");
					$row = $result->fetch_row();
					$id = 0;
					if ($row['0'] != null) $id = $row['0']; 
				
					$query = $mysqli->prepare("INSERT INTO TEAM_EVENT_SCORE (TEAM_EVENT_SCORE_ID, TOURN_TEAM_ID, TOURN_EVENT_ID, SCORE, POINTS_EARNED, RAW_SCORE, TIER_TEXT, 											TIE_BREAK_TEXT, TEAM_STATUS) VALUES (".$id.",?,?,?,?,?,?,?,?) ");
					$query->bind_param('iiiissss',$score['4'],$_SESSION["tournEventId"], $value,$pointsEarned,$rawScore,$tier,$tieBreak,$status); 
					$query->execute();
					$score['3'] = $id;
				}
				else {
					$query = $mysqli->prepare("UPDATE TEAM_EVENT_SCORE SET SCORE=?, POINTS_EARNED=?, RAW_SCORE=?,TIER_TEXT=?,TIE_BREAK_TEXT=?,TEAM_STATUS=? WHERE TEAM_EVENT_SCORE_ID=".$score['3']);			
					$query->bind_param('iissss',$value,$pointsEarned,$rawScore,$tier,$tieBreak,$status);
					$query->execute();
				}
				$teamCount++;	
			}
		}
		
	// Alternate Teams
		$scoreList = $_SESSION["teamAlternateEventScoreList"];
		if ($scoreList) {
			$teamCount = 0;
			foreach ($scoreList as $score) {
				$value = $_GET['teamAScore'.$teamCount];
				$status = $_GET['teamAStatus'.$teamCount];
				$rawScore = $_GET['teamARawScore'.$teamCount];
				$tier = $_GET['teamAScoreTier'.$teamCount];
				$tieBreak = $_GET['teamATieBreak'.$teamCount];
				$pointsEarned = $_GET['teamAPointsEarned'.$teamCount];
				
				
				if ($value == '') $value = null;
					
				if ($score['3'] == null or $score['3'] == '') {
					$result = $mysqli->query("select max(TEAM_EVENT_SCORE_ID) + 1 from TEAM_EVENT_SCORE");
					$row = $result->fetch_row();
					$id = 0;
					if ($row['0'] != null) $id = $row['0']; 
				
					$query = $mysqli->prepare("INSERT INTO TEAM_EVENT_SCORE (TEAM_EVENT_SCORE_ID, TOURN_TEAM_ID, TOURN_EVENT_ID, SCORE, POINTS_EARNED, RAW_SCORE, TIER_TEXT, 											TIE_BREAK_TEXT, TEAM_STATUS) VALUES (".$id.",?,?,?,?,?,?,?,?) ");
					$query->bind_param('iiiissss',$score['4'],$_SESSION["tournEventId"], $value,$pointsEarned,$rawScore,$tier,$tieBreak,$status); 
					$query->execute();
					$score['3'] = $id;
				}
				else {
					$query = $mysqli->prepare("UPDATE TEAM_EVENT_SCORE SET SCORE=?, POINTS_EARNED=?, RAW_SCORE=?,TIER_TEXT=?,TIE_BREAK_TEXT=?,TEAM_STATUS=? WHERE TEAM_EVENT_SCORE_ID=".$score['3']);			
					$query->bind_param('iissss',$value,$pointsEarned,$rawScore,$tier,$tieBreak,$status);
					$query->execute();
				}
				$teamCount++;	
			}
		}
	
	
		// Update Submitted/Verified Flags
		$query = $mysqli->prepare("UPDATE TOURNAMENT_EVENT SET SUBMITTED_FLAG=?, VERIFIED_FLAG=?, COMMENTS=? WHERE TOURN_EVENT_ID=".$_SESSION["tournEventId"]);			
		$query->bind_param('iis',$_GET['submittedFlag'], $_GET['verifiedFlag'], $_GET['eventComments']); 
		$query->execute();
			
		$_SESSION["teamEventScoreList"] = $scoreList;
		$_SESSION['savesuccessScore'] = "1";
	}


	// MANAGE EVENTS SCREEN ---------------------------------------
	function loadAllEvents($mysqli) {
			$eventList = array();
			$query = "Select * from EVENT WHERE 1=1 ";
			if ($_SESSION["eventFilterName"] != null) {
				$query = $query . " AND NAME LIKE '".$_SESSION["eventFilterName"]."%' " ;
			}
			$query = $query . " ORDER BY NAME ASC ";
			if ($_SESSION["eventFilterNumber"] != null) {
				$query = $query . " LIMIT ".$_SESSION["eventFilterNumber"];
			}
			
			$result = $mysqli->query($query); 
 			if ($result) {
				while($eventRow = $result->fetch_array()) {
 					$eventRecord = array();	
					array_push($eventRecord, $eventRow['0']);
					array_push($eventRecord, $eventRow['1']);
 				
					array_push($eventList, $eventRecord);
				}
			}
		
		
		$_SESSION["eventsList"] = $eventList;
	}
	
	function clearEvent() {
		$_SESSION["eventId"] = null;
		$_SESSION["eventName"] = null;
		$_SESSION["eventDescription"] = null;
	}


	function loadEvent($id, $mysqli) {
		$result = $mysqli->query("SELECT * FROM EVENT WHERE EVENT_ID = " .$id); 
 			if ($result) {
 				$eventRow = $result->fetch_row();	
 				$_SESSION["eventId"] = $eventRow['0'];
 				$_SESSION["eventName"] = $eventRow['1'];
 				$_SESSION["eventDescription"] = $eventRow['2'];
 				$_SESSION["scoreSystemCode"] = $eventRow['3'];			
    		}
	}


	function saveEvent($mysqli) {
		// if Event id is null create new
		if ($_SESSION["eventId"] == null) { 
			$result = $mysqli->query("select max(EVENT_ID) + 1 from EVENT");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null) $id = $row['0'];  
			$_SESSION["eventId"] = $id;
			
			$query = $mysqli->prepare("INSERT INTO EVENT (EVENT_ID, NAME, COMMENTS, SCORE_SYSTEM_CODE) VALUES (".$id.", ?, ?,?) ");
			
			$query->bind_param('sss',$_GET["eventName"], $_GET["eventDescription"], $_GET["scoreSystemCode"]);
			
			$query->execute();
			$query->free_result();
		}
		else {
			$query = $mysqli->prepare("UPDATE EVENT SET NAME=?, COMMENTS=?, SCORE_SYSTEM_CODE=? WHERE EVENT_ID=".$_SESSION["eventId"]);
			
			$query->bind_param('sss',$_GET["eventName"], $_GET["eventDescription"], $_GET["scoreSystemCode"]);
			$query->execute();
			$query->free_result();
		}
		// save Confirmation
		$_SESSION['savesuccessEvent'] = "1";	
	}
	
	function deleteEvent($id, $mysqli) {
		$result = $mysqli->query("SELECT TOURN_EVENT_ID FROM TOURNAMENT_EVENT WHERE EVENT_ID = ".$id); 
		$count = $result->num_rows;
		
		if ($count > 0) { $_SESSION['deleteEventError'] = "1";	return false; }

		$query = $mysqli->prepare("DELETE FROM EVENT WHERE EVENT_ID = ?");	
		$query->bind_param('i',$id);	
		$query->execute();
		$_SESSION['deleteEventSuccess'] = "1";
		return true;
	
	}
	
	function isEventCreated($mysqli) {
		$_SESSION["eventName"] = $_GET["eventName"];
		$_SESSION["eventDescription"] = $_GET["eventDescription"];
		$id = -1;
		if ($_SESSION["eventId"] != null and $_SESSION["eventId"] != '') $id = $_SESSION["eventId"];
		
		$result = $mysqli->query("SELECT EVENT_ID FROM EVENT WHERE EVENT_ID <> ".$id." AND UPPER(NAME) = '".strtoupper($_GET["eventName"])."' "); 
		$count = $result->num_rows;
		
		if ($count > 0) { $_SESSION['saveEventError'] = "1";	return true; }
		return false;
	}




	// MANAGE TEAMS SCREEN ---------------------------------------
	function clearTeam() {
		$_SESSION["teamId"] = null;
		$_SESSION["teamName"] = null;
		$_SESSION["teamCity"] = null;
		$_SESSION["teamPhone"] = null;
		$_SESSION["teamEmail"] = null;
		$_SESSION["teamDivision"] = null;
		$_SESSION["teamDescription"] = null;
	}
	
	function loadAllTeams($mysqli) {
			$teamList = array();
			$query = "Select * from TEAM WHERE 1=1 ";
			if ($_SESSION["teamFilterName"] != null) {
				$query = $query . " AND NAME LIKE '".$_SESSION["teamFilterName"]."%' " ;
			}
			if ($_SESSION["filterDivision"] != null and $_SESSION["filterDivision"] != '') {
				$query = $query . " AND DIVISION = '".$_SESSION["filterDivision"]."' " ;
			}
			$query = $query . " ORDER BY NAME ASC ";
			if ($_SESSION["teamFilterNumber"] != null) {
				$query = $query . " LIMIT ".$_SESSION["teamFilterNumber"];
			}
			
			$result = $mysqli->query($query); 
 			if ($result) {
				while($teamRow = $result->fetch_array()) {
 					$teamRecord = array();	
					array_push($teamRecord, $teamRow['0']);
					array_push($teamRecord, $teamRow['1']);
					array_push($teamRecord, $teamRow['6']);
 				
					array_push($teamList, $teamRecord);
				}
			}
		$_SESSION["resultsPage"] = 1;
		$_SESSION["teamsList"] = $teamList;
	}
	
	function loadTeam($id, $mysqli) {
		$result = $mysqli->query("SELECT * FROM TEAM WHERE TEAM_ID = " .$id); 
 			if ($result) {
 				$eventRow = $result->fetch_row();	
 				$_SESSION["teamId"] = $eventRow['0'];
 				$_SESSION["teamName"] = $eventRow['1'];
 				$_SESSION["teamCity"] = $eventRow['2']; 
				$_SESSION["teamPhone"] = $eventRow['4'];
				$_SESSION["teamEmail"] = $eventRow['3'];
				$_SESSION["teamDescription"] = $eventRow['5'];
				$_SESSION["teamDivision"] = $eventRow['6'];			
    		}
	}
	
	function saveTeam($mysqli) {
		// if Event id is null create new
		if ($_SESSION["teamId"] == null) { 
			$result = $mysqli->query("select max(TEAM_ID) + 1 from TEAM");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null) $id = $row['0'];  
			$_SESSION["teamId"] = $id;
			
			$query = $mysqli->prepare("INSERT INTO TEAM (TEAM_ID, NAME, CITY, PHONE_NUMBER, EMAIL_ADDRESS, DESCRIPTION, DIVISION) VALUES (".$id.",?,?,?,?,?,?) ");
			
			$query->bind_param('ssssss',$_GET["teamName"], $_GET["teamCity"],$_GET["teamPhone"],$_GET["teamEmail"], $_GET["teamDescription"], $_GET["teamDivision"]);
			
			$query->execute();
			$query->free_result();
		}
		else {
			$query = $mysqli->prepare("UPDATE TEAM SET NAME=?, CITY=?, PHONE_NUMBER=?, EMAIL_ADDRESS=?, DESCRIPTION=?, DIVISION=? WHERE TEAM_ID=".$_SESSION["teamId"]);
			
			$query->bind_param('ssssss',$_GET["teamName"], $_GET["teamCity"],$_GET["teamPhone"],$_GET["teamEmail"], $_GET["teamDescription"], $_GET["teamDivision"]);
			$query->execute();
			$query->free_result();
		}
		// save Confirmation
		$_SESSION['savesuccessTeam'] = "1";	
	}
	
	function deleteTeam($id, $mysqli) {
		$result = $mysqli->query("SELECT TOURN_TEAM_ID FROM TOURNAMENT_TEAM WHERE TEAM_ID = ".$id); 
		$count = $result->num_rows;
		
		if ($count > 0) { $_SESSION['deleteTeamError'] = "1";	return false; }

		$query = $mysqli->prepare("DELETE FROM TEAM WHERE TEAM_ID = ?");	
		$query->bind_param('i',$id);	
		$query->execute();
		$_SESSION['deleteTeamSuccess'] = "1";
		return true;
	
	}
	
	function isTeamCreated($mysqli) {
		$_SESSION["teamName"] = $_GET["teamName"];
		$_SESSION["teamDescription"] = $_GET["teamDescription"];
		$_SESSION["teamCity"] = $_GET["teamCity"];
		$_SESSION["teamPhone"] = $_GET["teamPhone"];
		$_SESSION["teamEmail"] = $_GET["teamEmail"];
		$_SESSION["teamDivision"] = $_GET["teamDivision"];
		
		$id = -1;
		if ($_SESSION["teamId"] != null and $_SESSION["teamId"] != '') $id = $_SESSION["teamId"];
		
		$result = $mysqli->query("SELECT TEAM_ID FROM TEAM WHERE TEAM_ID <> ".$id." AND UPPER(NAME) = '".strtoupper($_GET["teamName"])."' "); 
		$count = $result->num_rows;
		
		if ($count > 0) { $_SESSION['saveTeamError'] = "1";	return true; }
		return false;
	}

	
	// GENERATE RESULTS / STATISTICS ---------------------------------------
	function generateTournamentResults($id, $mysqli) {
		$tournamentResultsHeader = array();
		$tournamentResults = array();
		$tournamentAlternateResults = array();
		$highLowWin = '0';
		
		// Load Event Headers
		$query1 = "SELECT E.NAME, TE.TOURN_EVENT_ID, TE.TRIAL_EVENT_FLAG, TM.HIGH_LOW_WIN_FLAG
					FROM TOURNAMENT TM
					INNER JOIN TOURNAMENT_EVENT TE ON TE.TOURNAMENT_ID=TM.TOURNAMENT_ID 
					INNER JOIN EVENT E ON E.EVENT_ID=TE.EVENT_ID 
					WHERE TM.TOURNAMENT_ID=".$id."
					ORDER BY NAME ASC ";
					
		$result = $mysqli->query($query1); 
 		if ($result) {
			while($events = $result->fetch_array()) {
					$eventName = $events['0'];
					if ($events['2'] != null and $events['2'] == 1) $eventName .= ' *';
					array_push($tournamentResultsHeader, $eventName);
					$highLowWin = $events['3'];
			}
		}	
		$_SESSION['tournamentResultsHeader'] = $tournamentResultsHeader;	
		
		// Load Primary Teams	
		$tournamentResults = getPrimaryTournamentResults($id,$mysqli,$highLowWin,$tournamentResults);
		// Additional Ordering OPTIONS
		usort($tournamentResults, "sortTeamNumberAsc");
		$_SESSION['tournamentResults'] = $tournamentResults;
		
		
		// Load Alternate Teams
		$tournamentAlternateResults = getAlternateTournamentResults($id,$mysqli,$highLowWin,$tournamentResults);
		// Additional Ordering OPTIONS
		usort($tournamentAlternateResults, "sortTeamNumberAsc");
		$_SESSION['tournamentAlternateResults'] = $tournamentAlternateResults;
	}
	
	// Results Structure:
	// TOURN_TEAM_ID : TEAM_NAME : TEAM_NUMBER : EV1_SCR : EV2_SCR : EV3_SCR ... ... : TOTAL_SCORE : RANK : POSITIONCOUNTS
	
	function getPrimaryTournamentResults($id,$mysqli,$highLowWin,$tournamentResults) {
				$query2 = "  SELECT TT.TEAM_NUMBER,T.NAME, TT.TOURN_TEAM_ID, TT.ALTERNATE_FLAG 
					FROM TOURNAMENT TM
					INNER JOIN TOURNAMENT_TEAM TT ON TT.TOURNAMENT_ID=TM.TOURNAMENT_ID
					INNER JOIN TEAM T ON T.TEAM_ID=TT.TEAM_ID
					WHERE TT.ALTERNATE_FLAG=0 AND TM.TOURNAMENT_ID=".$id."
					 ORDER BY TEAM_NUMBER ASC ";
		$result = $mysqli->query($query2); 
 		if ($result) {
			while($teams = $result->fetch_array()) {
					$resultRow = array();
					$teamName = $teams['1'];
					if ($teams['3'] != null and $teams['3'] == 1) $teamName .= ' +';
					array_push($resultRow, $teams['2']);
					array_push($resultRow, $teams['0']);
					array_push($resultRow, $teamName);
					
					$query3 = "SELECT TES.SCORE, E.NAME, TE.TRIAL_EVENT_FLAG, TES.POINTS_EARNED
								FROM TOURNAMENT_TEAM TT
                                INNER JOIN TOURNAMENT T ON T.TOURNAMENT_ID=TT.TOURNAMENT_ID
								INNER JOIN TOURNAMENT_EVENT TE ON TE.TOURNAMENT_ID=T.TOURNAMENT_ID
								INNER JOIN EVENT E ON E.EVENT_ID=TE.EVENT_ID
                                LEFT JOIN TEAM_EVENT_SCORE TES ON TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID 
								AND TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID
								WHERE TT.TOURN_TEAM_ID=".$teams['2']."
                                ORDER BY NAME ASC ";
					$scoreSet = $mysqli->query($query3); 
					$total = 0;
					$positionCounts = getNewPositionCountMap();
					if ($scoreSet) {
						while($scores = $scoreSet->fetch_array()) {
							$value = $scores['0'];
							array_push($resultRow, $scores['3']);
							if ($value <= 20 ) { // sizeof($resultRow)
								if ($scores['3'] != null and $scores['2'] != 1) // Trial Events not included in position count/tiebreaker
									$positionCounts[$value] = $positionCounts[$value] + 1;
							}
							if ($scores['3'] != null and $scores['2'] != 1) // Trial Events not included in total
								$total = $total + $scores['3'];
						}
					}
					array_push($resultRow, $total); // Total
					array_push($resultRow, 0); // Rank
					array_push($resultRow, $positionCounts);					
					array_push($tournamentResults, $resultRow);
			}
		}	
		// Determine Final Rank With Tie Breakers
		$tournamentResults = quicksort($tournamentResults, $highLowWin); 
		
		// Set Final Rank VALUE
		$count = 1;
		foreach ($tournamentResults as $k => $row) {
			$row[sizeof($row)-2] = $count;
			$tournamentResults[$k] = $row;
			$count++;
		}	
		return $tournamentResults;
	}
	
	function getAlternateTournamentResults($id,$mysqli,$highLowWin,$tournamentResults) {
		$query2 = "  SELECT TT.TEAM_NUMBER,T.NAME, TT.TOURN_TEAM_ID, TT.ALTERNATE_FLAG 
					FROM TOURNAMENT TM
					INNER JOIN TOURNAMENT_TEAM TT ON TT.TOURNAMENT_ID=TM.TOURNAMENT_ID
					INNER JOIN TEAM T ON T.TEAM_ID=TT.TEAM_ID
					WHERE TT.ALTERNATE_FLAG=1 AND TM.TOURNAMENT_ID=".$id."
					 ORDER BY TEAM_NUMBER ASC ";
		$result = $mysqli->query($query2); 
		$_SESSION['tournamentAlternateResults'] = null;
 		if ($result) {
			$tournamentAlternateResults = array();
			while($teams = $result->fetch_array()) {
					$resultRow = array();
					$teamName = $teams['1'];
					if ($teams['3'] != null and $teams['3'] == 1) $teamName .= ' +';
					array_push($resultRow, $teams['2']);
					array_push($resultRow, $teams['0']);
					array_push($resultRow, $teamName);
					
					$query3 = "SELECT TES.SCORE, E.NAME, TE.TRIAL_EVENT_FLAG, TES.POINTS_EARNED
								FROM TOURNAMENT_TEAM TT
                                INNER JOIN TOURNAMENT T ON T.TOURNAMENT_ID=TT.TOURNAMENT_ID
								INNER JOIN TOURNAMENT_EVENT TE ON TE.TOURNAMENT_ID=T.TOURNAMENT_ID
								INNER JOIN EVENT E ON E.EVENT_ID=TE.EVENT_ID
                                LEFT JOIN TEAM_EVENT_SCORE TES ON TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID 
								AND TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID
								WHERE TT.TOURN_TEAM_ID=".$teams['2']."
                                ORDER BY NAME ASC ";
					$scoreSet = $mysqli->query($query3); 
					$total = 0;
					$positionCounts = getNewPositionCountMap();
					if ($scoreSet) {
						while($scores = $scoreSet->fetch_array()) {
							$value = $scores['0'];
							array_push($resultRow, $scores['3']);
							if ($value <= 20 ) { // sizeof($resultRow)
								if ($scores['3'] != null and $scores['2'] != 1) // Trial Events not included in position count/tiebreaker
									$positionCounts[$value] = $positionCounts[$value] + 1;
							}
							if ($scores['3'] != null and $scores['2'] != 1) // Trial Events not included in total
								$total = $total + $scores['3'];
						}
					}
					array_push($resultRow, $total); // Total
					array_push($resultRow, '-'); // Rank
					array_push($resultRow, $positionCounts);					
					array_push($tournamentAlternateResults, $resultRow);
					
			}
			return $tournamentAlternateResults;
		}		
	}
	
	// QuickSort Results on Total Score + Tie Breakers
	function quicksort($array, $highLowWin) {
		if(count($array) < 2) {
			return $array;
		}
		$left = $right = array();
		reset($array);
		$pivot_key = key($array);
		$pivot  = array_shift($array);
		foreach($array as $k => $v) {
			//echo $v[sizeof($v)-3] . ' ' . $pivot[sizeof($pivot)-3]  . '<br />';
			if (($v[sizeof($v)-3] < $pivot[sizeof($pivot)-3]) and $highLowWin == 0)
				$left[$k] = $v;
			else if (($v[sizeof($v)-3] > $pivot[sizeof($pivot)-3]) and $highLowWin == 1)
				$left[$k] = $v;
			else if ($v[sizeof($v)-3] == $pivot[sizeof($pivot)-3])  {
				$vCounts = $v[sizeof($v)-1];
				$pivotCounts = $pivot[sizeof($pivot)-1];
				
					 if ($vCounts[1] >  $pivotCounts[1]) $left[$k] = $v; else if ($vCounts[1] < $pivotCounts[1]) $right[$k] = $v;
				else if ($vCounts[2] >  $pivotCounts[2]) $left[$k] = $v; else if ($vCounts[2] <  $pivotCounts[2]) $right[$k] = $v;
				else if ($vCounts[3] >  $pivotCounts[3]) $left[$k] = $v; else if ($vCounts[3] <  $pivotCounts[3]) $right[$k] = $v;
				else if ($vCounts[4] >  $pivotCounts[4]) $left[$k] = $v; else if ($vCounts[4] <  $pivotCounts[4]) $right[$k] = $v;
				else if ($vCounts[5] >  $pivotCounts[5]) $left[$k] = $v; else if ($vCounts[5] <  $pivotCounts[5]) $right[$k] = $v;
				else if ($vCounts[6] >  $pivotCounts[6]) $left[$k] = $v; else if ($vCounts[6] <  $pivotCounts[6]) $right[$k] = $v;
				else if ($vCounts[7] >  $pivotCounts[7]) $left[$k] = $v; else if ($vCounts[7] <  $pivotCounts[7]) $right[$k] = $v;
				else if ($vCounts[8] >  $pivotCounts[8]) $left[$k] = $v; else if ($vCounts[8] <  $pivotCounts[8]) $right[$k] = $v;
				else if ($vCounts[9] >  $pivotCounts[9]) $left[$k] = $v; else if ($vCounts[9] <  $pivotCounts[9]) $right[$k] = $v;
				else if ($vCounts[10] >  $pivotCounts[10]) $left[$k] = $v; else if ($vCounts[10] <  $pivotCounts[10]) $right[$k] = $v;
				else if ($vCounts[11] >  $pivotCounts[11]) $left[$k] = $v; else if ($vCounts[11] <  $pivotCounts[11]) $right[$k] = $v;
				else if ($vCounts[12] >  $pivotCounts[12]) $left[$k] = $v; else if ($vCounts[12] <  $pivotCounts[12]) $right[$k] = $v;
				else if ($vCounts[13] >  $pivotCounts[13]) $left[$k] = $v; else if ($vCounts[13] <  $pivotCounts[13]) $right[$k] = $v;
				else if ($vCounts[14] >  $pivotCounts[14]) $left[$k] = $v; else if ($vCounts[14] <  $pivotCounts[14]) $right[$k] = $v;
				else if ($vCounts[15] >  $pivotCounts[15]) $left[$k] = $v; else if ($vCounts[15] <  $pivotCounts[15]) $right[$k] = $v;
				else if ($vCounts[16] >  $pivotCounts[16]) $left[$k] = $v; else if ($vCounts[16] <  $pivotCounts[16]) $right[$k] = $v;
				else if ($vCounts[17] >  $pivotCounts[17]) $left[$k] = $v; else if ($vCounts[17] <  $pivotCounts[17]) $right[$k] = $v;
				else if ($vCounts[18] >  $pivotCounts[18]) $left[$k] = $v; else if ($vCounts[18] <  $pivotCounts[18]) $right[$k] = $v;
				else if ($vCounts[19] >  $pivotCounts[19]) $left[$k] = $v; else if ($vCounts[19] < $pivotCounts[19]) $right[$k] = $v;
				else if ($vCounts[20] >  $pivotCounts[20]) $left[$k] = $v; else if ($vCounts[20] <  $pivotCounts[20]) $right[$k] = $v;
				else $right[$k] = $v;

			}
			else
				$right[$k] = $v;
		}
		return array_merge(quicksort($left, $highLowWin), array($pivot_key => $pivot), quicksort($right, $highLowWin));
	
	}
	

	function getNewPositionCountMap() {
		$array = array(
			1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,
			12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,20=>0,21=>0,22=>0		
		);
		return $array;
	}
	
	function sortTeamNumberAsc($a, $b) {
		if ($a[1] < $b[1]) return -1;
		if ($a[1] > $b[1]) return 1;
		return 0;
	}
	
	function exportResultsCSV($mysqli) {
	
	 	// filename for download
  		$filename = $_SESSION["tournamentName"]." Results " . $_SESSION["tournamentDivision"] . ".csv";
  		header("Content-Disposition: attachment; filename=\"$filename\"");
  		header("Content-Type: text/csv; charset=utf-8");
  		
  		$output = fopen('php://output', 'w');

		$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
		$headings = array();
		array_push($headings,"#");
		array_push($headings,$_SESSION["tournamentName"]."\nDivision: ".$_SESSION["tournamentDivision"]."\nDate: ".$_SESSION["tournamentDate"]);
		if ($tournamentResultsHeader != null) {
			foreach ($tournamentResultsHeader as $resultHeader) {				
				array_push($headings,str_replace(',','',$resultHeader));
			}
		}
		array_push($headings,"Total Score");
		array_push($headings,"Final Rank");
		
		fputcsv($output, $headings);

		// Primary Teams
		 $tournamentResults = $_SESSION['tournamentResults'];
         if ($tournamentResults != null) {
			 foreach ($tournamentResults as $resultRow) {
				$row = array();
				array_push($row,$resultRow['1']);
				array_push($row,str_replace(',','',$resultRow['2']));
				$i = 3;
				while ($i < sizeof($resultRow)-1) {
					array_push($row,$resultRow[$i]);
					$i++;
				}
				fputcsv($output, $row);
		 	}
    	}
		
		// Alternate Teams
		$tournamentAlternateResults = $_SESSION['tournamentAlternateResults'];
		 if ($tournamentAlternateResults != null) {		
			$row = array();
			fputcsv($output, $row);
			 foreach ($tournamentAlternateResults as $resultRow) {
				$row = array();
				array_push($row,$resultRow['1']);
				array_push($row,str_replace(',','',$resultRow['2']));
				$i = 3;
				while ($i < sizeof($resultRow)-1) {
					array_push($row,$resultRow[$i]);
					$i++;
				}
				fputcsv($output, $row);
		 	}
    	}
		
		
		$row = array();
		fputcsv($output, $row);
		array_push($row,'');array_push($row,'* = Trial Event');
		fputcsv($output, $row);
		$row = array();
    	array_push($row,'');array_push($row,'+ = Alternate Team');
		fputcsv($output, $row);
		
		fclose($output);
		exit;
	}
	
	function exportResultsEXCEL($mysqli) {
		$filename = $_SESSION["tournamentName"]." Results " . $_SESSION["tournamentDivision"] . ".xlsx";
		$title = $_SESSION["tournamentName"]." Results " . $_SESSION["tournamentDivision"];
		
		$filename = str_replace(" ", "_", $filename);$filename = str_replace("/", "", $filename);$filename = str_replace("\\", "", $filename);$filename = str_replace("'", "", $filename);
		$title = str_replace(" ", "_", $title);$title = str_replace("/", "", $title);$title = str_replace("\\", "", $title);$title = str_replace("'", "", $title);
		
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->getProperties()->setCreator("Score Center")
							 ->setLastModifiedBy("Score Center")
							 ->setTitle($title)
							 ->setSubject($title)
							 ->setDescription("Score Center Science Olympiad Results Spreadsheet")
							 ->setKeywords("Science Olympiad Score Center Results Scores")
							 ->setCategory("Score Center Results");
		
		// Format Sheet
		$objExcelSheet = $objPHPExcel->getSheet(0);
		// ASCII 65 - 90 (A-Z)
		$asciiValue = 67;
		$rowCount = 2;
		$colPrefixAscii = 65;
		$colPrefix = '';

		// Header
		$objExcelSheet->setCellValue('A1', '#')->setCellValue('B1', $_SESSION["tournamentName"]."\nDivision: ".$_SESSION["tournamentDivision"]."\nDate: ".$_SESSION["tournamentDate"]);
		$objExcelSheet->getColumnDimension('A')->setWidth(4);
		$objExcelSheet->getColumnDimension('B')->setAutoSize(true);
		$objExcelSheet->getStyle('A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
		$objExcelSheet->getStyle('B1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
		$objExcelSheet->getStyle('B1')->getAlignment()->setWrapText(true);
		$objExcelSheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,));
		$objExcelSheet->getStyle('A1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objExcelSheet->getStyle('B1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];								
		if ($tournamentResultsHeader != null) {
			foreach ($tournamentResultsHeader as $resultHeader) {		
				if ($asciiValue > 90) {$asciiValue = 65; $colPrefix = chr($colPrefixAscii); $colPrefixAscii++;}
				if ($asciiValue % 2 != 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
				else $objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
				$objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getAlignment()->setTextRotation(90);
				$objExcelSheet->setCellValue($colPrefix.chr($asciiValue).'1', $resultHeader);
				$objExcelSheet->getColumnDimension($colPrefix.chr($asciiValue))->setWidth(4);
				$objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$asciiValue++;
			}
		}
		if ($asciiValue > 90) {$asciiValue = 65; $colPrefix = chr($colPrefixAscii); $colPrefixAscii++;}
		$objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getAlignment()->setTextRotation(90);
		if ($asciiValue % 2 != 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
		else $objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
		$objExcelSheet->setCellValue($colPrefix.chr($asciiValue).'1', 'Total Score');
		$objExcelSheet->getColumnDimension($colPrefix.chr($asciiValue))->setWidth(4);
		$objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$asciiValue++;

		if ($asciiValue > 90) {$asciiValue = 65; $colPrefix = chr($colPrefixAscii); $colPrefixAscii++;}
		if ($asciiValue % 2 != 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
		else $objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
		$objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getAlignment()->setTextRotation(90);
		$objExcelSheet->setCellValue($colPrefix.chr($asciiValue).'1', 'Final Rank');
		$objExcelSheet->getColumnDimension($colPrefix.chr($asciiValue))->setWidth(4);
		$objExcelSheet->getStyle($colPrefix.chr($asciiValue).'1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$asciiValue = 67;
		$rowCount = 2;
		$colPrefixAscii = 65;
		$colPrefix = '';
		
		// Primary Team Scores
		$tournamentResults = $_SESSION['tournamentResults'];
         if ($tournamentResults != null) {
			 foreach ($tournamentResults as $resultRow) {
				$colPrefixAscii = 65;
				$colPrefix = '';
				
				$objExcelSheet->setCellValue('A'.$rowCount, $resultRow['1'])->setCellValue('B'.$rowCount, $resultRow['2']);
				$objExcelSheet->getStyle('A'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('CCCCCC');
				$objExcelSheet->getStyle('B'.$rowCount)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
				if ($rowCount % 2 == 0) {
					$objExcelSheet->getStyle('A'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryColumnColor"]);
					$objExcelSheet->getStyle('B'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryRowColor"]);
				} else {
					$objExcelSheet->getStyle('A'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
					$objExcelSheet->getStyle('B'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
				}			
				$i = 3;
				while ($i < sizeof($resultRow)-1) {					
					if ($asciiValue > 90) {$asciiValue = 65; $colPrefix = chr($colPrefixAscii); $colPrefixAscii++;}
					$objExcelSheet->setCellValue($colPrefix.chr($asciiValue).$rowCount, $resultRow[$i]);
					if ($asciiValue % 2 != 0 && $rowCount % 2 == 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryColumnColor"]);
					else if ($asciiValue % 2 != 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
					else if ($rowCount % 2 == 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryRowColor"]);
					else $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
					if ($i == sizeof($resultRow) - 4)	
						$objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$i++;
					$asciiValue++;
				}
				$rowCount++;
				$asciiValue = 67;
		 	}
    	}
		
		$colPrefixAscii = 65;
		$colPrefix = '';
		// Alternate Team Scores
		$tournamentAlternateResults = $_SESSION['tournamentAlternateResults'];
         if ($tournamentAlternateResults != null) {			 
			 $rowCount++;
			 foreach ($tournamentAlternateResults as $resultRow) {
				$colPrefixAscii = 65;
				$colPrefix = '';
				
				$objExcelSheet->setCellValue('A'.$rowCount, $resultRow['1'])->setCellValue('B'.$rowCount, $resultRow['2']);
				$objExcelSheet->getStyle('A'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('CCCCCC');
				$objExcelSheet->getStyle('B'.$rowCount)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
				if ($rowCount % 2 == 0) {
					$objExcelSheet->getStyle('A'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryColumnColor"]);
					$objExcelSheet->getStyle('B'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryRowColor"]);
				} else {
					$objExcelSheet->getStyle('A'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
					$objExcelSheet->getStyle('B'.$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
				}			
				$i = 3;
				while ($i < sizeof($resultRow)-1) {
					if ($asciiValue > 90) {$asciiValue = 65; $colPrefix .= chr($colPrefixAscii); $colPrefixAscii++;}
					$objExcelSheet->setCellValue($colPrefix.chr($asciiValue).$rowCount, $resultRow[$i]);
					if ($asciiValue % 2 != 0 && $rowCount % 2 == 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryColumnColor"]);
					else if ($asciiValue % 2 != 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryColumnColor"]);
					else if ($rowCount % 2 == 0) $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["primaryRowColor"]);
					else $objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($_SESSION["secondaryRowColor"]);
					if ($i == sizeof($resultRow) - 4)	
						$objExcelSheet->getStyle($colPrefix.chr($asciiValue).$rowCount)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$i++;
					$asciiValue++;
				}
				$rowCount++;
				$asciiValue = 67;
		 	}
    	}
		$objExcelSheet->setCellValue('B'.($rowCount+2), '* = Trial Event');
		$objExcelSheet->setCellValue('B'.($rowCount+3), '+ = Alternate Team');

							 					 
		// Additional Sheet Attributes	
		$title = 'Results - ' . str_replace ("/"," ",$_SESSION["tournamentName"]) . ' ' . $_SESSION["tournamentDivision"];
		if (strlen($title) > 30) $title = substr($title, 0, 29); 
		$objExcelSheet->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}
	
	function reloadResults() {
			$rowCount = 2;
			$colWidth = 1;
		echo '<table id="primaryResultsGrid" class="table table-bordered table-hover tablesorter" style="table-layout:fixed;">';
        echo '<thead> ';
        echo '<tr> ';
		echo '<th '; echo 'width="5%" style="background-color: #'.$_SESSION["primaryColumnColor"].';border-bottom: 1px solid #000000;"'; echo 'class="rotate" ><div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#</span></div></th>';
		echo '<th '; echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';border-bottom: 1px solid #000000;"'; echo ' width="20%"><div><span>'; 
		echo $_SESSION["tournamentName"] .'<br />'; echo 'Division: '.$_SESSION["tournamentDivision"] . '<br />'; echo 'Date: '.$_SESSION["tournamentDate"] .' </span></div></th>';
				$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
				if ($tournamentResultsHeader != null) {
					foreach ($tournamentResultsHeader as $resultHeader) {
						$colWidth = sizeof($tournamentResultsHeader) + 2;
						$colWidth = 75 / $colWidth;
						
						echo '<th width="'.$colWidth.'%" style="border-bottom: 1px solid #000000;'; 
						if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].';';
						else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';
						echo '" class="rotate"><div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$resultHeader.'</span></div></th>';						
						$rowCount++;
					}
				}

                echo '<th width="'.$colWidth.'%" style="border-bottom: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Score</span></div></th>';
				$rowCount++;
			   echo '<th width="'.$colWidth.'%" style="border-bottom: 1px solid #000000; '; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Final Rank</span></div></th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

		 $rowCount = 0;
		 
		 $tournamentResults = $_SESSION['tournamentResults'];
         if ($tournamentResults != null) {
			 foreach ($tournamentResults as $resultRow) {
				$colCount = 0;
      			echo '<tr>'; //style="border-right: 1px solid #000000;
				echo '<td width="5%" '; 
					if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["secondaryColumnColor"].';"'; 
					else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["primaryColumnColor"].';"'; 
					else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'style="background-color: #'.$_SESSION["primaryRowColor"].';"';	
					else echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';"';
				echo '><b>'.$resultRow['1'].'</b></td>';
				$colCount++;
				echo '<td style="white-space: nowrap; overflow: hidden; border-right: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryRowColor"]; else echo ' background-color: #'.$_SESSION["secondaryRowColor"]; echo '"><b>'.$resultRow['2'].'</b></td>';
				$i = 3;
				$colCount++;
				while ($i < sizeof($resultRow)-1) {
					echo '<td width="'.$colWidth.'%" style="'; 
						if ($i == (sizeof($resultRow)-4)) echo 'border-right: 1px solid #000000; ';
						if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["secondaryColumnColor"].';'; 
						else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["primaryColumnColor"].';';	
						else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'background-color: #'.$_SESSION["primaryRowColor"].';';
						else echo 'background-color: #'.$_SESSION["secondaryRowColor"].';';
					echo '">'.$resultRow[$i].'</td>';
					$i++;
					$colCount++;
				}
				
				echo '</tr>';
			$rowCount++;
		 }
    	}

         echo '</tbody>';
         echo '</table>';
		  if ($_SESSION['tournamentAlternateResults'] != null) {
			  echo '';
		  $tournamentAlternateResults = $_SESSION['tournamentAlternateResults'];
          if ($tournamentAlternateResults != null) {
			 echo '<table class="table table-bordered table-hover" data-sortable data-sort-name="rank" data-sort-order="desc" style="table-layout:fixed;">';
			 foreach ($tournamentAlternateResults as $resultRow) {
				$colCount = 0;
      			echo '<tr>'; //style="border-right: 1px solid #000000;
				echo '<td width="5%" ';  
					if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["secondaryColumnColor"].';"'; 
					else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'style="background-color: #'.$_SESSION["primaryColumnColor"].';"'; 
					else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'style="background-color: #'.$_SESSION["primaryRowColor"].';"';	
					else echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';"';
				echo '><b>'.$resultRow['1'].'</b></td>';
				$colCount++;
				echo '<td width="20%" style="white-space: nowrap; overflow: hidden; border-right: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryRowColor"]; else echo ' background-color: #'.$_SESSION["secondaryRowColor"]; echo '"><b>'.$resultRow['2'].'</b></td>';
				$i = 3;
				$colCount++;
				while ($i < sizeof($resultRow)-1) {
					echo '<td width="'.$colWidth.'%" style="'; 
						if ($i == (sizeof($resultRow)-4)) echo 'border-right: 1px solid #000000; ';
						if ($rowCount % 2 == 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["secondaryColumnColor"].';'; 
						else if ($rowCount % 2 != 0 && $colCount % 2 == 0) echo 'background-color: #'.$_SESSION["primaryColumnColor"].';';	
						else if ($rowCount % 2 == 0 && $colCount % 2 != 0) echo 'background-color: #'.$_SESSION["primaryRowColor"].';';
						else echo 'background-color: #'.$_SESSION["secondaryRowColor"].';';
					echo '">'.$resultRow[$i].'</td>';
					$i++;
					$colCount++;
				}
				
				echo '</tr>';
			$rowCount++;
			}
			echo '</table>';
			}
		  }	
	}
	
	// SLIDESHOW RESULTS ----------------------------------------------------
	function loadSlideShow($id, $mysqli) { 
		$resultSlideshow = array();
		// Load Tournament info. 
			// Upload tournament logo?
			// 
		$tournamentDetails = $mysqli->query("SELECT T.NAME, T.LOCATION, T.EVENTS_AWARDED, T.OVERALL_AWARDED, T.BEST_NEW_TEAM_FLAG, T.MOST_IMPROVED_FLAG FROM TOURNAMENT T WHERE T.TOURNAMENT_ID=".$_SESSION["tournamentId"]);
		$tournamentRow = $tournamentDetails->fetch_row();
		
		// Placeholder Slide
		$slide = new slideshowSlide();
		$slide->setType('PLACEHOLDER');
		$slide->setHeaderText($tournamentRow[0]); // Tournament Name
		$slide->setText('at '.$tournamentRow[1]); // Tournament Location
		$slide->setLogoPath('img/misologo.png');
		array_push($resultSlideshow, $slide);
		
		// Build interface to add these general slides.
		$slide = new slideshowSlide();
		//$slide->setType('GENERAL');
		//$slide->setHeaderText('2016 Michigan State Tournament');
		//$slide->setText('Random Text that identifies this as the 2nd slide!!!');
		//array_push($resultSlideshow, $slide);
		
		$aId = null; $aEvents = array(); $aHighLowFlag = 0; $aOverallAwarded = 0;
		$bId = null; $bEvents = array(); $bHighLowFlag = 0; $bOverallAwarded = 0;
		$cId = null; $cEvents = array(); $cHighLowFlag = 0; $cOverallAwarded = 0;
		
		$query = "select T1.TOURNAMENT_ID, T1.DIVISION, T1.LINKED_TOURN_1, T2.DIVISION, T1.LINKED_TOURN_2, T3.DIVISION FROM TOURNAMENT T1 
				LEFT JOIN TOURNAMENT T2 on T2.TOURNAMENT_ID=T1.LINKED_TOURN_1
				LEFT JOIN TOURNAMENT T3 on T3.TOURNAMENT_ID=T1.LINKED_TOURN_2
				WHERE T1.TOURNAMENT_ID=".$_SESSION["tournamentId"];
		$tournaments = $mysqli->query($query);
		$row = $tournaments->fetch_row();
		if ($row[1] != null AND $row[1] == 'A') $aId = $row[0]; else if ($row[1] != null AND $row[1] == 'B') $bId = $row[0]; else if ($row[1] != null AND $row[1] == 'C') $cId = $row[0];
		if ($row[3] != null AND $row[3] == 'A') $aId = $row[2]; else if ($row[3] != null AND $row[3] == 'B') $bId = $row[2]; else if ($row[3] != null AND $row[3] == 'C') $cId = $row[2];
		if ($row[5] != null AND $row[5] == 'A') $aId = $row[4]; else if ($row[5] != null AND $row[5] == 'B') $bId = $row[4]; else if ($row[5] != null AND $row[5] == 'C') $cId = $row[4];
		
		
		if ($cId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND SCORE <= T.EVENTS_AWARDED AND SCORE > 0
					LEFT JOIN TOURNAMENT_TEAM TT1 on TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$cId." GROUP BY NAME,TEAM,SCORE ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
			$results = $mysqli->query($query);
			$event = null;
			$eventArray = array();
			$count = 0;
			while ($row = $results->fetch_array()) {
				$cHighLowFlag = $row[3];
				$cOverallAwarded = $row[4];
				if ($event != $row[0] and sizeof($eventArray) > 0) {
					array_unshift($eventArray, $event);
					array_push($cEvents, $eventArray);
					$eventArray = array();
					$count = 0;
				}
				$event = $row[0];
				array_push($eventArray, $row[1]);			
				$count++;
			}
			if (sizeof($eventArray) > 0) {array_unshift($eventArray, $event); array_push($cEvents, $eventArray); }
		}
		
		if ($bId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED  from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND SCORE <= T.EVENTS_AWARDED AND SCORE > 0
					LEFT JOIN TOURNAMENT_TEAM TT1 on TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$bId." GROUP BY NAME,TEAM,SCORE ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
			$results = $mysqli->query($query);
			$event = null;
			$eventArray = array();
			$count = 0;
			while ($row = $results->fetch_array()) {	
				$bHighLowFlag = $row[3];
				$bOverallAwarded = $row[4];
				if ($event != $row[0] and sizeof($eventArray) > 0) {
					array_unshift($eventArray, $event);
					array_push($bEvents, $eventArray);
					$eventArray = array();
					$count = 0;
				}
				$event = $row[0];
				array_push($eventArray, $row[1]);			
				$count++;
			}
			if (sizeof($eventArray) > 0) {array_unshift($eventArray, $event); array_push($bEvents, $eventArray); }
		}
		
		if ($aId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED  from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND SCORE <= T.EVENTS_AWARDED  AND SCORE > 0
					LEFT JOIN TOURNAMENT_TEAM TT1 on TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$aId." GROUP BY NAME,TEAM,SCORE ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
			$results = $mysqli->query($query);
			$event = null;
			$eventArray = array();
			$count = 0;
			while ($row = $results->fetch_array()) {
				$aHighLowFlag = $row[3];				
				$aOverallAwarded = $row[4];
				if ($event != $row[0] and sizeof($eventArray) > 0) {
					array_unshift($eventArray, $event);
					array_push($aEvents, $eventArray);
					$eventArray = array();
					$count = 0;
				}
				$event = $row[0];
				array_push($eventArray, $row[1]);			
				$count++;
			}
			if (sizeof($eventArray) > 0) {array_unshift($eventArray, $event); array_push($aEvents, $eventArray); }
		}
		
		for ($i=0; $i < 100; $i++) {
			if ($i >= sizeof($cEvents) AND $i >= sizeof($bEvents) AND $i >= sizeof($aEvents)) break;
				if ($i < sizeof($aEvents)) {
					$event = $aEvents[$i];
					$slide = new slideshowSlide();
					$slide->setType('EVENTSCORE');		
					$slide->setHeaderText($event[0]);
					$teamList = array();
					$count2 = 1;
					while ($count2 <  sizeof($event)) {
						if ($event[$count2] != null) array_push($teamList, $count2.'. '.$event[$count2]);
					$count2++;
					}
					$slide->setTeamNames($teamList);
					array_push($resultSlideshow, $slide);
				}
				if ($i < sizeof($bEvents)) {
					$event = $bEvents[$i];
					$slide = new slideshowSlide();
					$slide->setType('EVENTSCORE');		
					$slide->setHeaderText($event[0]);
					$teamList = array();
					$count2 = 1;
					while ($count2 <  sizeof($event)) {
						if ($event[$count2] != null) array_push($teamList, $count2.'. '.$event[$count2]);
					$count2++;
					}
					$slide->setTeamNames($teamList);
					array_push($resultSlideshow, $slide);
				}
				if ($i < sizeof($cEvents)) {
					$event = $cEvents[$i];
					$slide = new slideshowSlide();
					$slide->setType('EVENTSCORE');		
					$slide->setHeaderText($event[0]);
					$teamList = array();
					$count2 = 1;
					while ($count2 <  sizeof($event)) {
						if ($event[$count2] != null) array_push($teamList, $count2.'. '.$event[$count2]);
					$count2++;
					}
					$slide->setTeamNames($teamList);
					array_push($resultSlideshow, $slide);
				}
		}
		
		// Placeholder Slide
		$slide = new slideshowSlide();
		$slide->setType('PLACEHOLDER');
		$slide->setHeaderText($tournamentRow[0]); // Tournament Name
		$slide->setText('at '.$tournamentRow[1]); // Tournament Location
		$slide->setLogoPath('img/misologo.png');
		array_push($resultSlideshow, $slide);
		
		// Best New Team selected
			$slide = new slideshowSlide();
			$slide->setType('OVERALLRESULTS');
			$slide->setHeaderText('Best New Team');
			$labelValues = array();
			$query = "SELECT T.NAME, T.DIVISION FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID INNER JOIN TOURNAMENT TN ON TN.TOURNAMENT_ID=TT.TOURNAMENT_ID WHERE TN.BEST_NEW_TEAM_FLAG=1 AND TT.TOURNAMENT_ID IN (-1";
			if ($aId != null and $aId != '') $query .= ",".$aId;
			if ($bId != null and $bId != '') $query .= ",".$bId;
			if ($cId != null and $cId != '') $query .= ",".$cId;
			$query .= ") AND TT.BEST_NEW_TEAM_FLAG=1 ORDER BY T.DIVISION ASC ";
			$results = $mysqli->query($query);
			while ($row = $results->fetch_array()) {	
				array_push($labelValues, array("Division ".$row[1], $row[0]));
			}
			$slide->setLabelValues($labelValues);
			
			if (sizeof($labelValues) > 0) {
				array_push($resultSlideshow, $slide);
			}
			
		// Most Improved Team selected
			$slide = new slideshowSlide();
			$slide->setType('OVERALLRESULTS');
			$slide->setHeaderText('Most Improved Team');
			$labelValues = array();
			$query = "SELECT T.NAME, T.DIVISION FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID INNER JOIN TOURNAMENT TN ON TN.TOURNAMENT_ID=TT.TOURNAMENT_ID WHERE TN.MOST_IMPROVED_FLAG=1 AND TT.TOURNAMENT_ID IN (-1";
			if ($aId != null and $aId != '') $query .= ",".$aId;
			if ($bId != null and $bId != '') $query .= ",".$bId;
			if ($cId != null and $cId != '') $query .= ",".$cId;
			$query .= ") AND TT.MOST_IMPROVED_TEAM_FLAG=1 ORDER BY T.DIVISION ASC ";
			$results = $mysqli->query($query);
			while ($row = $results->fetch_array()) {	
				array_push($labelValues, array("Division ".$row[1], $row[0]));
			}
			$slide->setLabelValues($labelValues);
			if (sizeof($labelValues) > 0) {
				array_push($resultSlideshow, $slide);
			}
		
		if ($tournamentRow[4] == 1 OR $tournamentRow[5] == 1) {
			// Placeholder Slide
			$slide = new slideshowSlide();
			$slide->setType('PLACEHOLDER');
			$slide->setHeaderText($tournamentRow[0]); // Tournament Name
			$slide->setText('at '.$tournamentRow[1]); // Tournament Location
			$slide->setLogoPath('img/misologo.png');
			array_push($resultSlideshow, $slide);
		}
		$tournamentResultsA = array(); $tournamentResultsB = array(); $tournamentResultsC = array();
		$tournamentResultsA = getPrimaryTournamentResults($aId,$mysqli,$aHighLowFlag,$tournamentResultsA);
		$tournamentResultsB = getPrimaryTournamentResults($bId,$mysqli,$bHighLowFlag,$tournamentResultsB);
		$tournamentResultsC = getPrimaryTournamentResults($cId,$mysqli,$cHighLowFlag,$tournamentResultsC);
		
		// Overall Results
		$maxRank = $cOverallAwarded;
		if ($aOverallAwarded > $maxRank) $maxRank = $aOverallAwarded;
		if ($bOverallAwarded > $maxRank) $maxRank = $bOverallAwarded;
		
		for ($i = $maxRank; $i > 0; $i--) {	
			$slide = new slideshowSlide();
			$slide->setType('OVERALLRESULTS');
			$ordinalSuffix = array('th','st','nd','rd','th','th','th','th','th','th');
			$suffix = 'th';
			if (($i % 100) >= 11 && ($i % 100) <= 13) $suffix = 'th';
			else $suffix = $ordinalSuffix[$i % 10];
			$slide->setHeaderText('Final Results - '.$i.$suffix.' Place');
			$labelValues = array();
		
			if (sizeof($tournamentResultsA) >= $i AND $aOverallAwarded >= $i) {
				$row = $tournamentResultsA[$i-1];
				array_push($labelValues, array("Division A", $row[2]));
			}
			if (sizeof($tournamentResultsB) >= $i AND $bOverallAwarded >= $i) {
				$row = $tournamentResultsB[$i-1];
				array_push($labelValues, array("Division B", $row[2]));
			}
			if (sizeof($tournamentResultsC) >= $i AND $cOverallAwarded >= $i) {
				$row = $tournamentResultsC[$i-1];
				array_push($labelValues, array("Division C", $row[2]));
			}
			$slide->setLabelValues($labelValues);
			array_push($resultSlideshow, $slide);
		}
		
		// Placeholder Slide
		$slide = new slideshowSlide();
		$slide->setType('PLACEHOLDER');
		$slide->setHeaderText($tournamentRow[0]); // Tournament Name
		$slide->setText('at '.$tournamentRow[1]); // Tournament Location
		$slide->setLogoPath('img/misologo.png');
		array_push($resultSlideshow, $slide);
		
		$_SESSION["resultSlideshowIndex"] = 0;
		$_SESSION["resultSlideshow"] = json_encode($resultSlideshow);
		$_SESSION["resultSlideshowPDF"] = $resultSlideshow;
	}
	
	function clearSlideshow() {
		$_SESSION["resultSlideshow"] = null;
	}	
	
	function exportSlideShowPDF($mysqli) {
		$resultSlideshow = $_SESSION["resultSlideshowPDF"]; //json_decode($_SESSION["resultSlideshow"], FALSE);
		
		require('libs/fpdf/fpdf.php');
		$pdf = new FPDF();
		$pdf->SetTitle('Tournament Results Slideshow', true);
		
		foreach ($resultSlideshow as $slide) {
			// General Slide Setup
			$pdf->AddPage('L','Letter', 0);
			$pdf->SetAutoPageBreak(True, 2);
			// Logic For Each Type of Slide
			if ($slide->getType() == 'PLACEHOLDER') {
				$pdf->SetFont('Arial','I',16);
				$pdf->Cell(0,10,'Science Olympiad',0,0,'C');
				$pdf->Ln(16);
				$pdf->SetFont('Arial','B',48);
				$pdf->MultiCell(0,16,$slide->getHeaderText(),0,'C',false);
				$pdf->Ln(16);
				$len = $pdf->GetPageWidth();
				$len = ($len / 2) - 50;
				$pdf->SetX($len);
				$pdf->Image($slide->getLogoPath(),null,null,100,100);
				$pdf->Ln(16);
				$pdf->SetFont('Arial','BI',20);
				$pdf->Cell(0,10,$slide->getText(),0,0,'C');
				
			}
			else if ($slide->getType() == 'GENERAL') {
				$pdf->SetFont('Arial','I',16);
				$pdf->Cell(0,10,'Science Olympiad',0,0,'C');
				$pdf->Ln(16);
				$pdf->SetFont('Arial','B',48);
				$pdf->MultiCell(0,16,$slide->getHeaderText(),0,'C',false);
				$pdf->Ln(16);
			
				$pdf->SetFont('Arial','B',36);
				$pdf->MultiCell(0,16,$slide->getText(),0,'L',false);
			}
			else if ($slide->getType() == 'EVENTSCORE') {		
				$pdf->SetFont('Arial','B',48);
				$pdf->MultiCell(0,16,$slide->getHeaderText(),0,'C',false);
				$pdf->Ln(16);
				foreach ($slide->getTeamNames() as $team) {
					$pdf->SetFont('Arial','',36);
					$pdf->Cell(0,10, $team,0,0,'L');
					$pdf->Ln(18);
				}
				if (sizeof($slide->getTeamNames()) == 0) {
					$pdf->SetFont('Arial','',36);
					$pdf->Cell(0,10, 'No Results Available',0,0,'L');
					$pdf->Ln(18);
				}
			}
			else if ($slide->getType() == 'OVERALLRESULTS') {
				$pdf->SetFont('Arial','B',48);
				$pdf->MultiCell(0,16,$slide->getHeaderText(),0,'C',false);
				$pdf->Ln(16);
				foreach ($slide->getLabelValues() as $labels) {
					$pdf->SetFont('Arial','',26);	
					$pdf->Cell(0,10, $labels[0],0,0,'L');
					$pdf->Ln(18);
					
					$pdf->SetFont('Arial','',36);	
					$pdf->Cell(0,10, $labels[1],0,0,'L');
					$pdf->Ln(18);
					$pdf->Ln(10);
				}
			}
			
			
		}
		
		$pdf->Output('D', 'slideshow.pdf',true);
	
	
	}
	
	// USER MANAGEMENT ------------------------------------------------
	function loadAllUsers($mysqli) {
			$userList = array();
			$query = "Select * from USER WHERE 1=1 ";
			if ($_SESSION["userFirstName"] != null) {
				$query = $query . " AND FIRST_NAME LIKE '".$_SESSION["userFirstName"]."%' " ;
			}
			if ($_SESSION["userLastName"] != null) {
				$query = $query . " AND LAST_NAME LIKE '".$_SESSION["userLastName"]."%' " ;
			}
			if ($_SESSION["userRole"] != null) {
				$query = $query . " AND ROLE_CODE = '".$_SESSION["userRole"]."' " ;
			}
			$query = $query . " ORDER BY UPPER(LAST_NAME) ASC ";
			if ($_SESSION["userFilterNumber"] != null and $_SESSION["userFilterNumber"] != '0') {
				$query = $query . " LIMIT ".$_SESSION["userFilterNumber"];
			}
			
			$result = $mysqli->query($query); 
 			if ($result) {
				while($userRow = $result->fetch_array()) {
 					$userRecord = array();	
					array_push($userRecord, $userRow['0']);
					array_push($userRecord, $userRow['4']);
					array_push($userRecord, $userRow['5']);
					array_push($userRecord, $userRow['1']);
					array_push($userRecord, $userRow['3']);
 				
					array_push($userList, $userRecord);
				}
			}
		$_SESSION["resultsPage"] = 1;
		$_SESSION["userList"] = $userList;
	
	}
	
	function clearUser() {
		$_SESSION["userId"] = null;
		$_SESSION["userName"] = null;
		$_SESSION["userFirstLastName"] = null;
		$_SESSION["userRoleCode"] = null;
		$_SESSION["userActiveFlag"] = null;
		$_SESSION["userPhoneNumber"] = null;
	}


	function loadUser($id, $mysqli) {
		$result = $mysqli->query("SELECT USER_ID, USERNAME, CONCAT(FIRST_NAME,' ', LAST_NAME) as name, ROLE_CODE, ACCOUNT_ACTIVE_FLAG, PHONE_NUMBER, FIRST_NAME, LAST_NAME
							  FROM USER WHERE USER_ID = " .$id); 
 			if ($result) {
 				$userRow = $result->fetch_row();	
 				$_SESSION["userId"] = $userRow['0'];
 				$_SESSION["userName"] = $userRow['1'];
 				$_SESSION["userFirstLastName"] = $userRow['2'];
 				$_SESSION["userRoleCode"] = $userRow['3'];
 				$_SESSION["userActiveFlag"] = $userRow['4'];
 				$_SESSION["userPhoneNumber"] = $userRow['5'];
 				$_SESSION["firstName"] = $userRow['6'];
				$_SESSION["lastName"] = $userRow['7'];				
    		}
	}
	
	function saveUser($mysqli) {
		$query = $mysqli->prepare("UPDATE USER SET ROLE_CODE=?, ACCOUNT_ACTIVE_FLAG=? WHERE USER_ID=".$_SESSION["userId"]);
			
		$query->bind_param('si',$_GET["userRoleCode"], $_GET["userActiveFlag"]);
		$query->execute();
		$query->free_result();
		
		// save Confirmation
		$_SESSION['savesuccessUser'] = "1";	
	}
	
	// UTILITIES MANAGEMENT --------------------------------------------------
	function clearUtilities() {
		$_SESSION["registerCodeSupervisor"] = "";
		$_SESSION["registerCodeVerifier"] = "";
		$_SESSION["registerCodeAdmin"] = "";
		$_SESSION["resetPassword"] = "";
		
		$_SESSION["emailHost"] = "";
		$_SESSION["emailPort"] = "";
		$_SESSION["emailUsername"] = "";
		$_SESSION["emailPassword"] = "";
		$_SESSION["smtpSecure"] = "";
		
		$_SESSION["accountCreationEmail"] = "";
		$_SESSION["passwordResetMessage"] = "";
		
	}
	
	function loadUtilities($mysqli) {		
		$result = $mysqli->query("SELECT DOMAIN_CODE,REF_DATA_CODE,DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE IN ('REGISTRATIONCODE', 'MAILSERVER','PASSWORDRESET','EMAILMESSAGE') ORDER BY DOMAIN_CODE ASC"); 
 		if ($result) {
			while($utilityRow = $result->fetch_array()) {
				if ($utilityRow != null) {
					if ($utilityRow['0'] == 'REGISTRATIONCODE' and $utilityRow['1'] == 'SUPERVISOR') $_SESSION["registerCodeSupervisor"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'REGISTRATIONCODE' and $utilityRow['1'] == 'VERIFIER') $_SESSION["registerCodeVerifier"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'REGISTRATIONCODE' and $utilityRow['1'] == 'ADMIN') $_SESSION["registerCodeAdmin"] = $utilityRow['2'];	
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'HOST') $_SESSION["emailHost"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'PORT') $_SESSION["emailPort"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'USERNAME') $_SESSION["emailUsername"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'PASSWORD') $_SESSION["emailPassword"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'MAILSERVER' and $utilityRow['1'] == 'SMTPSECURE') $_SESSION["smtpSecure"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'PASSWORDRESET' and $utilityRow['1'] == 'SALT') $_SESSION["resetPassword"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'EMAILMESSAGE' and $utilityRow['1'] == 'ACCOUNTCREATE') $_SESSION["accountCreationEmail"] = $utilityRow['2'];
					else if ($utilityRow['0'] == 'EMAILMESSAGE' and $utilityRow['1'] == 'PASSWORDRESET') $_SESSION["passwordResetMessage"] = $utilityRow['2'];
				}
			}
		}		
	}
	
	function saveUtilities($mysqli) {
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='REGISTRATIONCODE' AND REF_DATA_CODE='SUPERVISOR' ");			
		$query->bind_param('s',$_GET["registerCodeSupervisor"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='REGISTRATIONCODE' AND REF_DATA_CODE='VERIFIER' ");			
		$query->bind_param('s',$_GET["registerCodeVerifier"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='REGISTRATIONCODE' AND REF_DATA_CODE='ADMIN' ");			
		$query->bind_param('s',$_GET["registerCodeAdmin"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='PASSWORDRESET' AND REF_DATA_CODE='SALT' ");			
		$query->bind_param('s',$_GET["resetPassword"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='MAILSERVER' AND REF_DATA_CODE='HOST' ");			
		$query->bind_param('s',$_GET["emailHost"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='MAILSERVER' AND REF_DATA_CODE='PORT' ");			
		$query->bind_param('s',$_GET["emailPort"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='MAILSERVER' AND REF_DATA_CODE='USERNAME' ");			
		$query->bind_param('s',$_GET["emailUsername"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='MAILSERVER' AND REF_DATA_CODE='PASSWORD' ");			
		$query->bind_param('s',$_GET["emailPassword"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='MAILSERVER' AND REF_DATA_CODE='SMTPSECURE' ");			
		$query->bind_param('s',$_GET["smtpSecure"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='EMAILMESSAGE' AND REF_DATA_CODE='ACCOUNTCREATE' ");			
		$query->bind_param('s',$_GET["accountCreationEmail"]); $query->execute();$query->free_result();
		
		$query = $mysqli->prepare("UPDATE REF_DATA SET DISPLAY_TEXT=? WHERE DOMAIN_CODE='EMAILMESSAGE' AND REF_DATA_CODE='PASSWORDRESET' ");			
		$query->bind_param('s',$_GET["passwordResetMessage"]); $query->execute();$query->free_result();
		
		// save Confirmation
		$_SESSION['savesuccessUtilities'] = "1";	
	}
	
	
	
	// LOGIN AND ACCOUNT MANAGEMENT ---------------------------------------
	function login($mysqli) {
		$myusername=$_POST['userName']; 
		$mypassword=$_POST['password']; 
		
		$query = $mysqli->prepare("SELECT * FROM USER WHERE UPPER(USERNAME)=? AND ACCOUNT_ACTIVE_FLAG=1 ");
		if ($_SESSION["accountMode"] == 'update') {
			$query = $mysqli->prepare("SELECT * FROM USER WHERE UPPER(USERNAME)=? ");
		}	
		if ($_SESSION["accountMode"] == 'update') $query->bind_param('s',strtoupper($myusername));
		else $query->bind_param('s',strtoupper($myusername));
			
		$query->execute();
		$result = $query->get_result();
		$count = $result->num_rows;
		$account = $result->fetch_row();
		$query->free_result();

		
		
		
		if($_SESSION["accountMode"] == 'update' or $account['2'] === crypt($mypassword, $account['2'])) {
			$_SESSION["accountMode"] == '';
				
			$userSessionInfo = new UserSessionInfo($account['1']);
			$userSessionInfo->setAuthenticatedFlag(1);
			$userSessionInfo->setUserId($account['0']);
			$userSessionInfo->setFirstName($account['4']);
			$userSessionInfo->setLastName($account['5']);
			$userSessionInfo->setRole($account['3']);
			$userSessionInfo->setPhoneNumber($account['8']);
			
			$_SESSION["userSessionInfo"] = serialize($userSessionInfo);
			//$_SESSION["userEventDate"] = date("m/d/y");
			$_SESSION['sessionTimeout'] = time(); // Session Timeout
			
			// Log Login Success
			$query = $mysqli->prepare("INSERT INTO USER_LOGIN_LOG (USER_ID, LOGIN_TIME) VALUES (".$account['0'].",NOW())");			
			$query->execute();$query->free_result();
			
			
			return true;
		}
				
		// Throw Error Message
		$_SESSION["accountMode"] == '';
		$_SESSION["loginError1"] = "1";
		return false;
	}
	
	function forgotPassword($mysqli) {
		if ($_POST['userName'] == null or $_POST['userName'] == '') {
			$_SESSION["resetPasswordError"] = "1";
			return;
		}
		$query = $mysqli->prepare("SELECT * FROM USER WHERE UPPER(USERNAME)=?");
		$query->bind_param('s',strtoupper($_POST['userName']));
		$query->execute();
		$result = $query->get_result();
		$count = $result->num_rows;
		$resultRow = $result->fetch_row();
		$name = $resultRow['4'] . ' ' . $resultRow['5'];
		$userId = $resultRow['0'];
		$query->free_result();
		
		if($count == 1) {
			$query = $mysqli->prepare("SELECT DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE='PASSWORDRESET' AND REF_DATA_CODE='SALT' ");
			$query->execute();
			$result = $query->get_result();
			$query->free_result();
			$resetPassword = $result->fetch_row();
			
			$salt = crypt(uniqid());
			$encryptedPassword = crypt($resetPassword['0'], $salt);
			
			$query = $mysqli->prepare("UPDATE USER SET PASSWORD_RESET_SALT=? WHERE USERNAME=?");
			$query->bind_param('ss',$salt, $_POST['userName']);
			$query->execute();
			$query->free_result();			
			
			emailPasswordReset($mysqli, $_POST['userName'], $name, $userId, $encryptedPassword, $salt);
			$_SESSION["resetPasswordSuccess"] = "1";
		}
		else {
			$_SESSION["resetPasswordError"] = "2";
		}
	}
	
	function checkPasswordReset($mysqli) {
		$userId = $_GET['id'];
		$encryptedPassword = $_GET['ep'];
		
		$query = $mysqli->prepare("SELECT PASSWORD_RESET_SALT FROM USER WHERE USER_ID=".$userId);
		$query->execute();
		$result = $query->get_result();
		$query->free_result();
		$passwordSalt = $result->fetch_row();
		
		
		$query = $mysqli->prepare("SELECT DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE='PASSWORDRESET' AND REF_DATA_CODE='SALT' ");
		$query->execute();
		$result = $query->get_result();
		$query->free_result();
		$resetPassword = $result->fetch_row();
		
		if ($encryptedPassword === crypt($resetPassword['0'], $passwordSalt['0'])) {
			$query = $mysqli->prepare("UPDATE USER SET PASSWORD_RESET_SALT=null WHERE USER_ID=?");
			$query->bind_param('s',$userId);
			$query->execute();
			$query->free_result();
			
			// Load User Info
			$_SESSION["accountMode"] = 'update';
			loadUser($userId, $mysqli);
			return true;
		}
	
		return false;
	}
	
	function clearAccount() {	
		$_SESSION["userName"] = '';
		$_SESSION["password"] = '';
		$_SESSION["firstName"] = '';
		$_SESSION["lastName"] = '';
		$_SESSION["vPassword"] = '';
	}
	
	function loadAccount() {
		$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
		if ($userSessionInfo != null) {
			$_SESSION["userName"] = $userSessionInfo->getUserName();
			$_SESSION["firstName"] = $userSessionInfo->getFirstName();
			$_SESSION["lastName"] = $userSessionInfo->getLastName();
			$_SESSION["userPhoneNumber"] = $userSessionInfo->getPhoneNumber();
		}
	}
	
	function manageAccount($mysqli, $mode) {
		$userName = $_POST['userName']; 
		$password = $_POST['password']; 
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$regCode = $_POST['regCode'];
		$phoneNumber = $_POST['userPhoneNumber'];
		
		$_SESSION["userName"] = $userName;
		$_SESSION["password"] = $password;
		$_SESSION["firstName"] = $firstName;
		$_SESSION["lastName"] = $lastName;
		$_SESSION["vPassword"] = $password;
		$_SESSION["userPhoneNumber"] = $phoneNumber;
		
		// Encrypt Password
		$encryptedPassword = crypt($password);
		
		// check email / username not already registered
		if ($mode == 'create') {
			$query = $mysqli->prepare("SELECT * FROM USER WHERE USERNAME=? ");
			
			$query->bind_param('s',$userName);
			$query->execute();
			$result = $query->get_result();
			$count = $result->num_rows;
			$query->free_result();
		
			if($count  > 0){
				$_SESSION["createAccountError"] = "error1";
				return false;
			}
			// validate registration code / Get Role
			$role = 'SUPERVISOR';
			$query = $mysqli->prepare("SELECT REF_DATA_CODE FROM REF_DATA WHERE DOMAIN_CODE='REGISTRATIONCODE' AND DISPLAY_TEXT = ? ");
			$query->bind_param('s',$regCode);
			$query->execute();
			$result = $query->get_result();
			$roleRow = $result->fetch_row(); 
			$query->free_result();
			if ($roleRow == null || $roleRow['0'] == null || $roleRow['0'] == '') {
				$_SESSION["createAccountError"] = "error2";
				return false;
			}
			else {
				$role = $roleRow['0'];
			}
			
			// save account info
			$result = $mysqli->query("select max(USER_ID) + 1 from USER");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null) $id = $row['0'];  
			
			$query = $mysqli->prepare("INSERT INTO USER (USER_ID, USERNAME, PASSWORD, ROLE_CODE, FIRST_NAME, LAST_NAME, ACCOUNT_ACTIVE_FLAG, PHONE_NUMBER) 
				VALUES (".$id.",?,?,?,?,?,?,?) ");

			$activeFlag = 1;	
			$query->bind_param('sssssis',$userName, $encryptedPassword, $role,$firstName, $lastName, $activeFlag, $phoneNumber);		
			$query->execute();
			$query->free_result();	
			$_SESSION["accountCreationSuccess"] = "1";
	
			// Send Creation Email
			sendAccountCreationEmail($mysqli, $userName, $firstName, $lastName, $password);
		}
		
		else {
			$userId = "";
			$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
			if ($userSessionInfo != null)
				$userId = $userSessionInfo->getUserId();
			else $userId = $_SESSION["userId"];
			
			
			$query = $mysqli->prepare("SELECT * FROM USER WHERE USERNAME=? AND USER_ID<>?");
			
			$query->bind_param('si',$userName, $userId);
			$query->execute();
			$result = $query->get_result();
			$count = $result->num_rows;
			$query->free_result();
		
			if($count  > 0){
				$_SESSION["createAccountError"] = "error1";
				return false;
			}
			
			$sql = "UPDATE USER SET USERNAME=?, PASSWORD=?, FIRST_NAME=?, LAST_NAME=?,PHONE_NUMBER=? WHERE USER_ID=? ";
			if ($password == null or $password == '') $sql = "UPDATE USER SET USERNAME=?, FIRST_NAME=?, LAST_NAME=?,PHONE_NUMBER=? WHERE USER_ID=? ";
			$query = $mysqli->prepare($sql);
			if ($password == null or $password == '') $query->bind_param('ssssi',$userName, $firstName,$lastName,$phoneNumber, $userId);		
			else  $query->bind_param('sssssi',$userName, $encryptedPassword, $firstName,$lastName,$phoneNumber, $userId);		
			$query->execute();
			$query->free_result();	
		
			$_SESSION["accountUpdateSuccess"] = "1";
		}
		
		return true;
	}
	
	function loadDefaultSettings() {
		$_SESSION["primaryRowColor"] = "FFFFFF"; // Default Green. Primary Row D1ECD1
		$_SESSION["primaryColumnColor"] = "FFFFFF"; // Default Gray. Primary Column D1D1D1
		$_SESSION["secondaryRowColor"] = "FFFFFF"; // Default White. Secondary Row FFFFFF
		$_SESSION["secondaryColumnColor"] = "FFFFFF"; // Default Green/Gray. Secondary Column CEDCCE
	}


	/**** TODO / GENERAL ISSUES ********
	
	-- ISSUES TO IMPLEMENT / FIX --
	
	-- CRITICAL
	
	-- HIGH
	** load highest points per event when supervisor logs in
	** Page Iterator Fix on Tournaments, Add to Home Page
	
	-- MEDIUM
	** Log More User data on login
	** Make Logo Dynamic
	** Make Footer Text Dynamic
	** Make Home Page News Text Dynamic
	** Make Any Place where 'Science Olympiad' Text Referenced Dynamic
	** Make Quick Links Dynamic
	** Work on About Page
	** Make General Slide Builder for Slideshow
	** Tie Scores display tied position in Raw Score Field
	
	** Filter supervisor drop down in edit tournament
	** Filter Verifier drop down in edit tournament
	** Make Offline Event Score screen. Export/Import Function for offline users
	** Delete user function
	** 
	

	-- LOW
	** Manual Reminder email to supervisor
	** Results Order By OPTION / Keep Color PAttern per Row
	** Generate Results as XML
	** AJAX on TIMEOUT	
	** controller class security
	** declare constants on login (to avoid notices if server has them turned on)
	** Print Broke Again?
	** Implement offline Event Score Version. Exportable to file, and importable in online version
	
	
	-- APP LIMITATIONS --
	** Handles 100 Teams / Events Per TOURNAMENT
	** Results: Ties Broken to 20 positions

	
	-- ACKNOWLEDGEMENTS --
	^^ PHPMAILER
	^^ PHPEXCEL
	^^ JQUERY Table Sorter
	^^ Spectrum Color Picker
	^^ FPDF
		
	
	**** TODO / GENERAL ISSUES *********/
?>