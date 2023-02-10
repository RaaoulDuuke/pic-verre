<?php
	session_start();
	
	require_once ("../../includes/connect.php");
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		$societe = (!empty($_POST["societe"])) ? strtolower(str_replace("'", "’", $_POST["societe"])) : '';
		$nom = (!empty($_POST["nom"])) ? strtolower(str_replace("'", "’", $_POST["nom"])) : '';
		$prenom = (!empty($_POST["prenom"])) ? strtolower(str_replace("'", "’", $_POST["prenom"])) : '';
		$voieNumero = (!empty($_POST["voieNumero"])) ? $_POST["voieNumero"] : '';
		$voieSelect = (!empty($_POST["voieSelect"])) ? strtolower(str_replace("'", "’", $_POST["voieSelect"])) : '';
		$voieCpl = (!empty($_POST["voieCpl"])) ? strtolower(str_replace("'", "’", $_POST["voieCpl"])) : '';
		$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
		$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
		$subID = (!empty($_POST["subID"])) ? $_POST["subID"] : 1;
		$formuleID = (!empty($_POST["formuleID"])) ? $_POST["formuleID"] : '';
		$credits = (!empty($_POST["credits"])) ? $_POST["credits"] : '';
		$pwd = (!empty($_POST["pwd"])) ? $_POST["pwd"] : '';

		$err = 0;

		// REQUESTED VARIABLES
		if(empty($nom)||empty($prenom)||empty($voieNumero)||empty($voieSelect)||empty($email)||empty($tel)||empty($formuleID)||empty($subID)||empty($pwd)){
			$err = 1;
			$action = "errForm";
		}

		// EMAIL EXISTS
		$emailExist_rq = "SELECT id FROM users WHERE email='{$email}'";
		$emailExist_rs = mysqli_query($connexion, $emailExist_rq) or die();	
		if(mysqli_num_rows($emailExist_rs)){
			$err = 1;
			$action = "errMail";
		}

		// ADRESSE EXISTS
		$voie = explode(' ', $voieSelect, 2);
		$voieTest_rq="SELECT id FROM voies WHERE voieType='{$voie[0]}' AND voieLibelle='{$voie[1]}'";
		$voieTest_rs = mysqli_query($connexion, $voieTest_rq) or die();
		if(!mysqli_num_rows($voieTest_rs)){
			$err = 1;
			$action = "errVoie";
		}

		if(!$err){
			
			require_once ("../../includes/pay.functions.php");
			include ("../../includes/pay.fr.php");
			
			$sub_rq = "SELECT tarif FROM subs WHERE id={$subID}";
			$sub_rs = mysqli_query($connexion, $sub_rq) or die();
			$sub = mysqli_fetch_assoc($sub_rs);
			$montantSub = $sub['tarif'];

			$formule_rq = "SELECT * FROM formules WHERE id={$formuleID} ";
			$formule_rs = mysqli_query($connexion, $formule_rq) or die();
			$formule = mysqli_fetch_assoc($formule_rs);
			if($formule['credits']){
				$montantCredits = $formule['montant'];
				$credits = $formule["credits"];
			}else{
				$montantCredits = $credits*$formule['montant'];
			}

			$montantTotal = $montantSub+$montantCredits;

			$voieTest = mysqli_fetch_assoc($voieTest_rs);
			$voieID = $voieTest['id'];
			
			if($societe){
				$orderNom = $societe;
				$pro = 1;
			}else{
				$orderNom = "{$nom} {$prenom}";
				$pro = 0;
			}
			
			$orderAdresse = "{$voieNumero} {$voieSelect}, 33000 Bordeaux";
			
			// INSERT ADRESSE
			$adresseCreate_rq = "
			INSERT INTO adresses (voieNumero, voieID, cpl)
			VALUES ('{$voieNumero}','{$voieID}', '{$voieCpl}')";
			$adresseCreate_rs = mysqli_query($connexion, $adresseCreate_rq) or die();
			$adresseID = mysqli_insert_id($connexion);		

			// INSERT USER
			$salt = uniqid(mt_rand());
			$pwdenc = hash_hmac('sha256', $pwd, $salt);

			$userCreate_rq = "
			INSERT INTO users(dateCreation, nom, prenom, societe, adresseID, tel, email, pwdenc)
			VALUES (NOW(),'{$nom}','{$prenom}','{$societe}',{$adresseID},'{$tel}','{$email}','{$pwdenc}')";
			$userCreate_rs = mysqli_query($connexion, $userCreate_rq) or die();
			$userID = mysqli_insert_id($connexion);

			// INSERT SALT
			$saltCreate_rq = "INSERT INTO testUser(salt, userID) VALUES ('{$salt}',{$userID})";
			$saltCreate_rs = mysqli_query($connexion, $saltCreate_rq) or die();	
			
			// INSERT ORDER
			$orderCreate_rq = "
			INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro)
			VALUES (NOW(), {$userID}, '{$orderNom}', '{$orderAdresse}', {$montantTotal}, {$pro})";
			$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
			$orderID = mysqli_insert_id($connexion);
			
			// INSERT CREDITS 
			$creditCreate_rq = "
			INSERT INTO credits (formuleID, nb, montant, orderID)
			VALUES ({$formuleID}, {$credits}, {$montantCredits}, {$orderID})";
			$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
			
			// INSERT ABO
			$aboCreate_rq = "
			INSERT INTO abos (subID, orderID)
			VALUES ({$subID}, {$orderID})";
			$aboCreate_rs = mysqli_query($connexion, $aboCreate_rq) or die();
			
			// INSERT SACS
			$sacsCreate_rq = "
			INSERT INTO sacs (nb, orderID) 
			VALUES (1, {$orderID})";
			$sacsCreate = mysqli_query($connexion, $sacsCreate_rq) or die();

			// VADS VARIABLES
			$_REQUEST['vads_theme_config'] = "CANCEL_FOOTER_MSG_RETURN=Annuler la commande;SUCCESS_FOOTER_MSG_RETURN=Retour à mon compte";
			$_REQUEST['vads_url_check'] = "https://mon-compte.pic-verre.fr/edit/abo.pay.php";
			$_REQUEST['vads_url_return'] = "https://mon-compte.pic-verre.fr/edit/abo.payreturn.php";
			$_REQUEST['vads_amount'] = $montantTotal*100;

			$_REQUEST['vads_cust_id'] = $userID;
			$_REQUEST['vads_cust_last_name'] = $nom;
			$_REQUEST['vads_cust_first_name'] = $prenom;
			$_REQUEST['vads_cust_address_number'] = $voieNumero;
			$_REQUEST['vads_cust_address'] = $voieSelect;
			$_REQUEST['vads_cust_address2'] = $voieCpl;
			$_REQUEST['vads_cust_zip'] = "33000";
			$_REQUEST['vads_cust_city'] = "Bordeaux";
			$_REQUEST['vads_cust_phone'] = $tel;
			$_REQUEST['vads_cust_email'] = $email;

			$_REQUEST['vads_order_id'] = $orderID ;
			$_REQUEST['vads_nb_products'] = 2 ;
			$_REQUEST['vads_product_label0'] = "Adhésion";
			$_REQUEST['vads_product_amount0'] = $montantSub*100;
			$_REQUEST['vads_product_qty0'] = 1 ;
			$_REQUEST['vads_product_label1'] = "Crédits";
			$_REQUEST['vads_product_amount1'] = $montantCredits*100 ;
			$_REQUEST['vads_product_qty1'] = $credits ;
			
			if(!empty($societe)){
				$_REQUEST['vads_cust_legal_name'] = $societe;
				$_REQUEST['vads_cust_status'] = "COMPANY";
			}else{
				$_REQUEST['vads_cust_status'] = "PRIVATE";
			}
			

			// CREATION DU FORMULAIRE DE PAIEMENT  encodé en UTF8
			$form = get_formHtml_request($_REQUEST, "fr"); 

			// REDIRECTION AUTOMATIQUE VERS PLATEFORME DE PAIEMENT SI DEBUG DESACTIVE
			// AFFICHAGE ET CONFIRMATION DU FORMULAIRE DE PAIEMENT AVANT REDIRECTION SI DEBUG ACTIVE
			$conf_txt = parse_ini_file("../../includes/pay.conf.txt");
			if ($conf_txt['debug'] == 0){
				echo $form;
			}
			else{ 
				echo (display_form("fr",$form));
			}
			
		}else{
			
			header("location:https://mon-compte.pic-verre.fr/abonnement.php?action={$action}");
			exit;
			
		}

	}else{
		
		header("location:https://mon-compte.pic-verre.fr/abonnement.php");
		exit;
		
	}
?>