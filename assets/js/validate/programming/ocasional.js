$(document).ready(function () {
	$('#divHauling').hide();
	$( "#formOcasional" ).validate( {
		rules: {
			company: 			{ required: true },
			equipment: 			{ required: true },
			quantity: 			{ number: true, maxlength:10 },
			unit:	 			{ minlength:2 , maxlength:20 },
			hour: 				{ number: true, maxlength:10 }
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

	$("#btnSubmitOcasional").click(function(){

		if ($("#formOcasional").valid() == true){

				//Activa icono guardando
				$('#btnSubmitOcasional').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");

				$.ajax({
					type: "POST",
					url: base_url + "programming/save_ocasional",
					data: $("#formOcasional").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,

					success: function(data){

						if( data.result == "error" )
						{
							$("#div_load").css("display", "none");
							$('#btnSubmitOcasional').removeAttr('disabled');
							return false;
						}

						if( data.result )//true
						{
							$("#div_load").css("display", "none");
							$('#btnSubmitOcasional').removeAttr('disabled');

							var url = base_url + "programming/" + data.controller + "/" + data.path;
							$(location).attr("href", url);
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSubmitOcasional').removeAttr('disabled');
						}
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_load").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmitOcasional').removeAttr('disabled');
					}
				});
		}
	});

	$('#companySelect').change(function () {
		var company = $(this).find('option:selected');
		var companyValue = company.val();
		var dataHauling = company.data('hauling');

		if (dataHauling == 1) {
			$('#divHauling').show();
			$('#quantity').attr('required', true);
		} else {
			$('#divHauling').hide();
			$('#quantity').removeAttr('required');
		}
    });
});