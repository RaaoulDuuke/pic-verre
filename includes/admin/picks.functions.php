<?php

/* picksPage */
function picksPage($date, $period, $week, $secteur, $userID){
	
	if($date==date("Y-m-d")&&$period=="day"){

		$mainContent = picksDayTable($date, $secteur);
		
		$page ="
		<div class='row'>
			<div class='col-sm-12'>
				{$mainContent}
			</div>
		</div>";
		
	}else{
		
		if($period=="week"){
			
			$date_string = $date . 'W' . sprintf('%02d', $week);
			$dateStart = date('Y-m-d', strtotime($date_string));
			$dateEnd = date('Y-m-d', strtotime($date_string . '5'));
			
		}
		
		if( ($period=="day" AND $date>=date("Y-m-d")) || ($period=="week" AND $dateEnd>=date("Y-m-d")) ){

			$pickOpenedSection = picksSection($date, $period, $week, "future");
				
		}
		
		if( ($period=="year" AND $date<=date("Y")) || ($period=="month" AND $date<=date("Y-m")) || ($period=="day" AND $date<date("Y-m-d")) || ($period=="week" AND $dateStart<=date("Y-m-d")) ){
			
			$pickClosedSection = picksSection($date, $period, $week, "closed");

		}
		

		$page ="
		{$pickOpenedSection}
		{$pickClosedSection}";
		
	}
	
	return $page;
	
}

/* picksSection */
function picksSection($date, $period, $week, $pickType, $userID){
	
	global $connexion;
	
	// REQUEST VARIABLES
	switch($period){
		case "year":
			$whereDate_rq = "YEAR(cal.date) = {$date}";
		break;			
		case "month":
			$month = date('m', strtotime($date));
			$year = date('Y', strtotime($date));
			$whereDate_rq = "MONTH(cal.date) = {$month} AND YEAR(cal.date) = {$year}";
		break;
		case "day":
			$whereDate_rq = "cal.date = '{$date}'";
		break;

		case "week":
			$date_string = $date . 'W' . sprintf('%02d', $week);
			$dateStart = date('Y-m-d', strtotime($date_string));
			$dateEnd = date('Y-m-d', strtotime($date_string . '7'));
			$whereDate_rq = "cal.date BETWEEN '{$dateStart}' AND '{$dateEnd}'";
		break;
		
		case "custom":
			$whereDate_rq = "cal.date BETWEEN '{$date[0]}' AND '{$date[1]}'";
		break;
		default:
			$period="all";
			$whereDate_rq = "";
		break;
	}
	
	$currentDate = date('Y-m-d');
	
	if($pickType=="closed"){
		$sectionTitle = "Collectes cloturées";
		$wherePicks_rq = "{$whereDate_rq} AND (picks.collectID!=0 OR cal.date <'{$currentDate}' OR EXISTS(SELECT * FROM miss WHERE miss.pickID = picks.id))";
		$whereBundles_rq = "{$whereDate_rq} AND (bundles.collectID!=0 OR cal.date < '{$currentDate}')";
	}
	
	if($pickType=="future"){
		$sectionTitle = "Collectes programmées";
		$wherePicks_rq = "{$whereDate_rq} AND cal.date>='{$currentDate}' AND NOT EXISTS(SELECT * FROM miss WHERE miss.pickID = picks.id) AND picks.collectID=0 ";
		$whereBundles_rq = "{$whereDate_rq} AND bundles.collectID=0 AND cal.date>='{$currentDate}'";

	}
	
	// PICKS REQUEST
	$picks_rq = "
	SELECT picks.id FROM picks	
	INNER JOIN cal ON picks.calID = cal.id
	LEFT JOIN collects ON collects.id = picks.collectID
	WHERE picks.valid=1 AND {$wherePicks_rq}
	UNION
	SELECT bundles.id FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN cal ON picks.calID = cal.id
	LEFT JOIN collects ON collects.id = bundles.collectID
	WHERE {$whereBundles_rq}";

	$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($picks_rs)){
		
		$picksNb = mysqli_num_rows($picks_rs);
		
		if($picksNb){
			
			$picksTable = picksTable($date, $period, $week, $pickType);
			
			if($pickType=="future"){
				
				if($period=="day"){
					$picksCal = picksDayStats($date);
				}
				
				if($period=="week"){
					$picksCal = picksWeekStats($date, $week);
					
					if(date('Y-m-d')>$dateEnd){
						$picksCal .= picksWeekPlan($date,$week);
					}

				}	
			}
			
			if($pickType=="closed"){
				
				if($period=="day"){
					$picksCal = picksDayStats($date);
				}else{		
					$picksCal = picksStats($date, $period, $week);
				}
				
			}
			
			$section = "
			<h3>{$sectionTitle} <span class='badge badge-warning'>{$picksNb}</span></h3>
			<div class='row'>
				<div class='col-lg-4'>
					{$picksCal}
				</div>
				<div class='col-lg-8'>
					{$picksTable}
				</div>
			</div>";			

		}
		
	}else{
		
		if($period=="week"){
			
			$picksCal = picksWeekPlan($date,$week);
			
			$section = "
			<div class='row'>
				<div class='col-lg-4'>
					{$picksCal}
				</div>
				<div class='col-lg-8'>
				</div>
			</div>";
			
		}
		
	}
	
	return $section;

	
}

