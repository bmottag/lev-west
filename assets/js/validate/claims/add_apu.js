$( document ).ready( function () {
			
	$("#btnSubmit").click(function(){
	
		//Activa icono guardando
		$('#btnSubmit').attr('disabled','-1');
		$("#div_error").css("display", "none");
		$("#div_load").css("display", "inline");
	
		$.ajax({
			type: "POST",	
			url: base_url + "claims/save_claim_apu",	
			data: $("#form").serialize(),
			dataType: "json",
			contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			cache: false,
			
			success: function(data){
									
				if( data.result == "error" )
				{
					$("#div_load").css("display", "none");
					$('#btnSubmit').removeAttr('disabled');							
					return false;
				} 
								
				if( data.result )//true
				{	                                                        
					$("#div_load").css("display", "none");
					$('#btnSubmit').removeAttr('disabled');

					var url = base_url + "claims/upload_apu/" + data.idRecord;
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
		
	});

});