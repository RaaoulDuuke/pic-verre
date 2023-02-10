<?php

	/* alertSection */
	function alertSection($action, $modal){
		
		if(!empty($action)){
		
			$alertClass="success";
			$alertContent="";
			
			switch($action){
				
				case "login": 
					$alertContent = "Bonjour, vous êtes bien connecté à votre compte";	
				break;
				case "logout": 
					$alertContent = "Vous avez bien été déconnecté(e) de votre compte";	
				break;

				// USER
				case "user_errMail": 
					$alertContent = "Un compte est déjà associé à cette adresse e-mail<br><a href='reset.php'>Réinitialiser votre mot de passe</a>";	
					$alertClass="danger";
				break;
				case "user_errVoie": 
					$alertContent = "Le nom de la voie n'est pas valide.<br/>Veuillez sélectionner votre voie dans le menu déroulant qui apparait lors de la saisie du nom de celle-ci";	
					$alertClass="danger";
				break;
				case "user_errForm": 
					$alertContent = "Veuillez remplir tous les champs obligatoires";
					$alertClass="danger";				
				break;
				case "user_setPwd": 
					$alertContent = "Bienvenue sur votre compte Pic'Verre !<br>Vous allez recevoir un e-mail permettant d'initialiser votre mot de passe";	
				break;
				
				// INFOS
				case "updateInfos":
					$alertContent = "Vos informations ont bien été modifiées";
				break;
				case "updateInfos_errForm": 
					$alertContent = "Erreur formulaire - Veuillez remplir tous les champs";	
					$alertClass="danger";
				break;
				case "updateInfos_errVoie": 
					$alertContent = "L'adresse saisie n'existe pas, veuillez sélectionner une adresse dans la liste proposée";
					$alertClass="danger";
				break;	
				case "updatepwdInfos":
					$alertContent = "Votre mot de passe a bien été modifié";
				break;
				case "updatepwdInfos_errPwd": 
					$alertContent = "Le mot de passe saisi n'est pas valide";
					$alertClass="danger";
				break;
				
				// PICK
				case "createPick":
					$alertContent = "Votre collecte a bien été programmée";
				break;
				case "updatePick":
					$alertContent = "Votre collecte a bien été modifiée";
				break;
				case "deletePick":
					$alertContent = "Votre collecte a bien été annulée";	
				break;
				case "createPick_errCredits":
				case "updatePick_errCredits": 				
					$alertContent = "Veuillez sélectionner un autre nombre de sacs";
					$alertClass="danger";
				break;
				case "createPick_errForm":
				case "updatePick_errForm": 
				case "deletePick_errForm":
					$alertContent = "Une erreur est survenue";
					$alertClass="danger";
				break;
				case "Pick_errAction":
					$alertContent = "Erreur";	
					$alertClass="danger";
				break;	

				// CREDIT PAY
				case "creditPay_AUTHORISED":
					$alertContent = "Votre compte a bien été crédité";
				break;
				case "creditPay_REFUSED":
				case "creditPay_ABANDONED":
					$alertContent = "Votre compte n'a pas été crédité";
					$alertClass="danger";
				break;
				
				// SAC PAY
				case "sacPay_AUTHORISED":
					$alertContent = "La commande de votre sac a bien été prise en compte,<br>il vous sera livré lors de votre prochaine collecte.";
				break;
				case "sacPay_REFUSED":
				case "sacPay_ABANDONED":
					$alertContent = "La commande de votre sac n'a malheureusement pas pu etre prise en compte.<br><button data-edit='sac' class='btn btn-primary mt-2 hvr-icon-push' data-toggle='modal' data-target='#editModal'><i class='fas fa-shopping-bag hvr-icon'></i> Réessayer</button>";
					$alertClass="danger";
				break;

				// ASSO
				case "assos_create":
					$alertContent = "Une demande d'ami a bien été envoyée, vous pourrez grouper vos collecte dés que la demande aura été acceptée";
				break;
				case "assos_sponsor":
					$alertContent = "Une demande de parrainage a bien été envoyée";	
				break;
				case "assos_accept":
					$alertContent = "La demande a bien été acceptée, vous pourrez maintenant grouper vos collectes";	
				break;
				case "assos_refused":
					$alertContent = "La demande a bien été refusée,  vous ne pourrez pas grouper vos collectes";	
				break;
				case "assos_cancel":
					$alertContent = "La demande a bien été annulée";	
				break;
				
				case "assos_errForm": 
					$alertContent = "Erreur formulaire - Veuillez remplir les champs obligatoire";	
					$alertClass="danger";
				break;
				case "assos_errAction": 
					$alertContent = "Erreur action - Paramètres manquants";	
					$alertClass="danger";
				break;
				case "assos_errDuplicate": 
					$alertContent = "Une demande d'ami a déjà été envoyée à cet abonné";
					$alertClass="danger";
				break;	
				case "assos_sponsor_errDuplicate": 
					$alertContent = "Une demande de parrainnage a déjà été envoyée à cet e-mail";	
					$alertClass="danger";
				break;
				case "askreset_errEmail": 
				case "login_errEmail": 
					$alertContent = "Aucun compte n'est associé à cette adresse e-mail";	
					$alertClass="danger";
				break;
				case "login_errMdp": 
					$alertContent = "Le mot de passe saisi est invalide";
					$alertClass="danger";
				break;
				case "askreset_valid": 
					$alertContent = "Un email permettant de réinitialiser votre mot de passe a bien été envoyé";	
				break;
				case "resetpwd_valid": 
					$alertContent = "Votre mot de passe a bien été réinitialisé";
				break;
				case "resetpwd_err": 
					$alertContent = "Votre mot de passe n'a pas pu être réinitialisé";
					$alertClass="danger";
				break;
			}
			
			if($modal){
				$closeBtn = "";
			}else{
				$closeBtn = "<button type='button' class='close' data-dismiss='alert' aria-label='Fermer'><span aria-hidden='true'>&times;</span></button>";
			}
			
			$alert = "
			<section class='row alert alert-{$alertClass} p-4 mb-0 align-items-center rounded-0'>
				<div class='col'>
					{$closeBtn}
					<p class='text-center m-0'><strong>".$alertContent."</strong></p>
				</div>
			</section>";
			
			return($alert);
		}
	}	
	
?>