/* picksTable */
function picksTable($date, $period, $week, $pickType, $userID){
	
	global $connexion;
	
	// REQUEST VARIABLES
	switch($period){
		case "year":
			$whereDate_rq = "YEAR(cal.date) = {$date}";
		break;			
		case "month":
			$month = date('m', strtotime($date));
			$year = date('Y', strtotime($date));
			$whereDate_rq = "MONTH(cal.date) = {$month} AND YEAR(cal.date) = {$year}";
		break;
		case "day":
			$whereDate_rq = "cal.date = '{$date}'";
		break;

		case "week":
			$date_string = $date . 'W' . sprintf('%02d', $week);
			$dateStart = date('Y-m-d', strtotime($date_string));
			$dateEnd = date('Y-m-d', strtotime($date_string . '7'));
			$whereDate_rq = "cal.date BETWEEN '{$dateStart}' AND '{$dateEnd}'";
		break;
		
		case "custom":
			$whereDate_rq = "cal.date BETWEEN '{$date[0]}' AND '{$date[1]}'";
		break;
		default:
			$period="all";
			$whereDate_rq = "";
		break;
	}
	
	$currentDate = date('Y-m-d');
	
	if($pickType=="closed"){
		$wherePicks_rq = "{$whereDate_rq} AND (picks.collectID!=0 OR cal.date<'{$currentDate}' OR EXISTS(SELECT * FROM miss WHERE miss.pickID = picks.id))";
		$whereBundles_rq = "{$whereDate_rq} AND (bundles.collectID!=0 OR cal.date<'{$currentDate}')";
	}
	
	if($pickType=="future"){
		$wherePicks_rq = "{$whereDate_rq} AND cal.date>='{$currentDate}' AND NOT EXISTS(SELECT * FROM miss WHERE miss.pickID = picks.id) AND picks.collectID=0 ";
		$whereBundles_rq = "{$whereDate_rq} AND bundles.collectID=0 AND cal.date>='{$currentDate}'";

	}
	
	if(!empty($userID)){
		$wherePicks_rq .= " AND picks.userID={$userID}";
		$whereBundles_rq .= " AND bundles.userID={$userID}";
		
		$userCol_class = "d-none";
		$thead_colspan = 4;
		$tfoot_colspan = 2;
		
		if($period="all"){
			$wherePicks_rq = substr($wherePicks_rq, 4); 
			$whereBundles_rq = substr($whereBundles_rq, 4); 
		}
		
		if($pickType=="closed"){			
			$tableTitle = "Collectes cloturées";
		}
		
		if($pickType=="future"){
			$tableTitle = "Collecte programmée";
		}
		
	}else{
		
		$tableTitle = "Détail";
		$badgeTitleClass = "d-none";
		
		$thead_colspan = 5;
		$tfoot_colspan = 3;

	}
	
	// PICKS REQUEST
	$picks_rq = "
	SELECT 'pick' AS type, picks.id, picks.sacs, picks.slotID, picks.userID AS userID, cal.date, slots.start, slots.end, voies.secteur, picks.collectID, collects.sacs AS sacsCol FROM picks	
	INNER JOIN cal ON picks.calID = cal.id
	INNER JOIN slots ON slots.id = picks.slotID
	INNER JOIN adresses ON adresses.id = picks.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	LEFT JOIN collects ON collects.id = picks.collectID
	WHERE picks.valid=1 AND {$wherePicks_rq}
	UNION
	SELECT 'bundle' AS type, bundles.id, bundles.sacs, picks.slotID, bundles.userID AS userID, cal.date, slots.start, slots.end, voies.secteur, bundles.collectID, collects.sacs AS sacsCol FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN cal ON picks.calID = cal.id
	INNER JOIN slots ON slots.id = picks.slotID
	INNER JOIN adresses ON adresses.id = picks.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	LEFT JOIN collects ON collects.id = bundles.collectID
	WHERE {$whereBundles_rq}
	ORDER BY date ASC, start ASC";

	$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($picks_rs)){
		
		$picksNb = mysqli_num_rows($picks_rs);
		
	
		while($picks = mysqli_fetch_assoc($picks_rs)){
			

			$dateCell = convertDate($picks['date']);
			$userCell = userLink($picks['userID']);
			$dateThCell = "Date";
		
			$refCell = pickRef($picks['id'], $picks['type']);
			
			$trClass = "table-default";
			$sColCell = "-";
			$sOrdCell = "-";
			
			if($picks['collectID']){
				
				$trClass = "table-success";
				$sColCell = $picks['sacsCol'];
				$sColTotal += $picks['sacsCol'];
				
				$sCmd_rq = "
				SELECT SUM(sacs.nb) AS nb FROM sacs 
				INNER JOIN orders ON orders.id = sacs.orderID
				WHERE orders.userID={$picks['userID']} AND orders.tID!=0 AND sacs.collectID={$picks['collectID']}";
				$sCmd_rs = mysqli_query($connexion, $sCmd_rq) or die(mysqli_error($connexion));
				if(mysqli_num_rows($sCmd_rs)){
					$sCmd = mysqli_fetch_assoc($sCmd_rs);
					if($sCmd['nb']){	
						$sOrdCell = $sCmd['nb'];
						$sOrdTotal += $sCmd['nb'];
					}else{
						$sOrdCell = "-";
					}
				}
		
			}else{

				if($picks['date']<date('Y-m-d')){
					
					$trClass = "table-warning";
					$sColCell = "-";
					$sOrdCell = "-";
					
					$miss_rq = "
					SELECT * FROM miss WHERE pickID={$picks['id']} AND pickType='{$picks['type']}'";
					$miss_rs = mysqli_query($connexion, $miss_rq) or die(mysqli_error($connexion));
					if(mysqli_num_rows($miss_rs)){
						$trClass = "table-danger";
					}
					
					
				}else{
										
					if($picks['sacs']){
						$sColCell = $picks['sacs'];
						$sColTotal += $picks['sacs'];
					}else{
						$userSacs = userSacs($picks['userID'])+1;
						$sColTotal += $userSacs;
						$sColCell = $userSacs."<small>+</small>";
					}
					
					
					$sCmd_rq = "
					SELECT SUM(sacs.nb) AS nb FROM sacs 
					INNER JOIN orders ON orders.id = sacs.orderID
					WHERE orders.userID={$picks['userID']} AND orders.tID!=0 AND sacs.collectID=0";
					$sCmd_rs = mysqli_query($connexion, $sCmd_rq) or die(mysqli_error($connexion));
					if(mysqli_num_rows($sCmd_rs)){
						$sCmd = mysqli_fetch_assoc($sCmd_rs);
						if($sCmd['nb']){	
							$sOrdCell = $sCmd['nb'];
							$sOrdTotal += $sCmd['nb'];
						}else{
							$sOrdCell = "-";
						}
					}
					
					if($picks['date']==date('Y-m-d')){
						
						$trClass = "table-warning";
						
						$miss_rq = "
						SELECT * FROM miss WHERE pickID={$picks['id']} AND pickType='{$picks['type']}'";
						$miss_rs = mysqli_query($connexion, $miss_rq) or die(mysqli_error($connexion));
						if(mysqli_num_rows($miss_rs)){
							$trClass = "table-danger";
							$sColCell = "-";
							$sOrdCell = "-";
						}
						
					}
				}
			}
			
			$dataRq = "action=view&pickID={$picks['id']}&pickType={$picks['type']}";
			$dataEdit = "pick";
			
			
			if($period=="week"&&$pickType=="future"){
				
				$dateTh = convertDate($picks['date'],"2adb");
				$dateCell = $picks['start']."/".$picks['end'];
				$dateThCell = "Horaire";
				
				$tfoot_class = "d-none";

				if($dateTmp!=$picks['date']){
					
					$dateTmp = $picks['date'];
					
					$sColDay = 0;
					
					// PICKS REQUEST
					$picksDayUsers_rq = "
					SELECT picks.sacs, picks.userID FROM picks	
					INNER JOIN cal ON picks.calID = cal.id
					LEFT JOIN collects ON collects.id = picks.collectID
					WHERE picks.valid=1 AND cal.date = '{$picks['date']}' AND NOT EXISTS(SELECT * FROM miss WHERE miss.pickID = picks.id) AND picks.collectID=0";
					$picksDayUsers_rs = mysqli_query($connexion, $picksDayUsers_rq) or die(mysqli_error($connexion));
					while($picksDayUsers = mysqli_fetch_assoc($picksDayUsers_rs)){
						
						$sCmdDay_rq = "
						SELECT SUM(sacs.nb) AS nb FROM sacs 
						INNER JOIN orders ON orders.id = sacs.orderID
						WHERE orders.userID={$picksDayUsers['userID']} AND orders.tID!=0 AND sacs.collectID=0";
						$sCmdDay_rs = mysqli_query($connexion, $sCmdDay_rq) or die(mysqli_error($connexion));
						if(mysqli_num_rows($sCmdDay_rs)){
							$sCmdDay = mysqli_fetch_assoc($sCmdDay_rs);
							if($sCmdDay['nb']){	
								$sOrdDayCell = $sCmdDay['nb'];
							}else{
								$sOrdDayCell = "-";
							}
						}
						
						if($picksDayUsers['sacs']==0){
							$sColDay += (userSacs($picksDayUsers['userID'])+1);
						}else{
							$sColDay += $picksDayUsers['sacs'];
						}
						
					}
					
					$sColDayCell = $sColDay;
					
					$tbody .="
					<tr>
						<td colspan='3' class='align-middle bg-dark text-left'>{$dateTh}</td>
						<td class='align-middle bg-warning text-center font-weight-bolder'>{$sColDayCell}</td>
						<td class='align-middle bg-warning text-center font-weight-bolder'>{$sOrdDayCell}</td>
					</tr>";
					
				}			
			}

			if($period=="day"&&$pickType=="future"){

				
				$dateThCell = "Sct.";
				$dateTh = $picks['start']."/".$picks['end'];
				$dateCell = "Sct. ".$picks['secteur'];

				$tfoot_class = "d-none";


				if($dateTmp!=$picks['start']){
					
					$dateTmp = $picks['start'];
					
					$sColDay = 0;
					
					// PICKS REQUEST
					$picksDayUsers_rq = "
					SELECT picks.sacs, picks.userID FROM picks	
					INNER JOIN cal ON picks.calID = cal.id
					LEFT JOIN collects ON collects.id = picks.collectID
					WHERE picks.valid=1 AND cal.date = '{$picks['date']}' AND picks.slotID={$picks['slotID']} AND NOT EXISTS(SELECT * FROM miss WHERE miss.pickID = picks.id) AND picks.collectID=0";
					$picksDayUsers_rs = mysqli_query($connexion, $picksDayUsers_rq) or die(mysqli_error($connexion));
					while($picksDayUsers = mysqli_fetch_assoc($picksDayUsers_rs)){
						
						$sCmdDay_rq = "
						SELECT SUM(sacs.nb) AS nb FROM sacs 
						INNER JOIN orders ON orders.id = sacs.orderID
						WHERE orders.userID={$picksDayUsers['userID']} AND orders.tID!=0 AND sacs.collectID=0";
						$sCmdDay_rs = mysqli_query($connexion, $sCmdDay_rq) or die(mysqli_error($connexion));
						if(mysqli_num_rows($sCmdDay_rs)){
							$sCmdDay = mysqli_fetch_assoc($sCmdDay_rs);
							if($sCmdDay['nb']){	
								$sOrdDayCell = $sCmdDay['nb'];
							}else{
								$sOrdDayCell = "-";
							}
						}
						
						if($picksDayUsers['sacs']==0){
							$sColDay += (userSacs($picksDayUsers['userID'])+1);
						}else{
							$sColDay += $picksDayUsers['sacs'];
						}
						
					}
					
					$sColDayCell = $sColDay;
					
					$tbody .="
					<tr>
						<td colspan='3' class='align-middle bg-dark text-left'>{$dateTh}</td>
						<td class='align-middle bg-warning text-center font-weight-bolder'>{$sColDayCell}</td>
						<td class='align-middle bg-warning text-center font-weight-bolder'>{$sOrdDayCell}</td>
					</tr>";
					
				}				
			}

			$tbody .="
			<tr class={$trClass}>
				<td class='align-middle' style='font-weight:500;'>{$dateCell}</td>
				<td class='align-middle text-center'><button data-edit='{$dataEdit}' data-rq='{$dataRq}' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0'>{$refCell}</button></td>
				<td class='align-middle ellipsis {$userCol_class}'>{$userCell}</td>
				<td class='align-middle text-center table-light font-weight-bold'>{$sColCell}</td>
				<td class='align-middle text-center table-light font-weight-bold'>{$sOrdCell}</td>
			</tr>";
			
		}	
		
		$sColTotalCell = "-";
		if($sColTotal){
			$sColTotalCell = $sColTotal;
		}
		$sOrdTotalCell = "-";
		if($sOrdTotal){
			$sOrdTotalCell = $sOrdTotal;
		}
	
		$table ="
		<div class='mb-4 table-responsive-md'>
			<table class='table mb-2 table-hover font-weight-bold' id='{$pickType}PicksTable'>
			<thead>
				<tr class='{$badgeTitleClass}'>
					<th colspan='{$thead_colspan}' class='bg-dark rounded-top  p-3'>{$tableTitle} <span class='badge badge-warning '>{$picksNb}</span></th>
				</tr>
				<tr>
					<th width='90'>{$dateThCell}</th>
					<th width='90' class='text-center'>Ref.</th>
					<th class='{$userCol_class}'>Abonné</th>
					<th width='60' class='text-center'>Col</th>
					<th width='60' class='text-center'>Cmd</th>
				</tr>
			</thead>
			<tbody>
				{$tbody}
			</tbody>
			<tfoot class='{$tfoot_class}'>
				<tr>
					<th class='text-right bg-dark' colspan='{$tfoot_colspan}'>Total</th>
					<td class='text-center font-weight-bolder bg-warning' style='font-size:110%'>{$sColTotalCell}</td>
					<td class='text-center font-weight-bolder bg-warning' style='font-size:110%'>{$sOrdTotalCell}</td>
				</tr>
			</tfoot>
			</table>
		</div>";

		return $table;
	}
}

