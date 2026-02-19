$( document ).ready( function () {

	// Método para validar formato YYYY-MM-DD
	$.validator.addMethod("dateISO", function(value, element) {
		return this.optional(element) || /^\d{4}-\d{2}-\d{2}$/.test(value);
	}, "Please enter a valid date (YYYY-MM-DD).");
			
	$( "#form" ).validate( {
		rules: {
			type				: {	required	:	true },
			observation			: {	required	:	true },
            date: { 
                required: true,
                dateISO: true // Aquí aplicamos la regla
            }
		},
        messages: {
            date: {
                required: "Please select a date",
                dateISO: "Invalid format. Use YYYY-MM-DD"
            }
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
					url: base_url + "dayoff/save_dayoff",	
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
							$("#div_error").css("display", "inline");
							$("#span_msj").html(data.mensaje);
							return false;
						
						} 
	
						if( data.result )//true
						{	                                                        
							$("#div_load").css("display", "none");
							
							//alert('Su solicitud se recibió correctamente.');
							$('#btnSubmit').removeAttr('disabled');   

							var url = base_url + "dayoff";
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$('#btnSubmit').removeAttr('disabled');
					}
					
		
				});	
		
		}//if			
	});

});