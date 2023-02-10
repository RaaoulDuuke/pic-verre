<nav class="navbar fixed-top py-0 d-flex justify-content-between border-bottom border-white">
	<a id="menu-toggle" href="#" class="toggle btn btn-link p-2 hvr-icon-push" role="button" title="menu"><i class="fa fa-bars hvr-icon"></i> <span class="sr-only">Menu</span></a>
	<div class="navbar-brand p-0 m-0 text-primary text-uppercase">
		<a href="https://mon-compte.pic-verre.fr" class="my-0 mx-1"><img src="https://assets.pic-verre.fr/img/logo-pv.svg" alt="Pic'Verre"/> <span>Mon compte</span></a> Pic'Verre
	</div>	
	<div>
		<a href="edit/log.php?action=out" class="btn-sm btn-link btn hvr-icon-push"><i class="fas fa-sign-out-alt hvr-icon"></i> <span>Déconnexion</span></a> <a href="https://www.pic-verre.fr/app" class="btn-sm btn-link btn hvr-icon-push" target="_blank"><i class="fas fa-mobile-alt hvr-icon"></i><span class="sr-only">Application</span></a> <a href="edit/theme.php "class="btn-sm btn-link btn hvr-icon-push"><span class="sr-only">Mode <?php echo themeLink($_SESSION['theme']); ?></span> <i class="fas fa-eye"></i></a>
	</div>
</nav>