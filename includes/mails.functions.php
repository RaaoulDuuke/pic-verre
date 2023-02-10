<?php

/* sendMail */
function sendMail($email, $title, $lead, $content, $mailBody, $unsub, $html, $img){
	
	require_once('../../PHPMailer/src/Exception.php');
	require_once('../../PHPMailer/src/PHPMailer.php');
	require_once('../../PHPMailer/src/SMTP.php');

	$mailHead = file_get_contents('../includes/mailing/email-head.php');
	
	if(!empty($unsub)){
		$unsubContent = "
		Pour ne plus recevoir les notifications de vos prochaines collectes <a href='https://mails.pic-verre.fr/unsub.php?e={$email}&u={$unsub}' style='text-decoration:none;color:#FFF;'><span style='color:#FFF;border-bottom:1px solid #FFF;'>cliquez ici</span></a></small>";
	}
	
	if(!empty($html)){	
		$link="https://mails.pic-verre.fr/archives/{$html}.html";
		$headerContent = "Si vous ne voyez pas cet email, <a href='{$link}' target='_blank' style='text-decoration:none;border-bottom:1px solid #828282;color:#828282;'><span style='color:#828282;'>affichez&nbsp;le&nbsp;dans&nbsp;votre&nbsp;navigateur&nbsp;web</span></a>.";	
	}
	
	if(empty($img)){
		$img = "logo-pv-mailing.png";
	}
	
	
	$subject = $title;
	$body = "
	
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
{$mailHead}
<body bgcolor='#11a66d' leftmargin='0' marginwidth='0' topmargin='' marginheight='0' offset='0'>
<center style='background-color:#11a66d;'>
<table bgcolor='#11a66d' border='0' cellpadding='0' cellspacing='0' height='100%' width='100%' id='bodyTable' style='table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;'>
<tr>
<td align='center' valign='top' id='bodyCell'>


<!-- EMAIL BODY // -->
<table bgcolor='#11a66d'  border='0' cellpadding='0' cellspacing='0' width='500' id='emailBody'>


<!-- MODULE ROW // -->
<tr>
	<td align='center' valign='top'>
		<!-- CENTERING TABLE // -->
		<table border='0' cellpadding='0' cellspacing='0' width='100%' style='color:#FFFFFF;' bgcolor='#11a66d'>
			<tr>
				<td align='center' valign='top'>
					<!-- FLEXIBLE CONTAINER // -->
					<table border='0' cellpadding='0' cellspacing='0' width='500' class='flexibleContainer'>
						<tr>
							<td align='center' valign='top' width='500' class='flexibleContainerCell'>

								<!-- CONTENT TABLE // -->
								<table border='0' cellpadding='30' cellspacing='0' width='100%'>
									<tr>
										<td align='center' valign='top'>
											
											<img src='https://assets.pic-verre.fr/img/{$img}' width='420' class='flexibleImage' style='max-width:440px;width:100%;display:block;margin-bottom:20px;' alt='Pic-Verre.fr' title='Pic-Verre.fr'>
											
											<h1 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:28px;font-weight:bold;margin-bottom:5px;text-align:center; text-transform:uppercase;'>
											<strong>{$title}</strong>
											</h1>
											
											<div style='text-align:center;font-weight:bold;font-family:Helvetica,Arial,sans-serif;font-size:19px;color:#ffffff;line-height:135%;margin-bottom:20px;'>
											<strong>{$lead}</strong>
											</div>
											
											<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:15px;color:#ffffff;line-height:135%;margin-bottom:0;'>
											<strong>{$content}</strong>
											</div>

											
										</td>
									</tr>
								</table>
								<!-- // CONTENT TABLE -->

							</td>
						</tr>
					</table>
					<!-- // FLEXIBLE CONTAINER -->
				</td>
			</tr>
		</table>
		<!-- // CENTERING TABLE -->
	</td>
</tr>
<!-- // MODULE ROW -->
		
{$mailBody}

</table>
			
<!-- EMAIL FOOTER // -->
<table bgcolor='#11a66d' border='0' cellpadding='0' cellspacing='0' width='500' id='emailFooter'>
	<!-- FOOTER ROW // -->
	<tr>
		<td align='center' valign='top'>
			<!-- CENTERING TABLE // -->
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td align='center' valign='top'>
						<!-- FLEXIBLE CONTAINER // -->
						<table border='0' cellpadding='0' cellspacing='0' width='500' class='flexibleContainer'>
						<tr>
							<td align='center' valign='top' width='500' class='flexibleContainerCell'>
								<table border='0' cellpadding='15' cellspacing='0' width='100%'>
								<tr>
									<td valign='top'>

										<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:125%; color:#FFF;'>
											<strong>Tout l'équipe Pic'Verre vous remercie<br>pour la confiance que vous portez en notre service.</sttrong>
										</div>

									</td>
								</tr>								
								<tr>
									<td valign='top'>

										<div style='margin-bottom:5px; text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:115%; color:#FFF;'>
											<strong>Afin de limiter la pollution numérique pensez à supprimer cet email.</strong><br><br>
											Vous avez reçu cet email car vous avez créé un compte Pic'Verre.<br>
											Pour ne plus recevoir les notifications de vos prochaines collectes <a href='https://mails.pic-verre.fr/unsub.php?e={$email}&u={$unsub}' style='text-decoration:none;color:#FFF;'><span style='color:#FFF;border-bottom:1px solid #FFF;'>cliquez ici</span></a><br><br>
											&#169; 2019 <a href='https://www.pic-verre.fr/' target='_blank' style='text-decoration:none;color:#FFF;'><span style='color:#FFF;border-bottom:1px solid #FFF;'>Pic&rsquo;Verre</span></a> &bull; <a href='https://www.pic-verre.fr/mentions-legales' target='_blank' style='text-decoration:none;color:#FFF;'><span style='color:#FFF; border-bottom:1px solid #FFF;'>Mentions légales</span></a>
										</div>

									</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
						<!-- // FLEXIBLE CONTAINER -->
					</td>
				</tr>
			</table>
			<!-- // CENTERING TABLE -->
		</td>
	</tr>
</table>	
	

</td>
</tr>
</table>
</center>
</body>
</html>";
	
		
	$mail = new PHPMailer\PHPMailer\PHPMailer;
	$mail->CharSet = "UTF-8";
	$mail->Encoding = 'base64';

	try {
		//Recipients
		$mail->setFrom('contact@pic-verre.fr', 'Pic-Verre.fr ');
		$mail->addReplyTo('contact@pic-verre.fr', 'Pic-Verre.fr');
		$mail->addAddress($email);
		$mail->AddCC("dev@pic-verre.fr");
		//Content
		$mail->AddEmbeddedImage('https://assets.pic-verre.fr/img/'.$img, 'logo', 'logo.png');
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		// $mail->AltBody = "This is the body in plain text for non-HTML mail clients";
		$mail->send();

	} catch (Exception $e) {
		// $action = "_errMail";
	}
	
	$mail->clearAddresses();
			
	// return $action;
}

