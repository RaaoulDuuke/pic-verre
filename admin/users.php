<?php
	require_once ("../includes/admin.init.php");

	$secteur = (!empty($_REQUEST["secteur"])) ? $_REQUEST["secteur"] : 0;
	$state =  (!empty($_REQUEST["state"])) ? $_REQUEST["state"] : "actifs";
	$userID =  (!empty($_REQUEST["userID"])) ? $_REQUEST["userID"] : "";
	
?>

<!DOCTYPE html>
<html lang="fr">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Pic'Verre - Abonnés</title>
		<?php require_once ("includes/head.php");?>

	</head>
	
	<body>
	
		<div id="wrapper">

			<?php require_once ("includes/navbar.php");?>
			<?php echo sidebarNav("users");?>
			<?php echo usersPageBreadcrumb($state, $secteur, $userID); ?>
		
			<!-- CONTAINER  -->
			<div id="page-content-wrapper">
				<div class="page-content main">
					<div class="container-fluid">
					
						<?php echo usersPageHeader($state, $secteur, $userID); ?>
						
						<?php 
							if(empty($userID)){
								echo usersPage($state, $secteur); 
							}else{
								echo userPage($userID); 
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
			$('#usersTablepro').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": false,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0},
					{"targets": [ 2 ]},
					{"targets": [ 3 ]},
					{"targets": [ 4 ]}
				]
			});
			$('#usersTablepar').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": false,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0},
					{"targets": [ 2 ]},
					{"targets": [ 3 ]},
					{"targets": [ 4 ]}
				]
			});
			
			$('#usersTable').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": true,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0},
					{"targets": [ 2 ]},
					{"targets": [ 3 ]},
					{"targets": [ 4 ]}
				]
			});
			
			$('#transOrdersTable').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": false,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0},
					{"targets": [ 2 ],"orderable": false},
					{"targets": [ 3 ],"orderable": false},
					{"targets": [ 4 ],"orderable": false},
					{"targets": [ 5 ],"orderable": false}
				]
			});		
			
			$('#pendingOrdersTable').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": false,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0},
					{"targets": [ 2 ],"orderable": false},
					{"targets": [ 3 ],"orderable": false},
					{"targets": [ 4 ],"orderable": false},
					{"targets": [ 5 ],"orderable": false}
				]
			});	
			
			$('#cancelOrdersTable').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": false,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{"targets": [ 0 ], "visible": false, "searchable": false },
					{"targets": [ 1 ], "orderData": 0},
					{"targets": [ 2 ],"orderable": false},
					{"targets": [ 3 ],"orderable": false},
					{"targets": [ 4 ],"orderable": false},
					{"targets": [ 5 ],"orderable": false}
				]
			});			
		
		});
		
		
		
		
		
		
		</script>
	
	</body>
	
</html>