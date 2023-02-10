<?php
	require_once ("../includes/admin.init.php");
?>

<!DOCTYPE html>
<html lang="fr">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="description" content="">
		<meta name="author" content="">

		<title>Pic'Verre - Connexion</title>

		<?php require_once ("includes/head.php");?>
		
		<style>
/*
 * Base structure
 */

html,
body {
  height: 100%;

}

body {
	  color:#FFF;
  padding-top:0;
	background-color: #2fac66;
  display: -ms-flexbox;
  display: flex;
  box-shadow: inset 0 0 15rem rgba(0, 0, 0, .5);
}

.cover-container {
  max-width: 35em;
}



		</style>

	</head>
  
	 <body class="login">
	 
	 
		 <div class="cover-container d-flex w-100 h-100 mx-auto flex-column">

				<div class="row  my-auto">
					<div class="col-md-5">
						<img src="https://assets.pic-verre.fr/img/logo-pv.svg" class="img-fluid" alt="Responsive image">
					</div>
					<div class="col-md-7">
					
						<form class="form-signin" action="login.action.php" method="post">
							<h2 class="form-signin-heading" style="color:#ffc107; font-size:1.8rem;">Connectez-vous</h2>
							<label for="email" class="sr-only">email</label>
							<input type="text" id="email" name="email" class="form-control mb-1" placeholder="Email" required autofocus>

							<label for="pwd" class="sr-only">Mot de passe</label>
							<input type="password" id="pwd" name="pwd" class="form-control mb-1" placeholder="Mot de passe" required>

							<input type="hidden" value="login" name="action"  />

							<button class="btn btn-primary btn-block" type="submit">Valider</button>


						</form>	
					
					</div>
				</div>						

		</div>
		
		
	</body>

</html>