/* sendMail */
function sMail($email, $title, $lead, $content, $mailBody, $unsub, $html, $img){
	
	require_once('../../PHPMailer/src/Exception.php');
	require_once('../../PHPMailer/src/PHPMailer.php');
	require_once('../../PHPMailer/src/SMTP.php');

	$mailHead = file_get_contents('../includes/mailing/email-head.php');
	
	if(!empty($unsub)){
		$unsubContent = "
		Pour ne plus recevoir les notifications de vos prochaines collectes <a href='https://mails.pic-verre.fr/unsub.php?e={$email}&u={$unsub}' style='text-decoration:none;color:#FFF;'><span style='color:#FFF;border-bottom:1px solid #FFF;'>cliquez ici</span></a></small>";
	}
	
		
	if(!empty($html)){	
		$link="https://mails.pic-verre.fr/archives/{$html}.html";
		$headerContent = "Si vous ne voyez pas cet email, <a href='{$link}' target='_blank' style='text-decoration:none;border-bottom:1px solid #828282;color:#828282;'><span style='color:#828282;'>affichez&nbsp;le&nbsp;dans&nbsp;votre&nbsp;navigateur&nbsp;web</span></a>.";	
	}
	
	if(empty($img)){
		$img = "logo-pv-mailing.png";
	}
	
	
	$subject = "Pic-Verre.fr | {$title}";
	$body = "
	
<center style='background-color:#11a66d;'>
<table bgcolor='#11a66d' border='0' cellpadding='0' cellspacing='0' height='100%' width='100%' id='bodyTable' style='table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;'>
<tr>
<td align='center' valign='top' id='bodyCell'>


<!-- EMAIL BODY // -->
<table bgcolor='#11a66d'  border='0' cellpadding='0' cellspacing='0' width='500' id='emailBody'>


<!-- MODULE ROW // -->
<tr>
	<td align='center' valign='top'>
		<!-- CENTERING TABLE // -->
		<table border='0' cellpadding='0' cellspacing='0' width='100%' style='color:#FFFFFF;' bgcolor='#11a66d'>
			<tr>
				<td align='center' valign='top'>
					<!-- FLEXIBLE CONTAINER // -->
					<table border='0' cellpadding='0' cellspacing='0' width='500' class='flexibleContainer'>
						<tr>
							<td align='center' valign='top' width='500' class='flexibleContainerCell'>

								<!-- CONTENT TABLE // -->
								<table border='0' cellpadding='30' cellspacing='0' width='100%'>
									<tr>
										<td align='center' valign='top'>
											
											<img src='https://assets.pic-verre.fr/img/{$img}' width='420' class='flexibleImage' style='max-width:440px;width:100%;display:block;margin-bottom:20px;' alt='Pic-Verre.fr' title='Pic-Verre.fr'>
											
											<h1 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:25px;font-weight:bold;margin-bottom:5px;text-align:center; text-transform:uppercase;'>
											<strong>{$title}</strong>
											</h1>
											
											<div style='text-align:center;font-weight:bold;font-family:Helvetica,Arial,sans-serif;font-size:19px;color:#ffffff;line-height:135%;margin-bottom:20px;'>
											<strong>{$lead}</strong>
											</div>
											
											<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:15px;color:#ffffff;line-height:135%;margin-bottom:0;'>
											<strong>{$content}</strong>
											</div>

											
										</td>
									</tr>
								</table>
								<!-- // CONTENT TABLE -->

							</td>
						</tr>
					</table>
					<!-- // FLEXIBLE CONTAINER -->
				</td>
			</tr>
		</table>
		<!-- // CENTERING TABLE -->
	</td>
</tr>
<!-- // MODULE ROW -->
		
{$mailBody}

</table>
			
<!-- EMAIL FOOTER // -->
<table bgcolor='#11a66d' border='0' cellpadding='0' cellspacing='0' width='500' id='emailFooter'>
	<!-- FOOTER ROW // -->
	<tr>
		<td align='center' valign='top'>
			<!-- CENTERING TABLE // -->
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td align='center' valign='top'>
						<!-- FLEXIBLE CONTAINER // -->
						<table border='0' cellpadding='0' cellspacing='0' width='500' class='flexibleContainer'>
						<tr>
							<td align='center' valign='top' width='500' class='flexibleContainerCell'>
								<table border='0' cellpadding='15' cellspacing='0' width='100%'>
								<tr>
									<td valign='top'>

										<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:125%; color:#FFF;'>
											<strong>Tout l'équipe Pic'Verre vous remercie<br>pour la confiance que vous portez en notre service.</sttrong>
										</div>

									</td>
								</tr>								
								<tr>
									<td valign='top'>

										<div style='margin-bottom:5px; text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:115%; color:#FFF;'>
											<strong>Afin de limiter la pollution numérique pensez à supprimer cet email.</strong><br><br>
											Vous avez reçu cet email car vous avez créé un compte Pic'Verre.<br>
											Pour ne plus recevoir les notifications de vos prochaines collectes <a href='https://mails.pic-verre.fr/unsub.php?e={$email}&u={$unsub}' style='text-decoration:none;color:#FFF;'><span style='color:#FFF;border-bottom:1px solid #FFF;'>cliquez ici</span></a><br><br>
											&#169; 2019 <a href='https://www.pic-verre.fr/' target='_blank' style='text-decoration:none;color:#FFF;'><span style='color:#FFF;border-bottom:1px solid #FFF;'>Pic&rsquo;Verre</span></a> &bull; <a href='https://www.pic-verre.fr/mentions-legales' target='_blank' style='text-decoration:none;color:#FFF;'><span style='color:#FFF; border-bottom:1px solid #FFF;'>Mentions légales</span></a>
										</div>

									</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
						<!-- // FLEXIBLE CONTAINER -->
					</td>
				</tr>
			</table>
			<!-- // CENTERING TABLE -->
		</td>
	</tr>
</table>	
	

</td>
</tr>
</table>
</center>";

$arr = [ 
          'email'=>"{$email}",
          'username'=>"test",
          'html'=>"{$body}",
          'subject'=>"{$subject}",
       ];
$postData = json_encode( $arr );


	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/email",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{\"sender\":{\"name\":\"Pic'Verre\",\"email\":\"contact@pic-verre.fr\"},\"to\":[{\"email\":\"{$email}\",\"name\":\"Francois Ducos\"}],\"replyTo\":{\"name\":\"Pic'Verre\",\"email\":\"contact@pic-verre.fr\"},\"subject\":\"{$subject}\",\"htmlContent\":\"{$body}\",\"textContent\":\"test\"}",
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"api-key: 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",
		"content-type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}


}


