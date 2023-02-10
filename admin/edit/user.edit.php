<?php

	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$edit = (!empty($_REQUEST["edit"])) ? $_REQUEST["edit"] : '';
	$userID = (!empty($_REQUEST["userID"])) ? $_REQUEST["userID"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		

		echo userEdit($userID, $action, $edit);

		
	}
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		$societe = (!empty($_POST["societe"])) ? strtolower(str_replace("'", "’", $_POST["societe"])) : '';
		$nom = (!empty($_POST["nom"])) ? strtolower(str_replace("'", "’", $_POST["nom"])) : '';
		$prenom = (!empty($_POST["prenom"])) ? strtolower(str_replace("'", "’", $_POST["prenom"])) : '';		
		$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
		$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
		$prevEmail = (!empty($_POST["prevEmail"])) ? $_POST["prevEmail"] : '';
		
		$voieNumero = (!empty($_POST["voieNumero"])) ? $_POST["voieNumero"] : '';
		$voieSelect = (!empty($_POST["voieSelect"])) ? strtolower(str_replace("'", "’", $_POST["voieSelect"])) : '';
		$voieCpl = (!empty($_POST["voieCpl"])) ? strtolower(str_replace("'", "’", $_POST["voieCpl"])) : '';
		$prevSecteur = (!empty($_POST["prevSecteur"])) ? $_POST["prevSecteur"] : '';
		$adresseID = (!empty($_POST["adresseID"])) ? $_POST["adresseID"] : '';
			
		switch($action){
						
			case 'create':
			
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
					
					$voieTest = mysqli_fetch_assoc($voieTest_rs);
					$voieID = $voieTest['id'];
					
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
					
				}
			
			
			break;
			
			case 'update':
			
				if($edit=='adresse'){
					
					if(!empty($userID)&&!empty($voieNumero)&&!empty($voieSelect)){
					
						// ADRESSE EXISTS
						$voie = explode(' ', $voieSelect, 2);
						$voieTest_rq="SELECT id, secteur FROM voies WHERE voieType='{$voie[0]}' AND voieLibelle='{$voie[1]}'";
						$voieTest_rs = mysqli_query($connexion, $voieTest_rq) or die();
						if(!mysqli_num_rows($voieTest_rs)){
							$action = "errVoie";
						}else{
							$voieTest = mysqli_fetch_assoc($voieTest_rs);
							$voieID = $voieTest['id'];							
							if($voieTest['secteur']!=$prevSecteur){
								
								$adresseCreate_rq = "
								INSERT INTO adresses (voieNumero, voieID, cpl)
								VALUES ('{$voieNumero}','{$voieID}', '{$voieCpl}')";
								$adresseCreate_rs = mysqli_query($connexion, $adresseCreate_rq) or die();
								$adresseID = mysqli_insert_id($connexion);
								
								$userUpdate_rq="UPDATE users SET adresseID={$adresseID} WHERE id={$userID}";
								$userUpdate_rs = mysqli_query($connexion, $userUpdate_rq) or die();
								$action = "{$edit}Update";
								
							}else{
								
								$adresseUpdate_rq = "
								UPDATE adresses SET voieNumero='{$voieNumero}', voieID={$voieID}, cpl='{$voieCpl}'
								WHERE id = {$adresseID}";
								$adresseUpdate_rs = mysqli_query($connexion, $adresseUpdate_rq) or die();	
								
							}
						}
					
					}else{
						$action = "errForm";
					}
					
					
				}
				
				if($edit=='contact'){
					
					if(!empty($userID)&&!empty($nom)&&!empty($prenom)&&!empty($tel)&&!empty($email)){
						
						if($email!=$prevEmail){
							
							$emailExist_rq = "SELECT id FROM users WHERE email='{$email}'";
							$emailExist_rs = mysqli_query($connexion, $emailExist_rq) or die();	
							if(mysqli_num_rows($emailExist_rs)){	
								$action = "errMail";
							}
							
						}
						
						if($action != "errMail"){
							
							$updateUser_rq="UPDATE users SET nom='{$nom}', prenom='{$prenom}', societe='{$societe}', tel='{$tel}', email='{$email}' WHERE id={$userID}";
							$updateUser_rs = mysqli_query($connexion, $updateUser_rq) or die();
							$action = "{$edit}Update";
							
						}
						
					}else{
						$action = "errForm";
					}
					
				}

			break;


		}	
		// REDIRECTION
		if($action!='delete'){
			header("location:../users.php?userID={$userID}&action=user_{$action}");
		}else{
			header("location:../users.php?action=users_{$action}");
		}
		
		exit;		
	}
	
?>
