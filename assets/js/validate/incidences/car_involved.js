$( document ).ready( function () {

	
	$( "#formInvolved" ).validate( {
		rules: {
			make: 			{ required: true, maxlength:30 },
			model:	 		{ required: true, minlength:2 , maxlength:30 },
			type:	 		{ minlength:2 , maxlength:30 },
			insurance:	 	{ required: true, minlength:2 , maxlength:30 },
			owner:	 		{ required: true, minlength:2 , maxlength:60 },
			driver:	 		{ required: true, minlength:2 , maxlength:60 },
			license:	 	{ required: true, minlength:2 , maxlength:20 },
			company:	 	{ minlength:2 , maxlength:60 },
			plate:	 		{ required: true, minlength:3 , maxlength:20 }
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
	
	$("#btnSubmitInvolved").click(function(){		
	
		if ($("#formInvolved").valid() == true){
		
				//Activa icono guardando
				$('#btnSubmitInvolved').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "incidences/save/saveInvolved",	
					data: $("#formInvolved").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							$("#div_load").css("display", "none");
							$('#btnSubmitInvolved').removeAttr('disabled');							
							return false;
						} 

						if( data.result )//true
						{	                                                        
							$("#div_load").css("display", "none");
							$('#btnSubmitInvolved').removeAttr('disabled');

							var url = base_url + "incidences/add_accident/" + data.idRecord;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSubmitInvolved').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmitInvolved').removeAttr('disabled');
					}
					
		
				});	
		
		}//if			
	});
});