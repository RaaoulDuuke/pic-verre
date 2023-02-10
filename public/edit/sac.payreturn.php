<?php

session_start();

$orderID = (!empty($_REQUEST["vads_order_id"])) ? $_REQUEST["vads_order_id"] : '';
$transStatut = (!empty($_REQUEST["vads_trans_status"])) ? $_REQUEST["vads_trans_status"] : '';
$result = (!empty($_REQUEST["vads_result"])) ? $_REQUEST["vads_result"] : '';
$authResult = (!empty($_REQUEST["vads_auth_result"])) ? $_REQUEST["vads_auth_result"] : '';
$threedsStatus = (!empty($_REQUEST["vads_threeds_status"])) ? $_REQUEST["vads_threeds_status"] : '';

if(!empty($orderID)&&!empty($transStatut)&&!empty($result)&&!empty($authResult)&&!empty($threedsStatus)){

	header("Location:https://mon-compte.pic-verre.fr/connexion.php?action=user_setPwd");
	exit;

}else{
	
	header("location:https://mon-compte.pic-verre.fr/");
	exit;
	
}

	
?>