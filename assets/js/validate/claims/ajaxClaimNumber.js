/**
 * Claim next number
 * @author bmottag
 * @since  13/05/2025
 */

$(document).ready(function () {
	$('#id_job').change(function () {
		let job = $(this).val();
	
		if (job > 0) {
			$.ajax({
				type: 'POST',
				url: base_url + 'claims/nextClaimNumber',
				data: { job: job },
				cache: false,
				success: function (response) {
					let data = JSON.parse(response);
					$('#claimNumber').val(data.next);

					if(data.lastObservation)
					{
						$('#span_observation').html(data.lastObservation);
						$("#div_observation").css("display", "block");
					} else {
						$("#div_observation").css("display", "none");
					}
				},
				error: function () {
					$('#claimNumber').val('Error');
				}
			});
		} else {
			$('#claimNumber').val('');
		}
	});
});