/* picksDayTable */
function picksDayTable($date, $secteur){
	
	global $connexion;
	
	$calDay_rq = "SELECT * FROM cal WHERE date='{$date}'";
	$calDay_rs = mysqli_query($connexion, $calDay_rq) or die();
	$calDay_nb = mysqli_num_rows($calDay_rs);
	if($calDay_nb){
		
		$pick_rq = "
		SELECT picks.id FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE picks.valid=1 AND cal.date = '{$date}'";
		$pick_rs=mysqli_query($connexion, $pick_rq) or die();		
		$pick_nb = mysqli_num_rows($pick_rs);

		if(mysqli_num_rows($pick_rs)){

			$slots_rq = "SELECT * FROM slots ORDER BY start ASC";
			$slots_rs=mysqli_query($connexion, $slots_rq) or die();
			while($slots=mysqli_fetch_assoc($slots_rs)){
				
				$picks_rq = "
				SELECT id, sacs, userID, collectID, calID, adresseID, tel, secteur, sacsCol, type FROM
				(
					SELECT picks.id, picks.sacs, picks.userID, picks.collectID, picks.calID, picks.adresseID, users.tel, cal.secteur,  collects.sacs AS sacsCol, 'pick' AS type FROM picks
					INNER JOIN users ON users.id = picks.userID
					INNER JOIN cal ON cal.id = picks.calID
					LEFT JOIN collects ON collects.id = picks.collectID
					WHERE picks.valid=1 AND cal.date = '{$date}' AND picks.slotID = {$slots['id']}
					
					UNION
					
					SELECT bundles.id, bundles.sacs, bundles.userID, bundles.collectID, picks.calID, picks.adresseID, users.tel, cal.secteur, collects.sacs AS sacsCol, 'bundle' AS type FROM bundles
					INNER JOIN users ON users.id = bundles.userID
					INNER JOIN picks ON picks.id = bundles.pickID
					INNER JOIN cal ON cal.id = picks.calID
					LEFT JOIN collects ON collects.id = bundles.collectID
					WHERE cal.date = '{$date}' AND picks.slotID = {$slots['id']}
				) AS pb
				
				ORDER BY secteur ASC";
				
				$picks_rs=mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));		
				$picks_nb = mysqli_num_rows($picks_rs);
				if($picks_nb){
					
					$tbody = "";
					
					$sCmdTtCell = "";
					$sPckTtCell = "";
					
					$sPrgTt = 0;
					$sColTt = 0;
					$sOrdTt = 0;
					$sDelTt = 0;

					while($picks=mysqli_fetch_assoc($picks_rs)){
						
						$trClass = "";
						
						$pickUser = userName($picks['userID']);
						$usrCell = "<button class='btn btn-link text-uppercase' data-edit='user' data-rq='action=view&userID={$picks['userID']}' data-toggle='modal' data-target='#editModal' >{$pickUser}</button>";
						$editCell = pickBtn($picks['id'], $picks['type'], "dropright float-left mr-2");
						$refCell = pickRef($picks['id'], $picks['type']);;
						
						if($picks['type']=='pick'){
							
							$sctCell = userSecteur($picks['userID']);
							$pickAdresse = pickAdresse($picks['adresseID']);
							$adrCell = "<button data-edit='pick' data-rq='action=loc&calDate={$date}&slotID={$slots['id']}&userID={$picks['userID']}' data-toggle='modal' data-target='#editModal' class='btn btn-link text-uppercase'>{$pickAdresse}</button>";

						}else{
							$adrCell = "";
							$sctCell = "";
						}
						
						if(!empty($picks['collectID'])){
							
							$trClass = "table-success";
							$sColTt += $picks['sacsCol'];
							$sPckCell = "{$picks['sacsCol']}";
							
							$sCmd_rq = "
							SELECT SUM(sacs.nb) AS nb FROM sacs 
							INNER JOIN orders ON orders.id = sacs.orderID
							WHERE orders.userID={$picks['userID']} AND orders.tID!=0 AND sacs.collectID={$picks['collectID']}";
							$sCmd_rs = mysqli_query($connexion, $sCmd_rq) or die(mysqli_error($connexion));
							$sCmd = mysqli_fetch_assoc($sCmd_rs);
							if($sCmd['nb']){

								$sCmdCell = $sCmd['nb'];
								$sDelTt += $sCmd['nb'];

							}else{
								$sCmdCell = "-";
							}
							
						}else{
							
							if($picks['sacs']){
								$sPckCell = $picks['sacs'];
								$sPrgTt += $picks['sacs'];
							}else{
								$userSacs = userSacs($picks['userID'])+1;
								$sPrgTt +=  userSacs($picks['userID']);
								$sPckCell = $userSacs."<small>+</small>";
							}
	
							if($date>=date("Y-m-d")){
							
								$sCmd_rq = "
								SELECT SUM(sacs.nb) AS nb FROM sacs 
								INNER JOIN orders ON orders.id = sacs.orderID
								WHERE orders.userID={$picks['userID']} AND orders.tID!=0 AND sacs.collectID=0";
								$sCmd_rs = mysqli_query($connexion, $sCmd_rq) or die(mysqli_error($connexion));
								$sCmd = mysqli_fetch_assoc($sCmd_rs);
								if($sCmd['nb']){

									$sCmdCell = $sCmd['nb'];
									$sOrdTt += $sCmd['nb'];

								}else{
									$sCmdCell = "-";
								}
								
								$pMiss_rq = "
								SELECT COUNT(miss.id) AS nb FROM miss 
								WHERE miss.pickID={$picks['id']} AND miss.pickType='{$picks['type']}'";
								$pMiss_rs = mysqli_query($connexion, $pMiss_rq) or die(mysqli_error($connexion));
								$pMiss = mysqli_fetch_assoc($pMiss_rs);
								
								if($pMiss['nb'] || $date." ".$slots['end']<date("Y-m-d h:i")){
									$trClass = "table-warning";
								}
							
							}else{
								
								$pMiss_rq = "
								SELECT COUNT(miss.id) AS nb FROM miss 
								WHERE miss.pickID={$picks['id']} AND miss.pickType='{$picks['type']}'";
								$pMiss_rs = mysqli_query($connexion, $pMiss_rq) or die(mysqli_error($connexion));
								$pMiss = mysqli_fetch_assoc($pMiss_rs);
								
								if($pMiss['nb']){
									$trClass = "table-warning";
								}else{
									$trClass = "table-danger";
								}
								
								$sCmdCell = "-";
								$sPckCell = "-";

							}
						}
						
						if($sctTmp!=$picks['secteur'] && $slots['start']!="10:00"){
					
							$sctTmp = $picks['secteur'];
							
							$tbody .= " 
							<thead>
								<tr>
									<th style='min-width:120px;' colspan='3' scope='col'>Secteur {$picks['secteur']}</th>
									<th style='min-width:50px;' scope='col' class='text-center'>Col</th>
									<th style='min-width:50px;' scope='col' class='text-center'>Cmd</th>
								</tr>
							</thead>";
							
						}

						$tbody .= "
						<tr class='{$trClass}'>
							<td style='min-width:90px;'><button data-edit='pick' data-rq='action=view&pickID={$picks['id']}&pickType={$picks['type']}' data-toggle='modal' data-target='#editModal' class='btn btn-link'>{$refCell}</button></td>
							<td style='min-width:200px'>{$adrCell}</td>
							<td style='min-width:200px'>{$usrCell}</td>
							<td class='text-center table-light font-weight-bold' style='min-width:60px;'>{$sPckCell}</td>
							<td class='text-center table-light font-weight-bold' style='min-width:60px;'>{$sCmdCell}</td>
						</tr>";

					}
					
					$content .= "
					<div class='border-0 mb-1' style='background:none;'>
					
						<div class='bg-dark p-3'>
						
							<strong>{$slots['start']}/{$slots['end']}</strong> <span class='badge badge-warning'>{$picks_nb}</span>
							
							<button data-edit='pick' data-rq='action=loc&calDate={$date}&slotID={$slots['id']}&secteur={$secteur}' data-toggle='modal' data-target='#editModal' class='btn btn-sm btn-secondary float-right' style='margin-top:-6px;'><i class='fas fa-map-marker-alt text-warning'></i> Carte</button>
							
						</div>
						
						<section id='slot{$slots['id']}'>
						
							<div class='table-responsive-sm font-weight-bold'>
								<table class='table m-0'>
									{$thead}
									<tbody>
										{$tbody}
									</tbody>
								</table>
							</div>
						</section>
					</div>";
				}
			}
			
			return $content;
			
		}else{
			
			return "Aucune collecte";
			
		}
	}
}	

