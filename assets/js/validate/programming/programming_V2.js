$(document).ready(function () {

	$('.js-example-basic-single').select2();
	$(".date_range").css("display", "none");

	jQuery.validator.addMethod("fieldValidationDate", function(value, element, param) {
		var flag = $('#flag_date').val();
		if(flag == 1 && value == ""){
			return false;
		}else{
			return true;
		}
	}, "This field is required.");

	jQuery.validator.addMethod("fieldValidationPeriod", function(value, element, param) {
		var flag = $('#flag_date').val();
		if(flag == 2 && value == ""){
			return false;
		}else{
			return true;
		}
	}, "This field is required.");

	$('#jobName').change(function () {
		var planning = $('#jobName option:selected').data('planning');

		if (planning == 1) {
			$(".date_range").css("display", "block");
			$('#job_planning').val(1);
		}else{
			$(".date_range").css("display", "none");
			$('#job_planning').val(2);
			$('#flag_date').val(1);
			$('.period-fields').css("display", "none");
			$('.date-fields').css("display", "block");
		}
    });

	$('#flag_date').change(function () {
        $('#flag_date option:selected').each(function () {

			var flag = $('#flag_date').val();
			if (flag == 1) {
				$(".period-fields").css("display", "none");
				$(".date-fields").css("display", "block");

				$('#from').val("");
				$('#to').val("");
			}else{
				$(".period-fields").css("display", "block");
				$(".date-fields").css("display", "none");
				$('#date').val("");
			}
        });
    });

	$( "#form" ).validate( {
		rules: {
			date:					{ fieldValidationDate: true },
			from:					{ fieldValidationPeriod: true },
			to:						{ fieldValidationPeriod: true },
			apply_for:				{ fieldValidationPeriod: true },
			jobName: 				{ required: true },
			observation: 			{ required: true }
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

	$(".btn-delete-programming").click(function () {
			var oID = $(this).attr("id");

			//Activa icono guardando
			if(window.confirm('Are you sure you want to delete the Planning ?'))
			{
					$(".btn-delete-programming").attr('disabled','-1');
					$.ajax ({
						type: 'POST',
						url: base_url + 'programming/delete_programming',
						data: {'identificador': oID},
						cache: false,
						success: function(data){

							if( data.result == "error" )
							{
								alert(data.mensaje);
								$(".btn-delete-programming").removeAttr('disabled');
								return false;
							}

							if( data.result )//true
							{
								$(".btn-delete-programming").removeAttr('disabled');
								var url = base_url + "programming/index/" + data.path;
								$(location).attr("href", url);
							}
							else
							{
								alert('Error. Reload the web page.');
								$(".btn-delete-programming").removeAttr('disabled');
							}
						},
						error: function(result) {
							alert('Error. Reload the web page.');
							$(".btn-delete-programmingr").removeAttr('disabled');
						}

					});
			}
	});

	$("#btnSubmit").click(function(){

		if ($("#form").valid() == true){

				$("#jobName").prop("disabled", false);
				//Activa icono guardando
				$('#btnSubmit').attr('disabled','-1');
				$("#div_error").css("display", "none");
				$("#div_load").css("display", "inline");

				$.ajax({
					type: "POST",
					url: base_url + "programming/save_programming",
					data: $("#form").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,

					success: function(data){

						if( data.result == "error" )
						{
							$("#div_load").css("display", "none");
							$("#div_error").css("display", "inline");
							$("#span_msj").html(data.mensaje);
							alert(data.mensaje);
							$('#btnSubmit').removeAttr('disabled');
							return false;
						}

						if( data.result )//true
						{
							$("#div_load").css("display", "none");
							$('#btnSubmit').removeAttr('disabled');

							var url = base_url + "programming/index/" + data.path;
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

	$("#btnSubmitClone").click(function(){

		if ($("#clonePlanning").valid() == true){

				//Activa icono guardando
				$('#btnSubmitClone').attr('disabled','-1');
				$("#loader").addClass("loader");

				$.ajax({
					type: "POST",
					url: base_url + "programming/clone_planning",
					data: $("#clonePlanning").serialize(),
					dataType: "json",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					cache: false,

					success: function(data){
						$("#loader").removeClass("loader");
						if( data.result == "error" )
						{
							$("#div_error").css("display", "block");
							$('#btnSubmitClone').removeAttr('disabled');
							return false;
						}

						if( data.result )//true
						{
							$("#div_guardado").css("display", "block");
							$('#btnSubmitClone').removeAttr('disabled');
						}
						else
						{
							alert('Error. Reload the web page.');
							$("#div_cargando").css("display", "none");
							$("#div_error").css("display", "inline");
							$('#btnSubmitClone').removeAttr('disabled');
						}
					},
					error: function(result) {
						alert('Error. Reload the web page.');
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$('#btnSubmitClone').removeAttr('disabled');
					}
				});
		}
	});

});