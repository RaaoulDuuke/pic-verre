<?php

/* CREDITS *********************************************/

/* creditsSection */	
function creditsSection($userID){
	
	global $connexion;
	
	if(userCredits($userID)>0){
		$creditsLibelle = userCredits($userID);
		$sectionClass = "bg-info";
		$creditsLabel = "<strong class='badge badge-pill badge-dark mb-2'>Crédits : {$creditsLibelle} <i class='fas fa-coins hvr-icon'></i></strong>";
		
	}else{
		$sectionClass = "bg-info";
		$creditsLabel = "<strong class='badge badge-pill badge-dark mb-2'>Crédits : 0 <i class='fas fa-coins hvr-icon'></i></strong>";
	}

	$section="
	<section id='creditsSidebar' class='row bg-info p-2 m-0 align-items-center text-center border-bottom border-white'>
		<div class='col-12'>
			<p class='m-0 font-weight-bold text-white'>{$creditsLabel}<br>1 <i class='fas fa-coins hvr-icon'></i> = 1 sac collecté</p>
			<button data-edit='credit' data-toggle='modal' data-target='#editModal' class='btn btn-primary hvr-icon-push mt-3'><i class='fas fa-coins hvr-icon'></i> Créditer mon compte</button>
		</div>
	</section>";
	
	return $section;
	
}


/* PICK *********************************************/

/* pickProgBtn */
function pickProgBtn($calID, $class){
	
	$btn = "<span class='btn {$class}' type='button' style='margin-bottom:-22px;' ><i class='fas fa-bicycle hvr-icon'></i> Programmer ma collecte</span>";
	
	return $btn;
	
}

/* pickProgSection */
function pickProgSection($calID, $sidebar){
	
	global $connexion;
	
	if($calID){
		
		$cal_rq = "SELECT cal.id, cal.date FROM cal WHERE cal.id={$calID}";
		$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error($connexion));
		$cal = mysqli_fetch_assoc($cal_rs);	

		
		$pickDay = convertDate($cal['date'], "2A");
		$pickDate = convertDate($cal['date'], "2dB");
		

	}
	
	$btn = pickProgBtn($calID, "btn-lg btn-primary");
	
	if($calID){	
		$content = "<strong class='text-primary text-capitalize'>{$pickDay}</strong><br><strong class='text-capitalize'>{$pickDate}</strong>";
	}else{
		$content = "<strong class='text-primary'>Aucune collecte<br>programmée</strong>";
	}
	
	$section = "
	<div class='row'>
	<button id='pickSection' style='width:100%;' class='btn-tada pt-4 text-center bg-white border-0 border-bottom border-light' data-edit='pick' data-rq='action=create&calID={$calID}' data-toggle='modal' data-target='#editModal'>
		<div class='mb-3'>
			{$content}
		</div>
		{$btn}
	</button>
	</div>";

	
	return $section;
}

/* pickInfosBtn */
function pickInfosBtn($pickID, $pickType){
	
	global $connexion;
	
	switch($pickType){
		case "pick":
			$pick_rq = "
			SELECT cal.date FROM picks
			INNER JOIN cal ON cal.id = picks.calID
			WHERE picks.id = {$pickID}";

		break;
		case "bundle":
			$pick_rq = "
			SELECT cal.date FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			INNER JOIN cal ON cal.id = picks.calID
			WHERE bundles.id = {$pickID}";

			
		break;
	}
	$pick_rs = mysqli_query($connexion, $pick_rq) or die();
	$pick = mysqli_fetch_assoc($pick_rs);
	
	if(date("Y-m-d")<$pick['date']){
		$btn = "
		<button data-edit='pick' data-rq='action=update&pickID={$pickID}&pickType={$pickType}' data-toggle='modal' data-target='#editModal' class='btn btn-primary' type='button'><i class='fas fa-bicycle hvr-icon'></i> Modifier</button>";
	}

	$btn .= "
	<button data-edit='pick' data-rq='action=delete&pickID={$pickID}&pickType={$pickType}' data-toggle='modal' data-target='#editModal' class='btn btn-danger' type='button'><i class='fas fa-ban'></i> Annuler</button>";
	
	return $btn;
}

