$( document ).ready( function () {
			
	$("#hours").bloquearTexto().maxlength(10);
	$( "#form" ).validate( {
		rules: {
			hours: 				{ number: true, minlength: 2, maxlength: 10 },
			belt:				{ required: true },
			fuelFilter:			{ required: true },
			oil: 				{ required: true },
			coolantLevel: 		{ required: true },
			coolantLeaks: 		{ required: true },
			turnSignal:			{ required: true },
			hazardLights:		{ required: true },			
			tailLights:			{ required: true },
			floodLights:		{ required: true },
			boom:				{ required: true },	
			gears:				{ required: true },
			gauges:				{ required: true },
			pulley:				{ required: true },
			electrical:			{ required: true },
			brackers:			{ required: true },	
			tires:				{ required: true },
			cleanExterior:		{ required: true },
			decals:				{ required: true }
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
					url: base_url + "inspection/save_generator_inspection",	
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

							var url = base_url + "inspection/add_generator_inspection/" + data.idGeneratorInspection;
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
		
		}//if			
		else
		{
			alert('There are missing fields that have not been filled.');
			
		}					
	});

});