/* orderTableMail */
function orderTableMail($orderID){
	
	global $connexion;
	
	$order_rq = "
	SELECT orders.*, users.nom, users.prenom, users.societe, sacs.montant AS sMt, sacs.nb AS sNb, credits.nb AS cNb, credits.montant AS cMt
	FROM orders
	INNER JOIN users ON users.id = orders.userID
	LEFT JOIN credits ON credits.orderID=orders.id
	LEFT JOIN sacs ON sacs.orderID=orders.id
	WHERE orders.id = {$orderID}";
	$order_rs = mysqli_query($connexion, $order_rq) or die(mysqli_error($connexion));
	
	if($order = mysqli_fetch_assoc($order_rs)){
		
		$rowContent = "";
		
		$orderMontant = formatPrice($order['montant']);
		
		if($order['cNb']){
			
			if(!empty($order['societe'])){
				$montantCreditsCell = formatPrice($order['cMt']/1.2);
				$priceCreditsCell = formatPrice($order['cMt']/$order['cNb']/1.2);
				
			}else{
				$montantCreditsCell = formatPrice($order['cMt']);
				$priceCreditsCell = formatPrice($order['cMt']/$order['cNb']);
			}
			
			$tbody.="
			<tr>
				<td>Crédit(s)</td>
				<td style='text-align:center;'>{$order['cNb']}</td>
				<td style='text-align:center;'>{$priceCreditsCell}</td>
				<td style='text-align:right;'>{$montantCreditsCell}</td>
			</tr>";
			
		}
		
		if($order['sNb']){
			
			if(!empty($order['societe'])){
				$montantSacsCell = formatPrice(($order['sMt'])/1.2);
				$sacPrice = formatPrice((($order['sMt'])/1.2)/$order['sNb']);
			}else{
				$montantSacsCell = formatPrice(($order['sMt']));
				$sacPrice = formatPrice(($order['sMt'])/$order['sNb']);
			}
			
			$tbody.="
			<tr>
				<td>Sac(s)</td>
				<td style='text-align:center;'>{$order['sNb']}</td>
				<td style='text-align:center;'>{$sacPrice}</td>
				<td style='text-align:right;'>{$montantSacsCell}</td>
			</tr>";
			
			$sacNotif = "<small>Le(s) sac(s) vous seront remis lors de votre prochaine collecte.<small>";
			
		}
		
		if($order['remise']){
			
			$montantRemiseCell = formatPrice($order['remise']);
			
			$tbody.="
			<tr>
				<td colspan='3'>Remise</td>
				<td style='text-align:right;'>- {$montantRemiseCell}</td>
			</tr>";		
			
		}
		
		if($order['societe']){
			
			$htLabel = "HT";
			
			$montantTotalHtCell = formatPrice($order['montant']/1.2);
			$montantTvaCell = formatPrice($order['montant']-($order['montant']/1.2));
				
			$tfoot = "
			<tr>
				<th colspan='3'>Total HT</th>
				<td style='text-align:right;'>{$montantTotalHtCell}</td>
			</tr>
			<tr>
				<th colspan='3'>TVA (20%)</th>
				<td style='text-align:right;'>{$montantTvaCell}</td>
			</tr>";
		}
		
		$tfoot .= "
		<tr>
			<th colspan='3'>Total</th>
			<td style='text-align:right;'>{$orderMontant}</td>
		</tr>";
		
		
		$rowContent ="
		<h2 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:17px;font-weight:bold;margin-bottom:5px; text-transform:uppercase; text-align:left;'><strong>Détail de la commande</strong></h2>
		
		<div style='font-weight:normal;font-family:Helvetica,Arial,sans-serif;font-size:15px;color:#ffffff;line-height:135%; margin-bottom:25px; text-align:left;'>
		
		<table align='left' border='0' cellpadding='0' cellspacing='0' width='440' style='max-width: 100%; color:#FFF; text-align:left;'>
			<thead class='bg-secondary'>
				<tr>
					<th>Désignation</th>
					<th style='text-align:center;'>Qté</th>
					<th style='text-align:center;'>Prix {$htLabel}</th>
					<th style='text-align:right;'>Montant {$htLabel}</th>
				</tr>
			</thead>
			<tbody>
				{$tbody}
			</tbody>
			<tfoot class='table-dark'>
				{$tfoot}
			</tfoot>		
		</table>
		
		{$sacNotif}
		
		</div>";		
			
		return $rowContent;
	
	}
	
}

