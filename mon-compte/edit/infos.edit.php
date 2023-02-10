<?php

	require_once ("../../includes/account.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		echo infosEdit($userID, $action);
	}
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		switch($action){
			
			// UPDATE ADRESSE
			case 'update':
				
				$err = "";
				
				$voieNumero = (!empty($_POST["voieNumero"])) ? $_POST["voieNumero"] : '';
				$selectVoie = (!empty($_POST["selectVoie"])) ? $_POST["selectVoie"] : '';
				$cpl = (!empty($_POST["cpl"])) ? $_POST["cpl"] : '';
				
				$nom = (!empty($_POST["nom"])) ? strtolower(str_replace("'", "’", $_POST["nom"])) : '';
				$prenom = (!empty($_POST["prenom"])) ? strtolower(str_replace("'", "’", $_POST["prenom"])) : '';
				
				$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
				$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
				
				// TEST TEL
				if(empty($tel)){
					$err = "_telEmpty";
				}else{
					if(!preg_match('`[0-9]{10}`',$tel)){
						$err = "_telInvalid";
					}
					
				}
				
				// TEST EMAIL
				if(empty($email)){
					$err = "_emailEmpty";
				}else{
					$emailTest_rq="SELECT id FROM users WHERE email='{$email}' AND valid=1 AND id!={$userID}";
					$emailTest_rs = mysqli_query($connexion, $emailTest_rq) or die();
					if(mysqli_num_rows($emailTest_rs)){
						$err = "_emailExist";
					}	
					/*
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$err = "emailInvalid";
					}
					*/
				}
				
				// TEST NUMERO VOIE
				if(empty($voieNumero)){
					$err = "_numeroEmpty";
				}else{
					if(!is_numeric($voieNumero)){
						$err = "_numeroInvalid";
					}
					
				}
				
				// TEST VOIE
				if(empty($selectVoie)){
					$err = "_voieEmpty";
				}else{
					$voie = explode(' ', $selectVoie, 2);
					// TEST VOIE ID
					$voieTest_rq="SELECT id FROM voies WHERE voieType='{$voie[0]}' AND voieLibelle='{$voie[1]}'";
					$voieTest_rs = mysqli_query($connexion, $voieTest_rq) or die();
					if(!mysqli_num_rows($voieTest_rs)){
						$err = "_voieInvalid";
					}
				}
				
				if(empty($err)){
					
					$voieTest = mysqli_fetch_assoc($voieTest_rs);
					$voieID = $voieTest['id'];
					// INSERT ADRESSE
					$adresseCreate_rq="INSERT INTO adresses (voieNumero, voieID, cpl) VALUES ({$voieNumero}, {$voieID}, '{$cpl}')";
					$adresseCreate_rs = mysqli_query($connexion, $adresseCreate_rq) or die();
					$adresseID = mysqli_insert_id($connexion);
					// UPDATE USER
					$udateUser_rq="UPDATE users SET adresseID={$adresseID}, tel='{$tel}', email='{$email}', prenom='{$prenom}', nom='{$nom}' WHERE id={$userID}";
					$udateUser_rs = mysqli_query($connexion, $udateUser_rq) or die();

				}

			break;

			// UPDATE PWD
			case 'updatepwd':
			
				$pwd = (!empty($_POST["pwd"])) ? $_POST["pwd"] : ''; 
				$pwdPrev = (!empty($_POST["pwdPrev"])) ? $_POST["pwdPrev"] : '';
				
				if(!empty($pwd)&&!empty($pwdPrev)){
					$userSalt_rq = "SELECT salt FROM testUser WHERE userID='{$userID}'";
					$userSalt_rs = mysqli_query ($connexion, $userSalt_rq);
					$userSalt = mysqli_fetch_array($userSalt_rs);	
					$pwdenc = hash_hmac('sha256', $pwdPrev, $userSalt['salt']);
					// TEST PWD REQUEST
					$pwdValid_rq = "SELECT * FROM users WHERE pwdenc='{$pwdenc}' AND id='{$userID}'";
					$pwdValid_rs = mysqli_query ($connexion, $pwdValid_rq);
					if(mysqli_num_rows($pwdValid_rs)){			
						// SALT PWD
						$salt = uniqid(mt_rand());
						$pwdenc = hash_hmac('sha256', $pwd, $salt);			
						// UPDATE USER
						$pwdUpdate_rq = "UPDATE users SET pwdenc='{$pwdenc}' WHERE id={$userID}";
						$pwdUpdate_rs = mysqli_query($connexion, $pwdUpdate_rq) or die();	
						// UPDATE SALT
						$saltUpdate_rq = "UPDATE testUser SET salt='{$salt}' WHERE userID={$userID}";
						$saltUpdate_rs = mysqli_query($connexion, $saltUpdate_rq) or die();	

					}else{
						$action = "_errPwd";
					}
				}else{
					$action = "_errForm";	
				}
			break;	
		}
		
		// REDIRECTION
		$_SESSION['action'] = "{$action}Infos{$err}";
		header("location:../index.php");
		exit;		
	}
	
?>
