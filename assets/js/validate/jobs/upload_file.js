$(function() {
    $('#btnSubir').click(function(event) {
        event.preventDefault();
        $('#btnSubir').addClass('disabled');
        $('#animationload').fadeIn();
        $('#formCargue').submit();
    });
});