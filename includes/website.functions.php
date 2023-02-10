<?php

$servicePage = array("id"=>"service", "url"=>"fonctionnement","title"=>"Fonctionnement");
$sacPage = array("id"=>"sac", "url"=>"sac","title"=>"Notre sac");
$pricingPage = array("id"=>"pricing", "url"=>"tarifs","title"=>"Tarifs");
$prosPage = array("id"=>"pros", "url"=>"pros","title"=>"Pros");
$accountPage = array("id"=>"account", "url"=>"https://mon-compte.pic-verre.fr","title"=>"Mon compte");
$progPage = array("id"=>"programmer", "url"=>"programmer","title"=>"Programmer ma collecte");
$companyPage = array("id"=>"company", "url"=>"qui-sommes-nous","title"=>"Qui-sommes-nous");
$partnersPage = array("id"=>"partners", "url"=>"partenaires","title"=>"Nos partenaires");
$legalPage = array("id"=>"legal", "url"=>"mentions-legales","title"=>"Mentions légales");
$cgvPage = array("id"=>"cgv", "url"=>"cgv","title"=>"Conditions générales de vente");

$navbarPages = array($servicePage,$sacPage,$pricingPage, $prosPage, $accountPage);

$mainPages = array($companyPage,$partnersPage,$progPage);
$footerPages = array($legalPage,$cgvPage);
$sitePages = array_merge($navbarPages,$mainPages,$footerPages);	

function pageHead($pageID){
	
	global $sitePages;
	$countPages = count($sitePages);
	
	$title = "Service de collecte du verre à domicile";
	
	for ($i = 0; $i <  $countPages; $i++) {
		if($sitePages[$i]['id']==$pageID){
			$title = $sitePages[$i]['title'];
		}
	}

	$pageHead = "
	<head>
	
		<meta charset='utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
		<meta name='description' content=''>
		<meta name='author' content='Pic Verre'>
		
		<!-- TITLE -->
		<title>Pic'Verre - {$title}</title>		
		
		<!-- STYLES -->
		<link href='https://assets.pic-verre.fr/jquery-ui/jquery-ui.min.css' rel='stylesheet' />
		<link href='https://assets.pic-verre.fr/jquery-ui/jquery-ui.structure.min.css' rel='stylesheet' />
		<link href='https://assets.pic-verre.fr/jquery-ui/jquery-ui.theme.min.css' rel='stylesheet' />
		<link href='https://assets.pic-verre.fr/bootstrap/bootstrap.min.css' rel='stylesheet'>
		<link href='https://assets.pic-verre.fr/css/website.css' rel='stylesheet' />
		<link href='https://use.fontawesome.com/releases/v5.5.0/css/all.css' rel='stylesheet'  integrity='sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU' crossorigin='anonymous'>
		
		<!-- ICONS -->
		<link rel='icon' type='image/png' sizes='32x32' href='https://assets.pic-verre.fr/img/favicon-32x32.png'>
		<link rel='icon' type='image/png' sizes='16x16' href='https://assets.pic-verre.fr/img/favicon-16x16.png'>
		<link rel='mask-icon' href='https://assets.pic-verre.fr/img/safari-pinned-tab.svg' color='#5bbad5'>	
		
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src='https://www.googletagmanager.com/gtag/js?id=UA-47699862-3'></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  gtag('config', 'UA-47699862-3');
		</script>
		
	</head>";
	
	return $pageHead;
}

function navbarNav($pageID){
	
	global $navbarPages;
	$countPages = count($navbarPages);
	
	for ($i = 0; $i <  $countPages; $i++) {
		$active="";
		if($navbarPages[$i]['id']==$pageID){
			$active = "active";
		}
		$navbarNavUl .= "<li class='nav-item'><a class='nav-link {$active}' href='{$navbarPages[$i]['url']}'>{$navbarPages[$i]['title']}</a></li>";
	}
	
	return $navbarNavUl;
}

