<?php
	require_once ("../includes/account.init.php"); 
	require_once ("../includes/account.alerts.php");
	$action = (!empty($_SESSION['action'])) ? $_SESSION['action'] : '';
	$_SESSION['action'] = "";
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="L’entreprise PIC’VERRE propose à Bordeaux, un Service d’Aide au Recyclage du Verre qui s’adresse aux particuliers, en assurant une collecte régulière à domicile">
		<meta name="author" content="Pic'Verre">
		<meta name="robots" content="noindex">
		<title>Mon Compte Pic'Verre</title>
		<?php require_once("../includes/account.head.php");?>
	</head>
	<body>
		<div id="wrapper">	
			<!-- NAVBAR  -->
			<?php require_once ("includes/account.navbar.php");?>
			<!-- CONTAINER  -->
			<div id="page-content-wrapper">
				<div class="container-fluid">
				
					<?php 
						echo alertSection($action);
						echo pickSection($userID);
					?>
					<div class="row main" id="mainContent">	
						<div class="col-md-6">					
							<?php  
								echo sacSection($userID);
							?>						
						</div>
						<div class="col-md-6">
							<div id="mainSidebar">
								<?php 
									if(!userPro($userID)){
										echo bundlesSection($userID);
										echo assosSection($userID);
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>	
			<!-- SIDEBAR -->
			<div id="sidebar-wrapper" class="bg-dark position-fixed">
				<div id="sidebar" class="sidebar sidebar-nav accordion position-absolute m-0 p-0">		
					<?php 
						echo creditsSection($userID);
						/*
						if(!userPro($userID)){
							echo bundlesSbSection($userID);
							echo assosSbSection($userID);
						}
						*/
						echo infosSidebar($userID);
						echo picksSidebar($userID);
						echo ordersSidebar($userID);
					?>
					<ul class="nav flex-column border-top border-light">
						<li class="nav-item"><a href="https://www.pic-verre.fr/app" class="btn" target="_blank"><i class="fas fa-mobile-alt"></i> Installer notre application</a></li>
						<li class="nav-item"><a href="edit/theme.php" class="btn"><i class="fas fa-eye"></i> Mode <?php echo themeLink($_SESSION['theme']); ?></a>
						<li class="nav-item"><a href="edit/log.php?action=out" class="btn"><i class="fas fa-sign-out-alt hvr-icon"></i> Déconnexion</a></li>
					</ul>				
				</div>
			</div>
			<!-- FOOTER -->
			<footer class="footer text-right bg-secondary fixed-bottom border-top border-white">
				<div class="container-fluid">
					<?php require_once("includes/footer.php");?>
				</div>
			</footer>
		</div>
		
		<!-- MODAL -->
		<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				</div>
			</div>
		</div>	
		
		<!-- MODAL CONTACT -->
		<?php require_once("includes/contact.modal.php");?>
		
		<!-- SCRIPTS -->
		<?php include("../includes/account.scripts.php"); ?>
		
	</body>	
</html>