/* pickInfosSection */
function pickInfosSection($userID, $pickID, $pickType, $sidebar){

	global $connexion;
	
	switch($pickType){
		case "pick":
			$pick_rq = "
			SELECT picks.id, picks.sacs, slots.start, slots.end, cal.date FROM picks
			INNER JOIN slots ON slots.id = picks.slotID
			INNER JOIN cal ON cal.id = picks.calID
			WHERE picks.id = {$pickID}";

		break;
		case "bundle":
			$pick_rq = "
			SELECT bundles.id, bundles.sacs, slots.start, slots.end, cal.date FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			INNER JOIN slots ON slots.id = picks.slotID
			INNER JOIN cal ON cal.id = picks.calID
			WHERE bundles.id = {$pickID}";

			
		break;
	}
	$pick_rs = mysqli_query($connexion, $pick_rq) or die();
	$pick = mysqli_fetch_assoc($pick_rs);
	
	if($pick['date']==date('Y-m-d')){
		$pickDate = "Aujourd'hui";
	}else{
		$pickDate = convertDate($pick['date'],"2adB");
	}
	

	if($pick['sacs']){
		$pickSacs = $pick['sacs'];
	}else{
		$userSacs = userSacs($userID);
		$pickSacs = "plus de ".$userSacs;
	}

	$btn = pickInfosBtn($pickID, $pickType);
	$btnGrp="
	<div class='btn-group' style='margin-bottom:-22px;' >
		{$btn}
	</div>";
	
	$section="
	<section id='pickSection' class='row align-items-center pt-4 text-center bg-white border-bottom border-light'>
		<div class='col-12'>
		
			<div class='mb-3'>
				<strong class='text-primary text-capitalize text-nowrap'>{$pickDate}</strong><br/>
				<span style='font-size:2rem;'>entre {$pick['start']} et {$pick['end']}</span>
			</div>
			
			{$btnGrp}
			
		</div>
	</section>";	

	return $section;

}

/* pickSection*/
function pickSection($userID, $sidebar){
	
	global $connexion;
	
	$pickNext = nextPick($userID);

	if($pickNext["type"]=="cal"){	
		$title = "Prochaine collecte";
		$content = pickProgSection($pickNext["id"], $sidebar);			
	}else{
		$title = "Collecte programmée";
		$content = pickInfosSection($userID, $pickNext["id"], $pickNext["type"], $sidebar);
	}
		
	$section = "
	<h2>{$title}</h2>
	{$content}";
	
	return $section;
	
}


/* ORDERS  *****************************************/

// ordersTable 
function ordersTable($userID){
	
	global $connexion;	

	$orders_rq = "
	SELECT transactions.dateCreation AS tDate, orders.tID, orders.id AS orderID, orders.montant, orders.reglement, orders.remise, orders.userID, orders.pro FROM orders
	INNER JOIN transactions ON orders.tID = transactions.id
	WHERE orders.tID!=0 AND orders.userID={$userID} 
	ORDER BY transactions.dateCreation DESC";	
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	
	if(mysqli_num_rows($orders_rs)){
	
		while($orders=mysqli_fetch_array($orders_rs)){
					
			$dCell = convertDate($orders['tDate']);	
			$montantCell = formatPrice($orders['montant']);				

			$ref = orderRef($orders['orderID']);
			$rCell = "<button data-edit='orders' data-rq='orderID={$orders['orderID']}&action=detail' data-toggle='modal' data-target='#editModal' class='btn btn-link p-1' type='button'>{$ref}</button>";
				
			$tbody .= " 		
			<tr>
				<td class='align-middle'>{$dCell}</td>
				<td class='align-middle'>{$rCell}</td>
				<td class='align-middle text-right font-weight-bold bg-light'>{$montantCell}</td>
			</tr>";			
		}		

		$table = "
		<table class='table table-sm mb-0'>
		<thead class='bg-secondary'>
			<tr>
				<th scope='col' style='width:90px'>Date</th>
				<th scope='col' style=''>Ref.</th>
				<th scope='col' class='text-right' style='width:90px'>Mt.</th>
			</tr>
		</thead>
		<tbody>
			{$tbody}
		</tbody>	
		</table>";
		
		return $table;
		
	}
}

// ordersSection 
function ordersSection($userID){
	
	global $connexion;	

	$orders_rq = "SELECT orders.id FROM orders WHERE orders.tID!=0 AND orders.userID={$userID}";	
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	
	if(mysqli_num_rows($orders_rs)){
	
		$ordersTable = ordersTable($userID);
		
		return "
		<h3>
			<button class='btn btn-link btn-block text-left hvr-icon-push p-0 m-0 collapsed h3' type='button' data-toggle='collapse' data-target='#ordersSection'><i class='fa hvr-icon text-muted' aria-hidden='true'></i> Mes Factures</button>
		</h3>
		<section id='ordersSection' class='collapse' >
			{$ordersTable}
		</section>
		<hr>";
	
	}
	
}


// ordersList
function ordersList($userID){
	
	global $connexion;	

	$orders_rq = "
	SELECT transactions.dateCreation AS tDate, orders.tID, orders.id AS orderID, orders.montant, orders.reglement, orders.remise, orders.userID, orders.pro FROM orders
	INNER JOIN transactions ON orders.tID = transactions.id
	WHERE orders.tID!=0 AND orders.userID={$userID} 
	ORDER BY transactions.dateCreation DESC";	
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	
	if(mysqli_num_rows($orders_rs)){
	
		while($orders=mysqli_fetch_array($orders_rs)){
					
			$dCell = convertDate($orders['tDate']);	
			$montantCell = formatPrice($orders['montant']);				

			$ref = orderRef($orders['orderID']);
			$rCell = "<button data-edit='orders' data-rq='orderID={$orders['orderID']}&action=detail' data-toggle='modal' data-target='#editModal' type='button'><i class ='fa'></i> {$dCell} &bull; {$ref} &bull; {$montantCell}</button>";
				
			$list .= "
			<li class='nav-item'>
				{$rCell}
			</li>";
			
			
		}		


		
		return $list;
		
	}
}

