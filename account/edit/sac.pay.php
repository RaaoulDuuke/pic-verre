<?php

session_start();

// PHP MAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ("../../includes/connect.php");

$orderID = (!empty($_REQUEST["vads_order_id"])) ? $_REQUEST["vads_order_id"] : '';
$transID = (!empty($_REQUEST["vads_trans_id"])) ? $_REQUEST["vads_trans_id"] : '';
$transStatut = (!empty($_REQUEST["vads_trans_status"])) ? $_REQUEST["vads_trans_status"] : '';
$result = (!empty($_REQUEST["vads_result"])) ? $_REQUEST["vads_result"] : '';
$authResult = (!empty($_REQUEST["vads_auth_result"])) ? $_REQUEST["vads_auth_result"] : '';
$threedsStatus = (!empty($_REQUEST["vads_threeds_status"])) ? $_REQUEST["vads_threeds_status"] : '';

if($transStatut=="AUTHORISED" && $result=="00" && $authResult==00 && $threedsStatus=="Y"){
	
	require_once ("../../includes/common.functions.php");
	require_once ("../../includes/mails.functions.php");
	
	// SEND MAIL
	orderMail($orderID);
	
	// INSERT TRANSACTIONS
	$transCreate_rq = "INSERT INTO transactions (dateCreation, ref) VALUES (NOW(), '{$transID}')";
	$transCreate_rs = mysqli_query($connexion, $transCreate_rq) or die();
	$tID = mysqli_insert_id($connexion);
	
	// UPDATE ORDERS
	$ordersUpdate_rq = "UPDATE orders SET tID={$tID} WHERE id={$orderID}";
	$ordersUpdate_rs = mysqli_query($connexion, $ordersUpdate_rq) or die();
	
}else{

	// DELETE ORDER
	$orderCreate_rq = "DELETE FROM orders WHERE id={$orderID}";
	$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
	
	// DELETE SACS
	$sacsCreate_rq = "DELETE FROM sacs WHERE orderID={$orderID}";
	$sacsCreate_rs = mysqli_query($connexion, $sacsCreate_rq) or die();

}

?>