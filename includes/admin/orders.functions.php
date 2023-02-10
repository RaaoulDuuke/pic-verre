<?php

/* ordersPage */
function ordersPage($period, $date){
	
	global $connexion;
	
	$ordersTab = ordersTab($period, $date);	
	$ordersStats = ordersStats($period, $date);

	$content = "
	<div class='row'>
		<div class='col-lg-4'>
			{$ordersStats}	
		</div>			
		<div class='col-lg-8'>
			{$ordersTab}
		</div>
	</div>";
	
	return $content;
	
}

/* ordersTab */
function ordersTab($period, $date, $userID){
	
	global $connexion;
	
		$whereTrans_rq = dateRequest($period, $date, "transactions");
		$whereOrders_rq = dateRequest($period, $date, "orders");
		
		if(!empty($userID)){
			$whereTrans_rq .= " AND orders.userID={$userID}";
			$whereOrders_rq .= " AND orders.userID={$userID}";
			
			if(empty($date)){
				$whereTrans_rq = substr($whereTrans_rq,4);
				$whereOrders_rq = substr($whereOrders_rq,4);	
			}
		}
		
		$ordTrans_rq = "
		SELECT COUNT(orders.id) AS nb FROM orders 
		INNER JOIN transactions ON transactions.id = orders.tID 
		WHERE orders.tID!=0 AND {$whereTrans_rq}";
		$ordTrans_rs=mysqli_query($connexion, $ordTrans_rq) or die(mysqli_error($connexion));
		$ordTrans=mysqli_fetch_assoc($ordTrans_rs);
		if($ordTrans['nb']){
			$transTable = ordersTable($period, $date, $userID, 'trans');
		}else{
			$transTable = "<div class='alert alert-light rounded-0'>Aucune commande</div>";
		}
		
		$tabNav_trans = "<a class='nav-item nav-link active' data-toggle='tab' href='#orders'>Facturées <span class='badge badge-warning font-weight-bold'>{$ordTrans['nb']}</span></a>";
		
		$tabPane_trans = "
		<div class='tab-pane active' id='orders'>
			{$transTable}	
		</div>";
			
		if(empty($userID)||(!empty($userID)&&!userPro($userID))){
			
			$ordCancel_rq = "
			SELECT COUNT(orders.id) AS nb FROM orders
			WHERE orders.tID=0 AND orders.reglement='CB' AND {$whereOrders_rq}";
			$ordCancel_rs=mysqli_query($connexion, $ordCancel_rq) or die(mysqli_error($connexion));
			$ordCancel=mysqli_fetch_assoc($ordCancel_rs);
			if($ordCancel['nb']){
				$cancelTable = ordersTable($period, $date, $userID, 'cancel');
			}else{
				$cancelTable = "<div class='alert alert-light rounded-0'>Aucune commande</div>";
			}
			
			$tabNav_cancel = "<a class='nav-item nav-link' data-toggle='tab' href='#cancel'>Annulées <span class='badge badge-warning font-weight-bold'>{$ordCancel['nb']}</span></a>";
			
			$tabPane_cancel = "
			<div class='tab-pane' id='cancel'>
				{$cancelTable}
			</div>";
		}
		
		if(empty($userID)||(!empty($userID)&&userPro($userID))){
			
			$ordPending_rq = "
			SELECT COUNT(orders.id) AS nb FROM orders
			WHERE orders.tID=0 AND orders.reglement!='CB' AND {$whereOrders_rq}";
			$ordPending_rs=mysqli_query($connexion, $ordPending_rq) or die(mysqli_error($connexion));
			$ordPending=mysqli_fetch_assoc($ordPending_rs);
			if($ordPending['nb']){
				$pendingTable = ordersTable($period, $date, $userID, 'pending');
			}else{
				$pendingTable = "<div class='alert alert-light rounded-0'>Aucune commande</div>";
			}
			
			$tabNav_pending = "<a class='nav-item nav-link' data-toggle='tab' href='#pending'>Devis <span class='badge badge-warning font-weight-bold'>{$ordPending['nb']}</span></a>";
			
			$tabPane_pending = "
			<div class='tab-pane' id='pending'>
				{$pendingTable}
			</div>";
		}
		
		$tab = "
		<nav>
			<div class='nav nav-tabs nav-fill' id='orders-tab' role='tablist'>
				{$tabNav_trans}
				{$tabNav_pending}
				{$tabNav_cancel}
			</div>
		</nav>
		<div class='tab-content' id='stats-tabContent'>
			{$tabPane_trans}
			{$tabPane_pending}
			{$tabPane_cancel}
		</div>";
		
		if(!empty($userID)){
			
			$userPro = userPro($userID);
			if($userPro){
				$btnTitle = "Nouveau devis";
			}else{
				$btnTitle = "Nouvelle commande";
			}
			
			$tab .= "
			<button data-edit='orders' data-rq='action=create&userID={$userID}&orderPro={$userPro}' data-toggle='modal' data-target='#editModal' class='btn btn-warning btn-block mt-3'>{$btnTitle}</button>";
			
		}
		
		return $tab;

}

