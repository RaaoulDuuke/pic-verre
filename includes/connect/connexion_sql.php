<?php
// Fonction Connexion: connexion à MySQL
function Connexion ($pNom, $pMotPasse, $pBase, $pServeur) {
 	// Connexion au serveur 
	$connexion = mysqli_connect ($pServeur, $pNom, $pMotPasse,$pBase );
	if (!$connexion) {
		echo "Désolé, connexion au serveur $pServeur impossible\n";
		exit;
	}
  	
	// Connexion à la base
	
	if (!mysqli_select_db ($connexion, $pBase)) {
		echo "Désolé, accès à la base $pBase impossible\n";
		echo "<B>Message de MySQL :</B> " . mysql_error($connexion);
		exit;
  	}
  	// On renvoie la variable de connexion
 	 return $connexion;
}
?>
