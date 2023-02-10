<?php
	require_once ("../includes/admin.init.php");
	
	$resellerID =  (!empty($_REQUEST["resellerID"])) ? $_REQUEST["resellerID"] : "";
	
?>

<!DOCTYPE html>
<html lang="fr">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Pic'Verre - Revendeurs</title>
		<?php require_once ("includes/head.php");?>

	</head>
	
	<body>
	
		<div id="wrapper">

			<?php require_once ("includes/navbar.php");?>
			<?php echo sidebarNav("resellers");?>
			<?php echo resellersPageBreadcrumb($resellerID); ?>
		
			<!-- CONTAINER  -->
			<div id="page-content-wrapper">
				<div class="page-content main">
					<div class="container-fluid">
					
						<?php echo resellersPageHeader($resellerID); ?>			
						<?php 
							if(empty($resellerID)){
								echo resellersPage(); 
							}else{
								echo resellerPage($resellerID);
							}
						?>	
					</div>
				</div>
			</div>
		
		</div>
		
		<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				</div>
			</div>
		</div>

		<?php include("../includes/admin.scripts.php"); ?>
		<script>
		$(document).ready(function() {
			$('#resellersTable').DataTable({
				"language": { "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json" },
				"paging": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0}
				]
			});
		
		});
		</script>
	
	</body>
	
</html>