/* picksStats */
function picksStats($date, $period, $week){
	
	global $connexion;
	
	// REQUEST VARIABLES
	switch($period){
		case "year":
			$year = $date;
			$where_pPeriod = "YEAR(cal.date)='{$year}'";
			$period_end = 12;
		break;			
		case "month":
			$month = date('m', strtotime($date));
			$year = date('Y', strtotime($date));			
			$where_pPeriod = "MONTH(cal.date)='{$month}' AND YEAR(cal.date)='{$year}'";
			$period_end = date('t',mktime(0,0,0,$month,1,$year));
		break;		
		case "week":
			$date_string = $date . 'W' . sprintf('%02d', $week);
			$dateStart = date('Y-m-d', strtotime($date_string));
			$dateEnd = date('Y-m-d', strtotime($date_string . '5'));
			$where_pPeriod = "cal.date BETWEEN '{$dateStart}' AND '{$dateEnd}'";
			$period_end = 5;
		break;
	}
	
	$pPeriod_rq = "
	SELECT cal.id FROM cal 
	INNER JOIN picks ON picks.calID = cal.id
	WHERE picks.valid=1 AND {$where_pPeriod} AND picks.collectID!=0";
	$pPeriod_rs = mysqli_query($connexion, $pPeriod_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($pPeriod_rs)){

		for($period_cpt = 1; $period_cpt <= $period_end; $period_cpt++){
			
			$pMssTt = 0;
			
			switch($period){
				case "year":
					$calPeriod = "month";
					$calMonth = sprintf("%02d", ($period_cpt));
					$calDate = $date."-".$calMonth;
					$calCell = convertDate($calDate, '2BY');
					$where_rq = "WHERE MONTH(cal.date)='{$calMonth}' AND YEAR(cal.date)='{$year}'";
				break;			
				case "month":
					$calPeriod = "day";
					$calDate = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($period_cpt));
					$calCell = convertDate($calDate, '2adb');
					$where_rq = "WHERE cal.date='{$calDate}'";						
				break;
				case "week":
					$calPeriod = "day";
					$calDate = date('Y-m-d', strtotime($date_string . $period_cpt));
					$calCell = convertDate($calDate, '2adb');
					$where_rq = "WHERE cal.date='{$calDate}'";						
				break;
			}
			
			
			$currentDate = date("Y-m-d");
			$picks_rq = "
				
				SELECT picks.id FROM picks
				INNER JOIN cal ON cal.id = picks.calID
				{$where_rq} AND picks.valid=1 AND (picks.collectID!=0 OR cal.date <'{$currentDate}')
				UNION
				SELECT bundles.id FROM bundles 
				INNER JOIN picks ON picks.id = bundles.pickID
				INNER JOIN cal ON cal.id = picks.calID
				{$where_rq}  AND (bundles.collectID!=0 OR cal.date < '{$currentDate}')
			
			";
			$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
			$picks_nb = mysqli_num_rows($picks_rs);

			if($picks_nb){
				
				$picksPro_rq = "
				
					SELECT picks.id FROM picks
					INNER JOIN cal ON cal.id = picks.calID
					INNER JOIN users ON users.id = picks.userID
					{$where_rq}  AND picks.valid=1 AND users.societe!='' AND (picks.collectID!=0 OR cal.date <'{$currentDate}')

				";
				$picksPro_rs = mysqli_query($connexion, $picksPro_rq) or die(mysqli_error($connexion));
				$picksPro_nb = mysqli_num_rows($picksPro_rs);
				$picksPro_total += $picksPro_nb;
					
				$picksPar_nb = $picks_nb-$picksPro_nb;
				$picksPar_total += $picksPar_nb;
				
				$dateCell = "<a href='picks.php?date={$calDate}&period={$calPeriod}' style='font-weight:500;'>{$calCell}</a>";

				$picksMiss_rq = "	
				SELECT DISTINCT picks.id FROM picks
				INNER JOIN miss ON miss.pickID = picks.id AND miss.pickType = 'pick'
				INNER JOIN cal ON cal.id = picks.calID
				{$where_rq}
				UNION
				SELECT DISTINCT bundles.id FROM bundles 
				INNER JOIN miss ON miss.pickID = bundles.id AND miss.pickType = 'bundle'
				INNER JOIN picks ON picks.id = bundles.pickID
				INNER JOIN cal ON cal.id = picks.calID
				{$where_rq}";
				$picksMiss_rs = mysqli_query($connexion, $picksMiss_rq) or die(mysqli_error($connexion));
				$picksMiss_nb = mysqli_num_rows($picksMiss_rs);					
				if($picksMiss_nb){
					
					$picksPar_nb .= " <span class='badge badge-danger'>{$picksMiss_nb}</span>";
					$pMssTt += $picksMiss_nb;
					
				}
				
				$picksPro_cell = "-";
				if($picksPro_nb){
					$picksPro_cell = $picksPro_nb;
				}
				
				$picksPar_cell = "-";
				if($picksPar_nb){
					$picksPar_cell = $picksPar_nb;
				}
				
				$tbody.="
				<tr>
					<td>{$dateCell}</td>
					<td class='text-center table-light font-weight-bold'>{$picksPro_cell}</td>
					<td class='text-center table-light font-weight-bold'>{$picksPar_cell}</td>	
				</tr>";

			}

		}
			
		$table= "
		<div class='mb-4'>
			<table class='table table-sm font-weight-bold'>
				<thead>
					<tr>
						<th>Date</th>
						<th class='text-center' width='60'>Pro</th>
						<th class='text-center' width='60'>Par</th>
					</tr>
				</thead>
				<tbody>
					{$tbody}
				</tbody>
				<tfoot>
					<th class='text-right bg-dark'>Total</th>
					<td class='text-center font-weight-bolder bg-warning'>{$picksPro_total}</td>
					<td class='text-center font-weight-bolder bg-warning'>{$picksPar_total}</td>
				</tfoot>
			</table>
		</div>";
		
		return $table;
		
	}
}

