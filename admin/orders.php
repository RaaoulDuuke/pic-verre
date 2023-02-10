<?php
	require_once ("../includes/admin.init.php");
	
	$page = "orders";
	$period = (!empty($_REQUEST["period"])) ? $_REQUEST["period"] : "month";
	$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : date("Y-m");
	$week = (!empty($_REQUEST["week"])) ? $_REQUEST["week"] : date("W");

?>

<!DOCTYPE html>
<html lang="fr">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Admin Pic'Verre - Commandes</title>
		<?php require_once ("includes/head.php");?>
	</head>

	<body>
	
		<div id="wrapper">
		
			<?php require_once ("includes/navbar.php");?>
			<?php echo sidebarNav($page);?>
			<?php echo dateBreadcrumb($period, $date, $week, $page); ?>	
			
			<!-- CONTAINER  -->
			<div id="page-content-wrapper">
				<div class="page-content main">
					<div class="container-fluid">
					
						<?php echo datePageHeader($period, $date, $week, $page); ?>											
						<?php echo ordersPage($period, $date); ?>							
						
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
				responsive:false,
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