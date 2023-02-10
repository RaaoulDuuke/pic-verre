<form id="orderForm" action="https://mon-compte.pic-verre.fr/edit/pick.www.edit.php" method="post" class="needs-validation mt-4" novalidate >

	<div class="row">
		<div class="col-md-6">

			<h3 class="mb-1">1. Mon adresse</h4>
			<p>Saisissez votre adresse pour connaitre la date de votre collecte.</h3>
			<div class="form-row">
				<div class="col-6 form-group">
					<label>Nom</label>
					<input id="nom" class="form-control" type="text" name="nom" required>
				</div>
				<div class="col-6 form-group">
					<label>Prenom</label>
					<input id="prenom" class="form-control" type="text" name="prenom" required>
				</div>
			</div>
			<div class="form-row">
				<div class="col-3 form-group">
					<label>Numero</label>
					<input id="voieNumero" class="form-control" type="text" name="voieNumero" required>
				</div>
				<div class="col-9 form-group">
					<label>Voie</label>
					<input id="voieSelect" class="form-control" type="text" name="voieSelect" required>
				</div>
			</div>
			
			<p id="voie-alert" class="d-none" style="font-size:80%;">Vous devez sélectionner une voie dans la liste proposée pour connaitre votre date de collecte.</p>
			
			<div class="form-row">
				<!--
				<div class="form-group col-3">
					<label>Code Postal</label>
					<input type="text" class="form-control" value="33000" disabled/>
				</div>
				-->
				<div class="form-group col-12">
					<label>Ville</label>
					<input type="text" class="form-control" value="Bordeaux" disabled/>
				</div>
			</div>
			
			<h3 class="mt-3 mb-1">2. <span id="pickDate">Ma collecte</span></h3>
			<p>Le jour de votre collecte un picker se présentera à votre domicile sur le créneau horaire choisi et remplira avec vous le(s) sac(s) à collecter.<br/><small>Vous vous engagez à être présent durant cette période.</small></p>
			<div class="form-row">
				<div class="col-md-9 form-group">
					<label>Horaire</label>
					<select name="slotID" id="slotID" class="form-control" required="" disabled="">
						<option value="1">Entre 16:00 et 17:00</option><option value="3">Entre 17:00 et 18:00</option><option value="5">Entre 18:00 et 19:00</option><option value="7">Entre 19:00 et 20:00</option><option value="9">Entre 20:00 et 21:00</option>
					</select>
				</div>
				<div class="col-md-3 form-group">
					<label>Sac(s)</label>
					<select name="sacs" id="sacs" class="form-control" required="" disabled="">
						<option value="1" selected="">1</option><option value="0">+</option>
					</select>
				</div>
			</div>
			
			<input id="calID" type="hidden" name="calID" value="">
			
			<div id="credits-row" class="form-row d-none">
				<div class="col-12 form-group">
					<p class="mb-0">Créditez votre compte pour pouvoir programmer la collecte de plusieurs sacs. 1 crédit = 1 sac collecté <br><small>Les crédits sont sans limite de durée.</small></p>
				</div>
				<div class="col-md-9 form-group">
					<label>Créditer mon compte</label>
					<select id="formuleID" name="formuleID" class="form-control" disabled="">										
						<option value="1" data-price="3.5" data-credits="0" data-libelle="3€50 par sac collecté">Moins de 6 crédits</option>
						<option value="8" data-price="20" data-credits="6" data-libelle="3€33  par sac collecté <strong>soit 1€ d'économisé.</strong>" >Pack de 6 crédits</option>
						<option value="2" data-price="36" data-credits="12" data-libelle="3€00 par sac collecté <strong>soit 6€ d'économisé.</strong>">Pack de 12 crédits</option>
						<option value="3" data-price="66" data-credits="24" data-libelle="2€75 par sac collecté <strong>soit 18€ d'économisé.</strong>">Pack de 24 crédits</option>
						<option value="4" data-price="90" data-credits="36" data-libelle="2€50 par sac collecté <strong>soit 36€ d'économisé.</strong>">Pack de 36 crédits</option>
					</select>
					
				</div>
				<div class="col-md-3 form-group">
					<label>Nb.</label>
					<select name="credits" id="credits" class="form-control" disabled="">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</div>
			</div>	

			
		</div>
		<div class="col-md-6">

			<h3 class="mb-1">3. Mon compte</h3>
			<p>En validant ce formulaire vous allez créer un compte Pic'Verre à partir duquel vous pourrez programmer vos prochaines collectes.<br><small> Vous allez recevoir un email permettant d'initialiser votre pot de passe.</small></p>
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

			<ul class='list-group mb-4' id='list-order'>
				<li class='list-group-item' style="background:#9fcced;">
					<div class='h6'><span class='text-uppercase'>1 crédit = <strong class=''>1 sac collecté</strong></span> <span class='badge badge-pill bg-secondary text-primary'><s>3&euro;50</s></span></div>
					<span class='text-dark font-weight-bold'><strong>Nous collectons votre premier sac gratuitement !</strong></span>
				</li>
				<li class='list-group-item pl-1' style="background:#9fcced;">
					<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:100px;'>
					<div class='h6'><span class='text-uppercase'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
					<span class='text-dark font-weight-bold' style="font-size:85%;">Vous devez faire l'acquisition d'un sac Pic'Verre <strong style="font-size:125%;">uniquement pour votre première collecte</strong>. &Agrave; la fin de la collecte le picker vous remettra un sac neuf pour la collecte suivante.</span>
				</li>
				<li id="list-group-credits" class="list-group-item bg-info d-none">
					<div class="h6"><span class="text-uppercase"><span id="creditsNb">1</span> crédit</span> <span id="creditsTotal" class="badge badge-pill bg-secondary text-primary">3€50</span></div>
					<span id="creditsLibelle" class='text-dark font-weight-bold' style="font-size:85%;">3€50 par sac collecté. <strong>Vous pouvez profiter de tarifs dégressifs avec nos packs de crédits.</strong></span>
				</li>
				<li class='list-group-item bg-secondary'>
					<div class='h6 text-uppercase text-white'>Total <span id='orderTotal' class='badge badge-pill bg-primary float-right'>5&euro;</span></div>
				</li>
			</ul>

			<div class='form-footer form-row clearfix'>
				<div class='col-md-12 form-group mb-0'>
					<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'><strong>Valider</strong></button>
				</div>
				<div id='cgv' class='col-md-12 form-group text-center mt-2 '>
				
					<p class='mb-0'><small>En validant ce formulaire, vous acceptez nos <a href='https://www.pic-verre.fr/cgv' target='_blank' class='text-nowrap link-primary'>conditions générales de vente</a></small></p>
					
					<p class="text-center p-2 m-0 border-top border-light" style="line-height:1"><small><strong>Le paiement sécurisé est assuré par <a href="https://www.ocl.natixis.com/systempay/syshome/index/id/1" class="link-primary" target="blank">SystemPay</a></strong>.<br>Vous allez être redirigé vers une page dédiée à partir de laquelle vous pourrez finaliser votre paiement en toute sécurité.</small></p>
					
				</div>
			</div>

		</div>	
	</div>
</form>