// ordersTable *************************************
function ordersTable($period, $date, $userID, $type){
	
	global $connexion;	
	
	if($type=="trans"){
		$where_table = "transactions";
	}else{
		$where_table = "orders";
	}
	
	$where_rq = dateRequest($period, $date, $where_table);
	
	if($type=="trans"){			
		$where_rq .= " AND orders.tID!=0";
	}
	
	if($type=="pending"){	
		$where_rq .= " AND orders.tID=0 AND orders.reglement!='CB'";
	}
	
	if($type=="cancel"){	
		$where_rq .= " AND orders.tID=0 AND orders.reglement='CB'";
	}
	
	if(!empty($userID)){	
	
		$where_rq .= " AND orders.userID={$userID} ";
		if(empty($date)){
			$where_rq = substr($where_rq,4);
		}
		
		$tfoot_colspan = 3;
		$userCol_class = "d-none";
		
	}else{
		
		$tfoot_colspan = 4;
		
	}
	
	$ordersTotal_rq = "
	SELECT SUM(orders.montant) AS ordersMontant, SUM(sacs.montant) AS sacsMontant, SUM(credits.montant) AS creditsMontant, SUM(orders.remise) AS remisesMontant, SUM(credits.nb) AS creditsNb, SUM(sacs.nb) AS sacsNb
	FROM orders
	LEFT JOIN transactions ON orders.tID = transactions.id
	LEFT JOIN credits ON credits.orderID = orders.id AND credits.montant!=0
	LEFT JOIN sacs ON sacs.orderID = orders.id  AND sacs.montant!=0
	WHERE {$where_rq}";
	$ordersTotal_rs=mysqli_query($connexion, $ordersTotal_rq) or die(mysqli_error($connexion));
	$ordersTotal=mysqli_fetch_array($ordersTotal_rs);
	
	$orders_rq = "
	SELECT transactions.dateCreation AS tDate, orders.dateCreation AS oDate, orders.tID, orders.id AS orderID, orders.montant, orders.reglement, orders.remise, orders.userID, orders.resellerID, orders.pro
	FROM orders
	LEFT JOIN transactions ON orders.tID = transactions.id
	WHERE {$where_rq}
	ORDER BY {$where_table}.dateCreation DESC";	
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	
	if(mysqli_num_rows($orders_rs)){

		$creditsMontant = 0;
		$sacsMontant = 0;
		$remisesMontant = 0;
		$transMontant = 0;
	
		while($orders=mysqli_fetch_array($orders_rs)){
				
			// DATE CELL
			if($orders['tID']){
				$dCell = convertDate($orders['tDate'],"en2fr");
				$dCmdCell = $orders['tDate']; 
				$editCol_class='d-none';

			}else{
				$dCell = convertDate($orders['oDate'],"en2fr");	
				$dCmdCell = $orders['oDate']; 
				$edit_cell = orderBtn($orders['orderID']);					
			}
			
			$ref = orderRef($orders['orderID'], $orders['tID'], $orders['reglement'], $orders['pro']);
			$rCell = "<button data-edit='orders' data-rq='orderID={$orders['orderID']}&action=detail' data-toggle='modal' data-target='#editModal' class='btn btn-link' type='button'>{$ref}</button>";
			
			
			if($orders['userID']){
				$uCell = userLink($orders['userID']);
			}else{
				$uCell = resellerLink($orders['resellerID']);
			}
			
			// MONTANT CELL
			$mtCell = formatPrice($orders['montant']);	
			
			$tbody .= " 		
			<tr class='{$trClass}'>
				<td>{$dCmdCell}</td>
				<td style='font-weight:500;' class='align-middle'>{$dCell}</td>
				<td class='align-middle text-center'>{$rCell}</td>
				<td class='align-middle {$userCol_class}'><span class='d-inline-block text-truncate' style='max-width:250px;'>{$uCell}</span></td>
				<td class='align-middle text-right bg-light font-weight-bold'>{$mtCell}</td>
				<td class='bg-light {$editCol_class}'>{$edit_cell}</td>
			</tr>";			
		}		

		if(empty($userID)){
						
			if($ordersTotal['creditsMontant']){
				$creditsTotal_td = formatPrice($ordersTotal['creditsMontant']);
				$creditsTotal_tr = "
				<tr style='font-size:1rem;'>
					<th colspan='4' scope='row'>Crédits <span class='badge badge-warning'>{$ordersTotal['creditsNb']}</span></th>
					<td class='font-weight-bold bg-dark'>{$creditsTotal_td}</td>
					<th class='{$editCol_class}'></th>
				</tr>";
			}
			
			if($ordersTotal['sacsMontant']){
				$sacsTotal_td = formatPrice($ordersTotal['sacsMontant']);
				$sacsTotal_tr = "
				<tr style='font-size:1rem;'>
					<th colspan='4' scope='row'>Sacs <span class='badge badge-warning'>{$ordersTotal['sacsNb']}</span></th>
					<td class='font-weight-bold bg-dark'>{$sacsTotal_td}</td>
					<th class='{$editCol_class}'></th>
				</tr>";
			}
			
			if($ordersTotal['remisesMontant']){
				$remisesTotal_td = formatPrice($ordersTotal['remisesMontant']);
				$remisesTotal_tr = "
				<tr style='font-size:1rem;'>
					<th colspan='4' scope='row'>Remises</th>
					<td class='font-weight-bold bg-dark'>{$remisesTotal_td}</td>
					<th class='{$editCol_class}'></th>
				</tr>";
			}
			
			$tfoot = "
			{$creditsTotal_tr}
			{$sacsTotal_tr}
			{$remisesTotal_tr}";	

		}
		
		$ordersTotal_td = formatPrice($ordersTotal['ordersMontant']);
		
		$table = "
		
		<table style='margin-top:0!important' class='table table-hover font-weight-bold' id='{$type}OrdersTable'>
			<thead>
				<tr>
					<th scope='col'>Date (Ymd)</th>
					<th style='width:65px' scope='col'>Date</th>
					<th style='width:90px' scope='col' class=' text-center'>Ref.</th>
					<th scope='col' class='{$userCol_class}'>Abonné</th>
					<th style='width:90px' scope='col' class='text-right'>Mt.</th>
					<th class='{$editCol_class}' style='width:40px'></th>
				</tr>
			</thead>
			<tbody>
				{$tbody}
			</tbody>
			<tfoot class='text-right'>
			
				{$tfoot}
				
				<tr style='font-size:1rem;'>
					<th class='bg-dark' scope='row' colspan='{$tfoot_colspan}'>Total</th>
					<td class='font-weight-bolder bg-warning'>{$ordersTotal_td}</td>
					<th class='bg-dark {$editCol_class}'></th>
				</tr>
				
			</tfoot>	
		</table>

		";
		
		return $table;
		
	}
}

