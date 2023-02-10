<?php
	session_start();
	
	require_once ("../../includes/connect.php");
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		$nom = (!empty($_POST["nom"])) ? strtolower(str_replace("'", "’", $_POST["nom"])) : '';
		$prenom = (!empty($_POST["prenom"])) ? strtolower(str_replace("'", "’", $_POST["prenom"])) : '';		
		$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
		$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
		$voieNumero = (!empty($_POST["voieNumero"])) ? $_POST["voieNumero"] : '';
		$voieSelect = (!empty($_POST["voieSelect"])) ? strtolower(str_replace("'", "’", $_POST["voieSelect"])) : '';
		$voieCpl = (!empty($_POST["voieCpl"])) ? strtolower(str_replace("'", "’", $_POST["voieCpl"])) : '';
		$code = (!empty($_POST["code"])) ? strtolower($_POST["code"]) : '';
		$err = 0;
		
		if(empty($nom)||empty($prenom)||empty($voieNumero)||empty($voieSelect)||empty($email)){
			$err = 1;
			$_SESSION['action'] = "user_errForm";
		}else{
		
			// EMAIL EXISTS
			$emailExist_rq = "SELECT id FROM users WHERE email='{$email}'";
			$emailExist_rs = mysqli_query($connexion, $emailExist_rq) or die();	
			if(mysqli_num_rows($emailExist_rs)){
				$err = 1;
				$_SESSION['action'] = "user_errMail";
			}

			// ADRESSE EXISTS
			$voie = explode(' ', $voieSelect, 2);
			$voieTest_rq="SELECT id FROM voies WHERE voieType='{$voie[0]}' AND voieLibelle='{$voie[1]}'";
			$voieTest_rs = mysqli_query($connexion, $voieTest_rq) or die();
			if(!mysqli_num_rows($voieTest_rs)){
				$err = 1;
				$_SESSION['action'] = "user_errVoie";
			}
			
			// CODE VALIDITY
			if(!empty($code)){
				$codeValid_rq = "SELECT id FROM resell WHERE code='{$code}' AND valid=1";
				$codeValid_rs = mysqli_query($connexion, $codeValid_rq) or die();
				
				if(!mysqli_num_rows($codeValid_rs)){
					$err = 1;
					$_SESSION['action'] = "user_errCode";
				}
	
			}
			
		}
		
		if(!$err){
			
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
			
			
			if(!empty($code)){
				
				$codeValid = mysqli_fetch_assoc($codeValid_rs);
				$resellID = $codeValid['id'];
				
				$resellUpdate_rq = "UPDATE resell SET valid=0 WHERE id={$resellID}";
				$resellUpdate_rs = mysqli_query($connexion, $resellUpdate_rq) or die();
				
				$bonusCreate_rq = "
				INSERT INTO bonus(dateCreation, userID, rewardID)
				VALUES (NOW(),'{$userID}',2)";
				$bonusCreate_rs = mysqli_query($connexion, $bonusCreate_rq) or die();
				
			}
			
			$token =  random_str(64);
				
			$resetDelete_rq = "DELETE FROM reset WHERE email='{$email}'";
			$resetDelete_rs = mysqli_query($connexion, $resetDelete_rq) or die();
				
			$resetCreate_rq = "INSERT INTO reset (email, token) VALUES ('{$email}','{$token}')";
			$resetCreate_rs = mysqli_query($connexion, $resetCreate_rq) or die();
			
			$title = "Initialisez votre mot de passe";
			$lead = "Merci d'avoir créé un compte Pic'Verre !<br>Cliquez sur le lien ci-dessous pour être redirigé vers la page permettant d'initialiser votre mot de passe.<br><small>Si vous n'avez pas fait cette demande, ignorez cet email.</small>";		
			$content = "<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:115%;padding:15px;background:#ffc107; display:block;' href='https://mon-compte.pic-verre.fr/reset.php?action=resetpwd&token={$token}&email={$email}'><strong>Initialiser mon mot de passe</strong></a>";
			
			sendmail($email, $title, $lead, $content);
			
			$_SESSION['logged'] = true;
			$_SESSION['userID'] = $userID ;
			$_SESSION['action'] = "user_setPwd";
			
			header("Location:../connexion.php");
			exit;
			
		}else{
			
			header("Location:../inscription.php");
			exit;

		}

	}
	
?>
