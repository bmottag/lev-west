/**
 * Link list by menu
 * @author bmottag
 * @since  1/4/2020
 */

$(document).ready(function () {
	   
    $('#id_menu').change(function () {
        $('#id_menu option:selected').each(function () {
            var idMenu = $('#id_menu').val();
            if (idMenu > 0 || idMenu != '') {
				$("#div_link").css("display", "inline");
				
                $.ajax ({
                    type: 'POST',
                    url: base_url + 'enlaces/linkListInfo',
                    data: {'idMenu': idMenu},
                    cache: false,
                    success: function (data)
                    {
                        $('#id_link').html(data);
                    }
                });
            } else {				
                var data = '';
				$("#div_link").css("display", "none");
                $('#id_link').html(data);
            }
        });
    });
    
});