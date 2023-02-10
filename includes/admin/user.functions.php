<?php

// userLink
function userLink($userID){
	
	$user = userName($userID);
	if(userPro($userID)){
		$badge = "<span class='badge bg-warning text-dark font-weight-bold'>Pro</span>";	
	}
	
	$link = "<a class='btn btn-link' href='users.php?userID={$userID}'>{$user} {$badge}</a>";
	
	return $link;
	
}

/* userInfos */
function userInfos($userID){
	
	global $connexion;
	
	$user_rq = "SELECT nom, prenom, tel, email FROM users WHERE users.id=".$userID;
	$user_rs = mysqli_query($connexion, $user_rq) or die();
	$user = mysqli_fetch_array($user_rs);
	
	$secteur_td = userSecteur($userID);
	$adresse_td = userAdresse($userID);
	
	if($user['tel']){
		$tel_tr = "
		<tr>
			<th  width='100' class='align-top'>Tel</th>
			<td>
				<a href='tel:{$user['tel']}'>{$user['tel']}</a>
			</td>
		</tr>";
	}
	
	if($user['email']){
		$mail_tr = "
		<tr>
			<th  width='100' class='align-top'>E-mail</th>
			<td>
				<a href='mailto:{$user['email']}'>{$user['email']}</a>
			</td>
		</tr>";
	}
	
	if(userPro($userID)){
		$contact_tr = "
		<tr>
			<th class='align-top'>Nom</th>
			<td>
				{$user['prenom']} {$user['nom']}
			</td>
		</tr>";
	}
	
	
	if(userActive($userID)){
		
		$td_class="bg-warning";
		
		$credits = userCredits($userID);
		$sacs_td = userSacs($userID);
		$creditsDetail = userCreditsDetail($userID);
		$credits_td = "<a href='#' data-toggle='tooltip' data-html='true' title='{$creditsDetail}' class='text-white'>{$credits}</a>";
		
		if(!userCredits($userID)){
			$td_class = "bg-danger";
		}
		
		$infos_table = "
		<table class='table font-weight-bold mb-4'>
			<tr>
				<th width='100'>Crédits</th>
				<td class='{$td_class} text-center' style='font-size:120%'>{$credits_td}</td>
				<th width='100'>Sacs</th>
				<td class='text-center'  style='font-size:120%'>{$sacs_td}</td>
			</tr>
		</table>";
		
	}
	
	$section = "
	
	{$infos_table}
	
	<div class='nav nav-tabs nav-fill' id='infos-tab'>
		<a class='nav-item nav-link active font-weight-bolder' data-toggle='tab' href='#contact'>Contact</a>
		<a class='nav-item nav-link font-weight-bolder' data-toggle='tab' href='#adresse'>Adresse</a>
	</div>
	<div class='tab-content' id='infos-tabContent'>
		<div class='tab-pane active' id='contact'>
			<table class='table  table-sm  font-weight-bold'>
				{$contact_tr}
				{$tel_tr}
				{$mail_tr}
			</table>		
		</div>
		<div class='tab-pane' id='adresse'>
			<table class='table table-sm font-weight-bold'>		
				<tr>
					<th width='100' class='align-top'>Voie</th>
					<td>{$adresse_td}</td>
				</tr>
				<tr>
					<th width='100' class='align-top'>Secteur</th>
					<td>{$secteur_td}</td>
				</tr>
			</table>
		</div>
	</div>";
	
	return $section;
}

/* userPage */
function userPage($userID){
	
	
	global $connexion;
	
	$userInfos =  userInfos($userID);
	$ordersTab = ordersTab('', '', $userID);	
	$editBtn = userBtnDrop($userID);
	
	
	if(userActive($userID)){
		$pickUserTable = pickUserTable($userID);
		$picksTable = picksTable('', '','', 'future', $userID).picksTable('', '','', 'closed', $userID);
	}
	
	$page = "
	<div class='row'>
		<div class='col-sm-5'>
			<section class='mb-5'>
				<h3>Infos <div class='float-right'>{$editBtn}</div></h3>
				{$userInfos}
			</section>
			<section class='mb-5'>
				<h3>Commandes</h3>
				{$ordersTab}
			</section>
		</div>
		<div class='col-sm-7'>	
			<h3>Collectes</h3>
			{$pickUserTable}
			{$picksTable}
		</div>
	</div>";
		
	return $page;

}

