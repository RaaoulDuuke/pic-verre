<?php

/* pageStyle */
function pageStyle($page, $theme){
	$parts = Explode('/', $page);
	$page = $parts[count($parts) - 1];
	/* DEFAULT CSS */
	if($page=="connexion.php" || $page=="abonnement.php" || $page=="reset.php" || $page=="inscription.php" || $page=="programmer.php"){
		$style = "login";		
	}else{
		$style = "account";
	}
	$stylesheet = "<link href='https://assets.pic-verre.fr/css/{$style}.css' rel='stylesheet'>";
	/* MODE SOMBRE */
	if($theme=="dark"){ 
		$stylesheet.="<link href='https://assets.pic-verre.fr/css/{$style}.dark.css' rel='stylesheet'>";
	}		
	return $stylesheet;
}

/* themeLink */
function themeLink($theme){
	if($theme=="dark"){
		$themeLink = "classique";
	}else{
		$themeLink = "sombre";
	}
	return $themeLink;
}

/* selectVoies */
function selectVoies(){
	
	global $connexion;
	
	$dateFrom = date('Y-m-d', strtotime("+2 days"));
	$dateTo = date('Y-m-d', strtotime("+1 month"));
	
	$voies_rq = "
	SELECT voies.voieType, voies.voieLibelle, cal.date 
	FROM voies
	LEFT JOIN cal ON cal.secteur=voies.secteur AND cal.date BETWEEN '{$dateFrom}' AND '{$dateTo}'";
	$voies_rs = mysqli_query($connexion, $voies_rq) or die(mysqli_error());
	while($voies = mysqli_fetch_assoc($voies_rs)){
		
		$voieLabel = str_replace("'", "’",$voies['voieType'])." ".str_replace("'", "’",$voies['voieLibelle']);
		$pickDate = convertDate($voies['date'],"2AdB");
		
		$selectVoies .= "{label:'{$voieLabel}', date:'{$pickDate}'},";
	}
	
	$selectVoies = substr($selectVoies, 0, -1);
	
	return $selectVoies;
}


/* nextPick */
function nextPick($userID){
	
	global $connexion;

	if(userPro($userID)){
		
		$pick_rq = "
		SELECT picks.id FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE picks.userID={$userID} AND cal.date>=NOW()";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick_nb = mysqli_num_rows($pick_rs);
		
		if($pick_nb){
			$pick = mysqli_fetch_assoc($pick_rs);
			$nextPick = array("id"=>$pick['id'],"type"=>"pick");
		}else{
			$nextPick = array("id"=>0, "type"=>"cal");
		}
		
	}
	
	else{
		
		$userSecteur = userSecteur($userID);
		$currentDate = date("Y-m-d");
		
		$nextCal_rq = "
		SELECT cal.id, cal.date FROM cal 
		WHERE cal.date>='{$currentDate}' AND cal.secteur={$userSecteur}
		ORDER BY cal.date ASC LIMIT 1";
		$nextCal_rs = mysqli_query($connexion, $nextCal_rq) or die();
		$nextCal = mysqli_fetch_assoc($nextCal_rs);
		
		$pick_rq = "
		SELECT picks.id, 'pick' AS type FROM picks
		WHERE NOT EXISTS(
			SELECT * FROM miss WHERE miss.pickID = picks.id
		) AND picks.valid=1 AND picks.calID = {$nextCal['id']} AND picks.collectID='' AND picks.userID={$userID}
		UNION
		SELECT bundles.id, 'bundle' AS type FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		WHERE picks.calID = {$nextCal['id']} AND bundles.userID={$userID}";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick_nb = mysqli_num_rows($pick_rs);
		if(!$pick_nb){
			
			$nextCal_rq = "
			SELECT cal.id, cal.date FROM cal 
			WHERE cal.date>'{$currentDate}' AND cal.secteur={$userSecteur}
			ORDER BY cal.date ASC LIMIT 1";
			$nextCal_rs = mysqli_query($connexion, $nextCal_rq) or die();
			$nextCal = mysqli_fetch_assoc($nextCal_rs);
			
			$pick_rq = "
			SELECT picks.id, 'pick' AS type FROM picks
			WHERE NOT EXISTS(
				SELECT * FROM miss WHERE miss.pickID = picks.id
			) AND picks.valid=1 AND picks.calID = {$nextCal['id']} AND picks.collectID='' AND picks.userID={$userID}
			UNION
			SELECT bundles.id, 'bundle' AS type FROM bundles
			INNER JOIN picks ON picks.id = bundles.pickID
			WHERE picks.calID = {$nextCal['id']} AND bundles.userID={$userID}";
			$pick_rs = mysqli_query($connexion, $pick_rq) or die();
			$pick_nb = mysqli_num_rows($pick_rs);
			
			if(!$pick_nb){
			

				$nextPick = array("id"=>$nextCal['id'],"type"=>"cal");
				
			}else{
			
				$pick = mysqli_fetch_assoc($pick_rs);
				$nextPick = array("id"=>$pick['id'],"type"=>$pick['type']);
				
			}

		}else{
			
			$pick = mysqli_fetch_assoc($pick_rs);
			$nextPick = array("id"=>$pick['id'],"type"=>$pick['type']);
			
		}
		
	}
	
	return $nextPick;
	
}	


/* ASSOS *********************************************/

