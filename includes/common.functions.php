<?php

/* convertDate */
function convertDate($date, $format){
	switch($format){
		case "fr2en":
			$date = date("Y-m-d", strtotime($date));
		break;
		case "en2fr":
			$date = date("d/m/Y", strtotime($date));
		break;
		case "2adb":
			$date = strftime("%a %d %b", strtotime($date));
		break;
		case "2adbY":
			$date = strftime("%a %d %b %Y", strtotime($date));
		break;
		case "2dbY":
			$date = strftime("%d %b %Y", strtotime($date));
		break;
		case "2BY":
			$date = strftime("%B %Y", strtotime($date));
		break;
		case "2bY":
			$date = strftime("%b %Y", strtotime($date));
		break;
		case "2AdB":
			$date = strftime("%A %d %B", strtotime($date));
		break;
		case "2adB":
			$date = strftime("%a %d %B", strtotime($date));
		break;
		case "2A":
			$date = strftime("%A", strtotime($date));
		break;
		case "2dB":
			$date = strftime("%d %B", strtotime($date));
		break;
		default:
			$date = date("d/m/Y", strtotime($date));
		break;
	}
	return utf8_encode($date);
}

/* formatPrice */
function formatPrice($price){
	return number_format($price, 2, ',', ' ')."&euro;";
}

/* random_str */
function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < $length; ++$i) {
		$pieces []= $keyspace[random_int(0, $max)];
	}
	return implode('', $pieces);
}	


// CAL ********************************************

/* calDate */
function calDate($calID){
	global $connexion;		
	$cal_rq = "SELECT date FROM cal WHERE id={$calID}";
	$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error());
	$cal = mysqli_fetch_assoc($cal_rs);
	return $cal["date"];
}	


// USER ********************************************

/* userExists */
function userExists($userID){	
	global $connexion;		
	$userExist_rq = "SELECT id FROM users WHERE id={$userID}";
	$userExist_rs = mysqli_query($connexion, $userExist_rq) or die(mysqli_error());
	return mysqli_num_rows($userExist_rs);
}

/* userCredits */
function userCredits($userID){
	
	global $connexion;
	
	$credits_rq = "SELECT SUM(credits.nb) AS nb FROM credits
	INNER JOIN orders ON orders.id=credits.orderID
	WHERE orders.userID={$userID} AND orders.tID!=0";
	$credits_rs = mysqli_query($connexion, $credits_rq) or die();
	$credits = mysqli_fetch_assoc($credits_rs);
	
	$bonus_rq = "
	SELECT SUM(rewards.credits) AS nb
	FROM bonus 
	INNER JOIN rewards ON rewards.id = bonus.rewardID
	WHERE bonus.userID={$userID}";
	$bonus_rs = mysqli_query($connexion, $bonus_rq) or die();
	$bonus = mysqli_fetch_assoc($bonus_rs);

	$pickCollected_rq = "
	SELECT SUM(nb) AS total
	FROM(
		SELECT SUM(collects.sacs) AS nb
		FROM picks
		INNER JOIN collects ON collects.id = picks.collectID
		WHERE picks.userID={$userID}
		UNION
		SELECT SUM(collects.sacs) AS nb
		FROM bundles
		INNER JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.userID={$userID}
	)  AS pb";
	$pickCollected_rs = mysqli_query($connexion, $pickCollected_rq) or die();
	$pickCollected = mysqli_fetch_assoc($pickCollected_rs);
	
	/*
	$pickMissed_rq = "
	SELECT COUNT(id) AS total
	FROM(
		SELECT picks.id
		FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE NOT EXISTS(
			SELECT * FROM collects WHERE collects.id = picks.collectID
		) AND picks.userID={$userID} AND cal.date<'".date("Y-m-d")."'
		UNION
		SELECT bundles.id
		FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		WHERE NOT EXISTS(
			SELECT * FROM collects WHERE collects.id = bundles.collectID
		) AND bundles.userID={$userID} AND cal.date<'".date("Y-m-d")."'
	) AS pb";
	$pickMissed_rs = mysqli_query($connexion, $pickMissed_rq) or die();
	$pickMissed = mysqli_fetch_assoc($pickMissed_rs);
	*/
	
	$pickMissed_rq = "
	SELECT COUNT(id) AS total
	FROM(
		SELECT picks.id
		FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE EXISTS(
			SELECT * FROM miss WHERE miss.pickID = picks.id
		) AND picks.userID={$userID}
		UNION
		SELECT bundles.id
		FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		WHERE EXISTS(
			SELECT * FROM miss WHERE miss.pickID = picks.id
		) AND bundles.userID={$userID}
	) AS pb";
	$pickMissed_rs = mysqli_query($connexion, $pickMissed_rq) or die();
	$pickMissed = mysqli_fetch_assoc($pickMissed_rs);
	
	$userCredits = $credits['nb']+$bonus['nb']-($pickCollected['total']+$pickMissed['total']);
	return $userCredits;
}

