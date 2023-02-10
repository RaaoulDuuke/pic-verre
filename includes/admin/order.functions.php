<?php

// orderInfos ***************************************
function orderInfos($orderID){
	
	global $connexion;
	
	$orders_rq = "
	SELECT orders.*	FROM orders	WHERE orders.id = {$orderID}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);
	
	$ref_cell = orderRef($orderID);
	$nom_cell = $orders['nom'];
	$date_cell = convertDate($orders['dateCreation']);
	$reglement_cell = orderReglement($orders['reglement']);
	
	if($orders['userID']){
		$user_tr = "
		<tr>
			<th width='120'>Nom</th>
			<td>{$nom_cell}</td>
		</tr>";
		
	}
	
	if($orders['resellerID']){
		$reseller_td = resellerName($orders['resellerID']);
		$reseller_tr = "
		<tr>
			<th width='120'>Revendeur</th>
			<td>{$reseller_td}</td>
		</tr>";
	}
	
	$table = "
	<table class='table table-sm font-weight-bold'>
		<tr>
			<th width='120'>Ref.</th>
			<td>{$ref_cell}</td>
		</tr>
		<tr>
			<th>Date</th>
			<td>{$date_cell}</td>
		</tr>		
		{$user_tr}
		<tr>
			<th>Reglement</th>
			<td>{$reglement_cell}</td>
		</tr>
		{$reseller_tr}
	</table>";
	
	return $table;

}

// orderTable ***************************************
function orderTable($orderID){
	
	global $connexion;
	
	$orders_rq = "
	SELECT orders.*, sacs.nb AS sNb, sacs.montant AS sMt, credits.nb AS cNb, credits.montant AS cMt, transactions.dateCreation AS transDate
	FROM orders
	LEFT JOIN transactions ON transactions.id = orders.tID
	LEFT JOIN credits ON credits.orderID = orders.id
	LEFT JOIN sacs ON sacs.orderID = orders.id
	WHERE orders.id = {$orderID}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);

	$orderMontant_cell = formatPrice($orders['montant']);
	$orderTVA_cell = formatPrice($orders['montant']-($orders['montant']/1.2));
	
	if($orders['cNb']){
		
		$creditNb_cell = $orders['cNb'];
		
		if($orders['pro']){
			
			$creditMontant_cell = formatPrice($orders['cMt']/1.2);
			$creditNbMois_cell = $orders['cNb']/12;
			$creditMontantMois_cell = formatPrice($orders['cMt']/1.2/12);
			$creditPrice_cell = formatPrice($orders['cMt']/$orders['cNb']/1.2);
			
			$tbody.="
			<tr>
				<td>Crédits</td>
				<td class='text-center'>{$creditNb_cell}</td>
				<td class='text-center'>{$creditPrice_cell}</td>
				<td class='text-right table-light font-weight-bold'>{$creditMontant_cell}</td>
			</tr>";
			
			
		}else{
			$creditMontant_cell = formatPrice($orders['cMt']);
			$creditPrice_cell = formatPrice($orders['cMt']/$orders['cNb']);
			
			$tbody.="
			<tr>
				<td>Crédit(s)</td>
				<td class='text-center'>{$creditNb_cell}</td>
				<td class='text-center'>{$creditPrice_cell}</td>
				<td class='text-right table-light font-weight-bold'>{$creditMontant_cell}</td>
			</tr>";
		}
		
	}
	
	if($orders['sNb']){
		
		$sacNb_cell = $orders['sNb'];
				
		if($orders['pro']){
			$sacMontant_cell = formatPrice($orders['sMt']/1.2);
			$sacPrice_cell = formatPrice($orders['sMt']/1.2/$orders['sNb']);
		}else{
			$sacMontant_cell = formatPrice($orders['sMt']);
			$sacPrice_cell = formatPrice($orders['sMt']/$orders['sNb']);
		}
		
		$tbody.="
		<tr>
			<td>Sacs</td>
			<td class='text-center'>{$sacNb_cell}</td>
			<td class='text-center'>{$sacPrice_cell}</td>
			<td class='text-right table-light font-weight-bold'>{$sacMontant_cell}</td>
		</tr>";
		
	}
	
	if($orders['remise']){
		
		$remiseMontant_cell = formatPrice($orders['remise']);
		
		$tbody.="
		<tr class='table-warning'>
			<td colspan='3'>Remise</td>
			<td class='text-right table-light font-weight-bold'>- {$remiseMontant_cell}</td>
		</tr>";		
		
	}
	
	if($orders['pro']){
		
		$orderHT_cell = formatPrice($orders['montant']/1.2);
		

		$tfoot="
		<tr>
			<th class='text-right bg-dark' colspan='3'>Total HT</th>
			<td class='text-right table-dark font-weight-bold bg-warning'>{$orderHT_cell}</td>
		</tr>
		<tr>
			<th class='text-right' colspan='3'>TVA (20%)</th>
			<td class='text-right table-dark font-weight-bold'>{$orderTVA_cell}</td>
		</tr>			
		<tr>
			<th class='text-right' colspan='3'>Total TTC</th>
			<td class='text-right table-dark font-weight-bold'>{$orderMontant_cell}</td>
		</tr>";
		
		$thead = "
		<tr>
			<th>Désignation</th>
			<th class='text-center'>Qté</th>
			<th class='text-center'>Prix HT</th>
			<th class='text-right'>Mnt HT</th>
		</tr>";
		
	}else{
		
		$tfoot="	
		<tr>
			<th class='text-right bg-dark' colspan='3'>Total</th>
			<td class='text-right table-dark font-weight-bold bg-warning'>{$orderMontant_cell}</td>
		</tr>
		<tr>
			<th class='text-right' colspan='3'>TVA (20%)</th>
			<td class='text-right table-dark font-weight-bold'>{$orderTVA_cell}</td>
		</tr>";
		
		$thead = "
		<tr>
			<th>Désignation</th>
			<th class='text-center'>Qté</th>
			<th class='text-center'>Prix</th>
			<th class='text-right'>Mnt</th>
		</tr>";
		
	}

	// DETAIL THEAD
	$table = "
	<table class='table table-sm font-weight-bold'>
	<thead>
		<tr>
			<td colspan='4' class='bg-dark p-2 rounded-top ' style='font-size:.9rem;'>Détail commande</td>
		</tr>
		{$thead}
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	<tfoot>
		{$tfoot}
	</tfoot>
	</table>";
	
	return $table;

}

