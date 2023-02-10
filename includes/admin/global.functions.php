<?php

/* pageTitle*/
function pageTitle($page){
	
	switch($page){
		case "picks":
			return "collectes";
		break;
		case "orders":
			return "commandes";
		break;
	}
	
}

/* sidebarNav */
function sidebarNav($page){

	switch ($page){
		case "dash":
			$dashClass = " active";
		break;
		case "users":
			$usersClass = " active";
		break;
		case "picks":
			$picksClass = " active";
		break;
		case "pickers":
			$pickersClass = " active";
		break;
		case "voies":
			$voiesClass = " active";
		break;
		case "orders":
			$ordersClass = " active";
		break;
		case "resellers":
			$resellersClass = " active";
		break;
	}

	$nav ="
	<div id='sidebar-wrapper' class='position-fixed bg-dark'>
		<div class='sidebar sidebar-nav'>
			<ul class='nav flex-column'>
				<li class='nav-item'><a class='nav-link {$dashClass}' href='index.php'>Tableau de bord</a></li>
				<li class='nav-item'><a class='nav-link {$picksClass}' href='picks.php'>Collectes</a></li>
				<li class='nav-item'><a class='nav-link {$ordersClass}' href='orders.php'>Commandes</a></li>
				<li class='nav-item'><a class='nav-link {$usersClass}' href='users.php'>Abonnés</a></li>
				<li class='nav-item'><a class='nav-link {$resellersClass}' href='resellers.php'>Revendeurs</a></li>
				<li class='nav-item'><a class='nav-link {$voiesClass}' href='voies.php'>Voies</a></li>
			</ul>
		</div>
	</div>";

	return $nav;
}



