<?php
/**
 * Tournament Score Center (TSC) - Tournament scoring web application.
 * Copyright (C) 2016  Preston Frazier
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
 * @version: 1.16.3, 12.07.2016 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
 
	// Display Page Level Errors
	function displayErrors() {
		if ($_SESSION['scorecenter_errors'] != null) {
		    $errors = $_SESSION['scorecenter_errors'];
		    foreach($errors as $error) {
			    echo '<script type="text/javascript">displayError("'.$error.'");</script>';
			    break;
		    }		    
		    $_SESSION['scorecenter_errors'] = null;
	    }
	}
	
	// Display Page Level Succes Messages
	function displayMsgs() {
		if ($_SESSION['scorecenter_msgs'] != null) {
		    $$msgs = $_SESSION['scorecenter_msgs'];
		    foreach($msgs as $msg) {
			    echo '<script type="text/javascript">displaySuccess("'.$msg.'");</script>';
			    break;
		    }		    
		    $_SESSION['scorecenter_msgs'] = null;
	    }
	}
	
	// Return Team Status
	function getEventStatus($key) {
		$status = 'P';
		switch($key) {
			case 'P':
				$status = 'P';
				break;			
			case 'N':
				$status = 'NP';
				break;
			case 'X':
				$status = 'PX';
				break;
			case 'D':
				$status = 'DQ';
				break;	
			default:
				$status = 'P';
		}		
		return $status;
	}
	
	// Display Tournament Search Header
	function getTournamentSearchHeader() {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless" >';
		$html .= '<tr><td><label for="fromDate">From Date: </label></td>';
		
		$html .= '<td><div class="controls"><div class="input-group">
	<input type="text" size="20" class="date-picker form-control" readonly="true" name="fromDate" id="fromDate" value='; $html .= '"'.$_SESSION["fromTournamentDate"].'"' ; $html .= '>
	<label for="fromDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
	</div></div></td>';
	
		$html .= '<td for="toDate"><label>To Date: </label></td>';
		$html .= '<td><div class="controls"><div class="input-group">
	<input type="text" size="20" class="date-picker form-control" readonly="true" name="toDate" id="toDate" value='; $html .= '"'.$_SESSION["toTournamentDate"].'"'; $html .= '>
	<label for="toDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
	</div></div></td></tr>';
		
		$html .= '<tr><td><label for="tournamentsNumber">Limit Result Count: </label></td>';
		$html .= '<td><input type="number" class="form-control" size="10" onkeydown="limit(this);" onkeyup="limit(this);" name="tournamentsNumber" id="tournamentsNumber" min="0" max="999"
		step="1" value='; $html .= '"'.$_SESSION["tournamentsNumber"].'"'; $html .= '></td>';
		
		$html .= '<td colspan="2" align="right";></td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display Team Search Header
	function getTeamSearchHeader() {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label for="teamFilterName">Team Name: </label></td>';
		$html .= '<td><input type="text" size="20" class="form-control" name="teamFilterName" id="teamFilterName" value="'.$_SESSION["teamFilterName"].'"></td>';
		$html .= '<td><label for="filterDivision">Division: </label></td>';
		$html .= '<td><select class="form-control" name="filterDivision" id="filterDivision" >
			<option value=""></option>
			<option value="A"'; if($_SESSION["filterDivision"] == 'A'){$html .= ' selected ';}$html .= ' >A</option>
			<option value="B"'; if($_SESSION["filterDivision"] == 'B'){$html .= ' selected ';}$html .= ' >B</option>
			<option value="C"'; if($_SESSION["filterDivision"] == 'C'){$html .= ' selected ';}$html .= ' >C</option>
			</select></td></tr>';
			
		$html .= '<tr><td><label for="filterState">Team State: </label></td>';
		$html .= '<td><select class="form-control" name="filterState" id="filterState" >
			<option value=""></option>';

			if ($_SESSION["stateCodeList"] != null) {	
				$results = $_SESSION["stateCodeList"];
				foreach($results as $row) {	
					$html .= '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["filterState"] == $row['REF_DATA_CODE']){$html .=' selected ';} $html .= '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}
		$html .= '</select></td>';
		
		$html .= '<td><label for="filterRegion">Team Region: </label></td>';
		$html .= '<td><select class="form-control" name="filterRegion" id="filterRegion" >
			<option value=""></option>';

			if ($_SESSION["regionCodeList"] != null) {	
				$results = $_SESSION["regionCodeList"];
				foreach($results as $row) {	
					$html .= '<option value="'.$row['REF_DATA_CODE'].'" '; if($_SESSION["filterRegion"] == $row['REF_DATA_CODE']){$html .= ' selected ';} $html .= '>'.$row['DISPLAY_TEXT'].'</option>';
				}
			}

		$html .= '</select></td></tr>';
		
		$html .= '<tr><td><label for="filterMyTeams">Team Filter: </label></td>';
		$html .= '<td><input type="radio"  name="filterMyTeams" id="filterMyTeams1" value="NO"'; if($_SESSION["filterMyTeams"] == 'NO'){$html .= ' checked ';}$html .= '> <label class="radio1" for="filterMyTeams1">All Teams</label>&nbsp;&nbsp;
	<input type="radio" name="filterMyTeams" id="filterMyTeams2" value="YES"'; if($_SESSION["filterMyTeams"] == 'YES'){$html .= ' checked ';} $html .= '> <label class="radio1" for="filterMyTeams2">My Teams</label>&nbsp;&nbsp;</td>';
		$html .= '<td><label></label></td>';
		$html .= '<td></td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display Event Search Header
	function getEventSearchHeader() {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label for="eventName">Event Name: </label></td>';
		$html .= '<td><input type="text" size="20" class="form-control" name="eventName" id="eventName" value="'.$_SESSION["eventFilterName"].'"></td>';
		$html .= '<td><label for="filterMyEvents">Event Filter: </label></td>';
		$html .= '<td><input type="radio"  name="filterMyEvents" id="filterMyEvents1" value="OFFICIAL"'; if($_SESSION["filterMyEvents"] == 'OFFICIAL'){$html .=' checked ';} $html .='> <label class="radio1" for="filterMyEvents1">Official Events</label>&nbsp;&nbsp;
	<input type="radio" name="filterMyEvents" id="filterMyEvents2" value="MY"'; if($_SESSION["filterMyEvents"] == 'MY'){$html .=' checked ';} $html .='> <label class="radio1" for="filterMyEvents2">My Events</label>&nbsp;&nbsp;
	<input type="radio"  name="filterMyEvents" id="filterMyEvents3" value="ALL"'; if($_SESSION["filterMyEvents"] == 'ALL'){$html .=' checked ';}$html .='> <label class="radio1" for="filterMyEvents3">All Events</label>&nbsp;&nbsp;</td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display User Search Header
	function getUserSearchHeader($disabled,$isSuperUser) {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label for="userFirstName">First Name: </label></td>';
		$html .= '<td><input type="text" size="20" class="form-control" name="userFirstName" id="userFirstName" value="'.$_SESSION["userFirstName"].'"></td>';
		$html .= '<td><label for="userLastName">Last Name: </label></td>';
		$html .= '<td><input type="text" size="20" class="form-control" name="userLastName" id="userLastName" value="'.$_SESSION["userLastName"].'"></td></tr>';
		$html .= '<tr><td><label for="userRole">Role: </label></td>';
		$html .= '<td><select class="form-control" name="userRole" id="userRole" '.$disabled.'>
			<option value="" '; if ($_SESSION["userRole"] == null or $_SESSION["userRole"] == '') $html .= ' selected '; $html .= '></option>
			<option value="SUPERUSER" '; if ($_SESSION["userRole"] == 'SUPERUSER') $html .= ' selected '; $html .= ' >Super User</option>
			<option value="ADMIN" '; if ($_SESSION["userRole"] == 'ADMIN') $html .= ' selected '; $html .= ' >Admin</option>
			<option value="VERIFIER" '; if ($_SESSION["userRole"] == 'VERIFIER') $html .=  ' selected '; $html .= ' >Verifier</option>
			<option value="SUPERVISOR" '; if ($_SESSION["userRole"] == 'SUPERVISOR') $html .= ' selected '; $html .= ' >Supervisor</option>
			<option value="COACH" '; if ($_SESSION["userRole"] == 'COACH') $html .= ' selected '; $html .= ' >Coach</option>
			</select></td>';
		if ($isSuperUser) {
			$html .= '<td><label for="autoCreatedFlag">Include Auto Created: </label></td>';
			$html .= '<td>
			<select class="form-control" name="autoCreatedFlag" id="autoCreatedFlag" '.$disabled.'>
			<option value="NO" '; if ($_SESSION["autoCreatedFlag"] == 'NO') $html .= ' selected '; $html .= ' >No</option>
			<option value="YES" '; if ($_SESSION["autoCreatedFlag"] == 'YES') $html .= ' selected '; $html .= ' >Yes</option>
			</select>
			</td>';
		}
		else {
			$html .= '<td></td>';
			$html .= '<td></td>';
		}
		$html .= '</tr>';
		$html .= '<tr><td for="userFilterNumber"><label>Limit Result Count: </label></td>';
		$html .= '<td><input type="number" class="form-control" size="10" onkeydown="limit(this);" onkeyup="limit(this);" name="userFilterNumber" id="userFilterNumber" min="0" max="999" step="1" value="'.$_SESSION["userFilterNumber"].'"></td>';
		$html .= '<td><label></label></td>';
		$html .= '<td></td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display Supervisor Home Search Header
	function getSupervisorHomeSearchHeader($mysqli, $userSessionInfo) {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label for="userEventDate">Event Date: </label></td>';
		$html .= '<td><div class="controls"><div class="input-group">
			<input type="text" size="20" class="date-picker form-control" readonly="true" name="userEventDate" id="userEventDate" value="'.$_SESSION["userEventDate"].'">
			<label for="userEventDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
			</div></div></td>';
		$html .= '<td><label for="userTournament">Tournament: </label></td>';
		$html .= '<td><select class="form-control" name="userTournament" id="userTournament">
			<option value=""></option>';
				$query = "SELECT DISTINCT T.TOURNAMENT_ID, T.NAME
					FROM TOURNAMENT T
					INNER JOIN TOURNAMENT_EVENT TE on TE.TOURNAMENT_ID=T.TOURNAMENT_ID 									
					WHERE TE.USER_ID = ".$userSessionInfo->getUserId() . " ORDER BY NAME ASC ";
					$result1 = $mysqli->query($query);	
			    if ($result1) {				 
             		while($tournamentRow = $result1->fetch_array()) {
             			$html .= '<option '; if ($_SESSION["userTournament"] == $tournamentRow['0']) $html .= ' selected ';
						$html .= ' value="'.$tournamentRow['0'].'">'.$tournamentRow['1'].'</option>';		
             		}
             	}
		$html .= '</select></td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	function getCoachHomeSearchHeader($mysqli, $userSessionInfo) {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label for="userEventDate">Tournament: </label></td>';
		$html .= '<td><div class="controls"><div class="input-group">
			<input type="text" size="20" class="date-picker form-control" readonly="true" name="userEventDate" id="userEventDate" value="'.$_SESSION["userEventDate"].'">
			<label for="userEventDate" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>
			</div></div></td>';
		$html .= '<td><label for="userTournament">Tournament: </label></td>';
		$html .= '<td><select class="form-control" name="userTournament" id="userTournament">
			<option value=""></option>';
				$query = "SELECT DISTINCT T.TOURNAMENT_ID, T.NAME
					FROM TOURNAMENT T
					INNER JOIN TOURNAMENT_TEAM TT on TT.TOURNAMENT_ID=T.TOURNAMENT_ID	
					INNER JOIN TEAM_COACH TC ON TC.TEAM_ID=TT.TEAM_ID							
					WHERE TC.USER_ID = ".$userSessionInfo->getUserId() . " ORDER BY NAME ASC ";
					$result1 = $mysqli->query($query);	
			    if ($result1) {				 
             		while($tournamentRow = $result1->fetch_array()) {
             			$html .= '<option '; if ($_SESSION["userTournament"] == $tournamentRow['0']) $html .= ' selected ';
						$html .= ' value="'.$tournamentRow['0'].'">'.$tournamentRow['1'].'</option>';		
             		}
             	}

		$html .= '</select></td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display Tournament Header
	function getTournamentHeader() {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label>Tournament: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentName"].'</td>';
		$html .= '<td><label>Location: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentLocation"].'</td></tr>';
		$html .= '<tr><td><label>Division: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentDivision"].'</td>';
		$html .= '<td><label>Events Completed: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentEventsCompleted"].'</td></tr>';
		$html .= '<tr><td><label>Date: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentDate"].'</td>';
		$html .= '<td><label>Overall Points: </label></td>';
		$html .= '<td>'.$_SESSION["pointsSystem"].'</td></tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display Tournament Event Header
	function getTournamentEventHeader() {
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr><td><label>Tournament: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentName"].'</td>';
		$html .= '<td><label>Event: </label></td>';
		$html .= '<td>'.$_SESSION["eventName"].'</td></tr>';
		$html .= '<tr><td><label>Division: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentDivision"].'</td>';
		$html .= '<td><label>Supervisor: </label></td>';
		$html .= '<td>'.$_SESSION["eventSupervisor"].'</td></tr>';
		$html .= '<tr><td><label>Date: </label></td>';
		$html .= '<td>'.$_SESSION["tournamentDate"].'</td>';
		$html .= '<td><label>Event Scoring Algorithm: </label></td>';
		$html .= '<td>'.$_SESSION["scoreSystemText"].'</td></tr>';
		$html .= '</table></div><br>';
		return $html;	
	}
	
	// Display Self Schedule Tournament Header
	function getSSTournamentHeader() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]); 
		$html = '<div class="headerstyle">';
		$html .= '<table width="100%" class="borderless">';
		$html .= '<tr>';
		$html .= '<td><label for="tournamentName">Tournament Name: </label></td>';
		$html .= '<td>'.$selfSchedule->getTournamentName().'</td>';
		$html .= '<td><label for="tournamentDivision">Tournament Division: </label></td>';
		$html .= '<td>'.$selfSchedule->getTournamentDivision().'</td>';
		$html .= '<td><label for="tournamentDivision">Self Schedule Status: </label></td>';
		$html .= '<td>'; if ($selfSchedule->getSelfScheduleOpenFlag() == 1) $html .= 'Open'; else $html .= 'Closed'; $html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><label for="tournamentName">Tournament Location: </label></td>';
		$html .= '<td>'.$selfSchedule->getTournamentLocation().'</td>';	
		$html .= '<td><label for="tournamentDivision">Tournament Date: </label></td>';
		$html .= '<td>'.$selfSchedule->getTournamentDate().'</td>';
		$html .= '<td colspan="2">';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';	     
	    $html .= '</div><br>';
		
		return $html;
	}
	
	
?>