/* picksDayStats */
function picksDayStats($date){
	
	global $connexion;
	
	$picksCal_rq = "
	SELECT picks.id FROM picks
	INNER JOIN cal ON cal.id = picks.calID
	WHERE picks.valid=1 AND cal.date = '{$date}'";
	$picksCal_rs=mysqli_query($connexion, $picksCal_rq) or die();

	if(mysqli_num_rows($picksCal_rs)){
		
		$slots_rq = "SELECT * FROM slots ORDER BY start ASC";
		$slots_rs=mysqli_query($connexion, $slots_rq) or die();
		while($slots=mysqli_fetch_assoc($slots_rs)){
			
			$picksSlot_rq = "		
			SELECT picks.id FROM picks
			INNER JOIN cal ON cal.id = picks.calID
			WHERE picks.valid=1 AND cal.date = '{$date}' AND picks.slotID = {$slots['id']}		
			UNION		
			SELECT bundles.id FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			INNER JOIN cal ON cal.id = picks.calID
			WHERE cal.date = '{$date}' AND picks.slotID = {$slots['id']}";
			$picksSlot_rs=mysqli_query($connexion, $picksSlot_rq) or die(mysqli_error($connexion));
			$picksSlot_nb = mysqli_num_rows($picksSlot_rs);
			
			if($picksSlot_nb){
				
				$tbody.="
				<tr>
					<th class='bg-dark font-weight-bold'>{$slots['start']}/{$slots['end']}</th>
					<td width='60' class='bg-warning font-weight-bolder text-center'>{$picksSlot_nb}</td>
				</tr>";
				
				$picks_rq = "		
				SELECT secteur, COUNT(*) AS nb FROM
				(
					SELECT picks.id, voies.secteur FROM picks
					INNER JOIN cal ON cal.id = picks.calID
					INNER JOIN adresses ON adresses.id = picks.adresseID
					INNER JOIN voies ON voies.id = adresses.voieID
					WHERE picks.valid=1 AND cal.date = '{$date}' AND picks.slotID = {$slots['id']}
					
					UNION
					
					SELECT bundles.id, voies.secteur FROM bundles
					INNER JOIN picks ON picks.id = bundles.pickID
					INNER JOIN cal ON cal.id = picks.calID
					INNER JOIN adresses ON adresses.id = picks.adresseID
					INNER JOIN voies ON voies.id = adresses.voieID
					WHERE cal.date = '{$date}' AND picks.slotID = {$slots['id']}
				) AS pb
				GROUP BY secteur";
				$picks_rs=mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));				
			
				while($picks=mysqli_fetch_array($picks_rs)){

					$pNbCell = $picks['nb'];
					$pTotal += $picks['nb'];
					
					$tbody.="
					<tr>
						<td style='font-weight:500;'>Secteur {$picks['secteur']}</td>
						<td class='text-center table-light font-weight-bold'>{$pNbCell}</td>
					</tr>";

				}
			}
		
		}
		
		$table="
		<section class='mb-4'>
		<table class='table font-weight-bold mb-1'>
		<thead>
			{$thead}
			<tr>
				<th>Secteur</th>
				<th class='text-center'>Nb</th>
			</tr>
		</thead>
		<tbody>
			{$tbody}
		</tbody>

		</table>
		</section>";
		
		return $table;
		
	}
}		

