<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2019  Preston Frazier
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 *    
 * @package: Tournament Score Center (TSC) - Tournament scoring web application.
 * @version: 1.19.1, 01.13.2019
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
	
session_start();
include_once('score_center_objects.php');
include_once('role_check.php');

include_once('functions/mail_functions.php');
include_once('functions/constants.php');
include_once('functions/global_functions.php');
include_once('functions/self_schedule_functions.php');
include_once('functions/report_functions.php');

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

// Begin MAIN METHOD -------------------------->	
if (isset($_POST['login'])) {		
	if (login($mysqli)) {
		// initialize Session Settings
		init($mysqli);
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
	initNoSession($mysqli);
	clearAccount();
	$_SESSION["accountMode"] = 'create';
	header("Location: account.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'createNewAccount') {
	if (manageAccount($mysqli,'create')) {
		$navigationHandler = unserialize($_SESSION["navigationHandler"]);
		// Return to Calling Screen if exists
		if ($navigationHandler AND $navigationHandler->command != '') {
			processAccountNavigation($navigationHandler);
			clearAccount();
			$_SESSION["navigationHandler"] = null;
			header("Location: ".$navigationHandler->fromPath);
			exit();
		}
		// Standard Account Path
		else {
			login($mysqli);
			init($mysqli);
			header("Location: index.php");
			exit();
		}
	}	
	header("Location: account.php");	
	exit();
}
else if (isset($_POST['cancelAccount']) OR ($_GET['command'] != null AND $_GET['command'] == 'cancelAccount')) {
		$_SESSION["accountMode"] = null;
		$navigationHandler = unserialize($_SESSION["navigationHandler"]);
		// Return to Calling Screen if exists
		if ($navigationHandler AND $navigationHandler->fromPath != '') {
			clearAccount();
			header("Location: ".$navigationHandler->fromPath);
			exit();	
		}
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

// if user not logged in. forward to login.php - Application should exit if usersession is empty
$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
if (!$userSessionInfo) {
	$_SESSION['errorSessionTimeout'] = '1';
	header("location: logon.php");
	exit();
}

if ($_GET['command'] != null and ($_GET['command'] == 'loadIndex' or $_GET['command'] =='loadIndexLogin')) {
	header("Location: index.php");	
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'CHANGEUSERROLE') {
	// update current role
	changeUserRole($_GET['userRole']);
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
else if (isset($_GET['editEvent']) or isset($_GET['viewEvent']) ) {
	clearEvent();	
	if (isset($_GET['viewEvent'])) { loadEvent($_GET['viewEvent'], $mysqli); $_SESSION["disableRecord"] = 1; }
	else { loadEvent($_GET['editEvent'], $mysqli); $_SESSION["disableRecord"] = 0; }
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
	//loadStateList($mysqli);
	header("Location: team_detail.php");
	exit();
}
else if (isset($_GET['editTeam']) || isset($_GET['viewTeam'])) {
	clearTeam();
	//loadStateList($mysqli);
	if (isset($_GET['viewTeam'])) { $_SESSION["disableRecord"] = 1;  loadTeam($_GET['viewTeam'], $mysqli);}
	else { $_SESSION["disableRecord"] = 0; loadTeam($_GET['editTeam'], $mysqli); }	
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
else if (isset($_GET['addCoach'])) {
	$_SESSION["pageCommand"] = 'selectCoach';
	$_SESSION["userRole"] = 'COACH';
	$_SESSION["autoCreatedFlag"] = 'NO';
	cacheTeam();
	loadAllUsers($mysqli);
	header("Location: user.php");
	exit();
}
else if (isset($_GET['addVerifier'])) {
	$_SESSION["pageCommand"] = 'selectVerifier';
	$_SESSION["userRole"] = 'VERIFIER';
	$_SESSION["autoCreatedFlag"] = 'NO';
	cacheTournamnent();
	loadAllUsers($mysqli);
	header("Location: user.php");
	exit();
}
else if (isset($_GET['addSupervisor'])) {
	$_SESSION["pageCommand"] = 'selectSupervisor';
	$_SESSION["userRole"] = 'SUPERVISOR';
	$_SESSION["autoCreatedFlag"] = 'NO';
	$_SESSION["addSupervisorEventRowId"] = $_GET['addSupervisor'];
	cacheTournamnent();
	loadAllUsers($mysqli);
	header("Location: user.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'clearLinkedSupervisors') {
	cacheTournamnent();
	unlinkEventSupervisors($_GET['rowNum'], $mysqli);
	exit();
}
else if (isset($_GET['deleteCoach'])) {
	cacheTeam();
	deleteCoach($mysqli, $_GET['deleteCoach']);
	header("Location: team_detail.php");
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
		$_SESSION["filterMyEvents"] = $_GET['filterMyEvents'];
		$_SESSION["eventFilterName"] = $_GET['eventName'];
	}
	loadAllEvents($mysqli);
	header("Location: event.php");
	exit();	
}
else if (isset($_GET['searchTeam']) or ($_GET['command'] != null and $_GET['command'] == 'loadAllTeams')) {
	if (isset($_GET['searchTeam'])) {
		$_SESSION["teamFilterName"] = $_GET['teamFilterName'];
		$_SESSION["filterDivision"] = $_GET['filterDivision'];
		$_SESSION["filterState"] = $_GET['filterState'];
		$_SESSION["filterRegion"] = $_GET['filterRegion'];
		$_SESSION["filterMyTeams"] = $_GET['filterMyTeams'];
	}
	loadAllTeams($mysqli);
	header("Location: team.php");
	exit();	
}
else if (isset($_GET['searchUsers']) or ($_GET['command'] != null and $_GET['command'] == 'loadAllUsers')) {
	if ($_GET['command'] != null and $_GET['command'] == 'loadAllUsers') { $_SESSION["pageCommand"] = ''; }
	$_SESSION["userFirstName"] = $_GET['userFirstName'];
	$_SESSION["userLastName"] = $_GET['userLastName'];
	$_SESSION["autoCreatedFlag"] = $_GET['autoCreatedFlag'];
	//if ($_SESSION["pageCommand"] == null || $_SESSION["pageCommand"] == '')
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
else if (isset($_GET['selectUser'])) {
	if ($_SESSION["pageCommand"] == 'selectCoach' AND $_GET["selectUser"] != null) {
		addCoach('');
		header("Location: team_detail.php");
		exit();
	}
	else if ($_SESSION["pageCommand"] == 'selectSupervisor' AND $_GET["selectUser"] != null) {
		addSupervisor('');
		header("Location: tournament_detail.php");
		exit();
	}
	else if ($_SESSION["pageCommand"] == 'selectVerifier' AND $_GET["selectUser"] != null) {
		addVerifier('');
		header("Location: tournament_detail.php");
		exit();
	}
}
else if (isset($_GET['cancelSelectUser'])) {
	if ($_GET['cancelSelectUser'] == 'selectCoach') {
		header("Location: team_detail.php");
		exit();
	}
	else if ($_GET['cancelSelectUser'] == 'selectSupervisor') {
		header("Location: tournament_detail.php");
		exit();
	}
	else if ($_GET['cancelSelectUser'] == 'selectVerifier') {
		header("Location: tournament_detail.php");
		exit();
	}
	else {
		header("Location: index.php");
		exit();
	}
}

else if ($_GET['command'] != null and $_GET['command'] == 'resetUserPassword') {
	resetUserPassword($mysqli,$_GET['id']);
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
else if (isset($_GET['exportEventScores'])) {
	exportEventScores($mysqli, $_GET['exportEventScores']);
	header("Location: tournament_events.php");
	exit();
}
else if (isset($_GET['exportEventAwards'])) {
	generateEventAwards($mysqli, $_GET['exportEventAwards']);
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
else if (isset($_GET['applyEventScores'])) {
	saveEventScores($mysqli);
	//$_SESSION["tournEventId"] = null;
	header("Location: event_scores.php");
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
	clearTournament();
	loadAllTournaments($mysqli);
	header("Location: tournament.php");
	exit();
}
else if (isset($_GET['generateSupervisorLogins'])) {
	cacheTournamnent();
	$id = $_SESSION["tournamentId"];
	generateUsersForEvents($mysqli, $id);
	saveTournament($mysqli);
	clearTournament();
	$_SESSION["tournamentId"] = $id;
	loadTournament($id, $mysqli);
	header("Location: tournament_detail.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'exportUserPasswords') {
	exportUserPasswords();	
	//header("Location: tournament_detail.php");
	exit();
}

else if (isset($_GET['cancelTournament'])) {
	clearTournament();
	loadAllTournaments($mysqli);
	if ($_SESSION["tournamentScoresIndexReturn"] != null && $_SESSION["tournamentScoresIndexReturn"] == '1') {
		$_SESSION["tournamentScoresIndexReturn"] = null;
		header("Location: index.php");
		exit();
	}
	header("Location: tournament.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'loadDivisionTeams') {
	loadDivisionTeams($_GET['division'],$_GET['filterState'],$_GET['filterRegion'], $mysqli);
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'loadFilteredEvents') {
	loadFilteredEvents($_GET['option'], $mysqli);
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
else if ($_GET['command'] != null and $_GET['command'] == 'addVerifier') { // Depricated
	cacheTournamnent();
	addVerifier('');
	exit();
}

// Self Schedule Commands
else if (isset($_GET['selfSchedule'])) {
	loadSelfSchedule($mysqli, $_GET['selfSchedule'],false);
	header("Location: self_schedule.php");
	exit();
}
else if (isset($_GET['cancelScheduleSettings'])) {
	
	// Add navigation controller
	header("Location: tournament.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'selfScheduleNavigation') {
	if ($_GET['tab'] != null AND $_GET['tab'] == 'SETTINGS') $_SESSION["selfSchedulScreen"] = 'SETTINGS';
	else if ($_GET['tab'] != null AND $_GET['tab'] == 'SCHEDULE') $_SESSION["selfSchedulScreen"] = 'SCHEDULE';
	else if ($_GET['tab'] != null AND $_GET['tab'] == 'MYSCHEDULE') $_SESSION["selfSchedulScreen"] = 'MYSCHEDULE';
	header("Location: self_schedule.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'addPeriod') {
	cacheSelfScheduleSettings();
	addPeriod();
	echo getPeriodsTable();
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'deletePeriod') {
	cacheSelfScheduleSettings();
	deletePeriod($mysqli);
	echo getPeriodsTable();
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'addEventPeriod') {
	cacheSelfScheduleSettings();
	if (addEventPeriod()) echo getEventPeriodsTable();
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'reloadEventPeriods') {
	echo getEventPeriodsTable();
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'deleteEventPeriod') {
	cacheSelfScheduleSettings();
	if (deleteEventPeriod($mysqli)) echo getEventPeriodsTable();	
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'deleteAllEventPrds') {
	cacheSelfScheduleSettings();
	if (deleteAllEventPeriods($mysqli)) echo getEventPeriodsTable();	
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'scheduleEventPeriod') {
	$scheduleEventPeriodId = $_GET['scheduleEventPeriodId'];		
	if (loadScheduleEventPeriod($mysqli, $scheduleEventPeriodId)) {
		header("Location: self_schedule_period.php");
		exit();
	}
	else {
		header("Location: self_schedule.php");
		exit();
	}
}
else if ($_GET['command'] != null and $_GET['command'] == 'updateSelectedTeam') {
	setSelectedSelfScheduleTeam($_GET['tournTeamId']);
	echo $_GET['tournTeamId'];
	exit();
}
else if (isset($_GET['saveScheduleSettings'])) {	
	cacheSelfScheduleSettings();
	saveSelfScheduleSettings($mysqli);
	header("Location: self_schedule.php");
	exit();
}
else if (isset($_GET['cancelSelfSchedulePeriod'])) {
	$selfSchedule = unserialize($_SESSION["selfSchedule"]);
	loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
	header("Location: self_schedule.php");
	exit();	
}
else if (isset($_GET['saveSelfSchedulePeriod'])) {
	header("Location: self_schedule_period.php");
	exit();	
}
else if ($_GET['command'] != null and $_GET['command'] == 'addTeamEventPeriod') {
	if ($_GET['mode'] == 'coach') {
		if (addTeamEventPeriod($mysqli, $_GET['tournTeamId'], $_GET['scheduleEventPeriodId'], 'coach')) { 
			$selfSchedule = unserialize($_SESSION["selfSchedule"]);
			loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
			echo getMyTeams(); 
			echo getScheduleOverview(); 
		}
	}
	else {
		$scheduleEventPeriodId = $_GET['scheduleEventPeriodId'];
		if (addTeamEventPeriod($mysqli, $_GET['tournTeamId'], $scheduleEventPeriodId, 'admin')) {
			$selfSchedule = unserialize($_SESSION["selfSchedule"]);
			loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
			loadScheduleEventPeriod($mysqli, $scheduleEventPeriodId);
			echo getPeriodScheduler();
		}
	}
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'removeTeamEventPeriod') {
	if ($_GET['mode'] == 'coach') {
		$id = getScheduleTeamId($mysqli, $_GET['tournTeamId'], $_GET['scheduleEventPeriodId']);
		if (removeTeamEventPeriod($mysqli, $_GET['tournTeamId'], $_GET['scheduleEventPeriodId'], $id)) { 
			$selfSchedule = unserialize($_SESSION["selfSchedule"]);			
			loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
			echo getMyTeams(); 
			echo getScheduleOverview(); 
		}
	}
	else {
		$scheduleEventPeriodId = $_GET['scheduleEventPeriodId'];
		if (removeTeamEventPeriod($mysqli, $_GET['tournTeamId'], $scheduleEventPeriodId, $_GET['scheduleTeamId'])) {
			$selfSchedule = unserialize($_SESSION["selfSchedule"]);
			loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
			loadScheduleEventPeriod($mysqli, $scheduleEventPeriodId);
			echo getPeriodScheduler();
		}
	}
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'removeAddTeamPeriod') {	
	$tournTeamId = $_GET['tournTeamId'];
	$scheduleEventPeriodId = $_GET['scheduleEventPeriodId'];
	$id = getScheduleTeamId($mysqli, $tournTeamId, $scheduleEventPeriodId);
	removeTeamEventPeriod($mysqli, $tournTeamId, $scheduleEventPeriodId, $id);
	if (addTeamEventPeriod($mysqli, $tournTeamId, $scheduleEventPeriodId, 'coach')) { 
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
		echo getMyTeams(); 
		echo getScheduleOverview(); 
	}
	exit();
}
else if  ($_GET['command'] != null and $_GET['command'] == 'selectScheduleTeam') {
	selectScheduleTeam($_GET['tournTeamId']);
	echo getMyTeams();
	echo getScheduleOverview();
	exit();
}
else if (isset($_GET['exportEvent'])) {
	exportEventSchedule($_GET['exportEvent']);
	exit();	
}
else if (isset($_GET['exportScheduleOverview'])) {
	exportScheduleOverview();
	exit();	
}
else if (isset($_GET['exportScheduleAllEvents'])) {
	exportAllEventsSchedule();
	exit();	
}
else if (isset($_GET['exportMySchedule'])) {
	exportMySchedule();
	exit();
}
else if (isset($_GET['unscheduleAllTeams'])) {
	$id = $_GET['unscheduleAllTeams'];
	unscheduleTeams($id, $mysqli);
	loadSelfSchedule($mysqli, $id,false);
	header("Location: self_schedule.php");
	exit();
}
else if (isset($_GET['resetSelfSchedule'])) {
	$id = $_GET['resetSelfSchedule'];
	resetSelfSchedule($id, $mysqli);
	loadSelfSchedule($mysqli, $id,false);
	header("Location: self_schedule.php");
	exit();
}
else if (isset($_GET['generateReports'])) {
	header("Location: tournament_reports.php");
	exit();
}
else if (isset($_GET['generateTournamentReport'])) {
	// Generate Report
	//header("Location: tournament_reports.php");
	exit();
}
else if (isset($_GET['cancelTournamentReports'])) {
	header("Location: tournament_results.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'generateTournamentReport') {
	
	generateReport($mysqli);
	header("Location: tournament_reports.php");
	exit();
}
else if (isset($_GET['adminCreateUser'])) {
	$navigationHandler = new navigationHandler();
	$navigationHandler->command = $_GET['adminCreateUser'];
	$navigationHandler->toPath = 'account.php';
	$navigationHandler->fromPath = 'user.php';
	if ($navigationHandler->command == 'selectCoach') array_push($navigationHandler->parameters, 'COACH');
	else if ($navigationHandler->command == 'selectSupervisor') array_push($navigationHandler->parameters, 'SUPERVISOR');
	else if ($navigationHandler->command == 'selectVerifier') array_push($navigationHandler->parameters, 'VERIFIER');
	$_SESSION["navigationHandler"] = serialize($navigationHandler);
	clearAccount();
	$_SESSION["accountMode"] = 'create';
	header("Location: account.php");
	exit();
}
else if ($_GET['command'] != null and $_GET['command'] == 'refreshSelfSchedule') {
	$mode = $_GET['mode'];
	$selfSchedule = unserialize($_SESSION["selfSchedule"]);
	loadSelfSchedule($mysqli, $selfSchedule->getTournamentId(),true);
	if ($mode == 'overview') {			
		echo getSSTournamentHeader();
		echo STRING_SPLIT_TOKEN;
		echo getMyTeams(); 
		echo getScheduleOverview(); 
	} else if ($mode == 'scheduler') {
		$scheduleEventPeriodId = $_GET['scheduleEventPeriodId'];
		loadScheduleEventPeriod($mysqli, $scheduleEventPeriodId);
		echo getPeriodScheduler();
	}
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
		
		if (getCurrentRole() == 'ADMIN') {
			$query .= " AND T.ADMIN_USER_ID =" .getCurrentUserId();
		}
		 
		$query = $query . " ORDER BY T.DATE DESC, T.NAME ASC, T.DIVISION ASC ";
		
		if ($_SESSION["tournamentsNumber"] !=null and $_SESSION["tournamentsNumber"] != '') {
			$query = $query . " LIMIT ".$_SESSION["tournamentsNumber"];
		}
		
		$_SESSION["resultsPage"] = 1;
		$_SESSION["allTournaments"] = $query;
	}
	
	
	function deleteTournament($id, $mysqli) {
		
		// Delete Self Schedule
		// Schedule Team
		$result = $mysqli->query("DELETE TS.* FROM SCHEDULE_TEAM TS
			INNER JOIN SCHEDULE_EVENT_PERIOD SEP ON SEP.SCHEDULE_EVENT_PERIOD_ID=TS.SCHEDULE_EVENT_PERIOD_ID
			INNER JOIN SCHEDULE_EVENT SE ON SE.SCHEDULE_EVENT_ID=SEP.SCHEDULE_EVENT_ID
			INNER JOIN TOURNAMENT_SCHEDULE TTS ON TTS.TOURNAMENT_SCHEDULE_ID=SE.TOURNAMENT_SCHEDULE_ID
			WHERE TTS.TOURNAMENT_ID=".$id);
			
		// Delete Schedule Event Period
		$result = $mysqli->query("DELETE SEP.* FROM SCHEDULE_EVENT_PERIOD SEP
			INNER JOIN SCHEDULE_EVENT SE ON SE.SCHEDULE_EVENT_ID=SEP.SCHEDULE_EVENT_ID
			INNER JOIN TOURNAMENT_SCHEDULE TTS ON TTS.TOURNAMENT_SCHEDULE_ID=SE.TOURNAMENT_SCHEDULE_ID
			WHERE TTS.TOURNAMENT_ID=".$id);
		
		// Delete Schedule Event
		$result = $mysqli->query("DELETE SE.* FROM SCHEDULE_EVENT SE 
			INNER JOIN TOURNAMENT_SCHEDULE TTS ON TTS.TOURNAMENT_SCHEDULE_ID=SE.TOURNAMENT_SCHEDULE_ID
			WHERE TTS.TOURNAMENT_ID=".$id);
			
		// Delete Schedule Period
		$result = $mysqli->query("DELETE SP.* FROM SCHEDULE_PERIOD SP
			INNER JOIN TOURNAMENT_SCHEDULE TTS ON TTS.TOURNAMENT_SCHEDULE_ID=SP.TOURNAMENT_SCHEDULE_ID
			WHERE TTS.TOURNAMENT_ID=".$id);
			
		// Delete Tournament Schedule
		$result = $mysqli->query("DELETE TTS.* FROM TOURNAMENT_SCHEDULE TTS WHERE TTS.TOURNAMENT_ID=".$id);
		
		
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
		if ($_GET['eventsAAwarded'] != null) $_SESSION["eventsAAwarded"] = $_GET['eventsAAwarded'];
		if ($_GET['overallAAwarded'] != null) $_SESSION["overallAAwarded"] = $_GET['overallAAwarded'];
		if ($_GET['bestNewTeamFlag'] != null) $_SESSION["bestNewTeam"] = $_GET['bestNewTeamFlag']; else $_SESSION["bestNewTeam"] = null;
		if ($_GET['improvedTeam'] != null) $_SESSION["improvedTeam"] = $_GET['improvedTeam']; else $_SESSION["improvedTeam"] = null;
		if ($_GET['teamList1Text'] != null) $_SESSION["teamList1Text"] = $_GET['teamList1Text']; else $_SESSION["teamList1Text"] = '';
		if ($_GET['teamList2Text'] != null) $_SESSION["teamList2Text"] = $_GET['teamList2Text']; else $_SESSION["teamList2Text"] = '';
		if ($_GET['stateBids'] != null) $_SESSION["stateBids"] = $_GET['stateBids']; else $_SESSION["stateBids"] = null;
		
		$_SESSION["tourn1Linked"] = $_GET['tourn1Linked'];
		$_SESSION["tourn2Linked"] = $_GET['tourn2Linked'];
		$_SESSION["previousTournLinked"] = $_GET['previousTournLinked'];
		if ($_GET['highestScoreAlt'] != null) $_SESSION["highestScoreAlt"] = $_GET['highestScoreAlt'];
		if ($_GET['pointsForNP'] != null) $_SESSION["pointsForNP"] = $_GET['pointsForNP'];
		if ($_GET['pointsForDQ'] != null) $_SESSION["pointsForDQ"] = $_GET['pointsForDQ'];
		
		// Team Cache - teamNumber, alternateTeam, bestNewTeam,mostImprovedTeam
		$count = 0;
		$teamList = $_SESSION["teamList"];
		while ($count < 200) {
			$team = $teamList[$count];
			if ($_GET['teamNumber'.$count] != null or $_GET['alternateTeam'.$count] != null) {	
				if ($_GET['teamNumber'.$count] != null) {			
					$team[2] = $_GET['teamNumber'.$count];	
				}
				if ($_GET['alternateTeam'.$count] != null) {			
					$team[3] = $_GET['alternateTeam'.$count];	
				}
				if ($_GET['bestNewTeam'.$count] != null) {			
					$team[5] = $team[1];	
				}
				else $team[5] = '';
				if ($_GET['mostImprovedTeam'.$count] != null) {			
					$team[6] = $team[1];	
				}
				else $team[6] = '';
				
				//if ($_GET['bestNewTeam'] != null AND $_GET['bestNewTeam'] == $team[1]) $team[5] = $team[1]; else $team[5] = '';
				//if ($_GET['mostImprovedTeam'] != null AND $_GET['mostImprovedTeam'] == $team[1]) $team[6] = $team[1]; else $team[6] = '';

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
		
		while ($count < 200) {
			$event = $eventList[$count];
			if ($_GET['trialEvent'.$count] != null) {	
				
				$event['2'] = $_GET['trialEvent'.$count];	
				//$event['5'] = $_GET['eventSupervisor'.$count];	
				$event['7'] = $_GET['primAltFlag'.$count];
				
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
	
	function generateUsersForEvents($mysqli, $id) {
		// Delete linked users if auto_created flag = 1
		$result = $mysqli->query("DELETE FROM USER_LOGIN_LOG WHERE USER_ID IN (SELECT TE.USER_ID FROM TOURNAMENT_EVENT TE INNER JOIN USER U on u.USER_ID=TE.USER_ID WHERE TE.TOURNAMENT_ID=".$id." AND U.AUTO_CREATED_FLAG = 1)");
		$result = $mysqli->query("DELETE UR.* FROM USER_ROLE UR INNER JOIN USER U on U.USER_ID=UR.USER_ID
					WHERE U.AUTO_CREATED_FLAG = 1 AND U.USER_ID IN (SELECT TE.USER_ID FROM TOURNAMENT_EVENT TE WHERE TE.TOURNAMENT_ID=".$id.")");
		$result = $mysqli->query("DELETE U1.* FROM USER U1 WHERE U1.AUTO_CREATED_FLAG = 1 AND U1.USER_ID IN (SELECT TE.USER_ID FROM TOURNAMENT_EVENT TE WHERE TE.TOURNAMENT_ID=".$id.")");
		

		$eventRows = array();
		// loop through events
		$eventList = $_SESSION["eventList"];
		$sql = "";
		$count = 0;
		
		$result = $mysqli->query("select max(USER_ID) + 1 from USER");
		$row = $result->fetch_row(); 
		$id = 0;
		if ($row != null and $row['0'] != null) $id = $row['0'];
			
		foreach ($eventList as $event) { 		
			// create user for each event
			// link user Id in event list (Save will happen after this method)
			$flag = True;
			$username = '';
			while ($flag) {
				$eventName = str_replace('\'','',$event['1']); $eventName = str_replace('\"','',$eventName); $eventName = str_replace('\\','',$eventName);
				$n = rand(0, 9999); $e = explode(" ", $eventName); $d = $_SESSION["tournamentDivision"]; $y = date("Y");
				$username = strtolower($e[0].$y.$d.$n);
				
				$exists = $mysqli->query("SELECT 1 FROM TEAM WHERE TEAM_ID = ".$selectedTeam); 
				if ($exists) {
					$userNameRow = $exists->fetch_row();
					if ($userNameRow == null OR $userNameRow['0'] == null) $flag = False;
				} else {
					$flag = False;
				}
			}
			
			$passowrd = $username . rand(0, 9);
			$encryptPwd = crypt($passowrd);
			$sql .= " INSERT INTO USER (USER_ID, USERNAME, PASSWORD, ROLE_CODE, FIRST_NAME, LAST_NAME, ACCOUNT_ACTIVE_FLAG, PHONE_NUMBER, AUTO_CREATED_FLAG) 
				VALUES (".$id.",'".$username."','".$encryptPwd."',null,'".$eventName."','Supervisor',1,'',1); 
				INSERT INTO `USER_ROLE` (`USER_ID`, `ROLE_CODE`) VALUES (".$id.", 'SUPERVISOR'); ";
			
			$supervisor = array($id, $eventName,'Supervisor',$username);
			$event['5'] = $supervisor;
			$eventList[$count] = $event;
			$count++;
			
			$row = array();
			array_push($row,$username); array_push($row,$username);  array_push($row,$passowrd);
			array_push($eventRows, $row);
			$id++;
		}
		if (!$mysqli->multi_query($sql)) {
			
		}
		
		do {
		 if ($result = $mysqli->store_result()) {
		  var_dump($result->fetch_all(MYSQLI_ASSOC));
		  $result->free();
		 }
		} while ($mysqli->more_results() && $mysqli->next_result());
		
		$_SESSION["EXPORT_GENERATED_USERS"] = $eventRows;
		$_SESSION["EXPORT_GENERATED_USERS_FLAG"] = 1;
		$_SESSION["eventList"] = $eventList;

	}
	
	
	function exportUserPasswords() {
		// Build spreadsheet to export
		// filename for download
	  	$filename = $_SESSION["tournamentName"]." Supervisors " . $_SESSION["tournamentDivision"] . ".csv";
	  	header("Content-Disposition: attachment; filename=\"$filename\"");
	  	header("Content-Type: text/csv; charset=utf-8");
	  		
	  	$output = fopen('php://output', 'w');
	
		$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
		$users = $_SESSION["EXPORT_GENERATED_USERS"];
		$headings = array();
		array_push($headings,"Event Name");
		array_push($headings,"Username");
		array_push($headings,"Password");
		fputcsv($output, $headings);
		foreach ($users as $row) {
			fputcsv($output, $row);
		}
		fclose($output);
		$_SESSION["EXPORT_GENERATED_USERS_FLAG"] = null;
		$_SESSION["EXPORT_GENERATED_USERS"] = null;	
	}
	
	/**	function generateUser($mysqli, $role, $id) {			
			// GENERATE USER NAME
			$flag = True;
			$username = '';
			while ($flag) {
				$n = rand(0, 9999999); 
				$e = explode(" ", $role); 
				$d = 'user'; 
				$y = date("Y");
				$username = strtolower($e[0].$y.$d.$n);
					
				$exists = $mysqli->query("SELECT 1 FROM USER WHERE USERNAME = ".$username); 
				if ($exists) {
					$userNameRow = $exists->fetch_row();
					if ($userNameRow == null OR $userNameRow['0'] == null) $flag = False;
				} else {
					$flag = False;
				}
			}
			
			// GENERATE PASSWORD
			$passowrd = $username . rand(0, 9);
			$encryptPwd = crypt($passowrd);
			$sql .= " INSERT INTO USER (USER_ID, USERNAME, PASSWORD, ROLE_CODE, FIRST_NAME, LAST_NAME, ACCOUNT_ACTIVE_FLAG, PHONE_NUMBER, AUTO_CREATED_FLAG) 
			VALUES (".$id.",'".$username."','".$encryptPwd."','SUPERVISOR','".addslashes($event['1'])."','Supervisor',1,'',1); ";
					
			$supervisor = array($id, addslashes($event['1']),'Supervisor',$username);			
			$event['5'] = $supervisor;
			$eventList[$count] = $event;
			$count++;			
			$row = array();
			array_push($row, $role); array_push($row,$username);  array_push($row,$passowrd);
			

			
			// filename for download
		  	$filename = $_SESSION["tournamentName"]." Supervisors " . $_SESSION["tournamentDivision"] . ".csv";
		  	header("Content-Disposition: attachment; filename=\"$filename\"");
		  	header("Content-Type: text/csv; charset=utf-8");
		  		
		  	$output = fopen('php://output', 'w');
		
			$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
			$users = $_SESSION["EXPORT_GENERATED_USERS"];
			$headings = array();
			array_push($headings,"Event Name");
			array_push($headings,"Username");
			array_push($headings,"Password");
			fputcsv($output, $headings);
			foreach ($users as $row) {
				fputcsv($output, $row);
			}
			fclose($output);
	}**/
	
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
		$_SESSION["eventsAAwarded"] = null;
		$_SESSION["overallAAwarded"] = null;
		$_SESSION["bestNewTeam"] = null;
		$_SESSION["improvedTeam"] = null;
		$_SESSION["teamList1Text"] = null;
		$_SESSION["teamList2Text"] = null;
		$_SESSION["tourn1Linked"] = null;
		$_SESSION["tourn2Linked"] = null;
		$_SESSION["previousTournLinked"] = null;
		$_SESSION["highestScoreAlt"] = null;
		$_SESSION["pointsForNP"] = null;
		$_SESSION["pointsForDQ"] = null;
		$_SESSION["totalPointsWins"] = null;
		$_SESSION["stateBids"] = null;
		
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
	
		function addVerifier($str) {
			// USER_ID - LAST_NAME, FIRST_NAME - USERNAME	
			$verifierList = $_SESSION["verifierList"];
			if (!$verifierList) $verifierList = array();
			
			// Add coach if they are not already added 
			$values = '';		
			if ($_GET['selectUser']) $values = explode('-', $_GET['selectUser']);
			else if ($str) $values = explode('-',$str);
			
			// Validate Coach has not already been linked
			$exits = false;
			foreach ($verifierList as $verifier) {
				if ($verifier[0] == $values[0]) {
					$exists = true;
					$_SESSION['scorecenter_errors'] = array(ERROR_TOURNAMENT_ADD_VERIFIER);
					break;
				}
			}
			
			if (!$exists) {
				addRole($values[0],'VERIFIER');
				$verifier = array();
				array_push($verifier, $values[0]);
				array_push($verifier, $values[1]);
				array_push($verifier, $values[2]);
				array_push($verifier, '');
				array_push($verifierList, $verifier);
				$_SESSION["verifierList"] = $verifierList;
			}	
			
		/**Validation: cannot add existing Team or blank	
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
			} **/
	}
	
	function addSupervisor($str) {
		$eventList = $_SESSION["eventList"];
		if ($eventList == null) $eventList = array();
		$rowId = $_SESSION["addSupervisorEventRowId"];
		$count = 0;
		$values = '';		
		if ($_GET['selectUser']) $values = explode('-', $_GET['selectUser']);
		else if ($str) $values = explode('-',$str);
		$name = explode(',', $values[1]);
		
		foreach ($eventList as $event) {
			if ($count == $rowId) {
				addRole($values[0],'SUPERVISOR');
				$supervisor = $event[5];
				$supervisor[0] = $values[0];
				$supervisor[1] = $name[1];
				$supervisor[2] = $name[0];
				$supervisor[3] = $values[2];
				
				$event[5] = $supervisor;
				$eventList[$count] = $event;
				break;
			}
			$count++;
		}
		// $_SESSION['scorecenter_errors'] = array(ERROR_TOURNAMENT_ADD_VERIFIER);
		$_SESSION["addSupervisorEventRowId"] = null;
		$_SESSION["eventList"] = $eventList;
		
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
	
				// 0: EVENT_ID 1: NAME 2:TRIAL_EVENT 3: TOURN_EVENT_ID 4: New Event 0/1 5: USER_ID 6: USER NAME 7: P&A
				$event = array($selectedEvent, $row1['0'], "","","1", array(null,null,null,null), "", 0); 				
				array_push($eventList, $event);
				$_SESSION["eventList"] = $eventList;
				reloadTournamentEvent($mysqli);
			} else {
				echo $errorStr;
			}
	}
	
	function unlinkEventSupervisors($rowNum, $mysqli) {
		$eventList = $_SESSION["eventList"];
		$count = 0;
		$userIds = '-10';
		foreach ($eventList as $event) { 
			$user = $event['5'];
			if ($rowNum == -1) {
				if (!$user[0] || $user[0] == '') $user[0] = -10;
				$userIds .= ','. $user[0];
				$event['5'] = array(null,null,null,null);
				$event['6'] = null;	
				$eventList[$count] = $event;	
			}
			else if ($rowNum == $count) {
				$result = $mysqli->query("UPDATE TOURNAMENT_EVENT SET USER_ID = null WHERE TOURN_EVENT_ID = " .$event[3]);
								$result = $mysqli->query("DELETE UR.* FROM USER_ROLE UR INNER JOIN USER U ON U.USER_ID=UR.USER_ID WHERE U.AUTO_CREATED_FLAG = 1 AND U.USER_ID = " .$user[0]);
				$result = $mysqli->query("DELETE FROM USER WHERE AUTO_CREATED_FLAG = 1 AND USER_ID = " .$user[0]);
				$event['5'] = array(null,null,null,null);
				$event['6'] = null;
				$eventList[$count] = $event;
				break;
			}
			$count++;
		}
		if ($rowNum == -1) {
			$result = $mysqli->query("UPDATE TOURNAMENT_EVENT SET USER_ID = null WHERE TOURNAMENT_ID=".$_SESSION["tournamentId"]);
			$result = $mysqli->query("DELETE UR.* FROM USER_ROLE UR INNER JOIN USER U ON U.USER_ID=UR.USER_ID WHERE U.AUTO_CREATED_FLAG = 1 AND U.USER_ID IN (".$userIds.")");
			$result = $mysqli->query("DELETE FROM USER WHERE AUTO_CREATED_FLAG = 1 AND USER_ID IN (".$userIds.")");
		}
		
		
		
		$_SESSION["eventList"] = $eventList;
		reloadTournamentEvent($mysqli);
	}
	
	function deleteTournamentTeam($mysqli, $row) {
		// Remove From Cache
		$teamList = $_SESSION["teamList"];
		$count = 0;
		$valid = true;
		if ($teamList) {
			foreach ($teamList as $key => $team) { 
				if ($row == $count) {				
					$result = $mysqli->query("SELECT TES.SCORE FROM TEAM_EVENT_SCORE TES WHERE TES.TOURN_TEAM_ID = " .$team[4]);
					if ($result) {
						$row = $result->fetch_row();
						if ($row['0'] != null and $row['0'] != '') {$valid = false; echo 'error'; return;}
					}
					// Cannot Delete team When team has Self Scheduled
					$result = $mysqli->query("SELECT TOURN_TEAM_ID FROM SCHEDULE_TEAM WHERE TOURN_TEAM_ID = " .$team[4]);
					if ($result) {
						$row = $result->fetch_row();
						if ($row['0'] != null and $row['0'] != '') {$valid = false; echo 'error1'; return;}
					}

					if ($valid AND $team[4] AND $team[4] != '') {
						// delete tourn team
						$result = $mysqli->query("DELETE FROM TOURNAMENT_TEAM WHERE TOURN_TEAM_ID = " .$team[4]);
						unset($teamList[$key]);
						$teamList = array_values($teamList);
						$_SESSION["teamList"] = $teamList;
						reloadTournamentTeam();
					}
					else if ($valid){
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
		$valid = true;
		if ($eventList) {
			foreach ($eventList as $key => $event) { 
				if ($row == $count) {
					
					$result = $mysqli->query("SELECT TES.SCORE FROM TEAM_EVENT_SCORE TES WHERE TES.TOURN_EVENT_ID = " .$event[3]);
					if ($result) {
						$row = $result->fetch_row();
						if ($row['0'] != null and $row['0'] != '') { $valid = false; echo 'error'; return;}
					}
					
					// Cannot Delete event When Event has Self Schedule Periods
					$result = $mysqli->query("SELECT TOURN_EVENT_ID FROM SCHEDULE_EVENT WHERE TOURN_EVENT_ID = " .$event[3]);
					if ($result) {
						$row = $result->fetch_row();
						if ($row['0'] != null and $row['0'] != '') { $valid = false; echo 'error1'; return;}
					}
					
					if ($valid AND $event[3] AND $event[3] != '') {
						// delete tourn event
						$result = $mysqli->query("DELETE FROM TOURNAMENT_EVENT WHERE TOURN_EVENT_ID = " .$event[3]);
						unset($eventList[$key]);
						$eventList = array_values($eventList);
						$_SESSION["eventList"] = $eventList;
						reloadTournamentEvent($mysqli);
					} 
					else if ($valid){
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
	
	function loadFilteredEvents($option, $mysqli) {
		$_SESSION["filterMyEvents"] = $option;
		$query = "SELECT DISTINCT * FROM EVENT WHERE 1=1 ";
		if ($option == 'OFFICIAL') $query .= " AND OFFICIAL_EVENT_FLAG=1 ";
		else if ($option == 'MY') $query .= " AND CREATED_BY=".getCurrentUserId();
		$query .= " ORDER BY NAME ASC ";
		$results = $mysqli->query($query);
		
		echo '<select class="form-control" name="eventAdded" id="eventAdded">';
		echo '<option value=""></option>';
		if ($results) {
        	while($eventRow = $results->fetch_array()) {
            	echo '<option value="'.$eventRow['0'].'">'.$eventRow['1'].'</option>';		
             }
        }
		echo '</select>';
	}
	
	function loadDivisionTeams($division, $filterState, $filterRegion, $mysqli) {
		$_SESSION["filterState"] = $filterState;
		$_SESSION["filterRegion"] = $filterRegion;
		$query = "SELECT DISTINCT * FROM TEAM WHERE 1=1 ";
		if ($division != null and $division != '') $query .= " AND DIVISION = '".$division. "' ";
		if ($filterState != null and $filterState != '') $query .= " AND STATE = '".$filterState. "' ";
		if ($filterRegion != null and $filterRegion != '') $query .= " AND REGION = '".$filterRegion. "' ";
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
		$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
		$division1 = null;
		$date1 = strtotime($date); $date = date('Y-m-d', $date1 );
		if (strcmp($division,"A") == 0) $division1 = 'B'; else if (strcmp($division,"B") == 0) $division1 = 'A'; else if (strcmp($division,"C") == 0) $division1 = 'A';

		$linkedTournaments1 = $mysqli->query("SELECT T.TOURNAMENT_ID, CONCAT(T.NAME,' (',T.DIVISION,')') AS NAME,DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE1' FROM TOURNAMENT T WHERE T.DATE='".$date."' AND T.DIVISION='".$division1."' ORDER BY T.NAME ASC ");
		
		echo '<select class="form-control" name="tourn1Linked" id="tourn1Linked"><option value=""></option>';
			    if ($linkedTournaments1) {
             		while($linkedTourn1Row = $linkedTournaments1->fetch_array()) {
             			echo '<option value="'.$linkedTourn1Row['0'].'" '; if($_SESSION["tourn1Linked"] == $linkedTourn1Row['0']){echo("selected");} echo '>'.$linkedTourn1Row['1'].' '.$linkedTourn1Row['2'].'</option>';
             			
             		}
             	}
		echo '</select>';
		
		echo '*****';
		
		if (strcmp($division,"A") == 0) $division1 = 'C'; else if (strcmp($division,"B") == 0) $division1 = 'C'; else if (strcmp($division,"C") == 0) $division1 = 'B';
		$linkedTournaments2 = $mysqli->query("SELECT T.TOURNAMENT_ID, CONCAT(T.NAME,' (',T.DIVISION,')') AS NAME,DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE1' FROM TOURNAMENT T WHERE T.DATE='".$date."' AND T.DIVISION='".$division1."' ORDER BY T.NAME ASC ");

		echo '<select class="form-control" name="tourn2Linked" id="tourn2Linked"><option value=""></option>';
				if ($linkedTournaments2) {
             		while($linkedTourn2Row = $linkedTournaments2->fetch_array()) {
             			echo '<option value="'.$linkedTourn2Row['0'].'" '; if($_SESSION["tourn2Linked"] == $linkedTourn2Row['0']){echo("selected");} echo '>'.$linkedTourn2Row['1'].' '.$linkedTourn2Row['2'].'</option>';
             			
             		}
             	}
		echo '</select>';
		
		echo '*****';
		
		//if (strcmp($division,"A") == 0) $division1 = 'C'; else if (strcmp($division,"B") == 0) $division1 = 'C'; else if (strcmp($division,"C") == 0) $division1 = 'B';
		$sql = " SELECT DISTINCT * FROM (
					SELECT T.TOURNAMENT_ID, CONCAT(T.NAME,' (',T.DIVISION,')') AS NAME,DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE1' FROM TOURNAMENT T WHERE T.DIVISION='".$division."' AND T.TOURNAMENT_ID <> ".$_SESSION["tournamentId"];
					if ($userSessionInfo->getRole() != 'SUPERUSER') $sql .= " AND T.ADMIN_USER_ID=".$userSessionInfo->getUserId() ;
					$sql .= " UNION
					SELECT T1.TOURNAMENT_ID, CONCAT(T1.NAME,' (',T1.DIVISION,')') AS NAME,DATE_FORMAT(T.DATE,'%m/%d/%Y') 'DATE1' FROM TOURNAMENT T1 
					INNER JOIN TOURNAMENT T ON T.PREVIOUS_YEAR_TOURN=T1.TOURNAMENT_ID AND T.TOURNAMENT_ID=".$_SESSION["tournamentId"]." 
					) t ORDER BY NAME ASC ";
		$previousTournLinked = $mysqli->query($sql);

		echo '<select class="form-control" name="previousTournLinked" id="previousTournLinked"><option value=""></option>';
				if ($previousTournLinked) {
             		while($previousTournLinkedRow = $previousTournLinked->fetch_array()) {
             			echo '<option value="'.$previousTournLinkedRow['0'].'" '; if($_SESSION["previousTournLinked"] == $previousTournLinkedRow['0']){echo("selected");} echo '>'.$previousTournLinkedRow['1'].' '.$previousTournLinkedRow['2'].'</option>';
             			
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
      				echo '<input type="text"  class="form-control" size="10" 
      						name="teamNumber'.$teamCount.'" id="teamNumber'.$teamCount.'" autocomplete="off" value="'.$team['2'].'">';
      				echo '</div></td>';
					echo '<td><div class="col-xs-5 col-md-5">'; 
					echo '<select   class="form-control" name="alternateTeam'.$teamCount.'" id="alternateTeam'.$teamCount.'" >';
					echo '<option value="0"'; if($team['3'] == '' or $$team['3'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($team['3'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</div></td>';
					echo '<td><input type="checkbox" name="bestNewTeam'.$teamCount.'" id="bestNewTeam'.$teamCount.'" value="'.$team['1'].'" '; if($team['5'] == $team['1']){echo("checked");} echo '></td>';
					echo '<td><input type="checkbox" name="mostImprovedTeam'.$teamCount.'" id="mostImprovedTeam'.$teamCount.'" value="'.$team['1'].'" '; if($team['6'] == $team['1']){echo("checked");} echo '></td>';
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
	    	/**$supervisors = $mysqli->query("SELECT DISTINCT X.* FROM (
										SELECT DISTINCT U.USER_ID, CONCAT(U.LAST_NAME,', ',U.FIRST_NAME,' (', U.USERNAME,')') AS USER
										FROM USER U WHERE U.ROLE_CODE='SUPERVISOR' AND COALESCE(U.AUTO_CREATED_FLAG,0) <> 1 AND ACCOUNT_ACTIVE_FLAG=1
										UNION
										SELECT DISTINCT U1.USER_ID, CONCAT(U1.LAST_NAME,', ',U1.FIRST_NAME,' (', U1.USERNAME,')') AS USER
										FROM USER U1
										INNER JOIN TOURNAMENT_EVENT TE on TE.USER_ID=U1.USER_ID and TE. TOURNAMENT_ID=".$_SESSION["tournamentId"]."
										WHERE U1.ROLE_CODE='SUPERVISOR'
										) X
										ORDER BY UPPER(X.USER) ASC"); **/
    									 
			$eventList = $_SESSION["eventList"];
			$eventCount = 0;
			if ($eventList) {				
				foreach ($eventList as $event) {
					echo '<tr>';
      				echo '<td>'; echo $event['1']; echo '</td>';
      				echo '<td>';
      				/**echo '<select  class="form-control" name="eventSupervisor'.$eventCount.'" id="eventSupervisor'.$eventCount.'">';
      				echo '<option value=""></option>';
      				if ($supervisors) {
             			while($supervisorRow = $supervisors->fetch_array()) {
             				echo '<option value="'.$supervisorRow['0'].'"'; if($supervisorRow['0'] == $event['5']){echo("selected");} echo '>'.$supervisorRow['1'].'</option>';	
             			}
             		}    
             		mysqli_data_seek($supervisors, 0);				
      				echo '</select>'; **/
      				$supervisor = $event['5'];
      				echo '<table width="100%"><tr><td width="35%"><button type="submit" class="btn btn-xs btn-primary" name="addSupervisor" value="'.$eventCount.'">Select Supervisor</button> <button type="button" onclick="clearSupervisors('.$eventCount.')" class="btn btn-xs btn-primary" name="deleteSupervisor" value="'.$eventCount.'">Clear</button></td>';
      				echo '<td><input type="text" readonly class="form-control" style=" display: inline; white-space:nowrap;" name="eventSupervisor'.$eventCount.'" id="eventSupervisor'.$eventCount.'" value="';
      				if ($supervisor AND $supervisor[0]) echo $supervisor['2'] .', '. $supervisor['1'].' ('.$supervisor['3'].')';     				
      				echo '"></td></tr></table>';
      				echo '</td>';
					echo '<td>'; 
					echo '<select  class="form-control" name="trialEvent'.$eventCount.'" id="trialEvent'.$eventCount.'">';
					echo '<option value="0"'; if($event['2'] == '' or $event['2'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($event['2'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</td>';
					echo '<td>'; 
					echo '<select  class="form-control" name="primAltFlag'.$eventCount.'" id="primAltFlag'.$eventCount.'">';
					echo '<option value="0"'; if($event['7'] == '' or $event['2'] == 0){echo("selected");} echo '>No</option>';
					echo '<option value="1"'; if($event['7'] == 1){echo("selected");} echo '>Yes</option>';
					echo '</select>';
					echo '</td>';
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
				$_SESSION["eventsAAwarded"] = $tournamentRow['20'];
				$_SESSION["overallAAwarded"] = $tournamentRow['21'];
				$_SESSION["bestNewTeam"] = $tournamentRow['12'];
				$_SESSION["improvedTeam"] = $tournamentRow['13'];
				$_SESSION["tourn1Linked"] = $tournamentRow['14'];
				$_SESSION["tourn2Linked"] = $tournamentRow['15'];
				$_SESSION["lockScoresFlag"] = $tournamentRow['16'];
				$_SESSION["highestScoreAlt"] = $tournamentRow['17'];
				$_SESSION["pointsForNP"] = $tournamentRow['18'];
				$_SESSION["pointsForDQ"] = $tournamentRow['19'];
				$_SESSION["teamList1Text"] = $tournamentRow['23']; 
				$_SESSION["teamList2Text"] = $tournamentRow['24'];
				$_SESSION["stateBids"] = $tournamentRow['25'];
				$_SESSION["previousTournLinked"] = $tournamentRow['26'];				
 				
 				$date = strtotime($tournamentRow['4']);
 				$_SESSION["tournamentDate"] = date('m/d/Y', $date);
 				
 			// Set 'FILTER EVENTS' Filter to Default 
			if ($_SESSION["filterMyEvents"] == null) {
				if (getCurrentRole() == 'SUPERUSER') $_SESSION["filterMyEvents"] = 'OFFICIAL';
				else $_SESSION["filterMyEvents"] = 'OFFICIAL';
			}
			if ($_SESSION["filterState"] === null) {
				$_SESSION["filterState"] = getUserState();
			}
 				
    		}
	
			// Load Events
			$eventList = array();
			$result = $mysqli->query("SELECT TE.EVENT_ID, E.NAME, TE.TRIAL_EVENT_FLAG, TE.TOURN_EVENT_ID, U.USER_ID, U.FIRST_NAME, U.LAST_NAME, U.USERNAME, TE.PRIM_ALT_FLAG 
									FROM TOURNAMENT_EVENT TE 
									INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID 
									INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID 
									LEFT JOIN USER U on U.USER_ID=TE.USER_ID
									WHERE TE.TOURNAMENT_ID= " .$id. " ORDER BY UPPER(E.NAME) ASC "); 
 			if ($result) {
 				while($eventRow = $result->fetch_array(MYSQLI_BOTH)) {
 					$event = array();
 					$supervisor = array($eventRow['USER_ID'], $eventRow['FIRST_NAME'], $eventRow['LAST_NAME'],$eventRow['USERNAME']);
 					
 					array_push($event, $eventRow['0']);
 					array_push($event, $eventRow['1']);
 					array_push($event, $eventRow['2']);
 					array_push($event, $eventRow['3']);
 					array_push($event, "0");
 					array_push($event, $supervisor);
 					array_push($event, $eventRow['USER_ID']);
 					array_push($event, $eventRow['8']);
				
 					array_push($eventList, $event);
 				}
			}	
			$_SESSION["resultsPage"] = 1;
			$_SESSION["eventList"] = $eventList;
			
			// Load Teams
			$teamList = array();
			$result = $mysqli->query("SELECT TT.TEAM_ID, T.NAME, TT.TEAM_NUMBER, TT.ALTERNATE_FLAG, TT.TOURN_TEAM_ID, TT.BEST_NEW_TEAM_FLAG, TT.MOST_IMPROVED_TEAM_FLAG FROM TOURNAMENT_TEAM TT INNER JOIN TOURNAMENT TR on 		
									TR.TOURNAMENT_ID=TT.TOURNAMENT_ID 
									INNER JOIN TEAM T on T.TEAM_ID=TT.TEAM_ID WHERE TT.TOURNAMENT_ID= " .$id. " ORDER BY if(CAST(TT.TEAM_NUMBER AS UNSIGNED)=0,1,0), CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC, TEAM_NUMBER, T.NAME "); 
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
			$eventsAAwarded = $_SESSION["eventsAAwarded"];
			$overallAAwarded = $_SESSION["overallAAwarded"];
			$bestNewTeam = $_SESSION["bestNewTeam"];
			$improvedTeam = $_SESSION["improvedTeam"];
			$tourn1Linked = $_SESSION["tourn1Linked"]; if ($tourn1Linked == null or $tourn1Linked == '') $tourn1Linked = 'NULL';
			$tourn2Linked = $_SESSION["tourn2Linked"]; if ($tourn2Linked == null or $tourn2Linked == '') $tourn2Linked = 'NULL';
			$previousTournLinked = $_SESSION["previousTournLinked"]; if ($previousTournLinked == null or $previousTournLinked == '') $previousTournLinked = 'NULL';
			$highestScoreAlt = $_SESSION["highestScoreAlt"];
			$pointsForNP = $_SESSION["pointsForNP"];
			$pointsForDQ = $_SESSION["pointsForDQ"];
			$teamList1Text = $_SESSION["teamList1Text"];
			$teamList2Text = $_SESSION["teamList2Text"];
			$stateBids = $_SESSION["stateBids"];
			
		// if Tournament id is null create new
		if ($_SESSION["tournamentId"] == null) { 
			$result = $mysqli->query("select max(TOURNAMENT_ID) + 1 from TOURNAMENT");
			$row = $result->fetch_row(); 
			$id = 1;
			if ($row != null and $row['0'] != null) $id = $row['0'];  
			$_SESSION["tournamentId"] = $id;
			$userId = getCurrentUserId();
			
			$query = $mysqli->prepare("INSERT INTO TOURNAMENT (TOURNAMENT_ID, NAME, LOCATION, DIVISION, DATE, NUMBER_EVENTS, NUMBER_TEAMS, 
			HIGHEST_SCORE_POSSIBLE, DESCRIPTION, HIGH_LOW_WIN_FLAG, EVENTS_AWARDED, OVERALL_AWARDED,BEST_NEW_TEAM_FLAG,MOST_IMPROVED_FLAG,LINKED_TOURN_1,LINKED_TOURN_2,SCORES_LOCKED_FLAG,HIGHEST_SCORE_POSSIBLE_ALT,ADDITIONAL_POINTS_NP,ADDITIONAL_POINTS_DQ,EVENTS_AWARDED_ALT,OVERALL_AWARDED_ALT, ADMIN_USER_ID, TEAM_LIST_1_TEXT, TEAM_LIST_2_TEXT,STATE_BID_COUNT,PREVIOUS_YEAR_TOURN) VALUES (".$id.",?,?,?,?,?,?,?,?,?,?,?,?,?,".$tourn1Linked.",".$tourn2Linked.",?,?,?,?,?,?,?,?,?,?,".$previousTournLinked.") ");
			
			$query->bind_param('ssssiiisiiiiiiiiiiiissi',$name,$location, $division,$date, $numberEvents, $numberTeams, $highestScore, $description, $highLowWins, $eventsAwarded, $overallAwarded, $bestNewTeam, $improvedTeam,$lockScoresFlag,$highestScoreAlt,$pointsForNP,$pointsForDQ,$eventsAAwarded,$overallAAwarded,$userId,$teamList1Text,$teamList2Text,$stateBids);
			
			$query->execute();
			$query->free_result();
			//echo $query;
			//$result = mysql_query($query);
		}
		else {
			$query = $mysqli->prepare("UPDATE TOURNAMENT SET NAME=?, LOCATION=?, DIVISION=?, DATE=?, NUMBER_EVENTS=?,NUMBER_TEAMS=?,
			HIGHEST_SCORE_POSSIBLE=?, DESCRIPTION=?, HIGH_LOW_WIN_FLAG=?,EVENTS_AWARDED=?,OVERALL_AWARDED=?,BEST_NEW_TEAM_FLAG=?,MOST_IMPROVED_FLAG=?,LINKED_TOURN_1=".$tourn1Linked.",LINKED_TOURN_2=".$tourn2Linked.",SCORES_LOCKED_FLAG=?,HIGHEST_SCORE_POSSIBLE_ALT=?,ADDITIONAL_POINTS_NP=?,ADDITIONAL_POINTS_DQ=?,EVENTS_AWARDED_ALT=?,OVERALL_AWARDED_ALT=?,TEAM_LIST_1_TEXT=?,TEAM_LIST_2_TEXT=?,STATE_BID_COUNT=?,PREVIOUS_YEAR_TOURN=".$previousTournLinked." WHERE TOURNAMENT_ID=".$_SESSION["tournamentId"]);
			
			$query->bind_param('ssssiiisiiiiiiiiiiissi',$name,$location, $division,$date, $numberEvents, $numberTeams, $highestScore, $description, $highLowWins,$eventsAwarded, $overallAwarded, $bestNewTeam, $improvedTeam,$lockScoresFlag,$highestScoreAlt,$pointsForNP,$pointsForDQ,$eventsAAwarded,$overallAAwarded,$teamList1Text,$teamList2Text,$stateBids);
			$query->execute();
			$query->free_result();
			//$result = mysql_query($query);		

		
		}
	
	
		// save events
		// 0: EVENT_ID 1: NAME 2:TRIAL_EVENT 3: TOURN_EVENT_ID 4: New Event 0/1 5: USER_ID 6: USER NAME 7: PRIM_ALT_FLAG
		$eventList = $_SESSION["eventList"];
		if ($eventList) {
			foreach ($eventList as $event) {
			$supervisor = $event['5']; // Supervisor Array	
			$userId = $supervisor['0']; if ($userId == '') $userId = null;
			
			if ($event['3'] == null or $event['3'] == '') {
				// Generate Next TOURN_EVENT_ID
				$result = $mysqli->query("select max(TOURN_EVENT_ID) + 1 from TOURNAMENT_EVENT");
				$row = $result->fetch_row();
				$id = 0;
				if ($row['0'] != null) $id = $row['0']; 
				
				$query = $mysqli->prepare("INSERT INTO TOURNAMENT_EVENT (TOURN_EVENT_ID, TOURNAMENT_ID, EVENT_ID, TRIAL_EVENT_FLAG, USER_ID, PRIM_ALT_FLAG) VALUES (".$id.", ?, ?, ?, ?,?) ");
				$query->bind_param('iiiii',$_SESSION["tournamentId"],$event['0'], $event['2'], $userId,$event['7']); 
				$query->execute();
			}
			else {
				$query = $mysqli->prepare("UPDATE TOURNAMENT_EVENT SET TRIAL_EVENT_FLAG=?, USER_ID=?, PRIM_ALT_FLAG=? WHERE TOURN_EVENT_ID=".$event['3']);			
				$query->bind_param('iii',$event['2'], $userId, $event['7']);
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
				$query->bind_param('iisiii',$_SESSION["tournamentId"],$team['0'], $team['2'], $team['3'],$bestNew,$mostImproved); 
				$query->execute();
			}
			else {
				$query = $mysqli->prepare("UPDATE TOURNAMENT_TEAM SET TEAM_NUMBER=?, ALTERNATE_FLAG=?,BEST_NEW_TEAM_FLAG=?,MOST_IMPROVED_TEAM_FLAG=? WHERE TOURN_TEAM_ID=".$team['4']);			
				$query->bind_param('siii',$team['2'], $team['3'],$bestNew,$mostImproved);
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
					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID
					AND (TES.SCORE IS NOT NULL OR (TES.RAW_SCORE IS NOT NULL AND TES.RAW_SCORE != '')) 									
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
		$primAltFlag = 0; 
		 $result = $mysqli->query("SELECT E.NAME, T.HIGHEST_SCORE_POSSIBLE, T.TOURNAMENT_ID, T.DIVISION, T.NAME,TE.SUBMITTED_FLAG,TE.VERIFIED_FLAG,
							CONCAT(U.FIRST_NAME, ' ', U.LAST_NAME, ' - ',U.USERNAME,' - ',coalesce(U.PHONE_NUMBER,'')) as supervisor, T.DATE, T.HIGH_LOW_WIN_FLAG, TE.COMMENTS, 
							E.SCORE_SYSTEM_CODE, RD.DISPLAY_TEXT, T.HIGHEST_SCORE_POSSIBLE_ALT, T.ADDITIONAL_POINTS_NP, T.ADDITIONAL_POINTS_DQ, TE.PRIM_ALT_FLAG 
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
 				
 				$_SESSION["highestScoreAlt"] = $tournamentRow['13'];
 				$_SESSION["pointsForNP"] = $tournamentRow['14'];
 				$_SESSION["pointsForDQ"] = $tournamentRow['15'];
 				$primAltFlag = $tournamentRow['16'];
    		}    		
    		// PRIM_ALT_FLAG
    		$primAltSql = " IN (0) ";
    		if ($primAltFlag == 1) $primAltSql = " IN (0,1) ";
    	
		// Primary Teams
    	 $result = $mysqli->query("SELECT T.NAME, TT.TEAM_NUMBER, TES.SCORE, TES.TEAM_EVENT_SCORE_ID, TT.TOURN_TEAM_ID, TES.POINTS_EARNED, TES.RAW_SCORE, TES.TIER_TEXT, 								TES.TIE_BREAK_TEXT, TES.TEAM_STATUS 
    	 					FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID AND TT.ALTERNATE_FLAG ".$primAltSql." 
    	 					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID AND TES.TOURN_EVENT_ID = " .$_SESSION["tournEventId"].
    	 					" WHERE TT.TOURNAMENT_ID = " .$_SESSION["tournamentId"]. " ORDER BY if(CAST(TT.TEAM_NUMBER AS UNSIGNED)=0,1,0), CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC, TEAM_NUMBER "); 
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
			
		    // PRIM_ALT_FLAG
    		$primAltSql = " IN (1) ";
    		if ($primAltFlag == 1) $primAltSql = " IN (-1) ";
    		
		// Alternate Teams
		$_SESSION["teamAlternateEventScoreList"] = null;
		    $result = $mysqli->query("SELECT T.NAME, TT.TEAM_NUMBER, TES.SCORE, TES.TEAM_EVENT_SCORE_ID, TT.TOURN_TEAM_ID, TES.POINTS_EARNED, TES.RAW_SCORE, TES.TIER_TEXT, 								TES.TIE_BREAK_TEXT, TES.TEAM_STATUS 
    	 					FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID AND TT.ALTERNATE_FLAG ".$primAltSql."
    	 					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID AND TES.TOURN_EVENT_ID = " .$_SESSION["tournEventId"].
    	 					" WHERE TT.TOURNAMENT_ID = " .$_SESSION["tournamentId"]. " ORDER BY if(CAST(TT.TEAM_NUMBER AS UNSIGNED)=0,1,0), CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC, TEAM_NUMBER "); 
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
			foreach ($scoreList as &$score) {
				$value = $_GET['teamScore'.$teamCount];
				$rawScore = $_GET['teamRawScore'.$teamCount];
				$status = $_GET['teamStatus'.$teamCount];
				$tier = $_GET['teamScoreTier'.$teamCount];
				$tieBreak = $_GET['teamTieBreak'.$teamCount];
				$pointsEarned = $_GET['teamPointsEarned'.$teamCount];
				
				$score['2'] = $value;
				$score['5'] = $pointsEarned;
				$score['6'] = $rawScore;
				$score['7'] = $tier;
				$score['8'] = $tieBreak;
				$score['9'] = $status;
								
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
				$_SESSION["teamEventScoreList"] = $scoreList;
		}
		
	// Alternate Teams
		$scoreList = $_SESSION["teamAlternateEventScoreList"];
		if ($scoreList) {
			$teamCount = 0;
			foreach ($scoreList as &$score) {
				$value = $_GET['teamAScore'.$teamCount];
				$status = $_GET['teamAStatus'.$teamCount];
				$rawScore = $_GET['teamARawScore'.$teamCount];
				$tier = $_GET['teamAScoreTier'.$teamCount];
				$tieBreak = $_GET['teamATieBreak'.$teamCount];
				$pointsEarned = $_GET['teamAPointsEarned'.$teamCount];
				
				$score['2'] = $value;
				$score['5'] = $pointsEarned;
				$score['6'] = $rawScore;
				$score['7'] = $tier;
				$score['8'] = $tieBreak;
				$score['9'] = $status;
				
				
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
			$_SESSION["teamAlternateEventScoreList"] = $scoreList;
		}
	
	
		// Update Submitted/Verified Flags
		$query = $mysqli->prepare("UPDATE TOURNAMENT_EVENT SET SUBMITTED_FLAG=?, VERIFIED_FLAG=?, COMMENTS=? WHERE TOURN_EVENT_ID=".$_SESSION["tournEventId"]);			
		$query->bind_param('iis',$_GET['submittedFlag'], $_GET['verifiedFlag'], $_GET['eventComments']); 
		$query->execute();
		$_SESSION["submittedFlag"] = $_GET['submittedFlag'];
 		$_SESSION["verifiedFlag"] = $_GET['verifiedFlag'];
		$_SESSION["eventComments"] = $_GET['eventComments'];
			
		$_SESSION['scorecenter_msgs'] = array(SUCCESS_EVENT_SCORES_SAVED);
	}


	// MANAGE EVENTS SCREEN ---------------------------------------
	function loadAllEvents($mysqli) {
			// Set 'FILTER EVENTS' Filter to Default 
			if ($_SESSION["filterMyEvents"] == null) {
				if (getCurrentRole() == 'SUPERUSER') $_SESSION["filterMyEvents"] = 'OFFICIAL';
				else $_SESSION["filterMyEvents"] = 'OFFICIAL';
			}
			
			$eventList = array();
			$query = "SELECT *, CASE WHEN CREATED_BY=".getCurrentUserId()." OR 'SUPERUSER'='".getCurrentRole()."' THEN 1 ELSE 0 END AS EDIT_ACCESS FROM EVENT WHERE 1=1 ";
			
			if ($_SESSION["eventFilterName"] != null) {
				$query = $query . " AND NAME LIKE '".$_SESSION["eventFilterName"]."%' " ;
			}
			if ($_SESSION["filterMyEvents"] != null and $_SESSION["filterMyEvents"] == 'MY') {
				$query = $query . " AND CREATED_BY = '".getCurrentUserId()."' " ;
			}
			else if ($_SESSION["filterMyEvents"] != null and $_SESSION["filterMyEvents"] == 'OFFICIAL') {
				$query = $query . " AND OFFICIAL_EVENT_FLAG = 1 " ;
			}
			
			
			$query = $query . " ORDER BY NAME ASC ";
			if ($_SESSION["eventFilterNumber"] != null) {
				$query = $query . " LIMIT ".$_SESSION["eventFilterNumber"];
			}
			
			$result = $mysqli->query($query); 
 			if ($result) {
				while($eventRow = $result->fetch_array(MYSQLI_BOTH)) {
 					$eventRecord = array();	
					array_push($eventRecord, $eventRow[0]);
					array_push($eventRecord, $eventRow[1]);
					array_push($eventRecord, $eventRow['EDIT_ACCESS']);
					array_push($eventList, $eventRecord);
				}
			}
		
		
		$_SESSION["eventsList"] = $eventList;
	}
	
	function clearEvent() {
		$_SESSION["eventId"] = null;
		$_SESSION["eventName"] = null;
		$_SESSION["eventDescription"] = null;
		$_SESSION["disableRecord"] = 0;
		$_SESSION["officialEventFlag"] = 0;
	}


	function loadEvent($id, $mysqli) {
		$result = $mysqli->query("SELECT * FROM EVENT WHERE EVENT_ID = " .$id); 
 			if ($result) {
 				$eventRow = $result->fetch_array(MYSQLI_BOTH);	
 				$_SESSION["eventId"] = $eventRow['0'];
 				$_SESSION["eventName"] = $eventRow['1'];
 				$_SESSION["eventDescription"] = $eventRow['2'];
 				$_SESSION["scoreSystemCode"] = $eventRow['3'];
 				$_SESSION["officialEventFlag"] = $eventRow['OFFICIAL_EVENT_FLAG'];
 							
    		}
	}


	function saveEvent($mysqli) {
		if ($_SESSION["officialEventFlag"] == null) $_SESSION["officialEventFlag"] = 0;
		
		// if Event id is null create new
		if ($_SESSION["eventId"] == null) { 
			$result = $mysqli->query("select max(EVENT_ID) + 1 from EVENT");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null and $row['0'] != null) $id = $row['0'];  
			$_SESSION["eventId"] = $id;
			
			$query = $mysqli->prepare("INSERT INTO EVENT (EVENT_ID, NAME, COMMENTS, SCORE_SYSTEM_CODE, CREATED_BY,OFFICIAL_EVENT_FLAG) VALUES (".$id.", ?, ?,?, ?,?) ");
			
			$query->bind_param('sssii',$_GET["eventName"], $_GET["eventDescription"], $_GET["scoreSystemCode"], getCurrentUserId(), $_GET["officialEventFlag"]);
			
			$query->execute();
			$query->free_result();
		}
		else {
			$query = $mysqli->prepare("UPDATE EVENT SET NAME=?, COMMENTS=?, SCORE_SYSTEM_CODE=?, OFFICIAL_EVENT_FLAG=?  WHERE EVENT_ID=".$_SESSION["eventId"]);
			
			$query->bind_param('sssi',$_GET["eventName"], $_GET["eventDescription"], $_GET["scoreSystemCode"], $_GET["officialEventFlag"]);
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
	
	function generateEventAwards($mysqli, $eventId) {
		$primQuery = " select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED 
					from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					INNER JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 
					AND (TT1.ALTERNATE_FLAG = 0 or COALESCE(TE.PRIM_ALT_FLAG,0) = 1)
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURN_EVENT_ID=".$eventId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
		
		$altQuery = " select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED_ALT
					from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					INNER JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 
					AND TT1.ALTERNATE_FLAG = 1 AND COALESCE(TE.PRIM_ALT_FLAG,0) <> 1
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED_ALT #AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURN_EVENT_ID=".$eventId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE ORDER BY UPPER(E.NAME) ASC, SCORE = 0, SCORE ASC ";	
					
		$tournQuery = "	SELECT T.NAME, T.DIVISION, DATE_FORMAT(T.DATE, '%m-%d-%Y'), E.NAME FROM TOURNAMENT T
						INNER JOIN TOURNAMENT_EVENT TE on TE.TOURNAMENT_ID=T.TOURNAMENT_ID
						INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID
						WHERE TE.TOURN_EVENT_ID=".$eventId;			
		
		$result1 = $mysqli->query($primQuery);		
		$result2 = $mysqli->query($altQuery);	
		$result3 = $mysqli->query($tournQuery);
		
		$row = $result3->fetch_row();
		
		$pdf = new FPDF();
		$pdf->SetTitle($row[3], true);
		$pdf->AddPage('P','Letter', 0);
		$pdf->SetAutoPageBreak(True, 2);
		$pdf->SetFont('Arial','B',40);
		$pdf->MultiCell(0,16,$row[3].' - '. $row[1],0,'C',false);
		//$pdf->Ln(8);
		$pdf->SetFont('Arial','',12);
		$pdf->MultiCell(0,16,$row[0].' - '. $row[2],0,'C',false);
		//$pdf->Ln(8);
		$pdf->SetFont('Arial','',16);
		$pdf->Cell(0,10, 'Primary Teams:',0,0,'L');
		$pdf->Ln(12);
		if ($result1->num_rows == 0) {
			$pdf->SetFont('Arial','',16);
			$pdf->Cell(0,10, 'No Results Available',0,0,'L');
			$pdf->Ln(12);
		}
		else {
			while($teamRow = $result1->fetch_array()) {
				if ($teamRow[2] != 0) {
					$pdf->SetFont('Arial','',24);
					$pdf->Cell(0,10, $teamRow[2].'. '.$teamRow[1],0,0,'L');
					$pdf->Ln(12);
				}
			}
			if ($result2->num_rows != 0) {
				$pdf->SetFont('Arial','',16);
				$pdf->Cell(0,10, 'Alternate Teams:',0,0,'L');
				$pdf->Ln(12);
			}
			while($teamRow = $result2->fetch_array()) {
				if ($teamRow[2] != 0) {
					$pdf->SetFont('Arial','',24);
					$pdf->Cell(0,10, $teamRow[2].'. '.$teamRow[1],0,0,'L');
					$pdf->Ln(12);
				}
			}
		}
		
				
		$pdf->Output('D', str_replace(' ','',$row[3]).'_Awards.pdf',true);		
	}
	
	function exportEventScores($mysqli, $id) {
		$tournQuery = "	SELECT T.NAME, T.DIVISION, DATE_FORMAT(T.DATE, '%m/%d/%y'),
						CASE WHEN TE.TRIAL_EVENT_FLAG = 1 THEN CONCAT(E.NAME ,'*')
						ELSE E.NAME
						END AS ENAME,
						TE.COMMENTS FROM TOURNAMENT T
						INNER JOIN TOURNAMENT_EVENT TE on TE.TOURNAMENT_ID=T.TOURNAMENT_ID
						INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID
						WHERE TE.TOURN_EVENT_ID=".$id;	
						
		$queryPrimary = "SELECT TT.TEAM_NUMBER,
				T.NAME AS TNAME, TES.TEAM_STATUS, RAW_SCORE,
				CASE WHEN TES.TIER_TEXT IS NULL THEN NULL WHEN TES.TIER_TEXT = 1 THEN 'I' WHEN TES.TIER_TEXT = 2 THEN 'II'
				WHEN TES.TIER_TEXT = 3 THEN 'III' WHEN TES.TIER_TEXT = 4 THEN 'IV' WHEN TES.TIER_TEXT = 5 THEN 'V'
				END AS TIER_TEXT,
				TES.TIE_BREAK_TEXT, TES.SCORE, TES.POINTS_EARNED
				FROM TOURNAMENT_EVENT TE
				INNER JOIN TOURNAMENT_TEAM TT ON TT.TOURNAMENT_ID=TE.TOURNAMENT_ID
				LEFT JOIN TEAM_EVENT_SCORE TES ON TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID
				INNER JOIN TEAM T ON T.TEAM_ID=TT.TEAM_ID
				INNER JOIN EVENT E ON E.EVENT_ID=TE.EVENT_ID
				INNER JOIN TOURNAMENT TN ON TN.TOURNAMENT_ID=TE.TOURNAMENT_ID
				WHERE TE.TOURN_EVENT_ID=".$id." AND COALESCE(TT.ALTERNATE_FLAG,0) = 0 ORDER BY CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC ";	
				
		$queryAlternate = "SELECT TT.TEAM_NUMBER,
				CONCAT(T.NAME,' +') AS TNAME, TES.TEAM_STATUS, RAW_SCORE,
				CASE WHEN TES.TIER_TEXT IS NULL THEN NULL WHEN TES.TIER_TEXT = 1 THEN 'I' WHEN TES.TIER_TEXT = 2 THEN 'II'
				WHEN TES.TIER_TEXT = 3 THEN 'III' WHEN TES.TIER_TEXT = 4 THEN 'IV' WHEN TES.TIER_TEXT = 5 THEN 'V'
				END AS TIER_TEXT,
				TES.TIE_BREAK_TEXT, TES.SCORE, TES.POINTS_EARNED  
				FROM TOURNAMENT_EVENT TE
				INNER JOIN TOURNAMENT_TEAM TT ON TT.TOURNAMENT_ID=TE.TOURNAMENT_ID
				LEFT JOIN TEAM_EVENT_SCORE TES ON TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TES.TOURN_TEAM_ID=TT.TOURN_TEAM_ID
				INNER JOIN TEAM T ON T.TEAM_ID=TT.TEAM_ID
				INNER JOIN EVENT E ON E.EVENT_ID=TE.EVENT_ID
				INNER JOIN TOURNAMENT TN ON TN.TOURNAMENT_ID=TE.TOURNAMENT_ID
				WHERE TE.TOURN_EVENT_ID=".$id." AND COALESCE(TT.ALTERNATE_FLAG,0) = 1 ORDER BY CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC ";	
		
		$result = $mysqli->query($tournQuery);
		$row = $result->fetch_row();
		$eventName = $row[3];
		$tournamentName = $row[0];
		$division = $row[1];
		$date = $row[2];
		$comments = $row[4];
		
		$filename = $eventName.'_'.$division."_Results". ".csv";
		$title = $tournamentName.' - '.$date.' - '.$eventName.' '.$division.' Results';
		
		$filename = str_replace(" ", "_", $filename);$filename = str_replace("/", "", $filename);$filename = str_replace("*", "", $filename);
		$filename = str_replace("\\", "", $filename);$filename = str_replace("'", "", $filename);
	
  		header("Content-Disposition: attachment; filename=\"$filename\"");
  		header("Content-Type: text/csv; charset=utf-8");
  		
  		$output = fopen('php://output', 'w');	
  		
  		$header = array();
  		array_push($header, '');
  		array_push($header, $title);
  		fputcsv($output, $header);
  		
  		$headings = array();
  		array_push($headings, 'Team Number');
  		array_push($headings, 'Team Name');
  		array_push($headings, 'Status');
  		array_push($headings, 'Raw Score');
  		array_push($headings, 'Tier/Rank Group');
  		array_push($headings, 'Tie Break Rank');
  		array_push($headings, 'Rank');
  		array_push($headings, 'Points Earned');				 
		fputcsv($output, $headings);							 
		
		$space = array();
		array_push($space, 'Primary Teams:');
		fputcsv($output, $space);
		
		$result = $mysqli->query($queryPrimary);	
		while ($row = $result->fetch_assoc()) {
			fputcsv($output, $row);
		}
		
		$space = array();
		array_push($space, 'Alternate Teams:');
		fputcsv($output, $space);
		
		$result = $mysqli->query($queryAlternate);	
		while ($row = $result->fetch_assoc()) {
			fputcsv($output, $row);	
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




	// MANAGE TEAMS SCREEN ---------------------------------------
	function clearTeam() {
		$_SESSION["teamId"] = null;
		$_SESSION["teamName"] = null;
		$_SESSION["teamCity"] = null;
		$_SESSION["teamPhone"] = null;
		$_SESSION["teamEmail"] = null;
		$_SESSION["teamDivision"] = null;
		$_SESSION["teamState"] = null;
		$_SESSION["teamRegion"] = null;
		$_SESSION["teamDescription"] = null;
		$_SESSION["disableRecord"] = 0;
		$_SESSION["coachList"] = array();
	}
	
	function loadAllTeams($mysqli) {
			// Set 'My Teams' & State Filter to Default 
			if ($_SESSION["filterState"] === null) {
				$_SESSION["filterState"] = getUserState();
			}
			if ($_SESSION["filterMyTeams"] === null) {
				if (getCurrentRole() == 'SUPERUSER') $_SESSION["filterMyTeams"] = 'NO';
				else $_SESSION["filterMyTeams"] = 'NO';
			}
			
			$teamList = array();
			$query = "SELECT T.*, RD.DISPLAY_TEXT as D1, RD1.DISPLAY_TEXT as D2, CASE WHEN T.CREATED_BY=".getCurrentUserId()." OR 'SUPERUSER'='".getCurrentRole()."' THEN 1 ELSE 0 END AS EDIT_ACCESS FROM TEAM T 
			LEFT JOIN REF_DATA RD on RD.REF_DATA_CODE=T.STATE AND RD.DOMAIN_CODE='STATE' 
			LEFT JOIN REF_DATA RD1 on RD1.REF_DATA_CODE=T.REGION AND RD1.DOMAIN_CODE='REGION'
			WHERE 1=1  ";
			$params = array();
			if ($_SESSION["teamFilterName"] != null) {
				$query = $query . " AND NAME LIKE ? " ;
				array_push($params, $_SESSION["teamFilterName"].'%');
			}
			if ($_SESSION["filterDivision"] != null and $_SESSION["filterDivision"] != '') {
				$query = $query . " AND DIVISION = '".$_SESSION["filterDivision"]."' " ;
			}
			if ($_SESSION["filterState"] != null and $_SESSION["filterState"] != '') {
				$query = $query . " AND STATE = '".$_SESSION["filterState"]."' " ;
			}
			if ($_SESSION["filterRegion"] != null and $_SESSION["filterRegion"] != '') {
				$query = $query . " AND REGION = '".$_SESSION["filterRegion"]."' " ;
			}
			if ($_SESSION["filterMyTeams"] != null and $_SESSION["filterMyTeams"] == 'YES') {
				$query = $query . " AND CREATED_BY = '".getCurrentUserId()."' " ;
			}
			
			$query = $query . " ORDER BY NAME ASC ";
			if ($_SESSION["teamFilterNumber"] != null) {
				$query = $query . " LIMIT ".$_SESSION["teamFilterNumber"];
			}
		
			
			$query = $mysqli->prepare($query);
			bindMysqliParams($query,$params);
			$query->execute();
			$result = $query->get_result();
			 
 			if ($result) {
				while($teamRow = $result->fetch_array(MYSQLI_BOTH)) {
 					$teamRecord = array();	
					array_push($teamRecord, $teamRow[0]);
					array_push($teamRecord, $teamRow[1]);
					array_push($teamRecord, $teamRow[6]);
					array_push($teamRecord, $teamRow['EDIT_ACCESS']);
					array_push($teamRecord, $teamRow['D1']);
					array_push($teamRecord, $teamRow['D2']);
					array_push($teamList, $teamRecord);
				}
			}
		$_SESSION["resultsPage"] = 1;
		$_SESSION["teamsList"] = $teamList;
	}
	
	function loadTeam($id, $mysqli) {
		$result = $mysqli->query("SELECT * FROM TEAM WHERE TEAM_ID = " .$id); 
 			if ($result) {
 				$teamRow = $result->fetch_array(MYSQLI_BOTH);	
 				$_SESSION["teamId"] = $teamRow[0];
 				$_SESSION["teamName"] = $teamRow[1];
 				$_SESSION["teamCity"] = $teamRow[2]; 
				$_SESSION["teamDivision"] = $teamRow[6];
				$_SESSION["teamState"] = $teamRow['STATE'];
				$_SESSION["teamRegion"] = $teamRow['REGION'];
				if ($_SESSION["disableRecord"] != 1) {
					$_SESSION["teamPhone"] = $teamRow[4];
					$_SESSION["teamEmail"] = $teamRow[3];
					$_SESSION["teamDescription"] = $teamRow[5];
				}	
				
				$_SESSION["coachList"] = array();	
				$result = $mysqli->query("select TC.TEAM_COACH_ID, U.USER_ID, U.FIRST_NAME, U.LAST_NAME,U.USERNAME FROM USER U INNER JOIN TEAM_COACH TC ON  TC.USER_ID=U.USER_ID WHERE TC.TEAM_ID=" .$id); 
				if ($result) {
					$coachList = array();
 					while ($row = $result->fetch_array(MYSQLI_BOTH)) {
	 					$coach = array();
	 					array_push($coach, $row['TEAM_COACH_ID']);
	 					array_push($coach, $row['TEAM_ID']);
	 					array_push($coach, $row['USER_ID']);
	 					array_push($coach, $row['LAST_NAME'].', '.$row['FIRST_NAME']);
	 					array_push($coach, $row['USERNAME']);
	 					array_push($coachList, $coach);	
 					}	
 					$_SESSION["coachList"] = $coachList;
 				}
    		}
    		
	}
	
	function saveTeam($mysqli) {
		// if Event id is null create new
		if ($_SESSION["teamId"] == null) { 
			$result = $mysqli->query("select max(TEAM_ID) + 1 from TEAM");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null and $row['0'] != null) $id = $row['0'];  
			$_SESSION["teamId"] = $id;
			
			$query = $mysqli->prepare("INSERT INTO TEAM (TEAM_ID, NAME, CITY, PHONE_NUMBER, EMAIL_ADDRESS, DESCRIPTION, DIVISION, CREATED_BY, STATE, REGION) VALUES (".$id.",?,?,?,?,?,?,?,?,?) ");
			
			$query->bind_param('ssssssiss',$_GET["teamName"], $_GET["teamCity"],$_GET["teamPhone"],$_GET["teamEmail"], $_GET["teamDescription"], $_GET["teamDivision"], getCurrentUserId(),$_GET["teamState"],$_GET["teamRegion"]);
			
			$query->execute();
			$query->free_result();
		}
		else {
			$query = $mysqli->prepare("UPDATE TEAM SET NAME=?, CITY=?, PHONE_NUMBER=?, EMAIL_ADDRESS=?, DESCRIPTION=?, DIVISION=?, STATE=?, REGION=? WHERE TEAM_ID=".$_SESSION["teamId"]);
			
			$query->bind_param('ssssssss',$_GET["teamName"], $_GET["teamCity"],$_GET["teamPhone"],$_GET["teamEmail"], $_GET["teamDescription"], $_GET["teamDivision"],$_GET["teamState"],$_GET["teamRegion"]);
			$query->execute();
			$query->free_result();
		}
		
		// Save Coaches
		if ($_SESSION["coachList"]) {
			foreach ($_SESSION["coachList"] as $coach) {
				if ($coach[0] == null || $coach[0] == '') { 
					$result = $mysqli->query("select max(TEAM_COACH_ID) + 1 from TEAM_COACH");
					$row = $result->fetch_row(); 
					$id = 0;
					if ($row != null and $row['0'] != null) $id = $row['0'];  
		
					$query = $mysqli->prepare("INSERT INTO TEAM_COACH (TEAM_COACH_ID, TEAM_ID, USER_ID) VALUES (".$id.",?,?) ");
					$query->bind_param('ii',$_SESSION["teamId"], $coach[2]);
					
					$query->execute();
					$query->free_result();			
				}
				else {
					
				}
			}
		}
		
		//  
		
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
	
	function processAccountNavigation($navigationHandler) {
		if ($navigationHandler) {
			
			if ($navigationHandler->command == 'selectCoach') {				
				addCoach($_SESSION["userId"].'-'.$_SESSION["lastName"].', '.$_SESSION["firstName"].'-'.$_SESSION["userName"]);
				$navigationHandler->fromPath = 'team_detail.php';
			}
			else if ($navigationHandler->command == 'selectSupervisor') {
				addSupervisor($_SESSION["userId"].'-'.$_SESSION["lastName"].', '.$_SESSION["firstName"].'-'.$_SESSION["userName"]);
				$navigationHandler->fromPath = 'tournament_detail.php';
			}
			else if ($navigationHandler->command == 'selectVerifier') {
				addVerifier($_SESSION["userId"].'-'.$_SESSION["lastName"].', '.$_SESSION["firstName"].'-'.$_SESSION["userName"]);
				$navigationHandler->fromPath = 'tournament_detail.php';
			}
		}
		$_SESSION["userId"] = null;
	}
	
	function addCoach($str) {
		// TEAM_COACH_ID, TEAM_ID, USER_ID, NAME, USER_EMAIL	
		$coachList = $_SESSION["coachList"];
		if (!$coachList) $coachList = array();
		
		// Add coach if they are not already added 
		$values = '';		
		if ($_GET['selectUser']) $values = explode('-', $_GET['selectUser']);
		else if ($str) $values = explode('-',$str);
		
		// Validate Coach has not already been linked
		$exits = false;
		foreach ($coachList as $coach) {
			if ($coach[2] == $values[0]) {
				$exists = true;
				$_SESSION['duplicateCoachError'] = 1;
				break;
			}
		}
		
		if (!$exists) {
			// Give Coach Role if does not exist
			addRole($values[0],'COACH');
			$coach = array();
			array_push($coach, '');
			array_push($coach, '');
			array_push($coach, $values[0]);
			array_push($coach, $values[1]);
			array_push($coach, $values[2]);
			
			array_push($coachList, $coach);
			$_SESSION["coachList"] = $coachList;
		}		
	}
	
	function deleteCoach($mysqli, $userId) {
		$coachList = $_SESSION["coachList"];
		$coachCount = 0;
		if ($coachList) {
			foreach ($coachList as $coach) {
				if ($userId == $coach[2]) {
					unset($coachList[$coachCount]);
					$_SESSION["coachList"] = $coachList;
					if ($coach[0] != null AND $coach[0] != '') {
						$query = $mysqli->prepare("DELETE FROM TEAM_COACH WHERE TEAM_COACH_ID = ?");	
						$query->bind_param('i',$coach[0]);	
						$query->execute();						
					}
					
					break;
				}
				$coachCount++;
			}
		}
	}
	
	function cacheTeam() {
 		$_SESSION["teamName"] = $_GET["teamName"];
 		$_SESSION["teamCity"] = $_GET["teamCity"]; 
		$_SESSION["teamDivision"] = $_GET["teamDivision"];
		$_SESSION["teamState"] = $_GET["teamState"];
		$_SESSION["teamRegion"] = $_GET["teamRegion"];
		$_SESSION["teamPhone"] = $_GET["teamPhone"];
		$_SESSION["teamEmail"] = $_GET["teamEmail"];
		$_SESSION["teamDescription"] = $_GET["teamDescription"];		
	}
	
	function isTeamCreated($mysqli) {
		$_SESSION["teamName"] = $_GET["teamName"];
		$_SESSION["teamDescription"] = $_GET["teamDescription"];
		$_SESSION["teamCity"] = $_GET["teamCity"];
		$_SESSION["teamPhone"] = $_GET["teamPhone"];
		$_SESSION["teamEmail"] = $_GET["teamEmail"];
		$_SESSION["teamDivision"] = $_GET["teamDivision"];
		$_SESSION["teamState"] = $_GET["teamState"];
		$_SESSION["teamRegion"] = $_GET["teamRegion"];
		
		$id = -1;
		if ($_SESSION["teamId"] != null and $_SESSION["teamId"] != '') $id = $_SESSION["teamId"];
		
		$result = $mysqli->query("SELECT TEAM_ID FROM TEAM WHERE TEAM_ID <> ".$id." AND UPPER(NAME) = '".strtoupper($_GET["teamName"])."' AND DIVISION='".$_GET["teamDivision"]."'"); 
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
		$query1 = "SELECT E.NAME, TE.TOURN_EVENT_ID, TE.TRIAL_EVENT_FLAG, TM.HIGH_LOW_WIN_FLAG,
					CASE WHEN TM.NUMBER_TEAMS = COUNT(TES.TEAM_EVENT_SCORE_ID) AND TE.VERIFIED_FLAG = 1 THEN 1 ELSE 0 END AS COMPLETED_FLAG
					FROM TOURNAMENT TM
					INNER JOIN TOURNAMENT_EVENT TE ON TE.TOURNAMENT_ID=TM.TOURNAMENT_ID 
					INNER JOIN EVENT E ON E.EVENT_ID=TE.EVENT_ID 
					LEFT JOIN TEAM_EVENT_SCORE TES on TES.TOURN_EVENT_ID=TE.TOURN_EVENT_ID
					AND (TES.SCORE IS NOT NULL OR (TES.RAW_SCORE IS NOT NULL AND TES.RAW_SCORE != '')) 
					WHERE TM.TOURNAMENT_ID=".$id."
					GROUP BY TOURN_EVENT_ID
					ORDER BY NAME ASC ";
					
		$result = $mysqli->query($query1); 
		$headerList = array();

 		if ($result) {
			while($events = $result->fetch_array()) {
					$eventName = $events['0'];
					if ($events['2'] != null and $events['2'] == 1) $eventName .= ' *';
					array_push($tournamentResultsHeader, $eventName);
					$highLowWin = $events['3'];
					
					$resultHeaderObj = new tournamentResultHeader();
					$resultHeaderObj->eventName = $events['0'];
					$resultHeaderObj->tournEventId = $events['1'];
					$resultHeaderObj->trialEventFlag = $events['2'];
					$resultHeaderObj->completedFlag = $events['4'];
					array_push($headerList, $resultHeaderObj);
			}
		}	
		$_SESSION['tournamentResultsHeader'] = $tournamentResultsHeader;	
		$_SESSION['resultHeaderObj'] = serialize($headerList);
		
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
					 ORDER BY if(CAST(TT.TEAM_NUMBER AS UNSIGNED)=0,1,0), CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC, TEAM_NUMBER ";
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
					 ORDER BY if(CAST(TT.TEAM_NUMBER AS UNSIGNED)=0,1,0), CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC, TEAM_NUMBER ";
		$result = $mysqli->query($query2); 
		//$_SESSION['tournamentAlternateResults'] = null;
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
			
			// Determine Final Rank With Tie Breakers
			$tournamentAlternateResults = quicksort($tournamentAlternateResults, $highLowWin); 
			
			// Set Final Rank VALUE
			$count = 1;
			foreach ($tournamentAlternateResults as $k => $row) {
				$row[sizeof($row)-2] = $count;
				$tournamentAlternateResults[$k] = $row;
				$count++;
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
	
	// Primary Sort: Team Number
	// Secondary Sort: Team Name
	function sortTeamNumberAsc($a, $b) {
		if ($a[1] < $b[1]) return -1;
		if ($a[1] > $b[1]) return 1;
		if ($a[1] === $b[1]) {
			if ($a[2] < $b[2]) return -1;
			if ($a[2] > $b[2]) return 1;
		}
		return 0;
	}
	
	function getMostImprovedTeams($id, $currentResults, $previousResults, $statebids, $highLowWin, $mysqli) {
		if ($statebids === null OR $statebids == '') $statebids = 0;
		$teamList = array();
		$sql = " SELECT TE.TEAM_ID, TE.NAME, TT.TOURN_TEAM_ID as CURID,TT1.TOURN_TEAM_ID as PREVID, '' as difference FROM TEAM TE
				INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=TE.TEAM_ID
				INNER JOIN TOURNAMENT T ON T.TOURNAMENT_ID=TT.TOURNAMENT_ID
				INNER JOIN TOURNAMENT T1 ON T1.TOURNAMENT_ID=T.PREVIOUS_YEAR_TOURN
				INNER JOIN TOURNAMENT_TEAM TT1 ON TT1.TOURNAMENT_ID=T1.TOURNAMENT_ID
				WHERE TT1.TEAM_ID=TT.TEAM_ID AND T.TOURNAMENT_ID=".$id."
				ORDER BY TT.TEAM_NUMBER ASC ";
		$results = $mysqli->query($sql); 
		while ($row = $results->fetch_array(MYSQLI_BOTH)) {
			$currentTTId = $row['CURID'];
			$prevTTId = $row['PREVID'];
			$prevPoints = null;
			$currentPoints = null;
			foreach($currentResults as $result) {
				if ($currentTTId == $result[0] AND $result[sizeof($result)-2] > $statebids) {				
					
					$currentPoints = $result[sizeof($result)-3];
					break;
				}
			}
			foreach($previousResults as $result) {
				if ($prevTTId == $result[0]) {
					$prevPoints = $result[sizeof($result)-3];
					break;
				}
			}
			
			if ($prevPoints AND $currentPoints) {				
				array_push($teamList,array($row['NAME'], $prevPoints-$currentPoints));
			}
		}
		
		if ($highLowWin == 0) usort($teamList, "sortMostImprovedTeamsDesc");
		else if ($highLowWin == 1) usort($teamList, "sortMostImprovedTeamsAsc");
		$finalTeamList = array();
		$number;
		foreach($teamList as $team) {
			if ($number != null AND $number != $team[1]) break;
			$number = $team[1];
			array_push($finalTeamList, $team);
		}
		
		

		return $finalTeamList;
	}
	
	function sortMostImprovedTeamsDesc($a, $b) {
		if ($a[1] > $b[1]) return -1;
		if ($a[1] < $b[1]) return 1;
		return 0;
	}
	function sortMostImprovedTeamsAsc($a, $b) {
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
		$rowCount = 2; // Color Counts
		$colWidth = 1;
		echo '<table id="primaryResultsGrid" class="table table-bordered table-hover tablesorter" style="table-layout:fixed;">';
        echo '<thead> ';
        echo '<tr> ';
		echo '<th '; echo 'width="5%" style="background-color: #'.$_SESSION["primaryColumnColor"].';border-bottom: 1px solid #000000;"'; echo ' class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#</span></div></th>';
		echo '<th '; echo 'style="background-color: #'.$_SESSION["secondaryRowColor"].';border-bottom: 1px solid #000000;"'; echo ' width="20%"><div><span class="sortableTH">'; 
		echo $_SESSION["tournamentName"] .'<br />'; echo 'Division: '.$_SESSION["tournamentDivision"] . '<br />'; echo 'Date: '.$_SESSION["tournamentDate"] .' </span></div></th>';
				$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
				if ($tournamentResultsHeader != null) {
					foreach ($tournamentResultsHeader as $resultHeader) {
						$colWidth = sizeof($tournamentResultsHeader) + 2;
						$colWidth = 75 / $colWidth;
						
						echo '<th width="'.$colWidth.'%" style="border-bottom: 1px solid #000000;'; 
						if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].';';
						else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';
						echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$resultHeader.'</span></div></th>';						
						$rowCount++;
					}
				}

                echo '<th width="'.$colWidth.'%" style="border-bottom: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Score</span></div></th>';
				$rowCount++;
			   echo '<th width="'.$colWidth.'%" style="border-bottom: 1px solid #000000; '; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Final Rank</span></div></th>';
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
			$rowCount = 2; // Color Counts
			$colWidth = 1;
		  $tournamentAlternateResults = $_SESSION['tournamentAlternateResults'];
          if ($tournamentAlternateResults != null) {
			 echo '<table id="alternateResultsGrid" class="table table-bordered table-hover tablesorter" style="table-layout:fixed;">';
			 echo '<thead><tr>';
			 echo '<th width="5%" style="background-color: #'.$_SESSION["primaryColumnColor"].';border-bottom: 1px solid #000000;" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#</span></div></th>';
			 echo '<th style="background-color: #'.$_SESSION["secondaryRowColor"].';border-bottom: 1px solid #000000;" width="20%"><div><span class="sortableTH">'. $_SESSION["tournamentName"] . '<br />Division: '.$_SESSION["tournamentDivision"].' (Alternate)<br /> Date: '.$_SESSION["tournamentDate"] .'</span></div></th> ';

				$tournamentResultsHeader = $_SESSION['tournamentResultsHeader'];
				if ($tournamentResultsHeader != null) {
					foreach ($tournamentResultsHeader as $resultHeader) {
						$colWidth = sizeof($tournamentResultsHeader) + 2;
						$colWidth = 75 / $colWidth;
						
						echo '<th width="'.$colWidth.'%" style="padding-left: none; border-bottom: 1px solid #000000;'; 
						if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].';';
						else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';';
						echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$resultHeader.'</span></div></th>';						
						$rowCount++;
					}
				}
				echo '<th width="'. $colWidth.'%" style="border-bottom: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Score</span></div></th>';
				$rowCount++;
				echo '<th width="'. $colWidth.'%" style="border-bottom: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryColumnColor"].'; '; else echo ' background-color: #'.$_SESSION["secondaryRowColor"].';'; echo '" class="rotate"><div><span class="sortableTH">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Final Rank</span></div></th> ';

			echo '</tr></thead>';
			echo '<tbody>';
			$rowCount = 0;
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
				echo '<td width="20%" style="white-space: nowrap; overflow: hidden;  border-right: 1px solid #000000;'; if ($rowCount % 2 == 0) echo ' background-color: #'.$_SESSION["primaryRowColor"]; else echo ' background-color: #'.$_SESSION["secondaryRowColor"]; echo '"><b>'.$resultRow['2'].'</b></td>';
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
			echo '</tbody>';
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
		$tournamentDetails = $mysqli->query("SELECT T.NAME, T.LOCATION, T.EVENTS_AWARDED, T.OVERALL_AWARDED, T.BEST_NEW_TEAM_FLAG, 
					T.MOST_IMPROVED_FLAG,T.EVENTS_AWARDED_ALT ,T.OVERALL_AWARDED_ALT, T.TEAM_LIST_1_TEXT, T.TEAM_LIST_2_TEXT FROM TOURNAMENT T WHERE T.TOURNAMENT_ID=".$_SESSION["tournamentId"]);
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
		
		$aId = null; $aEvents = array(); $aAltEvents = array(); $aHighLowFlag = 0; $aOverallAwarded = 0; $aOverallAwardedAlt = 0; $cEventAwardedAlt = 0; $aPreviousTournId = -1; $aStateBids = 0;
		$bId = null; $bEvents = array(); $bAltEvents = array(); $bHighLowFlag = 0; $bOverallAwarded = 0; $bOverallAwardedAlt = 0; $bEventAwardedAlt = 0; $bPreviousTournId = -1; $bStateBids = 0;
		$cId = null; $cEvents = array(); $cAltEvents = array(); $cHighLowFlag = 0; $cOverallAwarded = 0; $cOverallAwardedAlt = 0; $aEventAwardedAlt = 0; $cPreviousTournId = -1; $cStateBids = 0;
		
		$query = "select T1.TOURNAMENT_ID, T1.DIVISION, T1.LINKED_TOURN_1, T2.DIVISION, T1.LINKED_TOURN_2, T3.DIVISION, 
				T1.PREVIOUS_YEAR_TOURN,T2.PREVIOUS_YEAR_TOURN,T3.PREVIOUS_YEAR_TOURN,
				T1.STATE_BID_COUNT,T2.STATE_BID_COUNT,T3.STATE_BID_COUNT
				FROM TOURNAMENT T1 
				LEFT JOIN TOURNAMENT T2 on T2.TOURNAMENT_ID=T1.LINKED_TOURN_1
				LEFT JOIN TOURNAMENT T3 on T3.TOURNAMENT_ID=T1.LINKED_TOURN_2
				WHERE T1.TOURNAMENT_ID=".$_SESSION["tournamentId"];
		$tournaments = $mysqli->query($query);
		$row = $tournaments->fetch_row();
		if ($row[1] != null AND $row[1] == 'A') { $aId = $row[0];  $aPreviousTournId = $row[6]; $aStateBids = $row[9]; }
		else if ($row[1] != null AND $row[1] == 'B') { $bId = $row[0]; $bPreviousTournId = $row[6]; $bStateBids = $row[9]; }
		else if ($row[1] != null AND $row[1] == 'C') { $cId = $row[0]; $cPreviousTournId = $row[6]; $cStateBids = $row[9]; }
		
		if ($row[3] != null AND $row[3] == 'A') { $aId = $row[2]; $aPreviousTournId = $row[7]; $aStateBids = $row[10]; }
		else if ($row[3] != null AND $row[3] == 'B') { $bId = $row[2]; $bPreviousTournId = $row[7]; $bStateBids = $row[10]; }
		else if ($row[3] != null AND $row[3] == 'C') { $cId = $row[2]; $cPreviousTournId = $row[7]; $cStateBids = $row[10]; }
		
		if ($row[5] != null AND $row[5] == 'A') { $aId = $row[4]; $aPreviousTournId = $row[8]; $aStateBids = $row[11]; }
		else if ($row[5] != null AND $row[5] == 'B') { $bId = $row[4]; $bPreviousTournId = $row[8]; $bStateBids = $row[11]; }
		else if ($row[5] != null AND $row[5] == 'C') { $cId = $row[4]; $cPreviousTournId = $row[8]; $cStateBids = $row[11]; }
		
		// Primary C
		if ($cId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 AND (TT1.ALTERNATE_FLAG = 0 or COALESCE(TE.PRIM_ALT_FLAG,0) = 1)
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$cId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE,HIGH_LOW_WIN_FLAG,OVERALL_AWARDED ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
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
		
		// Alternate C
		if ($cId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED_ALT, T.EVENTS_AWARDED_ALT from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 AND TT1.ALTERNATE_FLAG = 1 AND COALESCE(TE.PRIM_ALT_FLAG,0) <> 1
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED_ALT #AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$cId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE,HIGH_LOW_WIN_FLAG,OVERALL_AWARDED_ALT,EVENTS_AWARDED_ALT ORDER BY UPPER(E.NAME) ASC, SCORE = 0, SCORE ASC ";
			$results = $mysqli->query($query);
			$event = null;
			$eventArray = array();
			$count = 0;
			while ($row = $results->fetch_array()) {
				//$cHighLowFlag = $row[3];
				$cOverallAwardedAlt = $row[4];
				$cEventAwardedAlt = $row[5];
				if ($event != $row[0] and sizeof($eventArray) > 0) {
					array_unshift($eventArray, $event);
					array_push($cAltEvents, $eventArray);
					$eventArray = array();
					$count = 0;
				}
				$event = $row[0];
				if ($row[2] != 0) array_push($eventArray, $row[1]);
				if ($row[2] == 0 AND sizeof($eventArray) == 0) array_push($eventArray, '');
				$count++;
			}
			if (sizeof($eventArray) > 0) {array_unshift($eventArray, $event); array_push($cAltEvents, $eventArray); }
		}
		
		// Primary B
		if ($bId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 AND (TT1.ALTERNATE_FLAG = 0 or COALESCE(TE.PRIM_ALT_FLAG,0) = 1)
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$bId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE,HIGH_LOW_WIN_FLAG,OVERALL_AWARDED ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
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
		
		// Alternate B
		if ($bId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED_ALT, T.EVENTS_AWARDED_ALT from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 AND TT1.ALTERNATE_FLAG = 1 AND COALESCE(TE.PRIM_ALT_FLAG,0) <> 1
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED_ALT #AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$bId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE,HIGH_LOW_WIN_FLAG,OVERALL_AWARDED_ALT,EVENTS_AWARDED_ALT ORDER BY UPPER(E.NAME) ASC, SCORE = 0, SCORE ASC ";
			$results = $mysqli->query($query);
			$event = null;
			$eventArray = array();
			$count = 0;
			while ($row = $results->fetch_array()) {	
				//$bHighLowFlag = $row[3];
				$bOverallAwardedAlt = $row[4];
				$bEventAwardedAlt = $row[5];
				if ($event != $row[0] and sizeof($eventArray) > 0) {
					array_unshift($eventArray, $event);
					array_push($bAltEvents, $eventArray);
					$eventArray = array();
					$count = 0;
				}
				$event = $row[0];
				if ($row[2] != 0) array_push($eventArray, $row[1]);
				if ($row[2] == 0 AND sizeof($eventArray) == 0) array_push($eventArray, '');			
				$count++;
			}
			if (sizeof($eventArray) > 0) {array_unshift($eventArray, $event); array_push($bAltEvents, $eventArray); }
		}
		
		// Primary A
		if ($aId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 AND (TT1.ALTERNATE_FLAG = 0 or COALESCE(TE.PRIM_ALT_FLAG,0) = 1)
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$aId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE,HIGH_LOW_WIN_FLAG,OVERALL_AWARDED ORDER BY UPPER(E.NAME) ASC, SCORE ASC ";
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
		
		// Alternate A
		if ($aId != null) {
			$query = "select concat(E.NAME,' - ', T.DIVISION) As NAME, T1.NAME as TEAM, TES1.SCORE, T.HIGH_LOW_WIN_FLAG, T.OVERALL_AWARDED_ALT, T.EVENTS_AWARDED_ALT from TOURNAMENT_EVENT TE
					INNER JOIN EVENT E on TE.EVENT_ID=E.EVENT_ID
					INNER JOIN TOURNAMENT T on T.TOURNAMENT_ID=TE.TOURNAMENT_ID
					LEFT JOIN TOURNAMENT_TEAM TT1 on T.TOURNAMENT_ID=TT1.TOURNAMENT_ID AND coalesce(TE.VERIFIED_FLAG,0) = 1 AND TT1.ALTERNATE_FLAG = 1 AND COALESCE(TE.PRIM_ALT_FLAG,0) <> 1
					LEFT JOIN TEAM_EVENT_SCORE TES1 on TES1.TOURN_EVENT_ID=TE.TOURN_EVENT_ID AND TT1.TOURN_TEAM_ID=TES1.TOURN_TEAM_ID 
						AND TES1.SCORE <= T.EVENTS_AWARDED_ALT #AND TES1.SCORE > 0
					LEFT JOIN TEAM T1 ON TT1.TEAM_ID=T1.TEAM_ID
					WHERE TE.TOURNAMENT_ID=".$aId." AND (T1.NAME is null OR (T1.NAME is not null AND TES1.SCORE is not null))
					GROUP BY NAME,TEAM,SCORE,HIGH_LOW_WIN_FLAG,OVERALL_AWARDED_ALT,EVENTS_AWARDED_ALT ORDER BY UPPER(E.NAME) ASC, SCORE = 0, SCORE ASC ";
			$results = $mysqli->query($query);
			$event = null;
			$eventArray = array();
			$count = 0;
			while ($row = $results->fetch_array()) {
				//$aHighLowFlag = $row[3];				
				$aOverallAwardedAlt = $row[4];
				$aEventAwardedAlt = $row[5];
				if ($event != $row[0] and sizeof($eventArray) > 0) {
					array_unshift($eventArray, $event);
					array_push($aAltEvents, $eventArray);
					$eventArray = array();
					$count = 0;
				}
				$event = $row[0];
				if ($row[2] != 0) array_push($eventArray, $row[1]);
				if ($row[2] == 0 AND sizeof($eventArray) == 0) array_push($eventArray, '');			
				$count++;
			}
			if (sizeof($eventArray) > 0) {array_unshift($eventArray, $event); array_push($aAltEvents, $eventArray); }
		}
		
		for ($i=0; $i < 200; $i++) {
			if ($i >= sizeof($cEvents) AND $i >= sizeof($bEvents) AND $i >= sizeof($aEvents)) break;
				if ($i < sizeof($aAltEvents) AND $aEventAwardedAlt != null AND $aEventAwardedAlt > 0) {
					$event = $aAltEvents[$i];
					$slide = new slideshowSlide();
					$slide->setType('EVENTSCORE');		
					$slide->setHeaderText($event[0] . " (ALT)");
					$teamList = array();
					$count2 = 1;
					while ($count2 <  sizeof($event)) {
						if ($event[$count2] != null) array_push($teamList, $count2.'. '.$event[$count2]);
						$count2++;
					}
					$slide->setTeamNames($teamList);
					array_push($resultSlideshow, $slide);
				}
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
				if ($i < sizeof($bAltEvents) AND $bEventAwardedAlt != null AND $bEventAwardedAlt > 0) {
					$event = $bAltEvents[$i];
					$slide = new slideshowSlide();
					$slide->setType('EVENTSCORE');		
					$slide->setHeaderText($event[0] . " (ALT)");
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
				if ($i < sizeof($cAltEvents) AND $cEventAwardedAlt != null AND $cEventAwardedAlt > 0) {
					$event = $cAltEvents[$i];
					$slide = new slideshowSlide();
					$slide->setType('EVENTSCORE');		
					$slide->setHeaderText($event[0] . " (ALT)");
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
			$slide->setType('TEAMLIST');
			$slide->setHeaderText($tournamentRow[8]);
			$labelValues = array();
			$query = "SELECT T.NAME, T.DIVISION FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID INNER JOIN TOURNAMENT TN ON TN.TOURNAMENT_ID=TT.TOURNAMENT_ID WHERE TN.BEST_NEW_TEAM_FLAG=1 AND TT.TOURNAMENT_ID IN (-1";
			if ($aId != null and $aId != '') $query .= ",".$aId;
			if ($bId != null and $bId != '') $query .= ",".$bId;
			if ($cId != null and $cId != '') $query .= ",".$cId;
			$query .= ") AND TT.BEST_NEW_TEAM_FLAG=1 ORDER BY T.DIVISION ASC ";
			$results = $mysqli->query($query);
			while ($row = $results->fetch_array()) {	
				array_push($labelValues, array($row[0]." - Division ".$row[1]));
			}
			$slide->setLabelValues($labelValues);
			
			if (sizeof($labelValues) > 0) {
				array_push($resultSlideshow, $slide);
			}
			
		// Most Improved Team selected
			$slide = new slideshowSlide();
			$slide->setType('TEAMLIST');
			$slide->setHeaderText($tournamentRow[9]);
			$labelValues = array();
			$query = "SELECT T.NAME, T.DIVISION FROM TEAM T INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=T.TEAM_ID INNER JOIN TOURNAMENT TN ON TN.TOURNAMENT_ID=TT.TOURNAMENT_ID WHERE TN.MOST_IMPROVED_FLAG=1 AND TT.TOURNAMENT_ID IN (-1";
			if ($aId != null and $aId != '') $query .= ",".$aId;
			if ($bId != null and $bId != '') $query .= ",".$bId;
			if ($cId != null and $cId != '') $query .= ",".$cId;
			$query .= ") AND TT.MOST_IMPROVED_TEAM_FLAG=1 ORDER BY T.DIVISION ASC ";
			$results = $mysqli->query($query);
			while ($row = $results->fetch_array()) {	
				array_push($labelValues, array($row[0]." - Division ".$row[1]));
			}
			$slide->setLabelValues($labelValues);
			if (sizeof($labelValues) > 0) {
				array_push($resultSlideshow, $slide);
			}
		
		// Load Overall Tournament Results

		$tournamentResultsA = array(); $tournamentResultsB = array(); $tournamentResultsC = array();
		$tournamentResultsAltA = array(); $tournamentResultsAltB = array(); $tournamentResultsAltC = array();
		$previousTournResultsA = array(); $previousTournResultsB = array(); $previousTournResultsC = array();
		
		if ($aId != null) {
		  $tournamentResultsA = getPrimaryTournamentResults($aId,$mysqli,$aHighLowFlag,$tournamentResultsA);
		}
		if ($bId != null) {
		  $tournamentResultsB = getPrimaryTournamentResults($bId,$mysqli,$bHighLowFlag,$tournamentResultsB);
		}
		if ($cId != null) {
		  $tournamentResultsC = getPrimaryTournamentResults($cId,$mysqli,$cHighLowFlag,$tournamentResultsC);
		}
		
		if ($aId != null) {
		  $tournamentResultsAltA = getAlternateTournamentResults($aId,$mysqli,$aHighLowFlag,$tournamentResultsAltA);
		}
		if ($bId != null) {
		  $tournamentResultsAltB = getAlternateTournamentResults($bId,$mysqli,$bHighLowFlag,$tournamentResultsAltB);
		}
		if ($cId != null) {
		  $tournamentResultsAltC = getAlternateTournamentResults($cId,$mysqli,$cHighLowFlag,$tournamentResultsAltC);
		}
					
		// Most Improved Team Auto Calculated
		$aMostImprovedTeams = null;$bMostImprovedTeams = null; $cMostImprovedTeams = null;
		if ($aPreviousTournId AND $aPreviousTournId != -1) {
			$previousTournResultsA = getPrimaryTournamentResults($aPreviousTournId,$mysqli,$aHighLowFlag,$previousTournResultsA);
			$aMostImprovedTeams = getMostImprovedTeams($aId,$tournamentResultsA,$previousTournResultsA,$aStateBids,$aHighLowFlag,$mysqli);
		}
		if ($bPreviousTournId AND $bPreviousTournId != -1) {
			$previousTournResultsB = getPrimaryTournamentResults($bPreviousTournId,$mysqli,$bHighLowFlag,$previousTournResultsB);
			$bMostImprovedTeams = getMostImprovedTeams($bId,$tournamentResultsB,$previousTournResultsB,$bStateBids,$bHighLowFlag,$mysqli);
		}
		if ($cPreviousTournId AND $cPreviousTournId != -1) {
			$previousTournResultsC = getPrimaryTournamentResults($cPreviousTournId,$mysqli,$cHighLowFlag,$previousTournResultsC);
			$cMostImprovedTeams = getMostImprovedTeams($cId,$tournamentResultsC,$previousTournResultsC,$cStateBids,$cHighLowFlag,$mysqli);	
		}
		if ($aMostImprovedTeams OR $bMostImprovedTeams OR $cMostImprovedTeams) {
			$slide = new slideshowSlide();
			$slide->setType('TEAMLIST');
			$slide->setHeaderText('Most Improved Team');
			$labelValues = array();
			if ($aMostImprovedTeams) {
				foreach($aMostImprovedTeams as $team1)
					array_push($labelValues, array($team1[0].' (' .$team1[1].'pts) - Division A'));
			}
			if ($bMostImprovedTeams) {
				foreach($bMostImprovedTeams as $team2)
					array_push($labelValues, array($team2[0].' (' .$team2[1].'pts) - Division B'));
			}
			if ($cMostImprovedTeams) {
				foreach($cMostImprovedTeams as $team3)
					array_push($labelValues, array($team3[0].' (' .$team3[1].'pts) - Division C'));
			}

			
			$slide->setLabelValues($labelValues);
			if (sizeof($labelValues) > 0) {
				array_push($resultSlideshow, $slide);
			}
			
		}
		
		
		if ($tournamentRow[4] == 1 OR $tournamentRow[5] == 1 OR $aMostImprovedTeams OR $bMostImprovedTeams OR $cMostImprovedTeams) {
			// Placeholder Slide
			$slide = new slideshowSlide();
			$slide->setType('PLACEHOLDER');
			$slide->setHeaderText($tournamentRow[0]); // Tournament Name
			$slide->setText('at '.$tournamentRow[1]); // Tournament Location
			$slide->setLogoPath('img/misologo.png');
			array_push($resultSlideshow, $slide);
		}
		
		// Overall Results
		$maxRank = $cOverallAwarded;
		if ($aOverallAwarded > $maxRank) $maxRank = $aOverallAwarded;
		if ($bOverallAwarded > $maxRank) $maxRank = $bOverallAwarded;
		if ($aOverallAwardedAlt > $maxRank) $maxRank = $aOverallAwardedAlt;
		if ($bOverallAwardedAlt > $maxRank) $maxRank = $bOverallAwardedAlt;
		if ($cOverallAwardedAlt > $maxRank) $maxRank = $cOverallAwardedAlt;
		
		for ($i = $maxRank; $i > 0; $i--) {	
			$ordinalSuffix = array('th','st','nd','rd','th','th','th','th','th','th');
			$suffix = 'th';
			if (($i % 100) >= 11 && ($i % 100) <= 13) $suffix = 'th';
			else $suffix = $ordinalSuffix[$i % 10];
			
			// Add Overall Alternate Results Slide if Available
		/**	if (($aOverallAwardedAlt > 0 OR $bOverallAwardedAlt > 0 OR $cOverallAwardedAlt > 0)
				AND ($aOverallAwardedAlt >= $i OR $bOverallAwardedAlt >= $i OR $cOverallAwardedAlt >= $i)) {
				$slide = new slideshowSlide();
				$slide->setType('OVERALLRESULTS');
				$slide->setHeaderText('Final Results - '.$i.$suffix.' Place (ALT)');
				$labelValues = array();
				
				if (sizeof($tournamentResultsAltA) >= $i AND $aOverallAwardedAlt >= $i) {
					$row = $tournamentResultsAltA[$i-1];
					array_push($labelValues, array("Division A", str_replace("+","",$row[2])));
				}
				if (sizeof($tournamentResultsAltB) >= $i AND $bOverallAwardedAlt >= $i) {
					$row = $tournamentResultsAltB[$i-1];
					array_push($labelValues, array("Division B", str_replace("+","",$row[2])));
				}
				if (sizeof($tournamentResultsAltC) >= $i AND $cOverallAwardedAlt >= $i) {
					$row = $tournamentResultsAltC[$i-1];
					array_push($labelValues, array("Division C", str_replace("+","",$row[2])));
				}	
				$slide->setLabelValues($labelValues);
				array_push($resultSlideshow, $slide);	
			} **/
			
			// Add Overall Primary Results Slide
			$slide = new slideshowSlide();
			$slide->setType('OVERALLRESULTS');
			$slide->setHeaderText('Final Results - '.$i.$suffix.' Place');
			$labelValues = array();
		
			if (sizeof($tournamentResultsAltA) >= $i AND $aOverallAwardedAlt >= $i) {
					$row = $tournamentResultsAltA[$i-1];
					array_push($labelValues, array("Division A (ALT)", str_replace("+","",$row[2])));
			}
			if (sizeof($tournamentResultsA) >= $i AND $aOverallAwarded >= $i) {
				$row = $tournamentResultsA[$i-1];
				array_push($labelValues, array("Division A", $row[2]));
			}
							if (sizeof($tournamentResultsAltB) >= $i AND $bOverallAwardedAlt >= $i) {
					$row = $tournamentResultsAltB[$i-1];
					array_push($labelValues, array("Division B (ALT)", str_replace("+","",$row[2])));
				}
			if (sizeof($tournamentResultsB) >= $i AND $bOverallAwarded >= $i) {
				$row = $tournamentResultsB[$i-1];
				array_push($labelValues, array("Division B", $row[2]));
			}
							if (sizeof($tournamentResultsAltC) >= $i AND $cOverallAwardedAlt >= $i) {
					$row = $tournamentResultsAltC[$i-1];
					array_push($labelValues, array("Division C (ALT)", str_replace("+","",$row[2])));
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
			else if ($slide->getType() == 'TEAMLIST') {
				$pdf->SetFont('Arial','B',48);
				$pdf->MultiCell(0,16,$slide->getHeaderText(),0,'C',false);
				$pdf->Ln(16);
				foreach ($slide->getLabelValues() as $labels) {
					$pdf->SetFont('Arial','',22);	
					$pdf->Cell(0,10, $labels[0],0,0,'L');
					$pdf->Ln(12);
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
			$query = "Select U.USER_ID, U.FIRST_NAME, U.LAST_NAME, U.USERNAME, 
			group_concat(DISTINCT UR2.ROLE_CODE ORDER BY UR2.ROLE_CODE ASC  SEPARATOR ', ') as ROLES 
			from USER U 
			INNER JOIN USER_ROLE UR ON UR.USER_ID=U.USER_ID 
			INNER JOIN USER_ROLE UR2 ON UR.USER_ID=UR2.USER_ID
			WHERE 1=1 ";
			if ($_SESSION["userFirstName"] != null) {
				$query = $query . " AND U.FIRST_NAME LIKE '".$_SESSION["userFirstName"]."%' " ;
			}
			if ($_SESSION["userLastName"] != null) {
				$query = $query . " AND U.LAST_NAME LIKE '".$_SESSION["userLastName"]."%' " ;
			}
			if ($_SESSION["userRole"] != null) {
				$query = $query . " AND UR.ROLE_CODE = '".$_SESSION["userRole"]."' " ;
			}
			if ($_SESSION["pageCommand"] AND ($_SESSION["pageCommand"] == 'selectCoach' || $_SESSION["pageCommand"] == 'selectSupervisor' || $_SESSION["pageCommand"] == 'selectVerifier')) {
				$query = $query . " AND COALESCE(U.AUTO_CREATED_FLAG,0) = 0 " ;
			}
			if (isUserAccess(0) AND ($_SESSION["autoCreatedFlag"] == null || $_SESSION["autoCreatedFlag"] == '' || $_SESSION["autoCreatedFlag"] == 'NO')) {
				$query = $query . " AND COALESCE(U.AUTO_CREATED_FLAG,0) = 0 " ;
			}
			$query = $query . " GROUP BY U.USER_ID, U.FIRST_NAME, U.LAST_NAME, U.USERNAME ORDER BY UPPER(U.LAST_NAME) ASC ";
			if ($_SESSION["userFilterNumber"] != null and $_SESSION["userFilterNumber"] != '0') {
				$query = $query . " LIMIT ".$_SESSION["userFilterNumber"];
			}
			
			$result = $mysqli->query($query); 
 			if ($result) {
				while($userRow = $result->fetch_array(MYSQLI_BOTH)) {
 					$userRecord = array();	
					array_push($userRecord, $userRow['0']);
					array_push($userRecord, $userRow['1']);
					array_push($userRecord, $userRow['2']);
					array_push($userRecord, $userRow['3']);
					array_push($userRecord, $userRow['ROLES']);
 				
					array_push($userList, $userRecord);
				}
			}
		$_SESSION["resultsPage"] = 1;
		$_SESSION["userList"] = $userList;
		$_SESSION["mysqli_error"] = $mysqli->error;
	
	}
	
	function clearUser() {
		$_SESSION["userId"] = null;
		$_SESSION["userName"] = null;
		$_SESSION["userFirstLastName"] = null;
		//$_SESSION["userRoleCode"] = null;
		$_SESSION["userRoleCodes"] = null;
		$_SESSION["userActiveFlag"] = null;
		$_SESSION["userPhoneNumber"] = null;
	}


	function loadUser($id, $mysqli) {
		$result = $mysqli->query("SELECT U.USER_ID, U.USERNAME, CONCAT(U.FIRST_NAME,' ', U.LAST_NAME) as name, U.ACCOUNT_ACTIVE_FLAG, U.PHONE_NUMBER, U.FIRST_NAME, U.LAST_NAME,
		group_concat(UR.ROLE_CODE ORDER BY UR.ROLE_CODE ASC  SEPARATOR ', ') as ROLES 
							  FROM USER U
							  INNER JOIN USER_ROLE UR ON UR.USER_ID=U.USER_ID 
							  WHERE U.USER_ID = " .$id); 
 			if ($result) {
 				$userRow = $result->fetch_row();	
 				$_SESSION["userId"] = $userRow['0'];
 				$_SESSION["userName"] = $userRow['1'];
 				$_SESSION["userFirstLastName"] = $userRow['2'];
 				//$_SESSION["userRoleCode"] = $userRow['3'];
 				$_SESSION["userRoleCodes"] = explode(', ', $userRow['7']);
 				$_SESSION["userActiveFlag"] = $userRow['3'];
 				$_SESSION["userPhoneNumber"] = $userRow['4'];
 				$_SESSION["firstName"] = $userRow['5'];
				$_SESSION["lastName"] = $userRow['6'];				
    		}
	}
	
	function saveUser($mysqli) {
		$query = $mysqli->prepare("UPDATE USER SET ACCOUNT_ACTIVE_FLAG=? WHERE USER_ID=".$_SESSION["userId"]);
			
		$query->bind_param('i', $_GET["userActiveFlag"]);
		$query->execute();
		$query->free_result();
		
		// save Confirmation
		$_SESSION['savesuccessUser'] = "1";	
	}
	
	function resetUserPassword($mysqli, $id) {
		$decId = base64_decode($id);
		$encryptPassword = crypt($decId);
		
		$query = $mysqli->prepare("UPDATE USER SET PASSWORD=? WHERE USER_ID=".$_SESSION["userId"]);
			
		$query->bind_param('s',$encryptPassword);
		$query->execute();
		$query->free_result();
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
		$account = $result->fetch_array(MYSQLI_BOTH);
		$query->free_result();

		
		
		
		if($_SESSION["accountMode"] == 'update' or $account['2'] === crypt($mypassword, $account['2'])) {
			$_SESSION["accountMode"] == '';
				
			$userSessionInfo = new UserSessionInfo($account['1']);
			$userSessionInfo->setAuthenticatedFlag(1);
			$userSessionInfo->setUserId($account['0']);
			$userSessionInfo->setFirstName($account['4']);
			$userSessionInfo->setLastName($account['5']);
			$userSessionInfo->setPhoneNumber($account['8']);
			$userSessionInfo->setState($account['STATE_CODE']);
			
			// Load Coach Team List
			if ($userSessionInfo->getRole() == 'COACH') {
				$teamsCoached = array();
				$query = " SELECT T.TEAM_ID, T.NAME FROM TEAM T INNER JOIN TEAM_COACH TC ON TC.TEAM_ID=T.TEAM_ID
											WHERE TC.USER_ID=".$userSessionInfo->getUserId()." ORDER BY NAME ASC ";	
				$results = $mysqli->query($query); 
				while ($row = $results->fetch_array(MYSQLI_BOTH)) {
					$team = array();
					array_push($team,$row['TEAM_ID']);
					array_push($team,$row['NAME']);		
					array_push($teamsCoached, $team);
				}
				$userSessionInfo->setTeamsCoached($teamsCoached);
			}
			
			// Load Role List AND Current Role (Current Role is always Highest
			    $availableRoles = array();
				$query = " SELECT UR.ROLE_CODE FROM USER_ROLE UR WHERE UR.USER_ID=".$userSessionInfo->getUserId()." ORDER BY UR.ROLE_CODE ASC ";	
				$results = $mysqli->query($query); 		
				$currentRole = 4;	
				while ($row = $results->fetch_array(MYSQLI_BOTH)) {
					array_push($availableRoles,$row['ROLE_CODE']);
					if (getRoleNumber($row['ROLE_CODE']) < $currentRole) {
						$userSessionInfo->setRole($row['ROLE_CODE']);
						$currentRole = 	getRoleNumber($row['ROLE_CODE']);
					}
				}
				$userSessionInfo->setAvailableRoles($availableRoles);
			
			
			$url = explode("/", $_SERVER[REQUEST_URI]);
			$tmp = ''; 
			$i = 0;
			if (sizeof($url) > 1) {
				while ($i < sizeof($url) - 1) {
					$tmp .= $url[$i] . '/'; 
				    $i++;
				}
			}
			$url =  $_SERVER[HTTP_HOST] . $tmp;
			$userSessionInfo->setDomain($url);
			
			$_SESSION["userSessionInfo"] = serialize($userSessionInfo);
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
	
	function changeUserRole($newRole) {
		global $mysqli;
		$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
		// make sure suer has role.
		$query = " SELECT USER_ROLE_ID FROM USER_ROLE WHERE USER_ID=".$userSessionInfo->getUserId()." AND ROLE_CODE='".$newRole."'";
		$results = $mysqli->query($query); 
		if ($results->num_rows > 0) {
			$userSessionInfo->setRole($newRole);
			$_SESSION["userSessionInfo"] = serialize($userSessionInfo);
		}
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
		$_SESSION["state"] = '';
		$_SESSION["userPhoneNumber"] = '';
		$_SESSION["emailConfirmationFlag"] = '';		
	}
	
	function loadAccount() {
		$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
		if ($userSessionInfo != null) {
			$_SESSION["userName"] = $userSessionInfo->getUserName();
			$_SESSION["firstName"] = $userSessionInfo->getFirstName();
			$_SESSION["lastName"] = $userSessionInfo->getLastName();
			$_SESSION["userPhoneNumber"] = $userSessionInfo->getPhoneNumber();
			$_SESSION["state"] = $userSessionInfo->getState();;
		}
	}
	
		function manageAccount($mysqli, $mode) {
		$userName = $_POST['userName']; 
		$password = $_POST['password']; 
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$regCode = $_POST['regCode'];
		$phoneNumber = $_POST['userPhoneNumber'];
		$stateCode = $_POST['state'];
		$emailConfirmationFlag = $_POST['emailConfirmationFlag'];		
		
		$_SESSION["userName"] = $userName;
		$_SESSION["password"] = $password;
		$_SESSION["firstName"] = $firstName;
		$_SESSION["lastName"] = $lastName;
		$_SESSION["vPassword"] = $password;
		$_SESSION["userPhoneNumber"] = $phoneNumber;
		$_SESSION["state"] = $stateCode;
		$_SESSION["emailConfirmationFlag"] = $emailConfirmationFlag;
		
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
			$navigationHandler = unserialize($_SESSION["navigationHandler"]);
			// Get Role from navigation if it's selected
			if ($navigationHandler AND ($navigationHandler->command == 'selectCoach' || $navigationHandler->command == 'selectVerifier' || $navigationHandler->command == 'selectSupervisor')) {
				$role = $navigationHandler->parameters[0];	
			}
			else {
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
			}
			
			// save account info
			$result = $mysqli->query("select max(USER_ID) + 1 from USER");
			$row = $result->fetch_row(); 
			$id = 0;
			if ($row != null and $row['0'] != null) $id = $row['0'];  
			
			$query = $mysqli->prepare("INSERT INTO USER (USER_ID, USERNAME, PASSWORD, FIRST_NAME, LAST_NAME, ACCOUNT_ACTIVE_FLAG, PHONE_NUMBER, STATE_CODE) 
				VALUES (".$id.",?,?,?,?,?,?,?) ");

			$activeFlag = 1;	
			$query->bind_param('ssssiss',$userName, $encryptedPassword,$firstName, $lastName, $activeFlag, $phoneNumber,$stateCode);		
			$query->execute();
			$query->free_result();
			
			// Save Role Info
			$result = $mysqli->query("select max(USER_ROLE_ID) + 1 from USER_ROLE");
			$row = $result->fetch_row(); 
			$userRoleId = 0;
			if ($row != null and $row['0'] != null) $userRoleId = $row['0'];  
			
			$query = $mysqli->prepare("INSERT INTO USER_ROLE (USER_ROLE_ID, USER_ID, ROLE_CODE) VALUES (".$userRoleId.",?,?) ");	
			$query->bind_param('is',$id, $role);		
			$query->execute();
			$query->free_result();
				
			$_SESSION["accountCreationSuccess"] = "1";
			$_SESSION["userId"] = $id;
			
			// Send Creation Email
			if ($emailConfirmationFlag == 1)
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
			
			$sql = "UPDATE USER SET USERNAME=?, PASSWORD=?, FIRST_NAME=?, LAST_NAME=?,PHONE_NUMBER=?,STATE_CODE=? WHERE USER_ID=? ";
			if ($password == null or $password == '') $sql = "UPDATE USER SET USERNAME=?, FIRST_NAME=?, LAST_NAME=?,PHONE_NUMBER=?,STATE_CODE=? WHERE USER_ID=? ";
			$query = $mysqli->prepare($sql);
			if ($password == null or $password == '') $query->bind_param('sssssi',$userName, $firstName,$lastName,$phoneNumber,$stateCode, $userId);		
			else  $query->bind_param('ssssssi',$userName, $encryptedPassword, $firstName,$lastName,$phoneNumber,$stateCode, $userId);		
			$query->execute();
			$query->free_result();	
		
			$_SESSION["accountUpdateSuccess"] = "1";
		}
		
		return true;
	}
	
	function addRole($userId, $role) {
		global $mysqli;
		$query = " SELECT USER_ROLE_ID FROM USER_ROLE WHERE USER_ID=".$userId." AND ROLE_CODE='".$role."'";
		$results = $mysqli->query($query); 
		if ($results->num_rows < 1) {
			$result = $mysqli->query("select max(USER_ROLE_ID) + 1 from USER_ROLE");
			$row = $result->fetch_row(); 
			$userRoleId = 0;
			if ($row != null and $row['0'] != null) $userRoleId = $row['0'];  
			
			$query = $mysqli->prepare("INSERT INTO USER_ROLE (USER_ROLE_ID, USER_ID, ROLE_CODE) VALUES (".$userRoleId.",?,?) ");	
			$query->bind_param('is',$userId, $role);		
			$query->execute();
			$query->free_result();			
			return true;
		}
		return false;
		
	}
	
	
	// SELF SCHEDULE FUNCTIONS -----------------------------------------------
	
	function loadSelfSchedule($mysqli, $id, $loadCachedSchedule) {
		$selfSchedule = new SelfSchedule();
		$selfSchedule->setTournamentId($id);
		// Old Schedule (For Caching)
		$oldSelfSchedule = unserialize($_SESSION["selfSchedule"]);
		
		// Load Basic Tournament Information
		$query = " SELECT NAME, LOCATION, DIVISION, DATE_FORMAT(DATE,'%m/%d/%Y') 'DATE1' FROM TOURNAMENT WHERE TOURNAMENT_ID=".$selfSchedule->getTournamentId();
		$results = $mysqli->query($query); 
		while ($row = $results->fetch_array(MYSQLI_BOTH)) {				
			$selfSchedule->setTournamentName($row['NAME']);
			$selfSchedule->setTournamentLocation($row['LOCATION']);
			$selfSchedule->setTournamentDivision($row['DIVISION']);
			$selfSchedule->setTournamentDate($row['DATE1']);
			break;
		}
		// Load Basic Schedule Information
		$query = " SELECT TOURNAMENT_SCHEDULE_ID, OPEN_FLAG, DATE_FORMAT(START_TIME,'%h:%i %p') 'START_TIME', DATE_FORMAT(END_TIME,'%h:%i %p') 'END_TIME', COALESCE(ALTERNATE_TEAM_SCHEDULE_FLAG,0) 'ALTERNATE_TEAM_SCHEDULE_FLAG' FROM TOURNAMENT_SCHEDULE WHERE TOURNAMENT_ID=".$selfSchedule->getTournamentId();
		$results = $mysqli->query($query);
		if ($results) {
			$row = $results->fetch_array(MYSQLI_BOTH);			
			$selfSchedule->setTournamentScheduleId($row['TOURNAMENT_SCHEDULE_ID']);
			$selfSchedule->setSelfScheduleOpenFlag($row['OPEN_FLAG']);
			$selfSchedule->setStartTime($row['START_TIME']);
			$selfSchedule->setEndTime($row['END_TIME']);
			$selfSchedule->selfScheduleAlternateTeamFlag = $row['ALTERNATE_TEAM_SCHEDULE_FLAG'];
		}
		
		
		// Load Periods
		if ($selfSchedule->getTournamentScheduleId() != null and $selfSchedule->getTournamentScheduleId() != '') {
			$periods = array();
			$query = " SELECT SCHEDULE_PERIOD_ID, DATE_FORMAT(START_TIME,'%h:%i %p') START_TIME1,DATE_FORMAT(END_TIME,'%h:%i %p') END_TIME, PERIOD_NUMBER, PERIOD_INTERVAL_COUNT FROM SCHEDULE_PERIOD WHERE TOURNAMENT_SCHEDULE_ID=".$selfSchedule->getTournamentScheduleId()." ORDER BY START_TIME ASC ";
			$results = $mysqli->query($query); 
			while ($row = $results->fetch_array(MYSQLI_BOTH)) {				
				$period = new SelfSchedulePeriod();
				$period->setSchedulePeriodId($row['SCHEDULE_PERIOD_ID']);
				$period->setStartTime($row['START_TIME1']);
				$period->setEndTime($row['END_TIME']);
				$period->setPeriodNumber($row['PERIOD_NUMBER']);
				$period->setPeriodInterval($row['PERIOD_INTERVAL_COUNT']);
				array_push($periods, $period);
			}
			$selfSchedule->setPeriodList($periods);
		}
		
		// Load Events Periods
		$query = "SELECT TE.TOURN_EVENT_ID,E.NAME, SE.SCHEDULE_EVENT_ID, SE.ALL_DAY_FLAG, SE.ALLOW_SCHEDULE_FLAG, SE.PERIOD_LENGTH, SE.PERIOD_TEAM_LIMIT, SE.PERIOD_INTERVAL, DATE_FORMAT(SE.EVENT_START_TIME,'%h:%i %p') as EVENT_START_TIME,
		SEP.SCHEDULE_EVENT_PERIOD_ID,SEP.PERIOD_TEAM_LIMIT AS P_LIMIT,DATE_FORMAT(SEP.PERIOD_START_TIME,'%h:%i %p') PERIOD_START_TIME, DATE_FORMAT(SEP.PERIOD_END_TIME,'%h:%i %p') PERIOD_END_TIME,SEP.PERIOD_INTERVAL AS P_INTERVAL, SEP.PERIOD_NUMBER, SEP.PERIOD_TEAM_LIMIT-count(ST.SCHEDULE_TEAM_ID) AS P_COUNT
		FROM TOURNAMENT_EVENT TE
		INNER JOIN EVENT E on E.EVENT_ID=TE.EVENT_ID
		LEFT JOIN SCHEDULE_EVENT SE ON SE.TOURN_EVENT_ID=TE.TOURN_EVENT_ID
		LEFT JOIN SCHEDULE_EVENT_PERIOD SEP ON SEP.SCHEDULE_EVENT_ID=SE.SCHEDULE_EVENT_ID
		LEFT JOIN SCHEDULE_TEAM ST ON ST.SCHEDULE_EVENT_PERIOD_ID=SEP.SCHEDULE_EVENT_PERIOD_ID
		WHERE TE.TOURNAMENT_ID=".$selfSchedule->getTournamentId()." GROUP BY TE.TOURN_EVENT_ID,SEP.SCHEDULE_EVENT_PERIOD_ID, SE.SCHEDULE_EVENT_ID ORDER BY UPPER(E.NAME) ASC, SEP.PERIOD_START_TIME ASC ";

		$results = $mysqli->query($query); 
		$events = array();
		$tournEventId  = -1;
		$event = new selfScheduleEvent();
		$event->periodsList = array();
		$count = 0;
		while ($row = $results->fetch_array(MYSQLI_BOTH)) {	
			if ($tournEventId != $row['TOURN_EVENT_ID'] and $count != 0) {
				
				array_push($events, $event);
				$event = new selfScheduleEvent();
				$event->periodsList = array();
				$count = 0;
			}
			if ($count == 0) {
				$event->tournEventId = $row['TOURN_EVENT_ID'];
				$event->scheduleEventId = $row['SCHEDULE_EVENT_ID'];
				$event->eventName = $row['NAME'];
				$event->selfScheduleFlag = $row['ALLOW_SCHEDULE_FLAG'];
				$event->allDayFlag = $row['ALL_DAY_FLAG'];
				$event->periodLength = $row['PERIOD_LENGTH'];
				$event->periodInterval = $row['PERIOD_INTERVAL'];
				$event->teamLimit = $row['PERIOD_TEAM_LIMIT'];
				$event->eventStartTime = $row['EVENT_START_TIME'];
			}
			$tournEventId = $row['TOURN_EVENT_ID'];
			$period = new selfScheduleEventPeriod();
			$period->scheduleEventPeriodId = $row['SCHEDULE_EVENT_PERIOD_ID'];
			$period->scheduleEventId = $row['SCHEDULE_EVENT_ID'];
			//$period->schedulePeriodId = $row['SCHEDULE_PERIOD_ID'];
			$period->allDayFlag = $row['ALL_DAY_FLAG'];
			$period->periodStartTime = $row['PERIOD_START_TIME'];
			$period->periodEndTime = $row['PERIOD_END_TIME'];
			$period->periodInterval = $row['P_INTERVAL'];
			$period->teamLimit = $row['P_LIMIT'];
			$period->slotsOpen = $row['P_COUNT'];
			$period->periodNumber = $row['PERIOD_NUMBER'];
			if ($period->scheduleEventPeriodId)
				array_push($event->periodsList, $period);
			$count++;
		}
		if ($count > 0) { array_push($events, $event); } //array_unshift($events, $event);
		$selfSchedule->setEventList($events);
		
		// Determine which teams the logged in user can modify at this Tournament
		$availableTeams = array();
		$query = "SELECT TT.TOURN_TEAM_ID, T.TEAM_ID, T.NAME, TT.ALTERNATE_FLAG from TEAM_COACH TC
		INNER JOIN TOURNAMENT_TEAM TT ON TT.TEAM_ID=TC.TEAM_ID
		INNER JOIN TEAM T ON TC.TEAM_ID=T.TEAM_ID
		WHERE TT.TOURNAMENT_ID=".$selfSchedule->getTournamentId()." AND TC.USER_ID=".getCurrentUserId();
		$results = $mysqli->query($query); 
		while ($row = $results->fetch_array(MYSQLI_BOTH)) {	
			if ($selfSchedule->selfScheduleAlternateTeamFlag == 1 OR ($selfSchedule->selfScheduleAlternateTeamFlag == 0 AND $row['ALTERNATE_FLAG'] != 1)) {
				array_push($availableTeams, $row['TOURN_TEAM_ID']);
			}
		}
		
		// Load Teams at the Tournament
		$teams = array();
		$count = 0;
		$query = " SELECT T.TEAM_ID, T.NAME, TT.TOURN_TEAM_ID, TT.TEAM_NUMBER, GROUP_CONCAT(TS.SCHEDULE_EVENT_PERIOD_ID, '') as PERIODS 
		FROM TOURNAMENT_TEAM TT 
		INNER JOIN TEAM T ON T.TEAM_ID=TT.TEAM_ID
		LEFT JOIN SCHEDULE_TEAM TS ON TS.TOURN_TEAM_ID=TT.TOURN_TEAM_ID
		WHERE TT.TOURNAMENT_ID=".$selfSchedule->getTournamentId()." 
		GROUP BY TT.TOURN_TEAM_ID
		ORDER BY T.NAME ASC, CAST(TT.TEAM_NUMBER AS UNSIGNED) ASC ";
		$results = $mysqli->query($query); 
			while ($row = $results->fetch_array(MYSQLI_BOTH)) {				
				$team = new selfScheduleTeam();
				$team->teamId = $row['TEAM_ID'];
				$team->teamName = $row['NAME'];
				$team->teamNumber = $row['TEAM_NUMBER'];
				$team->tournTeamId = $row['TOURN_TEAM_ID'];
				$team->teamAvailableFlag = false;
				
				if ($row['TOURN_TEAM_ID'] != null) {
					$team->linkedPeriodsList = explode(",",$row['PERIODS']);
				}
				if (in_array($team->tournTeamId, $availableTeams) || isUserAccess(1)) {
					$team->teamAvailableFlag = true;
					$team->teamAvailableId = $count;
					if (getCurrentRole() == 'COACH') {
						$team->teamSelectedFlag = true;
						if (!$selfSchedule->tournTeamSelectedId) $selfSchedule->tournTeamSelectedId = $team->tournTeamId;
					}
					$count++;
				}
				
				
				if ($oldSelfSchedule AND $loadCachedSchedule) {
					$selfSchedule->tournTeamSelectedId = $oldSelfSchedule->tournTeamSelectedId;
					$selfSchedule->reservedSelected = $oldSelfSchedule->reservedSelected;
					foreach ($oldSelfSchedule->teamList as $oldTeam) {
						if ($oldTeam->tournTeamId == $team->tournTeamId) {
							if ($oldTeam->teamSelectedFlag) {
								$team->teamSelectedFlag = true;
							}
							break;
						}
					}
				}
				
				array_push($teams, $team);
			}
			$selfSchedule->teamList = $teams;
			
		// Load Reserved Sots
		$query = 'SELECT TS.TOURN_TEAM_ID, TS.SCHEDULE_EVENT_PERIOD_ID, count(TS.SCHEDULE_EVENT_PERIOD_ID) as COUNT
		FROM SCHEDULE_TEAM TS 
		INNER JOIN SCHEDULE_EVENT_PERIOD SEP ON TS.SCHEDULE_EVENT_PERIOD_ID=SEP.SCHEDULE_EVENT_PERIOD_ID
		INNER JOIN SCHEDULE_EVENT SE ON SE.SCHEDULE_EVENT_ID=SEP.SCHEDULE_EVENT_ID
		INNER JOIN TOURNAMENT_SCHEDULE TTS ON TTS.TOURNAMENT_SCHEDULE_ID=SE.TOURNAMENT_SCHEDULE_ID
		WHERE TTS.TOURNAMENT_ID='.$selfSchedule->getTournamentId().' AND TS.TOURN_TEAM_ID= -1
		GROUP BY TS.SCHEDULE_EVENT_PERIOD_ID
		ORDER BY TS.SCHEDULE_EVENT_PERIOD_ID ASC ' ;
		$results = $mysqli->query($query); 
		$reservedPeriods = array();
		if ($results) {
			while ($row = $results->fetch_array(MYSQLI_BOTH)) {	
				$reservedPeriod = array();
				array_push($reservedPeriod, $row['SCHEDULE_EVENT_PERIOD_ID']);
				array_push($reservedPeriod, $row['COUNT']);
				array_push($reservedPeriods, $reservedPeriod);
			}
		}
		$selfSchedule->reservedEventPeriods = $reservedPeriods;
			
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
		
		// Determine what tab to default
		$_SESSION["selfSchedulScreen"] = 'SCHEDULE';
	}
	
	function addPeriod() {
		$period = new selfSchedulePeriod();
		$period->setPeriodInterval($_GET['periodInterval']);
		$period->setPeriodNumber($_GET['periodNumber']);
		
		$startTime = DateTime::createFromFormat('h:i A',$_GET['periodStartTime']); 
		$period->setStartTime($startTime->format('h:i A'));
		$endTime = DateTime::createFromFormat('h:i A',$_GET['periodEndTime']); 
		$period->setEndTime($endTime->format('h:i A'));
		
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		// Periods cannot overlap and must be between start and end time
		$list = $selfSchedule->getPeriodList();
		if ($list == null) $list = array();
		if (True) {
			array_push($list, $period);
		}
		// Sort Period List
		usort($list, "sortPeriodsListAsc");
		$selfSchedule->setPeriodList($list);
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
	}
	
	
	function addEventPeriod() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$success = true;
		
		// get event row
		$event;
		$count = 0;
		$eventList = $selfSchedule->getEventList();
		foreach ($eventList as $eventInfo) {
			if ($_GET['eventRow'] == $count) {
				$event = $eventInfo;
				break;
			}	
			$count++;	
		}
			
		if ($_GET['allDayEventFlag'] == '0') {		
			$period = new selfScheduleEventPeriod();		
			$period->periodNumber = $_GET['addPeriodNumber'];
			$period->teamLimit = $_GET['addPeriodTeamLimit'];
			$period->slotsOpen = $_GET['addPeriodTeamLimit'];
			//get period info from periods
			$periodInfo;
			foreach ($selfSchedule->getPeriodList() as $periodRow) {
				if ($period->periodNumber == $periodRow->getPeriodNumber()) {
					$periodInfo = $periodRow;
					break;
				}
			}
			
			$period->periodStartTime = $periodInfo->getStartTime();
			$period->periodEndTime = $periodInfo->getEndTime();
			$period->periodInterval = $periodInfo->getPeriodInterval();
			
			// add eventPeriod to event unless already exists
			if ($event->periodsList == null) $event->periodsList = array();
			else {
				foreach($event->periodsList as $pItem) {
					if ($pItem->periodStartTime == $period->periodStartTime) {
						$success = false;
						echo 'error1';
						break;
					}
				}
			}
			if($success) {
				array_push($event->periodsList, $period);	
				usort($event->periodsList, "sortPeriodsAsc");
				$eventList[$count] = $event;
				$selfSchedule->setEventList($eventList);
				$_SESSION["selfSchedule"] = serialize($selfSchedule);
			}
		}
		else {
			// generate periods
			$length = $_GET['aPeriodLength'];
			$interval = $_GET['aPeriodInterval'];
			$limit = $_GET['aTeamLimit'];
			$startTime = $_GET['aEventStartTime'];
			
			$startTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$_GET['aEventStartTime']); 
			$endTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$selfSchedule->getEndTime()); 
			$periodStartTime = $startTime;
			$periodEndTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$_GET['aEventStartTime']); 
			$lenFMT = 'PT'.$length.'M';
			$intervalFMT = 'PT'.$interval.'M';
			
			$periodEndTime->add(new DateInterval($lenFMT));
			
			$event->periodsList = array();
			$count2 = 1;
			while ($periodEndTime <= $endTime) {
				$period = new selfScheduleEventPeriod();	
				$period->periodStartTime = $periodStartTime->format('h:i A');
				$period->periodEndTime = $periodEndTime->format('h:i A');
				$period->periodInterval = $interval;
				$period->teamLimit = $limit;
				$period->slotsOpen = $limit;
				$period->periodNumber = $count2;
				$period->scheduleEventId = $event->scheduleEventId;
				$period->allDayFlag = 1;
				
				$periodStartTime->add(new DateInterval($intervalFMT)); 
				$periodEndTime = DateTime::createFromFormat('m/d/Y H:i',$periodStartTime->format('m/d/Y H:i'));
				$periodEndTime->add(new DateInterval($lenFMT)); 			
				
				array_push($event->periodsList, $period);	
				$count2++;
			}
			
			
			$eventList[$count] = $event;
			$selfSchedule->setEventList($eventList);
			$_SESSION["selfSchedule"] = serialize($selfSchedule);
		}
		return $success;
	}
	
	function sortPeriodsAsc($a, $b) {
		$a = DateTime::createFromFormat('h:i A',$a->periodStartTime);
		$b = DateTime::createFromFormat('h:i A',$b->periodStartTime);
		if ($a < $b) return -1;
		if ($a > $b) return 1;
		return 0;
	}
	function sortPeriodsListAsc($a, $b) {
		$a = DateTime::createFromFormat('h:i A',$a->getStartTime());
		$b = DateTime::createFromFormat('h:i A',$b->getStartTime());
		if ($a < $b) return -1;
		if ($a > $b) return 1;
		return 0;
	}
	
	function deletePeriod($mysqli) {
		$id = $_GET['schedulePeriodId'];
		$rowNum = $_GET['periodRow'];
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$list = $selfSchedule->getPeriodList();
		unset($list[$rowNum]);
		$list = array_values($list);
		if ($id != null AND $id != '') {
			$result = $mysqli->query("DELETE FROM SCHEDULE_PERIOD WHERE SCHEDULE_PERIOD_ID = " .$id);
		}
		
		$selfSchedule->setPeriodList($list);
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
	}
	
	function deleteEventPeriod($mysqli) {
		$id = $_GET['scheduleEventPeriodId'];
		$eventRow = $_GET['eventRow'];
		$periodRow = $_GET['periodRow'];
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		
		$result = $mysqli->query("SELECT * FROM SCHEDULE_TEAM WHERE SCHEDULE_EVENT_PERIOD_ID = " .$id);
		if ($result->num_rows > 0) {
			echo 'error1';
			return false;
		}
		if ($id and $id != '') {
			$result = $mysqli->query("DELETE FROM SCHEDULE_EVENT_PERIOD WHERE SCHEDULE_EVENT_PERIOD_ID = " .$id);
		}
		if ($selfSchedule->getEventList()) {
			$count = 0;
			foreach($selfSchedule->getEventList() as $event) {
				if ($eventRow == $count) {
					$list = $event->periodsList;
					unset($list[$periodRow]);
					$list = array_values($list);
					$event->periodsList = $list;	
					break;
				}
				$count++;
			}
		}
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
		return true;
	}
	
	function deleteAllEventPeriods($mysqli) {
		$eventRow = $_GET['eventRowId'];		
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$count = 0;
		foreach ($selfSchedule->getEventList() as $event) {
			if ($eventRow == $count) {
				if ($event->scheduleEventId AND $event->scheduleEventId != '') {
					$result = $mysqli->query("SELECT * FROM SCHEDULE_TEAM ST
											INNER JOIN SCHEDULE_EVENT_PERIOD SEP ON SEP.SCHEDULE_EVENT_PERIOD_ID = ST.SCHEDULE_EVENT_PERIOD_ID
											WHERE SEP.SCHEDULE_EVENT_ID = " .$event->scheduleEventId);
					if ($result->num_rows > 0) {
						echo 'error1';
						return false;
					}
				}
				
				foreach ($event->periodsList as $period) {
					if ($period->scheduleEventPeriodId AND $period->scheduleEventPeriodId != '') {
						$result = $mysqli->query("DELETE FROM SCHEDULE_EVENT_PERIOD WHERE SCHEDULE_EVENT_PERIOD_ID = " .$period->scheduleEventPeriodId);
					}	
				}
				$event->periodsList = array();			
				break;
			}
			$count++;
		}
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
		return true;
	}
	
	function cacheSelfScheduleSettings() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$selfSchedule->setStartTime($_GET['tournamentStartTime']);
		$selfSchedule->setEndTime($_GET['tournamentEndTime']);
		$selfSchedule->setSelfScheduleOpenFlag($_GET['selfScheduleOpen']);
		$selfSchedule->selfScheduleAlternateTeamFlag = $_GET['selfScheduleAlternateTeams'];
		
		// Event info (period already cached)
		$count = 0;
		foreach ($selfSchedule->getEventList() as $event) {
			$event->allDayFlag = $_GET['allDayEventFlag'.$count];
			$event->selfScheduleFlag = $_GET['selfScheduleFlag'.$count];
			$event->periodLength = $_GET['periodLength'.$count];
			$event->periodInterval = $_GET['periodInterval'.$count];
			$event->teamLimit = $_GET['teamLimit'.$count];
			$event->eventStartTime = $_GET['eventStartTime'.$count];
			$count++;
		}
		
		
		
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
	}
	
	function saveSelfScheduleSettings($mysqli) {
			$selfSchedule = unserialize($_SESSION["selfSchedule"]);
			
			$startTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$selfSchedule->getStartTime()); 
			$startTime1 = $startTime->format('Y-m-d H:i:s');
			$endTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$selfSchedule->getEndTime()); 
			$endTime1 = $endTime->format('Y-m-d H:i:s');
			$tournamentId = $selfSchedule->getTournamentId();
			$openFlag = $selfSchedule->getSelfScheduleOpenFlag();
			$alternateTeamsScheduleFlag = $selfSchedule->selfScheduleAlternateTeamFlag;
			
			if ($selfSchedule->getTournamentScheduleId() == null or $selfSchedule->getTournamentScheduleId() == '') {			
				$result = $mysqli->query("select max(TOURNAMENT_SCHEDULE_ID) + 1 from TOURNAMENT_SCHEDULE");
				$row = $result->fetch_row(); 
				$id = 1;
				if ($row != null and $row['0'] != null) $id = $row['0'];  
				
				$query = $mysqli->prepare("INSERT INTO TOURNAMENT_SCHEDULE (TOURNAMENT_SCHEDULE_ID, TOURNAMENT_ID, START_TIME, END_TIME, OPEN_FLAG, ALTERNATE_TEAM_SCHEDULE_FLAG) VALUES (".$id.",?,?,?,?,?) ");
					
				$query->bind_param('issii',$tournamentId, $startTime1, $endTime1, $openFlag, $alternateTeamsScheduleFlag);		
				$query->execute();
				$query->free_result();	
				$selfSchedule->setTournamentScheduleId($id);
			}
			else {
				$query = $mysqli->prepare("UPDATE TOURNAMENT_SCHEDULE SET START_TIME=?, END_TIME=?, OPEN_FLAG=?, ALTERNATE_TEAM_SCHEDULE_FLAG=? WHERE TOURNAMENT_SCHEDULE_ID=".$selfSchedule->getTournamentScheduleId());	
				$query->bind_param('ssii',$startTime1, $endTime1, $openFlag, $alternateTeamsScheduleFlag);	
				$query->execute();
				$query->free_result();				
			}
			if ($selfSchedule->getPeriodList()) {
			foreach ($selfSchedule->getPeriodList() as $period) {
				$startTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$period->getStartTime()); 
				$startTime1 = $startTime->format('Y-m-d H:i:s');
				$endTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$period->getEndTime()); 
				$endTime1 = $endTime->format('Y-m-d H:i:s');
				
				if ($period->getSchedulePeriodId() == null or $period->getSchedulePeriodId() == '') {
					$result = $mysqli->query("select max(SCHEDULE_PERIOD_ID) + 1 from SCHEDULE_PERIOD");
					$row = $result->fetch_row(); 
					$id = 1;
					if ($row != null and $row['0'] != null) $id = $row['0'];  
					
					$query = $mysqli->prepare("INSERT INTO SCHEDULE_PERIOD (SCHEDULE_PERIOD_ID, TOURNAMENT_SCHEDULE_ID, START_TIME, END_TIME, PERIOD_NUMBER, PERIOD_INTERVAL_COUNT) VALUES (".$id.",".$selfSchedule->getTournamentScheduleId().",?,?,".$period->getPeriodNumber().",".$period->getPeriodInterval().") ");
						
					$query->bind_param('ss', $startTime1, $endTime1);		
					$query->execute();
					$query->free_result();	
					$period->setSchedulePeriodId($id);
				}
				else {

				}
			} 
			}
			
			// save Event List
			if ($selfSchedule->getEventList()) {
				foreach ($selfSchedule->getEventList() as $event) {
					$allDayFlag = 0; if ($event->allDayFlag) $allDayFlag = $event->allDayFlag;
					$periodLength = 0; if ($event->periodLength) $periodLength = $event->periodLength;
					$teamLimit = 0; if ($event->teamLimit) $teamLimit = $event->teamLimit;
					$periodInterval = 0; if ($event->periodInterval) $periodInterval = $event->periodInterval;
					$selfScheduleFlag = 0; if ($event->selfScheduleFlag) $selfScheduleFlag = $event->selfScheduleFlag;
					$eventStartTime = null; if ($event->eventStartTime) {
						$eventStartTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$event->eventStartTime); 
						$eventStartTime = $eventStartTime->format('Y-m-d H:i:s');
					}
					
					if ($event->scheduleEventId == null or $event->scheduleEventId == '') {
						$result = $mysqli->query("select max(SCHEDULE_EVENT_ID) + 1 from SCHEDULE_EVENT");
						$row = $result->fetch_row(); 
						$id = 1;
						if ($row != null and $row['0'] != null) $id = $row['0'];  
						
						$query = $mysqli->prepare("INSERT INTO SCHEDULE_EVENT (SCHEDULE_EVENT_ID, TOURNAMENT_SCHEDULE_ID, TOURN_EVENT_ID, ALL_DAY_FLAG, PERIOD_LENGTH, PERIOD_TEAM_LIMIT, PERIOD_INTERVAL, ALLOW_SCHEDULE_FLAG, EVENT_START_TIME) VALUES (".$id.",".$selfSchedule->getTournamentScheduleId().",".$event->tournEventId.",?,?,?,?,?,? ) ");
						
						$query->bind_param('iiiiis', $allDayFlag, $periodLength, $teamLimit, $periodInterval,$selfScheduleFlag, $eventStartTime);		
						$query->execute();
						$query->free_result();	
						$event->scheduleEventId = $id;
					}
					else {
						$query = $mysqli->prepare("UPDATE SCHEDULE_EVENT SET ALL_DAY_FLAG=?,PERIOD_LENGTH=?,PERIOD_TEAM_LIMIT=?,PERIOD_INTERVAL=?, ALLOW_SCHEDULE_FLAG=?, EVENT_START_TIME=? WHERE SCHEDULE_EVENT_ID=".$event->scheduleEventId);
							
						$query->bind_param('iiiiis', $allDayFlag, $periodLength, $teamLimit, $periodInterval,$selfScheduleFlag, $eventStartTime);			
						$query->execute();
						$query->free_result();	
					}
			
					if ($event->periodsList) {
						foreach ($event->periodsList as $period) {
							$pTeamLimit = $period->teamLimit;
							$pPeriodInterval = $period->periodInterval;
							$pPeriodNumber = $period->periodNumber;
							$pStartTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$period->periodStartTime); 
							$pStartTime = $pStartTime->format('Y-m-d H:i:s');
							$pEndTime = DateTime::createFromFormat('m/d/Y h:i A', $selfSchedule->getTournamentDate().' '.$period->periodEndTime); 
							$pEndTime = $pEndTime->format('Y-m-d H:i:s');
							
							if ($period->scheduleEventPeriodId == null or $period->scheduleEventPeriodId == '') {
								// Save newly added Periods
								$result = $mysqli->query("select max(SCHEDULE_EVENT_PERIOD_ID) + 1 from SCHEDULE_EVENT_PERIOD");
								$row = $result->fetch_row(); 
								$id = 1;
								if ($row != null and $row['0'] != null) $id = $row['0'];  
								
								$query = $mysqli->prepare("INSERT INTO SCHEDULE_EVENT_PERIOD (SCHEDULE_EVENT_PERIOD_ID, SCHEDULE_EVENT_ID, PERIOD_TEAM_LIMIT, PERIOD_START_TIME, PERIOD_END_TIME, PERIOD_INTERVAL, PERIOD_NUMBER) VALUES (".$id.",".$event->scheduleEventId.",?,?,?,?,? ) ");
								
								$query->bind_param('issis', $pTeamLimit, $pStartTime, $pEndTime, $pPeriodInterval,$pPeriodNumber);		
								$query->execute();
								$query->free_result();	
								$period->scheduleEventPeriodId = $id;
								
								
							}
							else {
								
							}
						}						
					}
					else if ($event->allDayFlag == 1 and ($event->periodsList == null or sizeof($event->periodsList) < 1) ) {
					
				
					}
				}
			}
			
			
			$_SESSION["selfSchedule"] = serialize($selfSchedule);
			$_SESSION["selfScheduleSaveSuccess"] = "1";
	}
	
	function selectScheduleTeam($tournTeamId) {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		if ($tournTeamId == -1) {
			if ($selfSchedule->reservedSelected) $selfSchedule->reservedSelected = false;
			else $selfSchedule->reservedSelected = true;
		}
		else {
			foreach($selfSchedule->teamList as $team) {
				if ($team->tournTeamId == $tournTeamId) {
					if ($team->teamSelectedFlag) $team->teamSelectedFlag = false;
					else $team->teamSelectedFlag = true;
					break;
				}
			}
		}	
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
	}
	
	function loadScheduleEventPeriod($mysqli, $scheduleEventPeriodId) {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		
		if ($scheduleEventPeriodId == null OR $scheduleEventPeriodId == '') {
			$errors = array(ERROR_SELF_SCHEDULE_SCHEDULE_TEAM_2);
			$_SESSION['scorecenter_errors'] = $errors;
			return false;
		}
		
		if ($selfSchedule->getSelfScheduleOpenFlag() == 0 AND !isUserAccess(1)) {
			$errors = array(ERROR_SELF_SCHEDULE_SCHEDULE_TEAM_1);
			$_SESSION['scorecenter_errors'] = $errors;
			return false;
		}
		$selfSchedule->currentPeriodId = $scheduleEventPeriodId;
		$query = " SELECT ST.SCHEDULE_TEAM_ID, ST.TOURN_TEAM_ID, ST.SCHEDULE_EVENT_PERIOD_ID, DATE_FORMAT(SEP.PERIOD_START_TIME,'%h:%i %p') AS PERIOD_START_TIME FROM SCHEDULE_TEAM ST 
			INNER JOIN SCHEDULE_EVENT_PERIOD SEP ON SEP.SCHEDULE_EVENT_PERIOD_ID=ST.SCHEDULE_EVENT_PERIOD_ID
			INNER JOIN SCHEDULE_EVENT_PERIOD SEP2 ON SEP2.SCHEDULE_EVENT_PERIOD_ID=".$scheduleEventPeriodId." 
			WHERE SEP.SCHEDULE_EVENT_ID = SEP2.SCHEDULE_EVENT_ID";
		
		$results = $mysqli->query($query); 
		
		// Set Any Admin Filled Slots
		$selfSchedule->noTeams = array();
		while ($row = $results->fetch_array(MYSQLI_BOTH)) {	
			if ($row['TOURN_TEAM_ID'] == -1 AND $scheduleEventPeriodId == $row['SCHEDULE_EVENT_PERIOD_ID']) {
				$noTeam = new selfScheduleTeam();
				$noTeam->scheduleTeamId = $row['SCHEDULE_TEAM_ID'];
				$noTeam->tournTeamId = -1;
				$noTeam->teamLinkedToEventFlag = true;
				array_push($selfSchedule->noTeams, $noTeam);
			}
		}
		$results->data_seek(0);
		
		// Set Teams Linked To This Period And All Teams Scheduled Time
		foreach ($selfSchedule->teamList as $team) {
			$team->scheduleTeamId = '';
			$team->teamLinkedToEventFlag = false;
			$team->scheduledTime = '';
			while ($row = $results->fetch_array(MYSQLI_BOTH)) {	
				if ($row['TOURN_TEAM_ID'] == $team->tournTeamId) $team->scheduledTime = $row['PERIOD_START_TIME'];
				if ($row['TOURN_TEAM_ID'] == $team->tournTeamId AND $scheduleEventPeriodId == $row['SCHEDULE_EVENT_PERIOD_ID']) {
					$team->scheduleTeamId = $row['SCHEDULE_TEAM_ID'];
					$team->teamLinkedToEventFlag = true;					
				}				
			}
			$results->data_seek(0);
		}
	
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
		return true;
	}
	
	function addTeamEventPeriod($mysqli, $tournTeamId, $scheduleEventPeriodId, $mode) {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		
		// Check if self scheduling is open.
		if ($selfSchedule->getSelfScheduleOpenFlag() == 0 AND !isUserAccess(1)) {
				echo 'error0';
				return false;
		}
		
		// Validate Team is not scheduled to Period (remove Scenario) AND is not a reserved slot
		if ('coach' == $mode AND $tournTeamId != -1) {
			$query = " SELECT ST.SCHEDULE_TEAM_ID as TEAM_COUNT FROM SCHEDULE_TEAM ST WHERE ST.SCHEDULE_EVENT_PERIOD_ID = ".$scheduleEventPeriodId. " AND ST.TOURN_TEAM_ID=".$tournTeamId;
			$results = $mysqli->query($query); 
			if ($results) {
				if ($results->num_rows > 0) {
					echo 'error3';
					return false;
				}
			}
		}
		
		
		// Validate Team limit
		$period = null;
		foreach ($selfSchedule->getEventList() as $event1) {
			foreach ($event1->periodsList as $period1) {
				if ($period1->scheduleEventPeriodId == $scheduleEventPeriodId) {
					$period = $period1;
					//$event = $event1;
					break;
				}
			}
		}
		$query = " SELECT ST.SCHEDULE_TEAM_ID as TEAM_COUNT FROM SCHEDULE_TEAM ST WHERE ST.SCHEDULE_EVENT_PERIOD_ID = ".$scheduleEventPeriodId;
		$results = $mysqli->query($query); 
		if ($results) {
			if ($results->num_rows >= $period->teamLimit) {
				echo 'error1';
				return false;
			}
		}
		
		
		// Validate Team is not already schedule to event AND is not a reserved slot
		$query = " SELECT ST.SCHEDULE_TEAM_ID FROM SCHEDULE_TEAM ST 
					INNER JOIN SCHEDULE_EVENT_PERIOD SEC ON ST.SCHEDULE_EVENT_PERIOD_ID=SEC.SCHEDULE_EVENT_PERIOD_ID
					WHERE SEC.SCHEDULE_EVENT_ID = ".$period->scheduleEventId." AND ST.TOURN_TEAM_ID=".$tournTeamId;
		$results = $mysqli->query($query); 
		if ($results) {
			if ($results->num_rows >= 1 AND $tournTeamId != -1) {
				echo 'error2';
				return false;
			}
		}
		
		foreach ($selfSchedule->teamList as $team) {
			if ($team->tournTeamId == $tournTeamId) {
				$team->teamLinkedToEventFlag = true;
				
				// save Schedule_Team record
				$result = $mysqli->query("select max(SCHEDULE_TEAM_ID) + 1 from SCHEDULE_TEAM");
					$row = $result->fetch_row(); 
					$id = 1;
					if ($row != null and $row['0'] != null) $id = $row['0'];  
					$query = $mysqli->prepare("INSERT INTO SCHEDULE_TEAM (SCHEDULE_TEAM_ID, SCHEDULE_EVENT_PERIOD_ID, TOURN_TEAM_ID) VALUES (".$id.",".$scheduleEventPeriodId.",".$team->tournTeamId.") ");				
						$query->execute();
						$query->free_result();	
						$team->scheduleTeamId = $id;
				break;
			}	
		}
		
		// Reserve Slot
		if ($tournTeamId == -1) {
			// save Schedule_Team record
			$result = $mysqli->query("select max(SCHEDULE_TEAM_ID) + 1 from SCHEDULE_TEAM");
			$row = $result->fetch_row(); 
			$id = 1;
			if ($row != null and $row['0'] != null) $id = $row['0'];  
			$query = $mysqli->prepare("INSERT INTO SCHEDULE_TEAM (SCHEDULE_TEAM_ID, SCHEDULE_EVENT_PERIOD_ID, TOURN_TEAM_ID) VALUES (".$id.",".$scheduleEventPeriodId.",-1) ");				
			$query->execute();
			$query->free_result();	
			$reservedTeam = new selfScheduleTeam();
			$reservedTeam->scheduleTeamId = $id;
			$reservedTeam->teamLinkedToEventFlag = true;
			$reservedTeam->tournTeamId = -1;
			array_push($selfSchedule->noTeams, $reservedTeam);
		}
	
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
		return true;
	}
	
	function removeTeamEventPeriod($mysqli, $tournTeamId, $scheduleEventPeriodId, $scheduleTeamId) {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);

		foreach ($selfSchedule->teamList as $team) {
			if ($team->tournTeamId == $tournTeamId) {
				$team->teamLinkedToEventFlag = false;
				
				$result = $mysqli->query("DELETE FROM SCHEDULE_TEAM WHERE SCHEDULE_TEAM_ID=".$scheduleTeamId);
				$team->scheduleTeamId = '';
				break;
			}	
		}
		
		// Reserve Slot
		if ($tournTeamId == -1) {
			foreach ($selfSchedule->noTeams as $key => $reservedTeams) {
				if ($reservedTeams->scheduleTeamId == $scheduleTeamId) {
					$result = $mysqli->query("DELETE FROM SCHEDULE_TEAM WHERE SCHEDULE_TEAM_ID=".$scheduleTeamId);
					// remove from list
					unset($selfSchedule->noTeams[$key]);
					$selfSchedule->noTeams = array_values($selfSchedule->noTeams);
					break;
				}
			}	
		}
	
		$_SESSION["selfSchedule"] = serialize($selfSchedule);
		return true;
		
	}
	
	// Get ScheduleTeamId For Event
	function getScheduleTeamId($mysqli, $tournTeamId, $scheduleEventPeriodId) {
		$scheduleEventId = -1;
		$selfSchedule = unserialize($_SESSION["selfSchedule"]);
		$exists = false;
		foreach ($selfSchedule->getEventList() as $event) {
			foreach ($event->periodsList as $period) {
				if ($period->scheduleEventPeriodId == $scheduleEventPeriodId) {
					$scheduleEventId = $period->scheduleEventId;
					$exists = true;
					break;
				}
			}
			if ($exists) break;
		}
		
		$id = -1;
		$query = " SELECT ST.SCHEDULE_TEAM_ID FROM SCHEDULE_TEAM ST 
					INNER JOIN SCHEDULE_EVENT_PERIOD SEC ON ST.SCHEDULE_EVENT_PERIOD_ID=SEC.SCHEDULE_EVENT_PERIOD_ID
					WHERE SEC.SCHEDULE_EVENT_ID = ".$scheduleEventId." AND ST.TOURN_TEAM_ID=".$tournTeamId;
					
		$results = $mysqli->query($query); 
		if ($results) {
			$row = $results->fetch_row();  
			$id = $row[0];
			
		}
		return $id;
	}
	
	
	
	
	// GENERAL FUNCTIONS AND UTILITIES ---------------------------------------
	
	function init($mysqli) {
		loadDefaultSettings();
		loadStateList($mysqli);
		loadRegionList($mysqli);
	}
	
	function initNoSession($mysqli) {
		loadStateList($mysqli);
		loadRegionList($mysqli);
	}
	
	function loadDefaultSettings() {
		$_SESSION["primaryRowColor"] = "FFFFFF"; // Default Green. Primary Row D1ECD1
		$_SESSION["primaryColumnColor"] = "FFFFFF"; // Default Gray. Primary Column D1D1D1
		$_SESSION["secondaryRowColor"] = "FFFFFF"; // Default White. Secondary Row FFFFFF
		$_SESSION["secondaryColumnColor"] = "FFFFFF"; // Default Green/Gray. Secondary Column CEDCCE
	}
	
	function loadStateList($mysqli) {
		if ($_SESSION["stateCodeList"] == null) {
			// load state code list
			$query = " SELECT REF_DATA_CODE, DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE='STATE' ORDER BY SORT_ORDER ASC ";
			$results = $mysqli->query($query); 
			$result = array();
			while ($row = $results->fetch_array(MYSQLI_BOTH)) {				
				array_push($result, $row);
			}
			$_SESSION["stateCodeList"] = $result;
		}
	}
	
		function loadRegionList($mysqli) {
		if ($_SESSION["regionCodeList"] == null) { 
			// load state code list
			$query = " SELECT REF_DATA_CODE, DISPLAY_TEXT FROM REF_DATA WHERE DOMAIN_CODE='REGION' ORDER BY SORT_ORDER ASC ";
			$results = $mysqli->query($query); 
			$result = array();
			while ($row = $results->fetch_array(MYSQLI_BOTH)) {				
				array_push($result, $row);
			}
			$_SESSION["regionCodeList"] = $result;
		}
	}
	

	
	
		
		
?>
