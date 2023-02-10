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
		<div class="container mx-auto" style="max-width:60em;">
			<form action="" method="post" class="p-0 needs-validation" id="userForm" novalidate>
			<div class="row">
			
				<div class="col-6 ">
					<div class="row">
						<div class="col-12">
							
						

								<h4>Adresse </h4>
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
								
								<h4>Contact</h4>
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


							</form>
						</div>
					</div>					
				</div>	
				
				<div class="col-6">
				
					<h4>Ma collecte</h4>
				
					<p id="pickDate" class="font-weight-bold d-none"></p>
					
		<div class="form-row">
			<div class="col-md-9 form-group">
				<label class="text-uppercase" style="font-weight:900;">Horaire</label>
				<select name="slotID" id="slotID" class="form-control" required="" disabled="">
					<option value="1">Entre 16:00 et 17:00</option><option value="3">Entre 17:00 et 18:00</option><option value="5">Entre 18:00 et 19:00</option><option value="7">Entre 19:00 et 20:00</option><option value="9">Entre 20:00 et 21:00</option>
				</select>
			</div>
			<div class="col-md-3 form-group">
				<label class="text-uppercase" style="font-weight:900;">Sac(s)</label>
				<select name="sacs" id="sacs" class="form-control" required="" disabled="">
					<option value="1" selected="">1</option><option value="0">+</option>
				</select>
			</div>
		</div>
			
			<div id="credits-row" class="form-row d-none">
				<div class="col-12 form-group">
					<p class="mb-0 font-weight-bold" role="alert">Vous devez créditer votre compte pour pouvoir programmer la collecte de plus de 1 sac(s).</p>
				</div>
			
				<div class="col-md-9 form-group">
					<label class="text-uppercase" style="font-weight:900;">Créditer mon compte</label>
					
					<select id="formuleID" name="formuleID" class="form-control" disabled="">
						
				<option value="1" data-price="3.5" data-credits="0" data-libelle="3,50€ par sac collecté">Moins de 6 crédits</option>
				<option value="8" data-price="20" data-credits="6" data-libelle="3,33€  par sac collecté" selected="">Pack de 6 crédits</option>
				<option value="2" data-price="36" data-credits="12" data-libelle="3,00€  par sac collecté">Pack de 12 crédits</option>
				<option value="3" data-price="66" data-credits="24" data-libelle="2,75€  par sac collecté">Pack de 24 crédits</option>
				<option value="4" data-price="90" data-credits="36" data-libelle="2,50€  par sac collecté">Pack de 36 crédits</option>
					</select>
					
				</div>
				<div class="col-md-3 form-group">
					<label class="text-uppercase" style="font-weight:900;">Nb.</label>
					<select name="credits" id="credits" class="form-control" disabled="">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</div>
				
			</div>
	<input type="hidden" name="action" value="create">
	<input type="hidden" name="pickType" value="pick">					
					
					
				
					<ul class='list-group mb-3 id='list-order'>
						<li class='list-group-item' style="background:#9fcced;">
							<div class='h6'><span class='text-uppercase'>1 crédit = <strong class='text-white'>1 sac collecté</strong></span> <span class='badge badge-pill bg-secondary text-primary'>offert</span></div>
						</li>
						<li class='list-group-item pl-1' style="background:#9fcced;">
							<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:100px;'>
							<div class='h6'><span class='text-uppercase'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
							<span class='font-weight-bold text-white'>Pour accéder au service vous devez faire l'acquisition d'un sac qui vous sera remis lors de votre première collecte.<br><a href='https://www.pic-verre.fr/sac' target='_blank' >En savoir plus</a></span>
						</li>
						<li id="list-group-credits" class="list-group-item bg-info d-none">
						<div class="h6"><span class="text-uppercase"><span id="creditsNb">6</span> crédit(s)<br><strong id="creditsLibelle" class="text-white">soit 3.33€ par sac collecté</strong></span> <span id="creditsTotal" class="badge badge-pill bg-secondary text-primary">20€</span></div>
					</li>
						<li class='list-group-item bg-secondary'>
							<div class='h6 text-uppercase text-white'>Total <span id='orderTotal' class='badge badge-pill bg-primary float-right'>5&euro;</span></div>
						</li>
					</ul>
					
					<div class='form-footer form-row clearfix'>
						<div class='col-md-12 form-group mb-0'>
							<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><i class='fa fa-check hvr-icon'></i> Valider</button>
						</div>
						<div id='cgv' class='col-md-12 form-group text-center mt-2 {$formFooterClass}'>
							<p class='mb-0'><small>En validant ce formulaire, vous acceptez nos <a href='https://www.pic-verre.fr/cgv' target='_blank' class='text-nowrap link-primary'>conditions générales de vente</a></small></p>
						</div>
					</div>
				
					<p id='citelis'  class='text-center p-2 m-0 border-top border-light'><small></small></p>
					


				</div>		
			</div>
			</form>
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
			
			$('#sacs').on('change', function () {
				if($('#sacs').val()==0){
					$('#list-group-credits').removeClass('d-none');
					$('#credits-row').removeClass('d-none');
					$('#formuleID').prop('disabled', false);
					$('#credits').prop('disabled', true);
					$('#credits').val(credits);
					$('#orderTotal').html('25&euro;');
				}else{
					$('#list-group-credits').addClass('d-none');
					$('#credits-row').addClass('d-none');
					$('#orderTotal').html('5&euro;');
					$('#credits').prop('disabled', true);
					$('#formuleID').prop('disabled', true);
				}
			});
			
			$('#credits').change(function() {
				$('#creditsNb').html($('#credits').val());
				$('#creditsTotal').html($('#credits').val()*3.5+'&euro;');
				$('#orderTotal').html({$sacTarif}+$('#credits').val()*3.5+'&euro;');
			});

			$('#formuleID').on('change', function () {
				$('#creditsLibelle').html($('#formuleID').find(':selected').attr('data-libelle'));	
				if($('#formuleID').val()==1){
					
					$('#list-group-credits').removeClass('d-none');
					
					$('#credits').prop('disabled', false);
					$('#credits').val('1');
					$('#credits').prop('required',true);
					$('#creditsNb').html('1');
					$('#creditsTotal').html('3.5&euro;');						
					$('#orderTotal').html(3.5+{$sacTarif}+'&euro;');
					
				}else{
					
					if($('#formuleID').val()==''){
						$('#list-group-credits').addClass('d-none');
					}else{
						$('#list-group-credits').removeClass('d-none');
					}
					
					var credits = $('#formuleID').find(':selected').attr('data-credits');
					$('#credits').val(credits);
					$('#credits').prop('disabled', true);
					
					$('#credits').prop('required',false);
					$('#creditsNb').html(credits);						
					$('#creditsTotal').html($('#formuleID').find(':selected').attr('data-price')+'&euro;');
				
					var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
					$('#orderTotal').html(price+{$sacTarif}+'&euro;');
					
				}
			});
			
			/* RESET VOIE */
			$('#voieSelect').focus(function() {
				$('#voieSelect').val('');
				$('#pickDate').empty();
				$('#pickDate').addClass('d-none');
				$('#slotID').prop('disabled', true);
				$('#sacs').prop('disabled', true);
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
					$('#slotID').prop('disabled', false);
					$('#sacs').prop('disabled', false);
					return false;
				  }
				});			
			});
		</script>

	</body>
</html>