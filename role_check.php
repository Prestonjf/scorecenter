<?php
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
		if ('ADMIN' == $role) $userLevel = 1;
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

function getCurrentRole() {
	$userSessionInfo = unserialize($_SESSION["userSessionInfo"]);

	if ($userSessionInfo->getRole() != null and $userSessionInfo->getRole() != '') {
		return $userSessionInfo->getRole();
	}
	return '';
}
?>