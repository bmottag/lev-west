$( document ).ready( function () {
	
jQuery.validator.addMethod("validacion", function(value, element, param) {
	
	var start_date = $('#start_date').val();
	var start_hour = $('#start_hour').val();
	var start_min = $('#start_min').val();
	var finish_hour = $('#finish_hour').val();
	var finish_min = $('#finish_min').val();
	
	var hddfechaInicio = $('#hddfechaInicio').val();
	var hddhoraInicio = $('#hddhoraInicio').val();
	var hddminutosInicio = $('#hddminutosInicio').val();
	var hddfechaFin = $('#hddfechaFin').val();
	var hddhoraFin = $('#hddhoraFin').val();
	var hddminutosFin = $('#hddminutosFin').val();
	
	if (hddfechaInicio == start_date &&  hddhoraInicio == start_hour  &&  hddminutosInicio == start_min &&  hddhoraFin == finish_hour &&  hddminutosFin == finish_min) {
		return false;
	}else{
		return true;
	}
}, "One of the field have to be different.");
	
	$( "#formWorker" ).validate( {
		rules: {
			start_date:	 			{ required: true },
			start_hour:	 			{ required: true },
			start_min:	 			{ required: true },
			finish_hour:	 		{ required: true },
			finish_min:	 			{ required: true },
			observation:	 		{ required: true, validacion:true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-4" ).addClass( "has-error" ).removeClass( "has-success" );
			$( element ).parents( ".col-sm-12" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-4" ).addClass( "has-success" ).removeClass( "has-error" );
			$( element ).parents( ".col-sm-12" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});
	
	$("#btnSubmit").click(function(){		
	
		if ($("#formWorker").valid() == true){
		
				//Activa icono guardando
				$('#btnSubmitWorker').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "payroll/savePayrollHour",	
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

							var url = base_url + "dashboard/info_by_day/payrollInfo/" + data.datePayroll;
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