<div id="carouselExampleControls" class="carousel  page-carousel">

	<ul class="carousel-indicators nav justify-content-center text-uppercase" >
		<li class="active nav-item " data-target="#carouselExampleControls" data-slide-to="0"><a class="nav-link" href="#">Le sac Pic'Verre</a></li>
		<li class="nav-item " data-target="#carouselExampleControls" data-slide-to="1"><a class="nav-link" href="#">Vidéo</a></li>
		<li class="nav-item " data-target="#carouselExampleControls" data-slide-to="2"><a class="nav-link" href="#">Fiche technique</a></li>   
	</ul>
	

	<div class="carousel-inner mt-5">
  
		<div class="carousel-item carousel-page-item active">

			<div class="row">

				<div class="col-md-5">
					<img src='https://assets.pic-verre.fr/img/sac-pv-poids-2.png' alt='Sac Pic Verre avec contenu' class='d-block w-100'>
				</div>
	
				<div class="col-md-7">
	
					<p class="lead"><strong>Nous avons fait concevoir un sac cabas en matériau recyclé, réutilisable et fermable, d’un volume de 20L (environ 5kg), avec 9&nbsp;compartiments de rangement lui conférant une tenue optimale pour le stockage et le transport du verre.</strong></p>
		
					<p>Pour la sécurité du contenu, le sac Pic’verre est équipé d'un rabat refermable grâce à une bande velcro cousue sur la largueur, de longues poignées en nylon facilitant le transport et des renforts aux coutures avec des bandeaux de protections pour soutenir le poids du verre lors de la collecte.</p>	
		
					<div class="alert rounded p-3 my-4 text-center">
						<p class=" font-weight-bold mb-3" style="font-size:1.3rem;">Commmandez un sac Pic'Verre maintenant<br><strong>Nous vous offrons votre première collecte</strong></p>
			
						<button  class="btn btn-primary btn-block"  data-toggle="modal" data-target="#sacOrderModal"><strong>Commander un sac</strong></button>
			
					</div>
		
				</div>
	
			</div>

		</div>
	
		<div class="carousel-item carousel-page-item " >
		
			<div class="video-container">
				<div class="embed-responsive embed-responsive-16by9">
				  <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/2W_oNx0cR6A?rel=0" allowfullscreen></iframe>
				</div>
			</div>
			
		</div>	

		 <div class="carousel-item carousel-page-item ">
		
			<div class="row align-items-center">

				<div class="col-12 col-md-4 offset-md-2">
					<img src="img/sac-pv-tech.png" class="d-block w-100 mb-4" alt="Sac Pic'Verre">
				</div>
				
				<div class="col-12 col-md-4">
					<p class='text-center'>
						Contenance : <strong>20L (environ 5kg)</strong><br>
						Format : <strong>40 x 30 x 16cm</strong><br>
						Matière : <strong>Polypropylène tissé</strong><br>(matière recyclée de 50 à 80%)<br>
						<strong>Poignées en nylon</strong> (3 x 70cm)<br>
						<strong>Bande velcro de fermeture</strong> (2.5cm)
					</p>
				</div>
				
			</div>	

		</div>	

	</div>

</div>




<div class="modal fade" id="sacOrderModal" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
	
		<div class="modal-header">
			<h3>Commander un sac</h3>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		
		<div class="modal-body">
			<form action="https://mon-compte.pic-verre.fr/edit/sac.www.edit.php" method="post" class="needs-validation" novalidate id="sacOrderForm">
			
				<p class='font-weight-bold'>Une fois votre commande validée vous pourrez programmer votre première collecte. Nous collectons votre premier sac gratuitement.</p>
				
				<ul class='list-group mb-3' id='list-order'>
				
					<li class='list-group-item bg-info' >
						<div class='h6'><span class='text-uppercase pr-5'>1 crédit = <strong class='text-white text-nowrap'>1 sac collecté</strong></span> <span class='badge badge-pill bg-secondary text-primary'>offert</span></div>
					</li>
					
					<li class='list-group-item pl-1 bg-info'>
						<img src='https://assets.pic-verre.fr/img/sac-pv-sm-border.png' class='float-left d-block mr-3' style='height:120px;'>
						<div class='h6'><span class='text-uppercase  pr-5'>1 sac Pic'Verre</span> <span class='badge badge-pill bg-secondary text-primary'>5&euro;</span></div>
						<span class='font-weight-bold text-white'>Un sac dont vous serz titulaire vous sera remis lors de votre première collecte.<br><br><a href='https://www.pic-verre.fr/fonctionnement' target='_blank' >En savoir plus</a></span>
					</li>
					
					<li class='list-group-item bg-secondary'>
						<div class='h6 text-uppercase text-white'>Total <span id='orderTotal' class='badge badge-pill bg-primary float-right'>5&euro;</span></div>
					</li>
					
				</ul>
				
				<h4 class="mt-3">Adresse</h4>
				<div class="form-row">
					<div class="col-md-6 form-group">
						<label>Nom</label>
						<input class="form-control" type="text" name="nom" required>
					</div>
					<div class="col-md-6 form-group">
						<label>Prenom</label>
						<input class="form-control" type="text" name="prenom" required>
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-3 form-group">
						<label>Numero</label>
						<input class="form-control" type="text" name="voieNumero" required>
					</div>
					<div class="col-md-9 form-group">
						<label>Voie</label>
						<input class="form-control" type="text" name="voieSelect" required>
					</div>
					
				</div>
				<div class="form-row">
					<div class="form-group col-md-3">
						<label>Code Postal</label>
						<input type="text" class="form-control" value="33000" disabled/>
					</div>
					<div class="form-group col-md-9">
						<label>Ville</label>
						<input type="text" class="form-control" value="Bordeaux" disabled/>
					</div>
				</div>
				
				<h4 class="mt-3">Contact</h4>
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
				
				<div class='form-footer form-row clearfix'>
					<div class='col-md-12 form-group mb-0'>
						<button class='btn btn-block btn-lg btn-primary hvr-icon-push' type='submit'>Valider</button>
					</div>
					<div id='cgv' class='col-md-12 form-group text-center mt-2 '>
					
						<p class='mb-0'><small>En validant ce formulaire, vous acceptez nos <a href='https://www.pic-verre.fr/cgv' target='_blank' class='text-nowrap link-primary'>conditions générales de vente</a></small></p>
						
						<p class="text-center p-2 m-0 border-top border-light"><small><strong>Le paiement sécurisé est assuré par <a href="https://www.ocl.natixis.com/systempay/syshome/index/id/1" class="link-primary" target="blank">SystemPay</a></strong>.<br>Vous allez être redirigé vers une page dédiée à partir de laquelle vous pourrez finaliser votre paiement en toute sécurité.</small></p>
						
					</div>
				</div>

			</form>
		
		</div>
		
    </div>
  </div>
</div>

