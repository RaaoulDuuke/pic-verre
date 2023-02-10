<?php 

	require_once ("../../includes/connect.php");

		
	$search = $_POST['search'];
	
	$response = array();		
	 
	$dateFrom = date('Y-m-d', strtotime("+1 days"));
	$dateTo = date('Y-m-d', strtotime("+7 days"));
	
	$voies_rq = "
	SELECT voies.voieType, voies.voieLibelle, cal.id AS calID
	FROM voies
	LEFT JOIN cal ON cal.secteur=voies.secteur AND cal.date BETWEEN '{$dateFrom}' AND '{$dateTo}'
	WHERE voies.voieMerged LIKE '%{$search}%'";
	$voies_rs = mysqli_query($connexion, $voies_rq) or die(mysqli_error($connexion));
	while($voies = mysqli_fetch_assoc($voies_rs)){
		
		$voieLabel = str_replace("'", "’",$voies['voieType'])." ".str_replace("'", "’",$voies['voieLibelle']);
		$response[] = array("label"=>$voieLabel,"calID"=>$voies['calID']);
		
	}

	echo json_encode($response); 

	exit;

?>