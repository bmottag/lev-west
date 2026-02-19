$( document ).ready( function () {
					
	$("#btnSubmit").click(function(){			
		//Activa icono guardando
		$('#btnSubmit').attr('disabled','-1');
		$("#div_load").css("display", "inline");
		$("#div_error").css("display", "none");
	
		$.ajax({
			type: "POST",	
			url: base_url + "jobs/save_fire_watch_checkin",	
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

					var url = base_url + "jobs/fire_watch_checkin/" + data.idFireWatch;
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