// ordersSidebar
function ordersSidebar($userID){
	
	global $connexion;	

	$orders_rq = "SELECT orders.id FROM orders WHERE orders.tID!=0 AND orders.userID={$userID}";	
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	
	if(mysqli_num_rows($orders_rs)){
	
		$ordersList = ordersList($userID);
		
		return "
		<button class='btn btn-block btn-primary border-bottom border-dark rounded-0 hvr-icon-push text-uppercase text-left px-3 py-2 m-0 collapsed' type='button' data-toggle='collapse' data-target='#ordersSidebar'><i class='fa hvr-icon' aria-hidden='true'></i> Mes factures</button>
		<div id='ordersSidebar' class='collapse' data-parent='#sidebar'>
			<ul class='nav flex-column'>
			{$ordersList}
			</ul>
		</div>";
	
	}
	
}


/* PICKS *********************************************/

// picksList
function picksList($userID){
	
	global $connexion;	

	$currentDate = date('Y-m-d');

	// PICKS REQUEST
	$picks_rq = "
	SELECT 'pick' AS type, picks.id, cal.date, picks.sacs AS sacsProg, picks.collectID, collects.hour, collects.sacs AS sacsCol FROM picks	
	INNER JOIN cal ON picks.calID = cal.id
	LEFT JOIN collects ON collects.id = picks.collectID
	WHERE  picks.userID={$userID} AND picks.valid=1
	UNION
	SELECT 'bundle' AS type, bundles.id, cal.date, bundles.sacs AS sacsProg, bundles.collectID, collects.hour, collects.sacs AS sacsCol FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN cal ON picks.calID = cal.id
	LEFT JOIN collects ON collects.id = bundles.collectID
	WHERE bundles.userID={$userID}
	ORDER BY date DESC";

	$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($picks_rs)){
		
		$picksNb = mysqli_num_rows($picks_rs);
	
		while($picks = mysqli_fetch_assoc($picks_rs)){
					
			$dateCell = convertDate($picks['date']);
			$refCell = pickRef($picks['id'], $picks['type']);	

		if($picks['collectID']){
			$sacsCell = $picks['sacsCol'];
		}else{
			$sacsCell =  $picks['sacsProg'];
		}			

			$rCell = "<button  data-edit='pick' data-rq='pickID={$picks['id']}&pickType={$picks['type']}' data-toggle='modal' data-target='#editModal' type='button'><i class ='fa'></i> {$dateCell} &bull; {$refCell} &bull; {$sacsCell} sac(s)</button>";
				
			$list .= "
			<li class='nav-item'>
				{$rCell}
			</li>";

		}		
		
		return $list;
		
	}
}


// picksSidebar
function picksSidebar($userID){
	
	global $connexion;	

	$currentDate = date('Y-m-d');

	$picks_rq = "
	SELECT picks.id FROM picks
	INNER JOIN cal ON picks.calID = cal.id
	WHERE picks.userID={$userID} AND picks.valid=1
	UNION
	SELECT bundles.id FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN cal ON picks.calID = cal.id
	WHERE bundles.userID={$userID}";
	$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
	$picks_nb = mysqli_num_rows($picks_rs);

	if($picks_nb){
	
		$picksList = picksList($userID);
		
		return "
		<button class='btn btn-block btn-primary border-bottom border-dark rounded-0 hvr-icon-push text-uppercase text-left px-3 py-2 m-0 collapsed' type='button' data-toggle='collapse' data-target='#picksSidebar'><i class='fa hvr-icon' aria-hidden='true'></i> Mes collectes</button>
		<div id='picksSidebar' class='collapse' data-parent='#sidebar'>
			<ul class='nav flex-column'>
			{$picksList}
			</ul>
		</div>";
	
	}
	
}

