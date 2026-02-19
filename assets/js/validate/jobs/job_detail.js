$( document ).ready( function () {
	
	$( "#form" ).validate( {
		rules: {
			description:			{ required: true },
			unit: 					{ required: true },
			quantity: 				{ required: true },
			unit_price: 			{ required: true }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-6" ).addClass( "has-error" ).removeClass( "has-success" );
			$( element ).parents( ".col-sm-12" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-6" ).addClass( "has-success" ).removeClass( "has-error" );
			$( element ).parents( ".col-sm-12" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});
	
	$("#btnSave").click(function(){		
	
		if ($("#form").valid() == true){
		
				$("#chapter").prop("disabled", false);
				$("#chapter_number").prop("disabled", false);
				//Activa icono guardando
				$('#btnSave').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "jobs/save_job_detail",	
					data: $("#form").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,
					
					success: function(data){
                                            
						if( data.result == "error" )
						{
							$("#div_load").css("display", "none");
							$('#btnSave').removeAttr('disabled');							
							return false;
						} 

						if( data.result )//true
						{	                                                        
							$("#div_load").css("display", "none");
							$('#btnSave').removeAttr('disabled');

							var url = base_url + "jobs/job_detail/" + data.idRecord;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSave').removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSave').removeAttr('disabled');
					}
					
		
				});	
		
		}//if			
	});

	$(".btn-delete-job-detail").click(function () {	
		var oID = $(this).attr("id");
		
		//Activa icono guardando
		if(window.confirm('Are you sure you want to delete this record?'))
		{
				$(".btn-delete-job-detail").attr('disabled','-1');
				$.ajax ({
					type: 'POST',
					url: base_url + 'jobs/deleteRecordJobDetail',
					data: {'identificador': oID},
					cache: false,
					success: function(data){
											
						if( data.result == "error" )
						{
							alert(data.mensaje);
							$(".btn-delete-job-detail").removeAttr('disabled');							
							return false;
						} 
										
						if( data.result )//true
						{	                                                        
							$(".btn-delete-job-detail").removeAttr('disabled');

							var url = base_url + "jobs/job_detail/" + data.idRecord;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$(".btn-delete-job-detail").removeAttr('disabled');
						}	
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$(".btn-delete-job-detail").removeAttr('disabled');
					}

				});
		}
	});
});