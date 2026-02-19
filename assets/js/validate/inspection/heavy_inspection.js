$( document ).ready( function () {

	$("#hours").bloquearTexto().maxlength(10);
	$( "#form" ).validate( {
		rules: {
			hours: 			{ number: true, minlength: 2, maxlength: 10 },
			belt:			{ required: true },
			oil: 			{ required: true },
			coolantLevel: 	{ required: true },
			coolantLeaks:	{ required: true },
			workingLamps:	{ required: true },
			beaconLights:	{ required: true },
			heater:			{ required: true },
			operatorSeat:	{ required: true },
			gauges:			{ required: true },
			horn:			{ required: true },
			seatbelt:		{ required: true },
			cleanInterior:	{ required: true },
			windows:		{ required: true },
			cleanExterior:	{ required: true },
			wipers:			{ required: true },
			backupBeeper:	{ required: true },
			door:			{ required: true },
			decals:			{ required: true },
			boom:			{ required: true },
			tableExcavator:	{ required: true },
			bucketPins:		{ required: true },
			bladePins:		{ required: true },
			ripper:			{ required: true },
			frontAxle:		{ required: true },
			rearAxle:		{ required: true },
			tableDozer:		{ required: true },
			pivinPoints:	{ required: true },
			bucketPinsSkit:	{ required: true },
			sideArms:		{ required: true },
			bucket:			{ required: true },
			cutting:		{ required: true },
			blades:			{ required: true },
			tracks:			{ required: true },
			rubberTrucks:	{ required: true },
			rollers:		{ required: true },
			thamper:		{ required: true },
			drill:			{ required: true },
			fire:			{ required: true },
			aid:			{ required: true },
			spillKit:		{ required: true },
			tire:			{ required: true },
			turn:			{ required: true },
			rims:			{ required: true },
			brake:			{ required: true },
			transmission:	{ required: true },
			hydrolic: 	    { required: true },
			def:		    { required: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});

	$("#btnSubmit").click(function(){

		if ($("#form").valid() == true){

				//Activa icono guardando
				$('#btnSubmit').attr('disabled','-1');
				$("#div_load").css("display", "inline");
				$("#div_error").css("display", "none");

				$.ajax({
					type: "POST",
					url: base_url + "inspection/save_heavy_inspection",
					data: $("#form").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,

					success: function(data){

						if( data.result == "error" )
						{
							//alert(data.mensaje);
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');

							$("#span_msj").html(data.mensaje);
							$("#div_msj").css("display", "inline");
							return false;
						}

						if( data.result )//true
						{
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');

							var url = base_url + "inspection/add_heavy_inspection/" + data.idHeavyInspection;
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
		else
		{
			alert('There are missing fields that have not been filled.');
		}
	});
});