/* picksTable */
function picksTable($userID){
	
	global $connexion;
	
	$currentDate = date('Y-m-d');

	// PICKS REQUEST
	$picks_rq = "
	SELECT 'pick' AS type, picks.id, cal.date, picks.collectID, collects.hour, collects.sacs AS sacsCol FROM picks	
	INNER JOIN cal ON picks.calID = cal.id
	LEFT JOIN collects ON collects.id = picks.collectID
	WHERE  picks.userID={$userID} AND picks.valid=1
	UNION
	SELECT 'bundle' AS type, bundles.id, cal.date, bundles.collectID, collects.hour, collects.sacs AS sacsCol FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN cal ON picks.calID = cal.id
	LEFT JOIN collects ON collects.id = bundles.collectID
	WHERE  bundles.userID={$userID}
	ORDER BY date DESC";

	$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
	if(mysqli_num_rows($picks_rs)){
		
		$picksNb = mysqli_num_rows($picks_rs);
	
		while($picks = mysqli_fetch_assoc($picks_rs)){
			
			$dateCell = convertDate($picks['date']);
			$refCell = pickRef($picks['id'], $picks['type']);
			
			$trClass = "table-default";
			$sPrgCell = "";
			$sDelCell = "";
			$sColCell = "";
			$sOrdCell = "";
			
			if($picks['collectID']){
				
				$sColTotal += $picks['sacsCol'];
				$trClass = "table-success";
				$sColCell = $picks['sacsCol'];
				$hourCell = $picks['hour'];
				
				
				$sCmd_rq = "
				SELECT SUM(sacs.nb) AS nb FROM sacs 
				INNER JOIN orders ON orders.id = sacs.orderID
				WHERE orders.userID={$userID} AND orders.tID!=0 AND sacs.collectID={$picks['collectID']}";
				$sCmd_rs = mysqli_query($connexion, $sCmd_rq) or die(mysqli_error($connexion));
				if(mysqli_num_rows($sCmd_rs)){
					$sCmd = mysqli_fetch_assoc($sCmd_rs);
					if($sCmd['nb']){	
						$sDelCell = $sCmd['nb'];
						$sDelTotal += $sCmd['nb'];
					}else{
						$sDelCell = "-";
					}
				}
		
			}else{

				if($picks['date']<date('Y-m-d')){
					
					$trClass = "table-danger";
					$sColCell = "-";
					$sDelCell = "-";
					$sPrgCell = "";
					$sOrdCell = "";
					
					$miss_rq = "
					SELECT * FROM miss WHERE pickID={$picks['id']} AND pickType='{$picks['type']}'";
					$miss_rs = mysqli_query($connexion, $miss_rq) or die(mysqli_error($connexion));
					if(mysqli_num_rows($miss_rs)){
						$trClass = "table-warning";
					}
				}
			}

			$tbody .="
			<tr class={$trClass}>
				<td class='align-middle'>{$dateCell}</td>
				<td class='align-middle'><button data-edit='pick' data-rq='pickID={$picks['id']}&pickType={$picks['type']}' data-toggle='modal' data-target='#editModal' class='hvr-icon-push btn btn-link p-0'>{$refCell}</button></td>
				<td class='text-center bg-light font-weight-bold'>{$sColCell}</td>
				<td class='text-center bg-light font-weight-bold'>{$sDelCell}</td>
			</tr>";
		}	
		
		$sColTotalCell = "";
		$sDelTotalCell = "";
		
		if($sColTotal){
			$sColTotalCell = $sColTotal;
		}
		if($sDelTotal){
			$sDelTotalCell = $sDelTotal;
		}
		
		$table ="
		<div class='mb-4 table-responsive-md'>
			<table class='table table-sm mb-2 table-hover'>
			<thead>
				<tr class='bg-secondary'>
					<th width='90'>Date</th>
					<th>Ref.</th>
					<th width='0' class='text-center'><i class='fa fa-shopping-bag' style='font-size:75%;'></i> Col</th>
					<th width='0' class='text-center'><i class='fa fa-shopping-bag' style='font-size:75%;'></i> Cmd</th>
				</tr>
			</thead>
			<tbody>
				{$tbody}
			</tbody>
			<tfoot>
				<tr>
					<th class='text-right bg-secondary' colspan='2'>Total</th>
					<td class='text-center font-weight-bold bg-dark'>{$sColTotalCell}</td>
					<td class='text-center font-weight-bold bg-dark'>{$sDelTotalCell}</td>
				</tr>
			</tfoot>
			</table>
		</div>";

		return $table;
	}
}

/* picksSection */
function picksSection($userID){
	
	global $connexion;
	
	$currentDate = date('Y-m-d');

	$picks_rq = "
	SELECT picks.id FROM picks
	INNER JOIN cal ON picks.calID = cal.id
	WHERE (picks.collectID!=0 OR cal.date <'{$currentDate}') AND picks.userID={$userID} AND picks.valid=1
	UNION
	SELECT bundles.id FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN cal ON picks.calID = cal.id
	WHERE (bundles.collectID!=0 OR cal.date < '{$currentDate}') AND bundles.userID={$userID}";
	$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
	$picks_nb = mysqli_num_rows($picks_rs);

	if($picks_nb){
		$section = picksTable($userID);
		$badge = "<span class='badge badge-primary'>{$picks_nb}</span>";
		
		return "
		<h3 class='d-flex align-items-center justify-content-between'>
			<button class='btn btn-link btn-block text-left hvr-icon-push p-0 m-0 h3' type='button' data-toggle='collapse' data-target='#picksSection'><i class='fa hvr-icon text-muted' aria-hidden='true'></i> Collectes cloturées {$badge}</button>
		</h3>
		<section id='picksSection' class='collapse show'>
			{$section}
		</section>";
	}
	

}

/* INFOS *********************************************/

/* infosContact */
function infosContact($userID){
	
	global $connexion;
	
	// USER INFOS
	$user_rq = "SELECT nom, prenom, societe, email, tel FROM users WHERE id=".$userID;
	$user_rs = mysqli_query($connexion, $user_rq) or die(mysqli_error());
	$user = mysqli_fetch_assoc($user_rs);
	
	$nom_cell = ucwords($user['prenom']." ".$user['nom']);
	
	if(!empty($user['societe'])){
		$societe = ucwords($user['societe']);
		$section .= "<small>Société :</small> <strong class='text-uppercase'>{$societe}</strong><br>";
	}

	$section .= "<small>Nom :</small> <strong>{$nom_cell}</strong><br><small>Email :</small> <strong>{$user['email']}</strong><br><small>Tél. :</small> <strong>{$user['tel']}</strong>";
	
	return $section;

}	


