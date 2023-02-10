<?php

	/* sidebarNav */
	function sidebarNav($page){
		
		
		switch ($page){
			case "dash":
				$dashClass = " active";
			break;
			case "users":
				$usersClass = " active";
			break;
			case "cal":
				$calClass = " active";
			break;
			case "pickers":
				$pickersClass = " active";
			break;
			case "voies":
				$voiesClass = " active";
			break;
			case "flux":
				$fluxClass = " active";
			break;
			case "devis":
				$devisClass = " active";
			break;
		}
		
		$nav ="
		<div id='sidebar-wrapper' class='position-fixed bg-dark'>
			<div class='sidebar sidebar-nav'>
				<ul class='nav flex-column'>
					<li class='nav-item'><a class='nav-link {$dashClass}' href='index.php'>Tableau de bord</a></li>
					<li class='nav-item'><a class='nav-link {$calClass}' href='calendar.php'>Collectes</a></li>
					<li class='nav-item'><a class='nav-link {$fluxClass}' href='flux.php'>Factures</a></li>
					<li class='nav-item'><a class='nav-link {$usersClass}' href='users.php'>Abonnés</a></li>
					<li class='nav-item'><a class='nav-link {$devisClass}' href='devis.php'>Devis</a></li>
					<li class='nav-item'><a class='nav-link {$voiesClass}' href='voies.php'>Voies</a></li>
				</ul>
			</div>
		</div>";
		
		return $nav;
	}
	
	/* formFooter */
	function formFooter($type){
		
		if(empty($type)){
			$libelle = "* champs obligatoires";
		}else{
			if($type=="pay"){
				$libelle = $GLOBALS['payLibelle'];
			}
			if($type=="nolibelle"){
				$libelle = "";
			}
		}

		$footer = "
		<div class='form-footer form-row clearfix border-top border-light pt-3'>
			<div class='col-md-8 form-group text-left mb-0'>
				<small>{$libelle}</small>
			</div>
			<div class='col-md-4 form-group mb-0'>
				<div class='btn-group float-right' role='group'>
					<button class='btn btn-sm btn-danger hvr-icon-push' data-dismiss='modal' aria-label='Close'><i class='fa hvr-icon'></i> Annuler</button>
					<button class='btn btn-sm btn-secondary hvr-icon-push' type='submit'><i class='fa hvr-icon'></i> Valider</button>
				</div>
			</div>
		</div>";
		
		return $footer;	
	}	
	

	
	/*****************************************************
		DATE
	*****************************************************/

	/* dateBreadcrumb */
	function dateBreadcrumb($period, $date, $page){
		
		$breadcrumb = "
		<nav aria-label='breadcrumb'>
			<ol class='breadcrumb mb-0 fixed-top font-weight-bold rounded-0'>
				<li class='breadcrumb-item'><a href='dashboard'>Tableau de bord</a></li>
				<li class='breadcrumb-item'><a href='{$page}'>".ucfirst($page)."</a></li>";
		
		switch($period){
			case "year":
				$breadcrumb.= "<li class='breadcrumb-item active' aria-current='page'>{$date}</li>";
			break;
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				$breadcrumb.= "
				<li class='breadcrumb-item' aria-current='page'><a href='?period=year&date={$year}'>{$year}</a></li>
				<li class='breadcrumb-item active' aria-current='page'>".ucfirst(convertDate($date, "2BY"))."</li>";
			break;
			case "day":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				$breadcrumb.= "
				<li class='breadcrumb-item' aria-current='page'><a href='?period=year&date={$year}'>{$year}</a></li>
				<li class='breadcrumb-item' aria-current='page'><a href='?period=month&date={$date}'>".ucfirst(convertDate($date, "2BY"))."</a></li>
				<li class='breadcrumb-item active' aria-current='page'>".convertDate($date, "2dbY")."</li>";
			break;
			case "custom":
				$breadcrumb.= "
				<li class='breadcrumb-item active' aria-current='page'>du ".convertDate($date[0], "2dbY")." au ".convertDate($date[1],"2dbY")."</li>";
			break;
		}
		
		$breadcrumb.="
			</ol>
		</nav>";
		
		return $breadcrumb;
		
	}

	/* datePageHeader */
	function datePageHeader($period, $date, $page){
		
		switch($period){
			case "year":
				$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : date("Y");
				$pageTitle = $date;		
			break;		
			case "month":
				$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : date("Y-m");
				$pageTitle = convertDate($date,"2BY");
			break;	
			case "day":
				$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : date("Y-m-d");
				$pageTitle = convertDate($date,"2adbY");
			break;			
			case "custom":
				$dateFrom = (!empty($_REQUEST["dateFrom"])) ? $_REQUEST["dateFrom"] : date("d-m-Y");
				$dateTo = (!empty($_REQUEST["dateTo"])) ? $_REQUEST["dateTo"] : date("d-m-Y");
				$pageTitle = "du {$dateFrom} au {$dateTo}";
				$dateFrom = convertDate($dateFrom,"fr2en");
				$dateTo = convertDate($dateTo,"fr2en");	
				$date = array($dateFrom, $dateTo);	
			break;
		}
		
		$pageHeader = "
		<div class='page-header'>
			<h2>{$page} <em>{$pageTitle}</em></h2>
			<div class='page-nav'>";
		
		if($period!="custom"){ 
			$pageHeader .= dateNav($period, $date);
		}
		$pageHeader .= dateCustomForm($period, $date);
					
		$pageHeader .= "
			</div>
		</div>";
		
		return $pageHeader;
		
	}

	/* dateNav */
	function dateNav($period, $date, $weekend){
		
		$weekend = (!empty($weekend)) ? $weekend : 0;
		
		$dateInterNext = "1";
		$dateInterPrev = "1";
		
		switch($period){
			case 'day':			
				$dateConversion = "2dbY";
				$dateFormat = "Y-m-d";
				// CHANGE INTERVAL
				if($weekend){
					$day_nb = date('N', strtotime($date));
					if($day_nb==1){
						$dateInterPrev = "3";
					}
					if($day_nb==5){
						$dateInterNext = "3";
					}
				}
			break;
			case 'month':
				$dateConversion = "2bY";
				$dateFormat = "Y-m";
			break;

		}
		
		if($period=="year"){
			$prevDate = $prevLink = $date-1;
			$nextDate = $nextLink = $date+1;
		}else{
			$nextDate = date($dateFormat, strtotime('+ '.$dateInterNext." ".$period, strtotime($date)));
			$prevDate = date($dateFormat, strtotime('- '.$dateInterPrev." ".$period, strtotime($date)));
			$prevLink = ucfirst(convertDate($prevDate, $dateConversion));
			$nextLink = ucfirst(convertDate($nextDate, $dateConversion));
		}
		
		$btnGrp = "
		<div class='btn-group btn-group-sm date-nav ' role='group' aria-label=''>
			<a href='?period={$period}&date={$prevDate}' class='btn btn-secondary'>{$prevLink}</a>
			<a href='?period={$period}&date={$nextDate}' class='btn btn-secondary'>{$nextLink}</a>
		</div>";
		
		return $btnGrp;
		
	}

	/* dateCustomForm */
	function dateCustomForm($period, $date){
		
		switch($period){
			case "year":
				$dateFrom = $date."-01-01";
				$dateTo = $date."-12-31";
			break;
			case "month":
				$dateFrom = $date."-01";
				$dateTo = date("t-m-Y", strtotime($date));
			break;
			case "day":
				$dateFrom = $dateTo = $date;
			break;	
			case "custom":
				$dateFrom = $date[0];
				$dateTo = $date[1];
			break;
		}			
		
		$form = "
		<form method='get' class='form-inline date-custom float-right ml-3'>
			<div class='form-group'>
				<label for='dateFrom'>Du</label>
				<input type='text' name='dateFrom' id='dateFrom' class='form-control form-control-sm mx-1 datepicker' value='".convertDate($dateFrom, "en2fr")."'/>
			</div>
			<div class='form-group'>
				<label for='dateTo'>au</label>
				<input type='text' name='dateTo' id='dateTo' class='form-control form-control-sm mx-1 datepicker' value='".convertDate($dateTo,"en2fr")."'/>
			</div>
			<input type='hidden' name='period' value='custom'>
			<button class='btn btn-secondary btn-sm' type='submit'>OK</button>
		</form>";
		
		return $form;
		
	}
	
	
	/*****************************************************
		USERS
	*****************************************************/
	
	/* usersListing */
	function usersListing($state, $secteur){
		
		global $connexion;
		
		$totalCredits = 0;
		
		// USERS LISTING THEAD
		$table = "
		<table class='table table-sm' id='usersTable'>
		<thead>
			<tr>
				<th>Date (en)</th>
				<th>Date</th>
				<th>Nom</th>
				<th class='text-center'>Sct</th>
				<th class='text-center'><i class='fas fa-coins hvr-icon'></i></th>
			</tr>
		</thead>
		<tbody>";
		
		// REQUEST VARIABLES
		if(!empty($secteur) AND $secteur!=0){
			$where_rq .= " AND voies.secteur={$secteur}";
		}

		// USERS ACTIVE REQUEST
		$usersActive_rq="
		SELECT users.*, voies.secteur, 'actifs' AS state
		FROM users
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE EXISTS(
			SELECT orders.id FROM orders
			INNER JOIN abos ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE users.id=orders.userID AND orders.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)) AND users.valid=1 {$where_rq}";
		
		// USERS INACTIVE REQUEST
		$usersInactive_rq="
		SELECT users.*, voies.secteur, 'inactifs' AS state
		FROM users
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE NOT EXISTS ( 
			SELECT orders.id FROM orders
			INNER JOIN abos ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE users.id=orders.userID AND orders.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)) AND users.valid=1 {$where_rq}";
		
		// USERS REQUEST
		switch($state){			
			case "tous":
				$users_rq = "{$usersActive_rq} UNION {$usersInactive_rq}";
			break;
			case "actifs":
				$users_rq = "{$usersActive_rq}";
			break;			
			case "inactifs":
				$users_rq = "{$usersInactive_rq}";
			break;
		}
		$users_rs=mysqli_query($connexion, $users_rq) or die(mysqli_error($connexion));
		while($users=mysqli_fetch_array($users_rs)){
			
			$userLastAbo_rq = "
			SELECT orders.dateCreation, subs.mois FROM abos 
			INNER JOIN orders ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE orders.userID={$users['id']} 
			ORDER BY orders.dateCreation DESC LIMIT 1";
			$userLastAbo_rs=mysqli_query($connexion, $userLastAbo_rq) or die();
			$userLastAbo=mysqli_fetch_array($userLastAbo_rs);
			
			
			//$dateFinCell = date('Y-m-d', strtotime("+{$userLastAbo['mois']} months", strtotime($userLastAbo['dateCreation'])));
			//$dateFinFrCell = convertDate($dateFinCell,"en2fr");
			$dateFrCell = convertDate($users['dateCreation'],"en2fr");
			$creditsCell =  userCredits2($users["id"]);
			$totalCredits += userCredits2($users["id"]);
			
			if($users['state']=="inactifs"){
				$trClass="table-danger";			
			}else{
				if(userCredits2($users["id"])==0){
					$trClass="table-warning";
				}else{
					$trClass="table-default";
				}
			}
			
			
			if(!empty($users['societe'])){
				$user = "{$users['societe']}";
			}else{
				$user = "{$users['nom']} {$users['prenom']}";
			}

			// USERS DETAIL RAW
			$table .= "
			<tr class='{$trClass}'>
				<td>{$users['dateCreation']}</td>
				<td>{$dateFrCell}</td>
				<td><a href='users.php?userID={$users['id']}'>{$user}</a></td>
				<td  class='text-center'>{$users['secteur']}</td>
				<td class='table-light font-weight-bold text-center'>{$creditsCell}</td>
			</tr>";			
		}
		
		// USERS DETAIL TFOOT
		$table .= "
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th class='text-right' colspan='4'>Total</td>
				<td class='text-center'>{$totalCredits}</td>
			</tr>
		</tfoot>
		</table>";
		
		return $table;
		
	}	

	/* usersStats */
	function usersStats($state, $secteur){
		
		global $connexion;
		
		$totalUsers = 0;
		$totalCredits = 0;
		
		// REQUEST VARIABLES
		if($secteur==0){
			$statThCell = "Secteur";
			$group_rq = "voies.secteur";
			
		}else{
			$statThCell = "Voie";
			$group_rq = "voies.id";
			$where_rq = " AND voies.secteur={$secteur}";
		}
		
		// USERS STATS THEAD
		$table = "
		<table class='table table-sm' id='secteur-stats-table'>
		<thead>
			<tr>
				<th>{$statThCell}</th>
				<th>%</th>
				<th>Abos</th>
			</tr>
		</thead>
		<tbody>";		
		
		// USERS ACTIVE REQUEST
		$usersActiveTotal_rq="
		SELECT COUNT(*) as nb, users.id, voies.secteur, voies.voieType, voies.voieLibelle
		FROM users
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE EXISTS(
			SELECT orders.id FROM abos
			INNER JOIN orders ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE users.id=orders.userID AND orders.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)) AND users.valid=1 {$where_rq}";
		$usersActiveTotal_rs=mysqli_query($connexion, $usersActiveTotal_rq) or die();
		$usersActiveTotal=mysqli_fetch_array($usersActiveTotal_rs);
		
		$usersActive_rq="{$usersActiveTotal_rq}	GROUP BY {$group_rq}";
		
		$usersInactiveTotal_rq="
		SELECT COUNT(*) as nb, users.id, voies.secteur, voies.voieType, voies.voieLibelle
		FROM users
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE NOT EXISTS ( 
			SELECT orders.id FROM abos
			INNER JOIN orders ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE users.id=orders.userID AND orders.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)) AND users.valid=1 {$where_rq}";
		$usersInactiveTotal_rs=mysqli_query($connexion, $usersInactiveTotal_rq) or die();
		$usersInactiveTotal=mysqli_fetch_array($usersInactiveTotal_rs);	

		$usersInactive_rq="{$usersInactiveTotal_rq}	GROUP BY {$group_rq}";
		
		// USERS REQUEST
		switch($state){			
			case "tous":
				$users_rq = "{$usersActive_rq} UNION {$usersInactive_rq}";
				$usersTotal = $usersActiveTotal['nb']+$usersInactiveTotal['nb'];
			break;
			case "actifs":
				$users_rq = "{$usersActive_rq}";
				$usersTotal = $usersActiveTotal['nb'];
			break;			
			case "inactifs":
				$users_rq = "{$usersInactive_rq}";
				$usersTotal =  $usersInactiveTotal['nb'];
			break;
		}
		
		$users_rs=mysqli_query($connexion, $users_rq) or die();
		while($users=mysqli_fetch_array($users_rs)){
			
			if($secteur==0){
				$statCell = "<a href=users.php?secteur={$users['secteur']}&state={$state}>Secteur {$users['secteur']}</a>";
			}else{
				$statCell = "{$users['voieType']} {$users['voieLibelle']}";
			}
			
			$ratioCell = round((100*$users["nb"])/$usersTotal,1)."%";
			
			// USERS STATS ROW
			$table .= "
			<tr>
				<td>{$statCell}</td>
				<td>{$ratioCell}</td>
				<td class='table-light font-weight-bold text-center'>{$users['nb']}</td>
			</tr>";
		}
		
		// USERS STATS TFOOT
		$table .= "
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th class='text-right' colspan='2'>Total</td>
				<td class='text-center'>{$usersTotal}</td>
			</tr>
		</tfoot>
		</table>";
	
		return $table;
	}
	
	/* usersCount */
	function usersCount($secteur){
		
		global $connexion;
		
		// REQUEST VARIABLES
		if($secteur!=0){
			$where_rq = " AND voies.secteur={$secteur}";
		}
		
		// USERS ACTIVE REQUEST
		$usersActiveTotal_rq="
		SELECT COUNT(*) as nb
		FROM users
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE EXISTS(
			SELECT orders.id FROM abos
			INNER JOIN orders ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE users.id=orders.userID AND orders.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)) AND users.valid=1 {$where_rq}";
		$usersActiveTotal_rs=mysqli_query($connexion, $usersActiveTotal_rq) or die();
		$usersActiveTotal=mysqli_fetch_array($usersActiveTotal_rs);
		
		// USERS INACTIVE REQUEST
		$usersInactiveTotal_rq="
		SELECT COUNT(*) as nb
		FROM users
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE NOT EXISTS ( 
			SELECT orders.id FROM abos
			INNER JOIN orders ON abos.orderID = orders.id
			INNER JOIN subs ON subs.id = abos.subID
			WHERE users.id=orders.userID AND orders.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)) AND users.valid=1 {$where_rq}";	
		$usersInactiveTotal_rs=mysqli_query($connexion, $usersInactiveTotal_rq) or die(mysqli_query($connexion));
		$usersInactiveTotal=mysqli_fetch_array($usersInactiveTotal_rs);
		
		// USERS COUNT TABLE
		$table = "
		<table class='table table-sm text-center'>
		<thead>
			<tr>
				<th>Actifs</th>
				<th>Inactifs</th>
			</tr>
		</thead>
		<tbody class='table-light font-weight-bold'>
			<tr>
				<td>{$usersActiveTotal['nb']}</td>
				<td>{$usersInactiveTotal['nb']}</td>
			</tr>
		</tbody>
		</table>";
			
		return $table;
	}
	
	/* usersNavForm */
	function usersNavForm($state, $secteur){
		
		$states = array("tous", "actifs", "inactifs");
		
		$form = "
		<form method='get' class='form-inline'>
			<div class='form-group'>
				<label for='secteur'>Abonnés</label>
				<select name='state' class='form-control form-control-sm mr-2'>";
			
		for($i=0;$i<=count($states)-1;$i++){
			$stateSelect="";
			if($state==$states[$i]){
				$stateSelect="selected";
			}
			$stateOption = ucfirst($states[$i]);
			$form.="
			<option value='{$states[$i]}' {$stateSelect}>{$stateOption}</option>";
			
		}
		$form.="
		</select>
		<label for='secteur'>Secteur</label>
			<select name='secteur' class='form-control form-control-sm mr-2'>";
				
		for($i=0;$i<=21;$i++){
			$secteurSelect="";
			$secteurOption = $i;
			if($secteur==$i){
				$secteurSelect="selected";
			}
			if($i==0){
				$secteurOption = "Tous";
			}
			$form.=" 
			<option value='{$i}' {$secteurSelect}>{$secteurOption}</option>";
		}
		
		$form.="
				</select>
			</div>
			<button class='btn btn-secondary btn-sm' type='submit'>OK</button>
		</form>";
		
		return $form;

	}
	
	
	/*****************************************************
		USER
	*****************************************************/
	
	/* userFlux */
	function userFlux($userID){
		
		global $connexion;
	
		$montantTotal = 0;
		$creditsTotal = 0;
		
		// USER FLUX THEAD
		$table ="
		<table class='table table-sm' id='user-flux-table'>
		<thead>
			<tr>
				<th>Date (eng)</th>
				<th>Date</th>
				<th>Action</th>
				<th>Crédits</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>";
		
		// FLUX REQUEST
		$flux_rq = "	
		SELECT 'credit' AS type, credits.dateCreation, credits.id AS fluxID
		FROM credits
		WHERE credits.userID = {$userID} AND credits.valid=1		
		UNION		
		SELECT 'pick' AS type, cal.date, picks.id AS fluxID
		FROM picks
		INNER JOIN cal ON picks.calID = cal.id
		WHERE picks.userID={$userID}";
		
		$flux_rs = mysqli_query($connexion, $flux_rq) or die();
		while($flux = mysqli_fetch_array($flux_rs)){
			
			$montant = 0;			
			$dateFrCell = convertDate($flux['dateCreation'],"en2fr");
			
			// FORMULE DATAS
			if($flux['type']=="credit"){
				
				$formule_rq = "
				SELECT credits.nb, credits.montant, abos.id AS aboID, subs.tarif AS subTarif
				FROM credits
				INNER JOIN formules ON formules.id = credits.formuleID
				LEFT JOIN abos ON abos.userID = credits.userID AND abos.creditID = credits.id
				LEFT JOIN subs ON subs.ID = abos.subID
				WHERE credits.id = {$flux['fluxID']}";
				$formule_rs = mysqli_query($connexion, $formule_rq) or die();
				$formule = mysqli_fetch_array($formule_rs);			
				
				// CREDITS
				if($formule['nb']!=0){
					$creditsCell = "+".$formule['nb'];
					$creditsClass = "text-success";
					$creditsTotal += $formule['nb'];		
					$montant+=$formule['montant'];
					
				}else{
					$creditsCell = "-";
					$creditsClass = "text-body";
				}
			
				// ABONNEMENT
				if(empty($formule['aboID'])){
					$actionCell = "Achat crédits";
				}else{
					$actionCell = "Abonnement ".formatPrice($formule['subTarif']);
					$montant+=$formule['subTarif'];
				}
				
				$montantTotal += $montant;
				$montantCell = formatPrice($montant);
				
				
			// PICK DATAS
			}else{
				
				$creditsClass = "text-danger";
				$montantCell = "-";
				
				$pick_rq = "
				SELECT picks.sacs, cal.date, collects.sacs AS collectSacs, collects.id AS collectID
				FROM picks
				INNER JOIN cal ON picks.calID = cal.id
				LEFT JOIN collects ON collects.pickID = picks.id
				WHERE picks.id={$flux['fluxID']}";
				$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
				$pick = mysqli_fetch_array($pick_rs);				
				
				if($pick['date']<date("Y-m-d")){
					if(!empty($pick['collectID'])){
						$trClass = "table-success";
						$actionCell = "Collecte effectuée";
						$credits = $pick['collectSacs'];
					}else{
						$trClass = "table-danger";
						$actionCell = "Collecte manquée";
						$credits = $pick['sacs'];					
					}
				} else{
					$trClass = "table-default";
					$actionCell = "Collecte programmée";
					$credits = $pick['sacs'];
				}
				
				$creditsTotal -= $credits;
				$creditsCell = "-".$credits;
				
			}
			
			$actionLink = "{$flux['type']}.detail.php?id={$flux['fluxID']}";
			
			// USER FLUX ROW
			$table .="
			<tr class={$trClass}>
				<td>{$flux['dateCreation']}</td>
				<td>{$dateFrCell}</td>
				<td><a href='{$actionLink}' data-toggle='modal' data-target='#edit_lightbox'>{$actionCell}</a></td>
				<td class='text-center table-light font-weight-bold {$creditsClass}'>{$creditsCell}</td>
				<td class='text-right table-light font-weight-bold'>{$montantCell}</td>
			</tr>";
		}
		
		$montantTotalCell = formatPrice($montantTotal);
		
		// USER FLUX TFOOT
		$table .="
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th class='text-right' colspan='3'>Total</th>
				<td class='text-center'>{$creditsTotal}</td>
				<td class='text-right'>{$montantTotalCell}</td>
			</tr>
		</tfoot>
		</table>";

		return $table;
	}

	/* userActive */
	function userActive($userID){		
		global $connexion;
/*		
		$userActive_rq = "
		SELECT abos.dateCreation, subs.mois
		FROM abos
		INNER JOIN subs ON subs.id = abos.subID
		WHERE abos.userID={$userID} AND abos.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)";
*/		
		$userActive_rq = "
		SELECT credits.id FROM abos
		INNER JOIN credits ON abos.creditID = credits.id
		INNER JOIN subs ON subs.id = abos.subID
		WHERE credits.userID={$userID} AND credits.dateCreation>=DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)";
		
		$userActive_rs = mysqli_query($connexion, $userActive_rq) or die();
		return mysqli_num_rows($userActive_rs);
		
	}	
	

	/* userCredits */
	function userCredits2($userID){
		
		global $connexion;
		
		$credits_rq = "SELECT SUM(creditsTmp.nb) AS nb FROM creditsTmp
		INNER JOIN credits ON credits.id=creditsTmp.orderID
		WHERE credits.userID={$userID} AND credits.transID!=0";
		$credits_rs = mysqli_query($connexion, $credits_rq) or die();
		$credits = mysqli_fetch_assoc($credits_rs);
		
		$bonus_rq="
		SELECT SUM(rewards.credits) AS nb FROM bonus 
		INNER JOIN rewards ON rewards.id = bonus.rewardID
		WHERE bonus.userID={$userID}";
		$bonus_rs = mysqli_query($connexion, $bonus_rq) or die();
		$bonus = mysqli_fetch_assoc($bonus_rs);		
		
		$pickCollected_rq = "
		SELECT SUM(nb) AS total
		FROM(
			SELECT SUM(collects.sacs) AS nb
			FROM picks
			INNER JOIN collects ON collects.id = picks.collectID
			WHERE picks.userID={$userID}
			UNION
			SELECT SUM(collects.sacs) AS nb
			FROM bundles
			INNER JOIN collects ON collects.id = bundles.collectID
			WHERE bundles.userID={$userID}
		)  AS pb";
		$pickCollected_rs = mysqli_query($connexion, $pickCollected_rq) or die();
		$pickCollected = mysqli_fetch_assoc($pickCollected_rs);
		
		$pickMissed_rq = "
		SELECT SUM(nb) AS total
		FROM(
			SELECT SUM(sacs) AS nb
			FROM picks
			WHERE NOT EXISTS(
				SELECT * FROM collects WHERE collects.id = picks.collectID
			) AND picks.userID={$userID}
			UNION
			SELECT SUM(sacs) AS nb
			FROM bundles
			WHERE NOT EXISTS(
				SELECT * FROM collects WHERE collects.id = bundles.collectID
			) AND bundles.userID={$userID}
		) AS pb";
		$pickMissed_rs = mysqli_query($connexion, $pickMissed_rq) or die();
		$pickMissed = mysqli_fetch_assoc($pickMissed_rs);
		
		$userCredits = $credits['nb']+$bonus['nb']-($pickCollected['total']+$pickMissed['total']);
		return $userCredits;
	}	
	
	
	
	/*****************************************************
		PICKS
	*****************************************************/
	
	/* picksCalendar */
	function picksCalendar($date){
		
		global $connexion;
		
		$month = date("m",strtotime($date));
		$year = date("Y",strtotime($date));
		
		$edit = 0;
		$editLimit = date('Y-m', strtotime("-2 months", strtotime($date)));
		if(date('Y-m')<=$editLimit){
			$edit = 1;
		}
		
		$secteursCal_rq = "SELECT id FROM cal WHERE MONTH(date)='{$month}' AND YEAR(date)='{$year}'";
		$secteursCal_rs = mysqli_query($connexion, $secteursCal_rq) or die();
		if(mysqli_num_rows($secteursCal_rs)){
			
			$picksTotal = 0;
			$sacsProgTotal = 0;
			$sacsColTotal = 0;
			$sacsSupTotal = 0;
					
			$running_day = date('w',mktime(0,0,0,$month,1,$year)-1);
			$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
			$day_counter = 0;
		
			for($list_day = 1; $list_day <= $days_in_month; $list_day++){
				
				if($running_day < 5){
			
					$date = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($day_counter+1));

					$calDay_rq = "SELECT * FROM cal WHERE date='".$date."'";
					$calDay_rs = mysqli_query($connexion, $calDay_rq) or die();
					$calDay_nb =mysqli_num_rows($calDay_rs);
					if($calDay_nb){
						
						$calDay_cpt = 0;
					
						while($calDay=mysqli_fetch_array($calDay_rs)){
							
							$sacsCell = "-";
							$sacsSupCount = "-";
							$sacsProgCount = 0;
							$sacsColCount = 0;

							$picks_rq = "
							SELECT picks.id, picks.userID, picks.sacs AS sacsProg,  collects.sacs AS sacsCol
							FROM picks
							LEFT JOIN collects ON collects.pickID = picks.id AND collects.pickType = 'pick'
							WHERE picks.calID={$calDay['id']}";
							$picks_rs = mysqli_query($connexion, $picks_rq) or die(mysqli_error($connexion));
							$picks_nb = mysqli_num_rows($picks_rs);
							
							if($picks_nb){
								
								$picksTotal += $picks_nb;
								
								$sacsSupCount = 0;
								
								while($picks=mysqli_fetch_array($picks_rs)){
									
									$sacsProgCount += $picks['sacsProg'];
									$sacsColCount  += $picks['sacsCol'];
									
									$sacs_rq = "SELECT SUM(sacs.nb) AS sacsSup FROM sacs 
									INNER JOIN credits ON credits.id = sacs.creditID
									WHERE credits.userID={$picks['userID']} AND credits.valid=1 AND sacs.collectID=0";
									$sacs_rs = mysqli_query($connexion, $sacs_rq) or die(mysqli_error($connexion));
									if(mysqli_num_rows($sacs_rs)){

										$sacs = mysqli_fetch_assoc($sacs_rs);
										$sacsSupCount += $sacs['sacsSup'];
										
									}

									$bundles_rq = "
									SELECT bundles.userID, bundles.sacs AS sacsProg, collects.sacs AS sacsCol
									FROM bundles 
									LEFT JOIN collects ON collects.pickID = bundles.id AND collects.pickType = 'bundle'
									WHERE bundles.pickID = {$picks['id']}";
									$bundles_rs = mysqli_query($connexion, $bundles_rq) or die(mysqli_error($connexion));
									
									while($bundles = mysqli_fetch_assoc($bundles_rs)){
										
										$sacsProgCount += $bundles['sacsProg'];
										$sacsColCount += $bundles['sacsCol'];
										
										$sacs_rq = "SELECT SUM(sacs.nb) AS sacsSup FROM sacs 
										INNER JOIN credits ON credits.id = sacs.creditID
										WHERE credits.userID={$bundles['userID']} AND credits.valid=1 AND sacs.collectID=0";
										$sacs_rs = mysqli_query($connexion, $sacs_rq) or die(mysqli_error($connexion));
										if(mysqli_num_rows($sacs_rs)){

											$sacs = mysqli_fetch_assoc($sacs_rs);
											$sacsSupCount += $sacs['sacsSup'];

										}
										
									}
									
								}
								
								$sacsProgTotal += $sacsProgCount;
								$sacsColTotal += $sacsColCount;
								$sacsSupTotal += $sacsSupCount;
								
								if($date<date("Y-m-d")){
									$sacsCell = $sacsColCount."<i class='fa fa-arrow-down' style='font-size:50%;'></i>";								
								}else{								
									$sacsCell = $sacsProgCount."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
								}
								
							}else{
								$picks_nb = "-";
							}
							

							if(!$calDay_cpt){							
								if($edit){
									$dateCell = utf8_encode(strftime("%a %d %B", strtotime($date)))." <a href='cal.edit.php?calDate={$date}&action=create' class='float-right' data-toggle='modal' data-target='#edit_lightbox'><i class='fas fa-plus-circle'></i></a>";
								}else{
									$dateCell = "<a href='cal.detail.php?date={$date}'>".utf8_encode(strftime("%a %d %B", strtotime($date)))."</a>";
								}
							}else{
								$dateCell = "";
							}
							
							
							if($calDay_nb==1 || $edit){
								$secteurCell = "{$calDay['secteur']}";
								if($edit){
									$secteurCell .= " <a href='cal.edit.php?calID={$calDay['id']}&action=delete' data-toggle='modal' data-target='#edit_lightbox' class='float-right'><i class='fas fa-minus-circle'></i></a>";
								}
								
							}else{
								$secteurCell = "<a href='cal.detail.php?date={$date}&secteur={$calDay['secteur']}'>{$calDay['secteur']}</a>";
							}
							
							
							if(!$edit){
								$tCells ="
								<td class='text-center table-light'>{$picks_nb}</td>
								<td class='text-center table-light'>{$sacsCell}</td>
								<td class='text-center table-light'>{$sacsSupCount}</td>";
							}

							$tBody.="
							<tr class='font-weight-bold'>
								<td>{$dateCell}</td>
								<td>{$secteurCell}</td>
								{$tCells}
							</tr>";
								
							$calDay_cpt++;

						}
						
					}else{
						
						if($edit){
							$dateCellAction = "<a href='cal.edit.php?calDate={$date}&action=create' class='float-right' data-toggle='modal' data-target='#edit_lightbox'><i class='fas fa-plus-circle'></i></a>";
							
						}else{
							$tdColspan = "4";
						}
						
						
						$tBody.="
						<tr class='table-dark '>
							<td>".utf8_encode(strftime("%a %d %B", strtotime($date)))." {$dateCellAction}</td>
							<td colspan='{$tdColspan}'></td>
						</tr>";
					}

				}
				
				if($running_day == 6){
					$running_day = -1;
				}
				$running_day++;
				$day_counter++;
			}
			
			if(!$edit){
				$tFoot = "
				<tfoot class='table-dark'>
					<th colspan='2' class='text-right'>Total</th>
					<td class='text-center'>{$picksTotal}</td>
					<td class='text-center'>{$sacsColTotal}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$sacsProgTotal}<i class='fa fa-arrow-up' style='font-size:50%;'></i></td>
					<td class='text-center'>{$sacsSupTotal}</td>
				</tfoot>";
				
				$tHead = "
				<th class='text-center'>Col.</th>
				<th class='text-center'>Sacs</th>
				<th class='text-center'>Sacs +</th>";
				
			}
			
			$table= "
			<table class='table table-sm table-hover'>
				<thead>
					<tr>
						<th>Date</th>
						<th>Secteur</th>
						{$tHead}
					</tr>
				</thead>
				<tbody>
					{$tBody}
				</tbody>
				{$tFoot}
			</table>";
			
		}else{
			
			$table = "Pas de collecte programmée ce mois ci - <a href='cal.action.php?action=plan&calDate={$date}'>Programmer les collectes</a>";
		}

		return $table;
	}
		
	/* picksDay */
	function picksDay($date){
		
		global $connexion;
		
		$calDay_rq = "SELECT * FROM cal WHERE date='".$date."'";
		$calDay_rs = mysqli_query($connexion, $calDay_rq) or die();
		if(mysqli_num_rows($calDay_rs)){
			while($calDay=mysqli_fetch_array($calDay_rs)){
				
				$section.="
				<h3>Secteur {$calDay['secteur']}</h3>".
				picksDayPickers($calDay['id']).
				picksDayTable($calDay['id']);
				
			}
		}else{
			$section="Aucune collecte programmée aujourd'hui";
		}
		
		return $section; 
	}
		
	/* picksDayPickers */
	function picksDayPickers($calID){
		
		global $connexion;
		
		$list="";

		$pickers_rq = "
		SELECT pickers.nom, pickers.prenom
		FROM pickersCal 
		LEFT JOIN pickers ON pickers.id = pickersCal.pickerID
		WHERE pickersCal.calID={$calID}";
		$pickers_rs = mysqli_query($connexion, $pickers_rq) or die(mysqli_error($connexion));
		if(mysqli_num_rows($pickers_rs)){
			$list.="<ul>";
			while($pickers = mysqli_fetch_array($pickers_rs)){
			
				$list.="<li>".$pickers['prenom']." ".$pickers['nom']."</li>";

			}
			$list.="</ul>";
			
		}else{
			$list.="<p>Aucun picker</p>";
		}		
		
		return $list;
		
	}

	/* picksDayTable */
	function picksDayTable($calID){
		
		global $connexion;
		
		
		$slots_rq = "SELECT * FROM slots";
		$slots_rs=mysqli_query($connexion, $slots_rq) or die();
		while($slots=mysqli_fetch_assoc($slots_rs)){
			
			$tBody = "";
			
			$sacsProgTotal = 0;
			$sacsColTotal = 0;
			
			$pick_rq = "
			SELECT cal.date, picks.id, picks.sacs, picks.userID, users.nom, users.prenom, adresses.voieNumero, voies.voieType, voies.voieLibelle, collects.sacs AS sacsCol, collects.id AS collectID
			FROM picks
			INNER JOIN users ON users.id = picks.userID
			INNER JOIN adresses ON adresses.id = picks.adresseID
			INNER JOIN voies ON voies.id = adresses.voieID
			LEFT JOIN collects ON collects.pickID = picks.id AND collects.pickType = 'pick'
			INNER JOIN cal ON cal.id = {$calID}
			WHERE picks.calID = '{$calID}' AND picks.slotID = {$slots['id']}";
			$pick_rs=mysqli_query($connexion, $pick_rq) or die();		
			$pick_nb = mysqli_num_rows($pick_rs);
			if($pick_nb){
				
				while($pick=mysqli_fetch_assoc($pick_rs)){
					
					$sacsProgTotal += $pick['sacs'];
					$sacsColTotal += $pick['sacsCol'];
					
					$sacsOrder_rq = "SELECT SUM(sacs.nb) AS sacsSup FROM sacs WHERE sacs.userID={$pick['userID']} AND sacs.collectID=0 AND sacs.valid=1";
					$sacsOrder_rs = mysqli_query($connexion, $sacsOrder_rq) or die(mysqli_error($connexion));
					if(mysqli_num_rows($sacsOrder_rs)){

						$sacsOrder = mysqli_fetch_assoc($sacsOrder_rs);
						$sacsSupCell = $sacsOrder['sacsSup'];
						$sacsSupTotal += $sacsOrder['sacsSup'];

					}
					
					if($pick['date']<date("Y-m-d")){
						
						if(empty($pick['collectID'])){
							$sacsPickCell = $pick['sacs']."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
						}else{
							$sacsPickCell = $pick['sacsCol']."<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
						}
						
						
					}else{
						
						if($pick['date']==date("Y-m-d")){
							
							if(empty($pick['collectID'])){
								$sacsPickCell = $pick['sacs']."<i class='fa fa-arrow-up' style='font-size:50%;'></i><a href='collect.edit.php?pickID={$pick['id']}&pickType=pick&action=create' data-toggle='modal' data-target='#edit_lightbox'>Edit</a>";
								
							}else{
								$sacsPickCell = $pick['sacsCol']."<i class='fa fa-arrow-down' style='font-size:50%;'></i>
							<a href='collect.edit.php?collectID={$pick['collectID']}&action=delete' data-toggle='modal' data-target='#edit_lightbox'><i class='fas fa-ban'></i></a> 
							<a href='collect.edit.php?collectID={$pick['collectID']}&pickID={$pick['id']}&pickType=pick&action=update' data-toggle='modal' data-target='#edit_lightbox'><i class='fas fa-edit'></i></a>";
							}
							
						}else{
							$sacsPickCell = $pick['sacs']."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
						}
						
					}
					
					$tBody .= "
					<tr>
						<td>{$pick['voieNumero']} {$pick['voieType']} {$pick['voieLibelle']}</td>
						<td>{$pick['prenom']} {$pick['nom']}</td>
						<td class='text-center table-light font-weight-bold'>{$sacsPickCell}</td>
						<td class='text-center table-light font-weight-bold'>{$sacsSupCell}</td>
					</tr>";
					
					
					$bundles_rq = "
					SELECT bundles.id, bundles.userID, bundles.sacs, users.nom, users.prenom, collects.sacs AS sacsCol, collects.id AS collectID
					FROM bundles
					INNER JOIN users ON users.id = bundles.userID
					LEFT JOIN collects ON collects.pickID = bundles.id AND collects.pickType = 'bundle'
					WHERE bundles.pickID = {$pick['id']}";
					$bundles_rs = mysqli_query($connexion, $bundles_rq) or die();
					if(mysqli_num_rows($bundles_rs)){
						while($bundles = mysqli_fetch_assoc($bundles_rs)){
							
							$sacsOrder_rq = "SELECT SUM(sacs.nb) AS sacsSup FROM sacs WHERE sacs.userID={$bundles['userID']} AND sacs.collectID=0 AND sacs.valid=1";
							$sacsOrder_rs = mysqli_query($connexion, $sacsOrder_rq) or die(mysqli_error($connexion));
							if(mysqli_num_rows($sacsOrder_rs)){

								$sacsOrder = mysqli_fetch_assoc($sacsOrder_rs);
								$sacsSupCell = $sacsOrder['sacsSup'];
								$sacsSupTotal += $sacsOrder['sacsSup'];

							}
							
							$sacsProgTotal += $bundles['sacs'];
							$sacsColTotal += $bundles['sacsCol'];
							
							if($pick['date']<date("Y-m-d")){
								
								if(empty($bundles['collectID'])){
									$sacsPickCell = $bundles['sacs']."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
								}else{
									$sacsPickCell = $bundles['sacsCol']."<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
								}
								
								
							}else{
								
								if($pick['date']==date("Y-m-d")){
									
									if(empty($bundles['collectID'])){
										$sacsPickCell = $bundles['sacs']."<i class='fa fa-arrow-up' style='font-size:50%;'></i><a href='collect.edit.php?pickID={$bundles['id']}&pickType=bundle&action=create' data-toggle='modal' data-target='#edit_lightbox'>Edit</a>";
										
									}else{
										$sacsPickCell = $bundles['sacsCol']."<i class='fa fa-arrow-down' style='font-size:50%;'></i>
									<a href='collect.edit.php?collectID={$bundles['collectID']}&action=delete' data-toggle='modal' data-target='#edit_lightbox'><i class='fas fa-ban'></i></a> 
									<a href='collect.edit.php?collectID={$bundles['collectID']}&pickID={$bundles['id']}&pickType=bundle&action=update' data-toggle='modal' data-target='#edit_lightbox'><i class='fas fa-edit'></i></a>";
									}
									
								}else{
									$sacsPickCell = $pick['sacs']."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
								}
								
							}							
							
							$tBody .= "
							<tr>
								<td></td>
								<td>{$bundles['prenom']} {$bundles['nom']}</td>
								<td class='text-center table-light font-weight-bold'>{$sacsPickCell}</td>
								<td class='text-center table-light font-weight-bold'>{$sacsSupCell}</td>
							</tr>";
						}
					}
				}
				
				$table .= "
				<h5>
					<button class='btn btn-link btn-block text-left hvr-icon-push p-0 m-0 h3' type='button' data-toggle='collapse' data-target='#slot{$slots['id']}'> {$slots['start']}/{$slots['end']} <span class='badge  badge-secondary float-right'>{$pick_nb}</span>
					</button>
				</h5>
				<section class='collapse' id='slot{$slots['id']}'>
					<table class='table table-sm'>
						<thead>
							<tr>
								<th>Adresse</th>
								<th>Nom</th>
								<th class='text-center'>Sacs</th>
								<th class='text-center'>Sacs +</th>
							</tr>
						</thead>
					<tbody>
						{$tBody}
					</tbody>
					<tfoot class='table-dark'>
						<tr>
							<th class='text-right' colspan='2'>Total</th>
							<td class='text-center'>{$sacsColTotal}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$sacsProgTotal}<i class='fa fa-arrow-up' style='font-size:50%;'></i></td>
							<td class='text-center'>{$sacsSupTotal}</td>
						</tr>
					</tfoot>
					</table>
				</section>";
			
			}

		}
		
		return $table;
		
	}	


	/* picksDayTable */
	function picksDayTable2($date, $secteur){
		
		global $connexion;
		
		$calDay_rq = "SELECT * FROM cal WHERE date='{$date}'";
		$calDay_rs = mysqli_query($connexion, $calDay_rq) or die();
		$calDay_nb = mysqli_num_rows($calDay_rs);
		if($calDay_nb){

			if($calDay_nb==1){
				
				$calDay=mysqli_fetch_assoc($calDay_rs);
				$collectNavUl = "
				<li class='nav-item'>
					<a class='nav-link active' href='#'>Secteur {$calDay['secteur']}</a>
				</li>";

			}else{
				
				if(empty($secteur)){
					
					$collectNavUl = "
					<li class='nav-item'>
						<a href='#' class='nav-link active'>Secteurs cumulés</a>
					</li>";
					while($calDay=mysqli_fetch_assoc($calDay_rs)){
						$collectNavUl .= "
						<li class='nav-item'>
							<a href='cal.detail.php?date={$date}&secteur={$calDay['secteur']}' class='nav-link'>Secteur {$calDay['secteur']}</a>
						</li>";
					}
					
					
				}else{
					
					$collectNavUl = "
					<li class='nav-item'>
						<a href='cal.detail.php?date={$date}' class='nav-link'>Secteurs cumulés</a>
					</li>";
					while($calDay=mysqli_fetch_assoc($calDay_rs)){
						if($calDay['secteur']==$secteur){
							$collectNavUl .= "
							<li class='nav-item'>
								<a href='#' class='nav-link active'>Secteur {$calDay['secteur']}</a>
							</li>";
						}else{
							$collectNavUl .= "
							<li class='nav-item'>
								<a href='cal.detail.php?date={$date}&secteur={$calDay['secteur']}' class='nav-link'>Secteur {$calDay['secteur']}</a>
							</li>";
						}
					}
				}	
			}
			
			$collectNav = "
			<ul class='nav nav-tabs mb-3'>
				{$collectNavUl}
			</ul>";

			if(!empty($secteur)){
				$where_rq = " AND voies.secteur = {$secteur}";
			}
			
			$pick_rq = "
			SELECT picks.id FROM picks
			INNER JOIN cal ON cal.id = picks.calID
			INNER JOIN adresses ON adresses.id = picks.adresseID
			INNER JOIN voies ON voies.id = adresses.voieID
			WHERE cal.date = '{$date}' {$where_rq}";
			$pick_rs=mysqli_query($connexion, $pick_rq) or die();		
			$pick_nb = mysqli_num_rows($pick_rs);

			if(mysqli_num_rows($pick_rs)){

				$edit = 0;
				$closed=0;
				
				if($date==date("Y-m-d")){
					$edit=1;
				}else{
					if($date<date("Y-m-d")){
						$closed = 1;
					}
				}

				$slots_rq = "SELECT * FROM slots";
				$slots_rs=mysqli_query($connexion, $slots_rq) or die();
				while($slots=mysqli_fetch_assoc($slots_rs)){
					
					$tBody = "";
					
					$sacsProgTotal = 0;
					
					$sacsSupOrderTotal = 0;
					$sacsSupDeliverTotal = 0;
					
					$pickSlots_rq = "
					SELECT cal.secteur, picks.id, picks.sacs, picks.userID, picks.collectID, users.nom, users.prenom, users.tel, adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle, collects.sacs AS sacsCol, miss.id AS missID
					FROM picks
					INNER JOIN users ON users.id = picks.userID
					INNER JOIN adresses ON adresses.id = picks.adresseID
					INNER JOIN voies ON voies.id = adresses.voieID
					INNER JOIN cal ON cal.id = picks.calID
					LEFT JOIN collects ON collects.id = picks.collectID
					LEFT JOIN miss ON miss.pickID = picks.id
					WHERE cal.date = '{$date}' AND picks.slotID = {$slots['id']} {$where_rq}
					ORDER BY picks.id ASC";
					$pickSlots_rs=mysqli_query($connexion, $pickSlots_rq) or die();		
					$pickSlots_nb = mysqli_num_rows($pickSlots_rs);
					if($pickSlots_nb){
						
						while($pickSlots=mysqli_fetch_assoc($pickSlots_rs)){
							
							$sacsProgTotal += $pickSlots['sacs'];
							$sacsColTotal += $pickSlots['sacsCol'];
							
							$pickUser = "{$pickSlots['prenom']} {$pickSlots['nom']}";
							$pickAdresse = "{$pickSlots['voieNumero']} {$pickSlots['voieType']} {$pickSlots['voieLibelle']}";
							if($pickSlots['cpl']){
								$pickAdresse .= "<br>{$pickSlots['cpl']}";
							}
							
							$trClass = "";
							$sacsSupOrder = "";
							$sacsSupDeliver = "";


							if(!empty($pickSlots['collectID'])){
								
								$sacsDeliver_rq = "
								SELECT SUM(sacs.nb) AS sacsSup FROM sacs 
								INNER JOIN credits ON credits.id = sacs.creditID
								WHERE credits.userID={$pickSlots['userID']} AND credits.valid=1 AND sacs.collectID={$pickSlots['collectID']}";
								$sacsDeliver_rs = mysqli_query($connexion, $sacsDeliver_rq) or die(mysqli_error($connexion));
								$sacsDeliver = mysqli_fetch_assoc($sacsDeliver_rs);
								if($sacsDeliver['sacsSup']){

									$sacsSupOrder = $sacsDeliver['sacsSup']."<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
									$sacsSupDeliverTotal += $sacsDeliver['sacsSup'];

								}else{
									$sacsSupOrder = "-";
								}
								
								$trClass = "table-success";

							}else{
								
								$sacsOrder_rq = "
								SELECT SUM(sacs.nb) AS sacsSup FROM sacs 
								INNER JOIN credits ON credits.id = sacs.creditID
								WHERE credits.userID={$pickSlots['userID']} AND credits.valid=1 AND sacs.collectID=0";
								$sacsOrder_rs = mysqli_query($connexion, $sacsOrder_rq) or die(mysqli_error($connexion));
								$sacsOrder = mysqli_fetch_assoc($sacsOrder_rs);
								if($sacsOrder['sacsSup']){

									$sacsSupOrder = $sacsOrder['sacsSup']."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
									$sacsSupOrderTotal += $sacsOrder['sacsSup'];

								}else{
									$sacsSupOrder = "-";
								}
								
								if(!empty($pickSlots['missID'])){
									$trClass = "table-warning";
								}
								
							}
							
							if($edit){
								
								if(empty($pickSlots['collectID'])){
									
									$editBtn = "
									<button data-edit='collect' data-rq='pickID={$pickSlots['id']}&pickType=pick&action=create' data-toggle='modal' data-target='#editModal' class='btn btn-secondary' type='button'>
										<i class='fas fa-check'></i>
									</button>
									<button data-edit='collect' data-rq='pickID={$pickSlots['id']}&pickType=pick&action=miss' data-toggle='modal' data-target='#editModal' class='btn btn-danger' type='button'>
										<i class='fas fa-times'></i>
									</button>";
	
									$sacsPick = "{$pickSlots['sacs']}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
									
								}else{
									$editBtn = "
									<button data-edit='collect' data-rq='collectID={$pickSlots['collectID']}&pickID={$pickSlots['id']}&pickType=pick&action=update' data-toggle='modal' data-target='#editModal' class='btn btn-secondary' type='button'>
										<i class='fas fa-pen'></i>
									</button>
									<button data-edit='collect' data-rq='collectID={$pickSlots['collectID']}&pickID={$pickSlots['id']}&pickType=pick&action=delete' data-toggle='modal' data-target='#editModal' class='btn btn-danger' type='button'>
										<i class='fas fa-ban'></i>
									</button>";

									$sacsPick = "{$pickSlots['sacsCol']}<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
								}
															
								$tdEdit = "
								<td class='table-light'>
									<div class='btn-group btn-group-sm'>
										{$editBtn}
									</div>
								</td>";
								
							}else{
								
								if(!$closed){
								
									if(empty($pickSlots['collectID'])){
										$sacsPickNb = $pickSlots['sacs'];
										$sacsPickIcon = "fa-arrow-up";
									}else{
										$sacsPickNb = $pickSlots['sacsCol'];
										$sacsPickIcon = "fa-arrow-down";
									}
									
									$sacsPick = "{$sacsPickNb}<i class='fa {$sacsPickIcon}' style='font-size:50%;'></i>";

								}else{
									
									$sacsPickIcon = "fa-arrow-down";
									
									if(empty($pickSlots['collectID'])){
										$sacsPickNb = 0;
										$trClass = "table-danger";
									}else{
										$sacsPickNb = $pickSlots['sacsCol'];
										$trClass = "table-success";
									}
									
									$sacsPick = "{$sacsPickNb}<i class='fa {$sacsPickIcon}' style='font-size:50%;'></i>";
								}
								
							}

							if($calDay_nb>1 && empty($secteur)){
								$tdSecteur = "<td>{$pickSlots['secteur']}</td>";
							}
							
							$tBody .= "
							<tr class='font-weight-bold {$trClass}'>
								{$tdSecteur}
								<td>{$pickAdresse}</td>
								<td><a href='tel:{$pickSlots['tel']}'>{$pickSlots['tel']}</a></td>
								<td>{$pickUser}</td>
								<td class='text-center table-light font-weight-bold'>{$sacsPick}</td>
								<td class='text-center table-light font-weight-bold'>{$sacsSupOrder}</td>
								{$tdEdit}
							</tr>";


							$bundles_rq = "
							SELECT bundles.id, bundles.userID, bundles.sacs, users.nom, users.prenom, collects.sacs AS sacsCol, collects.id AS collectID
							FROM bundles
							INNER JOIN users ON users.id = bundles.userID
							LEFT JOIN collects ON collects.id = bundles.collectID
							WHERE bundles.pickID = {$pickSlots['id']}";
							$bundles_rs = mysqli_query($connexion, $bundles_rq) or die();
							if(mysqli_num_rows($bundles_rs)){
								while($bundles = mysqli_fetch_assoc($bundles_rs)){
									
									$trClass = "";
									$sacsSupOrder = "";
									$sacsSupDeliver = "";

									if(!empty($bundles['collectID'])){
										
										$sacsDeliver_rq = "
										SELECT SUM(sacs.nb) AS sacsSup FROM sacs 
										INNER JOIN credits ON credits.id = sacs.creditID
										WHERE credits.userID={$bundles['userID']} AND sacs.collectID={$bundles['collectID']} AND credits.valid=1";
										$sacsDeliver_rs = mysqli_query($connexion, $sacsDeliver_rq) or die(mysqli_error($connexion));
										$sacsDeliver = mysqli_fetch_assoc($sacsDeliver_rs);
										if($sacsDeliver['sacsSup']){

											$sacsSupOrder = $sacsDeliver['sacsSup']."<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
											$sacsSupDeliverTotal += $sacsDeliver['sacsSup'];

										}
										
										$trClass = "table-success";
										
									}else{
										
										$sacsOrder_rq = "
										SELECT SUM(sacs.nb) AS sacsSup FROM sacs 
										INNER JOIN credits ON credits.id = sacs.creditID
										WHERE credits.userID={$bundles['userID']} AND sacs.collectID=0 AND credits.valid=1";
										$sacsOrder_rs = mysqli_query($connexion, $sacsOrder_rq) or die(mysqli_error($connexion));
										$sacsOrder = mysqli_fetch_assoc($sacsOrder_rs);
										if($sacsOrder['sacsSup']){

											$sacsSupOrder = $sacsOrder['sacsSup']."<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
											$sacsSupOrderTotal += $sacsOrder['sacsSup'];

										}else{
											$sacsSupOrder = "0";
										}
								
										if(!empty($pickSlots['missID'])&&empty($pickSlots['collectID'])){
											$trClass = "table-warning";
										}
										
									}
									
									$sacsProgTotal += $bundles['sacs'];
									$sacsColTotal += $bundles['sacsCol'];
									
									if($edit){
										
										if(empty($bundles['collectID'])){
											
											$editBtn = "
											<button data-edit='collect' data-rq='pickID={$bundles['id']}&pickType=bundle&action=create' data-toggle='modal' data-target='#editModal' class='btn btn-secondary' type='button'>
												<i class='fas fa-check'></i>
											</button>";

											$sacsPick = "{$bundles['sacs']}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
											
										}else{
											
											$editBtn = "
											<button data-edit='collect' data-rq='collectID={$bundles['collectID']}&pickID={$bundles['id']}&pickType=bundle&action=update' data-toggle='modal' data-target='#editModal' class='btn btn-secondary' type='button'>
												<i class='fas fa-pen'></i>
											</button>";
											
											$sacsPick = "{$bundles['sacsCol']}<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
											
										}
										
										$tdEdit = "
										<td class='table-light'>
											<div class='btn-group btn-group-sm'>
												{$editBtn}
											</div>
										</td>";
										
									}else{
										
										if(!$closed){
								
											if(empty($bundles['collectID'])){
												$sacsPickNb = $bundles['sacs'];
												$sacsPickIcon = "fa-arrow-up";
											}else{
												$sacsPickNb = $bundles['sacsCol'];
												$sacsPickIcon = "fa-arrow-down";
											}
											
											$sacsPick = "{$sacsPickNb}<i class='fa {$sacsPickIcon}' style='font-size:50%;'></i>";

										}else{
											
											$sacsPickIcon = "fa-arrow-down";
											
											if(empty($bundles['collectID'])){
												$sacsPickNb = 0;
												$trClass = "table-danger";
											}else{
												$sacsPickNb = $bundles['sacsCol'];
												$trClass = "table-success";
											}
											
											$sacsPick = "{$sacsPickNb}<i class='fa {$sacsPickIcon}' style='font-size:50%;'></i>";
										}
										
									}

									if($calDay_nb>1 && empty($secteur) ){
										$tdSecteur = "<td></td>";
									}
									
									
									$tBody .= "
									<tr class='font-weight-bold {$trClass}'>
										{$tdSecteur}
										<td></td>
										<td></td>
										<td>{$bundles['prenom']} {$bundles['nom']}</td>
										<td class='text-center table-light font-weight-bold'>{$sacsPick}</td>
										<td class='text-center table-light font-weight-bold'>{$sacsSupOrder}</td>
										{$tdEdit}
									</tr>";
								}
							}
						}
						
						if($calDay_nb>1 && empty($secteur)){
							$thSecteur = "<th><i class='fas fa-home'></i></th>";
							$thTotalColspan = 4;
						}else{
							$thTotalColspan = 3;
						}
						
						if($edit){
							$thPickEdit = "<td></td>";
							$sacsPickTotal = "{$sacsColTotal}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$sacsProgTotal}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
						}else{
							if(!$closed){	
								$sacsPickTotal = "{$sacsProgTotal}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";

							}else{
								$sacsPickTotal = "{$sacsColTotal}<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
							}
						}
						
						if($closed){
							$sacSupTotal = "{$sacsSupDeliverTotal}<i class='fa fa-arrow-down' style='font-size:50%;'></i>";
						}else{
							if($edit){
								$sacSupTotal = "{$sacsSupDeliverTotal}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$sacsSupOrderTotal}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
							}else{
								$sacSupTotal = "{$sacsSupOrderTotal}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
							}
						}
						
						$thead = "
						<thead>
							<tr>
								{$thSecteur}
								<th>Adresse</th>
								<th>Tel</th>
								<th>Nom</th>
								<th class='text-center'><i class='fas fa-shopping-bag'></i></th>
								<th class='text-center'><i class='fas fa-shopping-bag'></i> +</th>
								{$thPickEdit}
							</tr>
						</thead>";
						

						$collectCard .= "
						<div class='card border-0' style='background:none;'>
							<div class='card-header p-0' style='background-color:transparent;'>
								<button class='btn btn-link h5' type='button' data-toggle='collapse' data-target='#slot{$slots['id']}'> {$slots['start']}/{$slots['end']}
								</button>
								<span class='badge badge-primary float-right'>{$pickSlots_nb}</span>
							</div>
							<section class='collapse show' id='slot{$slots['id']}'  data-parent='#collectAccordion'>
								<table class='table m-0'>
								{$thead}
								<tbody>
									{$tBody}
								</tbody>
								<tfoot class='table-dark'>
									<tr>
										<th class='text-right' colspan='{$thTotalColspan}'>Total</th>
										<td class='text-center text-nowrap'>{$sacsPickTotal}</td>
										<td class='text-center text-nowrap'>{$sacSupTotal}</td>
										{$thPickEdit}
									</tr>
								</tfoot>
								</table>
							</section>
						</div>";
					
					}
					
					
				}
				
				$content = "
				<div class='accordion' id='collectAccordion'>
					{$collectCard}
				</div>";
				
			}else{
				$content = "
				<div class='alert alert-info text-center' role='alert'>
					Aucune collecte n'a été programmée
				</div>";
			}
			
			$content = $collectNav.$content;
			
		}else{
			
			$content = "
			<div class='alert alert-info text-center' role='alert'>
				Journée non collectée
			</div>";
			
		}
		
		return $content;
		
		
	}	



	/* picksStats */
	function picksStats($calID){
		
		global $connexion;
		
		$totalCol=0;
		$totalSac=0;
		
		$table="";
		$table.="
		";
		
		$pick_rq = "
		SELECT picks.*, collects.sacs AS collectSacs, slots.start, slots.end, count(*) as colNb, SUM(picks.sacs) AS sacNb
		FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON slots.id = picks.slotID
		LEFT JOIN collects ON collects.pickID = picks.id
		WHERE cal.id = '{$calID}'
		GROUP BY picks.slotID ";
		$pick_rs=mysqli_query($connexion, $pick_rq) or die();
		while($pick=mysqli_fetch_array($pick_rs)){
			

			$totalCol+=$pick['colNb'];
			$totalSac+=$pick['sacNb'];
			
			$tBody.="
			<tr>
				<td>{$pick['start']}/{$pick['end']}</td>
				<td class='text-center table-light font-weight-bold'>{$pick['colNb']}</td>
				<td class='text-center table-light font-weight-bold'>{$pick['sacNb']}</td>
			</tr>";
		}
		
		$table="
		<table class='table table-sm'>
		<thead>
			<tr>
				<th>Horaire</th>
				<th>Nb</th>
				<th>Sacs</th>
			</tr>
		</thead>
		<tbody>
			{$tBody}
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th>Total</th>
				<td class='text-center'>{$totalCol}</td>
				<td class='text-center'>{$totalSac}</td>
			</tr>
		</tfoot>
		</table>";
		
		return $table;
		
	}		
	
	/* picksDetail */
	function picksDetail($pickID){
		
		global $connexion;
		
		$pick_rq = "
		SELECT picks.*, cal.date, slots.start, slots.end, collects.id AS collectID, collects.sacs AS collectSacs, collects.hour, collects.pickerID, pickers.prenom
		FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON slots.id = picks.slotID
		LEFT JOIN collects ON collects.pickID = picks.id	
		LEFT JOIN pickers ON pickers.id	= collects.pickerID
		WHERE picks.id={$pickID}";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
		$pick = mysqli_fetch_array($pick_rs);

		$table="
		<p>Date : <strong>{$pick['date']}</strong></p>
		<p>Programmée entre <strong>{$pick['start']}</strong> et <strong>{$pick['end']}</strong><br/>Sacs programmés : <strong>{$pick['sacs']}</strong></p>";
		
		if($pick['date']<date("Y-m-d")){
			if(!empty($pick['collectID'])){
				$table.="	
				<p>Effecuée à <strong>{$pick['hour']}</strong> par <strong>{$pick['prenom']}</strong><br/>Sacs collectés : <strong>{$pick['collectSacs']}</strong></p>";
			}else{
				$table.="	
				<p>Manquée</p>";				
			}
		}		

		return $table;
		
	}


	/* calEdit */
	function calEdit($action, $calID, $calDate){
		
		global $connexion;
		$err = 0;
		
		switch($action){
			
			case 'delete':
				if(empty($calID)){
					$err = 1;
				}
			break;
			
			case 'create':
				if(empty($calDate)){
					$err = 1;
				}
			break;
			
			default;
				$err = 1;
			break;
			
		}
		
		
		if(!$err){
			
			$formFooter = formFooter("nolibelle ");
			
			if($action=='delete'){
				
				$title ="Supprimer un secteur";
				$lead = "Etes vous sur de vouloir supprimer ce secteur ?";
				
				
			}
			
			if($action=='create'){
				
				$calMonth = date('m', strtotime($calDate));
				$calYear = date('Y', strtotime($calDate));
				
				$title ="Ajouter un secteur";
				$lead = "Ajouter un secteur à ce jour de collecte";
					
				$secteursCal_rq = "SELECT id FROM cal WHERE MONTH(date)='{$calMonth}' AND YEAR(date)='{$calYear}'";
				$secteursCal_rs = mysqli_query($connexion, $secteursCal_rq) or die();
				if(mysqli_num_rows($secteursCal_rs)<21){
					
					$selectOptSecteur = "";
					
					for($i=1; $i<=21; $i++){
						
						$secteurExist_rq = "SELECT id FROM cal WHERE MONTH(date)='{$calMonth}' AND YEAR(date)='{$calYear}' AND secteur={$i}";
						$secteurExist_rs = mysqli_query($connexion, $secteurExist_rq) or die();
						if(!mysqli_num_rows($secteurExist_rs)){
							$selectOptSecteur .= "<option value={$i}>Secteur {$i}</option>";
						}
						
					}
					
					$form = "
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							<label>Secteur</label>
							<select name='secteur' id='secteur' class='form-control' required>
								{$selectOptSecteur}
							</select>
						</div>
					</div>";
					
					
				}else{
					$form = "Tous les secteurs sont déjà attribués";
				}
				
				
				
			}
			
			
			$section = "
			<h3>
				{$title}
				<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
			</h3>			
			<section id='pickEdit'>
				<form action='cal.action.php' method='post' id='calEditForm'>
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							<p class='text-form font-weight-bold mb-0'>{$lead}</p>
						</div>
					</div>					
					{$form}				
					<input type='hidden' name='action' value='{$action}'/>
					<input type='hidden' name='calID' id='calID' value='{$calID}'/>
					<input type='hidden' name='calDate' id='calDate' value='{$calDate}'/>			
					{$formFooter}				
				</form>
			</section>			
			<script>
				$('#calEditForm').validate();
			</script>";					

		}else{
			
			$section = "erreur";
			
		}
		
		return $section;
		
	}


	/* calPickEdit */
	function collectEdit($action, $collectID, $pickID, $pickType){
		
		global $connexion;
		$err = 0;
		
		switch($action){
			
			case 'create':
				if(empty($pickID)){
					$err = 1;
				}
			break;			
			case 'update':
				if(empty($collectID)){
					$err = 1;
				}
			break;			
			case 'delete':
				if(empty($collectID)){
					$err = 1;
				}
			break;
			case 'miss':
				if(empty($pickID)){
					$err = 1;
				}
			break;
			default;
				$err = 1;
			break;
			
		}
		
		if(!$err){
			
			if($pickType=="pick"){
				$pick_rq = "
				SELECT picks.userID, picks.sacs, slots.start, slots.end, users.nom, users.prenom, adresses.voieNumero, voies.voieType, voies.voieLibelle, collects.sacs AS sacsCol
				FROM picks
				INNER JOIN cal ON cal.id = picks.calID
				INNER JOIN slots ON slots.id = picks.slotID
				INNER JOIN users ON users.id = picks.userID
				INNER JOIN adresses ON adresses.id = users.adresseID
				INNER JOIN voies ON voies.id = adresses.voieID
				LEFT JOIN collects ON collects.pickID = {$pickID}
				WHERE picks.id={$pickID}";
			}
			
			if($pickType=="bundle"){
				$pick_rq = "
				SELECT bundles.userID, bundles.sacs, cal.date, slots.start, slots.end,  users.nom, users.prenom, adresses.voieNumero, voies.voieType, voies.voieLibelle, collects.sacs AS sacsCol
				FROM bundles
				INNER JOIN picks ON picks.id = bundles.pickID
				INNER JOIN cal ON cal.id = picks.calID
				INNER JOIN slots ON slots.id = picks.slotID
				INNER JOIN users ON users.id = bundles.userID
				INNER JOIN adresses ON adresses.id = picks.adresseID
				INNER JOIN voies ON voies.id = adresses.voieID
				LEFT JOIN collects ON collects.pickID = {$pickID}
				WHERE bundles.id={$pickID}";
			}

			$pick_rs = mysqli_query($connexion, $pick_rq) or die();
			$pick =  mysqli_fetch_array($pick_rs);

			$formFooter = formFooter("nolibelle ");
			
			if($action=='create' || $action=='update'){

				if($action=='create'){
					$title ="Valider la collecte";
					$lead = "Sélectionner le nombre de sacs collectés.";
					$creditsLimit = userCredits($pick['userID'])+$pick['sacs'];
					$sacsPickDetail = "{$pick['sacs']}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
					$sacsSelected = $pick['sacs'];
				}
				
				if($action=='update'){
					$title ="Modifier la collecte";
					$lead = "Modifier le nombre de sacs collectés.";
					$creditsLimit = userCredits($pick['userID'])+$pick['sacsCol'];
					$sacsPickDetail = "{$pick['sacsCol']}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$pick['sacs']}<i class='fa fa-arrow-up' style='font-size:50%;'></i> ";
					$sacsSelected = $pick['sacsCol'];
				}
				
				for($i=1; $i<=$creditsLimit; $i++){
					
					$selected="";
					if($i==$sacsSelected){
						$selected = "selected";
					}
					
					$selectOptSacs .= "<option value='{$i}' {$selected}>{$i}</option>";				
				}
				
				// SACS ORDER
				$sacsOrder_rq = "SELECT SUM(sacs.nb) AS sacsSup FROM sacs WHERE sacs.userID={$pick['userID']} AND sacs.collectID=0 AND sacs.valid=1";
				$sacsOrder_rs = mysqli_query($connexion, $sacsOrder_rq) or die(mysqli_error($connexion));
				$sacsOrder = mysqli_fetch_assoc($sacsOrder_rs);
				if($sacsOrder['sacsSup']){

					
					$sacsOrderCheck = "
					<div class='col-md-12 form-group'>
						<div class='form-check'>
							<input type='checkbox' value='1' class='form-check-input' name='sacsSup' id='sacsSup' checked>
							<label class='form-check-label' for='sacsSup'>Remise de {$sacsOrder['sacsSup']} sac(s) supplémentaires</label>
						 </div>
					 </div>";
					 
					$sacsOrderDetail = "<li>Sac(s) sup: <strong>{$sacsOrder['sacsSup']} <i class='fa fa-arrow-up' style='font-size:50%;'></i></strong> {$sacOrder_nb}</li>";
					 
				}
				
				if(!empty($collectID)){
					$sacsDeliver_rq = "SELECT SUM(sacs.nb) AS sacsSup FROM sacs WHERE sacs.userID={$pick['userID']} AND sacs.collectID={$collectID} AND sacs.valid=1";
					$sacsDeliver_rs = mysqli_query($connexion, $sacsDeliver_rq) or die(mysqli_error($connexion));
					if(mysqli_num_rows($sacsDeliver_rs)){

						$sacsDeliver = mysqli_fetch_assoc($sacsDeliver_rs);
						
						$sacsOrderDetail = "<li>Sac(s) sup: <strong>{$sacsDeliver['sacsSup']} <i class='fa fa-arrow-down' style='font-size:50%;'></i></strong></li>";

					}
				}
				
				$form = "
				<div class='form-row'>
					<div class='col-md-12 form-group'>
						<select name='sacs' id='sacs' class='form-control' required>
							{$selectOptSacs}
						</select>
					</div>
					{$sacsOrderCheck}
				</div>";

			}
			
			if($action=='delete'){
				
				$sacsPickDetail = "{$pick['sacsCol']}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$pick['sacs']}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
				
				$title ="Annuler la collecte";
				$lead = "Etes vous sur de vouloir annuler cette collecte ?";
				
			}
			
			if($action=='miss'){
				
				$sacsPickDetail = "{$pick['sacsCol']}<i class='fa fa-arrow-down' style='font-size:50%;'></i> {$pick['sacs']}<i class='fa fa-arrow-up' style='font-size:50%;'></i>";
				
				$title ="Collecte manquée";
				$lead = "Etes vous sur que cette collecte est manquée ?";
				
			}
			
			
			
			
			// PICK DETAIL
			$pickDetail = "
			<h4>Détail collecte</h4>
			<ul>
				<li>Abonné : <strong>{$pick['prenom']} {$pick['nom']}</strong></li>
				<li>Adresse : <strong>{$pick['voieNumero']} {$pick['voieType']} {$pick['voieLibelle']} </strong></li>
				<li>Sac(s) : <strong>{$sacsPickDetail}</strong></li>
				{$sacsOrderDetail}
			</ul>";
			
			
			$section = "
			<h3>
				{$title}
				<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
			</h3>			
			<section id='collectEdit'>
				<form action='edit/collect.edit.php' method='post' id='collectEditForm'>
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							{$pickDetail}
						</div>
					</div>
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							<p class='text-form font-weight-bold mb-0'>{$lead}</p>
						</div>
					</div>					
					{$form}				
					<input type='hidden' name='action' value='{$action}'/>
					<input type='hidden' name='collectID' id='collectID' value='{$collectID}'/>
					<input type='hidden' name='pickID' id='pickID' value='{$pickID}'/>	
					<input type='hidden' name='pickType' id='pickType' value='{$pickType}'/>					
					{$formFooter}				
				</form>
			</section>			
			<script>
				$('#collectEditForm').validate();
			</script>";		

		}else{
			$section = "erreur";		
		}
		
		return $section;
		
	}



	/*****************************************************
		TRANSACTIONS
	*****************************************************/	
		
	/* transListing */
	function transListing($period, $date){
		
		global $connexion;	
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
				$where_rq = "YEAR(transactions.dateCreation) = {$date}";
			break;			
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				$where_rq = "MONTH(transactions.dateCreation) = {$month} AND YEAR(transactions.dateCreation) = {$year}";
			break;
			case "day":
				$where_rq = "transactions.dateCreation = '{$date}'";
			break;			
			case "custom":
				$where_rq = "transactions.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
			break;
		}
				
		$subsMontant = 0;
		$creditsMontant = 0;
		$sacsMontant = 0;
		$remisesMontant = 0;
		$transMontant = 0;

		// FLUX REQUEST
		$orders_rq = "
		SELECT transactions.*, orders.id AS orderID, orders.montant, orders.reglement, orders.remise, users.nom, users.prenom, users.societe, abos.id AS aboID, subs.tarif AS subTarif, sacs.montant AS sacsMontant, creditsTmp.montant AS creditsMontant
		FROM transactions
		INNER JOIN orders ON orders.transID = transactions.transID
		INNER JOIN users ON users.id = orders.userID
		LEFT JOIN creditsTmp ON creditsTmp.orderID = orders.id
		LEFT JOIN sacs ON sacs.orderID = orders.id
		LEFT JOIN abos ON abos.orderID = orders.id
		LEFT JOIN subs ON subs.ID = abos.subID
		WHERE {$where_rq}";

		$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
		while($orders=mysqli_fetch_array($orders_rs)){
			
			$dateFrCell = convertDate($orders['dateCreation'],"en2fr");

			if(!empty($orders['societe'])){
				$user = "{$orders['societe']} <span class='badge badge-secondary'>Pro</span>";
			}else{
				$user = "{$orders['nom']} {$orders['prenom']}";	
			}
			
			//CREDITS
			if(!empty($orders['creditsMontant'])){			
				$creditsMontant += $orders['creditsMontant'];
			}
			
			// ABONNEMENT
			if(!empty($orders['aboID'])){
				$subsMontant += $orders['subTarif'];			
			}
			
			//SACS
			if(!empty($orders['sacsMontant'])){
				$sacsMontant += $orders['sacsMontant'];	
			}
			
			// REMISES
			if($orders['remise']){	
				$remisesMontant += $orders['remise']*1.2;
			}
			
			$transMontant += $orders['montant'];
			$orderMontantCell = formatPrice($orders['montant']);
			
			$tbody .= " 
			<tr>
				<td>{$orders['dateCreation']}</td>
				<td>{$dateFrCell}</td>
				<td><button data-edit='devis' data-rq='devisID={$orders['orderID']}&action=detail' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0' type='button'>{$orders['reglement']} {$orders['transID']}</button></td>
				<td><a href='users.php?userID={$orders['userID']}'>{$user}</a></td>
				<td class='text-right table-light font-weight-bold'>{$orderMontantCell}</td>
			</tr>";
			
		}		
		
		$transMontantCell = formatPrice($transMontant);
		$subsMontantCell = formatPrice($subsMontant);
		$creditsMontantCell = formatPrice($creditsMontant);
		$sacsMontantCell = formatPrice($sacsMontant);
		$remisesMontantCell = formatPrice($remisesMontant);
		
		// ORDER LISTING
		$table = "
		<table class='table table-sm' id='orders-listing-table'>
		<thead>
			<tr>
				<th>Date (Ymd)</th>
				<th>Date</th>
				<th>Trans.</th>
				<th>Abonné</th>
				<th class='text-right'>Montant</th>
			</tr>
		</thead>
		<tbody>
			{$tbody}
		</tbody>
		<tfoot class='table-dark text-right'>
			<tr>
				<th colspan='4'>Total</th>
				<td>{$transMontantCell}</td>
			</tr>
			<tr>
				<th colspan='4'>Abonnements</th>
				<td>{$subsMontantCell}</td>
			</tr>
			<tr>
				<th colspan='4'>Crédits</th>
				<td>{$creditsMontantCell}</td>
			</tr>
			<tr>
				<th colspan='4'>Sacs</th>
				<td>{$sacsMontantCell}</td>
			</tr>
			<tr>
				<th colspan='4'>Remises</th>
				<td>{$remisesMontantCell}</td>
			</tr>
		</tfoot>
		</table>";
		
		return $table;		

	}



	function ordersDetail($orderID){
		
		global $connexion;
		
		$orders_rq = "
		SELECT orders.*, users.nom, users.prenom,  users.societe, abos.id AS aboID , subs.tarif AS subTarif , sacs.nb AS sacsNb, sacs.montant AS sacsMontant, creditsTmp.nb AS creditsNb, creditsTmp.montant AS creditsMontant, transactions.dateCreation AS transDate, transactions.id AS factureID
		FROM orders
		INNER JOIN users ON users.id = orders.userID
		LEFT JOIN transactions ON transactions.transID = orders.transID
		LEFT JOIN creditsTmp ON creditsTmp.orderID = orders.id
		LEFT JOIN sacs ON sacs.orderID = orders.id
		LEFT JOIN abos ON abos.orderID = orders.id
		LEFT JOIN subs ON subs.ID = abos.subID
		WHERE orders.id = {$orderID}";
		$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
		$orders=mysqli_fetch_array($orders_rs);
		
		$dateFr = convertDate($orders['dateCreation'],"en2fr");
		
		$tbody = "";
		
		
		if(!empty($orders['aboID'])){
			
			$montantTotal += $orders['subTarif'];
			
			if(!empty($orders['societe'])){
				$montantAboCell = formatPrice($orders['subTarif']/1.2);
			}else{
				$montantAboCell = formatPrice($orders['subTarif']);
			}
			
			$tbody.="
			<tr>
				<td>Abonnement</td>
				<td class='text-center'>1</td>
				<td class='text-center'>{$montantAboCell}</td>
				<td class='text-right table-light font-weight-bold'>{$montantAboCell}</td>
			</tr>";
		}
		
		if(!empty($orders['creditsNb'])){
			
			$montantTotal += $orders['creditsMontant'];
			
			if(!empty($orders['societe'])){
				$montantCreditsCell = formatPrice($orders['creditsMontant']/1.2);
				$priceCreditsCell = formatPrice($orders['creditsMontant']/$orders['creditsNb']/1.2);
				
			}else{
				$montantCreditsCell = formatPrice($orders['creditsMontant']);
				$priceCreditsCell = formatPrice($orders['creditsMontant']/$orders['creditsNb']);
			}
			
			$tbody.="
			<tr>
				<td>Crédits</td>
				<td class='text-center'>{$orders['creditsNb']}</td>
				<td class='text-center'>{$priceCreditsCell}</td>
				<td class='text-right table-light font-weight-bold'>{$montantCreditsCell}</td>
			</tr>";
			
		}
		
		if(!empty($orders['sacsNb'])){
			
			$montantTotal += $orders['sacsMontant'];
			
			if(!empty($orders['societe'])){
				$montantSacsCell = formatPrice(($orders['sacsMontant'])/1.2);
				$sacPrice = formatPrice(($orders['sacsMontant'])/1.2/$orders['sacsNb']);
			}else{
				$montantSacsCell = formatPrice($orders['sacsMontant']);
				$sacPrice = formatPrice($orders['sacsMontant']/$orders['sacsNb']);
			}
			
			$tbody.="
			<tr>
				<td>Sacs</td>
				<td class='text-center'>{$orders['sacsNb']}</td>
				<td class='text-center'>{$sacPrice}</td>
				<td class='text-right table-light font-weight-bold'>{$montantSacsCell}</td>
			</tr>";
			
		}
		
		if($orders['remise']){
			
			$montantTotal -= $orders['remise']*1.2;
			$montantRemiseCell = formatPrice($orders['remise']);
			
			$tbody.="
			<tr>
				<td colspan='3'>Remise</td>
				<td class='text-right table-light font-weight-bold'>- {$montantRemiseCell}</td>
			</tr>";		
			
		}
		
		$montantTotalCell = formatPrice($montantTotal);
		

		if(!empty($orders['societe'])){
			
			$user = "{$orders['societe']}";
			
			$montantTotalHtCell = formatPrice($montantTotal/1.2);
			$montantTvaCell = formatPrice($montantTotal-($montantTotal/1.2));

			$tfoot="
			<tr>
				<th class='text-right' colspan='3'>Total HT</th>
				<td class='text-right table-dark font-weight-bold'>{$montantTotalHtCell}</td>
			</tr>
			<tr>
				<th class='text-right' colspan='3'>TVA (20%)</th>
				<td class='text-right table-dark font-weight-bold'>{$montantTvaCell}</td>
			</tr>			
			<tr>
				<th class='text-right' colspan='3'>Total TTC</th>
				<td class='text-right table-dark font-weight-bold'>{$montantTotalCell}</td>
			</tr>";
			
			$thead = "
			<tr>
				<th>Désignation</th>
				<th class='text-center'>Qté</th>
				<th class='text-center'>Prix HT</th>
				<th class='text-right'>Mnt HT</th>
			</tr>";
			
		}else{
			
			$user = "{$orders['prenom']} {$orders['nom']}";
			
			$tfoot="
			<tr>
				<th class='text-right' colspan='3'>Total TTC</th>
				<td class='text-right table-dark font-weight-bold'>{$montantTotalCell}</td>
			</tr>";
			
			$thead = "
			<tr>
				<th>Désignation</th>
				<th class='text-center'>Qté</th>
				<th class='text-center'>Prix</th>
				<th class='text-right'>Mnt</th>
			</tr>";
			
		}
		
		$facturation = str_replace(',', '<br />', $orders['facturation']);
		
		// DETAIL THEAD
		$table = "
		
		<h3>
			Facture {$orders['reglement']} {$orders['transID']}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>			
		<section id='devisDetail'>
		
		<p class='text-uppercase'><strong>{$user} <br> {$facturation}</strong></p>
		<p>Date : <strong>{$dateFr}</strong> <br>Mode reglement : <strong>{$orders['reglement']}</strong></p>
			
		
		<table class='table table-sm' id='devis-listing-table'>
		<thead>
			{$thead}
		</thead>
		<tbody>
			{$tbody}
		</tbody>
		<tfoot>
			{$tfoot}
		</tfoot>
		</table>
		</section>";
		
		return $table;

	}
	
	

	/* fluxStats */
	function fluxStats($period, $date, $stat, $statType){
		
		global $connexion;
		
		// STAT VARIABLES
		switch($stat){
			case "date":
				$infoThCell = "Jour";
				if($period=="year"){$infoThCell = "Mois";}
				
			break;
			case "secteur":
				$group_rq="voies.secteur";
				$infoThCell = "Secteur";
			break;
		}
		
		// FORMULES STATS THEAD
		$table="
		<table class='table table-sm' id='flux-{$stat}-stats-table'>
		<thead>
			<tr>
				<th>{$infoThCell}</th>
				<th>%</th>
				<th>Montant</th>
			</tr>
		</thead>
		<tbody>";
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
				if($stat=="date"){ 
					$group_rq="MONTH(credits.dateCreation)";
				}
				$where_rq = "YEAR(credits.dateCreation) = '{$date}'";
			break;			
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				if($stat=="date"){ 
					$group_rq="DAY(credits.dateCreation)";
				}
				$where_rq = "MONTH(credits.dateCreation) = '{$month}' AND YEAR(credits.dateCreation) = '{$year}'";
			break;
			case "day":
				$where_rq = "credits.dateCreation = '{$date}'";
			break;
			case "custom":
				$where_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
			break;
		}
		
		// FLUX TOTAL REQUEST
		$fluxTotal_rq = "
		SELECT SUM(credits.montant) AS montantFormule, SUM(subs.tarif) AS montantAbo
		FROM credits
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		LEFT JOIN abos ON abos.creditID=credits.id
		LEFT JOIN subs ON subs.id = abos.subID
		WHERE credits.valid=1 AND {$where_rq}";
		$fluxTotal_rs=mysqli_query($connexion, $fluxTotal_rq) or die();
		$fluxTotal=mysqli_fetch_array($fluxTotal_rs);
		
		$montantTotal = $fluxTotal['montantFormule']+$fluxTotal['montantAbo'];
		$montantTotalCell = formatPrice($montantTotal);
		
		// FLUX REQUEST
		$flux_rq = "
		SELECT credits.dateCreation,voies.secteur, abos.id AS aboID,
		SUM(credits.montant) AS montantFormule, SUM(subs.tarif) AS montantAbo
		FROM credits
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		LEFT JOIN abos ON abos.creditID=credits.id
		LEFT JOIN subs ON subs.id = abos.subID
		WHERE credits.valid=1 AND {$where_rq}
		GROUP BY {$group_rq}";
		$flux_rs=mysqli_query($connexion, $flux_rq) or die();
		while($flux=mysqli_fetch_array($flux_rs)){
			
			switch($stat){
				case "date":
					if($period=="year"){
						$infoCell = "<a href='flux.php?period=month&date=".date('Y-m', strtotime($flux['dateCreation']))."'>".convertDate($flux['dateCreation'],"2bY")."</a>";
					}else{
						$infoCell = "<a href='flux.php?period=day&date=".$flux['dateCreation']."'>". convertDate($flux['dateCreation'],"en2fr")."</a>";
					}
				break;
				case "secteur":
					$infoCell = "Secteur ".$flux['secteur'];
				break;
			}
			
			$montant = $flux['montantFormule']+$flux['montantAbo'];
			$montantCell = formatPrice($montant);
			$ratioCell = round((100*$montant)/$montantTotal,1)."%";
			
			// FORMULES STATS ROW
			$table.="
			<tr>
				<td>{$infoCell}</td>
				<td>{$ratioCell}</td>
				<td class='text-right table-light font-weight-bold'>{$montantCell}</td>
			</tr>";
		}
		
		// FORMULES STATS TFOOT
		$table.="
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th colspan='2'>Total</th>
				<td class='text-right'>{$montantTotalCell}</td>
			</tr>
		</tfoot>
		</table>";
				
		return $table;		
	}	

	/* creditDetail */
	function creditDetail($creditID){
		
		global $connexion;
		
		$credit_rq = "
		SELECT credits.nb,  credits.dateCreation, users.nom, users.prenom, credits.montant, abos.id AS aboID, subs.tarif AS subTarif, adresses.voieNumero, voies.voieType, voies.voieLibelle
		FROM credits
		INNER JOIN users ON users.id = credits.userID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		LEFT JOIN formules ON formules.id = credits.formuleID
		LEFT JOIN abos ON abos.userID = credits.userID AND abos.creditID=credits.id
		LEFT JOIN subs ON subs.ID = abos.subID
		WHERE credits.id = {$creditID}";
		$credit_rs = mysqli_query($connexion, $credit_rq) or die();
		$credit = mysqli_fetch_array($credit_rs);
		
		$table ="
		<p>
			Date: <strong>{$credit['dateCreation']}</strong><br>
			Abonné: <strong style='text-transform:uppercase'>{$credit['prenom']} {$credit['nom']}</strong><br>
			Adresse: <strong style='text-transform:uppercase'>{$credit['voieNumero']} {$credit['voieType']} {$credit['voieLibelle']}</strong>
		</p>
		<table class='table table-sm'>
		<thead>
			<tr>
				<th>Action</th>
				<th>Tarif</th>
				<th>Montant</th>
			</tr>
		</thead>
		<tbody>";
		
		// CREDITS
		if($credit['nb']!=0){
			
			$montantCell = formatPrice($credit['montant']);
			//$tarifCell = formatPrice($credit['tarif']);
			$montantTotal+=$credit['montant'];
			
			$table.="
			<tr>
				<td>Achat de {$credit['nb']} crédit(s)</td>
				<td class='text-center '>{$tarifCell}</td>
				<td class='table-light font-weight-bold text-right'>{$montantCell}</td>
			</tr>";
			
		}
	
		// ABONNEMENT
		if(!empty($credit['aboID'])){
			$montantCell = formatPrice($credit['subTarif']);
			$montantTotal+=$credit['subTarif'];
			$table.="
			<tr >
				<td colspan='2'>Abonnement annuel</td>
				<td class='table-light font-weight-bold text-right'>{$montantCell}</td>
			</tr>";
			
		}

		$montantTotalCell = formatPrice($montantTotal);
		
		$table .="
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th class='text-right' colspan='2'>Total</td>
				<td class='text-right'>{$montantTotalCell}</td>
			</tr>
		</tfoot>";		
		
		
		return $table;
		
	}
	
	
	/* devisListing */
	function devisListing($period, $date, $secteur){
		
		global $connexion;
		
		// FLUX DETAIL THEAD
		$table = "
		<table class='table table-sm' id='devis-listing-table'>
		<thead>
			<tr>
				<th>Date (Ymd)</th>
				<th>Date</th>
				<th>Ref</th>
				<th>Société</th>
				<th>Contact</th>
				<th class='text-right'>Prix TTC</th>
				<th class='text-right'>Prix HT</th>
			</tr>
		</thead>
		<tbody>";		
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
				$where_rq = "YEAR(credits.dateCreation) = {$date}";
				$whereSac_rq = "YEAR(sacs.dateCreation) = {$date}";
			break;			
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				$where_rq = "MONTH(credits.dateCreation) = {$month} AND YEAR(credits.dateCreation) = {$year}";
				$whereSac_rq = "MONTH(sacs.dateCreation) = {$month} AND YEAR(sacs.dateCreation) = {$year}";
			break;
			case "day":
				$where_rq = "credits.dateCreation = '{$date}'";
				$whereSac_rq = "sacs.dateCreation = '{$date}'";
			break;			
			case "custom":
				$where_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
				$whereSac_rq = "sacs.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
			break;
		}

		// FLUX REQUEST
		$credits_rq = "
		SELECT credits.*, users.nom, users.prenom,  users.societe, abos.id AS aboID , subs.tarif AS subTarif , sacs.nb AS sacsNb
		FROM credits
		INNER JOIN users ON users.id = credits.userID
		LEFT JOIN formules ON formules.id = credits.formuleID
		LEFT JOIN abos ON abos.creditID=credits.id
		LEFT JOIN subs ON subs.ID = abos.subID
		LEFT JOIN sacs ON sacs.creditID=credits.id
		WHERE credits.transID=0 AND users.societe!='' AND {$where_rq}";
		
		$credits_rs=mysqli_query($connexion, $credits_rq) or die(mysqli_error($connexion));
		while($credits=mysqli_fetch_array($credits_rs)){
			
			$fluxCreditTr = "";
			$dateFrCell = convertDate($credits['dateCreation'],"en2fr");

			$montantAboCell = formatPrice($credits['subTarif']);
			$montantHtAboCell = formatPrice($credits['subTarif']/1.2);
			
			$montantFormuleCell = formatPrice($credits['montant']);
			$montantHtFormuleCell = formatPrice($credits['montant']/1.2);
			
			$montantSacsCell = formatPrice($credits['sacsNb']*3.5);
			$montantHtSacsCell = formatPrice($credits['sacsNb']*3.5/1.2);
			
			$montantTotal = $credits['subTarif']+$credits['montant']+($credits['sacsNb']*3.5);
			$montantTotalCell = formatPrice($montantTotal);
			$montantTotalHtCell = formatPrice($montantTotal/1.2);
			
			$fluxCreditTr .= " 
			<tr>
				<td>{$credits['dateCreation']}</td>
				<td>{$dateFrCell}</td>
				<td><button data-edit='devis' data-rq='devisID={$credits['id']}&action=detail' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0' type='button'>Devis {$credits['id']}</button></td>
				<td><a href='users.php?userID={$credits['userID']}'>{$credits['societe']}</a></td>
				<td>{$credits['nom']} {$credits['prenom']}</td>
				<td class='text-right table-light font-weight-bold'>{$montantTotalCell}</td>
				<td class='text-right table-light font-weight-bold'>{$montantTotalHtCell} 
				
				<button data-edit='devis' data-rq='devisID={$credits['id']}&action=update' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0' type='button'>Modifier</button><br>
				<button data-edit='devis' data-rq='devisID={$credits['id']}&action=delete' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0' type='button'>Supprimer</button><br>
				<button data-edit='devis' data-rq='devisID={$credits['id']}&action=valid' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0' type='button'>Valider</button><br>
				<button data-edit='devis' data-rq='devisID={$credits['id']}&action=edit' data-toggle='modal' data-target='#editModal' class='btn btn-link p-0' type='button'>Editer</button>
				</td>
			</tr>";
					
			$table .= $fluxCreditTr;
			
		}		
		
		$montantTotalCell = formatPrice($montantTotal);
		$montantAboTotalCell = formatPrice($montantAboTotal);
		$montantFormuleTotalCell = formatPrice($montantFormuleTotal);
		$montantSacTotalCell = formatPrice($montantSacTotal);
		
		// FLUX DETAIL FOOT
		$table .= "
		</tbody>
		</table>";
		
		return $table;		

	}

	

	function devisEdit($action, $devisID){
		
		global $connexion;
		$err = 0;
		
		switch($action){
		
			case 'update':
				if(empty($devisID)){
					$err = 1;
				}else{
					
					$sSQL = "
					SELECT credits.*, sacs.nb AS sacsNb, users.societe, users.nom, users.prenom, users.tel, users.email, adresses.voieNumero
					FROM credits 
					INNER JOIN sacs ON sacs.creditID=$devisID
					INNER JOIN users ON users.id=credits.userID
					INNER JOIN adresses ON adresses.id=users.adresseID
					WHERE credits.id={$devisID}";
					$result = mysqli_query($connexion, $sSQL) or die();
					if ($row = mysqli_fetch_assoc($result)) {
						foreach ($row as $key => $value) {
							$$key = $value;
						}
					}
					mysqli_free_result($result);
						
				}
			break;			
			case 'delete':
				if(empty($devisID)){
					$err = 1;
				}
			break;
			default;
				$err = 1;
			break;
			
		}	

		if(!$err){
			
			$formFooter = formFooter("nolibelle ");
			
			$section = "
			<h3>
				{$title} Edit Devis
				<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
			</h3>			
			<section id='devisEdit'>
				<form action='edit/devis.edit.php' method='post' id='devisEditForm'>
					<h4>Société</h4>
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							<label>Raison sociale</label>
							<input class='form-control' type='text' name='societe' value='{$societe}' />
						</div>
					</div>
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							<label>Adresse de facturation</label>
							<input class='form-control' type='text' name='societe' value='{$facturation}' />
						</div>
					</div>
					<h4>Contact</h4>
					<div class='form-row'>
						<div class='col-md-6 form-group'>
							<label>Nom</label>
							<input class='form-control' type='text' name='nom' value='{$nom}' />
						</div>
						<div class='col-md-6 form-group'>
							<label>Prenom</label>
							<input class='form-control' type='text' name='prenom' value='{$prenom}' />
						</div>
					</div>
					<div class='form-row'>
						<div class='col-md-4 form-group'>
							<label>Téléphone</label>
							<input class='form-control' type='text' name='tel' value='{$tel}' />
						</div>
						<div class='col-md-8 form-group'>
							<label>E-mail</label>
							<input class='form-control' type='email' name='email' value='{$email}' />
						</div>						
					</div>					
					<h4>Formule</h4>					
					<div class='form-row'>
						<div class='col-md-4 form-group'>
							<label>Crédits</label>
							<input class='form-control' type='text' name='creditsNb' value='{$nb}' />
						</div>
						<div class='col-md-4 form-group'>
							<label>Sacs</label>
							<input class='form-control' type='text' name='sacsNb' value='{$sacsNb}'/>
						</div>
						<div class='col-md-4 form-group'>
							<label>Remise (HT)</label>
							<input class='form-control' type='text' name='remise' value='{$remise}'/>
						</div>
					</div>	
					<h4>Adresse de collecte</h4>
					<div class='form-row'>
						<div class='col-md-3 form-group'>
							<label>Numero</label>
							<input class='form-control' type='text' name='voieNumero' value='{$voieNumero}' />
						</div>
						<div class='col-md-9 form-group'>
							<label>Voie</label>
							<input class='form-control' type='text' name='sacsNb' value='{$voieSelect}'/>
						</div>
					</div>
					<div class='form-row'>
						<div class='col-md-12 form-group'>
							<label>Complément d'adresse</label>
							<input class='form-control' type='text' name='cpl' value='{$cpl}' />
						</div>
					</div>
					
					<input type='hidden' name='action' value='{$action}'/>
					<input type='hidden' name='devisID' id='devisID' value='{$devisID}'/>
					{$formFooter}				
				</form>
			</section>			
			<script>
				$('#devisEditForm').validate();
			</script>";	
			
			return $section;
			
		}
		
		
	}



	
	/*****************************************************
		ABOS
	*****************************************************/
	
	/* abosListing*/
	function abosListing($period, $date, $secteur){
		
		global $connexion;
		
		$montantTotal = 0;
		
		// ABOS DETAIL THEAD
		$table = "
		<table class='table table-sm' id='abos-listing-table'>
		<thead>
			<tr>
				<th>Date (eng)</th>
				<th>Date</th>
				<th>Abonné</th>
				<th>Abo.</th>
				<th class='text-center'>Secteur</th>
				<th>Montant</th>
			</tr>
		</thead>
		<tbody>";		
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
			
				$whereNew_rq = "YEAR(credits.dateCreation) = '{$date}'";
				
				$dateFrom = $date."-01-01";
				$dateTo = $date."-12-31";
				
				/*
				$whereEnd_rq = "abos.dateCreation BETWEEN DATE_ADD('{$dateFrom}',INTERVAL -subs.mois MONTH) AND DATE_ADD('{$dateTo}',INTERVAL -subs.mois MONTH)";
				if($date==date("Y")){
					$whereEnd_rq .= " AND abos.dateCreation <= DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)";
				}
				*/
				
			break;			
			case "month":
			
				$whereNew_rq = "MONTH(credits.dateCreation) = '".date('m', strtotime($date))."' AND YEAR(credits.dateCreation) = '".date('Y', strtotime($date))."'";
				
				$dateFrom = $date."-01";
				$dateTo = date("Y-m-t", strtotime($date));
				
				/*
				$whereEnd_rq = "abos.dateCreation BETWEEN DATE_ADD('{$dateFrom}',INTERVAL -subs.mois MONTH) AND DATE_ADD('{$dateTo}',INTERVAL -subs.mois MONTH)";			
				if($date==date("Y-m")){
					$whereEnd_rq .= " AND abos.dateCreation <= DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)";
				}
				*/
				
			break;
			case "day":
				if($date > date("Y-m-d")){
					$date = date("Y-m-d");
				}
				$whereNew_rq = "credits.dateCreation = '{$date}'";
				
				//$whereEnd_rq = "abos.dateCreation = DATE_ADD({$date},INTERVAL -subs.mois MONTH)";
				
			break;
			case "custom":
				$dateFrom = $date[0];
				$dateTo = $date[1];
				if($date[1] > date("Y-m-d")){
					$date[1] = date("Y-m-d");
				}	
				$whereNew_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
				
				//$whereEnd_rq = "abos.dateCreation BETWEEN DATE_ADD('{$date[0]}',INTERVAL -subs.mois MONTH) AND DATE_ADD('{$date[1]}',INTERVAL -subs.mois MONTH)";
				
			break;
		}		

		// ABOS REQUEST
		$abos_rq = "
		SELECT abos.id, credits.dateCreation, credits.userID, users.nom, users.prenom, voies.secteur, subs.tarif, subs.mois
		FROM abos
		INNER JOIN users ON users.id = abos.userID
		INNER JOIN subs ON subs.id = abos.subID
		INNER JOIN credits ON credits.id = abos.creditID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE credits.valid=1 AND {$whereNew_rq}";
		
		/*
		$abos_rq = "
		SELECT abos.id, abos.dateCreation, abos.userID, users.nom, users.prenom, voies.secteur, subs.tarif, subs.mois, 'new' AS type
		FROM abos
		INNER JOIN users ON users.id = abos.userID
		INNER JOIN subs ON subs.id = abos.subID
		INNER JOIN credits ON credits.id = abos.creditID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE {$whereNew_rq}
		UNION
		SELECT abos.id, abos.dateCreation, abos.userID, users.nom, users.prenom, voies.secteur, subs.tarif, subs.mois, 'end' AS type
		FROM abos
		INNER JOIN users ON users.id = abos.userID
		INNER JOIN credits ON credits.id = abos.creditID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		LEFT JOIN subs ON abos.subID = subs.id
		WHERE {$whereEnd_rq}";
		*/
		
		
		if(!empty($secteur) AND $secteur!=0){
			$abos_rq .= " AND credits.secteur={$secteur}";
		}						

		$abos_rs=mysqli_query($connexion, $abos_rq) or die(mysqli_error($connexion));
		while($abos=mysqli_fetch_array($abos_rs)){
			$trClass = "table-success";	
			$dateFrCell = convertDate($abos['dateCreation'],"en2fr");
			$montantCell = formatPrice($abos['tarif']);
			$montantTotal += $abos['tarif'];
			
			$abosPrev_rq = "SELECT COUNT(*) AS nb FROM abos WHERE dateCreation < '".$abos['dateCreation']."' AND userID={$abos['userID']}";
			$abosPrev_rs=mysqli_query($connexion, $abosPrev_rq) or die();
			$abosPrev=mysqli_fetch_array($abosPrev_rs);
		
			if($abosPrev['nb']!=0){
				$trClass = "table-default";
			}	
			
			/*
			if($abos['type']=="new"){
				
				$trClass = "table-default";	
				$dateFrCell = convertDate($abos['dateCreation'],"en2fr");
				$montantCell = formatPrice($abos['tarif']);
				$montantTotal += $abos['tarif'];
				
				$abosPrev_rq = "SELECT COUNT(*) AS nb FROM abos WHERE dateCreation < '".$abos['dateCreation']."' AND userID={$abos['userID']}";
				$abosPrev_rs=mysqli_query($connexion, $abosPrev_rq) or die();
				$abosPrev=mysqli_fetch_array($abosPrev_rs);
			
				if($abosPrev['nb']!=0){
					$trClass = "table-success";
				}	

			}else{
				$trClass = "table-warning";
				$dateEng = date('Y-m-d', strtotime("+{$subs['mois']} months", strtotime($abos['dateCreation'])));
				$dateFrCell = convertDate($dateEng,"en2fr");
				$montantCell = "-";
				$typeCell = "Fin abo";
			}
			*/
			
			$aboCell = "{$abos['mois']} mois";
			
			// ABOS DETAIL ROW
			$table .= "
			<tr class='{$trClass}'>
				<td>{$abos['dateCreation']}</td>
				<td>{$dateFrCell}</td>
				<td><a href='users.php?userID={$abos['userID']}'>{$abos['nom']} {$abos['prenom']}</a></td>
				<td>{$aboCell}</td>
				<td class='text-center'>{$abos['secteur']}</td>
				<td class='table-light font-weight-bold text-right'>{$montantCell}</td>
			</tr>";
						
		}
		
		$montantTotalCell = formatPrice($montantTotal);

		// ABOS DETAIL FOOT
		$table .= "
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th class='text-right' colspan='5'>Total</th>
				<td class='text-right'>{$montantTotalCell}</td>
			</tr>
		</tfoot>
		</table>";
		
		return $table;		

	}
	
	/* abosStats */
	function abosStats($period, $date, $stat, $statType){
		
		global $connexion;
		
		// STATS VARIABLES
		switch($stat){
			case "date":
				$statThCell = "Jour";
				if($period=="year"){$statThCell = "Mois";}	
			break;
			case "secteur":
				$group_rq="voies.secteur";
				$statThCell = "Secteur";
			break;
			case "abos":
				$group_rq="abos.subID";
				$statThCell = "Abo.";
			break;
		}
		
		switch($statType){
			case "montant":
				$select_rq = "SUM(subs.tarif)";
			break;
			case "abos":
				$select_rq = "COUNT(*)";
			break;
		}
		
		// ABOS STATS THEAD
		$table="
		<table class='table table-sm' id='abos-{$stat}-stats-table'>
		<thead>
			<tr>
				<th>{$statThCell}</th>
				<th>%</th>
				<th class='text-center'>{$statType}</th>
			</tr>
		</thead>
		<tbody>";
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
				if($stat=="date"){
					$group_rq="MONTH(credits.dateCreation)";
				}
				$where_rq = "YEAR(credits.dateCreation) = {$date}";
			break;			
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				if($stat=="date"){
					$group_rq="DAY(credits.dateCreation)";
				}
				$where_rq = "MONTH(credits.dateCreation) = {$month} AND YEAR(credits.dateCreation) = {$year}";
			break;
			case "day":
				$where_rq = "credits.dateCreation = '{$date}'";
			break;	
			case "custom":
				$where_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
			break;
		}
		
		// ABOS TOTAL REQUEST
		$abosTotal_rq = "
		SELECT {$select_rq} AS statNb
		FROM abos
		INNER JOIN credits ON credits.id = abos.creditID
		INNER JOIN subs ON subs.id = abos.subID
		WHERE {$where_rq} AND credits.valid=1";
		$abosTotal_rs=mysqli_query($connexion, $abosTotal_rq) or die(mysqli_error($connexion));
		$abosTotal=mysqli_fetch_array($abosTotal_rs);
		
		if($statType=="montant"){
			$totalCell = formatPrice($abosTotal['statNb']);
		}else{
			$totalCell = $abosTotal['statNb'];
		}		
		
		// ABOS REQUEST
		$abos_rq = "
		SELECT {$select_rq} AS statNb, abos.id, credits.dateCreation, voies.secteur, subs.tarif, subs.mois
		FROM abos
		INNER JOIN credits ON credits.id = abos.creditID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		INNER JOIN subs ON subs.id = abos.subID
		WHERE  {$where_rq} AND credits.valid=1
		GROUP BY {$group_rq}";
		$abos_rs=mysqli_query($connexion, $abos_rq) or die();
		while($abos=mysqli_fetch_array($abos_rs)){

			switch($stat){
				case "date":
					if($period=="year"){
						$statCell = "<a href='abos.php?period=month&date=".date('Y-m', strtotime($abos['dateCreation']))."'>".convertDate($abos['dateCreation'],"2bY")."</a>";
					}else{
						$statCell = "<a href='abos.php?period=day&date=".$abos['dateCreation']."'>".convertDate($abos['dateCreation'],"en2fr")."</a>";
					}
				break;
				case "secteur":
					$statCell = "Secteur ".$abos['secteur'];
				break;
				case "abos":
					$statCell = "{$abos['mois']} mois - ".formatPrice($abos['tarif']);
				break;
			}
			
			$ratioCell = round((100*$abos["statNb"])/$abosTotal["statNb"],1)."%";
			if($statType=="montant"){
				$statNbCell = formatPrice($abos['statNb']);
			}else{
				$statNbCell = $abos['statNb'];
			}
			
			// FORMULES STATS ROW
			$table.="
			<tr>
				<td>{$statCell}</td>
				<td class='text-center'>{$ratioCell}</td>
				<td class='text-center table-light'><strong>{$statNbCell}</strong></td>
			</tr>";
		}

		// FORMULES STATS FOOT
		$table.="
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th colspan='2' class='text-right'>Total</th>
				<td class='text-center'>{$totalCell}</td>
			</tr>
		</tfoot>
		</table>";
		
		return $table;
		
		
	}
	
	/* abosUsers */
	function abosUsers($period, $date){
		
		global $connexion;
		
		switch($period){
			case "year":
			
				$whereNew_rq = "YEAR(credits.dateCreation) = '{$date}'";
				
				$dateFrom = $date."-01-01";
				$dateTo = $date."-12-31";
				
				$whereEnd_rq = "credits.dateCreation BETWEEN DATE_ADD('{$dateFrom}',INTERVAL -subs.mois MONTH) AND DATE_ADD('{$dateTo}',INTERVAL -subs.mois MONTH)";
				if($date==date("Y")){
					$whereEnd_rq .= " AND credits.dateCreation <= DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)";
				}
				
			break;			
			case "month":
			
				$whereNew_rq = "MONTH(credits.dateCreation) = '".date('m', strtotime($date))."' AND YEAR(credits.dateCreation) = '".date('Y', strtotime($date))."'";
				
				$dateFrom = $date."-01";
				$dateTo = date("Y-m-t", strtotime($date));
				
				$whereEnd_rq = "credits.dateCreation BETWEEN DATE_ADD('{$dateFrom}',INTERVAL -subs.mois MONTH) AND DATE_ADD('{$dateTo}',INTERVAL -subs.mois MONTH)";
				
				if($date==date("Y-m")){
					$whereEnd_rq .= " AND credits.dateCreation <= DATE_ADD(CURDATE(),INTERVAL -subs.mois MONTH)";
				}
				
			break;
			case "day":
				if($date > date("Y-m-d")){
					$date = date("Y-m-d");
				}
				$whereNew_rq = "credits.dateCreation = '{$date}'";
				$whereEnd_rq = "credits.dateCreation = DATE_ADD({$date},INTERVAL -subs.mois MONTH)";
			break;
			case "custom":
				$dateFrom = $date[0];
				$dateTo = $date[1];
				if($date[1] > date("Y-m-d")){
					$date[1] = date("Y-m-d");
				}	
				$whereNew_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
				
				$whereEnd_rq = "credits.dateCreation BETWEEN DATE_ADD('{$date[0]}',INTERVAL -subs.mois MONTH) AND DATE_ADD('{$date[1]}',INTERVAL -subs.mois MONTH)";
			break;
		}

		$abosNew_rq = "
		SELECT COUNT(*) AS nb 
		FROM abos 
		INNER JOIN credits ON abos.creditID = credits.id
		INNER JOIN subs ON abos.subID = subs.id
		WHERE credits.valid=1 AND {$whereNew_rq}";
		$abosNew_rs = mysqli_query($connexion, $abosNew_rq) or die(mysqli_error($connexion));
		$abosNew = mysqli_fetch_array($abosNew_rs);		

		$abosEnd_rq = "
		SELECT COUNT(*) AS nb 
		FROM abos 
		INNER JOIN credits ON abos.creditID = credits.id
		INNER JOIN subs ON abos.subID = subs.id
		WHERE credits.valid=1 AND {$whereEnd_rq}";
		$abosEnd_rs = mysqli_query($connexion, $abosEnd_rq) or die(mysqli_error($connexion));
		$abosEnd = mysqli_fetch_array($abosEnd_rs);

		$abosDiff = $abosNew['nb']-$abosEnd['nb'];
		
		return "
		<table class='table table-sm text-center'>
		<thead>
			<tr>
				<th>Nouveaux</th>
				<th>Terminés</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>+{$abosNew['nb']}</td>
				<td>-{$abosEnd['nb']}</td>
				<td>{$abosDiff}</td>
			</tr>
		</tbody>
	</table>";
	}
	

	/*****************************************************
		FORMULES
	*****************************************************/	
		
	/* formulesListing */
	function formulesListing($period, $date, $secteur){
		
		global $connexion;
		
		$creditsTotal = 0;
		$montantTotal = 0;
		
		// FORMULES DETAIL HEAD
		$table = "
		<table class='table table-sm' id='formules-listing-table' style='width:100%'>
		<thead>
			<tr>
				<th>Date (eng)</th>
				<th>Date</th>
				<th>Abonné</th>
				<th>Formule</th>
				<th class='text-center'>Crédits</th>
				<th>Montant</th>
			</tr>
		</thead>
		<tbody>";		
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
				$where_rq = "YEAR(credits.dateCreation) = '{$date}'";
			break;			
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				$where_rq = "MONTH(credits.dateCreation) = '{$month}' AND YEAR(credits.dateCreation) = '{$year}'";
			break;
			case "day":
				$where_rq = "credits.dateCreation = '{$date}'";
			break;			
			case "custom":
				$where_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
			break;
		}
		
		// FORMULES REQUEST
		$credits_rq = "
		SELECT credits.*, users.nom, users.prenom, formules.libelle
		FROM credits
		INNER JOIN users ON users.id = credits.userID
		INNER JOIN formules ON formules.id = credits.formuleID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE credits.formuleID!=0 AND credits.valid=1 AND {$where_rq}";
		
		// SECTEUR
		if(!empty($secteur) AND $secteur!=0){
			$credits_rq .= " AND voies.secteur={$secteur}";
		}		
		
		$credits_rs=mysqli_query($connexion, $credits_rq) or die();
		while($credits=mysqli_fetch_array($credits_rs)){
			
			$dateFrCell = convertDate($credits['dateCreation'],"en2fr");
			$montant = $credits['montant'];
			$montantCell = formatPrice($montant);
			$creditsTotal += $credits['nb'];
			$montantTotal += $montant;

			$formuleCell = $credits['libelle'];				

			
			// FORMULES DETAIL ROW
			$table .= "
			<tr>
				<td>{$credits['dateCreation']}</td>
				<td>{$dateFrCell}</td>
				<td><a href='users.php?userID={$credits['userID']}'>{$credits['nom']} {$credits['prenom']}</a></td>
				<td>{$formuleCell}</td>
				<td class='text-center table-light font-weight-bold'>{$credits['nb']}</td>
				<td class='text-right table-light font-weight-bold'>{$montantCell}</td>
			</tr>";
		}
		
		$montantTotalCell = formatPrice($montantTotal);
		
		// FORMULES DETAIL FOOT
		$table .= "
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th class='text-right' colspan='4'>Total</th>
				<td class='text-center'>{$creditsTotal}</td>
				<td class='text-right'>{$montantTotalCell}</td>
			</tr>
		</tfoot>
		</table>";

		return $table;
		
	}	
	
	/* formulesStats */
	function formulesStats($period, $date, $stat, $statType){
		
		global $connexion;

		// STATS VARIABLES
		switch($stat){
			case "date":
				if($period=="year"){$statThCell = "Mois";}
				else{$statThCell = "Jour";}
			break;
			case "secteur":
				$group_rq="voies.secteur";
				$statThCell = "Secteur";
			break;
			case "formules":
				$group_rq="credits.formuleID";
				$statThCell = "Formule";
			break;
		}
		
		switch($statType){
			case "montant":
				$select_rq = "SUM(credits.montant) ";
			break;
			case "credits":
				$select_rq = "SUM(credits.nb) ";
			break;
		}
		
		// FORMULES STATS HEAD
		$table="
		<table class='table table-sm' id='formules-{$stat}-stats-table'>
		<thead>
			<tr>
				<th>{$statThCell}</th>
				<th>%</th>
				<th>{$statType}</th>
			</tr>
		</thead>
		<tbody>";		
		
		// REQUEST VARIABLES
		switch($period){
			case "year":
				if($stat=="date"){ 
					$group_rq="MONTH(credits.dateCreation)";
				}
				$where_rq = "YEAR(credits.dateCreation) = '{$date}'";
			break;			
			case "month":
				$month = date('m', strtotime($date));
				$year = date('Y', strtotime($date));
				if($stat=="date"){
					$group_rq="DAY(credits.dateCreation)";
				}
				$where_rq = "MONTH(credits.dateCreation) = '{$month}' AND YEAR(credits.dateCreation) = '{$year}'";
			break;
			case "day":
				$where_rq = "credits.dateCreation = '{$date}'";
			break;	
			case "custom":
				$where_rq = "credits.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}'";
			break;
		}
		
		// FORMULES TOTAL REQUEST
		$formulesTotal_rq = "
		SELECT {$select_rq} AS statNb
		FROM credits
		INNER JOIN formules ON formules.id = credits.formuleID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE credits.formuleID!=0 AND credits.valid=1 AND {$where_rq}";		
		$formulesTotal_rs=mysqli_query($connexion, $formulesTotal_rq) or die(mysqli_error($connexion));
		$formulesTotal=mysqli_fetch_array($formulesTotal_rs);
		
		if($statType=="montant"){
			$totalCell = formatPrice($formulesTotal['statNb']);
		}else{
			$totalCell = $formulesTotal['statNb'];
		}
		
		// FORMULES REQUEST
		$formules_rq = "
		SELECT {$select_rq} AS statNb, credits.*, voies.secteur, formules.libelle
		FROM credits
		INNER JOIN formules ON formules.id = credits.formuleID
		INNER JOIN adresses ON adresses.id = credits.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		WHERE credits.formuleID!=0 AND credits.valid=1 AND {$where_rq}
		GROUP BY {$group_rq}";		
		$formules_rs=mysqli_query($connexion, $formules_rq) or die();
		while($formules=mysqli_fetch_array($formules_rs)){
			
			// STAT CELL
			switch($stat){
				case "date":
					if($period=="year"){
						$statCell = "<a href='formules.php?period=month&date=".date('Y-m', strtotime($formules['dateCreation']))."'>".convertDate($formules['dateCreation'], "2bY")."</a>";
					}else{
						$statCell = "<a href='formules.php?period=day&date=".$formules['dateCreation']."'>".convertDate($formules['dateCreation'],"en2fr")."</a>";
					}
				break;
				case "secteur":
					$statCell = "Secteur {$formules['secteur']}";
				break;
				case "formules":
				/*
					if($formules['formuleID']==1){

						$formuleAvg_rq = "SELECT AVG(nb) FROM credits WHERE formuleID=1 AND nb!=0 AND {$where_rq}";
						$formuleAvg_rs=mysqli_query($connexion, $formuleAvg_rq) or die();
						$formuleAvg=mysqli_fetch_array($formuleAvg_rs);
						
						$statCell = "-11 crédits - <small>moy: ".round($formuleAvg[0],1)." crédits</small>";
						
					}else{
						$statCell = $formules['nb']." crédits";
					}
					*/
					$statCell = $formules['libelle'];
					
				break;
			}
			
			$ratioCell = round((100*$formules["statNb"])/$formulesTotal["statNb"],1)."%";
			if($statType=="montant"){
				$statNbCell = formatPrice($formules['statNb']);
			}else{
				$statNbCell = $formules['statNb'];
			}
			
			// FORMULES STATS ROW
			$table.="
			<tr>
				<td>{$statCell}</td>
				<td class='text-center'>{$ratioCell}</td>
				<td class='text-center table-light'><strong>{$statNbCell}</strong></td>
			</tr>";
		}

		// FORMULES STATS FOOT
		$table.="
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th colspan='2' class='text-right'>Total</th>
				<td class='text-center'>{$totalCell}</td>
			</tr>
		</tfoot>
		</table>";

		return $table;		
	}	


	/*****************************************************
		STATS 
	*****************************************************/	
	
	/* statsSection */
	function statsSection($period, $date, $stat, $statType){
		
		switch($period){
			case "year":
				$per = "Mois";	
			break;		
			case "month":
				$per = "Jours";
			break;
		}
		
		$pageStats = $stat."Stats";
		
		$sectionHeader = "
		<div class='h4 mb-3'>
			Stats {$stat}";
		if($stat!="flux"){
			$sectionHeader .= statTypeForm($period, $date, $stat, $statType);
		}
		$sectionHeader .= "
		</div>";
		
		$sectionNav = "<nav><div class='nav nav-tabs nav-fill' id='stats-tab' role='tablist'>";
		$sectionTabs = "<div class='tab-content' id='stats-tabContent'>";
		
		if($period!="custom" && $period!="day"){
			$sectionNav .= "
			<a class='nav-item nav-link active' id='stats-date-tab' data-toggle='tab' href='#stats-date-{$stat}'>{$per}</a>";
			$sectionTabs .="
			<div class='tab-pane show active' id='stats-date-{$stat}'>".
				$pageStats($period, $date, "date", $statType)."
			</div>";
		}

		if($period=="day" || $period=="custom"){
			$secteurClass = "active";
		}

		$sectionNav .= "
		<a class='nav-item nav-link {$secteurClass}' id='stats-secteur-tab' data-toggle='tab' href='#stats-secteur-{$stat}'>Secteurs</a>";
		$sectionTabs .="
		<div class='tab-pane {$secteurClass}' id='stats-secteur-{$stat}'>".
			$pageStats($period, $date, "secteur", $statType)."
		</div>";
		
		if($stat!="flux"){
			$sectionNav .= "
			<a class='nav-item nav-link' id='stats-page-tab' data-toggle='tab' href='#stats-page-{$stat}'>{$stat}</a>";
			$sectionTabs .="
			<div class='tab-pane' id='stats-page-{$stat}'>".
				$pageStats($period, $date, $stat, $statType)."
			</div>";
		}
		$sectionNav .= "</div></nav>";
		$sectionTabs .= "</div>";
		
		$section = $sectionHeader.$sectionNav.$sectionTabs;
		
		return $section;
	}
	
	/* statsSection */
	function statTypeForm($period, $date, $page, $statType){
		
		$formOptions = "";
		
		switch($page){
			case "formules";
				$statTypes = array("montant","credits");
				
			break;
			case "abos";
				$statTypes = array("montant","abos");		
			break;
		}
		
		foreach($statTypes as $value){
			$selected="";
			if($value==$statType){ $selected="selected"; }
			$formOptions.="<option value='{$value}' {$selected}>{$value}</option>";
		}
		
		$form = "
		<form method='get' class='form-inline float-right'>
			<select name='statType' class='form-control form-control-sm mr-1'>
				{$formOptions}
			</select>
			<input name='period' type='hidden' value='{$period}'>
			<input name='date' type='hidden' value='{$date}'>
			<button class='btn btn-secondary btn-sm' type='submit'>OK</button>
		</form>";
		
		return $form;
	}
	
	
	
	
	
	/*****************************************************
		TRASH 
	*****************************************************/	
	
	/* drawCalendar */
	function drawCalendar($month,$year){
		
		global $connexion;
		
		if($month==12){
			$nextMonth =1;
			$nextYear = $year+1;
		}else{
			$nextMonth =$month+1;
			$nextYear = $year;
		}
		if($month==1){
			$prevMonth =12;
			$prevYear = $year-1;
		}else{
			$prevMonth =$month-1;
			$prevYear = $year;
		}
		

		$calendar = '<h2 class="page-header">'.utf8_encode(strftime("%B %Y", strtotime("{$month}/01/{$year}"))).'</h2>';
		
		$calendar.= "
		<div class='btn-group btn-group-sm float-right' style='margin-top:-60px;' role='group' aria-label=''>
			<a href='calendar.php?month=".$prevMonth."&year=".$prevYear."' class='btn btn-secondary'>Précédent</a>
			<a href='calendar.php?month=".$nextMonth."&year=".$nextYear."' class='btn btn-secondary'>Suivant</a>
		</div>";

		/* draw table */
		$calendar .= '<table cellpadding="0" cellspacing="0" class="calendar table table-bordered">';

		/* table headings */
		$headings = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
		$calendar.= '<thead><tr class="calendar-row"><th class="calendar-day-head">'.implode('</th><th class="calendar-day-head">',$headings).'</th></tr></thead>';

		/* days and weeks vars now ... */
		$running_day = date('w',mktime(0,0,0,$month,1,$year)-1);
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		
		
		$secteurs = 21;

		/* row for week one */
		$calendar.= '<tr class="calendar-row">';

		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
			$days_in_this_week++;
		endfor;

		/* keep going with days.... */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		
			$date = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($day_counter+1));
			if(date("Y-m-d")==$date){
				$calendar.= '<td class="calendar-day calendar-day-now">';
			}else{
				$calendar.= '<td class="calendar-day">';
			}
		
			
				/* add in the day number */
				$calendar.= '<div class="day-number">'.$list_day.'</div>';

				/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
				$calendar.= str_repeat('<p> </p>',2);
				
				/*
				if($running_day < 5 && $secteurs_month<$secteurs):
					$secteurs_month ++;
					$calendar.= '<small>secteur'.$secteurs_month.'</small><br/>';
				endif;
				*/

				$date = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($day_counter+1));
				$calDay_rq = "SELECT * FROM cal WHERE date='".$date."'";
				$calDay_rs = mysqli_query($connexion, $calDay_rq) or die();
				$calDay_nb=mysqli_num_rows($calDay_rs);
				if($calDay_nb){
					
					$sacsTotal = 0;
					$picksTotal = 0;
					$secteurs = "";
					
					while($calDay=mysqli_fetch_array($calDay_rs)){
						
						$secteurs.=$calDay['secteur'].",";

						$picks_rq = "SELECT id, sacs FROM picks WHERE calID={$calDay['id']}";
						$picks_rs = mysqli_query($connexion, $picks_rq) or die();
						$picks_nb = mysqli_num_rows($picks_rs);
						
						$picksTotal+=$picks_nb;
						
						while($picks=mysqli_fetch_array($picks_rs)){
							$sacsTotal += $picks['sacs'];
						}
					}

					$calendar.= "<a href='cal.detail.php?calDate={$date}'>secteur {$secteurs}</a><br/><strong>{$picksTotal}</strong> ramassage(s)<br/><strong>{$sacsTotal}</strong> sacs";
				}
				
				

			$calendar.= '</td>';
			
			
			if($running_day == 6):
				$calendar.= '</tr>';
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			
			
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;

		/* finish the rest of the days in the week */
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
			endfor;
		endif;

		/* final row */
		$calendar.= '</tr>';

		/* end the table */
		$calendar.= '</table>';
		

		
		
		/* all done, return result */
		return $calendar;
	}
		
	/* pickerCalendar */
	function pickerCalendar($pickerID,$month,$year){
		
		global $connexion;
		
		if($month==12){
			$nextMonth =1;
			$nextYear = $year+1;
		}else{
			$nextMonth =$month+1;
			$nextYear = $year;
		}
		if($month==1){
			$prevMonth =12;
			$prevYear = $year-1;
		}else{
			$prevMonth =$month-1;
			$prevYear = $year;
		}
		

		$calendar = '<h3>Calendrier '.utf8_encode(strftime("%B %Y", strtotime("{$month}/01/{$year}"))).'</h3>';
		
		$calendar.= "
		<div class='btn-group btn-group-sm float-right' style='margin-top:-60px;' role='group' aria-label=''>
			<a href='picker.detail.php?pickerID={$pickerID}&month={$prevMonth}&year={$prevYear}' class='btn btn-secondary'>Précédent</a>
			<a href='picker.detail.php?pickerID={$pickerID}&month={$nextMonth}&year={$nextYear}' class='btn btn-secondary'>Suivant</a>
		</div>";

		/* draw table */
		$calendar .= '<table cellpadding="0" cellspacing="0" class="calendar table table-bordered">';

		/* table headings */
		$headings = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
		$calendar.= '<thead><tr class="calendar-row"><th class="calendar-day-head">'.implode('</th><th class="calendar-day-head">',$headings).'</th></tr></thead>';

		/* days and weeks vars now ... */
		$running_day = date('w',mktime(0,0,0,$month,1,$year)-1);
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		/* row for week one */
		$calendar.= '<tr class="calendar-row">';

		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
			$days_in_this_week++;
		endfor;

		/* keep going with days.... */
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
			
			$tdClass = "";
			$date = $year."-".sprintf("%02d", $month)."-".sprintf("%02d", ($day_counter+1));
			
			// CAL INFOS
			$cal_rq = "SELECT id FROM cal WHERE date='".$date."'";
			$cal_rs = mysqli_query($connexion, $cal_rq) or die();
			$cal_nb = mysqli_num_rows($cal_rs);
			if($cal_nb){
				$cal = mysqli_fetch_array($cal_rs);
			}

			$pickerCalDay_rq = "SELECT * FROM pickersCal WHERE pickerID={$pickerID} AND calID={$cal['id']}";
			$pickerCalDay_rs = mysqli_query($connexion, $pickerCalDay_rq) or die();
			$pickerCalDay_nb=mysqli_num_rows($pickerCalDay_rs);
			if($pickerCalDay_nb){
				$tdClass .= "table-success ";
			}
			if(date("Y-m-d")==$date){
				$tdClass .= "calendar-day-now ";
			}
			
			$calendar.= "<td class='calendar-day {$tdClass}'>";
			
				/* add in the day number */
				$calendar.= '<div class="day-number">'.$list_day.'</div>';

				/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
				$calendar.= str_repeat('<p> </p>',2);
				
			$calendar.= '</td>';
			
			
			if($running_day == 6):
				$calendar.= '</tr>';
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			
			
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;

		/* finish the rest of the days in the week */
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np"> </td>';
			endfor;
		endif;

		/* final row */
		$calendar.= '</tr>';

		/* end the table */
		$calendar.= '</table>';
		

		
		
		/* all done, return result */
		return $calendar;
	}	
	
	/* _abosTable*/
	function _abosTable($dateFrom, $dateTo, $secteur){
		
		global $connexion;
		
		if(strtotime($dateTo) > date("Y-m-d")){
			$dateTo = date("Y-m-d");
		}
	
		$table = "";
		$table .= "
		<table class='table table-hover table-sm' id='abosTable' style='width:100%'>
		<thead>
			<tr>
				<th>Date (eng)</th>
				<th>Date</th>
				<th>Nom</th>
				<th>Secteur</th>
			</tr>
		</thead>
		<tbody>";
		
		// ABOS NEW
		$abosNew_rq = "SELECT abos.id, abos.dateCreation, abos.userID, users.nom, users.prenom, voies.secteur
					FROM abos
					INNER JOIN users ON users.id = abos.userID
					INNER JOIN voies ON users.voieID = voies.id
					WHERE abos.dateCreation BETWEEN '{$dateFrom}' AND '{$dateTo}'";
					
		if(!empty($secteur) AND $secteur!=0){
			$abosNew_rq .= " AND voies.secteur={$secteur}";
		}						

		$abosNew_rs=mysqli_query($connexion, $abosNew_rq) or die();
		while($abosNew=mysqli_fetch_array($abosNew_rs)){
			
			$trClass = "table-default";	
			$dateCreationFr = convertDate($abosNew['dateCreation']);
			
			$abosPrev_rq = "SELECT id FROM abos WHERE dateCreation < '".$abosNew['dateCreation']."' AND userID={$abosNew['userID']}";
			$abosPrev_rs=mysqli_query($connexion, $abosPrev_rq) or die();
			$abosPrev_nb=mysqli_num_rows($abosPrev_rs);
			if($abosPrev_nb!=0){
				$trClass = "table-success";
			}	
			$table .= "
			<tr class='clickable-row {$trClass}'>
				<td>{$abosNew['dateCreation']}</td>
				<td>{$dateCreationFr}</td>
				<td><a href='users.php?userID={$abosNew['userID']}'>{$abosNew['nom']} {$abosNew['prenom']}</a></td>
				<td>{$abosNew['secteur']}</td>
			</tr>";
		}
		$table .= "
		</tbody>
		</table>";
		
		return $table;		
	
	}
	
	/* pickTableCal */
	function pickTableCal($date){
		
		global $connexion;
		
		$sacProgTotal = 0;
		$sacPickTotal = 0;
		
		$table="";
		$table.="
		<table class='table table-sm'>
			<thead>
				<tr>
					<th>Horaire</th>
					<th>Abonné</th>
					<th>Adresse</th>
					<th>Sac(s) prog.</th>
					<th>Sac(s) col.</th>
				</tr>
			</thead>
			<tbody>";
		
		$cal_rq = "SELECT * FROM cal WHERE date='".$date."'";
		$cal_rs = mysqli_query($connexion, $cal_rq) or die();
		$cal = mysqli_fetch_array($cal_rs);
		
		$pick_rq = "
		SELECT picks.*, users.nom, users.prenom, users.voieNumero, voies.voieType, voies.voieLibelle, slots.start, slots.end, users.id AS userID
		FROM picks
		INNER JOIN users ON picks.userID = users.id
		INNER JOIN adresses ON adresses.userID = picks.userID AND adresses.active=1
		INNER JOIN voies ON voies.id = adresses.voieID
		INNER JOIN slots ON picks.slotID = slots.id
		WHERE picks.calID={$cal['id']} ORDER BY picks.slotID";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		while($pick = mysqli_fetch_array($pick_rs)){
			
			$sacProgCell = $pick['sacs'];
			$sacProgTotal += $pick['sacs'];
			$pickTime = $date." ".$pick['end'];
				
			if($pickTime<=date("Y-m-d H:i")){
				$picked_rq = "SELECT * FROM collects WHERE pickID={$pick['id']}";
				$picked_rs = mysqli_query($connexion, $picked_rq) or die();
				$picked_nb = mysqli_num_rows($picked_rs);
				if($picked_nb){
					$picked = mysqli_fetch_array($picked_rs);
					$pickTrClass = "table-success";
					$sacPickCell = $picked['sacs'];
					$sacPickTotal += $picked['sacs'];
					
				}else{
					$pickTrClass = "table-danger";
					$sacPickCell = 0;
					$sacCell = $pick['sacs'];
				}
			} else{
				$pickTrClass = "table-default";
				$sacCell = $pick['sacs'];
			}

			$table.="
			<tr class='{$pickTrClass}'>
				<td>{$pick['start']}/{$pick['end']}</td>
				<td><a href='usersDetail.php?userID={$pick['userID']}'>{$pick['nom']} {$pick['prenom']}</a></td>
				<td>{$pick['voieNumero']} {$pick['voieType']} {$pick['voieLibelle']}</td>
				<td class='table-light font-weight-bold'>{$sacProgCell}</td>
				<td class='table-light font-weight-bold'>{$sacPickCell}</td>
			<tr>";
		}
		
		$table.="
		</tbody>
		<tfoot class='table-dark'>
			<tr>
				<th colspan='3'>Total</th>
				<td>{$sacProgTotal}</td>
				<td>{$sacPickTotal}</td>
			</tr>
		<tfoot>
		</table>";
		
		return $table;

	}	
	
	/* displayAlert */
	function displayAlert($action){
		
		if(!empty($action)){
		
			$alertClass="success";
			$alertContent="";
			
			switch($action){
				
				// USER
				case "user_create":
					$alertContent = "Le compte a bien été créé";
				break;
				case "user_update":
					$alertContent = "Le compte a bien été modifié";
				break;
				case "user_delete":
					$alertContent = "Le compte a bien été supprimé";
				break;
				case "user_errForm": 
					$alertContent = "Erreur formulaire - Veuillez remplir les champs obligatoire";	
					$alertClass="danger";
				break;
				case "user_errMail": 
					$alertContent = "Erreur formulaire - L'email saisi existe déjà";	
					$alertClass="danger";
				break;						
				case "user_errAction": 
					$alertContent = "Erreur action - Paramètres manquants";	
					$alertClass="danger";
				break;		
				
				// PICK
				case "pick_create":
					$alertContent = "Le ramassage a bien été programmé";
				break;
				case "pick_update":
					$alertContent = "Le ramassage a bien été modifié";
				break;
				case "pick_delete":
					$alertContent = "Le ramassage a bien été annulé";	
				break;
				case "pick_errCredits": 
					$alertContent = "Erreur crédits - Veuillez sélectionner un autre nombre de sacs";	
					$alertClass="danger";
				break;
				case "pick_errForm": 
					$alertContent = "Erreur formulaire - Veuillez remplir les champs obligatoire";	
					$alertClass="danger";
				break;
				case "pick_errLoad": 
					$alertContent = "Erreur chargement";	
					$alertClass="danger";
				break;
				case "pick_errAction": 
					$alertContent = "Erreur action - Paramètres manquants";	
					$alertClass="danger";
				break;	
				
				// ABO
				case "abo_new":
					$alertContent = "L'abonnement a bien été renouvelé";
				break;
				case "abo_credit":
					$alertContent = "Le compte a bien été crédité";	
				break;
				case "abo_errForm": 
					$alertContent = "Erreur formulaire - Veuillez remplir les champs obligatoire";	
					$alertClass="danger";
				break;
				case "abo_errAction": 
					$alertContent = "Erreur action - Paramètres manquants";	
					$alertClass="danger";
				break;	


				// voies
				case "voie_create":
					$alertContent = "La voie a bien été créée";
				break;
				case "voie_update":
					$alertContent = "La voie a bien été modifiée";
				break;
				case "voie_delete":
					$alertContent = "La voie a bien été supprimée";
				break;
				case "voie_errForm": 
					$alertContent = "Erreur formulaire - Veuillez remplir les champs obligatoire";	
					$alertClass="danger";
				break;					
				case "voie_errAction": 
					$alertContent = "Erreur action - Paramètres manquants";	
					$alertClass="danger";
				break;				
				
			}
			
			$alert = "
			<div class='main-alert alert alert-".$alertClass."'>
				<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
				<strong>".$alertContent."</strong>
			</div>";
			
			return($alert);
		}
	}	
	

		
	/* voiesTable*/
	function voiesTable(){
		
		global $connexion;
		
	
		$table = "";
		$table .= "
		<table class='table table-hover table-sm voiesTable' id='voiesTable' style='width:100%'>
		<thead>
			<tr>
				<th>Voie</th>
				<th>Secteur</th>
				<th></th>
			</tr>
		</thead>
		<tbody>";
		


		$voies_rq = "SELECT * FROM voies";
		$voies_rs = mysqli_query($connexion, $voies_rq) or die();
		while($voies = mysqli_fetch_array($voies_rs)){


			$table .= "
			<tr>
				<td>{$voies['voieType']} {$voies['voieLibelle']}</td>
				<td>{$voies['secteur']}</td>
				<td>
					<div class='btn-group dropleft'>
					  <button type='button' class='btn btn-sm btn-default dropdown-toggle' data-toggle='dropdown'></button>
					  <div class='dropdown-menu'>
						  <a href='voies.edit.php?action=update&voieID={$voies['id']}' class='dropdown-item' data-toggle='modal' data-target='#edit_lightbox'>modifier</a>
						  <a href='voies.edit.php?action=delete&voieID={$voies['id']}' class='dropdown-item' data-toggle='modal' data-target='#edit_lightbox'>supprimer</a>
					  </div>
					</div>
				</td>
			</tr>";
			
		}
		
		$table .= "
		</tbody>
		</table>";
		
		return $table;
		
	}	
		
?>