/* assoEdit */
function assoEdit(){
	
	global $connexion;	

	$section = "
	<h3>
		Parrainer un ami
		<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span>
		</button>
	</h3>			
	<section id='assoEdit'>
		 <form action='edit/assos.edit.php' method='post' id='assoEditForm'>
			<div class='form-row'> 
				<div class='col-md-12 form-group'>
					<p class='text-form font-weight-bold mb-0'>Saisissez l'adresse email de la personne à qui vous souhaitez envoyer une demande de parrainage.</p>
				</div>
			</div>					
			<div class='form-row'>
				<div class='col form-group mb-0'>
					<input type='email' name='email' id='email' class='form-control text-center' required />
				</div>
			</div>
			<input type='hidden' name='action' value='create'/>  
			<div class='form-footer form-row clearfix pt-3'>
				<div class='col form-group mb-0'>
					<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Envoyer</button>
				</div>
				
			</div>		
			<p class='text-center p-2 m-0 border-top border-light'><small>Le parrainage est considéré comme validé<br>dès qu'elle a fait l'acquisition d'un sac Pic'Verre.</small></p>
		</form>
	</section>";				

	// RETURN
	return $section;	
}


/* INFOS *********************************************/

/* infosEdit */
function infosEdit($userID, $action){
	
	global $connexion;	
	$error = 0;
	
	if(!empty($action)){
		switch($action){
			case "update":

				$sSQL = "
				SELECT adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle, users.email, users.tel, users.nom, users.prenom
				FROM users 
				INNER JOIN adresses ON adresses.id = users.adresseID
				INNER JOIN voies ON voies.id = adresses.voieID
				WHERE users.id={$userID}";
				$result = mysqli_query($connexion, $sSQL) or die();
				if ($row = mysqli_fetch_assoc($result)) {
					foreach ($row as $key => $value) {
						$$key = $value;
					}
				}
				mysqli_free_result($result);
				
				$selectVoie = selectVoies();
				$selectVoie_value = str_replace("'", "’",$voieType)." ".str_replace("'", "’",$voieLibelle);
				

				$title = "Mes infos";
				
				$formRowUser = "
				<div class='form-row'>
					<div class='col-md-6 form-group'>
						<label>Prénom</label>
						<input type='text' name='prenom' id='prenom' class='form-control' value='{$prenom}' required />
					</div>
					<div class='col-md-6 form-group'>
						<label>Nom</label>
						<input type='text' name='nom' id='nom' class='form-control' value='{$nom}' required />
					</div>
				</div>";
				
				
				$formRowContact = "
				<div class='form-row'>
					<div class='col-md-8 form-group'>
						<label>Email</label>
						<input type='email' name='email' id='email' class='form-control' value='{$email}' required />
					</div>
					<div class='col-md-4 form-group'>
						<label>Téléphone</label>
						<input type='text' name='tel' id='tel' class='form-control' value='{$tel}' required />
					</div>
				</div>
				<input type='hidden' name='emailPrev' id='emailPrev' value='{$email}'/>";
				
				
				
				$formRowAdresse="
				<div class='form-row'>
					<div class='form-group col-md-3'>
						<label>N°</label>
						<input type='text' name='voieNumero' id='voieNumero' class='form-control' value='{$voieNumero}' required/>
					</div>
					<div class='form-group col-md-9'>
						<label>Adresse</label>
						<input type='text' name='selectVoie' id='selectVoie' class='form-control' value='{$selectVoie_value}' required/>
					</div>
				</div>				
				<div class='form-row'>
					<div class='col-md-12 form-group'>
						<label>Adresse cpl. <small>(facultatif)</small></label>
						<input type='text' name='cpl' id='cpl' class='form-control' value='{$cpl}'/>
					</div>
				</div>";
				
				
				if(!userPro($userID)){
					$form = "
					<h4>Adresse</h4>
					{$formRowUser}
					{$formRowAdresse}
					<h4>Contact</h4>
					{$formRowContact}";
				}else{
					$form = "
					<h4>Adresse</h4>
					{$formRowAdresse}
					<h4>Contact</h4>
					{$formRowUser}
					{$formRowContact}";
				}

				$script="
				var voies = [{$selectVoie}];	 
				$( '#selectVoie').autocomplete({
					minLength: 5,
					delay: 750,
					source: voies,
					focus: function( event, ui ) {
						$( '#selectVoie').val( ui.item.label );
						return false;
					},
					select: function( event, ui ) {
						$( '#selectVoie' ).val( ui.item.label );
						return false;
					}
				});";
				
			break;
			case "updatepwd":
				$title = "Modifier mot de passe";
				$form="
				<div class='form-row'>
					<div class='col-md-6 form-group'>
						<label>Actuel</label>
						<input type='password' name='pwdPrev' id='pwdPrev' class='form-control' required />
					</div>
					<div class='col-md-6 form-group'>
						<label>Nouveau</label>
						<input type='password' name='pwd' id='pwd' class='form-control' required />
					</div>
				</div>";
			break;
			default:
				$error = 1;
			break;
		}
	}else{
		$error = 1;
	}	
	// VALID
	if(!$error){
		// FORM FOOTER
		$formFooter = "
		<div class='form-footer form-row clearfix pt-3'>
			<div class='col form-group mb-0'>
				<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Valider</button>
			</div>
		</div>";
		// SECTION
		$section = "
		<h3>
			<i class='fas fa-edit'></i> {$title}	
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>
		<section id='userEdit'>			
			<form action='edit/infos.edit.php' method='post' id='infosEditForm'>
				{$form}
				{$formFooter}
				<input type='hidden' name='action' value='{$action}'/>
			</form>
		</section>

		<script type='text/javascript'>
			{$script}
		</script>";			
	}else{
		// SECTION
		$section = "erreur";
	}
	// RETURN
	return $section;	
}


?>