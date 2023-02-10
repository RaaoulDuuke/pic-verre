<?php 

	require_once ("../../includes/connect.php");

	$calID = $_POST['calID'];
	
	$voies_rq = "SELECT cal.date  FROM cal WHERE cal.id={$calID}";
	$voies_rs = mysqli_query($connexion, $voies_rq) or die(mysqli_error($connexion));
	$voies = mysqli_fetch_assoc($voies_rs);
		
	$pickDate = strftime("%A %d %B", strtotime($voies['date']));

	echo utf8_encode($pickDate);

?>