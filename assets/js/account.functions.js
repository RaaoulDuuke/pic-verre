/* SERVICE WORKER */
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('sw.js').then(function(registration) {
      // Registration was successful
      console.log('ServiceWorker registration successful with scope: ', registration.scope);
    }, function(err) {
      // registration failed :(
      console.log('ServiceWorker registration failed: ', err);
    });
  });
}


// Example starter JavaScript for disabling form submissions if there are invalid fields
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


function cycleBackground() {
	
	var scrWidth = screen.width;
	
	if(scrWidth < 576){
		$('#pickSection').css('background-position', '20px 35px').animate({'background-position-x': '500px'}, 2000, 'swing',function(){
			$('#pickSection').css('background-position', '-300px 35px').animate({ 'background-position-x': '20px'}, 2500, 'swing');
		});
	}else{
		$('#pickSection').css('background-position', '40px center').animate({'background-position-x': '1400px'}, 2000, 'swing',function(){
			$('#pickSection').css('background-position', '-300px center').animate({ 'background-position-x': '40px'}, 2500, 'swing');
		});
	}	
	

}

$('[rel="tooltip"]').tooltip();

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

$("button.btn-tada").hover(function(){
	$(this).find("span.btn").addClass( "animated tada" );
});

$("button.btn-tada").mouseleave(function() {
 $(this).find("span.btn").removeClass( "animated tada" );
});



$(document).ready(function() {
	cycleBackground();

	$('#menu-toggle').on('click', function () {
		$("#wrapper").toggleClass("active");
	});
});

