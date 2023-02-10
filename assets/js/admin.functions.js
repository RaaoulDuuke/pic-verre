$(function () {
	
	
	$('[data-toggle="tooltip"]').tooltip();
	
	
	$('.datepicker').datepicker({
		dateFormat: "dd-mm-yy",
		showOn: "focus",
		//renderer: $.ui.datepicker.defaultRenderer,
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
		'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
		'Jul','Aoû','Sep','Oct','Nov','Déc'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		firstDay: 1,
		prevText: '&#x3c;Préc', prevStatus: 'Voir le mois précédent',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: 'Voir l\'année précédent',
		nextText: 'Suiv&#x3e;', nextStatus: 'Voir le mois suivant',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: 'Voir l\'année suivant',
		currentText: 'Courant', currentStatus: 'Voir le mois courant',
		todayText: 'Aujourd\'hui', todayStatus: 'Voir aujourd\'hui',
		clearText: 'Effacer', clearStatus: 'Effacer la date sélectionnée',
		closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
		yearStatus: 'Voir une autre année', monthStatus: 'Voir un autre mois',
		weekText: 'Sm', weekStatus: 'Semaine de l\'année',
		dayStatus: '\'Choisir\' le DD d MM',
		defaultStatus: 'Choisir la date',
		//isRTL: false
	});
	
	$( ".spinner" ).spinner();
	
	
});	

/*Menu-toggle*/
$("#menu-toggle").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("active");
});



/* RESET MODAL */
$('#editModal').on('hidden.bs.modal', function (e) {
	// $('.modal-content').removeData();
	$('#editModal .modal-content').empty();
});

/* LOAD MODAL */
$('#editModal').on('show.bs.modal', function (e) {

    var button = $(e.relatedTarget);
    var modal = $(this);
   
	var edit = button.attr('data-edit');
	var request = button.attr('data-rq');
	var href = 'edit/'+edit+'.edit.php?'+request;

	$.ajax({
	   url: href,
	   dataType: "html",
	   success: function(html) {
		  modal.find('.modal-content').html(html);
	   }
	});	
});

$('.table-responsive-sm').on('show.bs.dropdown', function () {
     $('.table-responsive-sm').css( "overflow", "inherit" );
});

$('.table-responsive-sm').on('hide.bs.dropdown', function () {
     $('.table-responsive-sm').css( "overflow", "auto" );
})