/* userActive */
function userActive($userID){
	
	global $connexion;
	
	if(userCredits($userID)){
		
		return 1;
		
	}else{
	
		$userActive_rq = "
		SELECT orders.id FROM credits
		INNER JOIN orders ON credits.orderID = orders.id
		INNER JOIN transactions ON transactions.id = orders.tID	
		WHERE orders.userID={$userID} ";
		$userActive_rs = mysqli_query($connexion, $userActive_rq) or die();
		
		return mysqli_num_rows($userActive_rs);
		
	}
	
}

/* userSacs */
function userSacs($userID){
	
	global $connexion;
	
	$sacs_rq = "SELECT SUM(sacs.nb) AS nb FROM sacs
	INNER JOIN orders ON orders.id=sacs.orderID
	WHERE orders.userID={$userID} AND orders.tID!=0";
	$sacs_rs = mysqli_query($connexion, $sacs_rq) or die();
	$sacs = mysqli_fetch_assoc($sacs_rs);
	
	$bonus_rq = "
	SELECT SUM(rewards.sacs) AS nb
	FROM bonus 
	INNER JOIN rewards ON rewards.id = bonus.rewardID
	WHERE bonus.userID={$userID}";
	$bonus_rs = mysqli_query($connexion, $bonus_rq) or die();
	$bonus = mysqli_fetch_assoc($bonus_rs);
	
	return $sacs['nb']+$bonus['nb'];
	
}


/* userCreditsDetail */
function userCreditsDetail($userID){
	
	global $connexion;
	
	$credits_rq = "SELECT SUM(credits.nb) AS nb FROM credits
	INNER JOIN orders ON orders.id=credits.orderID
	WHERE orders.userID={$userID} AND orders.tID!=0";
	$credits_rs = mysqli_query($connexion, $credits_rq) or die();
	$credits = mysqli_fetch_assoc($credits_rs);
	
	$bonus_rq = "
	SELECT SUM(rewards.credits) AS nb
	FROM bonus 
	INNER JOIN rewards ON rewards.id = bonus.rewardID
	WHERE bonus.userID={$userID}";
	$bonus_rs = mysqli_query($connexion, $bonus_rq) or die();
	$bonus = mysqli_fetch_assoc($bonus_rs);		
	
	$pickCollected_rq = "
	SELECT SUM(nb) AS total
	FROM(
		SELECT SUM(collects.sacs) AS nb
		FROM picks
		INNER JOIN collects ON collects.id = picks.collectID
		WHERE picks.userID={$userID}
		UNION
		SELECT SUM(collects.sacs) AS nb
		FROM bundles
		INNER JOIN collects ON collects.id = bundles.collectID
		WHERE bundles.userID={$userID}
	)  AS pb";
	$pickCollected_rs = mysqli_query($connexion, $pickCollected_rq) or die();
	$pickCollected = mysqli_fetch_assoc($pickCollected_rs);
	
	/*
	$pickMissed_rq = "
	SELECT COUNT(id) AS total
	FROM(
		SELECT picks.id
		FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE NOT EXISTS(
			SELECT * FROM collects WHERE collects.id = picks.collectID
		) AND picks.userID={$userID} AND cal.date<'".date("Y-m-d")."'
		UNION
		SELECT bundles.id
		FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		WHERE NOT EXISTS(
			SELECT * FROM collects WHERE collects.id = bundles.collectID
		) AND bundles.userID={$userID} AND cal.date<'".date("Y-m-d")."'
	) AS pb";
	$pickMissed_rs = mysqli_query($connexion, $pickMissed_rq) or die(mysqli_error($connexion));
	$pickMissed = mysqli_fetch_assoc($pickMissed_rs);
	*/
	
	$pickMissed_rq = "
	SELECT COUNT(id) AS total
	FROM(
		SELECT picks.id
		FROM picks
		INNER JOIN cal ON cal.id = picks.calID
		WHERE EXISTS(
			SELECT * FROM miss WHERE miss.pickID = picks.id
		) AND picks.userID={$userID}
		UNION
		SELECT bundles.id
		FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		WHERE EXISTS(
			SELECT * FROM miss WHERE miss.pickID = picks.id
		) AND bundles.userID={$userID}
	) AS pb";
	$pickMissed_rs = mysqli_query($connexion, $pickMissed_rq) or die();
	$pickMissed = mysqli_fetch_assoc($pickMissed_rs);
	
	return "Credits achetés : <strong>{$credits['nb']}</strong><br>Crédits offerts : <strong>{$bonus['nb']}</strong><br>Sacs collectés : {$pickCollected['total']} <br>Collectes manquées : {$pickMissed['total']}";
	
}


