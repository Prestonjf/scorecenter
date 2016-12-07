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
	
	// Display Tournament Header
	function getTournamentHeader() {
		$html = '<div style="padding-left: 1em; border: 1px solid #FFFFFF; background-color:#eeeeee; border-radius: 6px; ">';
		$html .= '<table width="100%">';
		$html .= '<tr>';
		$html .= '<td><h4>Tournament:<span style="font-weight:normal;font-size:14px;"> '.$_SESSION["tournamentName"]. '</span></h4></td>';
		$html .= '<td><h4>Date: <span style="font-weight:normal;font-size:14px;">'.$_SESSION["tournamentDate"] .'</span></h4></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><h4>Division:<span style="font-weight:normal;font-size:14px;"> '.$_SESSION["tournamentDivision"]. '<span></h4></td>';
		$html .= '<td><h4>Location:<span style="font-weight:normal;font-size:14px;"> '.$_SESSION["tournamentLocation"]. '<span></h4></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><h4>Events Completed:<span style="font-weight:normal;font-size:14px;"> '.$_SESSION["tournamentEventsCompleted"].' </span></h4></td>';
		$html .= '<td><h4>Overall Points: <span style="font-weight:normal;font-size:14px;">'.$_SESSION["pointsSystem"] .'</span></h4></td>';
		$html .= '</tr>';
		$html .= '</table></div><br>';
		return $html;
	}
	
	// Display Self Schedule Tournament Header
	function getSSTournamentHeader() {
		$selfSchedule = unserialize($_SESSION["selfSchedule"]); 
		$html = '<div style="padding-left: 1em; background-color: #eeeeee; border-radius: 6px;">';
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
		if (isUserAccess(1)) {
			$html .= '<button type="submit" class="btn btn-xs btn-primary" name="exportScheduleOverview">Export Schedule</button> ';
			$html .= '<button type="submit" class="btn btn-xs btn-primary" name="exportScheduleAllEvents">Export All Events</button>';
		}
		else {
			$html .= '<button type="submit" class="btn btn-xs btn-primary" name="exportMySchedule">Export My Schedule</button> ';
		}
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';	     
	    $html .= '<hr></div>';
		
		return $html;
	}
	
	
?>