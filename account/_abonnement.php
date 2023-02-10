<?php

	require_once ("../includes/account.init.php");
	require_once ("../includes/account.alerts.php");
	
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="L’entreprise PIC’VERRE propose à Bordeaux, un Service d’Aide au Recyclage du Verre qui s’adresse aux particuliers, en assurant une collecte régulière à domicile">
		<meta name="author" content="">
		<title>Pic'Verre - Abonnement</title>
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
				<a href="https://www.pic-verre.fr/"><i class="fas fa-arrow-circle-left"></i> Retour à l'accueil</a>
				<a href="edit/theme.php">Mode <?php echo themeLink($_SESSION['theme']); ?> <i class="fas fa-eye"></i></a>
			</div>
		</header>
		<!-- CONTENT -->	
		<div class="container mx-auto">
			<div class="row no-gutters">
				<div class="col-6 offset-3 offset-sm-0 col-sm-4 pr-3">
					<img src="https://assets.pic-verre.fr/img/logo-pv-txt.svg" class="img-fluid"  alt="PIC'VERRE">
				</div>		
				<div class="col-12 col-sm-8">
					<h1 class="mb-3">Abonnement</h1>
					<p class="lead mb-3">Sélectionnez le nombre de crédits que vous souhaitez ajouter à votre compte, puis renseignez les différentes informations demandées</p>
					<p class="lead mb-3">Une fois le paiement validé vous serez redirigé(e) vers la page de votre compte et vous pourrez programmer votre première collecte.</p>
				</div>		
			</div>
			<form action="edit/abo.edit.php" method="post" class="p-0 needs-validation" id="aboForm" novalidate>
				<div class="row">
					<div class="col-md-7">
						<div class="form-row">
							<!-- FORMULE -->
							<div class="col-9 form-group">
								<label class="h2" for="formuleID">Formule</label>
								<select name="formuleID" id="formuleID" class="form-control">
									<?php
										$formules_rq = "SELECT * FROM formules ORDER BY credits ASC";
										$formules_rs = mysqli_query($connexion, $formules_rq) or die(mysqli_error());
										while($formules = mysqli_fetch_assoc($formules_rs)){
											if($formules['credits']==0){
												$formuleLabel = "Moins de 6 crédits | {$formules['montant']}&euro;/crédit";
											}else{
												$formuleLabel = "Pack de {$formules['credits']} crédits à {$formules['montant']}&euro;";
											}
											echo "<option value='{$formules['id']}' data-price='{$formules['montant']}' data-credits='{$formules['credits']}' data-libelle='{$formules['libelle']}'> {$formuleLabel}</option>";
										}
									?>
								</select>
							</div>
							<!-- CREDITS -->							
							<div class="col-3 form-group">
								<label class="h2" for="credits">Crédits</label>
								<select name="credits" id="credits" class="form-control">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</div>
							
							<div class="col-12 form-group">
								<p class="lead text-center font-weight-bold p-2 rounded bg-info text-white">1 crédit <span class="badge badge-pill" style="background-color:#01bd70; color:#ffc107;"><i class="fas fa-equals"></i></span> 1 sac collecté</p>
								<p>Les crédits ne sont pas limités dans le temps : s'il vous reste des crédits, <strong>ils seront toujours disponibles lors de votre réadhésion</strong>.</p>
							</div>
						</div>
						<h2>Informations</h2>
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
							<div class="col-12 form-group">
								<label>Société <small>(facultatif)</small></label>
								<input type="text" name="societe" id="societe" class="form-control"/>
							</div>
						</div>
						<p>Renseignez ce champ <strong>si vous adhérez au service en tant que professionnel</strong>.</p>
						<hr>
						<div class="form-row">
							<div class="form-group col-3">
								<label for="voieNumero">N°</label>
								<input type="text" name="voieNumero" id="voieNumero" class="form-control" value="" required/>
							</div>
							<div class="form-group col-9">
								<label for="voieSelect">Adresse</label>
								<input type="text" name="voieSelect" id="voieSelect" class="form-control" value="" autocomplete="off" required/>
							</div>
						</div>
						<p id="pickDate" class="text-center font-weight-bold p-2 rounded bg-info d-none"></p>
						<div class="form-row">
							<div class="col-md-12 form-group">
								<label>Complément d'adresse <small>(facultatif)</small></label>
								<input type="text" name="voieCpl" id="voieCpl" class="form-control"/>
							</div>
						</div>
						<hr>
						<div class="form-row">
							<div class="col-sm-8 form-group">
								<label for="email">E-mail</label>
								<input type="email" name="email" id="email" class="form-control" value="" required />
							</div>
							<div class="col-sm-4 form-group mb-3">
								<label for="tel">Tél.</label>
								<input type="text" name="tel" id="tel" class="form-control" value="" required/>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<!-- RESUME -->
						<h2 class="mb-2">Résumé</h2>
						<ul class="list-group mb-4">
							<li class="list-group-item">
								<div class="h6 text-uppercase">Adhésion <span class="badge badge-pill float-right" style="background:rgba(0,0,0,.2)">5&euro;</span></div>
								<div class="text-muted mb-2"><strong>1 collecte par mois</strong><br>Soit 12 collectes sur l'année</div>
								<div class="text-muted mb-2"><strong>1 sac Pic'Verre</strong><br/>Remis lors de la 1<sup>ère</sup> collecte</div>
							</li>
							<li class="list-group-item">
								<div class="h6 text-uppercase"><span id="creditsNb">1</span> crédits <span id="creditsTotal" class="badge badge-pill float-right" style="background:rgba(0,0,0,.2)">3.5&euro;</span></div>
								<div id="creditsLibelle" class="text-muted mb-2"><strong>Moins de 6 crédits</strong><br>3,50 euros par sac collecté</div>
							</li>
							<li class="list-group-item bg-dark mb-0">
								<div class="h6 text-uppercase mb-0">Total <span id="orderTotal" class="badge badge-pill badge-warning float-right">8.5&euro;</span></div>
							</li>
						</ul>
							
						<p class="text-center font-weight-bold p-2 rounded bg-info text-white">Le paiement sécurisé est assuré par <a href='https://www.ocl.natixis.com/systempay/syshome/index/id/1' class='link-primary' target='blank' >SystemPay</a></p>

						<h2>Mot de passe</h2>
						<div class="form-row">
							<div class="col-sm-12 form-group">
								<p class="mb-0">L'accès à votre compte est sécurisé par un mot de passe. Il vous sera demandé avec votre e-mail pour vous connecter.</p>
							</div>
							<div class="col-sm-12 form-group">
							
								<label for="pwd" class="sr-only">Mot de passe</label>
								<input type="password" name="pwd" id="pwd" class="form-control" value="" required/>
							</div>
						</div>
						<!-- INFOS BANQUE -->
						<div class="row mt-3">
							<div class="col-sm-12 mb-3">
								<input class="btn btn-secondary btn-lg btn-block" type="submit" value="Valider">
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-sm-12">
								<p class="font-weight-bold">Vous allez être redirigé(e) vers une page à partir de laquelle vous pourrez finaliser votre paiement en toute sécurité.</p>
							</div>
						</div>
											
					</div>
				</div>
				<!-- CGV -->
				<div class="mt-2">
					<p class="lead text-center p-2 rounded bg-info text-white" style="font-size:.9rem;">En validant, vous acceptez les <a href="https://www.pic-verre.fr/cgv" target="_blank">conditions générales de vente</a></p>
				</div>	
			</form>
		</div>
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

			$('#credits').change(function() {
				$('#creditsNb').html($('#credits').val());
				$('#creditsTotal').html($('#credits').val()*3.5+'&euro;');
				$('#orderTotal').html(5+$('#credits').val()*3.5+'&euro;');
			});
			
			$('#formuleID').change(function() {
				$('#creditsLibelle').html($('#formuleID').find(':selected').attr('data-libelle'));
				if($('#formuleID').val()==1){
					$('#credits').prop('disabled',false);
					$('#credits').prop('required',true);
					$('#credits').val('1');
					$('#creditsNb').html('1');
					$('#creditsTotal').html('3.5&euro;');
					$('#orderTotal').html('8.5&euro;');
				}else{
					$('#credits').prop('required',false);
					$('#credits').prop('disabled',true);
					$('#credits').val('');
					$('#creditsNb').html($('#formuleID').find(':selected').attr('data-credits'));
					$('#creditsTotal').html($('#formuleID').find(':selected').attr('data-price')+"&euro;");					
					var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
					$('#orderTotal').html(price+5+"&euro;");
				}
			});
			
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