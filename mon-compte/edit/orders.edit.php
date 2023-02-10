<?php
	require_once ("../../includes/account.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$orderID = (!empty($_REQUEST["orderID"])) ? $_REQUEST["orderID"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){

		if($action=='detail'){
			echo orderDetail($orderID);
		}
		
	}

?>

