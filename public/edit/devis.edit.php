<?php

	// PHP MAILER
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require_once ("../../includes/connect.php");

	$societe = (!empty($_POST["societe"])) ? strtolower(str_replace("'", "’", $_POST["societe"])) : '';
	$nom = (!empty($_POST["nom"])) ? strtolower(str_replace("'", "’", $_POST["nom"])) : '';
	$prenom = (!empty($_POST["prenom"])) ? strtolower(str_replace("'", "’", $_POST["prenom"])) : '';		
	$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
	$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
	$voieNumero = (!empty($_POST["voieNumero"])) ? $_POST["voieNumero"] : '';
	$voieSelect = (!empty($_POST["voieSelect"])) ? strtolower(str_replace("'", "’", $_POST["voieSelect"])) : '';	
	$frequence = (!empty($_REQUEST["frequence"])) ? $_REQUEST["frequence"] : '';
	$sacs = (!empty($_REQUEST["sacs"])) ? $_REQUEST["sacs"] : '';
	$err = 0;
	
	if(empty($nom)||empty($prenom)||empty($voieNumero)||empty($voieSelect)||empty($email)||empty($tel)){
		$err = 1;
		$action = "errForm";
		
	}else{
	
		// EMAIL EXISTS
		$emailExist_rq = "SELECT id FROM users WHERE email='{$email}' AND valid=1";
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
		
		$action = "valid";
		
		$voieTest = mysqli_fetch_assoc($voieTest_rs);
		$voieID = $voieTest['id'];
		
		$adresse = "{$voieNumero} {$voieSelect}";
		
		$credits = $sacs*$frequence*12;
		$orderMontant = 2.5*$credits;
		
		// INSERT ADRESSE
		$adresseCreate_rq = "
		INSERT INTO adresses (voieNumero, voieID, cpl)
		VALUES ('{$voieNumero}','{$voieID}', '{$voieCpl}')";
		$adresseCreate_rs = mysqli_query($connexion, $adresseCreate_rq) or die();
		$adresseID = mysqli_insert_id($connexion);	
						
		$userCreate_rq = "
		INSERT INTO users(dateCreation, nom, prenom, societe, adresseID, tel, email)
		VALUES (NOW(),'{$nom}','{$prenom}','{$societe}',{$adresseID},'{$tel}','{$email}')";
		$userCreate_rs = mysqli_query($connexion, $userCreate_rq) or die();
		$userID = mysqli_insert_id($connexion);
		
		$orderCreate_rq = "
		INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro)
		VALUES (NOW(), {$userID}, '{$societe}', '{$adresse}', {$orderMontant}, 1)";				
		$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
		$orderID = mysqli_insert_id($connexion);
		
		$creditCreate_rq = "
		INSERT INTO credits (formuleID, nb, montant, orderID)
		VALUES (0, {$credits}, {$orderMontant}, {$orderID})";
		$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
		
	}
	
	header("location:../errors/devis.php?action={$action}");
	
?>