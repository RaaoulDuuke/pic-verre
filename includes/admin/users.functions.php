<?php

/* usersPageBreadcrumb */
function usersPageBreadcrumb($state, $secteur, $userID){
	
	global $connexion;
	
	if(empty($userID)){

		if($secteur==0){
			if($state!="tous"){
				$bread = "
				<li class='breadcrumb-item'><a href='users.php?state=tous'>Abonnés</a></li>
				<li class='breadcrumb-item active'>Abo. {$state}</li>";
			}else{
				$bread = "<li class='breadcrumb-item active'>Abonnés</li>";
			}
		}else{
			if($state!="tous"){
				$bread = "
				<li class='breadcrumb-item'><a href='users.php?state=tous'>Abonnés</a></li>
				<li class='breadcrumb-item'><a href='users.php?state={$state}'>Abo. {$state}</a></li>";
			}else{
				$bread = "
				<li class='breadcrumb-item'><a href='users'>Abonnés</a></li>";
			}
			$bread .= "
			<li class='breadcrumb-item active'>Secteur {$secteur}</li>";
		}
		
	}else{		
		$bread = "
		<li class='breadcrumb-item'><a href='users.php?state=tous'>Abonnés</a></li>
		<li class='breadcrumb-item active text-capitalize' aria-current='page'>".userName($userID)."</li>";
	}					
	
	return "
	<nav>
		<ol class='breadcrumb m-0 fixed-top rounded-0'>
			{$bread}
		</ol>
	</nav>";
	
}

/* usersPageHeader */
function usersPageHeader($state, $secteur, $userID){
	
	global $connexion;
	
	if(empty($userID)){
		
		$header = "Abonnés ";
		
		if($state!="tous"){ 
			$header = "Abo. ".$state;
		}
		
		if($secteur!=0){ 
			$header .=  " <em class='d-block'>Secteur {$secteur}</em>";
		}
		
		$pageHeader = "
		<div class='page-header d-flex'>					
			<h2 class='mr-auto'>{$header}</h2>
		</div>";
		
		
	}else{
		
		if(userPro($userID)){
			$badge = "<span class='badge badge-warning'>Pro</span>";
		}
		
		$pageHeader = "
		<div class='page-header d-flex'>					
			<h2 class='mr-auto'>".userName($userID)." {$badge}</h2>
		</div>";
	}
	
	return $pageHeader;
}

/* usersPage */
function usersPage($state, $secteur){
	
	global $connexion;
	
	$mainContent = usersTab($state, $secteur);
		
	$sideContent .= usersStats($state, $secteur);
	$sideContent .= usersStatsSct($state, $secteur);
		
	$content = "
	<div class='row'>
		<div class='col-sm-4'>
			<button data-edit='user' data-rq='action=create' data-toggle='modal' data-target='#editModal' class='btn btn-warning btn-block mb-3'>Ajouter un utilisateur</button> 
			{$sideContent}
		</div>	
		<div class='col-sm-8'>
			{$mainContent}
		</div>
	</div>";
		
	return $content;
		
}

/* usersTab */
function usersTab($state, $secteur){
	
	global $connexion;
	
	if(!empty($secteur) AND $secteur!=0){
		$whereSct_rq .= " AND voies.secteur={$secteur}";
	}
	
	// USERS ACTIVE
	$usersActive_rq="
	SELECT users.*, voies.secteur, 'active' AS state
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE 
	
	EXISTS(
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		INNER JOIN transactions ON transactions.id = orders.tID
		WHERE users.id=orders.userID) 
		
		{$whereSct_rq}
	";	
	

	// USERS INACTIVE
	$usersInactive_rq="
	SELECT users.*, voies.secteur, 'inactive' AS state
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE NOT EXISTS (
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		WHERE users.id=orders.userID AND orders.tID!=0) 
		
		{$whereSct_rq}

	";
		
	
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
	
	$usersPro_rq = $users_rq . " AND users.societe!='' ";
	$usersPro_rs=mysqli_query($connexion, $usersPro_rq) or die(mysqli_error($connexion));	
	$usersPro_nb=mysqli_num_rows($usersPro_rs);
	
	$usersPar_rq = $users_rq . " AND users.societe='' ";
	$usersPar_rs=mysqli_query($connexion, $usersPar_rq) or die(mysqli_error($connexion));	
	$usersPar_nb=mysqli_num_rows($usersPar_rs);

	$usersParTable = usersTable($state, $secteur);
	$usersProTable = usersTable($state, $secteur,1);
	
	if($state!="tous"){

		$tab = "
		<nav>
			<div class='nav nav-tabs nav-fill' id='orders-tab' role='tablist'>
				<a class='nav-item nav-link active' data-toggle='tab' href='#usersPar'>Particuliers <span class='badge badge-warning font-weight-bold'>{$usersPar_nb}</span></a>
				<a class='nav-item nav-link' data-toggle='tab' href='#usersPro'>Professionnels <span class='badge badge-warning font-weight-bold'>{$usersPro_nb}</span></a>
			</div>
		</nav>
		<div class='tab-content' id='stats-tabContent'>
			<div class='tab-pane active' id='usersPar'>
				{$usersParTable}	
			</div>
			<div class='tab-pane'  id='usersPro'>
				{$usersProTable}	
			</div>
		</div>";
	
	}else{
		
		$tab = usersTable("tous", $secteur);
	}
	
	return $tab;
	
}