/* infosContact */
function infosUser($userID){
	
	global $connexion;
	
	// USER INFOS
	$user_rq = "SELECT nom, prenom, societe, email, tel FROM users WHERE id=".$userID;
	$user_rs = mysqli_query($connexion, $user_rq) or die(mysqli_error());
	$user = mysqli_fetch_assoc($user_rs);
	
	$nom_cell = ucwords($user['prenom']." ".$user['nom']);
	
	$userAdresse = userAdresse($userID);
	
	$section .= "Adresse<br>";
	
	if(!empty($user['societe'])){
		$societe = ucwords($user['societe']);
		$section .= "<strong class='text-uppercase'>{$societe}</strong><br>";
	}else{
		$section .= "<strong class='text-uppercase'>{$nom_cell}</strong><br>";
	}
	$section .= "<strong class='text-uppercase'>".$userAdresse."</strong><br>";
	
	$section .= "Contact<br>";
	
	if(!empty($user['societe'])){
		$section .= "<strong class='text-uppercase'>{$nom_cell}</strong><br>";
	}
	$section .= "<strong>{$user['email']}</strong><br><strong>{$user['tel']}</strong>";
	
	return $section;

}	



/* infosSection */
function infosSection($userID){
	
	return "
	<h3>
		<button class='btn btn-link btn-block text-left hvr-icon-push p-0 m-0 collapsed h3' type='button' data-toggle='collapse' data-target='#userInfos'><i class='fa hvr-icon text-muted' aria-hidden='true'></i> Mes Infos</button>
	</h3>
	<section id='userInfos' class='collapse' >
		<div class='row'>
			<div class='col-lg-12 col-sm-6 col-12 mb-3'>
				<h4 class='text-muted'>Contact</h4>
				<p class='mb-1'>".infosContact($userID)."</p>
				<button data-edit='infos' data-rq='action=contact' data-toggle='modal' data-target='#editModal' class='btn btn-link btn-sm p-0 hvr-icon-push'><i class='fas fa-edit hvr-icon'></i> Modifier</button>
			</div>
			<div class='col-lg-12 col-sm-6 col-12 mb-1'>
				<h4 class='text-muted'>Adresse</h4>
				<p class='mb-1'><strong class='text-uppercase'>".userAdresse($userID)."</strong></p>
				<button data-edit='infos' data-rq='action=adresse' data-toggle='modal' data-target='#editModal' class='btn btn-link btn-sm p-0 hvr-icon-push'><i class='fas fa-edit hvr-icon'></i> Modifier</button>
			</div>	
		</div>
		<hr>
		<div class='row'>
			<div class='col-12'>
				<button data-edit='infos' data-rq='action=pwd' data-toggle='modal' data-target='#editModal' class='btn btn-link btn-sm p-0 hvr-icon-push'><i class='fas fa-edit hvr-icon'></i> Modifier mon mot de passe</button>
			</div>
		</div>
	</section>
	<hr>";
	
}


/* infosSidebar */
function infosSidebar($userID){
	
	$section = "
	<button class='btn btn-block btn-primary border-bottom border-dark rounded-0 hvr-icon-push text-uppercase text-left px-3 py-2 m-0 collapsed' type='button' data-toggle='collapse' data-target='#infosSidebar'><i class='fa hvr-icon' aria-hidden='true'></i> Mes infos</button>
	<div id='infosSidebar' class='collapse' data-parent='#sidebar'>
		
		<div style='padding:.75rem'>".infosUser($userID)."</div>
	
		<ul class='nav flex-column'>
			<li class='nav-item'>
				<button data-edit='infos' data-rq='action=update' data-toggle='modal' data-target='#editModal'><i class ='fa'></i> Modifier mes infos</button>
			</li>
			<li class='nav-item'>
				<button data-edit='infos' data-rq='action=updatepwd' data-toggle='modal' data-target='#editModal'><i class ='fa'></i> Modifier mon mot de passe</button>
			</li>	
		</ul>
		
	</div>";
	
	return $section;
}


/* ASSOS *********************************************/

