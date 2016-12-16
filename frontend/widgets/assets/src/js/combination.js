/**
 * Created by xeinsteinx on 14.12.16.
 */
$(document).ready(function() {
    var productId = $('#productId').val();

    var addToCartButton = $('#add-to-cart-button');

    var priceTag = $('#price');
    var combinationsBlock = $('#combinations-values');
    var combinationBlockInputsNumber = $('#combinations-values div.form-group').length;

    combinationsBlock.change(function () {
        var checkedValues = $('#combinations-values input:checked');
        var values = [];
        var attributeId;
        for (var i = 0; i < checkedValues.length; i++) {
            attributeId = checkedValues[i].name;
            attributeId = attributeId.replace('CartForm[attribute_value_id][', '');
            attributeId = attributeId.replace(']', '');
            values[i] = checkedValues[i].value;

        }

        if (values.length == combinationBlockInputsNumber) {
            values = JSON.stringify(values);
            $.ajax({
                type: "GET",
                url: '/shop/product/get-product-combination',
                data: {
                    values: values,
                    productId: productId
                },

                success: function (data) {
                    data = JSON.parse(data);
                    if(data) $(addToCartButton).removeAttr('disabled');

                    var price = (data.price) ? data.price : priceTag.data('default-value');
                    priceTag.text(price);
                    if (data.image) $('#main-image').attr('src', data.image);
                    $('img.zoomImg').attr('src', data.image);
                },
                error: function (data) {
                    $(addToCartButton).attr('disabled','disabled');
                    priceTag.text('');
                }
            });
        }
    });

});