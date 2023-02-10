<?php

	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$resellerID = (!empty($_REQUEST["resellerID"])) ? $_REQUEST["resellerID"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		
		echo resellerEdit($resellerID, $action);

	}
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		$societe = (!empty($_POST["societe"])) ? strtolower(str_replace("'", "’", $_POST["societe"])) : '';
		$contact = (!empty($_POST["contact"])) ? strtolower(str_replace("'", "’", $_POST["contact"])) : '';		
		$tel = (!empty($_POST["tel"])) ? $_POST["tel"] : '';
		$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
		$adresse = (!empty($_POST["adresse"])) ? strtolower(str_replace("'", "’", $_POST["adresse"])) : '';

			
		switch($action){
						
			case 'create':
			
				$err = 0;
				
				if(empty($societe)){
					$err = 1;
					$action = "errForm";
				}
				
				if(!$err){
		
					$resellerCreate_rq = "
					INSERT INTO resellers(dateCreation, societe, contact, tel, email, adresse)
					VALUES (NOW(),'{$societe}','{$contact}','{$tel}','{$email}','{$adresse}')";
					$resellerCreate_rs = mysqli_query($connexion, $resellerCreate_rq) or die(mysqli_error($connexion));
					$resellerID = mysqli_insert_id($connexion);
					
				}
			
			break;
			
			case 'update':
				
				if(!empty($resellerID)&&!empty($societe)){
					
					$updateReseller_rq="UPDATE resellers SET societe='{$societe}', contact='{$contact}', adresse='{$adresse}', tel='{$tel}', email='{$email}' WHERE id={$resellerID}";
					$updateReseller_rs = mysqli_query($connexion, $updateReseller_rq) or die();
					$action = "{$edit}Update";

		
				}else{
					$action = "errForm";
				}

			break;


		}
		
		// REDIRECTION
		if($action!='errForm'){
			header("location:../resellers.php?resellerID={$resellerID}&action=reseller_{$action}");
		}else{
			header("location:../resellers.php?action=resellers_{$action}");
		}
		
		exit;		
	}
	
?>
