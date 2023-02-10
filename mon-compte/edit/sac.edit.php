<?php

require_once ("../../includes/account.init.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
	
	echo orderSacEdit($userID);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	require_once ("../../includes/pay.functions.php");
	
	$sacs = (!empty($_POST["sacs"])) ? $_POST["sacs"] : '1';	
	$montantTotal = $sacs*5;
	
	// USER REQUEST
	$user_rq = "
	SELECT users.*, adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle 
	FROM users 
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	WHERE users.id={$userID}";
	$user_rs = mysqli_query($connexion, $user_rq) or die();
	$user = mysqli_fetch_assoc($user_rs);
	
	if($user['societe']){
		$orderNom = $user['societe'];
		$pro = 1;
	}else{
		$orderNom = "{$user['nom']} {$user['prenom']}";
		$pro = 0;
	}
	
	$orderAdresse = "{$user['voieNumero']} {$user['voieType']} {$user['voieLibelle']}, 33000 Bordeaux";
	
	// INSERT ORDER
	$orderCreate_rq = "
	INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro)
	VALUES (NOW(), {$userID}, '{$orderNom}', '{$orderAdresse}', {$montantTotal}, {$pro})";
	$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
	$orderID = mysqli_insert_id($connexion);
	
	// INSERT SACS
	$sacsCreate_rq = "
	INSERT INTO sacs (nb, montant, orderID)
	VALUES ({$sacs}, {$montantTotal}, {$orderID})";
	$sacsCreate = mysqli_query($connexion, $sacsCreate_rq) or die();
	
	if(!userActive($userID)){
		
		// INSERT CREDITS
		$creditCreate_rq = "
		INSERT INTO credits (formuleID, nb, montant, orderID)
		VALUES (1, 1, 0, {$orderID})";
		$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
		
	}

	// VADS USER
	$_REQUEST['vads_cust_id'] = $userID;
	$_REQUEST['vads_cust_last_name'] = $user['nom'];
	$_REQUEST['vads_cust_first_name'] = $user['prenom'];
	$_REQUEST['vads_cust_address_number'] = $user['voieNumero'];
	$_REQUEST['vads_cust_address'] = $user['voieType']." ".$user['voieLibelle'];
	$_REQUEST['vads_cust_address2'] = $user['cpl'];
	$_REQUEST['vads_cust_zip'] = "33000";
	$_REQUEST['vads_cust_city'] = "Bordeaux";
	$_REQUEST['vads_cust_phone'] = $user['tel'];
	$_REQUEST['vads_cust_email'] = $user['email'];	
	
	$_REQUEST['vads_order_id'] = $orderID ;
	$_REQUEST['vads_nb_products'] = 1 ;
	$_REQUEST['vads_product_label0'] = "Sacs" ;
	$_REQUEST['vads_product_amount0'] = $montantTotal*100 ;
	$_REQUEST['vads_product_qty0'] = $sacs ;
	
	// VADS VARIABLES
	$_REQUEST['vads_theme_config'] = "CANCEL_FOOTER_MSG_RETURN=Annuler la commande;SUCCESS_FOOTER_MSG_RETURN=Retour à mon compte";
	$_REQUEST['vads_url_check'] = "https://mon-compte.pic-verre.fr/edit/sac.pay.php";
	$_REQUEST['vads_url_return'] = "https://mon-compte.pic-verre.fr/edit/sac.payreturn.php";
	$_REQUEST['vads_amount'] = $montantTotal*100;

	// CREATION DU FORMULAIRE DE PAIEMENT  encodé en UTF8
	$form = get_formHtml_request($_REQUEST, "fr"); 

	// REDIRECTION AUTOMATIQUE VERS PLATEFORME DE PAIEMENT SI DEBUG DESACTIVE
	// AFFICHAGE ET CONFIRMATION DU FORMULAIRE DE PAIEMENT AVANT REDIRECTION SI DEBUG ACTIVE
	$conf_txt = parse_ini_file("pay.conf.txt");
	if ($conf_txt['debug'] == 0){
		echo $form;
	}
	else{ 
		echo (display_form("fr",$form));
	}
	
}
	
?>