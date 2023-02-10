(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();



$(function() {
		
	$( '#voieSelect' ).autocomplete({
	  minLength: 5,
	  source: function( request, response ) {
	   // Fetch data
	   $.ajax({
		url: 'includes/voies.select.php',
		type: 'post',
		dataType: 'json',
		data: {
		 search: request.term
		},
		success: function( data ) {
		 response( data );
		}
	   });
	  },
	  focus: function( event, ui ) {
		return false;
	  },
	  select: function( event, ui ) {
		$.ajax({
            method: "post",
            url: 'includes/voies.select2.php',
            data: {calID: ui.item.calID},
			success:function(response) {
				
				$('#pickDate').html(response);

			}
        })

		$('#calID').val( ui.item.calID );
		$('#voieSelect').val( ui.item.label );
		$('#slotID').prop('disabled', false);
		$('#sacs').prop('disabled', false);
		
		return false;
	  }
	});			
});



$('#voieSelect').focus(function() {
	$('#voieSelect').val('');
	$('#pickDate').html('Ma collecte');
	$('#slotID').prop('disabled', true);
	$('#sacs').prop('disabled', true);
	$('#calID').val( '' );
	$('#formuleID').prop('disabled', true);
	$('#credits').prop('disabled', true);
	$('#list-group-credits').addClass('d-none');
	$('#orderTotal').html(5+'&euro;');
	$('#credits-row').addClass('d-none');
});

$('#voieSelect').focusout(function() {
	if($('#calID').val()==''){
		$('#voie-alert').addClass('text-danger');
		$('#voie-alert').removeClass('d-none');
	}else{
		$('#voie-alert').addClass('d-none');
	}
});



$('#sacs').on('change', function () {
	if($('#sacs').val()==0){
		$('#list-group-credits').removeClass('d-none');
		$('#credits-row').removeClass('d-none');
		$('#formuleID').prop('disabled', false);
		$('#credits').prop('disabled', false);
		$('#credits').val('1');
		$('#orderTotal').html('8&euro;50');
	}else{
		$('#list-group-credits').addClass('d-none');
		$('#credits-row').addClass('d-none');
		$('#orderTotal').html('5&euro;');
		$('#credits').prop('disabled', true);
		$('#formuleID').prop('disabled', true);
	}
});

$('#credits').change(function() {
	$('#creditsNb').html($('#credits').val());
	$('#creditsTotal').html($('#credits').val()*3.5+'&euro;');
	$('#orderTotal').html(5+$('#credits').val()*3.5+'&euro;');
});

$('#formuleID').on('change', function () {
	$('#creditsLibelle').html($('#formuleID').find(':selected').attr('data-libelle'));	
	if($('#formuleID').val()==1){
		
		$('#list-group-credits').removeClass('d-none');		
		$('#credits').prop('disabled', false);
		$('#credits').val('1');
		$('#credits').prop('required',true);
		$('#creditsNb').html('1');
		$('#creditsTotal').html('3.5&euro;');						
		$('#orderTotal').html(3.5+5+'&euro;');
		
	}else{
		
		if($('#formuleID').val()==''){
			$('#list-group-credits').addClass('d-none');
		}else{
			$('#list-group-credits').removeClass('d-none');
		}
		
		var credits = $('#formuleID').find(':selected').attr('data-credits');
		$('#credits').val(credits);
		$('#credits').prop('disabled', true);					
		$('#credits').prop('required',false);
		$('#creditsNb').html(credits);					
		$('#creditsTotal').html($('#formuleID').find(':selected').attr('data-price')+'&euro;');
	
		var price=parseInt($('#formuleID').find(':selected').attr('data-price'));
		$('#orderTotal').html(price+5+'&euro;');
		
	}
});



$("button.alert").hover(function(){
	$(this).find("span.btn").addClass( "animated tada" );
});

$("button.alert").mouseleave(function() {
 $(this).find("span.btn").removeClass( "animated tada" );
});

$("a.alert").hover(function(){
	$(this).find("span.btn").addClass( "animated tada" );
});

$("a.alert").mouseleave(function() {
 $(this).find("span.btn").removeClass( "animated tada" );
});




$(document).ready(function() {
    $("#companyCarousel").carousel(window.location.hash.substr(1) - 1);
});




/*

$('#sacs').change(function() {	
					
	var sacMontant = $('#sacs').val()*3.5;
	var sacMontantHT = (sacMontant/1.2).toFixed(2);
	var creditsNb = $('#frequence').val()*$('#sacs').val();
	var creditsMontant = creditsNb*2.5;
	var creditsMontantHT = (creditsMontant/1.2).toFixed(2);
	var subMontant = 50;
	var subMontantHT = (subMontant/1.2).toFixed(2);							
	var orderMontant = (subMontant+sacMontant+creditsMontant).toFixed(2);
	var orderMontantHT = (orderMontant/1.2).toFixed(2);
	var orderTVA = (orderMontant-orderMontantHT).toFixed(2);
	
	$('#orderHT').html(orderMontantHT);
	$('#orderTTC').html(orderMontant);
	$('#orderTVA').html(orderTVA);
	
	$('#sacsNb').html($(this).val());
	$('#sacsTotal').html(sacMontantHT);
	$('#creditsNb').html(creditsNb);
	$('#creditsTotal').html(creditsMontantHT);

	if($('#sacs').val()==0){
		$('#list-group-sacs').addClass('d-none');
		$('#list-group-credits').addClass('d-none');
	}else{
		$('#list-group-sacs').removeClass('d-none');
		$('#list-group-credits').removeClass('d-none');
	}
	
});

$('#frequence').on('change', function () {
	
	var sacMontant = $('#sacs').val()*3.5;
	var sacMontantHT = (sacMontant/1.2).toFixed(2);
	var creditsNb = $('#frequence').val()*$('#sacs').val();
	var creditsMontant = creditsNb*2.5;
	var creditsMontantHT = (creditsMontant/1.2).toFixed(2);
	var subMontant = 50;
	var subMontantHT = (subMontant/1.2).toFixed(2);							
	var orderMontant = (subMontant+sacMontant+creditsMontant).toFixed(2);
	var orderMontantHT = (orderMontant/1.2).toFixed(2);
	var orderTVA = (orderMontant-orderMontantHT).toFixed(2);
	
	$('#orderHT').html(orderMontantHT);
	$('#orderTTC').html(orderMontant);
	$('#orderTVA').html(orderTVA);
	
	$('#creditsNb').html(creditsNb);
	$('#creditsTotal').html(creditsMontantHT);
	
});

*/
// $('#covid').modal('show');
