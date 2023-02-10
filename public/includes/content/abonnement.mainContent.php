


<h2>Abonnement au service</h2>
<?php echo "test".$action; ?>
<?php if(empty($action)){ ?>
			
<p class="font-weight-bold text-white mb-4">Saisissez votre adresse e-mail pour être tenu(e) informé(e) de l'ouverture des abonnements.</p>
<form action="suscribe.php" method="post" class="needs-validation" novalidate>
	<div class="form-row">
		<div class="col-9">
			<label class='sr-only' for='email'>Votre e-mail</label>
			<input type='email' class='form-control mr-sm-2' id='email' name='email' placeholder='Votre adresse e-mail' pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
		</div>
		<div class="col-3">
			<button type="submit" class="btn btn-sm btn-primary btn-block font-weight-bold">Valider</button>
		</div>
		<div class="col-12">
			<small class="form-text mt-3 text-white">Votre adresse email sera uniquement utilisé pour vous tenir informé(e) de l'ouverture des abonnements, rien d'autre.</small>
		</div>
		<input type='hidden' name='action' value='suscribe'>
	</div>
</form>
<script>
(function() {
  'use strict';
  window.addEventListener('load', function() {
	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.getElementsByClassName('needs-validation');
	// Loop over them and prevent submission
	var validation = Array.prototype.filter.call(forms, function(form) {
	  form.addEventListener('submit', function(event) {
		if (form.checkValidity() === false) {
		  event.preventDefault();
		  event.stopPropagation();
		}
		form.classList.add('was-validated');
	  }, false);
	});
  }, false);
})();
</script>
<?php } ?>