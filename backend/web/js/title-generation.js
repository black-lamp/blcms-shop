/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This script generates products seo-url from title.
 */

$(function () {
    $("button#generate-seo-url").click(function () {

        var title = $("#producttranslation-title").val();
        $.ajax({
            type: "GET",
            url: "/admin/shop/product/generate-seo-url",
            data: {'title':title},
            success: function (result) {
                $("#producttranslation-seourl").val(result);
            }
        });

    });
});