/* picksWeekStats */
function picksWeekStats($date, $week){
	
	global $connexion;
	
	if($date>date("Y") || $date==date("Y") && $week>=date("W")){
		
		$date_string = $date . 'W' . sprintf('%02d', $week);
		$dateStart = date('Y-m-d', strtotime($date_string));
		$dateEnd = date('Y-m-d', strtotime($date_string . '7'));

		$picks_rq = "
		SELECT picks.id FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		LEFT JOIN miss ON miss.pickID = picks.id
		WHERE picks.valid=1 AND cal.date BETWEEN '{$dateStart}' AND '{$dateEnd}' AND picks.collectID = 0 AND miss.id IS NULL";
		$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
		$picks_nb = mysqli_num_rows($picks_rs);
		
		if($picks_nb){
		
			if($date==date("Y")&&$week==date("W")){
				$start_day = date('w');
			}else{
				$start_day = 1;
			}
			
			for($day=$start_day; $day<=5; $day++){
				
				$calDate = date('Y-m-d', strtotime($date . 'W' . sprintf('%02d', $week) . $day));

				$picksDate_rq = "
				SELECT picks.id FROM picks
				INNER JOIN cal ON cal.id = picks.calID
				WHERE picks.valid=1 AND cal.date='{$calDate}' AND picks.collectID = 0";
				$picksDate_rs = mysqli_query($connexion, $picksDate_rq) or die(mysqli_error($connexion));
				$picksDate_nb = mysqli_num_rows($picksDate_rs);
				
				if($picksDate_nb){
					
					$dateCell = "<a href='picks.php?date={$calDate}&period=day' style='font-weight:500;'>".utf8_encode(strftime("%a %d %b", strtotime($calDate)))."</a>";
					
					$trClass = "table-default";
					if($calDate==date("Y-m-d")){
						$trClass = "table-warning";
					}
					
					$picksPar_rq = "
					SELECT picks.id FROM picks
					INNER JOIN cal ON cal.id = picks.calID
					INNER JOIN users ON users.id = picks.userID
					WHERE picks.valid=1 AND cal.date='{$calDate}' AND picks.collectID = 0 AND users.societe=''
					UNION		
					SELECT bundles.id FROM bundles 
					INNER JOIN picks ON picks.id = bundles.pickID
					INNER JOIN cal ON cal.id = picks.calID
					WHERE cal.date='{$calDate}' AND picks.collectID = 0	";
					$picksPar_rs = mysqli_query($connexion, $picksPar_rq) or die(mysqli_error($connexion));
					$picksPar_nb = mysqli_num_rows($picksPar_rs);
					
					$picksPro_rq = "
					SELECT picks.id FROM picks
					INNER JOIN cal ON cal.id = picks.calID
					INNER JOIN users ON users.id = picks.userID
					WHERE picks.valid=1 AND cal.date='{$calDate}' AND picks.collectID = 0 AND users.societe!=''";
					$picksPro_rs = mysqli_query($connexion, $picksPro_rq) or die(mysqli_error($connexion));
					$picksPro_nb = mysqli_num_rows($picksPro_rs);
					
					$picksPro_cell = "-";
					if($picksPro_nb){
						$picksPro_cell = $picksPro_nb;
					}
					
					$picksPar_cell = "-";
					if($picksPar_nb){
						$picksPar_cell = $picksPar_nb;
					}

					$tbody.="
					<tr class='{$trClass}'>
						<td>{$dateCell}</td>
						<td class='text-center table-light font-weight-bold'>{$picksPro_cell}</td>
						<td class='text-center table-light font-weight-bold'>{$picksPar_cell}</td>
						<td class='text-center bg-warning font-weight-bolder'>{$picksDate_nb}</td>
					</tr>";
				}
			}
				
			$table= "
			<div class='mb-4'>
			<table class='table font-weight-bold'>
				<thead>
					<tr>
						<th scope='col'>Date</th>
						<th width='60' scope='col' class='text-center'>Pro</th>
						<th width='60' scope='col' class='text-center'>Par</th>
						<th width='50'>Nb.</th>
					</tr>
				</thead>
				<tbody>
					{$tbody}
				</tbody>
			</table>
			</div>";
			
			return $table;
		
		}
	}
}

