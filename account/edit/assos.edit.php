<?php

	require_once ("../../includes/account.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	
	if(empty($action)){
		
		echo assoEdit();
		
	}
	else{
		
		require_once ("../../includes/mails.functions.php");
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			
			$email = (!empty($_POST["email"])) ? $_POST["email"] : '';
			
			if(!empty($email)&&$action='create'){
					
				$partner_rq = "SELECT id FROM users WHERE email='{$email}'";
				$partner_rs =  mysqli_query($connexion, $partner_rq) or die(mysqli_error($connexion));
				$partner_nb = mysqli_num_rows($partner_rs);
				if($partner_nb){
					
					$partner = mysqli_fetch_assoc($partner_rs);
					
					$partnerExists_rq = "SELECT id FROM assos WHERE userID={$userID} AND partnerID={$partner['id']}";
					$partnerExists_rs = mysqli_query($connexion, $partnerExists_rq) or die(mysqli_error($connexion));
					$assoExists_rq = "SELECT id FROM assos WHERE userID={$partner['id']} AND partnerID={$userID}";
					$assoExists_rs = mysqli_query($connexion, $assoExists_rq) or die(mysqli_error($connexion));
					
					if(!mysqli_num_rows($partnerExists_rs) && !mysqli_num_rows($assoExists_rs)){		
						$assoCreate_rq = "INSERT INTO assos (dateCreation, userID, partnerID) VALUES (NOW(), {$userID},{$partner['id']})";
						$assoCreate_rs = mysqli_query($connexion, $assoCreate_rq) or die(mysqli_error($connexion));
						$assoID = mysqli_insert_id($connexion);
					}
					else{
						$action = "errDuplicate";
					}
				}
				else{
					
					$sponsoExists_rq = "SELECT id FROM sponsors WHERE email='{$email}'";
					$sponsoExists_rs = mysqli_query($connexion, $sponsoExists_rq) or die(mysqli_error($connexion));
					
					if(!mysqli_num_rows($sponsoExists_rs)){
						
						$action = "sponsor";

						$sponsorCreate_rq = "INSERT INTO sponsors(dateCreation, userID, email) VALUES (NOW(), {$userID},'{$email}')";
						$sponsorCreate_rs = mysqli_query($connexion, $sponsorCreate_rq) or die();
						$assoID = mysqli_insert_id($connexion);
						
						
						$mlistExists_rq = "SELECT id FROM msurvio WHERE email='{$email}'";
						$mlistExists_rq = mysqli_query($connexion, $mlistExists_rq) or die();
						if(!mysqli_num_rows($mlistExists_rq)){
							$mlistUpdate_rq = "INSERT INTO msurvio(email) VALUES ('{$email}')";
							$mlistUpdate_rs = mysqli_query($connexion, $mlistUpdate_rq) or die();
						}
						
					}
					else{
						$action = "sponsor_errDuplicate";
					}
				}
			}
			else{
				$action = "errForm";
			}

		}
		
		
		if ($_SERVER['REQUEST_METHOD'] == 'GET'){
			
			$assoID = (!empty($_GET["assoID"])) ? $_GET["assoID"] : '';
			$statut = (!empty($_GET["statut"])) ? $_GET["statut"] : '';
			
			switch($action){
			
				// ACCEPT ASSO
				case "accept":
					if(!empty($assoID)){
						$assoAccept_rq = "UPDATE assos SET dateValid=NOW() WHERE id={$assoID} AND partnerID={$userID}";
						$assoAccept_rs = mysqli_query($connexion, $assoAccept_rq) or die();
						if(!mysqli_affected_rows($connexion)){
							$action = "errForm";
						}
						else{
							$userEmail_rq = "
							SELECT users.email
							FROM assos 
							INNER JOIN users ON users.id=assos.userID
							WHERE assos.id={$assoID}";
							$userEmail_rs = mysqli_query($connexion, $userEmail_rq) or die(mysqli_error($connexion));
							$userEmail = mysqli_fetch_assoc($userEmail_rs);
							$email=$userEmail['email'];
						}
					}
					else{
						$action = "errForm";
					}
				break;		
				// REFUSED ASSO
				case "refused":
					if(!empty($assoID)){
						$assoRefused_rq = "DELETE FROM assos WHERE id={$assoID} AND partnerID={$userID}";
						$assoRefused_rs = mysqli_query($connexion, $assoRefused_rq) or die();
						if(!mysqli_affected_rows($connexion)){
							$action = "errForm";
						}
					}
					else{
						$action = "errForm";
					}
				break;	
				// CANCEL ASSO
				case "cancel":
					if(!empty($assoID)&&!empty($statut)){
						if($statut=="in"){
							$assoCancel_rq = "DELETE FROM assos WHERE id={$assoID} AND partnerID={$userID}";
						}
						if($statut=="out"){
							$assoCancel_rq = "DELETE FROM assos WHERE id={$assoID} AND userID={$userID}";
						}
						$assoCancel_rs = mysqli_query($connexion, $assoCancel_rq) or die();
						if(!mysqli_affected_rows($connexion)){
							$action = "errForm".mysqli_affected_rows($connexion);
						}
					}else{
						$action = "errForm";
					}
				break;
				// DEFAULT
				default:
					$action = "errAction";
				break;	
			
			}
		}
					

		// MAILING
		if($action=="create"||$action=="accept"||$action=="sponsor"){		
			assoMail($action, $assoID);	
		}
		
		// REDIRECTION
		header("location:../index.php?action=assos_".$action);
		exit;
		
	}
	
	
?>