// orderTablePrint ***************************************
function orderTablePrint($orderID){
	
	global $connexion;
	
	$orders_rq = "
	SELECT orders.*, abos.id AS aboID, subs.tarif AS subTarif, sacs.nb AS sNb, sacs.montant AS sMt, credits.nb AS cNb, credits.montant AS cMt, transactions.dateCreation AS transDate
	FROM orders
	LEFT JOIN transactions ON transactions.id = orders.tID
	LEFT JOIN credits ON credits.orderID = orders.id
	LEFT JOIN sacs ON sacs.orderID = orders.id
	LEFT JOIN abos ON abos.orderID = orders.id
	LEFT JOIN subs ON subs.ID = abos.subID
	WHERE orders.id = {$orderID}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);


	$orderMontant_cell = formatPrice($orders['montant']);
	
/*	
	if($orders['aboID']&&!$orders['pro']){
		

		$aboMontant_cell = formatPrice($orders['subTarif']);
		
		$tbody.='
		<tr>
			<td width="330" style="border-bottom:1px solid #DDD;" >ABONNEMENT</td>
			<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">1</td>
			<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$aboMontant_cell.'</td>
			<td width="100" style="text-align:right; border-bottom:1px solid #DDD;"><strong>'.$aboMontant_cell.'</strong></td>
		</tr>';
	}
*/	

	if($orders['cNb']){
		
		$creditNb_cell = $orders['cNb'];
		
		if($orders['pro']){

			$creditMontant_cell = formatPrice($orders['cMt']/1.2);
			$creditNbMois_cell = $orders['cNb']/12;
			$creditMontantMois_cell = formatPrice($orders['cMt']/1.2/12);
			$creditPrice_cell = formatPrice($orders['cMt']/$orders['cNb']/1.2);
			
			$tbody.='
			<tr>
				<td colspan="4" style="text-align:center; padding:15px;" bgcolor="#DDDDDD">1 crédit = 1 sac collecté (environ 5kg de verre)</td>
			</tr>
			<tr>
				<td width="330" style="border-bottom:1px solid #DDD;">CR&Eacute;DIT(S)/MOIS</td>
				<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$creditNbMois_cell.'</td>
				<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$creditPrice_cell.'</td>
				<td width="100" style="text-align:right; border-bottom:1px solid #DDD;"><strong>'.$creditMontantMois_cell.'/mois</strong></td>
			</tr>
			<tr>
				<td width="330" style="border-bottom:1px solid #DDD;">CR&Eacute;DIT(S)/AN</td>
				<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$creditNb_cell.'</td>
				<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$creditPrice_cell.'</td>
				<td width="100" style="text-align:right; border-bottom:1px solid #DDD;"><strong>'.$creditMontant_cell.'/an</strong></td>
			</tr>';	
			

		}else{
			$creditMontant_cell = formatPrice($orders['cMt']);
			$creditPrice_cell = formatPrice($orders['cMt']/$orders['cNb']);
			
			$tbody.='
			<tr>
				<td width="330" style="border-bottom:1px solid #DDD;">CR&Eacute;DIT(S)<br><small>1 crédit correspond à 1 sac collecté</small></td>
				<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$creditNb_cell.'</td>
				<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$creditPrice_cell.'</td>
				<td width="100" style="text-align:right; border-bottom:1px solid #DDD;"><strong>'.$creditMontant_cell.'</strong></td>
			</tr>';	
		}
		
	}
	
	if($orders['sNb']){
		
		$sacNb_cell = $orders['sNb'];
				
		if($orders['pro']){
			$sacMontant_cell = formatPrice(($orders['sMt'])/1.2);
			$sacPrice_cell = formatPrice($orders['sMt']/$orders['sNb']/1.2);
			
			if($orders['cNb']){
				$tbody.='
				<tr>
					<td colspan="4" style="text-align:center; padding:15px;" bgcolor="#DDDDDD">Afin de faciliter le stockage et d’optimiser le temps de collecte vous devez faire l’acquisition du nombre de sacs que le picker devra recupérer lors des collectes</td>
				</tr>';
			}

		}else{
			$sacMontant_cell = formatPrice($orders['sMt']);
			$sacPrice_cell = formatPrice($orders['sMt']/$orders['sNb']);
			
		}
		
		$tbody.='
		<tr>
			<td width="330" style="border-bottom:1px solid #DDD;">SAC(S)<br><small>Spécialement adaptés au stockage et au transport du verre</strong></td>
			<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$sacNb_cell.'</td>
			<td width="100" style="text-align:center; border-bottom:1px solid #DDD;">'.$sacPrice_cell.'</td>
			<td width="100" style="text-align:right; border-bottom:1px solid #DDD;"><strong>'.$sacMontant_cell.'</strong></td>
		</tr>';
		
	}
	
	if($orders['remise']){
		
		$remiseMontant_cell = formatPrice($orders['remise']);
		
		$tbody.='
		<tr bgcolor="#DDDDDD">
			<td width="330" style="border-bottom:1px solid #DDD;">REMISE</td>
			<td width="100" style="border-bottom:1px solid #DDD;"></td>
			<td width="100" style="border-bottom:1px solid #DDD;"></td>
			<td width="100" style="text-align:right; border-bottom:1px solid #DDD;"><strong>- '.$remiseMontant_cell.'</strong></td>
		</tr>';		
		
	}
	
	if($orders['pro']){
		
		$orderHT_cell = formatPrice($orders['montant']/1.2);
		$orderTVA_cell = formatPrice($orders['montant']-($orders['montant']/1.2));

		$tfoot='
		<tr border="1">
			<th width="330" bgcolor="#2fac66" style="border-bottom:1px solid #FFF;" ></th>
			<th width="100" bgcolor="#2fac66" style="border-bottom:1px solid #FFF;"  ></th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:right; border-bottom:1px solid #FFF;">Total HT</th>
			<td width="100" bgcolor="#000000" style="color:#FFF; text-align:right; border-bottom:1px solid #FFF;"><strong>'.$orderHT_cell.'</strong></td>
		</tr>
		<tr>
			<th width="330" bgcolor="#2fac66" style="border-bottom:1px solid #FFF;"  ></th>
			<th width="100" bgcolor="#2fac66" style="border-bottom:1px solid #FFF;"  ></th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:right; border-bottom:1px solid #FFF;">TVA (20%)</th>
			<td width="100" bgcolor="#000000" style="color:#FFF; text-align:right; border-bottom:1px solid #FFF;">'.$orderTVA_cell.'</td>
		</tr>			
		<tr>
			<th width="330" bgcolor="#2fac66" style="border-bottom:1px solid #FFF;"  ></th>
			<th width="100" bgcolor="#2fac66" style="border-bottom:1px solid #FFF;"  ></th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:right; border-bottom:1px solid #FFF;">Total TTC</th>
			<td width="100" bgcolor="#000000" style="color:#FFF; text-align:right; border-bottom:1px solid #FFF;">'.$orderMontant_cell.'</td>
		</tr>';
		
		$thead = '
		<tr>
			<th width="330" bgcolor="#2fac66" style="color:#FFF;">Désignation</th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:center;">Qté</th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:center;">Prix <small>(HT)</small></th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:right;">Montant <small>(HT)</small></th>
		</tr>';
		
	}else{
		
		$tfoot = '
		<tr>
			<th width="330" bgcolor="#2fac66" ></th>
			<th width="100" bgcolor="#2fac66" ></th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:right;">Total</th>
			<td width="100" bgcolor="#000000" style="color:#FFF; text-align:right;"><strong>'.$orderMontant_cell.'</strong></td>
		</tr>';
		
		$thead = '
		<tr>
			<th width="330" bgcolor="#2fac66" style="color:#FFF;">Désignation</th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:center;">Qté</th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:center;">Prix</th>
			<th width="100" bgcolor="#2fac66" style="color:#FFF; text-align:right;">Montant</th>
		</tr>';
		
	}

	// DETAIL THEAD
	$table = '
	<table cellpadding="3">
		'.$thead.'
		'.$tbody.'
		'.$tfoot.'
	</table>';
	
	return $table;
	
}

