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
		<meta name="description" content="">
		<meta name="author" content="Pic'Verre">
		<title>Mon compte Pic'Verre - Connexion</title>
		<?php require_once("../includes/account.head.php");?>
	</head> 
	<body class="login">
		<!-- HEADER -->
		<header class="header container-fluid">
			<div class="d-flex justify-content-between">
				<a href="https://www.pic-verre.fr"><i class="fas fa-arrow-circle-left"></i> Retour à l'accueil</a>
				<div>
				<a href="https://www.pic-verre.fr/app" target="_blank" class="mr-2"><i class="fas fa-mobile-alt"></i> Application</a> <a href="edit/theme.php"><i class="fas fa-eye"></i> Mode <?php echo themeLink($_SESSION['theme']); ?></a>
				</div>
			</div>
		</header>
		
		<!-- CONTENT -->		
		<div class="container d-flex mx-auto flex-column">
			
			<div class="row my-auto no-gutters">	
				
				<div class="col-6 offset-3 offset-sm-0 col-sm-6 pr-3">
					<img id="logo" src="https://assets.pic-verre.fr/img/logo-pv-txt.svg" class="img-fluid" alt="PIC’VERRE">
				</div>				
				<div class="col-12 col-sm-6">		
					<h1 class="form-signin-heading mb-3">Connectez-vous à votre compte</h1>		
					<form action="edit/log.php" method="post" class="form-signin mb-3 needs-validation" novalidate>					
						<label for="email" class="sr-only">E-mail</label>
						<input type="email" id="email" name="email" class="form-control mb-2" placeholder="E-mail" required autofocus>
						<label for="pwd" class="sr-only">Mot de passe</label>
						<input type="password" id="pwd" name="pwd" class="form-control mb-3" placeholder="Mot de passe" required>
						<input type="hidden" value="in" name="action">
						<button class="btn btn-secondary btn-block mb-3" type="submit">Valider</button>
						<p class="text-right"><a href="#resetPwd" data-toggle="modal" data-target="#resetPwd" class="text-white"><i class="fas fa-key"></i> Mot de passe oublié</a></p>
					</form>
				</div>
				<!--
				<div class="col-sm-12 ">
					<div id="aboLink" class="row align-items-center px-3 pt-2 pb-3 m-0 mt-2">
						<div class="col-sm-7">
							<p class="text-center m-0 p-2 font-weight-bold">Vous n'avez pas encore créé votre compte Pic'Verre ?<br><em class="badge badge-pill badge-warning text-white font-weight-bolder" style='font-size:90%'>5€</em> d'adhésion annuelle</p>
						</div>
						<div class="col-sm-5 text-center">
							<a href="inscription.php" class="btn btn-secondary btn-block mb-0">Inscription</a>
						</div>
					</div>					
				</div>	
				-->
			</div>
		</div>
		
		
		<!-- MODAL RESET-->
		<div class="modal fade" id="resetPwd" tabindex="-1" role="dialog" aria-labelledby="">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<div class="row no-gutters">
							<div class="col-12">
								<h2> Réinitialisation du mot de passe</h2>
								<p><strong>Entrez l'adresse e-mail correspondant à votre compte.</strong><br>Vous allez recevoir un e-mail contenant un lien permettant de réinitialiser votre mot de passe.</p>
								<form action="edit/log.php" method="post" class="needs-validation" novalidate>
									<div class="form-row">
										<div class="form-group col-md-9">
								
											<label for="email" class="sr-only">email</label>
											<input type="email" id="email" name="email" class="form-control mb-2" placeholder="Votre e-mail" required autofocus>
										</div>
										<div class="form-group col-md-3">
											<button type="submit" class="btn btn-secondary btn-block mb-3">Valider</button>
											<input type="hidden" value="askreset" name="action">
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php if(!empty($action)){ ?>
		<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="">
			<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
				<div class="modal-content">
					<?php echo alertSection($action,1); ?>
				</div>
			</div>
		</div>	
		<?php } ?>
		
		<!-- FOOTER -->
		<footer class="footer">
			<div class="container-fluid text-right">
				<?php require_once("includes/footer.php");?>
			</div>
		</footer>
		
		<!-- MODAL CONTACT -->
		<?php require_once("includes/contact.modal.php");?>
		
		<?php include("../includes/account.scripts.php"); ?>
		
		<script>
			$('#alertModal').modal('show');
		</script>
		
		
	</body>
</html>