<?php

// pickBtn ***************************************
function pickBtn($pickID, $pickType, $drop){
	
	global $connexion;
	
	if($pickType=="pick"){
		$pick_rq = "
		SELECT cal.date, picks.userID, users.tel, collects.id AS collectID FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = picks.userID
		LEFT JOIN collects ON collects.id = picks.collectID
		WHERE picks.id={$pickID}";	
	}
	
	if($pickType=="bundle"){
	
		$pick_rq = "
		SELECT cal.date, bundles.userID, users.tel, collects.id AS collectID FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = picks.userID
		LEFT JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.id={$pickID}";

	}
	
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$btn = "";
	
	if($pick['date']>=date("Y-m-d")){
		
		if(!$pick['collectID']){
			
			if($pick['date']==date("Y-m-d")){

				$btn .= "
				<button data-edit='pick' data-rq='pickID={$pickID}&pickType={$pickType}&action=createCol' data-toggle='modal' data-target='#editModal' class='dropdown-item' type='button'>Valider la collecte</button>";
				
				if($pickType=='pick'){
					$btn .= "
					<button data-edit='pick' data-rq='pickID={$pickID}&pickType={$pickType}&action=miss' data-toggle='modal' data-target='#editModal' class='dropdown-item' type='button'>Collecte manquée</button>";
				}	

				$btn .= "
				<div class='dropdown-divider'></div>
				<a href='tel:{$pick['tel']}' class='dropdown-item'>Contacter abonné(e)</a>
				<button data-edit='pick' data-rq='action=loc&calDate={$pick['date']}&userID={$pick['userID']}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Localiser abonné(e)</button>";
				
			}else{
				$btn .= "
				<button data-edit='pick' data-rq='action=update&pickID={$pickID}&pickType={$pickType}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Modifier collecte</button> 
				<button data-edit='pick' data-rq='action=delete&pickID={$pickID}&pickType={$pickType}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Annuler collecte</button>";
				
			}

		}else{			
			
			$btn .= "
			<div class='dropdown-divider'></div>
			<button data-edit='pick' data-rq='pickID={$pickID}&pickType={$pickType}&action=updateCol' data-toggle='modal' data-target='#editModal' class='dropdown-item' type='button'>Modifier collecte</button>";
			
			if($pickType=='pick'){
				$btn .= "
				<button data-edit='pick' data-rq='pickID={$pickID}&pickType={$pickType}&action=deleteCol' data-toggle='modal' data-target='#editModal' class='dropdown-item' type='button'>Annuler collecte</button>";
			}

		}
	}
	
	if(!$drop){
		$drop = "dropleft";
	}

	$btnDropdown = "
	<div class='dropdown {$drop}'>
		<button class='btn btn-sm btn-secondary dropdown-toggle' type='button' id='editCol{$pickID}' data-toggle='dropdown'></button>
		<div class='dropdown-menu' aria-labelledby='editCol{$pickID}'>
			{$btn}
		</div>
	</div>";
	
	return $btnDropdown;
	
}

