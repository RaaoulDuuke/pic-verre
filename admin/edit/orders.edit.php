<?php
	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$userID = (!empty($_REQUEST["userID"])) ? $_REQUEST["userID"] : '';
	$orderID = (!empty($_REQUEST["orderID"])) ? $_REQUEST["orderID"] : '';
	$orderPro = (!empty($_REQUEST["orderPro"])) ? $_REQUEST["orderPro"] : '0';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){

		if($action=='detail'){
			echo orderDetail($orderID);
		}else{
			echo orderEdit($action, $orderID, $userID, $orderPro);
		}
		
	}
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		$formuleID = (!empty($_REQUEST["formuleID"])) ? $_REQUEST["formuleID"] : '';
		$credits = (!empty($_REQUEST["credits"])) ? $_REQUEST["credits"] : '';
		$sacs = (!empty($_REQUEST["sacs"])) ? $_REQUEST["sacs"] : '';
		$reglement = (!empty($_REQUEST["reglement"])) ? $_REQUEST["reglement"] : '';
		$frequence = (!empty($_REQUEST["frequence"])) ? $_REQUEST["frequence"] : '';
		$adresse = (!empty($_REQUEST["adresse"])) ? $_REQUEST["adresse"] : '';
		$remise =  (!empty($_REQUEST["remise"])) ? $_REQUEST["remise"] : '0';
		$transID = (!empty($_REQUEST["transID"])) ? $_REQUEST["transID"] : '';
		
		if($action=="create"||$action=="update"){
							

			if($sacs){				
				$sacsMontant = $sacs*5;
				$orderMontant += $sacsMontant;			
			}
			
			if(!$orderPro){
					
				if($formuleID){
					$formule_rq = "SELECT * FROM formules WHERE id={$formuleID} ";
					$formule_rs = mysqli_query($connexion, $formule_rq) or die();
					$formule = mysqli_fetch_assoc($formule_rs);
					if($formule['credits']){
						$credits = $formule["credits"];
						$creditsMontant = $formule["montant"];
					}else{
						$creditsMontant = $formule["montant"]*$credits;
					}					
					$orderMontant += $creditsMontant;
				}
				
			}else{
				
				if($action=="create"){
					$credits = $sacs*$frequence;
				}			
				$formuleID = 0;		
				$creditsMontant = 2.5*$credits;				
				$orderMontant += $creditsMontant;
				
				if($remise){
					$orderMontant -= ($remise*1.2);
				}
				
			}
						
			if($action=="create"){
				
				$user_rq = "
				SELECT users.nom, users.prenom, users.societe FROM users 
				WHERE users.id={$userID}";
				$user_rs = mysqli_query($connexion, $user_rq) or die();
				$user = mysqli_fetch_assoc($user_rs);
				
				if($user['societe']){
					$orderNom = $user['societe'];
				}else{
					$orderNom = "{$user['nom']} {$user['prenom']}";
				}
				
				$orderCreate_rq = "
				INSERT INTO orders (dateCreation, userID, nom, facturation, montant, pro, reglement)
				VALUES (NOW(), {$userID}, '{$orderNom}', '{$adresse}', {$orderMontant}, {$orderPro}, '{$reglement}')";				
				$orderCreate_rs = mysqli_query($connexion, $orderCreate_rq) or die();
				$orderID = mysqli_insert_id($connexion);
			
			}else{
				
				$orderUpdate_rq = "UPDATE orders SET facturation='{$adresse}', montant={$orderMontant}, reglement='{$reglement}', remise={$remise} WHERE orders.id={$orderID}";				
				$orderUpdate_rs = mysqli_query($connexion, $orderUpdate_rq) or die();
				
				$sacDelete_rq = "DELETE FROM sacs WHERE orderID={$orderID}";	
				$sacDelete_rs = mysqli_query($connexion, $sacDelete_rq) or die();
				
				$creditDelete_rq = "DELETE FROM credits WHERE orderID={$orderID}";	
				$creditDelete_rs = mysqli_query($connexion, $creditDelete_rq) or die();	
				
			}
					
			if($credits){
				$creditCreate_rq = "
				INSERT INTO credits (formuleID, nb, montant, orderID)
				VALUES ({$formuleID}, {$credits}, {$creditsMontant}, {$orderID})";
				$creditCreate_rs = mysqli_query($connexion, $creditCreate_rq) or die();
			}
			
			if($sacs){
				$sacsCreate_rq = "
				INSERT INTO sacs (nb, montant, orderID)
				VALUES ({$sacs}, {$sacsMontant}, {$orderID})";
				$sacsCreate = mysqli_query($connexion, $sacsCreate_rq) or die();
			}
				
		}

		if($action=="delete"){
			
			$sacDelete_rq = "DELETE FROM sacs WHERE orderID={$orderID}";	
			$sacDelete_rs = mysqli_query($connexion, $sacDelete_rq) or die();
			
			$creditDelete_rq = "DELETE FROM credits WHERE orderID={$orderID}";	
			$creditDelete_rs = mysqli_query($connexion, $creditDelete_rq) or die();
			
			$orderDelete_rq = "DELETE FROM orders WHERE id={$orderID}";	
			$orderDelete_rs = mysqli_query($connexion, $orderDelete_rq) or die();
					
		}
		
		if($action=="valid" && $transID){
			
			require_once ("../../includes/mails.functions.php");
			
			orderMail($orderID);

			$transCreate_rq = "INSERT INTO transactions (dateCreation, ref) VALUES (NOW(), '{$transID}')";
			$transCreate_rs = mysqli_query($connexion, $transCreate_rq) or die();
			$tID = mysqli_insert_id($connexion);
			
			$orderUpdate_rq = "UPDATE orders SET tID={$tID} WHERE orders.id={$orderID}";
			$orderUpdate_rs = mysqli_query($connexion, $orderUpdate_rq) or die();
			
		}
		
		header("location:../users.php?userID={$userID}&action=order_{$action}");
		
	}
	
?>

