<?php
session_start();
require_once ("../includes/connect.php");

   $now = new \DateTime('now');
   $month = $now->format('m');
   $year = $now->format('Y');
   
   
   $monthInterval = 2;
   
   
   if(($month+$monthInterval)>12){
		$month = $month-(12-$monthInterval);
		$year = $year+1;
	}else{
		$month =$month+$monthInterval;
	}
   
   


	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year)-1);
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));

	$day_counter = 0;
	
	$secteurs_month = 0;
	$secteurs = 21;


	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	
		if($running_day < 5 && $secteurs_month<$secteurs):
			
			
			$secteurs_month ++;
			$date = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($day_counter+1));

			$calCreate_rq = "INSERT INTO cal (date, secteur) VALUES ('".$date."','".$secteurs_month."')";
			//echo $calCreate_rq."<br/>";
			$calCreate_rs = mysqli_query($connexion, $calCreate_rq) or die();
			
		endif;

	
		if($running_day == 6):
			$running_day = -1;

		endif;

		$running_day++; $day_counter++;
		
	endfor;


?>
