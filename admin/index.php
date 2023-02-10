<?php
	require_once ("../includes/admin.init.php");
	
	$page = "dashboard";
	$period = "day";
	$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : date("Y-m-d");

?>

<!DOCTYPE html>
<html lang="fr">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Pic'Verre - Tableau de bord</title>
		<?php require_once ("includes/head.php");?>
	</head>

	<body style='padding-top:50px;'>
	
		<div id="wrapper">
		
			<!-- NAVBAR  -->
			<?php require_once ("includes/navbar.php");?>
			
			<!-- SIDEBAR -->
			<?php echo sidebarNav("dash");?>
			
			<!-- CONTAINER  -->
			<div id="page-content-wrapper">
			
				<!-- MAIN -->
				<div class="page-content main">
				<div class="container-fluid">

					<!-- PAGE HEADER -->
					<?php  echo datePageHeader($period, $date, $page); ?>

					<div class="row">
						<?php  echo dboardPage($date); ?>
					</div>

				</div>
				</div>
			</div>
		</div>
		
		<!-- MODAL -->
		<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				</div>
			</div>
		</div>		
	
		<?php include("../includes/admin.scripts.php"); ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAO8WVwrdcEined5BSvr2tvxhnvReA4Jwk" async defer></script>
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