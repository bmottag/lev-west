/**
 * Stock quantity list by stock
 * @author bmottag
 * @since  25/3/2020
 */

$(document).ready(function () {
	   
    $('#id_stock').change(function () {
        $('#id_stock option:selected').each(function () {
            var idStock = $('#id_stock').val();
            if (idStock > 0 || idStock != '') {
				$("#div_stockQuantity").css("display", "inline");
				
                $.ajax ({
                    type: 'POST',
                    url: base_url + 'maintenance/quantityInfo',
                    data: {'idStock': idStock},
                    cache: false,
                    success: function (data)
                    {
                        $('#stockQuantity').html(data);
                    }
                });
            } else {				
                var data = '';
				$("#div_stockQuantity").css("display", "none");
                $('#stockQuantity').html(data);
            }
        });
    });
    
});