<?php

/* resellerName */
function resellerName($resellerID){
	
	global $connexion;
	
	$reseller_rq = "
	SELECT societe FROM resellers
	WHERE id = {$resellerID}";
	$reseller_rs = mysqli_query($connexion, $reseller_rq) or die();
	$reseller = mysqli_fetch_assoc($reseller_rs);
	
	return $reseller['societe'];

}


/* resellerName */
function resellerLink($resellerID){
	
	global $connexion;
	
	$reseller_rq = "
	SELECT societe FROM resellers
	WHERE id = {$resellerID}";
	$reseller_rs = mysqli_query($connexion, $reseller_rq) or die();
	$reseller = mysqli_fetch_assoc($reseller_rs);
	
	return "<a class='btn btn-link' href='resellers.php?resellerID={$resellerID}'>".$reseller['societe']." <span class='badge bg-warning text-dark font-weight-bold'>Rev</span></a>";

}


/* resellerInfosTable */
function resellerInfosTable($resellerID){
	
	global $connexion;
	
	$reseller_rq = "SELECT societe, contact, tel, email, adresse FROM resellers WHERE resellers.id=".$resellerID;
	$reseller_rs = mysqli_query($connexion, $reseller_rq) or die();
	$reseller = mysqli_fetch_array($reseller_rs);
	
	if($reseller['tel']){
		$tel_cell = $reseller['tel']."<br>";
	}
	if($reseller['email']){
		$mail_cell = $reseller['email'];
	}

	$table = "
	<table class='table table-sm'>		
		<tr>
			<th class='align-top'>Contact</th>
			<td>
				{$reseller['contact']}<br/>
				{$tel_cell}
				{$mail_cell}
			</td>
		</tr>
		<tr>
			<th class='align-top'>Adresse</th>
			<td>{$reseller['adresse']}</td>
		</tr>
	</table>";
	
	return $table;
}

/* resellerPage */
function resellerPage($resellerID){
	
	global $connexion;
	
	$infosTable =  resellerInfosTable($resellerID);
	$depositsTable = depositsTable($resellerID);	
	//$pickUserTable = pickUserTable($userID);
	$depositsDetail = depositsDetail($resellerID);
	$editBtn = resellerBtnDrop($resellerID);
	
	$content = "
	<div class='row'>
		<div class='col-sm-5'>
			<h3>Infos <div class='float-right'>{$editBtn}</div></h3>
			{$infosTable}	
			<h3>Dépots</h3>
			<button data-edit='resell' data-rq='action=create&resellerID={$resellerID}' data-toggle='modal' data-target='#editModal' class='btn btn-warning btn-block mb-3'>Nouveau dépot</button>
			{$depositsTable}
		</div>
		<div class='col-sm-7'>	
			<h3>Détail</h3>
			{$depositsDetail}
		</div>
	</div>";
		
	return $content;

}