/* picksWeekPlan */
function picksWeekPlan($date, $week){
	
	global $connexion;
	
	if($date>date("Y") || $date==date("Y") && $week>date("W")){

		
		for($day=1; $day<=5; $day++){
			
			$calDate = date('Y-m-d', strtotime($date . 'W' . sprintf('%02d', $week) . $day));
			$dateCell = convertDate($calDate,'2adB');
			$tbody.="
			<tr>
				<th><a href='picks.php?date={$calDate}&period=day' class='text-white'>{$dateCell}</a></th>
				<th class='text-center font-weight-bold'><button data-edit='cal' data-rq='calDate={$calDate}&action=create' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0 '><i class='fas fa-plus-circle text-white'></i></button></th>
			</tr>";

			$picksDate_rq = "
			SELECT cal.id, cal.secteur FROM cal
			WHERE cal.date='{$calDate}'
			ORDER BY secteur";
			$picksDate_rs = mysqli_query($connexion, $picksDate_rq) or die(mysqli_error($connexion));
			while($picksDate=mysqli_fetch_assoc($picksDate_rs)){
				
				$tbody.="
				<tr class='{$trClass}'>
					<td>Secteur {$picksDate['secteur']}</td>
					<td class='text-center table-light font-weight-bold'><button data-edit='cal' data-rq='calID={$picksDate['id']}&action=delete' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0'><i class='fas fa-minus-circle'></i></button></td>
				</tr>";
			}
			
		}
			
		$table= "
		<div class='mb-4'>
		<table class='table font-weight-bold'>
			<thead>
				<tr>
					<th colspan='2' class='bg-dark p-3 rounded-top' style='font-size:.9rem;'>Planning</th>
				</tr>
			</thead>
			<tbody>
				{$tbody}
			</tbody>

		</table>
		</div>";
		
		return $table;
		
	}
}

