$( document ).ready( function () {

	$("#hours").bloquearTexto().maxlength(10);
	$( "#form" ).validate( {
		rules: {
			hours: 				{ number: true, minlength: 2, maxlength: 10 },
			belt:				{ required: true },
			powerSteering:		{ required: true },
			oil: 				{ required: true },
			coolantLevel: 		{ required: true },
			waterLeaks:			{ required: true },
			headLamps:			{ required: true },
			hazardLights:		{ required: true },
			bakeLights:			{ required: true },
			workLights:			{ required: true },
			turnSignals:		{ required: true },
			beaconLight:		{ required: true },
			clearanceLights:	{ required: true },
			brakePedal:			{ required: true },
			emergencyBrake:		{ required: true },
			gauges:				{ required: true },
			horn:				{ required: true },
			seatbelts:			{ required: true },
			driverSeat:			{ required: true },
			insurance:			{ required: true },
			registration:		{ required: true },
			cleanInterior:		{ required: true },
			nuts:				{ required: true },
			glass:				{ required: true },
			cleanExterior:		{ required: true },
			wipers:				{ required: true },
			backupBeeper:		{ required: true },
			passengerDoor:		{ required: true },
			properDecals:		{ required: true },
			fireExtinguisher:	{ required: true },
			firstAid:			{ required: true },
			emergencyReflectors:{ required: true },
			spillKit:			{ required: true },
			steeringAxle:		{ required: true },
			drivesAxle:			{ required: true },
			greaseFront:		{ required: true },
			greaseEnd:			{ required: true },
			grease:				{ required: true },
			hoist:				{ required: true },
			heater:				{ required: true },
			steering_wheel:		{ required: true },
			suspension_system:	{ required: true },
			air_brake:			{ required: true },
			fuel_system:        { required: true },
			def:			    { required: true }
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
					url: base_url + "inspection/save_daily_inspection",
					data: $("#form").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,

					success: function(data){

						if( data.result == "error" )
						{
							alert(data.mensaje);
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');

							$("#span_msj").html(data.mensaje);
							$("#div_error").css("display", "inline");
							return false;
						}

						if( data.result )//true
						{
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');

							var url = base_url + "inspection/add_daily_inspection/" + data.idDailyInspection;
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