/* userPro */
function userPro($userID){
	
	global $connexion;
	
	$user_rq = "
	SELECT societe FROM users
	WHERE id = {$userID} AND societe!=''";
	$user_rs = mysqli_query($connexion, $user_rq) or die();
	return mysqli_num_rows($user_rs);

	
}

/* userName */
function userName($userID, $firstName){
	
	global $connexion;
	
	$user_rq = "
	SELECT users.societe, users.nom, users.prenom FROM users
	WHERE id = {$userID}";
	$user_rs = mysqli_query($connexion, $user_rq) or die();
	$user = mysqli_fetch_assoc($user_rs);
	
	if($firstName){
		return $user['prenom'];
	}else{
	
		if($user['societe']){
			return $user['societe'];
		}else{
			return $user['nom']." ".$user['prenom'];
		}
	
	}
	
}

/* userAdresse */
function userAdresse($userID){
	
	global $connexion;
	
	$userAdresse_rq = "
	SELECT adresses.voieNumero, adresses.cpl, voies.secteur, voies.voieType, voies.voieLibelle, voies.cp, voies.ville FROM users
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	WHERE users.id={$userID}";
	$userAdresse_rs = mysqli_query($connexion, $userAdresse_rq) or die();
	$userAdresse = mysqli_fetch_array($userAdresse_rs);		
	
	return "{$userAdresse['voieNumero']} {$userAdresse['voieType']}  {$userAdresse['voieLibelle']}<br/>{$userAdresse['cp']} {$userAdresse['ville']} <br/>{$userAdresse['cpl']}";
	
}

/* userSecteur */
function userSecteur($userID){
	
	global $connexion;

	$user_rq = "
	SELECT voies.secteur FROM users
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	WHERE users.id={$userID}";
	$user_rs = mysqli_query($connexion, $user_rq) or die(mysqli_error($connexion));
	$user = mysqli_fetch_assoc($user_rs);

	return $user['secteur'];
						
}

/* userPick */
function userPick($userID, $date){
	
	global $connexion;

	if(!$date){
		$date = date('Y-m-d');
	}

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
	WHERE picks.calID = {$nextCal['id']} AND picks.userID={$userID} AND valid=1
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
	
	return $nextPick;
	
}	



// ORDER ********************************************

