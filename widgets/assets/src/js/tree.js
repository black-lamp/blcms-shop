$(document).ready(function () {

    /*FIND WIDGET*/
    var widget = $('#widget-menu');
    var currentCategoryId = widget.attr('data-current-category-id');

    /*FIND TREE ITEMS WHICH MUST BE OPENED USING DATA ATTRIBUTE AND OPEN ITS*/
    var openedTreeItem = $('[data-opened=true]');
    autoOpen(openedTreeItem, currentCategoryId);


    /*OPEN ON CLICK*/
    widget.on('click', '.category-toggle.glyphicon-plus', (function () {
        var element = this;
        var id = element.id;
        var ul = $(element).parent().parent();
        var level = $(ul).attr("data-level");

        $.ajax({
            type: "GET",
            url: '/shop/category/get-categories',
            data: 'parentId=' + id + '&level=' + level + '&currentCategoryId=' + currentCategoryId,

            success: function (data) {
                $(element).removeClass('glyphicon-plus');
                $(element).addClass('glyphicon-minus');

                $(data).height('100%').slideDown(300).insertAfter($('#' + id));
            }
        });

        ul.attr('style', '');
    }));

    /*CLOSE ON CLICK*/
    widget.on('click', '.category-toggle.glyphicon-minus', (function () {
        var element = this;

        $(element).removeClass('glyphicon-minus');
        $(element).addClass('glyphicon-plus');

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

            $(this).removeClass('glyphicon-plus');
            $(this).addClass('glyphicon-minus');

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