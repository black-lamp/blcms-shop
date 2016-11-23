$(document).ready(function () {

    /*FIND WIDGET*/
    var widget = $('#widget-menu');
    var currentCategoryId = widget.attr('data-current-category-id');

    /*FIND TREE ITEMS WHICH MUST BE OPENED USING DATA ATTRIBUTE AND OPEN ITS*/
    var openedTreeItem = $('[data-opened=true]');
    autoOpen(openedTreeItem, currentCategoryId);


    /*OPEN ON CLICK*/
    widget.on('click', '.category-toggle.glyphicon-chevron-down', (function () {
        var element = this;
        var id = element.id;
        var aHeight = $($(element).parent().children('a')).height(); // This is 'a' tag height
        var ul = $(element).parent().parent();
        var level = $(ul).attr("data-level");

        $.ajax({
            type: "GET",
            url: '/shop/category/get-categories',
            data: 'parentId=' + id + '&level=' + level + '&currentCategoryId=' + currentCategoryId,

            success: function (data) {
                $(element).removeClass('glyphicon-chevron-down');
                $(element).addClass('glyphicon-chevron-up');

                $(data).height($(data).children().length * aHeight * .7).slideDown(300).insertAfter($('#' + id));
            }
        });

        ul.attr('style', '');
    }));

    /*CLOSE ON CLICK*/
    widget.on('click', '.category-toggle.glyphicon-chevron-up', (function () {
        var element = this;

        $(element).removeClass('glyphicon-chevron-up');
        $(element).addClass('glyphicon-chevron-down');

        var ul = $(element).nextAll();
        $(ul).slideUp(300);

        setTimeout(function() {
            $(element).nextAll().remove()
        }, 300);
    }));
});


function autoOpen(openedTreeItem, currentCategoryId) {
    if (openedTreeItem) {
        openedTreeItem.each(function() {
            var id = this.id;
            var ul = $(this).parent().parent();
            var level = $(ul).attr("data-level");

            $(this).removeClass('glyphicon-chevron-down');
            $(this).addClass('glyphicon-chevron-up');

            $.ajax({
                type: "GET",
                url: '/shop/category/get-categories',
                data: 'parentId=' + id + '&level=' + level + '&currentCategoryId=' + currentCategoryId,

                success: function (data) {

                    $(data).slideDown(300).insertAfter($('#' + id));
                    level++;
                    autoOpen($('[data-level=' + level + '] ' + '[data-opened=true]'), currentCategoryId);
                }
            });
        });
    }
}