/* orderMail */
function orderMail($orderID){
	
	global $connexion;
	
	$order_rq = "
	SELECT users.email, orders.userID, orders.reglement, credits.id AS creditsID, sacs.id AS sacsID FROM orders
	INNER JOIN users ON users.id = orders.userID
	LEFT JOIN credits ON credits.orderID=orders.id
	LEFT JOIN sacs ON sacs.orderID=orders.id
	WHERE orders.id = {$orderID}";
	$order_rs = mysqli_query($connexion, $order_rq) or die();
	$order = mysqli_fetch_assoc($order_rs);
	
	$userName = ucfirst(userName($order['userID'],1));
	$orderRef = orderRef($orderID);
	
	$content = orderTableMail($orderID);
	
	if(!userActive($order['userID'])){
		
		$title = "Bienvenue sur Pic'Verre";
		
		if(!userPro($order['userID'])){
			
			$nextPick =  userPick($order['userID']);
			$cal_rq = "SELECT cal.date FROM cal WHERE cal.id={$nextPick['id']}";
			$cal_rs = mysqli_query($connexion, $cal_rq) or die(mysqli_error($connexion));
			$cal = mysqli_fetch_assoc($cal_rs);
			$pickDate = ucwords(convertDate($cal['date'],"2adB"));
			
			$content .= "
			<div style='text-align:center;font-weight:normal;font-family:Helvetica,Arial,sans-serif;font-size:17px;color:#ffffff;line-height:135%;margin-bottom:20px; margin-top:20px; display:block;'>
			Connectez-vous à votre compte dès maintenant pour programmer votre première collecte.
			</div>
			<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:115%;padding:15px;background:#ffc107; display:block;' href='https://mon-compte.pic-verre.fr/'><strong>Programmer ma collecte</strong></a>";
			
		}else{
			
			$token =  random_str(64);
			$resetDelete_rq = "DELETE FROM reset WHERE email='{$order['email']}'";
			$resetDelete_rs = mysqli_query($connexion, $resetDelete_rq) or die();
			$resetCreate_rq = "INSERT INTO reset (email, token) VALUES ('{$order['email']}','{$token}')";
			$resetCreate_rs = mysqli_query($connexion, $resetCreate_rq) or die();
			
			$content .= "
			<div style='text-align:center;font-weight:normal;font-family:Helvetica,Arial,sans-serif;font-size:17px;color:#ffffff;line-height:135%;margin-bottom:20px; margin-top:20px; display:block;'>
			Initialisez votre mot de passe dès maintenant pour accéder à votre compte et programmer votre première collecte.
			</div>
			<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:115%;padding:15px;background:#ffc107; display:block;' href='https://mon-compte.pic-verre.fr/reset.php?action=resetpwd&token={$token}&email={$order['email']}'><strong>Initialiser le mot de passe</strong></a>";
			
		}
		
			
	}else{

		$title = "Commande {$orderRef}";

	}
	
	$lead = "Merci {$userName} pour votre commande et pour la confiance que vous portez en notre service.";
	
	sendMail($order['email'], $title, $lead, $content);
	
}

/* pickMail */
function pickMail($pickID, $pickType){
	
	global $connexion;
	
	// PICK REQUEST		
	if($pickType=="pick"){
		$pick_rq = "
		SELECT picks.id, picks.sacs, slots.start, slots.end, cal.date, users.nom AS pickNom, users.prenom AS pickPrenom, users.email, adresses.voieNumero, voies.voieType, voies.voieLibelle
		FROM picks
		INNER JOIN adresses ON adresses.id = picks.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		INNER JOIN slots ON slots.id = picks.slotID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id=picks.userID
		WHERE picks.id = {$pickID}";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick = mysqli_fetch_assoc($pick_rs);
		
		

	}
	// BUNDLE REQUEST
	if($pickType=="bundle"){
		$pick_rq = "
		SELECT bundles.sacs, slots.start, slots.end, cal.date, users.nom, users.prenom, users.email, pickUser.email AS pickEmail, pickUser.nom AS pickNom, pickUser.prenom AS pickPrenom, adresses.voieNumero, voies.voieType, voies.voieLibelle
		FROM bundles
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN adresses ON adresses.id = picks.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		INNER JOIN slots ON slots.id = picks.slotID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id=bundles.userID
		INNER JOIN users AS pickUser ON pickUser.id = picks.userID
		WHERE bundles.id = {$pickID}";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick = mysqli_fetch_assoc($pick_rs);
		
		$lead = "Votre demande de collecte groupée à celle de ".ucwords($pick['pickPrenom']." ".$pick['pickNom'])." a bien été enregistrée.";
		$contentInfos = "Vous vous engagez à remettre <strong>{$pick['sacs']} sac(s)</strong> à ".ucwords($pick['pickPrenom'])." avant <strong>".ucwords(convertDate($pick['date'],"2adB"))."</strong>.";
	}
	
	
	// MAIL PICK DATAS
	$email = $pick['email'];
	$pickUser = ucwords($pick['pickPrenom']." ".$pick['pickNom']);
	$pickUserPrenom = ucwords($pick['pickPrenom']);
	$pickDate = ucwords(convertDate($pick['date'],"2adB"));
	$pickVoieLibelle = ucwords($pick['voieLibelle']);
	
	$title = "Votre collecte";
	$lead = "Bonjour {$pickUserPrenom},<br>votre collecte est programmée<br><strong style='color:#ffc107;'>{$pickDate}<br>entre {$pick['start']} et {$pick['end']}</strong>";
	
	$content =  "
	<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:19px;color:#ffffff;line-height:135%;margin-bottom:0;'>
		Vous vous engagez à être présent à votre domicile durant cette période.<br>
		<small style='font-size:75%'>Vous pouvez modifier ou annuler cette collecte depuis votre compte.</small>
		<br><br>
		<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Mon compte</strong></a>
	</div>";


	// BUNDLE TYPE
	if($pickType=="bundle"){
		
		$pickBundleUser = ucwords($pick['prenom']." ".$pick['nom']);
		$pickBundleUserPrenom = ucwords($pick['prenom']);
		$pickBundleDate = ucwords(convertDate($pick['date'],"2adB"));
		
		$emailBundle = $pick['pickEmail'];
		$titleBundle = "Collecte groupée";
		$leadBundle = "{$pickBundleUser} s'est groupé(e) à votre collecte.";
		$contentBundle = "
		<h2 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:17px;font-weight:bold;margin-bottom:5px; text-transform:uppercase; text-align:left;'><strong>Détail de la collecte</strong></h2>
		<div style='text-align:left; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:10px;'>
			Date : <strong>{$pickBundleDate}</strong> entre <strong>{$pick['start']} et {$pick['end']}</strong><br>Adresse : <strong>{$pickUser}, {$pick['voieNumero']} {$pick['voieType']} {$pickVoieLibelle}</strong><br> Nb. de sac(s) : <strong>{$pick['sacs']}</strong>
		</div>
		<div style='text-align:left; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:15px;'>
			<small>{$pickBundleUserPrenom} s'engage à vous remettre <strong>{$pick['sacs']} sac(s)</strong> avant <strong>{$pickBundleDate}</strong>.<br>Vous vous engagez à être présent le jour de la collecte.</small>
		</div>";
		
		sendMail($emailBundle, $titleBundle, $leadBundle, $contentBundle);			
	}
	
	// MAIL PICK
	sendMail($email, $title, $lead, $content);

}