/* usersTable */
function usersTable($state, $secteur, $pro){
	
	global $connexion;
	
	// REQUEST VARIABLES
	if(!empty($secteur) AND $secteur!=0){
		$whereSct_rq .= " AND voies.secteur={$secteur}";
	}
	
	if($state!="tous"){
		
		if(!empty($pro)){
			$wherePro_rq .= " AND users.societe!=''";
			$usersType = "pro";
		}else{
			$wherePro_rq .= " AND users.societe=''";
			$usersType = "par";
		}
		
	}
	

	// USERS ACTIVE
	$usersActive_rq="
	SELECT users.*, voies.secteur, 'active' AS state
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE 
	
	EXISTS(
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		INNER JOIN transactions ON transactions.id = orders.tID
		WHERE users.id=orders.userID) 
		
		{$whereSct_rq}
		{$wherePro_rq}
	";	
	

	// USERS INACTIVE
	$usersInactive_rq="
	SELECT users.*, voies.secteur, 'inactive' AS state
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE NOT EXISTS (
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		WHERE users.id=orders.userID AND orders.tID!=0) 
		
		{$whereSct_rq}
		{$wherePro_rq}
	";
		
	
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
		
		$date_td = convertDate($users['dateCreation']);
		$user_td = userlink($users["id"]);
		$credits_td =  userCredits($users["id"]);
		$secteur_td =  userSecteur($users["id"]);
		
		$creditsTotal += userCredits($users["id"]);
				
		$tr_class="";

		if(userCredits($users["id"])==0&&$users['state']=="active"){
			$tr_class="table-warning";
		}
		
		if($users['state']=="inactive" && $state=="tous"){
			$tr_class="table-secondary";
		}

		// USERS DETAIL RAW
		$tbody .= "
		<tr class='{$tr_class}'>
			<td class='align-middle'>{$users['dateCreation']}</td>
			<td style='font-weight:500;' class='align-middle'>{$date_td}</td>
			<td class='align-middle'>{$user_td}</a></td>
			<td class='text-center align-middle'>{$secteur_td}</td>
			<td class='align-middle table-light font-weight-bold text-center'>{$credits_td}</td>
		</tr>";			
	}

	$table = "
	<div class='table-responsive'>
	<table class='table table-hover font-weight-bold' style='margin-top:0!important' id='usersTable{$usersType}'>
	<thead>
		<tr>
			<th scope='col'>Date (en)</th>
			<th scope='col' style='width:65px'>Date</th>
			<th scope='col'>Nom</th>
			<th scope='col' class='text-center' style='width:80px'>Sct</th>
			<th scope='col' class='text-center' style='width:80px'>Crd</th>
		</tr>
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	<tfoot>
		<tr>
			<th scope='row' class='text-right bg-dark' colspan='4' style='font-size:1rem;'>Total</td>
			<td class='text-center bg-warning font-weight-bold' style='font-size:1rem;'>{$credits_total}</td>
		</tr>
	</tfoot>
	</table>
	</div>";
	
	return $table;

}	

