$( document ).ready( function () {
			
	$( "#formVerify" ).validate( {
		rules: {
			login: 		{required:	true },
			password: 	{required:	true }
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
				
	$("#btnSubmitVerification").click(function(){		
	
		if ($("#formVerify").valid() == true){
		
				//Activa icono guardando
				$('#btnSubmitVerification').attr('disabled','-1');
				$("#div_error_message").css("display", "none");
				$("#div_load_message").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "safety/save_signature_credentials",	
					data: $("#formVerify").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							$("#div_load_message").css("display", "none");
							$("#div_error_message").css("display", "inline");
							$("#span_msj_error").html(data.mensaje);
							$('#btnSubmitVerification').removeAttr('disabled');							
							return false;
						} 

						if( data.result )//true
						{	                                                        
							$("#div_load_message").css("display", "none");
							$('#btnSubmitVerification').removeAttr('disabled');   

							var url = base_url + data.path;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load_message").css("display", "none");
							$("#div_error_message").css("display", "inline");
							$('#btnSubmitVerification').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load_message").css("display", "none");
						$("#div_error_message").css("display", "inline");
						$('#btnSubmitVerification').removeAttr('disabled');
					}
		
				});	
		
		}//if			
	});

});