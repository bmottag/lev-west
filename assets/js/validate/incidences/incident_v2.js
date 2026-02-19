$(document).ready(function () {
	$("#form").validate({
		rules: {
			jobName: { required: true },
			incidentType: { required: true },
			happened: { required: true },
			date: { required: true },
			hour: { required: true },
			min: { required: true },
			callManager: { required: true },
			cause: { required: true },
			uderlyingCauses: { required: true },
			instructionBefore: { required: true },
			correctiveActions: { required: true },
			preventativeAction: { required: true },
			manager: { required: true },
			coordinator: { required: true },
		},
		errorElement: "em",
		errorPlacement: function (error, element) {
			// Add the `help-block` class to the error element
			error.addClass("help-block");
			error.insertAfter(element);
		},
		highlight: function (element, errorClass, validClass) {
			$(element)
				.parents(".col-sm-5")
				.addClass("has-error")
				.removeClass("has-success");
		},
		unhighlight: function (element, errorClass, validClass) {
			$(element)
				.parents(".col-sm-5")
				.addClass("has-success")
				.removeClass("has-error");
		},
		submitHandler: function (form) {
			return true;
		},
	});

	$("#btnClose").click(function () {
		if (
			window.confirm("Are you sure you want to close this Near Miss report?")
		) {
			$.ajax({
				type: "POST",
				url: base_url + "incidences/update_incidence_state",
				data: $("#form").serialize(),
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				cache: false,

				success: function (data) {
					if (data.result == "error") {
						//alert(data.mensaje);
						$("#div_cargando").css("display", "none");
						$("#btnSubmit").removeAttr("disabled");

						$("#span_msj").html(data.mensaje);
						$("#div_msj").css("display", "inline");
						return false;
					}

					if (data.result) {
						//true
						$("#div_cargando").css("display", "none");
						$("#div_guardado").css("display", "inline");
						$("#btnSubmit").removeAttr("disabled");

						var url = base_url + "incidences/incident";
						$(location).attr("href", url);
					} else {
						alert("Error. Reload the web page.");
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$("#btnSubmit").removeAttr("disabled");
					}
				},
				error: function (result) {
					alert("Error. Reload the web page.");
					$("#div_cargando").css("display", "none");
					$("#div_error").css("display", "inline");
					$("#btnSubmit").removeAttr("disabled");
				},
			});
		}
	});

	$("#btnSubmit").click(function () {
		if ($("#form").valid() == true) {
			//Activa icono guardando
			$("#btnSubmit").attr("disabled", "-1");
			$("#div_guardado").css("display", "none");
			$("#div_error").css("display", "none");
			$("#div_msj").css("display", "none");
			$("#div_cargando").css("display", "inline");

			$.ajax({
				type: "POST",
				url: base_url + "incidences/save_incident",
				data: $("#form").serialize(),
				dataType: "json",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				cache: false,

				success: function (data) {
					if (data.result == "error") {
						//alert(data.mensaje);
						$("#div_cargando").css("display", "none");
						$("#btnSubmit").removeAttr("disabled");

						$("#span_msj").html(data.mensaje);
						$("#div_msj").css("display", "inline");
						return false;
					}

					if (data.result) {
						//true
						$("#div_cargando").css("display", "none");
						$("#div_guardado").css("display", "inline");
						$("#btnSubmit").removeAttr("disabled");

						var url = base_url + "incidences/add_incident/" + data.idJob + "/" + data.idRecord;

						console.log(url);
						$(location).attr("href", url);
					} else {
						alert("Error. Reload the web page.");
						$("#div_cargando").css("display", "none");
						$("#div_error").css("display", "inline");
						$("#btnSubmit").removeAttr("disabled");
					}
				},
				error: function (result) {
					alert("Error. Reload the web page.");
					$("#div_cargando").css("display", "none");
					$("#div_error").css("display", "inline");
					$("#btnSubmit").removeAttr("disabled");
				},
			});
		} //if
	});
});
