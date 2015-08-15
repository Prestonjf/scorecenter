<?php
// Validates that the user session information has been set and is valid

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
}
?>