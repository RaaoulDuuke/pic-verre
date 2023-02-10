<?php 

// orderTable 
function orderTable($orderID){
	
	global $connexion;
	
	$orders_rq = "
	SELECT orders.*, abos.id AS aboID, subs.tarif AS subTarif, sacs.nb AS sNb, sacs.montant AS sMt, credits.nb AS cNb, credits.montant AS cMt, transactions.dateCreation AS transDate FROM orders
	LEFT JOIN transactions ON transactions.id = orders.tID
	LEFT JOIN credits ON credits.orderID = orders.id
	LEFT JOIN sacs ON sacs.orderID = orders.id
	LEFT JOIN abos ON abos.orderID = orders.id
	LEFT JOIN subs ON subs.ID = abos.subID
	WHERE orders.id = {$orderID}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);

	$orderMontant_cell = formatPrice($orders['montant']);
	
	if($orders['cNb']){
		
		$creditNb_cell = $orders['cNb'];
		
		if($orders['pro']){
			$creditMontant_cell = formatPrice($orders['cMt']/1.2);
			$creditPrice_cell = formatPrice($orders['cMt']/$orders['cNb']/1.2);
			
		}else{
			$creditMontant_cell = formatPrice($orders['cMt']);
			$creditPrice_cell = formatPrice($orders['cMt']/$orders['cNb']);
		}
		
		$credits_tr = "
		<tr>
			<td>Crédit(s)</td>
			<td class='text-center'>{$creditNb_cell}</td>
			<td class='text-center'>{$creditPrice_cell}</td>
			<td class='text-right table-light font-weight-bold'>{$creditMontant_cell}</td>
		</tr>";		
	}
	
	if($orders['sNb']){
		
		$sacNb_cell = $orders['sNb'];
				
		if($orders['pro']){
			$sacMontant_cell = formatPrice(($orders['sMt'])/1.2);
			$sacPrice_cell = formatPrice(2.92);
		}else{
			$sacMontant_cell = formatPrice($orders['sMt']);
			$sacPrice_cell = formatPrice($orders['sMt']/$orders['sNb']);
		}
		
		$sacs_tr = "
		<tr>
			<td>Sac(s)</td>
			<td class='text-center'>{$sacNb_cell}</td>
			<td class='text-center'>{$sacPrice_cell}</td>
			<td class='text-right table-light font-weight-bold'>{$sacMontant_cell}</td>
		</tr>";
		
	}
	
	if($orders['remise']){
		
		$remiseMontant_cell = formatPrice($orders['remise']);
		
		$remise_tr = "
		<tr>
			<td colspan='3'>Remise</td>
			<td class='text-right table-light font-weight-bold'>- {$remiseMontant_cell}</td>
		</tr>";		
		
	}
	
	if($orders['pro']){
		
		$orderHT_cell = formatPrice($orders['montant']/1.2);
		$orderTVA_cell = formatPrice($orders['montant']-($orders['montant']/1.2));

		$tfoot="
		<tr class='bg-secondary'>
			<th class='text-right' colspan='3'>Total HT</th>
			<td class='text-right table-dark font-weight-bold'>{$orderHT_cell}</td>
		</tr>
		<tr class='bg-secondary'>
			<th class='text-right' colspan='3'>TVA (20%)</th>
			<td class='text-right table-dark font-weight-bold'>{$orderTVA_cell}</td>
		</tr>			
		<tr class='bg-secondary'>
			<th class='text-right' colspan='3'>Total TTC</th>
			<td class='text-right table-dark font-weight-bold'>{$orderMontant_cell}</td>
		</tr>";
		
		$thead = "
		<tr class='bg-secondary'>
			<th>Désignation</th>
			<th class='text-center'>Qté</th>
			<th class='text-center'>Prix HT</th>
			<th class='text-right'>Mnt HT</th>
		</tr>";
		
	}else{
		
		$tfoot="
		<tr class='bg-secondary'>
			<th class='text-right' colspan='3'>Total</th>
			<td class='text-right bg-dark font-weight-bold'>{$orderMontant_cell}</td>
		</tr>";
		
		$thead = "
		<tr class='bg-secondary'>
			<th>Désignation</th>
			<th class='text-center'>Qté</th>
			<th class='text-center'>Prix</th>
			<th class='text-right'>Mnt</th>
		</tr>";
		
	}

	$table = "
	<table class='table table-sm'>
	<thead>
		<tr>
			<td colspan='4' class='bg-dark p-2 rounded-top font-weight-bold' style='font-size:.9rem;'>Détail de la commande</td>
		</tr>
		{$thead}
	</thead>
	<tbody>
		{$credits_tr}
		{$sacs_tr}
		{$remise_tr}
	</tbody>
	<tfoot>
		{$tfoot}
	</tfoot>
	</table>";
	
	return $table;

}

