$( document ).ready( function () {

	jQuery.validator.addMethod("validacion", function(value, element, param) {
		// Obtener los valores de los campos necesarios
		var hours_first_project = parseFloat($('#hours_first_project').val()) || 0;
		var hours_last_project = parseFloat($('#hours_last_project').val()) || 0;
		var workedHours = parseFloat($('#workedHours').val()) || 0;

		// Calcular el tiempo trabajado en minutos
		let tiempo_trabajado = hours_first_project + hours_last_project;

		console.log(tiempo_trabajado);
		console.log(workedHours);

		return workedHours === tiempo_trabajado;
	}, function(params, element) {
		var workedHours = $('#workedHours').val() || 0;
		return "The total sum of hours must be equal to " + workedHours + " hours";
	});

	$( "#formWorker" ).validate( {
		rules: {
			jobName:	 			{ required: true },
			hours_first_project:	{ required: true, validacion: true },
			jobNameFinish:	 		{ required: true },
			hours_last_project:	 	{ required: true, validacion: true },
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

	$("#btnSubmit").click(function () {
		if ($("#formWorker").valid() == true){

			//Activa icono guardando
			$('#btnSubmitWorker').attr('disabled','-1');
			$("#div_error").css("display", "none");
			$("#div_load").css("display", "inline");

			$.ajax({
				type: "POST",
				url: base_url + "payroll/updateTaskWithWO",
				data: $("#formWorker").serialize(),
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				cache: false,

				success: function (data) {
					if( data.result == "error" )
					{
						$("#div_load").css("display", "none");
						$('#btnSubmitWorker').removeAttr('disabled');
						return false;
					}

					if( data.result )//true
					{
						$("#div_load").css("display", "none");
						$('#btnSubmitWorker').removeAttr('disabled');

						window.location.reload();
					}
					else
					{
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmitWorker').removeAttr('disabled');
					}
				},
				error: function(result) {
					alert('Error. Reload the web page.');
					$("#div_load").css("display", "none");
					$("#div_error").css("display", "inline");
					$('#btnSubmitWorker').removeAttr('disabled');
				}
			});
		}
	});
});