function pageHeader($pageID){
	
	// $headerInfos = file_get_contents("includes/content/header-infos.php");
	$navbarNav = navbarNav($pageID);
	
	$pageHeader = "
	<header class='fixed-top'>
	
		<!-- HEADER INFOS -->
		{$headerInfos}	
		
		<!-- NAVBAR -->
		<nav class='navbar navbar-expand-lg' data-toggle='affix'>
			<div class='container'>		
				
				<!-- NAVBAR BRAND -->
				<button class='navbar-toggler btn-primary btn' type='button' data-toggle='collapse' data-target='#navbarToggler' aria-expanded='false' aria-label='Navigation'>
					<span class='navbar-toggler-icon'><i class='fas fa-bars'></i></span>
				</button>

				<h1 class='navbar-brand'>
					<img src='https://assets.pic-verre.fr/img/logo-pv.svg' alt='Pic Verre'/>
					<a href='https://www.pic-verre.fr'>PIC'VERRE<br><small>Service de collecte du verre à domicile</small></a>
				</h1>			
				
				<!-- NAVBAR NAV -->
				<div class='collapse navbar-collapse' id='navbarToggler'>
					<ul class='navbar-nav ml-auto'>
						{$navbarNav}
						<li class='nav-item'><a class='nav-link btn btn-primary font-italic' href='programmer'>Programmer ma collecte</a></li>
					</ul>
				</div>	
				
			</div>
		</nav> 		
	</header>";
	
	return $pageHeader;
	
}


function buildPage($pageID, $title){
	
	$pageHead = pageHead($pageID);
	$pageHeader = pageHeader($pageID);
	$mainContent = file_get_contents("includes/content/{$pageID}.mainContent.php");

	$contactModal = file_get_contents("includes/content/contact-modal.php");
	
	if($pageID!="programmer" && $pageID!="home"){
		$orderModal = file_get_contents("includes/content/order-modal.php");
	}

	if(!empty($title)){
	$pageTitle ="
	<div style='padding-top:70px; '>
		<h2 class='mb-0 py-2 text-center'>{$title}</h2>
	</div>";
	}
	
	$page = "
	<!doctype html>
	<html lang='fr'>
		
		<!-- HEAD -->
		{$pageHead}
		
		<!-- BODY -->
		<body class='{$pageID}'>
			
			<!-- HEADER -->
			{$pageHeader}
			
			<!-- TITLE -->
			{$pageTitle}
			
			<!-- MAIN -->
			<div class='main container'>
				{$mainContent}
			</div>
			
			<!-- ORDER MODAL -->
			{$orderModal}
			
			<!-- CONTACT MODAL -->
			{$contactModal}
			
			<!-- FOOTER -->
			<footer class='footer'>
				<div class='container'>
				
					<nav>
						<a href='qui-sommes-nous'>Qui-sommes-nous</a> &bull; <a href='partenaires'>Nos partenaires</a> &bull; <a href='#contactModal' data-toggle='modal' data-target='#contactModal'>Contactez-nous</a>  &bull; <span class='text-nowrap'>Suivez-nous sur <a href='https://www.facebook.com/picverre/' target='_blank'><i class='fa-facebook fab'></i></a> <a href='https://www.instagram.com/picverre/' target='_blank'><i class='fa-instagram fab'></i></a></span>
					</nav>
					
					<div>
						&copy; Pic'Verre 2019 &bull; <a href='mentions-legales'>Mentions légales</a> &bull; <a href='cgv'>CGV</a>
					</div>
					
				</div>
			</footer>

			<!-- SCRIPTS -->
			<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
			<script src='https://assets.pic-verre.fr/bootstrap/bootstrap.bundle.min.js'></script>
			<script src='https://assets.pic-verre.fr/jquery-ui/jquery-ui.min.js'></script>
			<script src='https://assets.pic-verre.fr/bootstrap/bootstrap.bundle.min.js'></script>
			<script src='https://assets.pic-verre.fr/js/website.js'></script>
			
		</body>
	</html>";
	
	return $page;
}



?>