// orderInfos 
function orderInfos($orderID){
	
	global $connexion;
	
	$orders_rq = "
	SELECT orders.*	FROM orders	WHERE orders.id = {$orderID}";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_array($orders_rs);

	$nom_cell = ucwords($orders['nom']);
	$adresse_cell = ucwords(orderAdresse($orders['facturation']));
	$date_cell = convertDate($orders['dateCreation']);
	$reglement_cell = orderReglement($orders['reglement']);
	
	$table = "
	<table class='table table-sm'>
		<tr>
			<th class='bg-secondary'>Date</th>
			<td>{$date_cell}</td>
		</tr>
		<tr>
			<th class='bg-secondary'>Nom</th>
			<td>{$nom_cell}</td>
		</tr>
		<tr>
			<th class='bg-secondary' style='vertical-align:top;'>Adresse</th>
			<td>{$adresse_cell}</td>
		</tr>
		<tr>
			<th class='bg-secondary'>Reglement</th>
			<td>{$reglement_cell}</td>
		</tr>
	</table>";
	
	return $table;

}

// orderDetail 
function orderDetail($orderID){
	
	$orderInfos = orderInfos($orderID);
	$orderTable = orderTable($orderID);
	
	$orderRef = orderRef($orderID);
	
	$section = "
	<h3>
		Facture {$orderRef}
		<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
		</button>
	</h3>
	
	<section id='orderDetail'>
		{$orderInfos}
		{$orderTable}
	</section>";
	
	return $section;

}

/* orderCreditEdit */
function orderCreditEdit($userID){
	
	global $connexion;
	
	if(userActive($userID)){
		$orderTotal = "20";
		$sacTarif = 0;
		// $lead = $GLOBALS['creditEditLead'];
	}else{		
		$orderTotal = "25";
		$sacTarif = 5;
		$lead = "<p class='text-form font-weight-bold'>Une fois votre commande validée vous pourrez programmer votre première collecte.</p>";
	}

	$creditState = "disabled";
	
	$formules_rq = "SELECT * FROM formules ORDER BY credits ASC";
	$formules_rs = mysqli_query($connexion, $formules_rq) or die();
	while($formules = mysqli_fetch_assoc($formules_rs)){
		$formuleSelected = "";
		if($formules['credits']==0){
			$formuleLabel = "Moins de 6 crédits ";
		}else{
			$formuleLabel = "Pack de {$formules['credits']} crédits";
			if($formules['credits']==6){
				$formuleSelected = "selected";
			}
		}
		$selectOptions .=  "
		<option value='{$formules['id']}' data-price='{$formules['montant']}' data-credits='{$formules['credits']}' data-libelle='{$formules['libelle']}' {$formuleSelected}>{$formuleLabel}</option>";
	}
	
	$summary = "";
	
	if(!userActive($userID)){
		$summary .= "
		<li class='list-group-item bg-info'>
			<div class='h6'><span class='text-uppercase'>1 crédit = <strong class='text-white'>1 sac collecté</strong></span> <span class='badge badge-pill bg-secondary text-primary'>offert</span></div>
		</li>";
	}	
	
	
	$summary .= "
	<li id='list-group-credits' class='list-group-item bg-info {$lgCreditsClass}'>
		<div class='h6'><span class='text-uppercase'><span id='creditsNb'>6</span> crédit(s)<br><strong id='creditsLibelle' class='text-white'>soit 3.33&euro; par sac collecté</strong></span> <span id='creditsTotal' class='badge badge-pill bg-secondary text-primary'>20&euro;</span></div>
	</li>";
	
	
	if(!userActive($userID)){
		$summary .= "
		<li class='list-group-item bg-info pl-1'>
			<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:100px;'>
			<div class='h6'><span class='text-uppercase'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
			<span class='font-weight-bold'>Pour accéder au service vous devez faire l'acquisition d'un sac qui vous sera remis lors de votre première collecte.<br><a href='https://www.pic-verre.fr/sac' target='_blank' >En savoir plus</a></span>
		</li>";
	}
	
	$section = "
	<h3>
		<i class='fas fa-coins'></i> Créditer mon compte
		<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
		</button>
	</h3>
	<section id='creditEdit'>
		<form id='creditEditForm' action='edit/credit.edit.php' method='post' class='needs-validation' novalidate>

			{$lead}

			<div class='form-row'>
				<div class='col-md-9 form-group'>
					<label class='text-uppercase' style='font-weight:900;'>Crédits</label>
					<select id='formuleID' name='formuleID' class='form-control' required>
						{$selectOptions}
					</select>
				</div>
				<div class='col-md-3 form-group'>
					<label class='text-uppercase' style='font-weight:900;'>Nb.</label>
					<select name='credits' id='credits' class='form-control' {$creditState}>
						<option value='1'>1</option>
						<option value='2'>2</option>
						<option value='3'>3</option>
						<option value='4'>4</option>
						<option value='5'>5</option>
					</select>
				</div>
			</div>

			<ul class='list-group mb-3'>
				{$summary}
				<li class='list-group-item bg-secondary'>
					<div class='h6 text-uppercase text-white'>Total <span id='orderTotal' class='badge badge-pill bg-primary float-right'>{$orderTotal}&euro;</span></div>
				</li>
			</ul>
			
			<div class='form-footer form-row clearfix'>
				<div class='col-md-12 form-group mb-0'>
					<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Valider</button>
				</div>
				<div class='col-md-12 form-group text-center mt-2'>
					<p class='mb-0 font-weight-bold'><small>En validant ce formulaire, vous acceptez nos <a href='https://www.pic-verre.fr/cgv' target='_blank' class='text-nowrap link-primary'>conditions générales de vente</a></small></p>
				</div>
			</div>	
			
			<p class='text-center p-2 border-top border-light'><small>".$GLOBALS['citelisLead']."</small></p>
			
			<input type='hidden' name='userID' id='userID' value='{$userID}'/>
			<input type='hidden' name='action' value='{$action}'/>
			
		</form>
	</section>
	<script>
	
		{$script}

		$('#credits').change(function() {
			$('#creditsNb').html($('#credits').val());
			$('#creditsTotal').html($('#credits').val()*3.5+'&euro;');
			$('#orderTotal').html({$sacTarif}+$('#credits').val()*3.5+'&euro;');
		});

				
		$('#formuleID').on('change', function () {
			$('#creditsLibelle').html($('#formuleID').find(':selected').attr('data-libelle'));	
			if($('#formuleID').val()==1){
				
				$('#list-group-credits').removeClass('d-none');
				
				$('#credits').prop('disabled', false);
				$('#credits').val('1');
				$('#credits').prop('required',true);
				$('#creditsNb').html('1');
				$('#creditsTotal').html('3.5&euro;');						
				$('#orderTotal').html(3.5+{$sacTarif}+'&euro;');
				
			}else{
				
				if($('#formuleID').val()==''){
					$('#list-group-credits').addClass('d-none');
				}else{
					$('#list-group-credits').removeClass('d-none');
				}
				
				var credits = $('#formuleID').find(':selected').attr('data-credits');
				
				$('#credits').prop('disabled', true);
				$('#credits').val(credits);
				$('#credits').prop('required',false);
				$('#creditsNb').html(credits);						
				$('#creditsTotal').html($('#formuleID').find(':selected').attr('data-price')+'&euro;');
			
				var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
				$('#orderTotal').html(price+{$sacTarif}+'&euro;');
				
			}
		});
	</script>";

	// RETURN
	return $section;
}

