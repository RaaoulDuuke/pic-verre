<?php
	require_once ("../includes/account.init.php");
	
	$token = (!empty($_REQUEST['token'])) ? $_REQUEST['token'] : '';
	$email = (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : '';
	$action = (!empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';
	
	if(!empty($token)&&!empty($email)&&!empty($action)&&$action=='resetpwd'){
		
		$reset_rq = "SELECT * FROM reset WHERE email='{$email}' AND token='{$token}'";
		$reset_rs = mysqli_query ($connexion, $reset_rq);
		
		if(mysqli_num_rows($reset_rs)){
			
			
			if ($_SERVER['REQUEST_METHOD'] == 'GET'){
			
				echo "
				<form action='reset.php' method='post' name='resetForm'>
					<input type='hidden' name='token' value='{$token}'>
					<input type='hidden' name='email' value='{$email}'>
					<input type='hidden' name='action' value='{$action}'>
				</form>
				<script language='JavaScript'>
					document.resetForm.submit();
				</script>";
			}
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST'){
				
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
		<title>Mon compte Pic'Verre - Réinitialisation du mot de passe</title>
		<?php require_once("../includes/account.head.php");?>
	</head> 
	<body class="login">
		<!-- HEADER -->
		<header class="header container-fluid">
			<div class="d-flex justify-content-between">
				<a href="https://www.pic-verre.fr"><i class="fas fa-arrow-circle-left"></i> Retour à l'accueil</a>
				<a href="edit/theme.php">Mode <?php echo themeLink($_SESSION['theme']); ?> <i class="fas fa-eye"></i></a>
			</div>
		</header>
		<!-- CONTENT -->		
		<div class="container d-flex mx-auto flex-column">
			<div class="row my-auto no-gutters">			
				<div class="col-6 offset-3 offset-sm-0 col-sm-6 pr-3">
					<img id="logo" src="https://assets.pic-verre.fr/img/logo-pv-txt.svg" class="img-fluid" alt="PIC’VERRE">
				</div>				
				<div class="col-12 col-sm-6">		
					<h1 class="form-signin-heading" style="font-size:1.75rem;">Réinitialisation du mot de passe</h1>		
					<form class="form-signin mb-3 needs-validation" action="edit/log.php" method="post" novalidate>					
						<label for="pwd">Nouveau mot de passe</label>
						<input type="password" id="pwd" name="pwd" class="form-control mb-2" required autofocus>
						<label for="pwdconfirm">Confrmation</label>
						<input type="password" id="pwdconfirm" name="pwdconfirm" class="form-control mb-2" required>
						<input type="hidden" value="<?php echo $token; ?>" name="token">
						<input type="hidden" value="<?php echo $action; ?>" name="action">
						<button class="btn btn-secondary btn-block mt-3" type="submit">Valider</button>
					</form>
				</div>					
			</div>
		</div>
		<!-- FOOTER -->
		<footer class="footer">
			<div class="container-fluid text-right">
				<?php require_once("includes/footer.php");?>
			</div>
		</footer>
		<!-- MODAL CONTACT -->
		<?php require_once("includes/contact.modal.php");?>
		
		<?php include("../includes/account.scripts.php"); ?>	
		
	</body>
</html>

<?php			
				
			}

			
		}else{
			header("location:connexion.php");
			exit;
		}
	}else{
		header("location:connexion.php");
		exit;
	}
?>

