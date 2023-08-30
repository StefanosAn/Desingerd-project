
jQuery(document).ready(function($){
	
	var value = $('input[name = billing_timologio]');

	// Εδώ κοιτάμε αν είναι επιλεγμένο το "τιμολογιο" ή "αποδειξη" κατά την φόρτωση της σελίδας.
	if(value.val() == 1){
		hide_invoice_fields();
	}else{
		show_invoice_fields();
	}
	
	// Εδώ κοιτάμε το onchange event
	value.on('change',function(){
		if($(this).val() == 0){
			show_invoice_fields();
		}
		else{
			hide_invoice_fields();
		}
	});

	function show_invoice_fields(){
		$( '.invoice-field' ).each(function() {
		$(this).show();
		});
		//hide onomateponimo
		$( '.invoice-name-field' ).each(function() {
			$(this).hide();
			});
	}
	function hide_invoice_fields(){
		$( '.invoice-field' ).each(function() {
		$(this).hide();
		});
		//show onomateponimo
		$( '.invoice-name-field' ).each(function() {
			$(this).show();
			});
	}
});