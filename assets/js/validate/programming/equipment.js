$( document ).ready( function () {

jQuery.validator.addMethod("equipmentValidation", function(value, element, param) {
	var type = $('#type').val();
	if(type != 8 && value == ""){
		return false;
	}else{
		return true;
	}
}, "This field is required.");	

	$( "#formEquipment" ).validate( {
		rules: {
			type: 				{ required: true },
			truck: 				{ equipmentValidation: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-12" ).addClass( "has-error" ).removeClass( "has-success" );
			$( element ).parents( ".col-sm-6" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-12" ).addClass( "has-success" ).removeClass( "has-error" );
			$( element ).parents( ".col-sm-6" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});
	
	$("#btnSubmitEquipment").click(function(){		
	
		if ($("#formEquipment").valid() == true){
		
				//Activa icono guardando
				$('#btnSubmitEquipment').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "programming/save_equipment",	
					data: $("#formEquipment").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							$("#div_load").css("display", "none");
							$('#btnSubmitEquipment').removeAttr('disabled');							
							return false;
						} 

						if( data.result )//true
						{	                                                        
							$("#div_load").css("display", "none");
							$('#btnSubmitEquipment').removeAttr('disabled');

							var url = base_url + "programming/" + data.controlador + "/" + data.path;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSubmitEquipment').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmitEquipment').removeAttr('disabled');
					}
					
		
				});	
		
		}//if			
	});
});