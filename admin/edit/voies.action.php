<?php
	session_start();
	require_once ("../includes/connect.php");
	require_once ("../includes/admin.functions.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : ''; 
	$voieID = (!empty($_REQUEST["voieID"])) ? $_REQUEST["voieID"] : '';
	$voieType = (!empty($_REQUEST["voieType"])) ? strtolower(str_replace("'", "’", $_REQUEST["voieType"])) : '';
	$voieLibelle = (!empty($_REQUEST["voieLibelle"])) ? strtolower(str_replace("'", "’", $_REQUEST["voieLibelle"])) : '';
	$secteur = (!empty($_REQUEST["secteur"])) ? $_REQUEST["secteur"] : '';

	if (!empty($action)){
	
		/* CREATE VOIE */
		if ($action == "create") {
			
			if(!empty($voieType)&&!empty($voieLibelle)&&!empty($secteur)){
				
				// INSERT VOIES
				$voieCreate_rq = "INSERT INTO voies (voieType,voieLibelle,secteur) VALUES ('{$voieType}','{$voieLibelle}','{$secteur}')";
				$voieCreate_rs = mysqli_query($connexion, $voieCreate_rq) or die(mysqli_error());

			}else{
				$action="errForm";
			}

		}
		
		/* UPDATE VOIE */
		if($action == "update") {
			
			if(!empty($voieType)&&!empty($voieLibelle)&&!empty($voieID)){

				// UPDATE VOIE
				$voieUpdate_rq = "UPDATE voies SET voieType='{$voieType}', voieLibelle='{$voieLibelle}', secteur='{$secteur}' WHERE id={$voieID}";
				$voieUpdate_rs = mysqli_query($connexion, $voieUpdate_rq) or die(mysqli_error());					

			}else{
				$action="errForm";
			}
			
		}

		/* DELETE VOIE */
		if ($action == "delete") {
			
			if(!empty($voieID)){
				
				$voieDelete_rq = "DELETE FROM voies WHERE id={$voieID}";	
				$voieDelete_rs = mysqli_query($connexion, $voieDelete_rq) or die(mysqli_error());
				
			}else{
				$action="errForm";
			}
		}
		
		// REDIRECTION
		header("location:voies.php?action=voie_".$action);
	
	} else {
		
		// REDIRECTION
		header("location:voies.php?&action=user_errAction");
		
	}


?>