// pickView *************************************
function pickView($pickID, $pickType){
	
	global $connexion;
	
	$infosTable =  pickInfosTable($pickID, $pickType);
	$detailTable =  pickDetailTable($pickID, $pickType);
	$bundleTable = pickBundleTable($pickID, $pickType);
	
	if($pickType=="pick"){
		$pick_rq = "
		SELECT picks.calID, cal.date, picks.userID, users.tel, collects.id AS collectID, miss.id AS missID FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = picks.userID
		LEFT JOIN collects ON collects.id = picks.collectID
		LEFT JOIN miss ON miss.pickID = picks.id
		WHERE picks.id={$pickID}";	
	}
	
	if($pickType=="bundle"){
	
		$pick_rq = "
		SELECT picks.calID, cal.date, bundles.userID, users.tel, collects.id AS collectID FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = picks.userID
		LEFT JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.id={$pickID}";

	}
	
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$editBtn = "
	<a href='#' class='btn btn-block btn-warning mb-2' data-target='#carouselDetail' data-slide-to='1'>Modifier la collecte</a>
	<a href='#' class='btn btn-block btn-outline-danger' data-target='#carouselDetail' data-slide-to='2'>Supprimer la collecte</a>";

	if($pick['date']>=date("Y-m-d")){
		
		if(!$pick['collectID']){
			
			if(!$pick['missID']){
				
				$thCell = "Collecte programée";
				
				if($pick['date']==date("Y-m-d")){

					$pickUpdateForm = pickForm("createCol", $pickID, $pickType, $pick['calID'], $pick['userID']);
					$pickDeleteForm = pickForm("miss", $pickID, $pickType, $pick['calID'], $pick['userID']);
					
					$editBtn = "
					<a href='#' class='btn btn-block btn-warning mb-2' data-target='#carouselDetail' data-slide-to='1'>Valider la collecte</a>
					<a href='#' class='btn btn-block btn-outline-danger' data-target='#carouselDetail' data-slide-to='2'>Collecte manquée</a>";
					
					
				}else{

					$pickUpdateForm = pickForm("update", $pickID, $pickType, $pick['calID'], $pick['userID']);
					$pickDeleteForm = pickForm("delete", $pickID, $pickType, $pick['calID'], $pick['userID']);
					
				}

			}else{
				
				$thCell = "Collecte cloturée";
				
				if($pick['date']==date("Y-m-d")){
				
					$pickUpdateForm = pickForm("updateCol", $pickID, $pickType, $pick['calID'], $pick['userID']);
					
				}
				
			}
			
		}else{
			
			$thCell = "Collecte cloturée";
			
			if($pick['date']==date("Y-m-d")){
				
				$pickUpdateForm = pickForm("updateCol", $pickID, $pickType, $pick['calID'], $pick['userID']);

			}
			
		}
		
		$editSection = "
		<div class='carousel-item'>
			{$pickUpdateForm}
			<a href='#' class='btn btn-block btn-outline-secondary mt-3' data-target='#carouselDetail' data-slide-to='0'>Annuler</a>
		</div>
		<div class='carousel-item'>
			{$pickDeleteForm}
			<a href='#' class='btn btn-block btn-outline-secondary mt-3' data-target='#carouselDetail' data-slide-to='0'>Annuler</a>
		</div>";
		

	}else{
		
		$thCell = "Collecte cloturée";
		
		if(!$pick['collectID']){
			
			$pickUpdateForm = pickForm("createCol", $pickID, $pickType, $pick['calID'], $pick['userID']);				

		}else{
			
			$pickUpdateForm = pickForm("updateCol", $pickID, $pickType, $pick['calID'], $pick['userID']);

		}
		
		$pickDeleteForm = pickForm("delete", $pickID, $pickType, $pick['calID'], $pick['userID']);
		
		$editSection = "
		<div class='carousel-item'>
			{$pickUpdateForm}
			<a href='#' class='btn btn-block btn-outline-secondary mt-3' data-target='#carouselDetail' data-slide-to='0'>Annuler</a>
		</div>
		<div class='carousel-item'>
			{$pickDeleteForm}
			<a href='#' class='btn btn-block btn-outline-secondary mt-3' data-target='#carouselDetail' data-slide-to='0'>Annuler</a>
		</div>";
		
		
		
	}		
	
	$section = "
	{$infosTable}
	<div id='carouselDetail' class='carousel'>
		<h4 class='bg-dark text-white p-2 text-uppercase font-weight-bolder mb-0' style='font-size:.9rem;'>
			 <a data-target='#carouselDetail' data-slide-to='0' class='active'>{$thCell}</a>
		</h4>
		<div class='carousel-inner'>
			<div class='carousel-item active'>
				{$detailTable}
				{$bundleTable}
				{$editBtn}
			</div>
			{$editSection}
		</div>
	</div>";

	return $section; 
}

