$(document).ready(function () {

    /*OPEN*/
    $('#widget-menu').on('click', '.category-toggle.fa-toggle-down', (function () {
        var element = this;

        var id = element.id;
        var aHeight = $($(element).parent().children('a')).height();

        var ul = $(element).parent().parent();

        var level = $(ul).attr("data-level");

        $.ajax({
            type: "GET",
            url: '/shop/category/get-categories',
            data: 'parentId=' + id + '&level=' + level,

            success: function (data) {
                $(element).removeClass('fa-toggle-down');
                $(element).addClass('fa-toggle-up');

                $(data).height($(data).children().length * aHeight * .7).slideDown(300).insertAfter($('#' + id));
            }
        });

        ul.attr('style', '');

    /*CLOSE*/
    })).on('click', '.category-toggle.fa-toggle-up', (function () {
        var element = this;

        $(element).removeClass('fa-toggle-up');
        $(element).addClass('fa-toggle-down');

        var ul = $(element).nextAll();
        $(ul).slideUp(300);

        setTimeout(function() {
            $(element).nextAll().remove()
        }, 300);

    }));


});