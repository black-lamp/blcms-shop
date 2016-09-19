/*This script removes empty get params*/
$('button').click(function(e) {
    e.preventDefault();
    $('input').each(function(){
        if ($(this).val() == '')
            $(this).attr('disabled', true);
    });
    $('option').each(function(){
        if ($(this).val() == '')
            $(this).attr('disabled', true);
    });
    $('form').submit();
});