// orderRef
function orderRef($id){
	
	global $connexion;
	
	$order_rq = "
	SELECT orders.id, orders.tID, orders.reglement, orders.pro FROM orders WHERE orders.id = {$id}";
	$order_rs=mysqli_query($connexion, $order_rq) or die(mysqli_error($connexion));
	$order=mysqli_fetch_array($order_rs);
	
	if($order['tID']!=0){
		$ref = "{$order['reglement']}-{$id}";
	}else{
		if($order['pro']){
			$ref = "DEV-{$id}";
		}else{
			$ref = "CMD-{$id}";
		}
	}
	
	return $ref;
	
}

// orderAdresse
function orderAdresse($adresse){
	
	return str_replace(',', '<br />', $adresse);
	
}

// orderReglement
function orderReglement($reglement){
	
	switch($reglement){
		case 'CB':
			return "Carte banquaire";
		break;	
		case 'CHQ':
			return "Chèque";
		break;	
		case 'ESP':
			return "Espèce";
		break;			
	}	
	
	return str_replace(',', '<br />', $adresse);
	
}

// orderType
function orderType($orderID){
	
	global $connexion;
	
	$order_rq = "
	SELECT orders.tID, orders.pro FROM orders WHERE orders.id = {$orderID}";
	$order_rs=mysqli_query($connexion, $order_rq) or die(mysqli_error($connexion));
	$order=mysqli_fetch_array($order_rs);
	
	if($order['tID']){
		$type = "facture";	
	}else{
		if($order['pro']){	
			$type = "devis";	
		}else{
			$type = "commande";
		}
	}
	return $type;
	
}


// PICK ******************************************

// pickRef 
function pickRef($pickID, $pickType){
	if($pickType=="pick"){
		$ref = "COL-";
	}
	if($pickType=="bundle"){
		$ref = "CGR-";
	}
	$ref .= $pickID;
	
	return $ref;
}

// pickAdresse 
function pickAdresse($adresseID, $full){
	
	global $connexion;

	$adresse_rq = "
	SELECT adresses.voieNumero, adresses.cpl, voies.voieType, voies.voieLibelle, users.nom, users.prenom, users.societe FROM adresses
	INNER JOIN voies ON voies.id = adresses.voieID
	LEFT JOIN users ON users.adresseID = adresses.id
	WHERE adresses.id={$adresseID}";
	$adresse_rs = mysqli_query($connexion, $adresse_rq) or die(mysqli_error($connexion));
	$adresse = mysqli_fetch_assoc($adresse_rs);
	
	
	if($full){
		if($adresse['societe']){
			$pickAdresse = $adresse['societe'];
		}else{
			$pickAdresse = $adresse['nom']." ".$adresse['prenom'];
		}
		$pickAdresse.="<br>";
	}

	$pickAdresse .= "{$adresse['voieNumero']} {$adresse['voieType']} {$adresse['voieLibelle']}";
	/*
	if($adresse['cpl']){
		$pickAdresse .= " <span class='badge badge-secondary' data-toggle='tooltip' data-placement='top' title='{$adresse['cpl']}'>?</span> ";
	}
	*/
	
	return $pickAdresse;
						
}

/* pickDate */
function pickDate($pickID, $pickType){
	
	global $connexion;
	
	if($pickType=="pick"){
		$pick_rq = "
		SELECT cal.date, slots.start, slots.end FROM picks 
		INNER JOIN cal ON cal.id=picks.calID 
		INNER JOIN slots ON slots.id = picks.slotID
		WHERE picks.id={$pickID}";
	}
	
	if($pickType=="bundle"){
		$pick_rq = "
		SELECT cal.date, slots.start, slots.end  FROM bundles 
		INNER JOIN picks ON picks.id=bundles.pickID 
		INNER JOIN slots ON slots.id = picks.slotID
		INNER JOIN cal ON cal.id=picks.calID
		WHERE picks.id={$pickID}";
	}
	
	$pick_rs = mysqli_query($connexion, $pick_rq) or die(mysqli_error());
	$pick = mysqli_fetch_assoc($pick_rs);
	
	
	$pickDate = array(
		"day" => $pick["date"],
		"start" => $pick["start"],
		"end" => $pick["end"],
	);
	
	return $pickDate;
	
}


?>