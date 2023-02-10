<?php
	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$collectID = (!empty($_REQUEST["collectID"])) ? $_REQUEST["collectID"] : '';
	$pickID = (!empty($_REQUEST["pickID"])) ? $_REQUEST["pickID"] : '';
	$pickType = (!empty($_REQUEST["pickType"])) ? $_REQUEST["pickType"] : '';
	$sacs = (!empty($_REQUEST["sacs"])) ? $_REQUEST["sacs"] : '';
	$sacsSup = (!empty($_REQUEST["sacsSup"])) ? $_REQUEST["sacsSup"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){
		

		if($action=="detail"){
			echo collectDetail($pickID, $pickType);
		}else{
			echo collectEdit($action, $collectID, $pickID, $pickType);
		}
		
	}
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		require_once ("../../includes/mails.functions.php");
	
		switch($action){
			
			case "create":
			
				if(!empty($pickID)&&!empty($pickType)&&!empty($sacs)){
				
					$hour = date('H:i');
					
					// INSERT COLLECT
					$collectCreate_rq = "INSERT INTO collects (pickID, sacs, hour, pickerID) VALUES ({$pickID}, {$sacs}, '{$hour}', 1)";
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
					
					// UPDATE SACS CMD
					if(!empty($sacsSup)&&$sacsSup){
						
						if($pickType=="pick"){
							$user_rq = "SELECT userID AS id FROM picks WHERE id={$pickID}";
						}
						
						if($pickType=="bundle"){
							$user_rq = "SELECT userID AS id FROM bundles WHERE id={$pickID}";
						}
						
						$user_rs = mysqli_query($connexion, $user_rq) or die();
						$user = mysqli_fetch_array($user_rs);
											
						$sacsCmd_rq = "
						SELECT sacs.id FROM sacs 
						INNER JOIN orders ON orders.id = sacs.orderID
						WHERE orders.userID={$user['id']} AND orders.transID!=0 AND sacs.collectID=0";
						$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die();
						while($sacsCmd = mysqli_fetch_array($sacsCmd_rs)){
							$sacsUpdate_rq = "UPDATE sacs SET collectID={$collectID} WHERE id={$sacsCmd['id']}";
							$sacsUpdate_rs = mysqli_query($connexion, $sacsUpdate_rq) or die();
						}
					}
					
					collectMail($collectID, $pickType);
				
				}

			break;
			
			case "update":
				
				if(!empty($collectID)&&!empty($sacs)){
			
					$collectUpdate_rq = "UPDATE collects SET sacs={$sacs} WHERE id={$collectID}";
					$collectUpdate_rs = mysqli_query($connexion, $collectUpdate_rq) or die();
					
					if(!empty($sacsSup)&&$sacsSup){
						
						if($pickType=="pick"){
							$user_rq = "SELECT userID AS id FROM picks WHERE id={$pickID}";
							
						}
						
						if($pickType=="bundle"){
							$user_rq = "SELECT userID AS id FROM bundles WHERE id={$pickID}";
						}

						$user_rs = mysqli_query($connexion, $user_rq) or die();
						$user = mysqli_fetch_array($user_rs);
						
						$sacsCmd_rq = "
						SELECT sacs.id FROM sacs 
						INNER JOIN orders ON orders.id = sacs.orderID
						WHERE orders.userID={$user['id']} AND orders.transID!=0 AND sacs.collectID=0";
						$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die();
						while($sacsCmd = mysqli_fetch_array($sacsCmd_rs)){
							$sacsUpdate_rq = "UPDATE sacs SET collectID={$collectID} WHERE id={$sacsCmd['id']}";
							$sacsUpdate_rs = mysqli_query($connexion, $sacsUpdate_rq) or die();
						}

					}else{
						// UPDATE SAC
						$sacsUpdate_rq = "UPDATE sacs SET collectID=0 WHERE collectID={$collectID}";
						$sacsUpdate_rs = mysqli_query($connexion, $sacsUpdate_rq) or die();
					}
					
				}
				
			break;

			case "delete":
			
				if(!empty($collectID)){
					
					// DELETE COLLECT
					$collectDelete_rq = "DELETE FROM collects WHERE id={$collectID}";	
					$collectDelete_rs = mysqli_query($connexion, $collectDelete_rq) or die();
					
					// UPDATE PICKS
					$picksUpdate_rq = "UPDATE picks SET collectID=0 WHERE collectID={$collectID}";
					$picksUpdate_rs = mysqli_query($connexion, $picksUpdate_rq) or die();
					
					// UPDATE PICKS
					$bundlesUpdate_rq = "UPDATE bundles SET collectID=0 WHERE collectID={$collectID}";
					$bundlesUpdate_rs = mysqli_query($connexion, $bundlesUpdate_rq) or die();
					
					// UPDATE SACS
					$sacUpdate_rq = "UPDATE sacs SET collectID=0 WHERE collectID={$collectID}";
					$sacUpdate_rs = mysqli_query($connexion, $sacUpdate_rq) or die();
				
				}

			break;
			
			case "miss":
			
				$hour = date('H:i');
			
				// INSERT MISS
				$missCreate_rq = "INSERT INTO miss (pickID, hour) VALUES ({$pickID}, '{$hour}')";
				$missCreate_rs = mysqli_query($connexion, $missCreate_rq) or die();
			
			break;

		}
					
		if($pickType=="pick"){
			$pick_rq = "
			SELECT cal.date FROM picks 
			INNER JOIN cal ON cal.id = picks.calID 
			WHERE picks.id={$pickID}";
		}
		
		if($pickType=="bundle"){
			$pick_rq = "
			SELECT cal.date FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			INNER JOIN cal ON cal.id = picks.calID
			WHERE bundles.id={$pickID}";
		}
		
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick = mysqli_fetch_array($pick_rs);
		
		// REDIRECTION
		header("location:../cal.detail.php?date={$pick['date']}&action=collect_{$action}");	
	
	}
	
?>

