<?php



require_once ("../../includes/admin.init.php");
	
$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
$depositID = (!empty($_REQUEST["depositID"])) ? $_REQUEST["depositID"] : '';

$deposit_rq = "SELECT * FROM resell WHERE id={$depositID}";
$deposit_rs = mysqli_query($connexion, $deposit_rq) or die(mysqli_error($connexion));
$deposit = mysqli_fetch_array($deposit_rs);

$depositUpdate_rq = "UPDATE resell SET dateSold=NOW() WHERE id={$depositID}";
$depositUpdate_rs = mysqli_query($connexion, $depositUpdate_rq) or die(mysqli_error($connexion));

$transCreate_rq = "INSERT INTO transactions (dateCreation, ref) VALUES (NOW(), '{$deposit['code']}')";
$transCreate_rs = mysqli_query($connexion, $transCreate_rq) or die();
$tID = mysqli_insert_id($connexion);

$orderCreate_rq = "
INSERT INTO orders (dateCreation, resellerID, montant, pro, reglement, tID) 
VALUES (NOW(), {$deposit['resellerID']}, 5, 0, 'ESP',{$tID})";
$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die(mysqli_error($connexion));
$orderID = mysqli_insert_id($connexion);

$sacCreate_rq = "INSERT INTO sacs (nb, montant, orderID) VALUES (1, 5, {$orderID})";				
$sacCreate_rs = mysqli_query($connexion, $sacCreate_rq) or die(mysqli_error($connexion));

header("location:../resellers.php?resellerID={$deposit['resellerID']}");
	
	
?>