// orderInfosPrint ***************************************
function orderInfosPrint($orderID){
	
	global $connexion;
	
	$orders_rq = "
	SELECT orders.*	FROM orders	WHERE orders.id = {$orderID}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);
	
	$date_cell = convertDate($orders['dateCreation']);
	$ref_cell = orderRef($orderID, $orders['tID'], $orders['reglement'], $orders['pro']);
	$nom_cell = mb_strtoupper($orders['nom'],"UTF-8");
	$adresse_cell = mb_strtoupper(orderAdresse($orders['facturation']),"UTF-8");
	$reglement_cell = mb_strtoupper(orderReglement($orders['reglement']),"UTF-8");
	
	$table = '
	<table cellpadding="3" style="border-top:1px solid #DDD;">
		<tr>
			<th width="150" bgcolor="#2fac66" style="color:#FFF; border-bottom:1px solid #DDD;">Ref.</th>
			<td width="470" style="border-bottom:1px solid #DDD;">'.$ref_cell.'</td>
		</tr>
		<tr>
			<th width="150" bgcolor="#2fac66" style="color:#FFF; border-bottom:1px solid #DDD;">Nom</th>
			<td width="470" style="border-bottom:1px solid #DDD; text-transform:uppercase;">'.$nom_cell.'</td>
		</tr>
		<tr>
			<th width="150" bgcolor="#2fac66" style="color:#FFF; border-bottom:1px solid #DDD;">Adresse</th>
			<td width="470" style="border-bottom:1px solid #DDD;">'.$adresse_cell.'</td>
		</tr>
		<tr>
			<th width="150" bgcolor="#2fac66" style="color:#FFF; border-bottom:1px solid #DDD;">Date</th>
			<td width="470" style="border-bottom:1px solid #DDD;">'.$date_cell.'</td>
		</tr>
		<tr>
			<th width="150" bgcolor="#2fac66" style="color:#FFF; border-bottom:1px solid #DDD;">Reglement</th>
			<td width="470" style="border-bottom:1px solid #DDD;">'.$reglement_cell.'</td>
		</tr>
	</table>';
	
	return $table;

}

// orderDetail **************************************
function orderDetail($orderID){
	
	$orderInfos = orderInfos($orderID);
	$orderTable = orderTable($orderID);
	$orderType = orderType($orderID);
	
	if($orderType!="commande"){
		
		$editBtn = "<a href='edit/order.print.php?orderID={$orderID}' class='btn btn-block btn-outline-secondary' target='_blank'>Editer PDF</a>";
		
	}
	
	$section = "
	<h3>
		Infos {$orderType}
		<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
		</button>
	</h3>
	
	<section id='orderDetail'>
		{$orderInfos}
		{$orderTable}
		{$editBtn}
	</section>";
	
	return $section;

}