/* pickBundleMail*/
function pickBundlesMail($action, $pickID){
	
	global $connexion;

	// BUNDLES REQUEST
	$bundles_rq = "
	SELECT users.email FROM bundles
	INNER JOIN users ON users.id = bundles.userID
	WHERE bundles.pickID={$pickID}";
	$bundles_rs = mysqli_query($connexion, $bundles_rq) or die();	
	if(mysqli_num_rows($bundles_rs)){
		
		$pick_rq="
		SELECT users.nom, users.prenom, slots.start, slots.end, cal.date, adresses.voieNumero, voies.voieType, voies.voieLibelle FROM picks
		INNER JOIN users ON users.id=picks.userID
		INNER JOIN adresses ON adresses.id = picks.adresseID
		INNER JOIN voies ON voies.id = adresses.voieID
		INNER JOIN slots ON slots.id = picks.slotID
		INNER JOIN cal ON cal.id = picks.calID
		WHERE picks.id={$pickID}";
		$pick_rs = mysqli_query($connexion, $pick_rq) or die();
		$pick = mysqli_fetch_assoc($pick_rs);

		$pickUser = ucwords($pick['prenom']." ".$pick['nom']);
		$pickUserPrenom = ucwords($pick['prenom']);
		$pickDate = ucwords(convertDate($pick['date'],"2adB"));
		$pickVoieLibelle = ucwords($pick['voieLibelle']);
		
		if($action=="update"){
			
			$title = "Collecte groupée modifiée";
			$lead = "{$pickUser} a modifié l'heure de la collecte à laquelle vous étiez groupé(e).</strong>";
			$content .=  "
			<h2 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:17px;font-weight:bold;margin-bottom:5px; text-transform:uppercase; text-align:left;'><strong>Détail de la collecte</strong></h2>
			<div style='text-align:left; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:5px;'>
				Date : <strong>{$pickDate}</strong> entre <strong>{$pick['start']} et {$pick['end']}</strong><br>Adresse : <strong>{$pickUser}, {$pick['voieNumero']} {$pick['voieType']} {$pickVoieLibelle}</strong>
			</div>";
			
		}

		if($action=="delete"){
			
			$title = "Collecte groupée annulée";
			$lead = "{$pickUser} a annulé la collecte à laquelle vous étiez groupé(e).";
			$content = "";
			
		}
		
		// MAIL BUNDLES
		while($bundles = mysqli_fetch_assoc($bundles_rs)){
			sendMail($bundles['email'], $title, $lead, $content);
		}
		
	}				

}

/* bundleMail*/
function bundleMail($action, $pickID){
	
	global $connexion;
	
	$pick_rq = "
	SELECT bundles.sacs, slots.start, slots.end, cal.date, users.nom, users.prenom, users.email, pickUser.email AS pickEmail, pickUser.nom AS pickNom, pickUser.prenom AS pickPrenom, adresses.voieNumero, voies.voieType, voies.voieLibelle
	FROM bundles
	INNER JOIN picks ON picks.id = bundles.pickID
	INNER JOIN adresses ON adresses.id = picks.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	INNER JOIN slots ON slots.id = picks.slotID
	INNER JOIN cal ON cal.id = picks.calID
	INNER JOIN users ON users.id=bundles.userID
	INNER JOIN users AS pickUser ON pickUser.id = picks.userID
	WHERE bundles.id = {$pickID}";
	$pick_rs = mysqli_query($connexion, $pick_rq) or die();
	$pick = mysqli_fetch_assoc($pick_rs);
	
	$email = $pick['pickEmail'];
	$pickUser = ucwords($pick['prenom']." ".$pick['nom']);
	$pickUserPrenom = ucwords($pick['prenom']);
	$pickDate = ucwords(convertDate($pick['date'],"2adB"));
	$pickVoieLibelle = ucwords($pick['voieLibelle']);
	$userAdresse = ucwords($pick['pickPrenom']." ".$pick['pickNom']);
	
	if($action=="update"){
		$title = "Collecte modifiée";
		$lead = "{$pickUser} a modifié le nombre de sacs qu'il souhaite se faire collecter.";
		$content = "
		<h2 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:17px;font-weight:bold;margin-bottom:5px; text-transform:uppercase; text-align:left;'><strong>Détail de la collecte</strong></h2>
		<div style='text-align:left; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:5px;'>
			Date : <strong>{$pickDate}</strong> entre <strong>{$pick['start']} et {$pick['end']}</strong><br>Adresse  : <strong>{$userAdresse}, {$pick['voieNumero']} {$pick['voieType']} {$pickVoieLibelle}</strong><br> Nb. de sac(s) : <strong>{$pick['sacs']}</strong>
		</div>
		<div style='text-align:left; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:15px;'>
			<small>Le nombre de sac(s) indiqué correspond à celui qui devra vous être remis par {$pickUserPrenom} avant la collecte.</small>
		</div>";
	}
	
	if($action=="delete"){
		$title = "Collecte annulée";
		$lead = "<strong>{$pickUser} a annulé sa collecte groupée à la votre.</strong>";
		$content = "";
	}

	sendMail($email, $title, $lead, $content);

}

