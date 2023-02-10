<?php
	require_once ("connect/identifiants_bdd.php");
	require_once ("connect/connexion_sql.php");
	global $connexion; 
	$connexion = Connexion (NOM, PASSE, BASE, SERVEUR);
	
	setlocale(LC_TIME, 'french');	
	mysqli_query($connexion,"SET CHARACTER SET 'utf8'");

?>