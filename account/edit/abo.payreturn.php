<?php

session_start();

$userID = (!empty($_POST["vads_cust_id"])) ? $_POST["vads_cust_id"] : '';
$orderID = (!empty($_POST["vads_order_id"])) ? $_POST["vads_order_id"] : '';
$transStatut = (!empty($_POST["vads_trans_status"])) ? $_POST["vads_trans_status"] : '';
$result = (!empty($_POST["vads_result"])) ? $_POST["vads_result"] : '';
$authResult = (!empty($_POST["vads_auth_result"])) ? $_POST["vads_auth_result"] : '';
$warrantyResult = (!empty($_POST["vads_warranty_result"])) ? $_POST["vads_warranty_result"] : '';
$threedsStatus = (!empty($_POST["vads_threeds_status"])) ? $_POST["vads_threeds_status"] : '';
	
if($transStatut=="AUTHORISED" && $result=="00" && $authResult==00 && $threedsStatus=="Y"){
	
	// SESSION VAR
	$_SESSION['logged'] = true;
	$_SESSION['userID'] = $userID;
	
	// REDIRECTION
	header("location:https://mon-compte.pic-verre.fr/index.php?action=sub");
	exit;
	
}else{
	
	// REDIRECTION
	header("location:https://mon-compte.pic-verre.fr/abonnement.php?action=sub_{$transStatut}");
	exit;
	
}

?>