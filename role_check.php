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
 * @version: 1.16.2, 09.05.2016 
 * @author: Preston Frazier http://scorecenter.prestonsproductions.com/index.php 
 * @license: http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */
    
	
/** Role Check - if User does not have correct role, they are returned to the home Page.
 	Parameter is the least level of security required. Defined below highest to lowest:
 	
	1. ADMIN
	2. VERIFIER
	3. SUPERVISOR
**/

function checkUserRole($level) {

	$userSessionInfo = null;
	if($_SESSION["userSessionInfo"] == null) {
		header("location: logon.php");
		exit();
	}

	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);

	if ($userSessionInfo->getRole() != null and $userSessionInfo->getRole() != '') {
		$userLevel = 10;
		$role = $userSessionInfo->getRole();
		if ('SUPERUSER' == $role) $userLevel = 0;
		else if ('ADMIN' == $role) $userLevel = 1;
		else if ('VERIFIER' == $role) $userLevel = 2;
		else if ('SUPERVISOR' == $role) $userLevel = 3;
	
		if ($userLevel > $level) {
			header("location: index.php");
			exit(); 
		}
	}
	else {
		header("location: index.php");
		exit(); 
	}
}

function isUserAccess($level) {

	$userSessionInfo = null;
	if($_SESSION["userSessionInfo"] == null) {
		header("location: logon.php");
		exit();
	}

	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);

	if ($userSessionInfo->getRole() != null and $userSessionInfo->getRole() != '') {
		$userLevel = 10;
		$role = $userSessionInfo->getRole();
		if ('SUPERUSER' == $role) $userLevel = 0;
		else if ('ADMIN' == $role) $userLevel = 1;
		else if ('VERIFIER' == $role) $userLevel = 2;
		else if ('SUPERVISOR' == $role) $userLevel = 3;
	
		if ($userLevel > $level) {
			return false;
		}
		else {
			return true;
		}
	}
	else {
		return false;
	}
}

function getCurrentRole() {
	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);

	if ($userSessionInfo->getRole() != null and $userSessionInfo->getRole() != '') {
		return $userSessionInfo->getRole();
	}
	return '';
}

function getCurrentUserId() {
	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);

	if ($userSessionInfo->getUserId() != null and $userSessionInfo->getUserId() != '') {
		return $userSessionInfo->getUserId();
	}
	return '';
}

function getUserState() {
	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);

	if ($userSessionInfo->getState() != null and $userSessionInfo->getState() != '') {
		return $userSessionInfo->getState();
	}
	return '';
}
?>