// ordersStats ***************************************
function ordersStats($period, $date){
	
	global $connexion;
	
	$where_rq = dateRequest($period, $date, "transactions");
	
	$orders_rq = "
	SELECT COUNT(orders.id) AS nb FROM orders 
	INNER JOIN transactions ON transactions.id = orders.tID 
	WHERE orders.tID!=0 AND {$where_rq}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_assoc($orders_rs);
	if($orders['nb']){
	
		$formules_rq = "
		SELECT SUM(credits.nb) AS nb FROM credits
		LEFT JOIN orders ON orders.id = credits.orderID
		LEFT JOIN transactions ON transactions.id = orders.tID
		WHERE orders.tID!=0 AND {$where_rq}";
		$formules_rs=mysqli_query($connexion, $formules_rq) or die(mysqli_error($connexion));
		$formules=mysqli_fetch_array($formules_rs);
		
		if($period=="year"){
			$stats .= ordersStatsDate($period, $date);
		}
		
		if($formules['nb']){
			$statsCredits = ordersStatsCredits($period, $date);
		}else{
			$statsCredits = "<div class='alert alert-light rounded-0'>Aucun crédit facturé</div>";
		}
		
		$stats .= "	
		<h3>Crédits <span class='badge badge-warning font-weight-bolder'>{$formules['nb']}</span></h3>
		{$statsCredits}";
		
		return $stats;
		
	}

}

