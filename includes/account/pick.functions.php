<?php

/* pickInfosTable */
function pickInfosTable($pickID, $pickType, $action){
	
	global $connexion;
	
	if($pickType=="pick"){
		$pick_rq = "
		SELECT picks.adresseID, picks.sacs, picks.userID, slots.start, slots.end, cal.date, collects.id AS collectID FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON slots.id = picks.slotID
		LEFT JOIN collects ON collects.id = picks.collectID
		WHERE picks.id={$pickID}";	
	}
	
	if($pickType=="bundle"){	
		$pick_rq = "
		SELECT picks.adresseID, bundles.sacs, bundles.userID, slots.start, slots.end, cal.date, collects.id AS collectID FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN slots ON slots.id = picks.slotID
		LEFT JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.id={$pickID}";
	}	
	
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error($connexion));
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$refCell = pickRef($pickID, $pickType);
	$dateCell = convertDate($pick['date'],"2adB");
	
	if($pickType=="bundle"||$action=="createBundle"){
		$adresseCell = pickAdresse($pick['adresseID'],1);
	}else{
		$adresseCell = pickAdresse($pick['adresseID']);
	}
	
	if($pick['collectID']){
		
		$statusCell = "Collecte validée";
		$tr_class = "table-success";
			
		$collect_rq = "
		SELECT collects.*, pickers.prenom AS picker FROM collects
		INNER JOIN pickers ON pickers.id = collects.pickerID
		WHERE collects.id = {$pick['collectID']}";
		$collect_rs = mysqli_query($connexion, $collect_rq) or die(mysqli_error($connexion));
		$collect = mysqli_fetch_assoc($collect_rs);
		
		$hourCell = $collect['hour']." ({$collect['picker']})";
		$sacCell = $collect['sacs'];
		
		$sacsCmd_rq = "
		SELECT SUM(sacs.nb) AS nb FROM sacs 
		INNER JOIN orders ON orders.id = sacs.orderID
		WHERE orders.userID={$pick['userID']} AND orders.tID!=0 AND sacs.collectID={$pick['collectID']}";
		$sacsCmd_rs = mysqli_query($connexion, $sacsCmd_rq) or die(mysqli_error($connexion));
		$sacsCmd = mysqli_fetch_assoc($sacsCmd_rs);
		
		if($sacsCmd['nb']){						
			$sacsCmdTr="
			<tr class='{$tr_class}'>
				<th width='130' class='bg-secondary'>Sacs cmd.</th>
				<td>{$sacsCmd['nb']}</td>
			</tr>";
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
				
				$statusCell = "Collecte programmée";
				$tr_class = "";
				$hourCell = "entre {$pick['start']} et {$pick['end']}";
				$sacCell = $pick['sacs'];
				
				if($sacsCmd['nb']){
							
					$sacsCmdTr="
					<tr class='{$tr_class}'>
						<th width='130' class='bg-secondary'>Sacs cmd.</th>
						<td>{$sacsCmd['nb']}</td>
					</tr>";
	
				}

			}else{
			
				$statusCell = "Collecte manquée";
				$tr_class = "table-warning";
				while($pMiss = mysqli_fetch_assoc($pMiss_rs)){
					$hourCell.= $pMiss['hour']."<br>";
				}		

			}
		
		}else{
	
			if($pMiss_nb){
				
				$statusCell = "Collecte manquée";
				$tr_class = "table-warning";
				while($pMiss = mysqli_fetch_assoc($pMiss_rs)){
					$hourCell.= $pMiss['hour']."<br>";
				}
				
			}else{
				$statusCell = "Non collecté";
				$tr_class = "table-danger";
				$hourTr_class = "d-none";
			}
			
			$sacsTr_class = "d-none";
		}
		
	}


	$adresse_tr = "
	<tr class='{$tr_class}'>
		<th width='130' class='bg-secondary' style='vertical-align:top;'>Adresse</th>
		<td class='text-uppercase'>{$adresseCell}</td>
	</tr>";
	
	$horaire_tr = "
	<tr class='{$tr_class} {$hourTr_class}'>
		<th width='130' class='bg-secondary'>Horaire</th>
		<td class='text-uppercase'>{$hourCell}</td>
	</tr>";
	
	$sacs_tr = "
	<tr class='{$tr_class} {$sacsTr_class}'>
		<th  width='130' class='bg-secondary'>Sacs col.</th>
		<td>{$sacCell}</td>
	</tr>";

	if($action=="view"){
		$status_tr = "
		<tr class='{$tr_class}'>
			<th width='130' class='bg-secondary'>Statut</th>
			<td class='text-uppercase'>{$statusCell}</td>
		</tr>";
		$ref_tr = "
		<tr class='{$tr_class}'>
			<th width='130' class='bg-secondary'>Ref.</th>
			<td class='text-uppercase'>{$refCell}</td>
		</tr>";
	}else{
		
		$sacsCmdTr = "";
		
		if(($action=="createBundle" || $action=="update") && $pickType=="pick"){
			$horaire_tr = "";
			$sacs_tr = "";
		}
	}
	
	$table = "
	<table class='table table-sm mb-3'>
		{$status_tr}
		{$ref_tr}
		{$date_tr}
		{$adresse_tr}
		{$horaire_tr}
		{$sacs_tr}
		{$sacsCmdTr}
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
						<th class='text-center' width='90px'><i class='fas fa-shopping-bag'></i> Col</th>
						<th class='text-center' width='90px'><i class='fas fa-shopping-bag'></i> Cmd</th>
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
	
	if($action!="create"){

		if($pickType=="pick"){
			$sSQL = "
			SELECT picks.sacs AS sacsProg, picks.slotID, picks.calID, picks.bundle, cal.date FROM picks 
			INNER JOIN cal ON cal.id = picks.calID
			WHERE picks.id={$pickID}";				
		}
		
		if($pickType=="bundle"){
			$sSQL = "
			SELECT bundles.sacs AS sacsProg, picks.slotID, picks.calID, cal.date FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			INNER JOIN cal ON cal.id = picks.calID
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
	
	if($action=="create" && $pickType=="bundle"){

		$sSQL = "
		SELECT picks.slotID, picks.calID, picks.bundle, cal.date FROM picks 
		INNER JOIN cal ON cal.id = picks.calID
		WHERE picks.id={$pickID}";	

		$result = mysqli_query($connexion, $sSQL) or die(mysqli_error($connexion));
		if ($row = mysqli_fetch_assoc($result)) {
			foreach ($row as $key => $value) {
				$$key = $value;
			}
		}	
		mysqli_free_result($result);	
		
	}

	if($action=="create" || $action=="update"){
		
		if(!userPro($userID)){
			
			$slotSelect_label = "Horaire";
			$slotSelect_name = "slotID";
			
			$slots_rq = "SELECT * FROM slots WHERE pro=0";
			$slots_rs = mysqli_query($connexion, $slots_rq) or die(mysqli_error($connexion));
			while($slots = mysqli_fetch_assoc($slots_rs)){
				
				$slotOptions_state = "";
				$slotOptions_label = "Entre {$slots["start"]} et {$slots["end"]}";
				
				$slotLimit_rq = "SELECT id FROM picks WHERE picks.valid=1 AND calID={$calID} AND slotID={$slots['id']}";
				$slotLimit_rs = mysqli_query($connexion, $slotLimit_rq) or die(mysqli_error($connexion));
				$slotLimit_nb = mysqli_num_rows($slotLimit_rs);
				
				if($slotLimit_nb>=10){
					$slotOptions_state .= " disabled ";
				}
				
				if($action=="update" || ($action=="create" && $pickType=="bundle")){
					if($slots['id']==$slotID){
						$slotOptions_state .= " selected ";
					}
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
			
			$calDistinct_rq = "SELECT  DISTINCT date FROM cal WHERE cal.date>NOW() ORDER BY date ASC LIMIT 5";
			$calDistinct_rs = mysqli_query($connexion, $calDistinct_rq) or die(mysqli_error($connexion));
			while($calDistinct = mysqli_fetch_assoc($calDistinct_rs)){
				
				$calOptions_state = "";
				$calOptions_label = convertDate($calDistinct['date'], "2AdB");
				
				$cal_rq = "SELECT  id FROM cal WHERE cal.date='{$calDistinct['date']}' LIMIT 1";
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
		
		if(userActive($userID)){
			
			$userCredits = userCredits($userID);
			$userSacs = userSacs($userID);
			
			if(!$userCredits){
				
				$sacsMax = 1;
				
			}else{
			
				if($userCredits<=$userSacs){
					$sacsMax = $userCredits;
				}else{
					$sacsMax = $userSacs;
				}
			}
			
			
		}else{
			$userCredits = 1;
			$userSacs = 1;
			$sacsMax = $userSacs;
		}
		
		if($action=="create"){
			
			if(!userPro($userID)){
				$sacsProg = 1;
			}else{
				$sacsProg = $userSacs;
			}
			
		}			

		for($i=1;$i<=$sacsMax;$i++){
			$sacsOptions_state ="";
			if($i==$sacsProg){
				$sacsOptions_state = "selected ";
			}
			$sacsOptions .="<option value='{$i}' {$sacsOptions_state}>{$i}</option>";
		}
		
		$sacsOptions .="<option value='0'>+</option>";
	
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


		$form = "
		<div class='form-row'>
			<div class='col-md-9 form-group'>
				<label class='text-uppercase' style='font-weight:900;'>{$slotSelect_label}</label>
				<select name='{$slotSelect_name}' id='{$slotSelect_name}' class='form-control' required {$slotSelect_state}>
					{$slotOptions}
				</select>
			</div>
			<div class='col-md-3 form-group'>
				<label class='text-uppercase' style='font-weight:900;'>Sac(s)</label>
				<select name='sacs' id='sacs' class='form-control' required>
					{$sacsOptions}
				</select>
			</div>
		</div>";
		
		if(!userActive($userID) || ( userActive($userID) && $userCredits<=$userSacs ) ){
			
			$formules_rq = "SELECT * FROM formules ORDER BY credits ASC";
			$formules_rs = mysqli_query($connexion, $formules_rq) or die();
			while($formules = mysqli_fetch_assoc($formules_rs)){
				$formuleSelected = "";
				if($formules['credits']==0){
					$formuleLabel = "Moins de 6 crédits";
				}else{
					$formuleLabel = "Pack de {$formules['credits']} crédits";
					if($formules['credits']==6){
						$formuleSelected = "selected";
					}
				}
				$selectOptions .=  "
				<option value='{$formules['id']}' data-price='{$formules['montant']}' data-credits='{$formules['credits']}' data-libelle='{$formules['libelle']}' {$formuleSelected}>{$formuleLabel}</option>";
			}
			
			if($userCredits){
				$creditsRowClass = "d-none";
				$selectFormuleState = "disabled";
			}
			
			$form.="
			
			<div id='credits-row' class='form-row {$creditsRowClass}'>
				<div class='col-12 form-group'>
					<p class='mb-0 font-weight-bold' role='alert'>Vous devez créditer votre compte pour pouvoir programmer la collecte de plus de {$sacsMax} sac(s).</p>
				</div>
			
				<div class='col-md-9 form-group'>
					<label class='text-uppercase' style='font-weight:900;'>Créditer mon compte</label>
					
					<select id='formuleID' name='formuleID' class='form-control' {$selectFormuleState}>
						{$selectOptions}
					</select>
					
				</div>
				<div class='col-md-3 form-group'>
					<label class='text-uppercase' style='font-weight:900;'>Nb.</label>
					<select name='credits' id='credits' class='form-control' {$creditState} disabled>
						<option value='1'>1</option>
						<option value='2'>2</option>
						<option value='3'>3</option>
						<option value='4'>4</option>
						<option value='5'>5</option>
					</select>
				</div>
				
			</div>";
		}
	}
	
	$form .= "
	<input type='hidden' name='action' value='{$action}'/>
	<input type='hidden' name='pickID' value='{$pickID}'/>
	<input type='hidden' name='pickType' value='{$pickType}'/>
	<input type='hidden' name='sacsPrev' value='{$sacsProg}'/>";
	
	if(!userPro($userID)){
		$form .= "
		<input type='hidden' name='calID' value='{$calID}'/>
		<input type='hidden' name='slotPrev' value='{$slotID}'/>";
	}
	
	
	return $form;
	
}

/* pickEdit */
function pickEdit($userID, $action, $pickType, $calID, $pickID ){
	
	global $connexion;
	$error = 0;
	
	if($calID){
		$cal_rq = "SELECT cal.date FROM cal WHERE cal.id={$calID}";
		$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error($connexion));
		$cal = mysqli_fetch_assoc($cal_rs);
		$title =  convertDate($cal['date'],"2AdB");

	}else{
		
		if(userPro($userID)){
			$title = "Programmer";
		}else{
			$pickDate = pickDate($pickID, $pickType);
			$title =  convertDate($pickDate["day"],"2AdB");
		}
		
	}
	
	
	
	switch($action){
		
		case "view":
		
			if(!empty($pickID)&&!empty($pickType)){
				$title = "COL-{$pickID}";
				$view = pickInfosTable($pickID, $pickType, $action).pickBundleTable($pickID, $pickType);
			}else{
				$error = 1;
			}
			
		break;
		
		case "create":
		
			if(userPro($userID)){
				$title = "Programmer la collecte";
			}
		
			switch($pickType){					
				case "pick":	

						
						
						if($calID){
							$cal_rq = "SELECT cal.date FROM cal WHERE cal.id={$calID}";
							$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error($connexion));
							$cal = mysqli_fetch_assoc($cal_rs);
						
							$date =  convertDate($cal['date'],"2AdB");
							$dateCell =  "<h4 class='text-center'>{$date}</h4>";

						}


				break;
				case "bundle":
					if(!empty($pickID)){
						//$title ="Grouper la collecte";
						$view .= pickInfosTable($pickID, 'pick', $action."Bundle")."<p class='font-weight-bold'>{$GLOBALS['pickEditLead']}</p>";
					}else{
						$error = 1;
						$err = "pickID";
					}					
				break;
				default:
					$error = 1;
					$err = "pickType";
				break;					
			}

				
		break;
		
		case "update":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}
			if(userPro($userID)){
				$title = "Modifier la collecte";
			}
		break;
		
		case "delete":
			if(empty($pickID)||empty($pickType)){
				$error = 1;
			}else{
				if(userPro($userID)){
					$title = "Annuler la collecte";
				}
				$lead = "<p class='font-weight-bold text-center py-3'>Etes vous sur de vouloir annuler cette collecte ?</p>";
			}
		break;
		
		default;
			$error = 1;
		break;

	}
	
	if(!$error){
		
		if($action!="view"){
			
			$form = pickForm($action, $pickID, $pickType, $calID, $userID);
			
			$userCredits = userCredits($userID);
			$userSacs = userSacs($userID);
			
			if(!userActive($userID) || ( userActive($userID) && $userCredits<=$userSacs ) ){
				
				if(!userActive($userID)){
					
					$lead = "<p class='font-weight-bold'>Nous collectons votre premier sac gratuitement !</p>";
					
					$listGroupCreditsClass = "d-none";
					$orderTotal = "5&euro;";
					
					$listGroupSac = "
					<li class='list-group-item bg-info'>
						<div class='h6'><span class='text-uppercase'>1 crédit = <strong class='text-white'>1 sac collecté</strong></span> <span class='badge badge-pill bg-secondary text-primary'>offert</span></div>
					</li>
					<li class='list-group-item bg-info pl-1'>
						<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:100px;'>
						<div class='h6'><span class='text-uppercase'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
						<span class='font-weight-bold'>Pour accéder au service vous devez faire l'acquisition d'un sac qui vous sera remis lors de votre première collecte.<br><a href='https://www.pic-verre.fr/sac' target='_blank' >En savoir plus</a></span>
					</li>";
					
				}else{
					
					if($userCredits){
						$listGroupClass = "d-none";
						$listGroupCreditsClass = "d-none";
						$formFooterClass = "d-none";
						
						
					}else{
						$orderTotal = "20&euro;";
					}
				}
				
				$formFooter = "
				<ul class='list-group mb-3 {$listGroupClass}' id='list-order'>
					{$listGroupSac}
					<li id='list-group-credits' class='list-group-item bg-info {$listGroupCreditsClass}'>
						<div class='h6'><span class='text-uppercase'><span id='creditsNb'>6</span> crédit(s)<br><strong id='creditsLibelle' class='text-white'>soit 3.33&euro; par sac collecté</strong></span> <span id='creditsTotal' class='badge badge-pill bg-secondary text-primary'>20&euro;</span></div>
					</li>
					<li class='list-group-item bg-secondary'>
						<div class='h6 text-uppercase text-white'>Total <span id='orderTotal' class='badge badge-pill bg-primary float-right'>{$orderTotal}</span></div>
					</li>
				</ul>

				<div class='form-footer form-row clearfix'>
					<div class='col-md-12 form-group mb-0'>
						<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Valider</button>
					</div>
					<div id='cgv' class='col-md-12 form-group text-center mt-2 {$formFooterClass}'>
						<p class='mb-0'><small>En validant ce formulaire, vous acceptez nos <a href='https://www.pic-verre.fr/cgv' target='_blank' class='text-nowrap link-primary'>conditions générales de vente</a></small></p>
					</div>
				</div>
				
				<p id='citelis'  class='text-center p-2 m-0 border-top border-light {$formFooterClass}'><small>".$GLOBALS['citelisLead']."</small></p>";

			}else{
				
				if($action=="delete"||$action=="update"||(userCredits($userID)&&$action=="create")){
					$formFooter = "
					<div class='form-footer form-row clearfix pt-3'>
						<div class='col form-group mb-0'>
							<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Valider</button>
						</div>
					</div>";
				
				}
			
			}
		
			$view.="
			<form action='edit/pick.edit.php' method='post' id='pickEditForm' class='needs-validation' novalidate>
				{$lead}
				{$form}				
				{$formFooter}				
			</form>";
			
			if(!userActive($userID) || ( userActive($userID) && $userCredits<=$userSacs ) ){
				
				if(!userActive($userID)){
					$sacTarif = 5;
					$scriptSacs = "
					$('#sacs').on('change', function () {
						if($('#sacs').val()==0){
							$('#list-group-credits').removeClass('d-none');
							$('#credits-row').removeClass('d-none');
							$('#formuleID').prop('disabled', false);
							$('#credits').prop('disabled', true);
							$('#credits').val(credits);
							$('#orderTotal').html('25&euro;');
						}else{
							$('#list-group-credits').addClass('d-none');
							$('#credits-row').addClass('d-none');
							$('#orderTotal').html('5&euro;');
							$('#credits').prop('disabled', true);
							$('#formuleID').prop('disabled', true);
						}
					});";
				}
				
				if(userActive($userID) && $userCredits<=$userSacs){
					$sacTarif = 0;
					
					if($userCredits){
					
						$scriptSacs = "
						$('#sacs').on('change', function () {
							if($('#sacs').val()==0){
								$('#list-order').removeClass('d-none');
								$('#list-group-credits').removeClass('d-none');
								$('#credits-row').removeClass('d-none');
								$('#cgv').removeClass('d-none');
								$('#citelis').removeClass('d-none');
								$('#formuleID').prop('disabled', false);
								$('#credits').prop('disabled', true);
								$('#credits').val(credits);
								$('#orderTotal').html('20&euro;');
							}else{
								$('#list-order').addClass('d-none');
								$('#list-group-credits').addClass('d-none');
								$('#credits-row').addClass('d-none');
								$('#cgv').addClass('d-none');
								$('#citelis').addClass('d-none');
								$('#orderTotal').html('5&euro;');
								$('#credits').prop('disabled', true);
								$('#formuleID').prop('disabled', true);
							}
						});";
					}
					
				}

				$view.="
				<script>
				
				{$scriptSacs}
						
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
						$('#credits').val(credits);
						$('#credits').prop('disabled', true);
						
						$('#credits').prop('required',false);
						$('#creditsNb').html(credits);						
						$('#creditsTotal').html($('#formuleID').find(':selected').attr('data-price')+'&euro;');
					
						var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
						$('#orderTotal').html(price+{$sacTarif}+'&euro;');
						
					}
				});

				</script>";
			}
		}
			

		$section = "
		<h3>
			<i class='fas fa-bicycle'></i> {$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>			
		<section id='pickEdit'>			
			{$view}
		</section>";		
		
	}else{
		$section = "erreur {$err}";
	}
	
	return $section;
	
}

?>