// orderBtn *****************************************
function orderBtn($orderID){
	
	global $connexion;
	
	$order_rq = "
	SELECT orders.tID, orders.pro, orders.reglement, orders.userID FROM orders WHERE orders.id = {$orderID}";
	$order_rs=mysqli_query($connexion, $order_rq) or die(mysqli_error($connexion));
	$order=mysqli_fetch_array($order_rs);
	
	$orderType = orderType($orderID);

	
	if($order['reglement']!="CB"&&!$order['tID']){
		$btns .= "
		<button data-edit='orders' data-rq='action=valid&orderID={$orderID}' data-toggle='modal' data-target='#editModal' class='dropdown-item text-success'>Valider {$orderType}</button>	
		<button data-edit='orders' data-rq='action=update&orderID={$orderID}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Modifier {$orderType}</button>				
		<div class='dropdown-divider'></div>
		";
	}
	
	if(!$order['tID']){
		$btns .= "
		<button data-edit='orders' data-rq='action=delete&orderID={$orderID}' data-toggle='modal' data-target='#editModal' class='dropdown-item text-danger'>Supprimer {$orderType}</button>";
	}
	
	$btnDrop = "
	<div class='dropdown dropleft'>
		<button class='btn btn-sm btn-secondary dropdown-toggle' type='button' id='edit{$orderID}' data-toggle='dropdown'></button>
		<div class='dropdown-menu' aria-labelledby='edit{$orderID}'>
			{$btns}
		</div>
	</div>";
	
	return $btnDrop;
	
}

