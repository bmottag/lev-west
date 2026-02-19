$( document ).ready( function () {
				
	$( "#formState" ).validate( {
		rules: {
			state:					{ required: true },
			information:			{ required: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-8" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-8" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});
		
	$("#btnState").click(function(){		
	
		if ($("#formState").valid() == true){
		
				//Activa icono guardando
				$('#btnState').attr('disabled','-1');
			
				$.ajax({
					type: "POST",	
					url: base_url + "workorders/update_wo_state",	
					data: $("#formState").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							alert(data.mensaje);
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$("#span_msj").html(data.mensaje);
							$('#btnState').removeAttr('disabled');	
							return false;
						} 

						if( data.result )//true
						{	                                                        
							$("#div_cargando").css("display", "none");
							$("#div_guardado").css("display", "inline");
							$('#btnState').removeAttr('disabled');

							var url = base_url + "workorders/search/y";
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_cargando").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnState').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnState').removeAttr('disabled');
					}
					
		
				});	
		
		}//if			
	});



});