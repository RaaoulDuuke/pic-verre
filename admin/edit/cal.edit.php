<?php
	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$calID = (!empty($_REQUEST["calID"])) ? $_REQUEST["calID"] : '';
	$calDate= (!empty($_REQUEST["calDate"])) ? $_REQUEST["calDate"] : '';

	echo calEdit($action, $calID, $calDate);
?>

