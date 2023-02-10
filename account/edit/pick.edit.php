<?php

	require_once ("../../includes/account.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : 'view';
	$pickType = (!empty($_REQUEST["pickType"])) ? $_REQUEST["pickType"] : 'pick';
	$pickID = (!empty($_REQUEST["pickID"])) ? $_REQUEST["pickID"] : '';
	$calID = (!empty($_REQUEST["calID"])) ? $_REQUEST["calID"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET' ){
		echo pickEdit($userID, $action, $pickType, $calID, $pickID);
	}	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
		require_once ("../../includes/mails.functions.php");
		
		$slotID = (!empty($_POST["slotID"])) ? $_POST["slotID"] : '1';
		if(userPro($userID)){
			$slotID = 10;
		}		
		$slotPrev = (!empty($_REQUEST["slotPrev"])) ? $_POST["slotPrev"] : '';
		$sacs = (!empty($_POST["sacs"])) ? $_POST["sacs"] : '0';
		$sacsPrev = (!empty($_POST["sacsPrev"])) ? $_POST["sacsPrev"] : '';
		$bundle = (!empty($_POST["bundle"])) ? $_POST["bundle"] : '0';
		$formuleID = (!empty($_POST["formuleID"])) ? $_POST["formuleID"] : 0;
		
		switch($action){
			
			/* CREATE PICK ACTION */
			case "create":
				
				if(!empty($calID)){
					
					if(!userActive($userID) || $formuleID){
						
						require_once ("../../includes/pay.functions.php");
						
						$credits = (!empty($_POST["credits"])) ? $_POST["credits"] : 0;
						$orderMontant = 0;
						$vadsProductNb = 0;
						
						// USER REQUEST
						$user_rq = "
						SELECT users.*, adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle 
						FROM users 
						INNER JOIN adresses ON adresses.id = users.adresseID
						INNER JOIN voies ON voies.id = adresses.voieID
						WHERE users.id={$userID}";
						$user_rs = mysqli_query($connexion, $user_rq) or die();
						$user = mysqli_fetch_assoc($user_rs);
						
						if($user['societe']){
							$orderNom = $user['societe'];
							$pro = 1;
						}else{
							$orderNom = "{$user['nom']} {$user['prenom']}";
							$pro = 0;
						}
						
						$orderAdresse = "{$user['voieNumero']} {$user['voieType']} {$user['voieLibelle']}, 33000 Bordeaux";
						
						// VADS USER
						$_REQUEST['vads_cust_id'] = $userID;
						$_REQUEST['vads_cust_last_name'] = $user['nom'];
						$_REQUEST['vads_cust_first_name'] = $user['prenom'];
						$_REQUEST['vads_cust_address_number'] = $user['voieNumero'];
						$_REQUEST['vads_cust_address'] = $user['voieType']." ".$user['voieLibelle'];
						$_REQUEST['vads_cust_address2'] = $user['cpl'];
						$_REQUEST['vads_cust_zip'] = "33000";
						$_REQUEST['vads_cust_city'] = "Bordeaux";
						$_REQUEST['vads_cust_phone'] = $user['tel'];
						$_REQUEST['vads_cust_email'] = $user['email'];
						
						// FORMULE CREDITS
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
						
						if(!userActive($userID)){
							$orderMontant += 5;
						}
						
						// INSERT ORDER
						$orderCreate_rq = "
						INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro)
						VALUES (NOW(), {$userID}, '{$orderNom}', '{$orderAdresse}', {$orderMontant}, {$pro})";
						$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
						$orderID = mysqli_insert_id($connexion);
						
						// VADS ORDER
						$_REQUEST['vads_order_id'] = $orderID ;
						
						if($formuleID){
							
							// INSERT CREDITS
							$creditCreate_rq = "
							INSERT INTO credits (formuleID, nb, montant, orderID)
							VALUES ({$formuleID}, {$credits}, {$montantCredits}, {$orderID})";
							$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
							
							// VADS CREDITS
							$_REQUEST["vads_product_label{$vadsProductNb}"] = "Crédits" ;
							$_REQUEST["vads_product_label{$vadsProductNb}"] = $montantCredits*100 ;
							$_REQUEST["vads_product_label{$vadsProductNb}"] = $credits ;
							
							$vadsProductNb += 1;

						}
						
						if(!userActive($userID)){
							
							// INSERT SAC
							$sacsCreate_rq = "INSERT INTO sacs (nb, montant, orderID) VALUES (1, 5, {$orderID})";
							$sacsCreate = mysqli_query($connexion, $sacsCreate_rq) or die();	
							
							// VADS SAC
							$_REQUEST["vads_product_label{$vadsProductNb}"] = "Sac" ;
							$_REQUEST["vads_product_label{$vadsProductNb}"] = 5*100;
							$_REQUEST["vads_product_label{$vadsProductNb}"] = 1 ;
							
							// INSERT CREDIT SAC
							$creditSacCreate_rq = "
							INSERT INTO credits (formuleID, nb, montant, orderID)
							VALUES (1, 1, 0, {$orderID})";
							$creditSacCreate_rs = mysqli_query($connexion, $creditSacCreate_rq) or die();

						}
						
						// INSERT PICK
						$pickCreate_rq = "
						INSERT INTO picks (calID, slotID, sacs, userID, adresseID, bundle, valid)
						VALUES ({$calID}, {$slotID}, {$sacs}, {$userID}, {$user['adresseID']}, {$bundle}, 0)";
						$pickCreate_rs = mysqli_query($connexion, $pickCreate_rq) or die(mysqli_error($connexion));
						$pickID =  mysqli_insert_id($connexion);
							
						// INSERT PICK ORDER
						$pickOrderCreate_rq = "
						INSERT INTO picksOrders (pickID, orderID) VALUES ({$pickID}, {$orderID})";
						$pickOrderCreate_rs = mysqli_query($connexion, $pickOrderCreate_rq) or die(mysqli_error($connexion));

						// VADS PRODUCTS NB
						$_REQUEST['vads_nb_products'] = $vadsProductNb+1 ;
						
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
						
						// TEST CREDITS
						$userCredits = userCredits($userID);
						if($sacs<=$userCredits){
							// PICK	TYPE
							if($pickType == "pick"){

								// USER ADRESSE
								$userAdresse_rq = "SELECT adresseID FROM users WHERE users.id={$userID}";
								$userAdresse_rs = mysqli_query($connexion, $userAdresse_rq) or die();
								$userAdresse = mysqli_fetch_array($userAdresse_rs);						
								// INSERT PICK
								$pickCreate_rq = "
								INSERT INTO picks (calID, slotID, sacs, userID, adresseID, bundle)
								VALUES ({$calID}, {$slotID}, {$sacs}, {$userID}, {$userAdresse['adresseID']}, {$bundle})";
								$pickCreate_rs = mysqli_query($connexion, $pickCreate_rq) or die(mysqli_error($connexion));
								$pickID =  mysqli_insert_id($connexion);
							}
							// BUNDLE TYPE
							if($pickType == "bundle"){	
								// INSERT BUNDLE
								$bundleCreate_rq = "
								INSERT INTO bundles (pickID, sacs, userID)
								VALUES ({$pickID}, {$sacs}, {$userID})";
								$bundleCreate_rs = mysqli_query($connexion, $bundleCreate_rq) or die();
								$pickID =  mysqli_insert_id($connexion);
							}
							// MAIL PICK
							pickMail($pickID, $pickType);
						}else{
							$err = "_errCredits";
						}
					}
					
				}else{
					$err ="_errForm";
				}
			break;		
			
			/* UPDATE PICK ACTION */
			case "update":
			
				if(!empty($pickID)){
					
					if($formuleID){
						
						require_once ("../../includes/pay.functions.php");
						
						$credits = (!empty($_POST["credits"])) ? $_POST["credits"] : 0;
						$orderMontant = 0;

						// USER REQUEST
						$user_rq = "
						SELECT users.*, adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle 
						FROM users 
						INNER JOIN adresses ON adresses.id = users.adresseID
						INNER JOIN voies ON voies.id = adresses.voieID
						WHERE users.id={$userID}";
						$user_rs = mysqli_query($connexion, $user_rq) or die();
						$user = mysqli_fetch_assoc($user_rs);
						
						if($user['societe']){
							$orderNom = $user['societe'];
							$pro = 1;
						}else{
							$orderNom = "{$user['nom']} {$user['prenom']}";
							$pro = 0;
						}
						
						// VADS USER
						$_REQUEST['vads_cust_id'] = $userID;
						$_REQUEST['vads_cust_last_name'] = $user['nom'];
						$_REQUEST['vads_cust_first_name'] = $user['prenom'];
						$_REQUEST['vads_cust_address_number'] = $user['voieNumero'];
						$_REQUEST['vads_cust_address'] = $user['voieType']." ".$user['voieLibelle'];
						$_REQUEST['vads_cust_address2'] = $user['cpl'];
						$_REQUEST['vads_cust_zip'] = "33000";
						$_REQUEST['vads_cust_city'] = "Bordeaux";
						$_REQUEST['vads_cust_phone'] = $user['tel'];
						$_REQUEST['vads_cust_email'] = $user['email'];
						
						// FORMULE CREDITS
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

						// INSERT ORDER
						$orderCreate_rq = "
						INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro)
						VALUES (NOW(), {$userID}, '{$orderNom}', '{$orderAdresse}', {$orderMontant}, {$pro})";
						$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
						$orderID = mysqli_insert_id($connexion);
						
						// VADS ORDER
						$_REQUEST['vads_order_id'] = $orderID ;
						
						// INSERT CREDITS
						$creditCreate_rq = "
						INSERT INTO credits (formuleID, nb, montant, orderID)
						VALUES ({$formuleID}, {$credits}, {$montantCredits}, {$orderID})";
						$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
						
						// VADS CREDITS
						$_REQUEST["vads_product_label{$vadsProductNb}"] = "Crédits" ;
						$_REQUEST["vads_product_label{$vadsProductNb}"] = $montantCredits*100 ;
						$_REQUEST["vads_product_label{$vadsProductNb}"] = $credits ;

						// INSERT PICK
						$pickCreate_rq = "
						INSERT INTO picks (calID, slotID, sacs, userID, adresseID, bundle, valid)
						VALUES ({$calID}, {$slotID}, {$sacs}, {$userID}, {$user['adresseID']}, {$bundle}, 0)";
						$pickCreate_rs = mysqli_query($connexion, $pickCreate_rq) or die(mysqli_error($connexion));
						$pickID =  mysqli_insert_id($connexion);
							
						// INSERT PICK ORDER
						$pickOrderCreate_rq = "
						INSERT INTO picksOrders (pickID, orderID) VALUES ({$pickID}, {$orderID})";
						$pickOrderCreate_rs = mysqli_query($connexion, $pickOrderCreate_rq) or die(mysqli_error($connexion));

						// VADS PRODUCTS NB
						$_REQUEST['vads_nb_products'] =1 ;
						
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
						
						// TEST CREDITS
						if($sacs!=$sacsPrev){							
							$userCredits = userCredits($userID);
							if($sacs>$userCredits+$sacsPrev){
								$err="_errCredits";
							}						
						}
						
						if($err!="errCredits"){
							// PICK TYPE
							if($pickType == "pick"){
								
								// UPDATE PICK
								$pickUpdate_rq = "
								UPDATE picks SET calID={$calID}, slotID={$slotID}, sacs={$sacs}, bundle={$bundle}
								WHERE id={$pickID} AND userID={$userID}";
								$pickUpdate_rs = mysqli_query($connexion, $pickUpdate_rq) or die(mysqli_error($connexion));
								if(!mysqli_affected_rows($connexion)){
									$err = "_errForm";
								}else{
									if(!$bundle){
										// MAIL BUNDLES
										pickBundlesMail("delete", $pickID);
										// DELETE BUNDLES
										$bundlesDelete_rq = "DELETE FROM bundles WHERE pickID={$pickID}";	
										$bundlesDelete_rs = mysqli_query($connexion, $bundlesDelete_rq) or die();
									}else{
										if($slotPrev!=$slotID){
											// MAIL BUNDLES
											pickBundlesMail("update", $pickID);
										}
									}
								}
							}
							// BUNDLE TYPE
							if($pickType == "bundle"){
								// UPDATE BUNDLE
								$bundleUpdate_rq = "
								UPDATE bundles SET sacs={$sacs} 
								WHERE id={$pickID} AND userID={$userID}";
								$bundleUpdate_rs = mysqli_query($connexion, $bundleUpdate_rq) or die();
								if(!mysqli_affected_rows($connexion)){
									$err = "_errForm";
								}else{	
									// MAIL BUNDLE
									bundleMail("update", $pickID);
								}
							}				
						}	
					}
					
			
				}else{
					$err="_errForm";
				}				
			break;		
			/* DELETE PICK ACTION */
			case "delete":
				if(!empty($pickID)){
					// PICK TYPE
					if($pickType == "pick"){
						
						$pick_rq = "SELECT cal.date FROM picks INNER JOIN cal ON cal.id=picks.calID WHERE picks.id={$pickID}";
						$pick_rs = mysqli_query($connexion, $pick_rq) or die();
						$pick = mysqli_fetch_assoc($pick_rs);
						
						if(date("Y-m-d")==$pick['date']){
						
							$hour = date('H:i');
							$missCreate_rq = "INSERT INTO miss (pickID, pickType, hour) VALUES ({$pickID}, '{$pickType}', '{$hour}')";
							$missCreate_rs = mysqli_query($connexion, $missCreate_rq) or die();
						
						}else{
						
							//MAIL BUNDLE
							pickBundlesMail("delete", $pickID);			
							// DELETE PICK
							$pickDelete_rq = "DELETE FROM picks WHERE id={$pickID} AND userID={$userID}";	
							$pickDelete_rs = mysqli_query($connexion, $pickDelete_rq) or die();
							if(!mysqli_affected_rows($connexion)){
								$err="_errForm";							
							}else{
								// DELETE BUNDLES
								$bundlesDelete_rq = "DELETE FROM bundles WHERE pickID={$pickID}";	
								$bundlesDelete_rs = mysqli_query($connexion, $bundlesDelete_rq) or die();
							}
						}
					}
					// BUNDLE TYPE
					if($pickType == "bundle"){	
						// MAIL BUNDLE
						bundleMail("delete", $pickID);
						// DELETE BUNDLE
						$pickDelete_rq = "DELETE FROM bundles WHERE id={$pickID} AND userID={$userID}";	
						$pickDelete_rs = mysqli_query($connexion, $pickDelete_rq) or die();
						if(!mysqli_affected_rows($connexion)){
							$action="_errForm";
						}						
					}
				}else{
					$err="_errForm";
				}					
			break;
			/* DEFAULT ACTION */
			default:
				$err="_errAction";
			break;
		}
		
		if(userActive($userID) && !$formuleID){
			// REDIRECTION
			$_SESSION['action'] = "{$action}Pick{$err}";
			header("location:../index.php");
			exit;
		}		
		
	}
	
?>