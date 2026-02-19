$( document ).ready( function () {

jQuery.validator.addMethod("campoNit", function(value, element, param) {
	var license = $('#license').val();
	if ( license == 1 && value == "" ) {
		return false;
	}else{
		return true;
	}
}, "This field is required.");


	
	$( "#formWorker" ).validate( {
		rules: {
			name:	 				{ required: true, minlength:2, maxlength:100 },
			phone_number:	 		{ required: true, minlength:2, maxlength:20 }
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
	
	$("#btnSubmitWorker").click(function(){		
	
		if ($("#formWorker").valid() == true){
		
				//Activa icono guardando
				$('#btnSubmitWorker').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "jobs/saveJSOWorker",	
					data: $("#formWorker").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
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

							var url = base_url + "jobs/jso_worker_view/" + data.idRecordExternal;
							$(location).attr("href", url);
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
		
		}//if			
	});
});