/* orderSacEdit */
function orderSacEdit($userID){
	

	if(!userActive($userID)){
		$summary .= "
		<li class='list-group-item bg-info'>
			<div class='h6'><span class='text-uppercase'>1 crédit = <strong class='text-white'>1 sac collecté</strong></span> <span class='badge badge-pill bg-secondary text-primary'>offert</span></div>
		</li>
		<li class='list-group-item bg-info pl-1'>
			<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:100px;'>
			<div class='h6'><span class='text-uppercase'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
			<span class='font-weight-bold'>Pour accéder au service vous devez faire l'acquisition d'un sac qui vous sera remis lors de votre première collecte.<br><a href='https://www.pic-verre.fr/sac' target='_blank' >En savoir plus</a></span>
		</li>";
	}else{
	
	$summary .= "
	<li class='list-group-item bg-info pl-1'>
		<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:100px;'>
		<div class='h6'><span class='text-uppercase'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
		<span class='font-weight-bold'>Votre sac vous sera remis lors de votre prochaine collecte.</span>
	</li>";
	}
	

	
	$section = "
	<h3>
		<i class='fas fa-shopping-bag'></i> Commander un sac
		<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
		</button>
	</h3>
	<section id='sacEdit'>
		<form action='edit/sac.edit.php' method='post' id='sacEditForm' class='needs-validation' novalidate>

			<div class='row'>

				
				<div class='col-sm-12'>
					<ul class='list-group mb-3'>
						{$summary}
						<li class='list-group-item bg-secondary'>
							<div class='h6 text-uppercase text-white'>Total <span id='sacsTotal' class='badge badge-pill bg-primary float-right'>5&euro;</span></div>
						</li>
					</ul>
				</div>
			</div>		
			
			<div class='form-footer form-row clearfix'>
				<div class='col-md-12 form-group mb-0'>
					<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Valider</button>
				</div>
				<div class='col-md-12 form-group text-center mt-2'>
					<p class='mb-0'><small>En validant ce formulaire, vous acceptez nos <a href='https://www.pic-verre.fr/cgv' target='_blank' class='text-nowrap link-primary'>conditions générales de vente</a></small></p>
				</div>
			</div>	
			
			<p class='text-center p-2 m-0 border-top border-light'><small>".$GLOBALS['citelisLead']."</small></p>
			
		</form>
	</section>

	<script>		
		$('#sacs').change(function() {				
			$('#sacsNb').html($( this ).val());
			$('#sacsTotal').html($( this ).val()*5+'&euro;');
			$('#sacsMontant').html($( this ).val()*5+'&euro;');
		});
	</script>";
	
	return $section;
	
}


?>