/* pickInfosTable */
function pickInfosTable($pickID, $pickType){
	
	global $connexion;
	
	if($pickType=="pick"){
		$pick_rq = "
		SELECT picks.adresseID, cal.date, picks.userID FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE picks.id={$pickID}";	
	}
	
	if($pickType=="bundle"){
		$pick_rq = "
		SELECT picks.adresseID, cal.date, bundles.userID FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		WHERE bundles.id={$pickID}";
	}	
	
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$refCell = pickRef($pickID, $pickType);
	$userCell = userName($pick['userID']);
	$adresseCell = pickAdresse($pick['adresseID']);
	$dateCell = convertDate($pick['date']);
	
	$table = " 
	<table class='table font-weight-bold mb-3'>
		<tr>
			<th width='100'>Ref.</th>
			<td class='table-light'>{$refCell}</td>
		</tr>
		<tr>
			<th>Abonné</th>
			<td class='table-light'>{$userCell}</td>
		</tr>	
		<tr>
			<th style='vertical-align:top;'>Adresse</th>
			<td class='table-light'>{$adresseCell}<br></td>
		</tr>
	</table>";
	
	return $table; 
}

/* pickDetailTable */
function pickDetailTable($pickID, $pickType){
	
	global $connexion;
	
	if($pickType=="pick"){
		$pick_rq = "
		SELECT picks.sacs, picks.userID, slots.start, slots.end, cal.date, collects.id AS collectID FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON slots.id = picks.slotID
		LEFT JOIN collects ON collects.id = picks.collectID
		WHERE picks.id={$pickID}";	
	}
	
	if($pickType=="bundle"){	
		$pick_rq = "
		SELECT bundles.sacs, bundles.userID, slots.start, slots.end, cal.date, collects.id AS collectID FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON slots.id = picks.slotID
		LEFT JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.id={$pickID}";
	}	
	
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$dateCell = convertDate($pick['date']);
	
	if($pick['collectID']){
		
		$thCell = "Collecte cloturée";
		$statusCell = "Validée";
		$tr_class = "table-success";
			
		$collect_rq = "
		SELECT collects.*, pickers.prenom AS picker FROM collects
		INNER JOIN pickers ON pickers.id = collects.pickerID
		WHERE collects.id = {$pick['collectID']}";
		$collect_rs = mysqli_query($connexion, $collect_rq) or die(mysqli_error($connexion));
		$collect = mysqli_fetch_assoc($collect_rs);
		
		$hourCell = $collect['hour'];	
		$pickerCell = $collect['picker'];
		
		
		$sacCell = $collect['sacs'];
		
		$sacsCmd_rq = "
		SELECT SUM(sacs.nb) AS nb FROM sacs 
		INNER JOIN orders ON orders.id = sacs.orderID
		WHERE orders.userID={$pick['userID']} AND orders.tID!=0 AND sacs.collectID={$pick['collectID']}";
		$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
		$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
		
		if($sacsCmd['nb']){						
			$sacsCmdCell=$sacsCmd['nb'];
		}
		
		
	}else{
		
		$pMiss_rq = "
		SELECT miss.hour, miss.pickerID FROM miss 
		WHERE miss.pickID={$pickID} AND miss.pickType='{$pickType}'";
		$pMiss_rs = mysqli_query($connexion, $pMiss_rq) or die(mysqli_error($connexion));
		$pMiss_nb = mysqli_num_rows($pMiss_rs);
		
		if($pick['date']>=date("Y-m-d")){
			
			$sacsCmd_rq = "
			SELECT SUM(sacs.nb) AS nb FROM sacs 
			INNER JOIN orders ON orders.id = sacs.orderID
			WHERE orders.userID={$pick['userID']} AND sacs.collectID=0 AND orders.tID!=0";
			$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
			$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
			
			if(!$pMiss_nb){
				
				if($pick['sacs']){
					$sacCell = $pick['sacs'];
				}else{
					$userSacs = userSacs($pick['userID'])+1;
					$sacCell = $userSacs."<small>+</small>";
				}
				
				$tr_class = "";
				
				$thCell = "Collecte programée";
				if($pick['date']==date("Y-m-d")){
					$statusCell = "En cours";
					$tr_class = "table-warning";
				}else{
					$statusCell = "A venir";
				}
				
				
				$hourCell = "{$pick['start']}/{$pick['end']}";
				
				if($sacsCmd['nb']){
							
					$sacsCmdCell=$sacsCmd['nb'];
	
				}

			}else{
				
				$thCell = "Collecte cloturée";
				$statusCell = "Manquée";
				$tr_class = "table-warning";
				while($pMiss = mysqli_fetch_assoc($pMiss_rs)){
					$hourCell.= $pMiss['hour']."<br>";
				}		

			}
		
		}else{
			
			$thCell = "Collecte cloturée";
	
			if($pMiss_nb){
				
				$statusCell = "Manquée";
				$tr_class = "table-danger";
				while($pMiss = mysqli_fetch_assoc($pMiss_rs)){
					$hourCell.= $pMiss['hour']."<br>";
				}
				
			}else{
				$statusCell = "Non collectée";
				$tr_class = "table-warning";
				$hourTr_class = "d-none";
			}
			
			$sacsTr_class = "d-none";
		}
		
	}
	
	$table = "
	<table class='table mb-3 font-weight-bold'>
		<tr class='{$tr_class}'>
			<th width='100'>Statut</th>
			<td>{$statusCell}</td>
			<th width='100'>Date</th>
			<td>{$dateCell}</td>
		</tr>
		<tr class='{$tr_class} {$hourTr_class}'>
			<th calss='align:top;'>Horaire</th>
			<td>{$hourCell}</td>
			<th>Picker</th>
			<td>{$pickerCell}</td>
		</tr>
		<tr class='{$tr_class} {$sacsTr_class}'>
			<th>Sacs col.</th>
			<td>{$sacCell}</td>
			<th>Sacs cmd.</th>
			<td>{$sacsCmdCell}</td>
		</tr>
		
		
	</table>";
	
	return $table; 
}