/* assoEmail */
function assoMail($action, $assoID, $sponsorship){
	
	global $connexion;
	
	if($action!="sponsor"){
		
		$title = "Demande d'ami(e)";
		$body = file_get_contents('../../includes/mailing/email-sub.php');
		$html = "";
		$unsubToken = "";
	
		if($action=="create"){	
		
			$userAsso_rq = "
			SELECT users.nom, users.prenom, partners.email
			FROM assos 
			INNER JOIN users ON users.id=assos.userID
			INNER JOIN users AS partners ON partners.id=assos.partnerID
			WHERE assos.id={$assoID}";
			
		}
		
		if($action=="accept"){	
		
			$userAsso_rq = "
			SELECT partners.id AS userID, users.nom, users.prenom, partners.email, partners.prenom AS partnerPrenom
			FROM assos 
			INNER JOIN users ON users.id=assos.partnerID
			INNER JOIN users AS partners ON partners.id=assos.userID
			WHERE assos.id={$assoID}";
			
		}
		
	}
	else{
		
		$body = file_get_contents('../../includes/mailing/email-sponsor.php');
		$html = "parrainage";
		
		$userAsso_rq = "
		SELECT users.nom, users.prenom, sponsors.email
		FROM sponsors 
		INNER JOIN users ON users.id=sponsors.userID
		WHERE sponsors.id={$assoID}";

	}	
	
	$userAsso_rs = mysqli_query($connexion, $userAsso_rq) or die();
	$userAsso = mysqli_fetch_assoc($userAsso_rs);
	
	$email = $userAsso['email'];
	$userFullName = ucwords("{$userAsso['prenom']} {$userAsso['nom']}");
	$userPrenom = ucwords($userAsso['prenom']);
	
	switch($action){
		
		case 'create':	
		
			$lead = "{$userFullName} vous a envoyé une demande d'ami(e).";
			$content = "<div style='text-align:center;font-weight:normal;font-family:Helvetica,Arial,sans-serif;font-size:15px;color:#ffffff;line-height:135%;'>Vous pouvez accepter cette demande en vous rendant dans la rubrique <strong>Mes amis > Demandes reçues</strong> de votre compte.<br>Il vous sera ensuite possible de grouper vos collectes.</div>";
			
		break;
		
		case 'accept':
		
			if($sponsorship){
				
				$partnerPrenom = ucwords($userAsso['partnerPrenom']);
				
				$title = "Parrainage validé";
				$lead = "Merci {$partnerPrenom}, grâce à vous {$userFullName} s'est abonné au service !";
				
				$sponsorBonus_rq = "
				SELECT sponsors.id FROM sponsors
				WHERE sponsors.userID={$userAsso['userID']} 
				AND EXISTS ( SELECT id FROM users WHERE users.email=sponsors.email AND users.valid=1)";
				$sponsorBonus_rs = mysqli_query($connexion, $sponsorBonus_rq) or die(mysqli_error($connexion));
				$sponsorBonus_nb = mysqli_num_rows($sponsorBonus_rs);
				
				$sponsorGoal = 3-($sponsorBonus_nb%3);

				if($sponsorGoal==3){
					$sponsorContent = "Félicitations, <strong>vous avez gagné 1 crédit</strong>";
				}

				if($sponsorGoal==2){
					$sponsorContent = "<strong>Encore 2 parrainages</strong> avant de gagner 1 crédit !";
				}
				
				if($sponsorGoal==1){
					$sponsorContent = "<strong>Plus qu'un parrainage</strong> avant de gagner 1 crédit !";
				}
				
				$content = "
				<div style='text-align:center; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:17px;'>
					{$sponsorContent}
				</div>";	

			}
			else{
				
				$title.=" acceptée";
				$lead = "{$userFullName} a accepté votre demande.<br>Vous pouvez grouper vos collectes.";
				$content = "<div style='text-align:center;font-weight:normal;font-family:Helvetica,Arial,sans-serif;font-size:15px;color:#ffffff;line-height:135%;'>Cochez l'option <strong>Permettre la collecte groupée</strong> lorsque vous programmez une collecte pour que {$userPrenom} puisse s'y joindre.<br><br>Si {$userPrenom} autorise la collecte groupée, le détail de celle-ci apparaîtra dans la rubrique <strong>Collectes groupées</strong> de vote compte.</div>";
			}

		break;
		
		case 'sponsor':
		
			$unsubToken =  random_str(64);
			$mlistUpdate_rq = "UPDATE msurvio SET unsubToken='{$unsubToken}' WHERE email='{$email}'";
			$mlistUpdate_rs = mysqli_query($connexion, $mlistUpdate_rq) or die();
		
			$title = "{$userFullName} vous a parrainné !";
			$lead = "Pour vous simplifier le geste vert, nous proposons aux habitants de Bordeaux un <em>Service d’Aide au Recyclage du Verre à domicile</em>, sous la forme d’une prestation de service par abonnement.";
			$content = "
			<h2 style='color:#ffc107; font-weight:bold; font-size:25px;line-height:115%;font-family:Helvetica,Arial,sans-serif; margin-bottom:5px;'>
				<strong><em>5€</em> D'ADHÉSION ANNUELLE</strong>
			</h2>
			<div style='color:#FFFFFF;font-family:Helvetica,Arial,sans-serif;font-size:17px;line-height:115%; font-weight:bold; margin:0 0 15px;'>
				<strong style='color:#FFF;'>Entre <em>2€50</em> et <em>3€50</em> par sac collecté</strong><br>
				<strong style='color:#FFF;'>1 sac collecté = <em>5kg de verre recyclé</em></strong>
			</div>";
			
		break;		
	}
	
	sendMail($email, $title, $lead, $content, $body, $unsubToken, $html);

}

