<?php

	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$resellerID = (!empty($_REQUEST["resellerID"])) ? $_REQUEST["resellerID"] : '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET'){		

		echo resellEdit($resellerID, $action);
		
	}
		
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		$sacs = (!empty($_POST["sacs"])) ? $_POST["sacs"] : '';
		$resellID = (!empty($_POST["resellID"])) ? $_POST["resellID"] : '';
			
		switch($action){
						
			case 'create':
			
				$err = 0;
				
				if(empty($sacs)||empty($resellerID)){
					$err = 1;
					$action = "errForm";
				}
				
				if(!$err){
					
					for ($i = 1; $i <= $sacs; ++$i) {
						
						$code =  random_str(6, '123456789abcdefghijklmnpqrstuvwxyz');
						
						$resellCreate_rq = "
						INSERT INTO resell(dateCreation, code, resellerID)
						VALUES (NOW(),'{$code}',{$resellerID})";
						$resellCreate_rs = mysqli_query($connexion, $resellCreate_rq) or die(mysqli_error($connexion));
						
					}
					
				}
			
			
			break;
			
			case 'update':
			
				if(empty($resellID)){
					
				}
			
			
			break;
			

		}	
		// REDIRECTION
		if($action!='delete'){
			header("location:../resellers.php?resellerID={$resellerID}&action=resell_{$action}");
		}else{
			header("location:../resellers.php?action=users_{$action}");
		}
		
		exit;		
	}
	
?>
