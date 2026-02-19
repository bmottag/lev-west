$( document ).ready( function () {

    $('#type').change(function () {
		$("#loader").addClass("loader");
        $('#type option:selected').each(function () {
			var type = $('#type').val();
			var idAttachment = $('#hddId').val();
			loadEquipmentList(type,idAttachment);

        });
    });

	$(function() {
		$("#loader").addClass("loader");
		var type = $('#type').val();
		var idAttachment = $('#hddId').val();
		loadEquipmentList(type,idAttachment);
	})

	$("#contact").bloquearNumeros().maxlength(50);		
	$("#movilNumber").bloquearTexto().maxlength(10);
	
	$( "#form" ).validate( {
		rules: {
			attachment_number:				{ required: true, minlength: 3, maxlength:10 },
			attachment_description:			{ required: true, minlength: 3, maxlength:60 }
		},
		errorElement: "em",
		errorPlacement: function ( error, element ) {
			// Add the `help-block` class to the error element
			error.addClass( "help-block" );
			error.insertAfter( element );

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parents( ".col-sm-12" ).addClass( "has-error" ).removeClass( "has-success" );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parents( ".col-sm-12" ).addClass( "has-success" ).removeClass( "has-error" );
		},
		submitHandler: function (form) {
			return true;
		}
	});
	
	$("#btnSubmit").click(function(){		
	
		if ($("#form").valid() == true){
		
				//Activa icono guardando
				$('#btnSubmit').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");
			
				$.ajax({
					type: "POST",	
					url: base_url + "admin/save_attachments",	
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

							var url = base_url + "admin/attachments/active";
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
	});
});

/*
* Function to load Equipment List
*/
function loadEquipmentList(type,idAttachment) {
	if (type > 0 || type != '') {
		$.ajax ({
			type: 'POST',
			url: base_url + 'admin/equipmentList',
			data: {'type': type, idAttachment},
			cache: false,
			success: function (data)
			{
				$('#equipment').html(data);
			}
		});
		$("#div_equipment").css("display", "inline");
		$('#equipment').val("");
	} else {
		var data = '';
		$('#equipment').html(data);
	}
	$("#loader").removeClass("loader");
}