/* pickBundleTable */
function pickBundleTable($pickID, $pickType){
	
	global $connexion;
	
	if($pickType=="pick"){
	
		$bundles_rq = "
		SELECT bundles.id, bundles.sacs, bundles.userID, cal.date, users.nom, users.prenom, collects.id AS collectID FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = bundles.userID
		LEFT JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.pickID = {$pickID}";
		$bundles_rs = mysqli_query($connexion, $bundles_rq) or die();
		$bundles_nb = mysqli_num_rows($bundles_rs);
		
		if($bundles_nb){
			
			while($bundles = mysqli_fetch_assoc($bundles_rs)){
				
				$tr_class = "";

				if($bundles['date']>=date("Y-m-d")){
					
					$sacCell = $bundles['sacs'];
					
					$sacsCmd_rq = "
					SELECT SUM(sacs.nb) AS nb FROM sacs 
					INNER JOIN orders ON orders.id = sacs.orderID
					WHERE orders.userID={$bundles['userID']} AND sacs.collectID=0 AND orders.tID!=0";
					$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
					$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
					
				}else{
					
					if($bundles['collectID']){
						
						$tr_class = "table-success";
						
						$collect_rq = "
						SELECT collects.*, pickers.prenom AS picker FROM collects
						INNER JOIN pickers ON pickers.id = collects.pickerID
						WHERE collects.id = {$bundles['collectID']}";
						$collect_rs = mysqli_query($connexion, $collect_rq) or die(mysqli_error($connexion));
						$collect = mysqli_fetch_assoc($collect_rs);
						
						$sacCell = $collect['sacs'];
						
						$sacsCmd_rq = "
						SELECT SUM(sacs.nb) AS nb FROM sacs 
						INNER JOIN orders ON orders.id = sacs.orderID
						WHERE orders.userID={$bundles['userID']} AND orders.tID!=0 AND sacs.collectID={$bundles['collectID']}";
						$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
						$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
						
					}else{
						
						$tr_class = "table-warning";
						
						$statusCell = "Manquée";
						$casColCell = 0;

					}	
				}			

				if($sacsCmd['nb']){		
					$sacsCmdCell=$sacsCmd['nb'];
				}else{
					$sacsCmdCell="-";
				}

				$tbody.="
				<tr class='{$tr_class}'>
					<td class='text-uppercase'>{$bundles['prenom']} {$bundles['nom']}</td>
					<td class='text-uppercase'>CGR {$bundles['id']}</td>
					<td class='text-center'>{$sacCell}</td>
					<td class='text-center'>{$sacsCmdCell}</td>
				</tr>";
			}
			
			$table="
			<table class='table table-sm'>
				<thead class='bg-secondary'>
					<tr>
						<th colspan='4' class='bg-dark font-weight-bold' style='font-size:.9rem'>Collecte groupée</th>
					</tr>
					<tr>
						<th>Abonné</th>
						<th>Ref</th>
						<th class='text-center'><i class='fas fa-shopping-bag'></i> Col</th>
						<th class='text-center'><i class='fas fa-shopping-bag'></i> Cmd</th>
					</tr>
				</thead>			
				<tbody>
					{$tbody}
				</tbody>
			</table>";
			
			return $table;
			
		}		
	}
}

