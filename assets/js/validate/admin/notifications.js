$( document ).ready( function () {

	jQuery.validator.addMethod("fieldOperated", function(value, element, param) {
		var smsTo = $('#smsTo').val();
		var emailTo = $('#emailTo').val();

		// Verifica si ambos arreglos están vacíos
		if (!Array.isArray(smsTo) || smsTo.length === 0) {
			smsTo = []; // Asegura que smsTo sea un arreglo vacío si no hay selección
		}

		if (!Array.isArray(emailTo) || emailTo.length === 0) {
			emailTo = []; // Asegura que emailTo sea un arreglo vacío si no hay selección
		}

		// Retorna false si ambos arreglos están vacíos, true de lo contrario
		return smsTo.length > 0 || emailTo.length > 0;
	}, "You must indicate Email or/and SMS.");


	$( "#form" ).validate( {
		rules: {
			notification: { required: true },
			"emailTo[]":  { fieldOperated: true },
			"smsTo[]":    { fieldOperated: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-6" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-6" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});

	$("#btnSubmit").click(function(){

		if ($("#form").valid() == true){

			//Activa icono guardando
			$('#btnSubmit').attr('disabled','-1');
			$("#div_error").css("display", "none");
			$("#div_load").css("display", "inline");

			$.ajax({
				type: "POST",
				url: base_url + "admin/save_notifications",
				data: $("#form").serialize(),
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				cache: false,

				success: function(data){

					if( data.result == "error" )
					{
						$("#div_load").css("display", "none");
						$('#btnSubmit').removeAttr('disabled');
						return false;
					}

					if( data.result )//true
					{
						$("#div_load").css("display", "none");
						$('#btnSubmit').removeAttr('disabled');

						var url = base_url + "admin/notifications";
						$(location).attr("href", url);
					}
					else
					{
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmit').removeAttr('disabled');
					}
				},
				error: function(result) {
					alert('Error. Reload the web page.');
					$("#div_load").css("display", "none");
					$("#div_error").css("display", "inline");
					$('#btnSubmit').removeAttr('disabled');
				}
			});
		}
	});
});