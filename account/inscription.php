<?php

	require_once ("../includes/account.init.php");
	require_once ("../includes/account.alerts.php");
	$action = (!empty($_SESSION['action'])) ? $_SESSION['action'] : '';
	$_SESSION['action'] = "";
	
	$code = (!empty($_REQUEST['s'])) ? $_REQUEST['s'] : '';
	if(empty($code)){
		$codeLabel = "Si vous avez acheté un sac chez l'un de nos revendeurs saisissez le code promo situé sur son rabas pour bénéficier d'une collecte offerte.";
	}else{
		$codeLabel = "Merci d'avoir acheté un sac chez l'un de nos revendeurs, finalisez votre inscription pour benéficier d'une collecte gratuite.";
	}
	
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="Pic'Verre">
		<title>Mon compte Pic'Verre - Inscription</title>
		<?php require_once("../includes/account.head.php");?>
		<style>
			html { position: relative; height:auto;}	
			@media (min-width: 768px) {
				.container {  margin-top:60px; margin-bottom:60px; max-width: 40em;}
			}
		</style>
	</head>
	<body class="abonnement">
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
		<div class="container mx-auto">
			<div class="row no-gutters">
			
				<div class="col-6 offset-3 offset-sm-0 col-sm-4 pl-4 pr-4">
					<img src="https://assets.pic-verre.fr/img/logo-pv-txt.svg" class="img-fluid"  alt="PIC'VERRE">
				</div>	
				
				<div class="col-12 col-sm-8">
				
					<h1 class="mb-1">Créez votre compte</h1>
					
					<p class="lead mb-3"><strong>Une fois votre inscription validée vous allez recevoir un e-mail vous permettant d'initialiser votre mot de passe et d'accéder à votre compte à partir duquel vous pourrez programmer vos collectes, commander un sac, créditer votre compte…</strong></p>
					
					<div class="row">
						<div class="col-12">
							<form action="edit/user.edit.php" method="post" class="p-0 needs-validation" id="userForm" novalidate>
						
								<h2>Contact</h2>
								<div class="form-row">
									<div class="col-6 form-group">
										<label for="prenom">Prénom</label>
										<input type="text" name="prenom" id="prenom" class="form-control" value="" required/>
									</div>
									<div class="col-6 form-group">
										<label for="nom">Nom</label>
										<input type="text" name="nom" id="nom" class="form-control" value="" required/>
									</div>						
								</div>
								<div class="form-row">
									<div class="col-sm-8 form-group">
										<label for="email">E-mail</label>
										<input type="email" name="email" id="email" class="form-control" value="" required />
									</div>
									<div class="col-sm-4 form-group">
										<label for="tel">Tél. <small>(facultatif)</small></label>
										<input type="text" name="tel" id="tel" class="form-control" value=""/>
									</div>
								</div>
								<h2 class="mt-3">Adresse de collecte</h2>
								<div class="form-row">
									<div class="form-group col-3">
										<label for="voieNumero">N°</label>
										<input type="text" name="voieNumero" id="voieNumero" class="form-control" value="" required/>
									</div>
									<div class="form-group col-9">
										<label for="voieSelect">Adresse</label>
										<input type="text" name="voieSelect" id="voieSelect" class="form-control" value="" required/>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-3">
										<label>Code Postal</label>
										<input type="text" class="form-control" value="33000" disabled/>
									</div>
									<div class="form-group col-9">
										<label>Ville</label>
										<input type="text" class="form-control" value="Bordeaux" disabled/>
									</div>
								</div>
								<p id="pickDate" class="text-center font-weight-bold p-2 rounded bg-info d-none"></p>
								<div class="form-row">
									<div class="col-md-12 form-group">
										<label>Complément d'adresse <small>(facultatif)</small></label>
										<input type="text" name="voieCpl" id="voieCpl" class="form-control"/>
									</div>
								</div>
								<h2 class="mt-3">Code promo</h2>
								<p class=""><?php echo $codeLabel; ?></p>
								<div class="form-row">
									<div class="col-md-12 form-group">
										<input type="text" name="code" id="code" value="<?php echo $code; ?>" class="form-control text-center"/>
									</div>
								</div>
								
								
								<div class="row mt-3">
									<div class="col-sm-12 mb-3">
										<input class="btn btn-secondary btn-lg btn-block" type="submit" value="Valider">
									</div>
								</div>
							</form>
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
		<!-- SCRIPTS -->
		<?php include("../includes/account.scripts.php"); ?>
		
		<script>

			$('#alertModal').modal('show');
			
			/* RESET VOIE */
			$('#voieSelect').focus(function() {
				$('#voieSelect').val('');
				$('#pickDate').empty();
				$('#pickDate').addClass('d-none');
			});
			
			$(function() {			
				var voies = [<?php echo selectVoies();?>];
				$( "#voieSelect" ).autocomplete({
				  minLength: 5,
				  source: voies,
				  focus: function( event, ui ) {
					$('#voieSelect').val( ui.item.label );
					$('#pickDate').addClass('d-none');
					return false;
				  },
				  select: function( event, ui ) {
					$('#voieSelect').val( ui.item.label );
					$('#pickDate').html("Prochaine collecte : <strong class='text-white'>"+ui.item.date+"</strong>");
					$('#pickDate').removeClass('d-none');
					return false;
				  }
				});			
			});
		</script>

	</body>
</html>