<?php

	session_start();

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
			
		$formuleID = (!empty($_POST["formuleID"])) ? $_POST["formuleID"] : 0;
		$credits = (!empty($_POST["credits"])) ? $_POST["credits"] : 0;
		
		$calID = (!empty($_REQUEST["calID"])) ? $_REQUEST["calID"] : '';
		$slotID = (!empty($_POST["slotID"])) ? $_POST["slotID"] : '1';
		$sacs = (!empty($_POST["sacs"])) ? $_POST["sacs"] : '0';
		
		$vadsProductNb = 1;
		$orderMontant = 5;
		
		$err = 0;
		
		if(empty($nom)||empty($prenom)||empty($voieNumero)||empty($voieSelect)||empty($email)){
			$err = 1;
			$action = $_SESSION['action'] = "errForm";
		}else{
		
			// EMAIL EXISTS
			$emailExist_rq = "SELECT id FROM users WHERE email='{$email}'";
			$emailExist_rs = mysqli_query($connexion, $emailExist_rq) or die();	
			if(mysqli_num_rows($emailExist_rs)){
				$err = 1;
				$action = $_SESSION['action'] = "errMail";
			}

			// ADRESSE EXISTS
			$voie = explode(' ', $voieSelect, 2);
			$voieTest_rq="SELECT id FROM voies WHERE voieType='{$voie[0]}' AND voieLibelle='{$voie[1]}'";
			$voieTest_rs = mysqli_query($connexion, $voieTest_rq) or die();
			if(!mysqli_num_rows($voieTest_rs)){
				$err = 1;
				$action = $_SESSION['action'] = "errVoie";
			}
			
		}
		
		if(!$err){
			
			require_once ("../../includes/pay.functions.php");
			require_once ("../../includes/common.functions.php");
			require_once ("../../includes/mails.functions.php");
			
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
			
			$_SESSION['logged'] = true;
			$_SESSION['userID'] = $userID ;
			
			$token =  random_str(64);			
			$resetDelete_rq = "DELETE FROM reset WHERE email='{$email}'";
			$resetDelete_rs = mysqli_query($connexion, $resetDelete_rq) or die();
			$resetCreate_rq = "INSERT INTO reset (email, token) VALUES ('{$email}','{$token}')";
			$resetCreate_rs = mysqli_query($connexion, $resetCreate_rq) or die();			
			$title = "Initialisez votre mot de passe";
			$lead = "Merci d'avoir créé un compte Pic'Verre !<br>Cliquez sur le lien ci-dessous pour être redirigé vers la page permettant d'initialiser votre mot de passe.<br><small>Si vous n'avez pas fait cette demande, ignorez cet email.</small>";		
			$content = "<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:115%;padding:15px;background:#ffc107; display:block;' href='https://mon-compte.pic-verre.fr/reset.php?action=resetpwd&token={$token}&email={$email}'><strong>Initialiser mon mot de passe</strong></a>";		
			sendmail($email, $title, $lead, $content);

			$orderNom = "{$nom} {$prenom}";
			$orderAdresse = "{$voieNumero} {$voie[0]} {$voie[1]}, 33000 Bordeaux";
			
			if($formuleID){
							
				$formule_rq = "SELECT * FROM formules WHERE id={$formuleID} ";
				$formule_rs = mysqli_query($connexion, $formule_rq) or die();
				$formule = mysqli_fetch_assoc($formule_rs);
				if($formule['credits']){
					$credits = $formule["credits"];
					$montantCredits = $formule["montant"];
				}else{
					$montantCredits = $formule["montant"]*$credits;
				}
				
				$orderMontant += $montantCredits;

			}

			// INSERT ORDER
			$orderCreate_rq = "
			INSERT INTO orders (dateCreation, userID, nom, facturation, montant)
			VALUES (NOW(), {$userID}, '{$orderNom}', '{$orderAdresse}', {$orderMontant})";
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

			if($formuleID){
							
				// INSERT CREDITS
				$creditCreate_rq = "
				INSERT INTO credits (formuleID, nb, montant, orderID)
				VALUES ({$formuleID}, {$credits}, {$montantCredits}, {$orderID})";
				$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
				
				// VADS CREDITS
				$_REQUEST["vads_product_label1"] = "Crédits" ;
				$_REQUEST["vads_product_label1"] = $montantCredits*100 ;
				$_REQUEST["vads_product_label1"] = $credits ;
				
				$vadsProductNb += 1;

			}
			
			// INSERT PICK
			$pickCreate_rq = "
			INSERT INTO picks (calID, slotID, sacs, userID, adresseID, bundle, valid)
			VALUES ({$calID}, {$slotID}, {$sacs}, {$userID}, {$adresseID}, 0, 0)";
			$pickCreate_rs = mysqli_query($connexion, $pickCreate_rq) or die(mysqli_error($connexion));
			$pickID =  mysqli_insert_id($connexion);
				
			// INSERT PICK ORDER
			$pickOrderCreate_rq = "
			INSERT INTO picksOrders (pickID, orderID) VALUES ({$pickID}, {$orderID})";
			$pickOrderCreate_rs = mysqli_query($connexion, $pickOrderCreate_rq) or die(mysqli_error($connexion));
			

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
			$_REQUEST['vads_nb_products'] = $vadsProductNb ;
			
			$_REQUEST['vads_product_label0'] = "Sacs" ;
			$_REQUEST['vads_product_amount0'] = 500 ;
			$_REQUEST['vads_product_qty0'] = 1 ;
			
			// VADS VARIABLES
			$_REQUEST['vads_theme_config'] = "CANCEL_FOOTER_MSG_RETURN=Annuler la commande;SUCCESS_FOOTER_MSG_RETURN=Retour à mon compte";
			$_REQUEST['vads_url_check'] = "https://mon-compte.pic-verre.fr/edit/credit.pay.php";
			$_REQUEST['vads_url_return'] = "https://mon-compte.pic-verre.fr/edit/credit.payreturn.php";
			$_REQUEST['vads_amount'] = $orderMontant*100;
			

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
			
			
		}else{
			
			header("Location:https://www.pic-verre.fr/programmer?{$action}");
			exit;

		}
	}
	
?>