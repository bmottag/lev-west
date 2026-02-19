$( document ).ready( function () {

    $('#id_shop').change(function () {
        $('#id_shop option:selected').each(function () {
            var id_shop = $('#id_shop').val();

            if ( id_shop == '') {
                $("#div_shop_detail").css("display", "block");
            }else{
				$("#div_shop_detail").css("display", "none");
			}
        });
    });

	jQuery.validator.addMethod("fieldSpecify", function(value, element, param) {
		var id_shop = $('#id_shop').val();
		var shop_name = $('#shop_name').val();
		if(shop_name=="" && id_shop == ""){
			return false;
		}else{
			return true;
		}
	}, "Select the Shop or Add a new one.");

	$("#shop_contact").bloquearNumeros().maxlength(50);
	$("#mobile_number").bloquearTexto().maxlength(10);

	$( "#form" ).validate( {
		rules: {
			id_shop: 						{ fieldSpecify: true },
			shop_name: 						{ fieldSpecify: true, minlength: 3, maxlength:60 },
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

	$("#btnSubmit").click(function(){

		if ($("#form").valid() == true){

				//Activa icono guardando
				$('#btnSubmit').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");

				$.ajax({
					type: "POST",
					url: base_url + "admin/save_shop_materials",
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

							var url = base_url + "admin/material";
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
		}
	});
});