/* assosCard */
function assosCard($userID, $statut){

	global $connexion;
	
	switch($statut){
		
		// ACCEPTED ASSOS REQUEST
		case "ok":
			$cardHeader = "Demandes acceptées";
			
			$assos_rq = "
			SELECT assos.id, assos.dateCreation, assos.dateValid, assos.partnerID AS partnerID, 'out' AS statut, users.nom, users.prenom
			FROM assos
			INNER JOIN users ON users.ID = assos.partnerID
			WHERE assos.userID={$userID} AND dateValid IS NOT NULL
			UNION
			SELECT assos.id, assos.dateCreation, assos.dateValid, assos.userID AS partnerID, 'in' AS statut, users.nom, users.prenom
			FROM assos
			INNER JOIN users ON users.ID = assos.userID
			WHERE assos.partnerID={$userID} AND dateValid IS NOT NULL";
			
			// SPONSORS REQUEST
			$sponsors_rq = "
			SELECT sponsors.id FROM sponsors
			INNER JOIN users ON users.email = sponsors.email AND users.valid=1
			WHERE sponsors.userID={$userID}";
			$sponsors_rs = mysqli_query($connexion, $sponsors_rq) or die();
			$sponsors_nb = mysqli_num_rows($sponsors_rs);
			if($sponsors_nb){
				$listItem.="
				<li class='list-group-item list-group-item-dark rounded-0'>
					<i class='fas fa-star'></i> {$sponsors_nb} parrainage(s) validés
				</li>";
			}					
		break;

		case "in":
			$cardHeader = "Demandes reçues";
		
			$assos_rq = "
			SELECT assos.id, users.nom, users.prenom
			FROM assos
			INNER JOIN users ON users.ID = assos.userID
			WHERE assos.partnerID={$userID} AND dateValid IS NULL";

		break;
		
		case "out":
			$cardHeader = "Demandes envoyées";
		
			$assos_rq = "
			SELECT assos.id, users.email
			FROM assos
			INNER JOIN users ON users.ID = assos.partnerID
			WHERE assos.userID={$userID} AND dateValid IS NULL
			UNION
			SELECT sponsors.id, sponsors.email
			FROM sponsors
			WHERE sponsors.userID={$userID} AND NOT EXISTS ( SELECT id FROM users WHERE email=sponsors.email)";
			
		break;

	}

	$assos_rs = mysqli_query($connexion, $assos_rq) or die();
	$assos_nb = mysqli_num_rows($assos_rs);
	if($assos_nb){
		// BADGE
		$badge = $assos_nb;			
		while($assos = mysqli_fetch_array($assos_rs)){
			
			if($statut=="in"){												
				$btn = "
				<a href='edit/assos.edit.php?assoID={$assos['id']}&action=refused' class='btn btn-link btn-sm ml-1 p-0 float-right' ><i class='fas fa-times text-danger'></i></a>
				<a href='edit/assos.edit.php?assoID={$assos['id']}&action=accept' class='btn btn-link btn-sm ml-1 p-0 float-right'><i class='fas fa-check text-success'></i></a>";							
			}

			if($statut=="ok"){									
				$btn = "<a href='edit/assos.edit.php?assoID={$assos['id']}&action=cancel&statut={$assos['statut']}' class='btn btn-link btn-sm ml-1 p-0 float-right'><i class='fas fa-times text-danger'></i></a>";						
			}

			// PARTNER FULL NAME
			if($statut=="out"){
				$partner = $assos['email'];
				
			}else{
				$partner = $assos['prenom']." ".$assos['nom'];
				$itemClass = 'text-capitalize';
			}
			
			// LIST ITEM
			$listItem.="
			<li class='list-group-item bg-white {$itemClass} rounded-0'>
				{$partner} {$btn}
			</li>";
		}
		
		
	}

	
	if($assos_nb){
		
		if($statut == "in"){
			
			$card_class = "show";
		}else{
			$card_class = "collapse";
			$btn_class = "collapsed";
		}

		// CARD
		$card = "
		<div class='card'>
			<div class='card-header bg-secondary'>
				<h4 class='m-0 d-flex justify-content-between align-items-center'>
					<button class='btn btn-link btn-block text-left hvr-icon-push  p-0 text-uppercase {$btn_class}' type='button' data-toggle='collapse' data-target='#{$statut}Assos'><i class='fa hvr-icon' aria-hidden='true'></i> {$cardHeader}</button>
					<span class='badge badge-pill '>{$badge}</span>
				</h4>
			</div>
			<div id='{$statut}Assos' class='{$card_class}' data-parent='#accordionAssos'>
				<div class='card-body p-0'>
					<ul class='list-group m-0'>
						{$listItem}
					</ul>
				</div>
			</div>
		</div>";
		
		// RETURN
		return $card;	
	}
}

/* assosSection */
function assosSection($userID){

	global $connexion;

	$assos_rq = "
	SELECT assos.id FROM assos
	INNER JOIN users ON users.ID = assos.partnerID
	WHERE assos.userID={$userID} 
	UNION
	SELECT assos.id FROM assos
	INNER JOIN users ON users.ID = assos.userID
	WHERE assos.partnerID={$userID}";	
	$assos_rs = mysqli_query($connexion, $assos_rq) or die(mysqli_error($connexion));
	
	if(!mysqli_num_rows($assos_rs)){
		
		$content = "<p><strong>Invitez vos voisins afin de grouper vos collectes et parrainez vos amis pour gagner des crédits.</strong></p>
		<!--
		<p>La personne sera informée par e-mail que vous souhaitez l'ajouter comme ami :</p>
		<p>&bull; <strong>Si elle est déjà abonnée,</strong> vous pourrez grouper vos collectes dès que la demande aura été acceptée.</p>
		<p>&bull; <strong>Si elle n'est pas encore abonnée,</strong> une demande de parrainage lui sera envoyée (vous pourrez également grouper vos collectes).</p>
		-->";
		
		$section_class = "show";
		
		
	}else{
		
		$assosOk_card = assosCard($userID, 'ok');
		$assosIn_card = assosCard($userID, 'in');
		$assosOut_card = assosCard($userID, 'out');
		
		if($assosIn_card){
			$section_class = "show";
		}else{
			$section_class = "collapse";
			$btn_class = "collapsed";
		}
		
		
		$content = "
		<div class='accordion mb-2' id='accordionAssos'>
			{$assosIn_card}
			{$assosOut_card}
			{$assosOk_card}
		</div>";
	}
	
	
	$section = "
	<button id='assoSection' class='text-center bg-info rounded border-0 btn-tada' style='width:100%; margin-bottom:100px;' data-edit='assos' data-toggle='modal' data-target='#editModal'>
		<img src='https://assets.pic-verre.fr/img/credit-pic-verre.png' style='height:120px; margin-top:-60px;'>
		<p class=' font-weight-bold py-3 m-0'>Parrainez vos amis :<br><strong>pour chaque parrainage validé<br>vous gagnez un crédit !</strong></p>
		<span class='btn btn-primary hvr-icon-push' style='margin-bottom:-20px;'><i class='fas fa-user-plus hvr-icon'></i> Parrainer un ami</span>
	</button>";

	return $section;
}

