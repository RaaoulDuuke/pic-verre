<?php

session_start();

// PHP MAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once ("../../includes/connect.php");
require_once ("../../includes/common.functions.php");
require_once ("../../includes/mails.functions.php");

$userID = (!empty($_REQUEST["vads_cust_id"])) ? $_REQUEST["vads_cust_id"] : '';
$email = (!empty($_REQUEST["vads_cust_email"])) ? $_REQUEST["vads_cust_email"] : '';
$orderID = (!empty($_REQUEST["vads_order_id"])) ? $_REQUEST["vads_order_id"] : '';
$transID = (!empty($_REQUEST["vads_trans_id"])) ? $_REQUEST["vads_trans_id"] : '';
$transStatut = (!empty($_REQUEST["vads_trans_status"])) ? $_REQUEST["vads_trans_status"] : '';
$result = (!empty($_REQUEST["vads_result"])) ? $_REQUEST["vads_result"] : '';
$authResult = (!empty($_REQUEST["vads_auth_result"])) ? $_REQUEST["vads_auth_result"] : '';
$warrantyResult = (!empty($_REQUEST["vads_warranty_result"])) ? $_REQUEST["vads_warranty_result"] : '';
$threedsStatus = (!empty($_REQUEST["vads_threeds_status"])) ? $_REQUEST["vads_threeds_status"] : '';

if($transStatut=="AUTHORISED" && $result=="00" && $authResult==00 && $threedsStatus=="Y"){
	
	// UPDATE USER
	$userUpdate_rq = "UPDATE users SET valid=1 WHERE id={$userID}";
	$userUpdate_rs = mysqli_query($connexion, $userUpdate_rq) or die();
		
	// UPDATE ORDERS
	$orderUpdate_rq = "UPDATE orders SET transID={$transID} WHERE id={$orderID}";
	$orderUpdate_rs = mysqli_query($connexion, $orderUpdate_rq) or die();
	
	// INSERT TRANSACTIONS
	$transCreate_rq = "INSERT INTO transactions (dateCreation, transID) VALUES (NOW(), {$transID})";
	$transCreate_rs = mysqli_query($connexion, $transCreate_rq) or die();
	
	// SEND MAIL
	creditMail("sub", $orderID);
	
	// SPONSORSHIP	
	$sponsor_rq = "SELECT * FROM sponsors WHERE email='{$email}'";
	$sponsor_rs = mysqli_query($connexion, $sponsor_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($sponsor_rs)){
		$sponsor = mysqli_fetch_assoc($sponsor_rs);
		// INSERT ASSO
		$assoCreate_rq = "
		INSERT INTO assos (dateCreation, userID, partnerID, dateValid) 
		VALUES ('{$sponsor['dateCreation']}', {$sponsor['userID']}, {$userID}, NOW())";
		$assoCreate_rs = mysqli_query($connexion, $assoCreate_rq) or die(mysqli_error($connexion));
		$assoID = mysqli_insert_id($connexion);
		// SPONSORS REQUEST
		$sponsorBonus_rq = "
		SELECT sponsors.id FROM sponsors
		WHERE sponsors.userID={$sponsor['userID']} 
		AND EXISTS ( SELECT id FROM users WHERE users.email=sponsors.email AND users.valid=1)";
		$sponsorBonus_rs = mysqli_query($connexion, $sponsorBonus_rq) or die(mysqli_error($connexion));
		$sponsorBonus_nb = mysqli_num_rows($sponsorBonus_rs);
		// BONUS TEST
		if($sponsorBonus_nb%3==0){
			$sponsorship = "bonus";
			// INSERT BONUS
			$bonusCreate_rq = "
			INSERT INTO bonus (dateCreation, userID, rewardID) 
			VALUES (NOW(), {$sponsor['userID']}, 1)";
			$bonusCreate_rs = mysqli_query($connexion, $bonusCreate_rq) or die(mysqli_error($connexion));				
		}
		
		// SEND MAIL
		assoMail("accept", $assoID, 1);
	}
	
	
	// REWARD ACTIVE
	$rewardLimit_rq = "SELECT * FROM bonus WHERE rewardID = 3";
	$rewardLimit_rs = mysqli_query($connexion, $rewardLimit_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($rewardLimit_rs)<25){
		$rewardList_rq = "SELECT * FROM msurvio WHERE email='{$email}'";
		$rewardList_rs = mysqli_query($connexion, $rewardList_rq) or die(mysqli_error($connexion));
		if(mysqli_num_rows($rewardList_rs)){
			// INSERT BONUS
			$bonusCreate_rq = "
			INSERT INTO bonus (dateCreation, userID, rewardID) 
			VALUES (NOW(), {$userID}, 3)";
			$bonusCreate_rs = mysqli_query($connexion, $bonusCreate_rq) or die(mysqli_error($connexion));	
		}
	}
	

}else{
	
	// DELETE USER
	$userCreate_rq = "DELETE FROM users WHERE id={$userID}";
	$userCreate_rs = mysqli_query($connexion, $userCreate_rq) or die();

	// DELETE SALT
	$saltCreate_rq = "DELETE FROM testUser WHERE userID={$userID}";
	$saltCreate_rs = mysqli_query($connexion, $saltCreate_rq) or die(mysqli_error($connexion));

	// DELETE ORDER
	$orderCreate_rq = "DELETE FROM orders WHERE id={$orderID}";
	$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();

	// DELETE ABO
	$aboCreate_rq = "DELETE FROM abos WHERE orderID={$orderID}";
	$aboCreate_rs = mysqli_query($connexion, $aboCreate_rq) or die();
	
	// DELETE CREDITS
	$creditCreate_rq = "DELETE FROM credits WHERE orderID={$orderID}";
	$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
	
	// DELETE SACS
	$sacsCreate_rq = "DELETE FROM sacs WHERE orderID={$orderID}";
	$sacsCreate_rs = mysqli_query($connexion, $sacsCreate_rq) or die();
	
}

?>