/* picksLoc */
function picksLoc($calDate, $slotID, $secteur, $userID){
	
	global $connexion;
	
	$cal_rq = "SELECT * FROM cal WHERE date='{$calDate}'";
	$cal_rs = mysqli_query($connexion, $cal_rq) or die();
	$cal_nb = mysqli_num_rows($cal_rs);
	if($cal_nb){
		
		$cal = mysqli_fetch_array($cal_rs);
			
		$picks_rq = "
		SELECT picks.*, cal.date, users.nom, users.prenom, adresses.voieNumero, voies.voieType, voies.voieLibelle, slots.start, slots.end
		FROM picks
		INNER JOIN users ON picks.userID = users.id
		INNER JOIN adresses ON adresses.id = picks.adresseID
		INNER JOIN voies ON adresses.voieID = voies.id
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON picks.slotID = slots.id
		WHERE cal.date='{$calDate}' ";
		
		if($slotID){
			$picks_rq .= " AND picks.slotID={$slotID}";
		}
		
		if($userID){
			$picks_rq .= " AND picks.userID={$userID}";
			$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
			$picks = mysqli_fetch_array($picks_rs);
			$locationsTable.= "['{$picks['nom']} {$picks['prenom']}','{$picks['voieNumero']} ".addslashes($picks['voieType'])." ".addslashes($picks['voieLibelle'])." BORDEAUX', '{$picks['start']}/{$picks['end']} &bull; {$picks['sacs']} SAC(S)']";
			$title = "{$picks['voieNumero']} {$picks['voieType']} {$picks['voieLibelle']}";
		}else{
				
			$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
			while($picks = mysqli_fetch_array($picks_rs)){
				
				// LOCATIONS TABLE GMAPS
				$locationsTable.= "['{$picks['nom']} {$picks['prenom']}','{$picks['voieNumero']} ".addslashes($picks['voieType'])." ".addslashes($picks['voieLibelle'])." BORDEAUX', '{$picks['start']}/{$picks['end']} &bull; {$picks['sacs']} SAC(S)'],";
				$title = "Collectes {$calDate}";
				
			}
		}

	}
	
	$section="
	<h3>
		{$title}
		<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
		</button>
	</h3>
	
	<section id='picksLoc'>
		<div id='map' style='height:450px;'></div>
	</section>
	
	
	<script>
	$( document ).ajaxComplete(function() {
		var mapWidth = $('#map').width();
		//$('#map').height(mapWidth + 'px');

		var locations = [{$locationsTable}];

		var map = new google.maps.Map(document.getElementById('map'), {
		  zoom: 16,
		  center: new google.maps.LatLng(43.253205,-80.480347),
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var infowindow = new google.maps.InfoWindow();
		var geocoder = new google.maps.Geocoder();

		var marker, i;

		for (i = 0; i < locations.length; i++) {
		  geocodeAddress(locations[i]);
		}

		function geocodeAddress(location) {
		  geocoder.geocode( { 'address': location[1]}, function(results, status) {
		  //alert(status);
			if (status == google.maps.GeocoderStatus.OK) {

			  //alert(results[0].geometry.location);
			  map.setCenter(results[0].geometry.location);
			  createMarker(results[0].geometry.location,location[0]+'<br>'+location[1]+'<br>'+location[2]);
			}
			else
			{
			  alert('some problem in geocode' + status);
			}
		  }); 
		}

		function createMarker(latlng,html){
		  var marker = new google.maps.Marker({
			position: latlng,
			map: map
		  }); 

		  google.maps.event.addListener(marker, 'mouseover', function() { 
			infowindow.setContent(html);
			infowindow.open(map, marker);
		  });
				
		  google.maps.event.addListener(marker, 'mouseout', function() { 
			infowindow.close();
		  });
		}
	});
	</script>";
		
	
	return $section; 
	
}


?>