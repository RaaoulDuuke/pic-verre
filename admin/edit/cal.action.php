<?php
	require_once ("../../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	$calID = (!empty($_REQUEST["calID"])) ? $_REQUEST["calID"] : '';
	$calDate = (!empty($_REQUEST["calDate"])) ? $_REQUEST["calDate"] : '';
	$secteur = (!empty($_REQUEST["secteur"])) ? $_REQUEST["secteur"] : '';
	


	switch($action){
		case "delete":
		
			$cal_rq = "SELECT date FROM cal WHERE id='".$calID."'";
			$cal_rs = mysqli_query($connexion, $cal_rq) or die();
			$cal = mysqli_fetch_assoc($cal_rs);
			
			$year = date('Y', strtotime($cal['date']));
			$week = date('W', strtotime($cal['date']));
	
			// DELETE CAL
			$calDelete_rq = "DELETE FROM cal WHERE id={$calID}";	
			$calDelete_rq = mysqli_query($connexion, $calDelete_rq) or die();
			
			
		break;
		
		case "create":
		
			// CREATE CAL
			$calCreate_rq = "INSERT INTO cal (date, secteur) VALUES ('{$calDate}', {$secteur})";
			$calCreate_rs = mysqli_query($connexion, $calCreate_rq) or die(mysqli_error());
			
			$year = date('Y', strtotime($calDate));
			$week = date('W', strtotime($calDate));

		break;
		
		case "plan":
		
			$dateReturn = $calDate;
			
			$month = date("m",strtotime($calDate));
			$year = date("Y",strtotime($calDate));
		
			$running_day = date('w',mktime(0,0,0,$month,1,$year)-1);
			$days_in_month = date('t',mktime(0,0,0,$month,1,$year));

			$day_counter = 0;
			
			$secteurs_month = 0;
			$secteurs = 21;

			for($list_day = 1; $list_day <= $days_in_month; $list_day++){
			
				if($running_day < 5 && $secteurs_month<$secteurs){
								
					$secteurs_month ++;
					$date = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($day_counter+1));
					// CREATE CAL
					$calCreate_rq = "INSERT INTO cal (date, secteur) VALUES ('".$date."','".$secteurs_month."')";
					$calCreate_rs = mysqli_query($connexion, $calCreate_rq) or die();
					
				}

				if($running_day == 6){
					$running_day = -1;
				}

				$running_day++;
				$day_counter++;
				
			}
			
		break;
	}
		
	// REDIRECTION
	header("location:../picks.php?period=week&date={$year}&week={$week}&action=cal_{$action}");



?>