/* collectMail */
function collectMail($collectID, $pickType){
	
	global $connexion;
	
	if($pickType=='pick'){
		$collect_rq = "
		SELECT users.email, users.prenom, collects.sacs, collects.hour, cal.date, pickers.prenom AS pickerName
		FROM collects
		INNER JOIN picks ON picks.collectID = collects.id
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = picks.userID
		LEFT JOIN pickers ON pickers.id = collects.pickerID
		WHERE collects.id = {$collectID}";
	}
	
	if($pickType=='bundle'){
		$collect_rq = "
		SELECT users.email, users.prenom, collects.sacs, collects.hour, cal.date, pickers.prenom AS pickerName
		FROM collects
		INNER JOIN bundles ON bundles.collectID = collects.id
		INNER JOIN picks ON picks.id = bundles.pickID
		INNER JOIN cal ON cal.id = picks.calID
		INNER JOIN users ON users.id = bundles.userID
		LEFT JOIN pickers ON pickers.id = collects.pickerID
		WHERE collects.id = {$collectID}";
	}

	$collect_rs = mysqli_query($connexion, $collect_rq) or die();
	$collect = mysqli_fetch_assoc($collect_rs);
	
	$pickDate = ucwords(convertDate($collect['date'],"2adB"));
	$pickerName = ucwords($collect['pickerName']);
	$userName = ucwords($collect['prenom']);
	
	$title = "Collecte effectuée";
	$lead = "Bonjour {$userName},<br>votre collecte a bien été effectuée.";
	
	$content =  "
	<h2 style='color:#ffc107;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:17px;font-weight:bold;margin-bottom:10px; text-transform:uppercase; text-align:left;'><strong>Détail de la collecte</strong></h2>
	<div style='text-align:left; color:#FFF;line-height:115%;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:5px;'>
		Date : <strong>{$pickDate}</strong> à <strong>{$collect['hour']}</strong><br>Picker : <strong>{$pickerName}</strong><br> Sac(s) collecté(s) : <strong>{$collect['sacs']}</strong> 
	</div>";

	sendMail($collect['email'], $title, $lead, $content);
	
}


