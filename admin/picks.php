<?php
	require_once ("../includes/admin.init.php");
	
	$page = "picks";
	$period = (!empty($_REQUEST["period"])) ? $_REQUEST["period"] : "week";
	$date = (!empty($_REQUEST["date"])) ? $_REQUEST["date"] : date("Y");
	$week = (!empty($_REQUEST["week"])) ? $_REQUEST["week"] : date("W");
	$secteur = (!empty($_REQUEST["secteur"])) ? $_REQUEST["secteur"] : ""; 
	
?>

<!DOCTYPE html>
<html lang="fr">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">	
		<title>Admin Pic'Verre - Collectes</title>
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
						<?php echo picksPage($date, $period, $week, $secteur); ?>
						
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
			$('#closedPicksTable').DataTable({
				"language": { 
					'paginate': {
					  'previous': '<span class="fas fa-chevron-left"></span>',
					  'next': '<span class="fas fa-chevron-right"></span>'
					}
				},
				"searching": false,
				"ordering": false,
				"paging": true,
				"lengthChange": false,
				"bInfo" : false,
				responsive:true,
				autoWidth: false,
				"columnDefs": [
					{"targets": [ 0 ]},
					{"targets": [ 1 ]},
					{"targets": [ 2 ]},
					{"targets": [ 3 ]},
					{"targets": [ 4 ]}
				]
			});	
		});
		</script>

		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAO8WVwrdcEined5BSvr2tvxhnvReA4Jwk" async defer></script>
	
	</body>
	
</html>