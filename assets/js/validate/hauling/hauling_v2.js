$(document).ready(function () {
	var state = $("#state").val();

	if (state && state != 1) {
		$("form :input").prop('disabled', true);
		$("#btnSubmit").prop('disabled', true);
		$("#btnEmail").prop('disabled', true);
		$('.class_disabled').attr('disabled', 'true');
		$('.class_disabled').removeAttr("href");
	}

	$( "#form" ).validate( {
		rules: {
			company:			{ required: true },
			truckType: 			{ required: true },
			materialType: 		{ required: true },
			fromSite:	 		{ required: true },
			toSite:				{ required: true },
			hourIn:				{ required: true },
			hourOut:			{ required: true },
			payment:			{ required: true },
			plate: { minlength: 3, maxlength: 15 },
			list_work_order: {
				required: function() {
					return $("#id_work_order").val() == "2";
				}
			}
		},
		errorElement: "em",
		errorPlacement: function (error, element) {
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

		if ($("#form").valid() == true) {

			$("#fromSite").prop("disabled", false);
			//Activa icono guardando
			$('#btnSubmit').attr('disabled','-1');
			$("#div_guardado").css("display", "none");
			$("#div_error").css("display", "none");
			$("#div_msj").css("display", "none");
			$("#div_cargando").css("display", "inline");


			$.ajax({
				type: "POST",
				url: base_url + "hauling/save_hauling",
				data: $("#form").serialize(),
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				cache: false,

				success: function(data){

					if( data.result == "error" )
					{
						//alert(data.mensaje);
						$("#div_cargando").css("display", "none");
						$('#btnSubmit').removeAttr('disabled');
						$("#div_error").css("display", "inline");
						$("#span_msj").html(data.mensaje);
						return false;
					}

					if( data.result )//true
					{
						$("#div_cargando").css("display", "none");
						$("#div_guardado").css("display", "inline");
						$('#btnSubmit').removeAttr('disabled');

						var url = base_url + "hauling/add_hauling/" + data.idHauling;
						$(location).attr("href", url);
					}
					else
					{
						alert('Error. Reload the web page.');
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmit').removeAttr('disabled');
					}
				},
				error: function(result) {
					alert('Error. Reload the web page.');
					$("#div_cargando").css("display", "none");
					$("#div_error").css("display", "inline");
					$('#btnSubmit').removeAttr('disabled');
				}
			});
		}
	});


});