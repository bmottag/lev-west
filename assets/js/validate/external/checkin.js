$( document ).ready( function () {

	jQuery.validator.addMethod("validation", function(value, element, param) {
		var login_before = $('#login_before').val();
		var id_name = $('#id_name').val();
		var new_name = $('#new_name').val();
		var new_phone_number = $('#new_phone_number').val();
		if ( login_before == 1 && id_name == "" ) {
			return false;
		}else{
			return true;
		}
	}, "This field is required.");

	jQuery.validator.addMethod("validation2", function(value, element, param) {
		var login_before = $('#login_before').val();
		var id_name = $('#id_name').val();
		var new_name = $('#new_name').val();
		var new_phone_number = $('#new_phone_number').val();
		if( login_before == 2 && new_name == "") {
			return false;
		}else{
			return true;
		}
	}, "This field is required.");

	jQuery.validator.addMethod("validation3", function(value, element, param) {
		var login_before = $('#login_before').val();
		var id_name = $('#id_name').val();
		var new_name = $('#new_name').val();
		var new_phone_number = $('#new_phone_number').val();
		if( login_before == 2 && new_phone_number == "") {
			return false;
		}else{
			return true;
		}
	}, "This field is required.");
			
	$("#hours").bloquearTexto().maxlength(10);
	$( "#form" ).validate( {
		rules: {
			login_before: 			{ required: true },
			id_name: 				{ validation: true},
			new_name: 				{ validation2: true},
			new_phone_number: 		{ validation3: true}
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
							url: base_url + "external/save_checkin",	
							data: $("#form").serialize(),
							dataType: "json",
							contentType: "application/x-www-form-urlencoded;charset=UTF-8",
							cache: false,
							
							success: function(data){
		                                console.log(data.result);            
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

									var url = base_url + "external/checkin/" + data.idCheckin;
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
					alert('Faltan campos por diligenciar.');
					
				}

	});

});