/* assosSbSection */
function assosSbSection($userID){
	
	global $connexion;
	
	$assosPendingIn = "
	SELECT assos.id FROM assos
	WHERE assos.partnerID={$userID} AND assos.dateValid IS NULL";		
	$assosPending_rq = "{$assosPendingIn}";
	$assosPending_rs = mysqli_query($connexion, $assosPending_rq) or die();
	$assosPending_nb = mysqli_num_rows($assosPending_rs);
	if($assosPending_nb){
		
		$btn_badge = "<span class='float-right'><i class='fas fa-exclamation-circle'></i></span>";

		$navItem = "
		<li class='nav-item'>
			<button href='#pendingAssos' data-toggle='collapse' data-target='#pendingAssos'><i class ='fa'></i> Demande(s) reçue(s) <span class='badge badge-pill float-right badge-warning'>{$assosPending_nb}</span></button>
		</li>";
	}
	
	$section = "
	<button class='btn btn-block btn-primary border-bottom border-dark rounded-0 hvr-icon-push text-uppercase text-left px-3 py-2 m-0 collapsed' type='button' data-toggle='collapse' data-target='#assosSidebar'><i class='fa hvr-icon' aria-hidden='true'></i> Mes ami(e)s {$btn_badge}</button>
	<div id='assosSidebar' class='collapse' data-parent='#sidebar'>
		<ul class='nav flex-column'>
			{$navItem}
			<li class='nav-item'>
				<button data-edit='assos' data-toggle='modal' data-target='#editModal'><i class ='fa'></i> Envoyer une demande</button>
			</li>
		</ul>
	</div>";
	
	return $section;
	
}


/* BUNDLES *******************************************/

/* bundleCard */
function bundleCard($userID, $pickID){
	
	global $connexion;
	
	$pick_rq = "
	SELECT picks.id, picks.sacs, slots.start, slots.end, cal.date, users.nom, users.prenom
	FROM picks
	INNER JOIN users ON users.id = picks.userID
	INNER JOIN slots ON slots.id = picks.slotID
	INNER JOIN cal ON cal.id = picks.calID
	WHERE picks.id = {$pickID}";
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$dateCell = ucwords(convertDate($pick['date'],"2adb"));
	$slotCell = "entre <strong>{$pick['start']}</strong> et <strong>{$pick['end']}</strong>";
	
	$pickNext = nextPick($userID);
	
	if($pickNext["type"]=="cal"){

		$btnGroup="
		<button data-edit='pick' data-rq='action=create&pickID={$pickID}&pickType=bundle' data-toggle='modal' data-target='#editModal' class='btn btn-primary btn-sm btn-block'><i class='fas fa-users'></i> Grouper</button>";
		
	}
		
	$card = "
	<div class='card bg-white'>
		<div class='card-header bg-secondary'>
			<h4 class='m-0 d-flex justify-content-between align-items-center'>
				<button class='btn btn-link btn-block text-left hvr-icon-push p-0 text-uppercase' type='button' data-toggle='collapse' data-target='#bundle{$pickID}'><i class='fa hvr-icon' aria-hidden='true'></i> {$pick['prenom']} {$pick['nom']}</button>
			</h4>
		</div>
		<div id='bundle{$pickID}' class='show' data-parent='#accordionBundles'>
			<div class='card-body p-2'>
				<div class='row align-items-center'>
					<div class='col-7'>
						<strong>{$dateCell}</strong><br/>
						{$slotCell}
					</div>
					<div class='col-5'>
						{$btnGroup}
					</div>
				</div>
			</div>
		</div>
	</div>";

	return $card;

}

