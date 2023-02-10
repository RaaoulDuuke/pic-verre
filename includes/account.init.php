<?php

	// PHP MAILER
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	session_start();
	require_once ("connect.php");
	
	$userID = (!empty($_SESSION["userID"])) ? $_SESSION["userID"] : '';
	$parts = Explode('/', $_SERVER["PHP_SELF"]);
	$page = $parts[count($parts) - 1];
	
	if(!empty($userID)){
		//$userExist_rq = "SELECT id FROM users WHERE id={$userID} AND valid=1";
		$userExist_rq = "SELECT id FROM users WHERE id={$userID}";
		$userExist_rs = mysqli_query($connexion, $userExist_rq) or die();
		$userExist = mysqli_num_rows($userExist_rs);
	}
	
	if($page=="connexion.php" || $page=="abonnement.php" || $page=="reset.php" || $page=="inscription.php" || $page=="programmer.php"){
		if (!empty($userID) && $userExist) {
			header("location:https://mon-compte.pic-verre.fr/index.php");
			exit;
		}
	}else{
		if (empty($userID) || !$userExist) {
			header("location:https://mon-compte.pic-verre.fr/connexion.php");
			exit;
		}	
	}
	
	require_once("account.local.php");
	require_once("common.functions.php");
	require_once("account/global.functions.php");
	require_once("account/pick.functions.php");
	require_once("account/order.functions.php");
	require_once("account/sections.functions.php");
	
?>