/* pickForm */
function pickForm($action, $pickID, $pickType, $calID, $userID){
	
	global $connexion;
	
	$btnLabel = "Valider";
	$btnClass = "warning";
	
	if($action!="create"){

		if($pickType=="pick"){
			$sSQL = "
			SELECT picks.sacs AS sacsProg, picks.userID, picks.collectID, picks.slotID, picks.calID, picks.bundle, cal.date, collects.sacs AS sacsCol FROM picks 
			INNER JOIN cal ON cal.id = picks.calID
			LEFT JOIN collects ON collects.id = picks.collectID
			WHERE picks.id={$pickID}";				
		}
		
		if($pickType=="bundle"){
			$sSQL = "
			SELECT bundles.sacs AS sacsProg, bundles.userID, bundles.collectID, picks.slotID, picks.calID, cal.date, collects.sacs AS sacsCol FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			INNER JOIN cal ON cal.id = picks.calID
			LEFT JOIN collects ON collects.id = bundles.collectID
			WHERE bundles.id={$pickID}";
		}
		
		$result = mysqli_query($connexion, $sSQL) or die(mysqli_error($connexion));
		if ($row = mysqli_fetch_assoc($result)) {
			foreach ($row as $key => $value) {
				$$key = $value;
			}
		}	
		mysqli_free_result($result);		
	}
	
	if($action=="create"||$action=="update"){
		
		
		if(!userPro($userID)){
			
			$slotSelect_label = "Horaire";
			$slotSelect_name = "slotID";
			
			$slots_rq = "SELECT * FROM slots";
			$slots_rs = mysqli_query($connexion, $slots_rq) or die(mysqli_error($connexion));
			while($slots = mysqli_fetch_assoc($slots_rs)){
				
				$slotOptions_state = "";
				$slotOptions_label = "Entre {$slots["start"]} et {$slots["end"]}";
				
				$slotLimit_rq = "SELECT id FROM picks WHERE calID={$calID} AND slotID={$slots['id']}";
				$slotLimit_rs = mysqli_query($connexion, $slotLimit_rq) or die(mysqli_error($connexion));
				$slotLimit_nb = mysqli_num_rows($slotLimit_rs);
				
				if($slotLimit_nb>=10){
					$slotOptions_state .= " disabled ";
				}

				if($slots['id']==$slotID){
					$slotOptions_state .= " selected ";
				}
				
				$slotOptions .="<option value='{$slots['id']}' {$slotOptions_state}>{$slotOptions_label}</option>";

			}
			
			if($pickType=="bundle"){ 
				$slotSelect_state = "disabled";			
			}		
			
		}
		
		else{

			$slotSelect_label = "Date";
			$slotSelect_name = "calID";

			$calDistinct_rq = "SELECT DISTINCT date FROM cal WHERE cal.date>=NOW() ORDER BY date ASC LIMIT 5";
			$calDistinct_rs = mysqli_query($connexion, $calDistinct_rq) or die(mysqli_error($connexion));
			while($calDistinct = mysqli_fetch_assoc($calDistinct_rs)){

				$calOptions_state = "";
				$calOptions_label = convertDate($calDistinct['date'], "2AdB");
				
				$cal_rq = "SELECT id FROM cal WHERE cal.date='{$calDistinct['date']}' LIMIT 1";
				$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error($connexion));
				$cal = mysqli_fetch_assoc($cal_rs);
				
				/*
				$calLimit_rq = "SELECT id FROM picks WHERE calID={$calID} AND slotID={$slots['id']}";
				$calLimit_rs = mysqli_query($connexion, $calLimit_rq) or die(mysqli_error($connexion));
				$calLimit_nb = mysqli_num_rows($calLimit_rs);
				
				if($calLimit_nb>=10){
					$calOptions_state .= " disabled ";
				}
				*/
				
				if($cal['id']==$calID){
					$calOptions_state .= " selected ";
				}
				
				$slotOptions .="<option value='{$cal['id']}' {$calOptions_state}>{$calOptions_label}</option>";

			}
			
		}
	
		$userCredits = userCredits($userID);
		$sacsMax = $userCredits;
		
		if($action=="create"){
			$sacsProg = 1;
			$btnLabel = "Programmer la collecte";
		}
		
		if($action=="update"){
			$formClass = "alert-warning";
			$btnLabel = "Modifier la collecte";
		}

		for($i=1;$i<=$sacsMax;$i++){
			$sacsOptions_state ="";
			if($i==$sacsProg){
				$sacsOptions_state = "selected ";
			}
			$sacsOptions .="<option value='{$i}' {$sacsOptions_state}>{$i}</option>";
		}
		
/*		
		if($pickType=="pick" && !userPro($userID)){
			if($bundle){
				$bundleCheck_state = "checked";
			}
			$bundle_formGroup = "
			<div class='col-md-12 form-group'>
				<div class='custom-control custom-switch'>						
				  <input class='custom-control-input' type='checkbox' value='1' id='bundle' name='bundle' {$bundleCheck_state}>
				  <label class='custom-control-label' for='bundle'>Permettre la collecte groupée </label>
				</div>
			</div>";
		}
*/

		$formRows = "
		<div class='form-row'>
			<div class='col-md-9 form-group'>
				<label>{$slotSelect_label}</label>
				<select name='{$slotSelect_name}' id='{$slotSelect_name}' class='form-control form-control-lg' required {$slotSelect_state}>
					{$slotOptions}
				</select>
			</div>
			<div class='col-md-3 form-group'>
				<label>Sac(s)</label>
				<select name='sacs' id='sacs' class='form-control form-control-lg' required>
					{$sacsOptions}
				</select>
			</div>
		</div>";
	}
	
	if($action=="createCol"||$action=="updateCol"){
		
		$sacsCmd_rq = "
		SELECT SUM(sacs.nb) AS nb FROM sacs 
		INNER JOIN orders ON orders.id = sacs.orderID 
		WHERE orders.userID={$userID} AND orders.tID!=0 AND sacs.collectID=0";
		$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
		$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
		
		if($action=='createCol'){
			
			$creditsLimit = userCredits($userID);
			$sacsPick = $sacsProg;
			
			$pMiss_rq = "
			SELECT miss.id FROM miss 
			WHERE miss.pickID={$pickID} AND miss.pickType='{$pickType}'";
			$pMiss_rs = mysqli_query($connexion, $pMiss_rq) or die(mysqli_error($connexion));
			$pMiss_nb = mysqli_num_rows($pMiss_rs);
			
			if($pMiss_nb){
				$btnClass = "warning";
				$formClass = "alert-warning";
				$btnLabel = "Modifier la collecte";
			}else{
				$btnClass = "warning";
				$formClass = "alert-success";
				$btnLabel = "Valider la collecte";
			}
			
			
		}
		
		if($action=='updateCol'){
			
			$creditsLimit = userCredits($userID)+$sacsCol;
			$sacsPick = $sacsCol;
			
			$btnClass = "warning";
			$formClass = "alert-warning";
			$btnLabel = "Modifier la collecte";
			
			if(!$sacsCmd['nb']){
				$sacsCmd_rq = "
				SELECT SUM(sacs.nb) AS nb FROM sacs 
				INNER JOIN orders ON orders.id = sacs.orderID 
				WHERE orders.userID={$userID} AND orders.tID!=0 AND sacs.collectID={$collectID}";
				$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
				$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
				if($sacsCmd['nb']){
					$sacsCmd_state = "checked";
				}
			}
		}
		
		for($i=1; $i<=$creditsLimit; $i++){			
			$sacsOptions_state="";
			if($i==$sacsPick){
				$sacsOptions_state = "selected";
			}
			
			$sacs_options .= "<option value='{$i}' {$sacsOptions_state}>{$i}</option>";
		}
		

		if($sacsCmd['nb']){

			$sacsCmd_check = "
			<div class='row form-group'>
				<div class='col'>
					<div class='form-check'>
						<input type='checkbox' value='1' class='form-check-input' name='sacsCmd' id='sacsCmd' {$sacsCmd_state}>
						<label class='form-check-label' for='sacsCmd'>{$sacsCmd['nb']} sac(s) commandés</label>
					 </div>
				</div>
			 </div>";
			 
		}
		
		$formRows = "
		<div class='form-group row'>
			<div class='col'>
				<label>Sacs collectés</label>
				<select name='sacs' id='sacs' class='form-control form-control-lg text-center' required>
					<option value='0'>0</option>
					{$sacs_options}
				</select>
			</div>
		</div>
		{$sacsCmd_check}";
		
	}
	

	if($action=="delete"){
		
		$formRows = "
		<div class='row form-group'>
			<div class='col text-center font-weight-bold'>
				&Ecirc;tes-vous sur(e) de vouloir<br>supprimer cette collecte ?
			</div>
		</div>
		";
		
		$btnLabel = "Supprimer la collecte";
		$btnClass = "danger";
		$formClass = "alert-danger";
	}
	
	if($action=="miss"){
		
		$formRows = "
		<div class='row form-group'>
			<div class='col text-center font-weight-bold'>
				&Ecirc;tes-vous sur(e) de vouloir<br>marquer cette collecte comme manquée ?
			</div>
		</div>
		";
		
		$btnLabel = "Collecte manquée";
		$btnClass = "danger";
		$formClass = "alert-danger";
	}
	
	$formRows .= "
	<input type='hidden' name='action' value='{$action}'/>
	<input type='hidden' name='userID' value='{$userID}'/>
	<input type='hidden' name='pickID' value='{$pickID}'/>
	<input type='hidden' name='pickType' value='{$pickType}'/>
	<input type='hidden' name='collectID' value='{$collectID}'/>
	<input type='hidden' name='sacsPrev' value='{$sacsProg}'/>";
	
	if(!userPro($userID)||(userPro($userID)&&$action!="create")){
		$formRows .= "
		<input type='hidden' name='calID' value='{$calID}'/>
		<input type='hidden' name='slotPrev' value='{$slotID}'/>";
	}
	
	$form = "
	<form action='edit/pick.edit.php' method='post' id='pickEditForm' class='needs-validation {$formClass}' novalidate>				
		{$formRows}
		<div class='form-row clearfix'>
			<div class='col-12 form-group mb-0'>
				<button class='btn btn-block btn-{$btnClass}' type='submit'>{$btnLabel}</button>
			</div>
		</div>		
	</form>";
	
	return $form;
	
}

