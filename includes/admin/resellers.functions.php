<?php

/* usersTable */
function resellersTable(){
	
	global $connexion;


	// USERS ACTIVE
	$resellers_rq="SELECT * FROM resellers";	
	$resellers_rs=mysqli_query($connexion, $resellers_rq) or die(mysqli_error($connexion));
	while($resellers=mysqli_fetch_array($resellers_rs)){
		
		$date_cell = convertDate($resellers['dateCreation']);
		$reseller_cell = "<a href='resellers.php?resellerID={$resellers['id']}'>".$resellers["societe"]."</a>";
		//$edit_cell = resellerBtnDrop($resellers["id"]);
		
		// USERS DETAIL RAW
		$tbody .= "
		<tr class='{$tr_class}'>
			<td>{$users['dateCreation']}</td>
			<td>{$date_cell}</td>
			<td>{$reseller_cell}</a></td>
			<td class='table-light'>{$edit_cell}</td>
		</tr>";			
	}

	$table = "
	<div class='table-responsive'>
	<table class='table table-sm table-hover' id='resellersTable'>
	<thead>
		<tr>
			<th scope='col' style='min-width:115px'>Date (en)</th>
			<th scope='col' style='min-width:115px'>Date</th>
			<th scope='col' style='min-width:270px'>Nom</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{$tbody}
	</tbody>
	<tfoot>
		<tr>
			<th scope='row' class='text-right' colspan='2'>Total</td>
			<td class='text-center bg-dark font-weight-bold'>{$credits_total}</td>
			<td class='bg-dark'></td>
		</tr>
	</tfoot>
	</table>
	</div>";
	
	return $table;

}	


/* usersPageBreadcrumb */
function resellersPageBreadcrumb($resellerID){
	
	global $connexion;
	
	if(empty($resellerID)){
		$bread = "
		<li class='breadcrumb-item'><a href='resellers.php'>Revendeurs</a></li>";

		
	}else{	

		$reseller_rq = "SELECT societe FROM resellers WHERE resellers.id=".$resellerID;
		$reseller_rs = mysqli_query($connexion, $reseller_rq) or die();
		$reseller = mysqli_fetch_array($reseller_rs);
	
		$bread = "
		<li class='breadcrumb-item'><a href='resellers.php'>Revendeurs</a></li>
		<li class='breadcrumb-item active' aria-current='page'>".$reseller['societe']."</li>";
	}					
	
	return "
	<nav>
		<ol class='breadcrumb m-0 fixed-top rounded-0'>
			{$bread}
		</ol>
	</nav>";
	
}

/* usersPageHeader */
function resellersPageHeader($resellerID){
	
	global $connexion;
	
	if(empty($resellerID)){
		
		$pageHeader = "
		<div class='page-header d-flex'>					
			<h2 class='mr-auto'>Revendeurs</h2>
		</div>";
		
		
	}else{
		
		$reseller_rq = "SELECT societe FROM resellers WHERE resellers.id=".$resellerID;
		$reseller_rs = mysqli_query($connexion, $reseller_rq) or die();
		$reseller = mysqli_fetch_array($reseller_rs);

		$pageHeader = "
		<div class='page-header d-flex'>					
			<h2 class='mr-auto'>".$reseller['societe']."</h2>
		</div>";
	}
	
	return $pageHeader;
}

/* usersPageContent */
function resellersPage(){
	
	global $connexion;
	
	$mainContent = resellersTable();
	$sideContent = "";
		
	$content = "
	<div class='row'>
		<div class='col-sm-4'>
			<button data-edit='reseller' data-rq='action=create' data-toggle='modal' data-target='#editModal' class='btn btn-warning btn-block mb-3'>Ajouter un revendeur</button> 
			{$sideContent}
		</div>	
		<div class='col-sm-8'>
			{$mainContent}
		</div>
	</div>";
		
	return $content;
		
}



?>