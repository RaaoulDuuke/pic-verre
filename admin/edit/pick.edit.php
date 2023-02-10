<?php

	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$userID = (!empty($_REQUEST["userID"])) ? $_REQUEST["userID"] : '';
	$pickID = (!empty($_REQUEST["pickID"])) ? $_REQUEST["pickID"] : '';
	$pickType = (!empty($_REQUEST["pickType"])) ? $_REQUEST["pickType"] : 'pick';
	$calID = (!empty($_REQUEST["calID"])) ? $_REQUEST["calID"] : '';
	$slotID = (!empty($_REQUEST["slotID"])) ? $_REQUEST["slotID"] : '10';
	
	$calDate = (!empty($_REQUEST["calDate"])) ? $_REQUEST["calDate"] : '';
	$secteur = (!empty($_REQUEST["secteur"])) ? $_REQUEST["secteur"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET' ){

		if($action=="loc"){
			echo picksLoc($calDate, $slotID, $secteur, $userID);
		}else{
			echo pickEdit($action, $pickID, $pickType, $calID, $userID);
		}
	}	
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
		require_once ("../../includes/mails.functions.php");
		
		$sacs = (!empty($_POST["sacs"])) ? $_POST["sacs"] : '';
		$sacsCmd = (!empty($_POST["sacsCmd"])) ? $_POST["sacsCmd"] : '';
		$sacsPrev = (!empty($_POST["sacsPrev"])) ? $_POST["sacsPrev"] : '';
		$slotPrev = (!empty($_REQUEST["slotPrev"])) ? $_POST["slotPrev"] : '';
		$bundle = (!empty($_POST["bundle"])) ? $_POST["bundle"] : '0';	
		$collectID = (!empty($_POST["collectID"])) ? $_POST["collectID"] : '0';
		
		switch($action){

			case "create":
				if(!empty($sacs)&&!empty($calID)&&!empty($userID)){
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
						//pickMail($pickID, $pickType);
						
					}else{
						$action="errCredits";
					}
				}else{
					$action="errForm";
				}
			break;		

			case "update":					
				if(!empty($pickID)&&!empty($pickType)&&!empty($userID)&&!empty($sacs)&&!empty($sacsPrev)){
					
					// TEST CREDITS
					if($sacs!=$sacsPrev){							
						$userCredits = userCredits($userID);
						if($sacs>$userCredits+$sacsPrev){
							$action="errCredits";
						}						
					}			
					if($action!="errCredits"){
						// PICK TYPE
						if($pickType == "pick"){
							// UPDATE PICK
							$pickUpdate_rq = "
							UPDATE picks SET calID={$calID}, slotID={$slotID}, sacs={$sacs}, bundle={$bundle} WHERE id={$pickID}";
							$pickUpdate_rs = mysqli_query($connexion, $pickUpdate_rq) or die();
							if(!mysqli_affected_rows($connexion)){
								$action = "errForm";
							}else{
								if(!$bundle){
									
									// MAIL BUNDLES
									//pickBundlesMail("delete", $pickID);
									
									// DELETE BUNDLES
									$bundlesDelete_rq = "DELETE FROM bundles WHERE pickID={$pickID}";	
									$bundlesDelete_rs = mysqli_query($connexion, $bundlesDelete_rq) or die();
									
								}else{
									if($slotPrev!=$slotID){
										
										// MAIL BUNDLES
										//pickBundlesMail("update", $pickID);
										
									}
								}
							}
							
						}
						// BUNDLE TYPE
						if($pickType == "bundle"){
							// UPDATE BUNDLE
							$bundleUpdate_rq = "
							UPDATE bundles SET sacs={$sacs} WHERE id={$pickID}";
							$bundleUpdate_rs = mysqli_query($connexion, $bundleUpdate_rq) or die();
							if(!mysqli_affected_rows($connexion)){
								$action = "errForm";
							}else{	
							
								// MAIL BUNDLE
								//bundleMail("update", $pickID);
								
							}

						}

					}				
				}else{
					$action="errForm";
				}				
			break;		

			case "delete":				
				if(!empty($pickID)&&!empty($pickType)){
					// PICK TYPE
					if($pickType == "pick"){
						
						//MAIL BUNDLE
						//pickBundlesMail("delete", $pickID);	
						
						// DELETE PICK
						$pickDelete_rq = "DELETE FROM picks WHERE id={$pickID}";	
						$pickDelete_rs = mysqli_query($connexion, $pickDelete_rq) or die();
						if(!mysqli_affected_rows($connexion)){
							$action="errForm";							
						}else{
							
							$missDelete_rq = "DELETE FROM miss WHERE miss.pickID={$pickID}";
							$missDelete_rs = mysqli_query($connexion, $missDelete_rq) or die();
							
							$collectDelete_rq = "DELETE FROM collects WHERE id={$collectID}";	
							$collectDelete_rs = mysqli_query($connexion, $collectDelete_rq) or die();
							
							$sacUpdate_rq = "UPDATE sacs SET collectID=0 WHERE collectID={$collectID}";
							$sacUpdate_rs = mysqli_query($connexion, $sacUpdate_rq) or die();
							
							// DELETE BUNDLES
							$bundlesDelete_rq = "DELETE FROM bundles WHERE pickID={$pickID}";	
							$bundlesDelete_rs = mysqli_query($connexion, $bundlesDelete_rq) or die();
						}
						
						
						
					}
					// BUNDLE TYPE
					if($pickType == "bundle"){	
					
						// MAIL BUNDLE
						//bundleMail("delete", $pickID);
						
						// DELETE BUNDLE
						$pickDelete_rq = "DELETE FROM bundles WHERE id={$pickID}";	
						$pickDelete_rs = mysqli_query($connexion, $pickDelete_rq) or die();
						if(!mysqli_affected_rows($connexion)){
							$action="errForm";
						}
					}
				}else{
					$action="errForm";
				}					
			break;

			case "miss":
			
				if(!empty($pickID)&&!empty($pickType)){
					$hour = date('H:i');
					$missCreate_rq = "INSERT INTO miss (pickID, pickType, hour) VALUES ({$pickID}, '{$pickType}', '{$hour}')";
					$missCreate_rs = mysqli_query($connexion, $missCreate_rq) or die();
				}else{
					$action="errForm";
				}	
				
			break;

			case "createCol":
			
				if(!empty($pickID)&&!empty($pickType)&&!empty($sacs)){
				
					$hour = date('H:i');
					
					
					$collectCreate_rq = "INSERT INTO collects (sacs, hour, pickerID) VALUES ({$sacs}, '{$hour}', 1)";
					$collectCreate_rs = mysqli_query($connexion, $collectCreate_rq) or die();
					$collectID = mysqli_insert_id($connexion);
					
					// UPDATE PICKS
					if($pickType=='pick'){
						$pickUpdate_rq = "UPDATE picks SET collectID={$collectID} WHERE picks.id={$pickID}";
						$pickUpdate_rs = mysqli_query($connexion, $pickUpdate_rq) or die();
					}
					
					// UPDATE BUNDLES
					if($pickType=='bundle'){
						$pickUpdate_rq = "UPDATE bundles SET collectID={$collectID} WHERE bundles.id={$pickID}";
						$pickUpdate_rs = mysqli_query($connexion, $pickUpdate_rq) or die();
					}
					
					// MISS DELETE
					$missDelete_rq = "DELETE FROM miss WHERE miss.pickID={$pickID}";
					$missDelete_rs = mysqli_query($connexion, $missDelete_rq) or die();
					
					// UPDATE SACS CMD		
					if(!empty($sacsCmd)&&$sacsCmd){
									
						$sacs_rq = "
						SELECT sacs.id FROM sacs 
						INNER JOIN orders ON orders.id = sacs.orderID
						WHERE orders.userID={$userID} AND orders.tID!=0 AND sacs.collectID=0";
						$sacs_rs = mysqli_query($connexion, $sacs_rq) or die();
						while($sacs = mysqli_fetch_array($sacs_rs)){
							$sacsUpdate_rq = "UPDATE sacs SET collectID={$collectID} WHERE id={$sacs['id']}";
							$sacsUpdate_rs = mysqli_query($connexion, $sacsUpdate_rq) or die();
						}
					}
					
					// collectMail($collectID, $pickType);
				
				}else{
					$action="errForm";
				}	
			break;
			
			case "updateCol":
			
				if(!empty($collectID)){
					
					if($sacs!=0){
						
						$collectUpdate_rq = "UPDATE collects SET sacs={$sacs} WHERE id={$collectID}";
						$collectUpdate_rs = mysqli_query($connexion, $collectUpdate_rq) or die();
						
						$missDelete_rq = "DELETE FROM miss WHERE miss.pickID={$pickID}";
						$missDelete_rs = mysqli_query($connexion, $missDelete_rq) or die();
						
						if(!empty($sacsCmd)&&$sacsCmd){
							
							$sacsCmd_rq = "
							SELECT sacs.id FROM sacs 
							INNER JOIN orders ON orders.id = sacs.orderID
							WHERE orders.userID={$userID} AND orders.tID!=0 AND sacs.collectID=0";
							$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die();
							while($sacsCmd = mysqli_fetch_array($sacsCmd_rs)){
								$sacsUpdate_rq = "UPDATE sacs SET collectID={$collectID} WHERE id={$sacsCmd['id']}";
								$sacsUpdate_rs = mysqli_query($connexion, $sacsUpdate_rq) or die();
							}

						}else{

							$sacsUpdate_rq = "UPDATE sacs SET collectID=0 WHERE collectID={$collectID}";
							$sacsUpdate_rs = mysqli_query($connexion, $sacsUpdate_rq) or die();
						}
					
					}else{
						
						$collectDelete_rq = "DELETE FROM collects WHERE id={$collectID}";	
						$collectDelete_rs = mysqli_query($connexion, $collectDelete_rq) or die();
						
						$picksUpdate_rq = "UPDATE picks SET collectID=0 WHERE collectID={$collectID}";
						$picksUpdate_rs = mysqli_query($connexion, $picksUpdate_rq) or die();
						
						$sacUpdate_rq = "UPDATE sacs SET collectID=0 WHERE collectID={$collectID}";
						$sacUpdate_rs = mysqli_query($connexion, $sacUpdate_rq) or die();
						
						$missCreate_rq = "INSERT INTO miss (pickID, pickType) VALUES ({$pickID}, '{$pickType}')";
						$missCreate_rs = mysqli_query($connexion, $missCreate_rq) or die();
						
					}
					
				}else{
					$action="errForm";
				}
				
			break;
			
			case "deleteCol":
				if(!empty($collectID)){
					
					$collectDelete_rq = "DELETE FROM collects WHERE id={$collectID}";	
					$collectDelete_rs = mysqli_query($connexion, $collectDelete_rq) or die();
					
					$picksUpdate_rq = "UPDATE picks SET collectID=0 WHERE collectID={$collectID}";
					$picksUpdate_rs = mysqli_query($connexion, $picksUpdate_rq) or die();
					
					$bundlesUpdate_rq = "UPDATE bundles SET collectID=0 WHERE collectID={$collectID}";
					$bundlesUpdate_rs = mysqli_query($connexion, $bundlesUpdate_rq) or die();
					
					$sacUpdate_rq = "UPDATE sacs SET collectID=0 WHERE collectID={$collectID}";
					$sacUpdate_rs = mysqli_query($connexion, $sacUpdate_rq) or die();
				
				}else{
					$action="errForm";
				}	
			break;
			
			default:
				$action="errAction";
			break;
		}
		
		$cal_rq = "SELECT cal.date FROM cal	WHERE cal.id={$calID}";
		$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error($connexion));
		$cal = mysqli_fetch_assoc($cal_rs);
		
		if($cal['date']==date("Y-m-d")){
			header("location:../picks.php?date={$cal['date']}&period=day&action=pick_{$action}");
			exit;	
		}else{
			header("location:../users.php?userID={$userID}&action=pick_{$action}");
			exit;	
		}

	}
	
?>