/* dateBreadcrumb */
function dateBreadcrumb($period, $date, $week, $page){
	
	$pageTitle = pageTitle($page);
	
	$breadcrumb = "
	<nav aria-label='breadcrumb'>
		<ol class='breadcrumb mb-0 fixed-top rounded-0'>
			<li class='breadcrumb-item'><a href='{$page}'>".ucfirst($pageTitle)."</a></li>";
	
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
		case "week":
			$breadcrumb.= "
			<li class='breadcrumb-item' aria-current='page'><a href='?period=year&date={$date}'>{$date}</a></li>
			<li class='breadcrumb-item active' aria-current='page'>Semaine ".$week."</li>";
		break;
		case "day":
			$month = date('m', strtotime($date));
			$year = date('Y', strtotime($date));
			$week = date('W', strtotime($date));
			$date_rq = $year."-".$month;
			$breadcrumb.= "
			<li class='breadcrumb-item' aria-current='page'><a href='?period=year&date={$year}'>{$year}</a></li>
			<li class='breadcrumb-item' aria-current='page'><a href='?period=week&date={$year}&week={$week}'>Semaine {$week}</a></li>
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
function datePageHeader($period, $date, $week,  $page){
	
	switch($period){
		case "year":
			$dateTitle = $date;		
		break;		
		case "month":
			$dateTitle = convertDate($date,"2BY");
		break;	
		case "week":
			$date_string = $date . 'W' . sprintf('%02d', $week);
			$dateStart = date('Y-m-d', strtotime($date_string));
			$dateEnd = date('Y-m-d', strtotime($date_string . '5'));
			$dateStart = convertDate($dateStart,"2dbY");
			$dateEnd = convertDate($dateEnd,"2dbY");
			$dateTitle = "{$dateStart} - {$dateEnd}";
		break;
		case "day":
			$dateTitle = convertDate($date,"2adbY");
		break;			
		case "custom":
			$dateFrom = (!empty($_REQUEST["dateFrom"])) ? $_REQUEST["dateFrom"] : date("d-m-Y");
			$dateTo = (!empty($_REQUEST["dateTo"])) ? $_REQUEST["dateTo"] : date("d-m-Y");
			$dateTitle = "du {$dateFrom} au {$dateTo}";
			$dateFrom = convertDate($dateFrom,"fr2en");
			$dateTo = convertDate($dateTo,"fr2en");	
			$date = array($dateFrom, $dateTo);	
		break;
	}
	
	$noweekend = 0;
	$navEnd = 0;
	
	if($period!="custom"){
		
		if($page=="picks"&&$period=="day"){
			$noweekend = 1;
		}
		
		if( (   ($period=="month"&&$date==date('Y-m'))   ||  ($period=="year"&&$date==date('Y'))    )   &&$page=="orders"){
			$navEnd = 1;
		}
		
		
		
		$pageNav = dateNav($period, $date, $week, $noweekend, $navEnd);
	}
	
	$title = pageTitle($page);
		
	$pageHeader = "
	<div class='page-header d-flex'>
		<h2 class='mr-auto'>{$title} <em class='d-block'>{$dateTitle}</em></h2>
		<div class='page-nav mt-auto mb-2'>
			{$pageNav}
		</div>
	</div>";
	
	return $pageHeader;
	
}

/* dateNav */
function dateNav($period, $date, $week, $noweekend, $navEnd){
	
	$noweekend = (!empty($noweekend)) ? $noweekend : 0;
	
	$dateInterNext = "1";
	$dateInterPrev = "1";
	
	switch($period){
		
		case 'day':			
			$dateConversion = "2dbY";
			$dateFormat = "Y-m-d";
			
			if($noweekend){
				$day_nb = date('N', strtotime($date));
				if($day_nb==1){
					$dateInterPrev = "3";
				}
				if($day_nb==5){
					$dateInterNext = "3";
				}
			}		
		break;
		
		case 'week':	
		break;
		
		case 'month':
			$dateConversion = "2bY";
			$dateFormat = "Y-m";
		break;

	}
	
	if($period=="year"){
		$prevDate = $prevLink = $date-1;
		$nextDate = $nextLink = $date+1;
		
		
	}else if($period=="week"){
		
		if($week == 52) {
			$nextDate =  $date+1;
			$nextWeek = 1;
			$prevDate = $date;
			$prevWeek = $week-1;
			
		} else if($week == 1) {
			$nextDate =  $date;
			$nextWeek = $week+1;
			$prevDate = $date-1;
			$prevWeek = 52;
		}else{
			$nextDate = $prevDate = $date;
			$nextWeek = $week+1;
			$prevWeek = $week-1;
		}
		
		
	}else{
		$nextDate = date($dateFormat, strtotime('+ '.$dateInterNext." ".$period, strtotime($date)));
		$prevDate = date($dateFormat, strtotime('- '.$dateInterPrev." ".$period, strtotime($date)));
		$prevLink = ucfirst(convertDate($prevDate, $dateConversion));
		$nextLink = ucfirst(convertDate($nextDate, $dateConversion));
	}
	
	if($navEnd){
		$nextBtn = "<a href='#' class='btn btn-secondary disabled' ><span class='sr-only'>{$nextLink}</span> <i class='fas fa-chevron-right'></i></a>";
	}else{
		
		$nextBtn = "<a href='?period={$period}&date={$nextDate}&week={$nextWeek}' class='btn btn-secondary' data-toggle='tooltip' data-placement='top' title='{$nextLink}'><span class='sr-only'>{$nextLink}</span><i class='fas fa-chevron-right'></i></a>";
	}
	
	$btnGrp = "
	<div class='btn-group btn-group-sm date-nav ' role='group' aria-label=''>
		<a href='?period={$period}&date={$prevDate}&week={$prevWeek}' class='btn btn-secondary' data-toggle='tooltip' data-placement='top' title='{$prevLink}'><i class='fas fa-chevron-left'></i><span class='sr-only'>{$prevLink}</span></a>
		{$nextBtn}
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

/* dateRequest*/
function dateRequest($period, $date, $table){
	
	switch($period){
		case "year":
			$request = "YEAR({$table}.dateCreation) = {$date}";
		break;			
		case "month":
			$month = date('m', strtotime($date));
			$year = date('Y', strtotime($date));
			$request = "MONTH({$table}.dateCreation) = {$month} AND YEAR({$table}.dateCreation) = {$year} ";
		break;
		case "day":
			$request = "{$table}.dateCreation = '{$date}' ";
		break;			
		case "custom":
			$request = "{$table}.dateCreation BETWEEN '{$date[0]}' AND '{$date[1]}' ";
		break;
	}
	
	return $request;
	
}



/* dashboardPageContent */
function dboardPage($date){
	
	global $connexion;
	
	$dateN = date('N', strtotime($date));
	
	if($dateN!=6 && $dateN!=7){
		
		$picksDayTable = picksDayTable($date, "day");
		$picksDayStats = picksDayStats($date);
		
		$picksSection = "
		<h3>Collectes</h3>
		<div class='row mb-4'>
			<div class='col-8'>
				{$picksDayTable}
			</div>
			<div class='col-4'>
				{$picksDayStats}
			</div>
		</div>";

	}
	
/*	
	$orders_rq = "SELECT COUNT(orders.id) AS nb FROM orders WHERE orders.dateCreation = '{$date}'";
	$orders_rs=mysqli_query($connexion, $orders_rq) or die(mysqli_error($connexion));
	$orders=mysqli_fetch_assoc($orders_rs);
	if($orders['nb']){
*/		
		$ordersSection = ordersTab("day", $date);
/*
	}else{
		
		$ordersSection = "Aucune commande";
		
	}
*/

	$page = "
	<div class='col'>
		{$picksSection}
		<h3>Commandes</h3>
		{$ordersSection}
	</div>";
	
	return $page;
	
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
		
		
		if($action=='delete'){
			
			$title ="Supprimer un secteur";
			$lead = "Etes vous sur de vouloir supprimer ce secteur ?";
			
			
		}
		
		if($action=='create'){
			
			$calMonth = date('m', strtotime($calDate));
			$calYear = date('Y', strtotime($calDate));
			
			$title ="Ajouter un secteur";
			$lead = "Ajouter un secteur à ce jour de collecte";
				
			$selectOptSecteur = "";
			
			for($i=1; $i<=21; $i++){
				
				$secteurExist_rq = "SELECT id FROM cal WHERE cal.date='{$calDate}' AND secteur={$i}";
				$secteurExist_rs = mysqli_query($connexion, $secteurExist_rq) or die();
				$secteurExist_nb = mysqli_num_rows($secteurExist_rs);
				
				if(!$secteurExist_nb){
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

			
		}
		
		
		$section = "
		<h3>
			{$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>			
		<section id='pickEdit'>
			<form action='edit/cal.action.php' method='post' id='calEditForm'>
				<div class='form-row'>
					<div class='col-md-12 form-group'>
						<p class='text-form font-weight-bold mb-0'>{$lead}</p>
					</div>
				</div>					
				{$form}				
				<input type='hidden' name='action' value='{$action}'/>
				<input type='hidden' name='calID' id='calID' value='{$calID}'/>
				<input type='hidden' name='calDate' id='calDate' value='{$calDate}'/>			
				<div class='form-row'>
					<div class='col-12 form-group mb-0'>
						<button class='btn btn-block btn-warning' type='submit'>Valider</button>
					</div>	
				</div>
				
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