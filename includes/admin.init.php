<?php
	
	session_start();
	require_once ("connect.php");

	$adminID = (!empty($_SESSION["adminID"])) ? $_SESSION["adminID"] : '';
	$parts = Explode('/', $_SERVER["PHP_SELF"]);
	$page = $parts[count($parts) - 1];
	
	if(!empty($adminID)){
		$userExist_rq = "SELECT id FROM admin WHERE id={$adminID}";
		$userExist_rs = mysqli_query($connexion, $userExist_rq) or die();
		$userExist = mysqli_num_rows($userExist_rs);
	}
	
	if($page=="login.php"){
		if (!empty($adminID) && $userExist) {
			header("location:index.php");
			exit;
		}
	}else{
		if (empty($adminID) || !$userExist) {
			header("location:login.php");
			exit;
		}	
	}

	require_once("common.functions.php");
	require_once("admin/global.functions.php");
	require_once("admin/user.functions.php");
	require_once("admin/users.functions.php");
	require_once("admin/pick.functions.php");
	require_once("admin/picks.functions.php");
	require_once("admin/order.functions.php");
	require_once("admin/orders.functions.php");
	require_once("admin/reseller.functions.php");
	require_once("admin/resellers.functions.php");
	
?>