/* usersStats */
function usersStats($state, $secteur){
	
	global $connexion;

	if($secteur!=0){
		$whereSct_rq = " AND voies.secteur={$secteur}";
	}
	
	// USERS ACTIVE
	$usersActive_rq="
	
	SELECT COUNT(*) as nb
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE 
	
	EXISTS(
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		INNER JOIN transactions ON transactions.id = orders.tID
		WHERE users.id=orders.userID) 
		{$whereSct_rq} ";
	$usersActive_rs=mysqli_query($connexion, $usersActive_rq) or die();
	$usersActive=mysqli_fetch_array($usersActive_rs);
	
	if($usersActive['nb']){
		
		$userTotal+=$usersActive['nb'];

		$usersActivePro_rq = $usersActive_rq." AND users.societe!='' ";
		$usersActivePro_rs=mysqli_query($connexion, $usersActivePro_rq) or die();
		$usersActivePro=mysqli_fetch_array($usersActivePro_rs);
		
		$usersActivePri_rq = $usersActive_rq." AND users.societe=''";
		$usersActivePri_rs=mysqli_query($connexion, $usersActivePri_rq) or die();
		$usersActivePri=mysqli_fetch_array($usersActivePri_rs);
		
		$trClass = "";
		$tdClass = "";
		$textClass = "";
		if($state=="actifs"){
			$trClass="bg-dark";
			$tdClass="bg-warning";
			$textClass = "text-warning";
		}
			
		$userAbo_tr = "
		<tr>
			<td class='{$trClass}'><a href='users.php?state=actifs' class='{$textClass}'>Actifs</a></td>
			<td class='text-center {$trClass}'>{$usersActivePri['nb']}</td>
			<td class='text-center {$trClass}'>{$usersActivePro['nb']}</td>
			<td class='font-weight-bold text-center {$tdClass}'>{$usersActive['nb']}</td>
		</tr>";
	}
	
	

	// USERS INACTIVE
	$usersInactive_rq="
	
	SELECT COUNT(*) as nb
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE 
	
	NOT EXISTS(
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		INNER JOIN transactions ON transactions.id = orders.tID
		WHERE users.id=orders.userID) 
		{$whereSct_rq} ";
	$usersInactive_rs=mysqli_query($connexion, $usersInactive_rq) or die();
	$usersInactive=mysqli_fetch_array($usersInactive_rs);
	
	if($usersInactive['nb']){
		
		$userTotal+=$usersInactive['nb'];
		
		$usersInactivePro_rq = $usersInactive_rq." AND users.societe!='' ";
		$usersInactivePro_rs=mysqli_query($connexion, $usersInactivePro_rq) or die();
		$usersInactivePro=mysqli_fetch_array($usersInactivePro_rs);
		
		$usersInactivePri_rq = $usersInactive_rq." AND users.societe=''";
		$usersInactivePri_rs=mysqli_query($connexion, $usersInactivePri_rq) or die();
		$usersInactivePri=mysqli_fetch_array($usersInactivePri_rs);
		
		$trClass = "";
		$tdClass = "";
		$textClass = "";
		if($state=="inactifs"){
			$trClass="bg-dark";
			$tdClass="bg-warning";
			$textClass = "text-warning";
		}
			
		$userNoAbo_tr = "
		<tr>
			<td class='{$trClass}'><a href='users.php?state=inactifs'  class='{$textClass}'>Inactifs</a></td>
			<td class='text-center {$trClass}'>{$usersInactivePri['nb']}</td>
			<td class='text-center {$trClass}'>{$usersInactivePro['nb']}</td>
			<td class='font-weight-bold text-center {$tdClass}'>{$usersInactive['nb']}</td>
		</tr>";
	}
	
	if($state!="tous"){
		$tfootClass="d-none";
	}
	
	// USERS COUNT TABLE
	$table = "
		<table class='table table-sm font-weight-bold'>
			<thead>
				<tr>
					<th>Abonnés</th>
					<th class='text-center'>Ind.</th>
					<th class='text-center'>Pro.</th>
					<th class='text-center'>Nb</th>
				</tr>
			</thead>
			<tbody>
				{$userAbo_tr}
				{$userNoAbo_tr}
			</tbody>
			<tfoot class='{$tfootClass}'>
			<tr>
				<th class='text-right bg-dark' colspan='3'>Total</th>
				<td class='bg-warning font-weight-bold text-center'>{$userTotal}</td>
			</tr>
			</tfoot>
		</table>
	";
		
	return $table;
	
}