/* resellerEdit */
function resellerEdit($resellerID, $action){
	
	global $connexion;
	$err = 0;
	
	switch($action){

		case 'create':
			
			$title = "Ajouter revendeur";
			
		break;
		
		case 'update':
			if(empty($resellerID)){
				$err = 1;
				
			}else{
				
				$title = "Modifier revendeur";
				
				$sSQL = "
				SELECT * FROM resellers WHERE id={$resellerID}";
				$result = mysqli_query($connexion, $sSQL) or die(mysqli_error($connexion));
				if ($row = mysqli_fetch_assoc($result)) {
					foreach ($row as $key => $value) {
						$$key = $value;
					}
				}
				mysqli_free_result($result);
					
			}
		break;			

		default;
			$err = 1;
		break;
		
	}	

	if(!$err){
		
		$infosForm = "
		<div class='form-row'>
			<div class='col-md-12 form-group'>
				<label>Société</label>
				<input class='form-control' type='text' name='societe' value='{$societe}' />
			</div>
		</div>
		<div class='form-row'>
			<div class='col-md-12 form-group'>
				<label>Contact</label>
				<input class='form-control' type='text' name='contact' value='{$contact}' />
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
				<label>Adresse</label>
				<input class='form-control' type='text' name='adresse' value='{$adresse}' />
			</div>
		</div>";

				
		$view.= "
		<form action='edit/reseller.edit.php' method='post' id='resellerEditForm'>
		
			{$infosForm}

			<input type='hidden' name='action' value='{$action}'/>
			<input type='hidden' name='resellerID' id='resellerID' value='{$resellerID}'/>
			
			<div class='col-12 form-group mb-0'>
				<button class='btn btn-block btn-warning' type='submit'>Valider</button>
			</div>	
			
		</form>";
			
		$section = "
		<h3>
			{$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>
		
		<section id='resellerEdit'>
			{$view}
		</section>
		
		<script>
			
		</script>";	
		
		return $section;
		
	}
	
	
}

/* resellerBtnDrop */
function resellerBtnDrop($resellerID){
	
	$btns = "
	<button data-edit='reseller' data-rq='action=update&resellerID={$resellerID}' data-toggle='modal' data-target='#editModal' class='dropdown-item'>Modifier</button>";
	
	$btnDrop = "
	<div class='dropdown dropleft'>
		<button class='btn btn-sm btn-secondary dropdown-toggle' type='button' id='edit{$resellerID}' data-toggle='dropdown'></button>
		<div class='dropdown-menu' aria-labelledby='edit{$resellerID}'>
			{$btns}
		</div>
	</div>";
	
	return $btnDrop;
	
	
}

/* resellEdit */
function resellEdit($resellerID, $action){
	
	global $connexion;
	
	$err = 0;
	
	switch($action){

		case 'create':
			if(empty($resellerID)){
				$err = 1;	
			}else{
				$title = "Nouveau dépot";
			}	
		break;
				
		default;
			$err = 1;
		break;
		
	}	

	if(!$err){
		
		
		$infosForm = "
		<div class='form-row'>
			<div class='col-md-12 form-group'>
				<label>Sacs</label>
				<input class='form-control' type='text' name='sacs' value='{$sacs}' />
			</div>
		</div>";

				
		$view.= "
		<form action='edit/resell.edit.php' method='post' id='resellEditForm'>
		
			{$infosForm}

			<input type='hidden' name='action' value='{$action}'/>
			<input type='hidden' name='resellerID' id='resellerID' value='{$resellerID}'/>
			
			<div class='col-12 form-group mb-0'>
				<button class='btn btn-block btn-warning' type='submit'>Valider</button>
			</div>	
			
		</form>";
			
		$section = "
		<h3>
			{$title}
			<button type='button' class='close my-auto m-0 p-0' data-dismiss='modal' aria-label='Close'>
				<span aria-hidden='true'>&times;</span>
			</button>
		</h3>
		
		<section id='resellEdit'>
			{$view}
		</section>
		
		<script>
			
		</script>";	
		
		return $section;
		
	}
	
	
}


/* depositTable */
function depositsTable($resellerID){
	
	global $connexion;

	$deposits_rq="SELECT COUNT(id) AS sacs, dateCreation FROM resell WHERE resellerID={$resellerID} GROUP BY dateCreation";	
	$deposits_rs=mysqli_query($connexion, $deposits_rq) or die(mysqli_error($connexion));
	while($deposits=mysqli_fetch_array($deposits_rs)){
		
		$date_cell = convertDate($deposits['dateCreation']);
		$deposit_cell = $deposits['sacs'];
		
		// USERS DETAIL RAW
		$tbody .= "
		<tr>
			<td>{$date_cell}</td>
			<td>{$deposit_cell}</td>
			<td>
				<a href='edit/sticker.print.php?resellerID={$resellerID}&date={$deposits['dateCreation']}' target='_blank'>Editer stickers</a>
			</td>
		</tr>";			
	}

	$table = "
	<div class='table-responsive'>
	<table class='table table-sm table-hover' id='depositsTable'>
	<thead>
		<tr>
			<th scope='col' style='min-width:115px'>Date</th>
			<th scope='col' style='min-width:115px'>Sacs</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	</table>
	</div>";
	
	return $table;

}	


/* depositTable */
function depositsDetail($resellerID){
	
	global $connexion;

	$deposits_rq="SELECT * FROM resell WHERE resellerID={$resellerID} ORDER BY dateCreation";
	$deposits_rs=mysqli_query($connexion, $deposits_rq) or die(mysqli_error($connexion));
	while($deposits=mysqli_fetch_array($deposits_rs)){
		
		$tr_class = "";
		$dateSold_cell = "";
		$edit_cell = "";
		
		if($deposits['dateSold']!='0000-00-00'){
			$tr_class = "table-success";
			$dateSold_cell = convertDate($deposits['dateSold']);
		}else{
			$edit_cell = "<a href='edit/deposit.edit.php?depositID={$deposits['id']}'>Vendu</a>";
		}
		
		$date_cell = convertDate($deposits['dateCreation']);
		$deposit_cell = $deposits['code'];
		
		// USERS DETAIL RAW
		$tbody .= "
		<tr class='{$tr_class}'>
			<td>{$date_cell}</td>
			<td>{$deposit_cell}</td>
			<td>{$dateSold_cell}</td>
			<td>{$edit_cell}</td>
		</tr>";			
	}

	$table = "
	<div class='table-responsive'>
	<table class='table table-sm table-hover' id='depositsTable'>
	<thead>
		<tr>
			<th scope='col' style='min-width:115px'>Date</th>
			<th scope='col' style='min-width:115px'>Sac</th>
			<th scope='col' style='min-width:115px'>Vendu</th>
			<th>
			</th>
		</tr>
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	</table>
	</div>";
	
	return $table;

}	


?>