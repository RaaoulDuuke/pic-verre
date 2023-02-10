<?php
	require_once ("../includes/admin.init.php");
	
	$action = (!empty($_REQUEST["action"])) ? $_REQUEST["action"] : '';
	
?>

<!DOCTYPE html>
<html lang="fr">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="description" content="">
		<meta name="author" content="">

		<title>Pic'Verre - Abonnements</title>

		<?php require_once ("includes/head.php");?>

	</head>
	
	<body>
	
		<div id="wrapper">
		
		<!-- NAVBAR  -->
		<?php require_once ("includes/navbar.php");?>
		
		<!-- SIDEBAR -->
		<?php echo sidebarNav("voies");?>
		
		<!-- CONTAINER  -->
		<div id="page-content-wrapper">
		
			<!-- MAIN -->
			<div class="page-content main">
				<div class="container-fluid">
				
					<?php echo displayAlert($action); ?>
					
					<!-- BREADCRUMB -->
					<nav aria-label="breadcrumb" >
					  <ol class="breadcrumb px-0 py-1">
						<li class="breadcrumb-item"><a href="dashboard">Tableau de bord</a></li>
						<li class="breadcrumb-item active" aria-current="page">Voies</li>
					  </ol>
					</nav>

					<h2 class="page-header">Voies</h2>
					
					<a href="voies.edit.php?action=create" data-toggle="modal" data-target="#edit_lightbox">Ajouter</a>

					<?php echo voiesTable(); ?>


					

				</div>
			</div>
		
		</div>
		
		</div>
		
		<div class="modal fade" id="edit_lightbox" tabindex="-1" role="dialog" aria-labelledby="">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				</div>
			</div>
		</div>

		<?php include("../includes/admin.scripts.php"); ?>
		
		<script>
		$(document).ready(function() {

			$('.voiesTable').DataTable({
				"language": { "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json" },
				"order": [[ 1, "asc" ]],
				"columnDefs": [
					{"targets": [ 0 ]},
					{"targets": [ 1 ]},
					{"targets": [ 2 ]}
				],
				dom: 'lfrtip'
			});
			
		});
		</script>
	
	</body>
	
</html>