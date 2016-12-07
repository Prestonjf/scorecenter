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
    
	
// Validates that the user session information has been set and is valid
// Also handles System Timeout

$userSessionInfo = null;
if($_SESSION["userSessionInfo"] == null){
	header("location: logon.php");
	exit();
}

else {
	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);
	if ($userSessionInfo->getUserName() == null or $userSessionInfo->getAuthenticatedFlag() == null) {
		$_SESSION["userSessionInfo"] = null;
		header("location: logon.php");
		exit(); 
	}
	      
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
	if ($url !== $userSessionInfo->getDomain()) {
		$_SESSION["userSessionInfo"] = null; 
		header("location: logon.php");
		exit(); 
	}
}

// Session Timeout 8 hours
$hours = 8;
$minutes = 60*$hours;
if ($_SESSION['sessionTimeout'] + (60 * $minutes) < time()) {
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
?>