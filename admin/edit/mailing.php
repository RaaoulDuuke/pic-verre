<?php

	session_start();
	
	require_once ("../../includes/connect.php");
	require_once ("../../includes/common.functions.php");
	require_once ("../../includes/mails.functions.php");
	
	$title = "Testez le service";
	
	$lead = "Pour l’achat d’un sac,<br>votre première collecte est offerte !";
	
	$content = "
	<p>Pic’Verre a fait concevoir un sac cabas en matériau recyclé, réutilisable et fermable, d’un volume de 20L (environ 5kg), avec 9 compartiments de rangement lui conférant une tenue optimale pour le stockage et le transport des emballages en verre.</p>
	<p>Connectez vous à votre compte Pic'Verre pour commander votre sac et programmer votre première collecte gratuitement.</p>
	<a style='color:#FFFFFF;text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:115%;padding:15px;background:#ffc107; display:block;' href='https://mon-compte.pic-verre.fr'><strong>Mon compte Pic'Verre</strong></a>";
	$body="";
	
	// sMail("dev@pic-verre.fr", $title, $lead, $content);


?>