// orderEdit ****************************************
function orderEdit($action, $orderID, $userID, $orderPro){
	
	global $connexion;
	$err = 0;
	
	switch($action){
		
		case 'create':
			if(empty($userID)){			
				$err = 1;
			}else{
				if($orderPro){
					$title = "Nouveau devis";
				}else{
					$title = "Nouvelle facture";
				}
			} 
			
		break;
		
		case 'update':
		case 'valid':
		case 'delete':
		
			if(empty($orderID)){
				
				$err = 1;
				
			}else{
				
				$sSQL = "
				SELECT orders.userID, orders.pro AS orderPro, orders.montant AS orderMontant, orders.remise AS remiseMontant, subs.id AS subID, subs.title AS subLibelle, subs.tarif AS subMontant, sacs.nb AS sacNb, sacs.montant AS sacMontant, credits.formuleID AS formuleID, credits.nb AS creditNb, credits.montant AS creditMontant FROM orders
				LEFT JOIN transactions ON transactions.id = orders.tID
				LEFT JOIN credits ON credits.orderID = orders.id
				LEFT JOIN sacs ON sacs.orderID = orders.id
				LEFT JOIN abos ON abos.orderID = orders.id
				LEFT JOIN subs ON subs.ID = abos.subID
				WHERE orders.id = {$orderID}";
				$result = mysqli_query($connexion, $sSQL) or die(mysqli_error($connexion));
				if ($row = mysqli_fetch_assoc($result)) {
					foreach ($row as $key => $value) {
						$$key = $value;
					}
				}	
				mysqli_free_result($result);
				
				if($action=="update"){
					$titleAction = "Modifier";
				}
				
				if($action=="delete"){
					$titleAction = "Annuler";
				}
				
				if($action=="valid"){
					$titleAction = "Valider";
				}
				
				if($orderPro){
					$title = "{$titleAction} le devis";
				}else{
					$title = "{$titleAction} la commande";
				}
				
				
			}
		break;			
		default;
			$err = 1;
		break;
		
	}	

	if(!$err){
		
		if($action=='create' || $action=='update'){
			
			if($orderPro){

				$sacPrice = 4.17;
				$creditPrice = 2.08;
				
				if($action=="create"){
					
					$sacNb = 0;
					$sacMontant = 0;
					$lgSacsClass = "d-none";
					$creditNb = 0;
					$creditMontant = 0;
					$lgCreditsClass = "d-none";
					$remise = 0;
					$lgRemiseClass = "d-none";
					
					$orderMontant = 41.67;
					$orderHT = 50.00;
					$orderTVA = 8.33;
					
					// SCRIPT
					$script .= "
					$('#sacs').change(function() {
						
						var sacMontant = $('#sacs').val()*5;
						var sacMontantHT = (sacMontant/1.2).toFixed(2);
						
						var creditsNb = $('#frequence').val()*$('#sacs').val();
						var creditsMontant = creditsNb*2.5;
						var creditsMontantHT = (creditsMontant/1.2).toFixed(2);
						var creditsMontantMoisHT = (creditsMontant/1.2/12).toFixed(2);
												
						var orderMontant = (sacMontant+creditsMontant).toFixed(2);
						var orderMontantHT = (orderMontant/1.2).toFixed(2);
						var orderTVA = (orderMontant-orderMontantHT).toFixed(2);
						
						$('#orderHT').html(orderMontantHT);
						$('#orderTTC').html(orderMontant);
						$('#orderTVA').html(orderTVA);
						
						$('#sacsNb').html($(this).val());
						$('#sacsTotal').html(sacMontantHT);
						$('#creditsNb').html(creditsNb);
						$('#creditsTotal').html(creditsMontantHT);
						$('#creditsTotalMois').html(creditsMontantMoisHT);

						if($('#sacs').val()==0){
							$('#list-group-sacs').addClass('d-none');
							$('#list-group-credits').addClass('d-none');
						}else{
							$('#list-group-sacs').removeClass('d-none');
							$('#list-group-credits').removeClass('d-none');
						}
						
					});
					
					$('#frequence').on('change', function () {
						
						var sacMontant = $('#sacs').val()*5;
						var sacMontantHT = (sacMontant/1.2).toFixed(2);
						
						var creditsNb = $('#frequence').val()*$('#sacs').val();
						var creditsMontant = creditsNb*2.5;
						var creditsMontantHT = (creditsMontant/1.2).toFixed(2);
						var creditsMontantMoisHT = (creditsMontant/1.2/12).toFixed(2);
												
						var orderMontant = (sacMontant+creditsMontant).toFixed(2);
						var orderMontantHT = (orderMontant/1.2).toFixed(2);
						var orderTVA = (orderMontant-orderMontantHT).toFixed(2);
						
						$('#orderHT').html(orderMontantHT);
						$('#orderTTC').html(orderMontant);
						$('#orderTVA').html(orderTVA);
						
						$('#creditsNb').html(creditsNb);
						$('#creditsTotal').html(creditsMontantHT);
						$('#creditsTotalMois').html(creditsMontantMoisHT);
						
					});";
					
				}
				
				if($action=="update"){
					
					// SCRIPT
					$script .= "
					$('#sacs').change(function() {
						
						if($('#tva').val()==20){
							var sacTTC = 5;
							var creditTTC = 2.5;
						}
						
						if($('#tva').val()==5.5){
							var sacTTC = 4.4;
							var creditTTC = 2.19;
						}	
										
						var sacMontant = $('#sacs').val()* sacTTC;
						var sacMontantHT = ($('#sacs').val()*4.17).toFixed(2);
						var creditsNb = $('#credits').val();
						var creditsMontant = creditsNb*creditTTC;			
						var creditsMontantHT = (creditsNb*2.08).toFixed(2);
						var orderMontant = (sacMontant+creditsMontant).toFixed(2);
						var orderMontantHT = (orderMontant/1.2).toFixed(2);
						var orderTVA = (orderMontant-orderMontantHT).toFixed(2);						
						$('#orderHT').html(orderMontantHT);
						$('#orderTTC').html(orderMontant);
						$('#orderTVA').html(orderTVA);
						
						$('#sacsNb').html($(this).val());
						$('#sacsTotal').html(sacMontantHT);

						if($('#sacs').val()==0){
							$('#list-group-sacs').addClass('d-none');
						}else{
							$('#list-group-sacs').removeClass('d-none');
						}
						
					});
					
					$('#credits').change(function() {	
						
						var sacMontant = $('#sacs').val()*5;
						var sacMontantHT = (sacMontant/1.2).toFixed(2);
						var creditsNb = $('#credits').val();
						var creditsMontant = creditsNb*2.5;
						var creditsMontantHT = (creditsMontant/1.2).toFixed(2);				
						var orderMontant = (sacMontant+creditsMontant).toFixed(2);
						var orderMontantHT = (orderMontant/1.2).toFixed(2);
						var orderTVA = (orderMontant-orderMontantHT).toFixed(2);						
						$('#orderHT').html(orderMontantHT);
						$('#orderTTC').html(orderMontant);
						$('#orderTVA').html(orderTVA);
						
						$('#creditsNb').html(creditsNb);
						$('#creditsTotal').html(creditsMontantHT);
						
					});
					
					$('#remise').change(function() {
						
						var sacMontant = $('#sacs').val()*5;
						var sacMontantHT = (sacMontant/1.2).toFixed(2);
						var creditsNb = $('#credits').val();
						var creditsMontant = creditsNb*2.5;
						var creditsMontantHT = (creditsMontant/1.2).toFixed(2);
						var remiseHT = $('#remise').val();
						var remise = (remiseHT*1.2).toFixed(2);
						var orderMontant = (sacMontant+creditsMontant-remise).toFixed(2);
						var orderMontantHT = (orderMontant/1.2).toFixed(2);
						var orderTVA = (orderMontant-orderMontantHT).toFixed(2);						
						$('#orderHT').html(orderMontantHT);
						$('#orderTTC').html(orderMontant);
						$('#orderTVA').html(orderTVA);
						
						$('#remiseMontant').html(remiseHT);
						
						if($('#remise').val()==0){
							$('#list-group-remise').addClass('d-none');
						}else{
							$('#list-group-remise').removeClass('d-none');
						}

					});
					
					$('#tva').change(function() {
						
						if($('#tva').val()==20){
							var sacTTC = 5;
							var creditTTC = 2.5;
						}
						
						if($('#tva').val()==5.5){
							var sacTTC = 4.4;
							var creditTTC = 2.19;
						}
						
						var sacMontant = $('#sacs').val()* sacTTC;
						var sacMontantHT = $('#sacs').val() * 4.17;
						
						var creditsNb = $('#credits').val();
						var creditsMontant = creditsNb*creditTTC;			
						var creditsMontantHT = (creditsNb*2.08);
						
						var orderMontant = (sacMontant+creditsMontant).toFixed(2);
						var orderMontantHT = (sacMontantHT+creditsMontantHT).toFixed(2);
						var orderTVA = (orderMontant-orderMontantHT).toFixed(2);	
						
						$('#orderHT').html(orderMontantHT);
						$('#orderTTC').html(orderMontant);
						$('#orderTVA').html(orderTVA);


					});
					
					
					";					
					
				}
								
				$orderMontant = number_format($orderMontant, 2, '.', ' ');
				$orderHT =  number_format(($orderMontant/1.2), 2, '.', ' ');
				$orderTVA = number_format(($orderMontant-$orderHT), 2, '.', ' ');
				$creditMontant = number_format(($creditMontant/1.2), 2, '.', ' ');
				$creditMontantMois = number_format(($creditMontant/1.2/12), 2, '.', ' ');
				$sacMontant = number_format(($sacMontant/1.2), 2, '.', ' ');
				
				
				if(!$remiseMontant){
					$lgRemiseClass = "d-none";
				}else{
					$remiseMontant = number_format($remiseMontant, 2, '.', ' ');
				}
				
				// SACS FORM GROUP
				for($i=0;$i<=25;$i++){				
					$sacSelect_state = "";
					if($i==$sacNb){
						$sacSelect_state = "selected";
					}					
					$sacSelect .="<option value='{$i}' {$sacSelect_state}>{$i}</option>";
				}				
				$sacs_formGrp = "
				<label>Sacs</label>
				<select name='sacs' id='sacs' class='form-control'>
					{$sacSelect}
				</select>";
				
				if($action=="create"){
					
					$frequence_formGrp = "
					<label>Fréquence</label>
					<select id='frequence' name='frequence' class='form-control' required>
						<option value='12' data-multi='1'>1 fois par mois</option>
						<option value='24' data-multi='2'>2 fois par mois</option>
						<option value='52' data-multi='4'>1 fois par semaine</option>
						<option value='104' data-multi='8'>2 fois par semaine</option>
						<option value='365' data-multi='16'>1 fois par jour</option>
					</select>";
					
					// ORDER FORM ROW
					$order_formRow = "
					<div class='form-row'>
						<div class='col-md-9 form-group'>
							{$frequence_formGrp}
						</div>						
						<div class='col-md-3 form-group'>
							{$sacs_formGrp}
						</div>					
					</div>";

				}else{
					
					$credits_formGrp = "
					<label>Crédits</label>
					<input type='text' value='{$creditNb}' name='credits' id='credits' class='form-control' >";
					
					$remise_formGrp = "
					<label>Remise</label>
					<input type='text' value='{$remiseMontant}' name='remise' id='remise' class='form-control' >";
					
					// ORDER FORM ROW
					$order_formRow = "
					<div class='form-row'>										
						<div class='col-md-4 form-group'>
							{$credits_formGrp}
						</div>
						<div class='col-md-4 form-group'>
							{$sacs_formGrp}
						</div>	
						<div class='col-md-4 form-group'>
							{$remise_formGrp}
						</div>
					</div>";
				}

			}
			
			
			else{
				
				// SUBS FORM GROUP
				if(!userActive($userID)){
					
					$subLibelle = "Sac";
					$subMontant = 5;
					
					if($subID){
						
						$orderMontant = 8.5;
						
					}else{
						
						$orderMontant = 0;						
						$lgSubClass = "d-none";
						$lgCreditsClass = "d-none";
						
						$selectFormule_state = "selected";
						
						$creditState = "disabled";
						$script_crd = "$('#credits').val('');";
						
					}
					
					$selectFormuleOptions .= "<option value='' data-price='0' data-credits='' data-libelle='' {$selectFormule_state}>Sans abonnement</option>";
					
					
					$subs_formGrp = "<input type='hidden' id='subID' name='subID' value='{$subID}'>";

				}
				
				
						
				$sacPrice = 5;
				$remise = 0;
				$lgRemiseClass = "d-none";
				
				if($action=="create"){
					
					$lgSacsClass = "d-none";
					
					$sacNb = 0;
					$sacMontant = 0;
					$creditNb = 1;
					$creditPrice = 3.50;
					$creditMontant = 3.50;
					
					if(userActive($userID)){
						
						$subMontant = 0;
						
						if(userCredits($userID)){
							$selectFormuleOptions .= "<option value='' data-price='0' data-credits='' data-libelle=''>Ne pas créditer</option>";
							$creditState = "disabled";
							$lgCreditsClass = "d-none";
							$script_crd = "$('#credits').val('');";
							$orderMontant = 0;
						}else{

							$orderMontant = 3.5;
						}
						
					}

				}else{
					
					if(!$sacNb){
						$lgSacsClass = "d-none";
					}
					
					if(!$creditNb){
						$lgCreditsClass = "d-none";
					}else{
						
						$creditPrice = formatPrice($creditMontant/$creditNb);
						
						if($formuleID!=1){
							$creditState = "disabled";
							$script_crd = "$('#credits').val('');";
						}	
						
					}					
				}

				for($i=0;$i<=5;$i++){
					
					$sacsOptions_state = "";
					if($i==$sacNb){
						$sacsOptions_state = "selected";
					}
					
					$selectSacsOptions .="<option value='{$i}' {$sacsOptions_state}>{$i}</option>";
				}

				$formules_rq = "SELECT * FROM formules ORDER BY credits ASC";
				$formules_rs = mysqli_query($connexion, $formules_rq) or die();
				while($formules = mysqli_fetch_assoc($formules_rs)){
					
					$formuleOptions_state = "";
					if($formules['id']==$formuleID){
						$formuleOptions_state = "selected";
					}		
					
					if($formules['credits']==0){
						$formuleLabel = "- de 6 crédits";
					}else{
						$formuleLabel = "Pack {$formules['credits']} crédits";
					}
					$selectFormuleOptions .=  "
					<option value='{$formules['id']}' data-price='{$formules['montant']}' data-credits='{$formules['credits']}' data-libelle='{$formules['libelle']}' {$formuleOptions_state}>{$formuleLabel}</option>";
				}

				for($i=1;$i<=5;$i++){
					
					$creditsOptions_state = "";
					if($i==$creditNb){
						$creditsOptions_state = "selected";
					}
					
					$selectCreditsOptions .="<option value='{$i}' {$creditsOptions_state}>{$i}</option>";
				}
				

				$order_formRow = "
				<div class='form-row'>
				
					{$subs_formGrp}
					
					<div class='col-md-6 form-group'>
						<label>Crédits</label>
						<select id='formuleID' name='formuleID' class='form-control'>
							{$selectFormuleOptions}
						</select>
					</div>
					
					<div class='col-md-3 form-group'>
						<label>Nb</label>
						<select name='credits' id='credits' class='form-control' {$creditState}>
							{$selectCreditsOptions}
						</select>
					</div>
					
					<div class='form-group col-md-3 '>
						<label>Sac(s)</label>
						<select name='sacs' id='sacs' class='form-control'>
							{$selectSacsOptions}
						</select>
					</div>


				</div>";
				
				$script .= "
				
				{$script_crd}
				
				$('#formuleID').on('change', function () {
					
					if($('#formuleID').val()==1){
						
						if($('#subID').length){
							var subMontant=5;
							$('#subID').val('1');
							$('#list-group-sub').removeClass('d-none');
						}else{
							var subMontant=0;
						}
						
						$('#list-group-credits').removeClass('d-none');
						
						$('#credits').prop('disabled', false);
						$('#credits').prop('required',true);
						$('#credits').val('1');
						$('#creditsNb').html('1');
						$('#creditsPrix').html('3.5');
						$('#creditsTotal').html('3.5');						
						$('#orderTTC').html(3.5+subMontant+$('#sacs').val()*5);	
						
					}else{

						if($('#formuleID').val()==''){
							
							$('#list-group-credits').addClass('d-none');
							
							var subMontant=0;
							
							if($('#subID').length){
								$('#list-group-sub').addClass('d-none');
								$('#subID').val('');
								
							}
							
						}else{
							
							$('#list-group-credits').removeClass('d-none');
							
							if($('#subID').length){
							
								$('#list-group-sub').removeClass('d-none');
								$('#subID').val('1');
								var subMontant=5;
								
							}else{
								var subMontant=0;
							}
							
						}
												
						$('#credits').prop('disabled', true);
						$('#credits').val('');
						$('#credits').prop('required',false);
						$('#creditsNb').html($('#formuleID').find(':selected').attr('data-credits'));						$('#creditsPrix').html(($('#formuleID').find(':selected').attr('data-price')/$('#formuleID').find(':selected').attr('data-credits')).toFixed(2));
						$('#creditsTotal').html($('#formuleID').find(':selected').attr('data-price'));
					
						var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
						$('#orderTTC').html(price+subMontant+$('#sacs').val()*5);

					}
					
				});
				
				
				$('#credits').change(function() {	
					
					if($('#subID').length &&  $('#subID').val()=='1'){
						var subMontant=5;
					}else{
						var subMontant=0;	
					}

					$('#creditsNb').html($('#credits').val());
					$('#creditsTotal').html($('#credits').val()*3.5);				
					$('#orderTTC').html(subMontant+$('#credits').val()*3.5+$('#sacs').val()*5);	
				});
				
				$('#sacs').change(function() {
					
					if($('#formuleID').val()==''){
						var subMontant=0;
					}else{
						if($('#subID').length && $('#subID').val()=='1'){
							var subMontant=5;
						}else{
							var subMontant=0;
						}
					}
				
					$('#sacsNb').html($(this).val());
					$('#sacsTotal').html($('#sacs').val()*5);
					
					if($('#sacs').val()==0){
						$('#list-group-sacs').addClass('d-none');
					}else{
						$('#list-group-sacs').removeClass('d-none');
					}
					
					if($('#formuleID').val()==1){
						$('#orderTTC').html(subMontant+$('#credits').val()*3.5+$('#sacs').val()*5);
					}else{
						var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
						$('#orderTTC').html(subMontant+price+$('#sacs').val()*5);
					}
					
				});";
				
			}
			
			// FACTURE FORM ROW
			$user_rq = "
			SELECT users.nom, users.prenom, users.societe, adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle, voies.cp, voies.ville FROM users 
			INNER JOIN adresses ON adresses.id = users.adresseID
			INNER JOIN voies ON voies.id = adresses.voieID
			WHERE users.id={$userID}";
			$user_rs = mysqli_query($connexion, $user_rq) or die();
			$user = mysqli_fetch_assoc($user_rs);		

			if(!$orderPro){
				$orderNom = "{$user['nom']} {$user['prenom']}";
			}else{
				$orderNom = $user['societe'];
			}
			
			$orderAdresse = "{$user['voieNumero']} {$user['voieType']} {$user['voieLibelle']}, {$user['cp']} {$user['ville']}";
			
			$nom_formGroup = "
			<label>Nom</label>
			<input type='text' name='nom' class='form-control' value='{$orderNom}' disabled />";
			
			$adresse_formGroup = "
			<label>Adresse fct.</label>
			<input type='text' name='adresse' class='form-control' value='{$orderAdresse}' />";
			
			$reglement_formGroup = "
			<label>Reglement</label>
			<select id='reglement' name='reglement' class='form-control' required>
				<option value='CHQ'>Chèque</option>
				<option value='ESP'>Espece</option>
			</select>";


			$tva_formGroup = "
			<label>TVA</label>
			<select id='tva' name='tva' class='form-control' required>
				<option value='20'>20%</option>
				<option value='5.5'>5.5%</option>
			</select>";
			
						
			$facture_formRow = "
			<div class='form-row'>
				<div class='col-md-12 form-group'>
					{$nom_formGroup}
				</div>
			</div>
			<div class='form-row'>
				<div class='col-md-12 form-group'>
					{$adresse_formGroup}
				</div>
			</div>
			<div class='form-row'>
				<div class='col-md-6 form-group'>
					{$reglement_formGroup}
				</div>
				<div class='col-md-6 form-group'>
					{$tva_formGroup}
				</div>
			</div>";
			
			// ORDER TABLE
			if($orderPro){
				
				$thead = "
				<tr>
					<th>Désignation</th>
					<th class='text-center'>Qté</th>
					<th class='text-center'>Prix HT</th>
					<th class='text-right'>Mnt HT</th>
				</tr>";	
				
				$tfoot="
				<tr>
					<th class='text-right bg-dark' colspan='3'>Total HT</th>
					<td class='text-right bg-warning font-weight-bold'><span id='orderHT'>{$orderHT}</span>&euro;</td>
				</tr>
				<tr>
					<th class='text-right' colspan='3'>TVA (20%)</th>
					<td class='text-right table-dark font-weight-bold'><span id='orderTVA'>{$orderTVA}</span>&euro;</td>
				</tr>			
				<tr>
					<th class='text-right' colspan='3'>Total TTC</th>
					<td class='text-right table-dark font-weight-bold'><span id='orderTTC'>{$orderMontant}</span>&euro;</td>
				</tr>";
										
			}
			
			else{
				
				$thead = "
				<tr>
					<th>Désignation</th>
					<th class='text-center'>Qté</th>
					<th class='text-center'>Prix</th>
					<th class='text-right'>Mnt</th>
				</tr>";	
				
				$tfoot="
				<tr>
					<th class='text-right' colspan='3'>Total</th>
					<td class='text-right table-dark font-weight-bold'><span id='orderTTC'>{$orderMontant}</span>&euro;</td>
				</tr>";
										
			}
				
			$tbody = "";
			if(!userActive($userID)&&!userPro($userID)){
				$tbody .= "
				<tr id='list-group-sub' class='{$lgSubClass}'>
					<td><span id='subslibelle'>{$subLibelle}</span></td>
					<td class='text-center'>1</td>
					<td class='text-center'><span id='subsMontant'>{$subMontant}</span>&euro;</td>
					<td class='bg-light text-right font-weight-bold'><span id='subsTotal'>{$subMontant}</span>&euro;</td>
				</tr>";
			}
			
			if(userPro($userID)){
			
				$tbody .= "
				<tr id='list-group-credits-mois' class='{$lgCreditsMoisClass}'>
					<td>Crédit(s)/an</td>
					<td class='text-center'><span id='creditsNb'>{$creditNb}</span></td>
					<td class='text-center'><span id='creditsPrix'>{$creditPrice}</span>&euro;</td>
					<td class='bg-light text-right font-weight-bold'><span id='creditsTotal'>{$creditMontant}</span>&euro;</td>
				</tr>
				<tr id='list-group-credits' class='{$lgCreditsClass}'>
					<td>Crédit(s)/mois</td>
					<td class='text-center'></td>
					<td class='text-center'></td>
					<td class='bg-light text-right font-weight-bold'><span id='creditsTotalMois'>{$creditMontantMois}</span>&euro;</td>
				</tr>
				";
			
			}else{
				$tbody .= "			
				<tr id='list-group-credits' class='{$lgCreditsClass}'>
					<td>Crédit(s)</td>
					<td class='text-center'><span id='creditsNb'>{$creditNb}</span></td>
					<td class='text-center'><span id='creditsPrix'>{$creditPrice}</span>&euro;</td>
					<td class='bg-light text-right font-weight-bold'><span id='creditsTotal'>{$creditMontant}</span>&euro;</td>
				</tr>";			
				
			}
			
			$tbody .= "	
			<tr id='list-group-sacs' class='{$lgSacsClass}'>
				<td>Sac(s)</td>
				<td class='text-center'><span id='sacsNb'>{$sacNb}</span></td>
				<td class='text-center'>{$sacPrice}</td>
				<td class='bg-light text-right font-weight-bold'><span id='sacsTotal'>{$sacMontant}</span>&euro;</td>
			</tr>
			
			<tr id='list-group-remise' class='{$lgRemiseClass}'>
				<td colspan='3'>Remise</td>
				<td class='bg-light text-right font-weight-bold'>- <span id='remiseMontant'>{$remiseMontant}</span>&euro;</td>
			</tr>";

			$order_table = "
			<table class='table table-sm' id='devis-listing-table'>
				<thead>
					<tr>
						<td colspan='4' class='bg-dark p-2 rounded-top font-weight-bold' style='font-size:.9rem;'>Détail</td>
					</tr>
					{$thead}
				</thead>
				<tbody>{$tbody}</tbody>
				<tfoot>{$tfoot}</tfoot>
			</table>";	
			
			$form = "
			{$facture_formRow}
			{$order_formRow}
			{$order_table}";

		}
		
		if($action=="delete"){
			
			$orderInfos = orderInfos($orderID);
			$orderTable = orderTable($orderID);
			
			$form = "
			<div class='alert alert-danger'>Etes vous sur de vouloir anuler cette commande ? </div>
			{$orderInfos}
			{$orderTable}";

		}
		
		if($action=="valid"){
			
			$orderInfos = orderInfos($orderID);
			$orderTable = orderTable($orderID);
			
			$form = "
			<div class='alert alert-warning'>Etes vous sur de vouloir valider cette commande ? </div>
			{$orderInfos}
			{$orderTable}
			<div class='form-row'>
				<div class='col-6 form-group'>
					<label>Date</label>
					<input type='text' name='dateFacturation' class='form-control datepicker'>
				</div>
				<div class='col-6 form-group'>
					<label>Transaction</label>
					<input type='text' name='transID' class='form-control'>
				</div>
			</div>";
			
		}

		$section = "
		<h3>
			{$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>
		<section id='orderEdit'>	
			<form id='orderEditForm' action='edit/orders.edit.php' method='post'>
				{$form}		
				<div class='form-row clearfix'>
				<div class='col-12 form-group mb-0'>
					<button class='btn btn-block btn-warning' type='submit'>Valider</button>
				</div>
				<input type='hidden' name='orderID' value='{$orderID}'/>
				<input type='hidden' name='orderPro' value='{$orderPro}'/>					
				<input type='hidden' name='userID' value='{$userID}'/>
				<input type='hidden' name='action' value='{$action}'/>			
			</form>			
		</section>		
		<script>		
			{$script}
		</script>";

		return $section;
		
	}	
}


?>