/* userBtnDrop */
function userBtnDrop($userID){
	
	$btns = "
	<button data-edit='user' data-rq='action=update&edit=contact&userID={$userID}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Modifier contact</button>
	<button data-edit='user' data-rq='action=update&edit=adresse&userID={$userID}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Modifier adresse</button>";
	
	$btnDrop = "
	<div class='dropdown dropleft'>
		<button class='btn btn-sm btn-secondary dropdown-toggle' type='button' id='edit{$userID}' data-toggle='dropdown'></button>
		<div class='dropdown-menu' aria-labelledby='edit{$userID}'>
			{$btns}
		</div>
	</div>";
	
	return $btnDrop;	
	
	
}

/* userEdit */
function userEdit($userID, $action, $edit){
	
	global $connexion;
	$err = 0;
	
	switch($action){
	
		case 'view':
		
			$title = "Détail abonné";
			$view = userInfos($userID);
		
		break;
		
		
		case 'create':
			
			$title = "Ajouter abonné";
			
		break;
		
		case 'update':
			if(empty($userID)){
				$err = 1;
				
			}else{
				
				if($action=="update"){
					$title = "Modifier abonné";
				}
				if($action=="delete"){
					$title = "Supprimer abonné";
				}
				
				$sSQL = "
				SELECT users.nom, users.prenom, users.societe, users.tel, users.email, users.adresseID, adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle, voies.secteur FROM users 
				INNER JOIN adresses ON adresses.id=users.adresseID
				INNER JOIN voies ON voies.id = adresses.voieID
				WHERE users.id={$userID}";
				$result = mysqli_query($connexion, $sSQL) or die(mysqli_error($connexion));
				if ($row = mysqli_fetch_assoc($result)) {
					foreach ($row as $key => $value) {
						$$key = $value;
					}
				}
				mysqli_free_result($result);
				
				$voieSelect = str_replace("'", "’",$voieType)." ".str_replace("'", "’",$voieLibelle);
					
			}
		break;			

		default;
			$err = 1;
		break;
		
	}	

	if(!$err){
		
		$infosForm = "
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
		<div class='form-row'>
			<div class='col-md-12 form-group'>
				<label>Société</label>
				<input class='form-control' type='text' name='societe' value='{$societe}' />
			</div>
		</div>";
		
		$adresseForm = "
		<h4>Adresse</h4>
		<div class='form-row'>
			<div class='col-md-3 form-group'>
				<label>Numero</label>
				<input class='form-control' type='text' name='voieNumero' value='{$voieNumero}' />
			</div>
			<div class='col-md-9 form-group'>
				<label>Voie</label>
				<input class='form-control' type='text' name='voieSelect' value='{$voieSelect}'/>
			</div>
		</div>
		<div class='form-row'>
			<div class='col-md-12 form-group'>
				<label>Complément d'adresse</label>
				<input class='form-control' type='text' name='voieCpl' value='{$cpl}' />
			</div>
		</div>";
		
		
		if($action=='create'){
			$form = $infosForm.$adresseForm;	
		}
		
		if($action=='update'&&$edit=='contact'){
			$form = $infosForm;	
		}
		
		if($action=='update'&&$edit=='adresse'){
			$form = $adresseForm;	
		}
		
		
		if($action!="view"){
					
			$view.= "
			<form action='edit/user.edit.php' method='post' id='userEditForm'>
			
				{$form}

				<input type='hidden' name='action' value='{$action}'/>
				<input type='hidden' name='edit' value='{$edit}'/>
				<input type='hidden' name='userID' id='userID' value='{$userID}'/>
				<input type='hidden' name='adresseID' value='{$adresseID}'/>
				<input type='hidden' name='prevSecteur' value='{$secteur}'/>
				<input type='hidden' name='prevEmail' value='{$email}'/>
				
				<div class='col-12 form-group mb-0'>
					<button class='btn btn-block btn-warning' type='submit'>Valider</button>
				</div>	
				
			</form>";
			
		}
		
		
		$section = "
		<h3>
			{$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>
		
		<section id='userEdit'>
			{$view}
		</section>
		
		<script>
			
		</script>";	
		
		return $section;
		
	}
	
	
}


?>