/* ordersStatsDate */
function ordersStatsDate($period, $date){
	
	global $connexion;
	
	$date_th = "Jour";

	$where_rq = dateRequest($period, $date, "transactions");
	
	switch($period){
		case "year":
			$group_rq = " MONTH(transactions.dateCreation)";
			$date_th = "Mois";
		break;			
		case "month":
			$group_rq = " DAY(transactions.dateCreation)";
		break;

	}
	
	// FLUX TOTAL REQUEST
	$orders_rq = "
	SELECT SUM(orders.montant) AS montant, transactions.dateCreation AS date FROM orders 
	LEFT JOIN transactions ON transactions.id = orders.tID
	WHERE orders.tID!=0 AND {$where_rq}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);
	
	if($orders['montant']){
	
		// FLUX REQUEST
		$ordersGroup_rq = $orders_rq." GROUP BY {$group_rq}";
		$ordersGroup_rs=mysqli_query($connexion, $ordersGroup_rq) or die(mysqli_error($connexion)); 
		while($ordersGroup=mysqli_fetch_array($ordersGroup_rs)){

			if($period=="year"){
				$date_td = "<a href='orders.php?period=month&date=".date('Y-m', strtotime($ordersGroup['date']))."'  style='font-weight:500;'>".convertDate($ordersGroup['date'],"2bY")."</a>";
			}else{
				$date_td = "<a href='orders.php?period=day&date=".$ordersGroup['date']."' style='font-weight:500;'>". convertDate($ordersGroup['date'],"en2fr")."</a>";
			}

			$totalGroup_td = formatPrice($ordersGroup['montant']);
			$ratio_td = "<small>".round((100*$ordersGroup['montant'])/$orders['montant'],1)."%</small>";
			
			// FORMULES STATS ROW
			$tbody.="
			<tr>
				<td>{$date_td}</td>
				<td class='text-right'>{$ratio_td}</td>
				<td class='text-right bg-light font-weight-bold'>{$totalGroup_td}</td>
			</tr>";
			
		}
		
		$total_td = formatPrice($orders['montant']);
		
		// FORMULES STATS TFOOT
		$table="
		<h3>Factures</h3>
		<table class='table table-hover mb-4 font-weight-bold' id='flux-{$stat}-stats-table'>
		<thead>
			<tr>
				<th>{$date_th}</th>
				<th class='text-right' class='text-center'></th>
				<th style='width:130px;' class='text-right'>Mt.</th>
			</tr>
		</thead>
		<tbody>
			{$tbody}
		</tbody>
		<tfoot>
			<tr class='text-right'>
				<th class='bg-dark' colspan='2' style='font-size:1rem;'>Total</th>
				<td class='bg-warning font-weight-bolder' style='font-size:1rem;'>{$total_td}</td>
			</tr>
		</tfoot>
		</table>";
					
		return $table; 
	
	}
}	

/* ordersStatsCredits */
function ordersStatsCredits($period, $date){
	
	global $connexion;	
	
	$where_rq = dateRequest($period, $date, "transactions");
	
	$credits_rq = "
	SELECT SUM(credits.montant) AS total, COUNT(credits.id) AS nb, formules.designation FROM credits
	LEFT JOIN formules ON formules.id = credits.formuleID
	LEFT JOIN orders ON orders.id = credits.orderID
	LEFT JOIN transactions ON transactions.id = orders.tID
	WHERE orders.tID!=0 AND {$where_rq}";
	$credits_rs=mysqli_query($connexion, $credits_rq) or die(mysqli_error($connexion));
	$credits=mysqli_fetch_array($credits_rs);

	$creditsGroup_rq = $credits_rq." GROUP BY credits.formuleID ORDER BY formules.credits";
	$creditsGroup_rs=mysqli_query($connexion, $creditsGroup_rq) or die(mysqli_error($connexion));
	while($creditsGroup=mysqli_fetch_array($creditsGroup_rs)){
		
		$totalGroup_td = formatPrice($creditsGroup['total']);
		
		$designation_td = $creditsGroup['designation'];
		if($designation_td==""){
			$designation_td = "Crédits pro";
		}

		$tbody.="
		<tr>
			<td style='font-weight:500;'>{$designation_td} <span class='badge badge-warning'>{$creditsGroup["nb"]}</span></td>
			<td class='text-right bg-light font-weight-bold'>{$totalGroup_td}</td>
		</tr>";
	}
	
	$total_td = formatPrice($credits['total']);

	$table="
	<table class='table table-hover table-sm font-weight-bold' id='credits-stats-table'>
	<thead>
		<tr>
			<th>Formule</th>
			<th width='130' class='text-right'>Montant</th>
		</tr>
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	<tfoot>
		<tr>
			<th class='text-right bg-dark' style='font-size:1rem;'>Total</th>
			<td class='text-right bg-warning font-weight-bolder' style='font-size:1rem;'>{$total_td}</td>
		</tr>
	</tfoot>
	</table>";
	
	return $table;
	
}	


?>