/* usersStatsSct */
function usersStatsSct($state, $secteur){
	
	global $connexion;
	
	// REQUEST VARIABLES
	if($secteur==0){
		$stat_thCell = "Secteur";
		$group_rq = "secteur";
		
	}else{
		$stat_thCell = "Voie";
		$whereSct_rq = " AND voies.secteur={$secteur}";
		$group_rq = "id";
		
	}
	

	// USERS ACTIVE REQUEST
	$usersAbo_rq="
	SELECT users.id AS userID, users.societe, voies.id, voies.secteur, voies.voieType, voies.voieLibelle
	
	FROM users
	
		INNER JOIN adresses ON adresses.id = users.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		
		WHERE 
		
		EXISTS(
			SELECT orders.id FROM orders
			INNER JOIN credits ON credits.orderID = orders.id
			INNER JOIN transactions ON transactions.id = orders.tID
			WHERE users.id=orders.userID) 
			
			{$whereSct_rq}
		
	";
	$usersAbo_rs=mysqli_query($connexion, $usersAbo_rq) or die();
	$usersAbo_nb=mysqli_num_rows($usersAbo_rs);
	
	$usersActivePro_rq = $usersAbo_rq." AND users.societe!='' ";
	$usersActivePri_rq = $usersAbo_rq." AND users.societe='' ";

	// USERS NO ABO
	$usersNoAbo_rq="
	SELECT users.id AS userID, users.societe, voies.id, voies.secteur, voies.voieType, voies.voieLibelle
	
	FROM users
	
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	
	WHERE NOT EXISTS (
		SELECT orders.id FROM orders
		INNER JOIN credits ON credits.orderID = orders.id
		WHERE users.id=orders.userID AND orders.tID!=0) 
		
	{$whereSct_rq}
		
	";
	$usersNoAbo_rs=mysqli_query($connexion, $usersNoAbo_rq) or die();
	$usersNoAbo_nb=mysqli_num_rows($usersNoAbo_rs);
	
	
	
	// USERS REQUEST
	switch($state){			
		case "tous":
			$usersTotal_rq = "{$usersAbo_rq} UNION {$usersNoAbo_rq}";
			$usersTotal = $usersAbo_nb+$usersNoAbo_nb;
		break;
		case "actifs":
			$usersTotal_rq = $usersAbo_rq;
			$usersTotal = $usersAbo_nb;
		break;			
		case "inactifs":
			$usersTotal_rq = $usersNoAbo_rq;	
			$usersTotal = $usersNoAbo_nb;
		break;
	}

	$users_rq = "
	SELECT COUNT(userID) AS nb, id, secteur, voieType, voieLibelle
	FROM ({$usersTotal_rq}) AS aboAll
	GROUP BY {$group_rq}";
	
	$users_rs=mysqli_query($connexion, $users_rq) or die(mysqli_error($connexion));
	while($users=mysqli_fetch_array($users_rs)){
		
		
		if($secteur==0){
			
			$stat_cell = "<a href=users.php?secteur={$users['secteur']}&state={$state}>Secteur {$users['secteur']}</a>";
			
			$usersPro_rq = "
			SELECT COUNT(userID) AS nb
			FROM ({$usersTotal_rq}) AS aboAll
			WHERE societe!='' AND secteur={$users['secteur']}";
			
			$usersPri_rq = "
			SELECT COUNT(userID) AS nb
			FROM ({$usersTotal_rq}) AS aboAll
			WHERE societe='' AND secteur={$users['secteur']}";

			
		}else{
			
			$stat_cell = "{$users['voieType']} {$users['voieLibelle']}";
			
			$usersPro_rq = "
			SELECT COUNT(userID) AS nb
			FROM ({$usersTotal_rq}) AS aboAll
			WHERE societe!='' AND id={$users['id']}";
			
			$usersPri_rq = "
			SELECT COUNT(userID) AS nb
			FROM ({$usersTotal_rq}) AS aboAll
			WHERE societe='' AND id={$users['id']}";
			
		}
		
		
		
		$usersPro_rs=mysqli_query($connexion, $usersPro_rq) or die(mysqli_error($connexion));
		$usersPro=mysqli_fetch_array($usersPro_rs);
		
		$usersPri_rs=mysqli_query($connexion, $usersPri_rq) or die(mysqli_error($connexion));
		$usersPri=mysqli_fetch_array($usersPri_rs);
		
		$ratio_cell = round((100*$users["nb"])/$usersTotal,1)."%";
		
		$usersParNb_cell = "-";
		if($usersPri['nb']){
			$usersParNb_cell = $usersPri['nb'];
		}
		$usersProNb_cell = "-";
		if($usersPro['nb']){
			$usersProNb_cell = $usersPro['nb'];
		}
		
		
		// USERS STATS ROW
		$tbody .= "
		<tr>
			<td style='font-weight:500;'>{$stat_cell}</td>
			<td class='text-center'>{$usersParNb_cell}</td>
			<td class='text-center'>{$usersProNb_cell}</td>
			<td class='table-light font-weight-bold text-center'>{$users['nb']}</td>
		</tr>";
	}
	
	// USERS STATS TFOOT
	$table = "
	<table class='table table-sm font-weight-bold' id='secteur-stats-table'>
	<thead>
		<tr>
			<th>{$stat_thCell}</th>
			<th>Ind.</th>
			<th>Pro.</th>
			<th class='text-center'>Nb</th>
		</tr>
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	<tfoot class=''>
		<tr>
			<th class='text-right bg-dark' colspan='3'>Total</td>
			<td class='text-center font-weight-bold bg-warning'>{$usersTotal}</td>
		</tr>
	</tfoot>
	</table>";

	return $table;
}


?>