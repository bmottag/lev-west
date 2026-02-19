$( document ).ready( function () {
	
	$("#btnSubmitExpense").click(function(){		
	
			//Activa icono guardando
			$('#btnSubmitExpense').attr('disabled','-1');
			$("#div_error").css("display", "none");
			$("#div_load").css("display", "inline");
		
			$.ajax({
				type: "POST",	
				url: base_url + "workorders/save/saveExpense",	
				data: $("#formExpense").serialize(),
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				cache: false,
				
				success: function(data){
										
					if( data.result == "error" )
					{
						$("#div_load").css("display", "none");
						$('#btnSubmitExpense').removeAttr('disabled');							
						return false;
					} 

					if( data.result )//true
					{	                                                        
						$("#div_load").css("display", "none");
						$('#btnSubmitExpense').removeAttr('disabled');

						var url = base_url + "workorders/" + data.controlador + "/" + data.idRecord;
						$(location).attr("href", url);
					}
					else
					{
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmitExpense').removeAttr('disabled');
					}	
				},
				error: function(result) {
					alert('Error. Reload the web page.');
					$("#div_load").css("display", "none");
					$("#div_error").css("display", "inline");
					$('#btnSubmitExpense').removeAttr('disabled');
				}
				
	
			});			
	});
});