/* pickEdit */
function pickEdit($action, $pickID, $pickType, $calID, $userID){
	
	global $connexion;
	$error = 0;
	
	switch($action){
		
		case "view":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Infos collecte";
				$view = pickView($pickID, $pickType);
			}
		
		break;
		
		case "create":
			if(empty($pickType)){
				$error = 1;
			}else{
				$title ="Programmer collecte";
			}
		
		break;

		case "update":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Modifier la collecte";
				$view = pickInfosTable($pickID, $pickType);
			}
		break;
		
		case "delete":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Annuler la collecte";			
				$view = "
				<div class='alert alert-danger'>
					&Ecirc;tes-vous sur(e) de vouloir annuler cette demande ?
				</div>";
				$view .= pickInfosTable($pickID, $pickType);
			}
		break;
		
		case "miss":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Collecte manquée";
				$view = "
				<div class='alert alert-danger'>
					&Ecirc;tes-vous sur(e) que cette collecte soit manquée ?
				</div>";
				$view .= pickInfosTable($pickID, $pickType);
			}	
		break;
		
		case "createCol":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Valider la collecte";
				$view = pickInfosTable($pickID, $pickType);
			}		
		break;
		
		case "updateCol":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Modifier la collecte";
				$view = pickInfosTable($pickID, $pickType);
			}
		break;
		
		case "deleteCol":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				$title ="Annuler la collecte";
				$view = "
				<div class='alert alert-danger'>
					&Ecirc;tes-vous sur(e) de vouloir annuler cette collecte ?
				</div>";
				$view .= pickInfos($pickID, $pickType);
			}
		break;

		default;
			$error = 1;
		break;

	}
	
	if(!$error){
		
		if($action!="view"){
			
			$view.= pickForm($action, $pickID, $pickType, $calID, $userID);
	
		}
	
		$section = "
		<h3>
			{$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>			
		<section id='pickEdit'>
			{$view}	
		</section>";
		
	}else{

		$section = "
		<div class='alert alert-danger'>
			Une erreur est survenue
		</div>";
		
	}
	
	return $section;
	
}

