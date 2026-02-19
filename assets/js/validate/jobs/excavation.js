$( document ).ready( function () {

jQuery.validator.addMethod("fieldTestedExplanation", function(value, element, param) {
	var tested_daily = $('#tested_daily').val();
	if(tested_daily==1 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");

jQuery.validator.addMethod("fieldVentilationExplanation", function(value, element, param) {
	var ventilation = $('#ventilation').val();
	if(ventilation==1 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");
			
	$( "#form" ).validate( {
		rules: {
			project_location:			{ required: true },
			depth:						{ required: true, minlength: 1, maxlength:2 },
			width:						{ required: true, minlength: 1, maxlength:2 },
			length:						{ required: true, minlength: 1, maxlength:3 },
			confined_space:				{ required: true },
			tested_daily:				{ required: true },
			tested_daily_explanation:	{ fieldTestedExplanation: "#tested_daily" },
			ventilation:				{ required: true },
			ventilation_explanation:	{ fieldVentilationExplanation: "#ventilation" },
			soil_classification:		{ required: true },
			soil_type:					{ required: true }
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
				$("#div_guardado").css("display", "none");
				$("#div_error").css("display", "none");
				$("#div_msj").css("display", "none");
				$("#div_cargando").css("display", "inline");

				$.ajax({
					type: "POST",	
					url: base_url + "jobs/save_excavation",	
					data: $("#form").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							//alert(data.mensaje);
							$("#div_cargando").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');							
							
							$("#span_msj").html(data.mensaje);
							$("#div_msj").css("display", "inline");
							return false;
						
						} 

						if( data.result )//true
						{	                                                        
							$("#div_cargando").css("display", "none");
							$("#div_guardado").css("display", "inline");
							$('#btnSubmit').removeAttr('disabled');

							var url = base_url + "jobs/upload_excavation_personnel/" + data.idExcavation;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_cargando").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSubmit').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmit').removeAttr('disabled');
					}
					
		
				});	
		
		}else{
			alert("There are missing fields.");
		}			
	});

});