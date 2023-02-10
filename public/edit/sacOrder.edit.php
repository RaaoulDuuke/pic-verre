<?php

	// PHP MAILER
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require_once ("../../includes/connect.php");

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		

		$nom = (!empty($_POST["nom"])) ? strtolower(str_replace("'", "’", $_POST["nom"])) : '';
		$prenom = (!empty($_POST["prenom"])) ? strtolower(str_replace("'", "’", $_POST["prenom"])) : '';		
		$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
		$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
		$voieNumero = (!empty($_POST["voieNumero"])) ? $_POST["voieNumero"] : '';
		$voieSelect = (!empty($_POST["voieSelect"])) ? strtolower(str_replace("'", "’", $_POST["voieSelect"])) : '';
		$voieCpl = (!empty($_POST["voieCpl"])) ? strtolower(str_replace("'", "’", $_POST["voieCpl"])) : '';
		$sacs = (!empty($_POST["sacs"])) ? $_POST["sacs"] : '1';	
		$montantTotal = 5;
		
		$err = 0;
		
		if(empty($nom)||empty($prenom)||empty($voieNumero)||empty($voieSelect)||empty($email)){
			$err = 1;
			$action = "errForm";
		}else{
		
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
			
		}
		
		if(!$err){
			
			require_once ("../../includes/pay.functions.php");
			
			$voieTest = mysqli_fetch_assoc($voieTest_rs);
			$voieID = $voieTest['id'];
			
			// INSERT ADRESSE
			$adresseCreate_rq = "
			INSERT INTO adresses (voieNumero, voieID, cpl)
			VALUES ('{$voieNumero}','{$voieID}', '{$voieCpl}')";
			$adresseCreate_rs = mysqli_query($connexion, $adresseCreate_rq) or die();
			$adresseID = mysqli_insert_id($connexion);	
							
			$userCreate_rq = "
			INSERT INTO users(dateCreation, nom, prenom, adresseID, tel, email)
			VALUES (NOW(),'{$nom}','{$prenom}',{$adresseID},'{$tel}','{$email}')";
			$userCreate_rs = mysqli_query($connexion, $userCreate_rq) or die();
			$userID = mysqli_insert_id($connexion);
			
			
			$orderNom = "{$nom} {$prenom}";
			$orderAdresse = "{$voieNumero} {$voie[0]} {$voie[1]}, 33000 Bordeaux";
			
			// INSERT ORDER
			$orderCreate_rq = "
			INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro)
			VALUES (NOW(), {$userID}, '{$orderNom}', '{$orderAdresse}', 5, 0)";
			$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
			$orderID = mysqli_insert_id($connexion);
			
			// INSERT SACS
			$sacsCreate_rq = "
			INSERT INTO sacs (nb, montant, orderID)
			VALUES (1, 5, {$orderID})";
			$sacsCreate = mysqli_query($connexion, $sacsCreate_rq) or die();
			
			$creditCreate_rq = "
			INSERT INTO credits (formuleID, nb, montant, orderID)
			VALUES (1, 1, 0, {$orderID})";
			$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
			
			
			// VADS USER
			$_REQUEST['vads_cust_id'] = $userID;
			$_REQUEST['vads_cust_last_name'] = $nom;
			$_REQUEST['vads_cust_first_name'] = $prenom;
			$_REQUEST['vads_cust_address_number'] = $voieNumero;
			$_REQUEST['vads_cust_address'] = $voie[0]." ".$voie[1];
			$_REQUEST['vads_cust_address2'] = $cpl;
			$_REQUEST['vads_cust_zip'] = "33000";
			$_REQUEST['vads_cust_city'] = "Bordeaux";
			$_REQUEST['vads_cust_phone'] = $tel;
			$_REQUEST['vads_cust_email'] = $email;	
			
			$_REQUEST['vads_order_id'] = $orderID ;
			$_REQUEST['vads_nb_products'] = 1 ;
			$_REQUEST['vads_product_label0'] = "Sacs" ;
			$_REQUEST['vads_product_amount0'] = 500 ;
			$_REQUEST['vads_product_qty0'] = 1 ;
			
			// VADS VARIABLES
			$_REQUEST['vads_theme_config'] = "CANCEL_FOOTER_MSG_RETURN=Annuler la commande;SUCCESS_FOOTER_MSG_RETURN=Retour à mon compte";
			$_REQUEST['vads_url_check'] = "https://mon-compte.pic-verre.fr/edit/sac.pay.php";
			$_REQUEST['vads_url_return'] = "https://mon-compte.pic-verre.fr/edit/sac.paywebsite.php";
			$_REQUEST['vads_amount'] = 500;

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
			
			/*
			header("Location:https://www.pic-verre.fr/sac");
			exit;
			*/
			
		}else{
			
			header("Location:https://www.pic-verre.fr");
			exit;

		}
	}
	
?>