/*  pickUserTable */
function pickUserTable($userID){
	
	global $connexion;
	
	$userSecteur = userSecteur($userID);
	
	if(userPro($userID)){
		
		$pick_rq = "
		SELECT picks.id FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE picks.userID={$userID} AND cal.date>NOW()";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick_nb = mysqli_num_rows($pick_rs);
		
	}else{
		
		$nextCal_rq = "
		SELECT cal.id, cal.date FROM cal 
		WHERE cal.date>NOW() AND cal.secteur={$userSecteur}
		ORDER BY cal.date ASC LIMIT 1";
		$nextCal_rs = mysqli_query($connexion, $nextCal_rq) or die();
		$nextCal = mysqli_fetch_assoc($nextCal_rs);
		
		
		$pick_rq = "
		SELECT picks.id FROM picks
		WHERE picks.valid=1 AND picks.calID = {$nextCal['id']} AND picks.userID={$userID}
		UNION
		SELECT bundles.id FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		WHERE picks.calID = {$nextCal['id']} AND bundles.userID={$userID}";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick_nb = mysqli_num_rows($pick_rs);
		
		
	}
	
	if(userActive($userID)){
		$btn = "<button data-edit='pick' data-rq='action=create&calID={$nextCal['id']}&userID={$userID}&pickType=pick' data-toggle='modal' data-target='#editModal' class='btn btn-sm btn-warning'>Programmer la collecte</button>";
	}
	
	if(!$pick_nb){
		
		if(userPro($userID)){
			$date_cell = "Aucune collecte programmée";
			$calID = 0;
		}else{
			$date_cell = convertDate($nextCal['date'],"2AdB");
			$calID = $nextCal['id'];
		}
		
		$table = " 
		<table class='table font-weight-bold mb-4'>
			<thead>
				<tr>
					<th colspan='2' class='bg-dark p-3 rounded-top font-weight-bold''>Prochaine collecte</th>
				</tr>
			</thead>
			<tbody>
				<tr class='table-light'>
					<td>{$date_cell}</td>
					<td class='text-right'>
						{$btn}			
					</td>
				</tr>
			</tbody>
		</table>";
		
		return $table;
		
	}
}

?>