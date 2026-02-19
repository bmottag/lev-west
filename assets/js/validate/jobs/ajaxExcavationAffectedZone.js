/**
 * Excavations filds
 * @author bmottag
 * @since  8/8/2021
 */

$(document).ready(function () {
	
    $('#utility_lines').change(function () {
        $('#utility_lines option:selected').each(function () {
            var utility_lines = $('#utility_lines').val();

            if ((utility_lines > 0 || utility_lines != '') ) {
				$("#div_utility_lines_explain").css("display", "none");
                $('#utility_lines_explain').val("");
				if(utility_lines==1){
					$("#div_utility_lines_explain").css("display", "inline");
				}
            }
        });
    });

    $('#encumbrances').change(function () {
        $('#encumbrances option:selected').each(function () {
            var encumbrances = $('#encumbrances').val();

            if ((encumbrances > 0 || encumbrances != '') ) {
                $("#div_method_support").css("display", "none");
                $('#method_support').val("");
                if(encumbrances==1){
                    $("#div_method_support").css("display", "inline");
                }
            }
        });
    });

    $('#spoil_piles').change(function () {
        $('#spoil_piles option:selected').each(function () {
            var spoil_piles = $('#spoil_piles').val();

            if ((spoil_piles > 0 || spoil_piles != '') ) {
                $("#div_spoils_transported").css("display", "none");
                $("#div_environmental_controls").css("display", "none");
                $('#spoils_transported').val("");
                $('#environmental_controls').val("");
                if(spoil_piles==2){
                    $("#div_spoils_transported").css("display", "inline");
                    $("#div_environmental_controls").css("display", "none");
                }else if(spoil_piles==1){
                    $("#div_spoils_transported").css("display", "none");
                    $("#div_environmental_controls").css("display", "inline");
                }
            }
        });
    });

    $('#open_overnight').change(function () {
        $('#open_overnight option:selected').each(function () {
            var open_overnight = $('#open_overnight').val();

            if ((open_overnight > 0 || open_overnight != '') ) {
                $("#div_methods_secure").css("display", "none");
                $('#methods_secure').val("");
                if(open_overnight==1){
                    $("#div_methods_secure").css("display", "inline");
                }
            }
        });
    });
    

    
});