/* userRecallMail */
function userRecallCron($emailTest){
	
	global $connexion;
	
	$dateNew = date('Y-m-d', strtotime("-1 days"));
	$dateDay = date('Y-m-d');
	$dateLast = date('Y-m-d', strtotime("+1 days"));
	
	$user_rq = "
	SELECT users.id, users.email, cal.date AS calDate, cal.id AS calID, voies.secteur, 'new' AS calType FROM users
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	INNER JOIN cal ON cal.secteur = voies.secteur
	WHERE cal.date='{$dateNew}' AND users.societe='' AND users.unsub=0
	
	UNION
	
	SELECT users.id, users.email, cal.date AS calDate, cal.id AS calID, voies.secteur, 'day' AS calType FROM users
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	INNER JOIN cal ON cal.secteur = voies.secteur
	WHERE cal.date='{$dateDay}' AND users.societe='' AND users.unsub=0 
	
	UNION
	
	SELECT users.id, users.email, cal.date AS calDate, cal.id AS calID, voies.secteur, 'last' AS calType FROM users
	INNER JOIN adresses ON adresses.id = users.adresseID
	INNER JOIN voies ON voies.id = adresses.voieID
	INNER JOIN cal ON cal.secteur = voies.secteur
	WHERE cal.date='{$dateLast}' AND users.societe='' AND users.unsub=0";
	
	$user_rs = mysqli_query($connexion, $user_rq) or die();
	while($user = mysqli_fetch_assoc($user_rs)){
		
		$sendMail = 1;
		$content = "";
		
		$email = $user['email'];
		$userName = ucfirst(userName($user['id'],1));
		$userPick = userPick($user["id"]);
		
		if($userPick["type"]=="cal"){		
			$calDate = calDate($userPick["id"]);
		}else{		
			$pickDate = pickDate($userPick["id"], $userPick["type"]);
		}
		
		
		if($user["calType"]=="day"){

			if($userPick["type"]!="cal"){
				
				$title = "Votre collecte";
				$lead_cell = "votre collecte est programmée<br><strong style='color:#ffc107;'>ajourd'hui entre {$pickDate["start"]} et {$pickDate["end"]}</strong>";
				$content = "
				<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:19px;color:#ffffff;line-height:135%;margin-bottom:0;'>
					Connectez-vous à votre compte pour pouvoir annuler votre collecte.<br>
					<small style='font-size:75%'>Si vous annulez cette collecte, un crédit sera débité de votre compte.</small>
					<br><br>
					<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Mon compte</strong></a>
				</div>";
				
			}else{
				$sendMail = 0;
			}
		}
		
		if($user["calType"]=="new"){
			
			if($userPick["type"]=="cal"){
				
				$date = ucwords(convertDate($calDate,"2AdB"));
				
				if(!userActive($user['id'])){
					
					$title = "Programmez votre première collecte";
					
					$lead_cell = "<strong style='font-size:130%;'>testez le service maintenanrt,</strong><br>nous collectons votre premier sac <strong style='font-size:130%;'>gratuitement !</strong>";
					
					$content = "
					<div style='text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#ffffff;line-height:135%;margin-bottom:20px;'>
						<img src='https://assets.pic-verre.fr/img/sac-pv-sm-prix.png' alt='Le Sac Pic'Verre' width='100' style='float:left; margin-right:15px;'>
						<strong style='color:#ffc107;'>1.</strong> Pour programmer votre première collecte et <strong style='font-size:120%;'>seulement lors de votre première collecte</strong>, vous allez devoir faire l'acquisition d'un sac Pic'Verre.
					</div>
					
					<div style='text-align:right;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#ffffff;line-height:135%;margin-bottom:20px;'>
						<img src='https://assets.pic-verre.fr/img/vuf-1.png' alt='Le vélo Pic'Verre' width='80' style='float:right; margin-left:15px;'>
						<strong style='color:#ffc107;'>2.</strong> Le jour de la collecte un picker se présentera à votre domicile et remplira avec vous le(s) premier(s) sac(s) à collecter.
					</div>
					
					<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#ffffff;line-height:135%margin-bottom:20px;'>
						<strong style='color:#ffc107;'>3.</strong> A la fin de la collecte le picker vous remettra un nouveau sac dont vous serez titulaire et que vous pourrez utiliser pour votre prochaine collecte.<br><br>
					</div>
					
					<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:22px;color:#ffffff;line-height:135%;margin-bottom:20px; font-weight:bold;'>
						<small>Prochaine collecte</small><br><strong style='color:#ffc107; font-size:130%;'>{$date}</strong>
					</div>

					<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Programmer ma première collecte</strong></a>";

				}else{
					
					$title = "Programmez votre collecte";
					$lead_cell = "programmez votre prochaine collecte !";
					
					$content = "
					<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:22px;color:#ffffff;line-height:135%;margin-bottom:20px; font-weight:bold;'>
						<small>Prochaine collecte</small><br><strong style='color:#ffc107; font-size:130%;'>{$date}</strong>
					</div>

					<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Programmer ma collecte</strong></a>";

				}
			
			}else{
				$sendMail = 0;
			}
		}

		if($user["calType"]=="last"){
			
			if($userPick["type"]=="cal"){
				
				$date = ucwords(convertDate($calDate,"2AdB"));
							
				if(!userActive($user['id'])){
					
					$title = "Programmez votre première collecte";				
					$lead_cell = "<strong style='font-size:130%;'>faites vous collecter demain,</strong><br>nous collectons votre premier sac <strong style='font-size:120%;'>gratuitement !</strong>";
					
					$content = "
					<div style='text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#ffffff;line-height:135%;margin-bottom:20px;'>
						<img src='https://assets.pic-verre.fr/img/sac-pv-sm-prix.png' alt='Le Sac Pic'Verre' width='100' style='float:left; margin-right:15px;'>
						<strong style='color:#ffc107;'>1.</strong> Pour programmer votre première collecte et <strong style='font-size:120%;'>seulement lors de votre première collecte</strong>, vous allez devoir faire l'acquisition d'un sac Pic'Verre.
					</div>
					
					<div style='text-align:right;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#ffffff;line-height:135%;margin-bottom:20px;'>
						<img src='https://assets.pic-verre.fr/img/vuf-1.png' alt='Le vélo Pic'Verre' width='80' style='float:right; margin-left:15px;'>
						<strong style='color:#ffc107;'>2.</strong> Le jour de la collecte un picker se présentera à votre domicile et remplira avec vous le(s) premier(s) sac(s) à collecter.
					</div>
					
					<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#ffffff;line-height:135%margin-bottom:20px;'>
						<strong style='color:#ffc107;'>3.</strong> A la fin de la collecte le picker vous remettra un nouveau sac dont vous serez titulaire et que vous pourrez utiliser pour votre prochaine collecte.<br><br>
					</div>
					
					<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:22px;color:#ffffff;line-height:135%;margin-bottom:20px; font-weight:bold;'>
						<small>Prochaine collecte</small><br><strong style='color:#ffc107; font-size:130%;'>{$date}</strong>
					</div>

					<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Programmer ma première collecte</strong></a>";

					
				}else{
					
					$title = "Programmez votre collecte";
					$lead_cell = "faites vous collecter demain !";
					
					$content = "
					<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:22px;color:#ffffff;line-height:135%;margin-bottom:20px; font-weight:bold;'>
						<strong style='color:#ffc107; font-size:130%;'>{$date}</strong>
					</div>
					<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Programmer ma collecte</strong></a>";

					
				}
				
			}else{
				
				$title = "Votre collecte";
				$lead_cell = "votre collecte est programmée<br><strong style='color:#ffc107;'>demain entre {$pickDate["start"]} et {$pickDate["end"]}</strong>";
				$content = "
				<div style='text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:19px;color:#ffffff;line-height:135%;margin-bottom:0;'>
					Connectez-vous à votre compte pour pouvoir modifier ou annuler votre collecte.<br>
					<small style='font-size:75%'>Si vous annulez cette collecte, un crédit sera débité de votre compte.</small>
					<br><br>
					<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:20px;line-height:115%;padding:15px;background:#ffc107; display:block; font-style:italic;' href='https://mon-compte.pic-verre.fr/'><strong>Mon compte</strong></a>
				</div>";

			}		
		}
		
		$lead = "Bonjour {$userName},<br> {$lead_cell}";
		
		if(!userActive($user['id'])){
			
			$unsubToken =  random_str(24);
			$unsubUpdate_rq = "UPDATE users SET unsubToken='{$unsubToken}' WHERE id={$user['id']}";
			$unsubUpdate_rs = mysqli_query($connexion, $unsubUpdate_rq) or die();
			
		}
		
		if($sendMail){
			
		//	echo $email."<br>".$title."<br>".$lead."<br>".$date."<br><br>";
		//	sendMail($email, $title, $lead, $content, "",$unsubToken); 
		
		}	
	}	
}

?>