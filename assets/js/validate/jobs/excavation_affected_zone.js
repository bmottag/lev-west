$( document ).ready( function () {

jQuery.validator.addMethod("fieldUtilityExplanation", function(value, element, param) {
	var utility_lines = $('#utility_lines').val();
	if(utility_lines==1 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");

jQuery.validator.addMethod("fieldMethod", function(value, element, param) {
	var encumbrances = $('#encumbrances').val();
	if(encumbrances==1 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");

jQuery.validator.addMethod("fieldSpoilsTransported", function(value, element, param) {
	var spoil_piles = $('#spoil_piles').val();
	if(spoil_piles==2 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");

jQuery.validator.addMethod("fieldEnvironmental", function(value, element, param) {
	var spoil_piles = $('#spoil_piles').val();
	if(spoil_piles==1 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");

jQuery.validator.addMethod("fieldMethodsSecure", function(value, element, param) {
	var open_overnight = $('#open_overnight').val();
	if(open_overnight==1 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");
			
	$( "#form" ).validate( {
		rules: {
			located:				{ required: true },
			permit_required:		{ required: true },
			utility_lines:			{ required: true },
			utility_lines_explain:	{ fieldUtilityExplanation: "#utility_lines" },
			encumbrances:			{ required: true },
			method_support:			{ fieldMethod: "#encumbrances" },
			utility_shutdown:		{ required: true },
			spoil_piles:			{ required: true },
			spoils_transported:		{ fieldSpoilsTransported: "#spoil_piles" },
			environmental_controls:	{ fieldEnvironmental: "#spoil_piles" },
			open_overnight:			{ required: true },
			methods_secure:			{ fieldMethodsSecure: "#open_overnight" },
			vehicle_traffic:		{ required: true }
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
					url: base_url + "jobs/save_affected_zone",	
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

							var url = base_url + "jobs/upload_affected_zone/" + data.idExcavation;
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