/* bundlesSection */
function bundlesSection($userID){
	
	global $connexion;
	
	$dateLimit = date('Y-m-d', strtotime("+2 days"));
	
	$assos_rq = "
	SELECT picks.id FROM assos
	INNER JOIN picks ON picks.userID = assos.partnerID AND picks.bundle=1
	INNER JOIN cal ON cal.id = picks.calID AND cal.date>'{$dateLimit}'
	WHERE assos.dateValid IS NOT NULL AND assos.userID={$userID}
	UNION 
	SELECT picks.id FROM assos
	INNER JOIN picks ON picks.userID = assos.userID AND picks.bundle=1
	INNER JOIN cal ON cal.id = picks.calID AND cal.date>'{$dateLimit}'
	WHERE assos.dateValid IS NOT NULL AND assos.partnerID={$userID}";
	$assos_rs = mysqli_query($connexion, $assos_rq) or die();
	$content = "";
	$assos_nb = mysqli_num_rows($assos_rs);
	if($assos_nb){
		
		$badge = "<span class='badge badge-pill badge-primary'>{$assos_nb}</span>";
		
		while($assos = mysqli_fetch_array($assos_rs)){
			$content .= bundleCard($userID, $assos["id"]);
		}
		
		$section = "
		<h3 class='d-flex align-items-center justify-content-between'>
			<button class='btn btn-link btn-block text-left hvr-icon-push p-0 m-0 h3' type='button' data-toggle='collapse' data-target='#bundlesSection'><i class='fa hvr-icon text-muted' aria-hidden='true'></i> Collectes groupées</button> 			
			{$badge}
		</h3>
		<section class='collapse show' id='bundlesSection'>
			<div id='accordionBundles' class='accordion mb-4'>
				{$content}
			</div>
		</section>
		<hr>";
		
		return $section;
		
		
		
	}

}

/* bundlesSbSection */
function bundlesSbSection($userID){

	global $connexion;
	
	$dateLimit = date('Y-m-d', strtotime("+2 days"));
	
	$assos_rq = "
	SELECT picks.id
	FROM assos
	INNER JOIN picks ON picks.userID = assos.partnerID AND picks.bundle=1
	INNER JOIN cal ON cal.id = picks.calID AND cal.date>'{$dateLimit}'
	WHERE assos.dateValid IS NOT NULL AND assos.userID={$userID}
	UNION 
	SELECT picks.id
	FROM assos
	INNER JOIN picks ON picks.userID = assos.userID AND picks.bundle=1
	INNER JOIN cal ON cal.id = picks.calID AND cal.date>'{$dateLimit}'
	WHERE assos.dateValid IS NOT NULL AND assos.partnerID={$userID}";
	$assos_rs = mysqli_query($connexion, $assos_rq) or die();
	$assos_nb = mysqli_num_rows($assos_rs);
	if($assos_nb){
		
		$nav = "";
		while($assos = mysqli_fetch_array($assos_rs)){
			$nav .= bundlesSidebarNav($assos["id"], $userID);
		}
		
		$section = "
		<button class='btn btn-block btn-primary border-bottom border-dark rounded-0 hvr-icon-push text-uppercase text-left px-3 py-2 m-0' type='button' data-toggle='collapse' data-target='#bundlesSidebar'><i class='fa hvr-icon' aria-hidden='true'></i> Collectes groupées <span class='badge badge-pill bg-dark float-right'>{$assos_nb}</span></button>			
		<div id='bundlesSidebar' class='collapse' data-parent='#sidebar'>
			<ul class='nav flex-column'>
				{$nav}
			</ul>
		</div>";
		
		return $section;
	}	
	
}

/* bundlesSidebarNav */
function bundlesSidebarNav($pickID, $userID){
	
	global $connexion;
	
	$pick_rq = "
	SELECT users.nom, users.prenom
	FROM picks
	INNER JOIN users ON users.id = picks.userID
	INNER JOIN slots ON slots.id = picks.slotID
	INNER JOIN cal ON cal.id = picks.calID
	WHERE picks.id = {$pickID}";
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$pickUser = "{$pick['prenom']} {$pick['nom']}";
	
	$navItem = "
	<li class='nav-item'>
		<button data-edit='pick' data-rq='action=create&pickID={$pickID}&pickType=bundle' data-toggle='modal' data-target='#editModal' class='text-capitalize'><i class ='fa'></i> {$pickUser}</button>
	</li>";

	return $navItem;

}

/* SAC */
function sacSection($userID){
	
	if(!userPro($userID)){
	
		if(userActive($userID)){
			$content = "Pour faciliter la collecte et le stockage<br><strong>vous pouvez commander<br>un sac supplémentaire !</strong>";
		}else{
			$content = "Commandez un sac Pic'Verre maintenant<br><strong>nous vous offrons<br>votre première collecte !</strong>";
		}
		
		return "
		<button id='sacsSection' class='text-center bg-info rounded border-0 btn-tada' data-edit='sac' data-toggle='modal' data-target='#editModal' style='width:100%; margin-bottom:100px;'>
			<img src='https://assets.pic-verre.fr/img/sac-pv-poids.png' style='height:120px; margin-top:-60px;'>
			<p class=' font-weight-bold py-3 m-0'>{$content}</p>
			<span class='btn  btn-primary hvr-icon-push' style='margin-bottom:-20px;'><i class='fas fa-shopping-bag hvr-icon'></i> Commander un sac</span>
		</button>";
		
	}
}

?>