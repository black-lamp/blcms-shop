$(document).ready(function () {

    var widget = $('.product-prices-widget');
    var notAvailableText = $(widget).data('not-available-text');
    var combinationsBlock = $('.combinations-values');

    combinationsBlock.change(function () {
        var thisCombinationBlock = this;
        var thisWidget = $(this).closest('.product-prices-widget');
        var productId = $(thisCombinationBlock).data('product-id');

        var combinationBlockInputsNumber = $(thisCombinationBlock).find('div.form-group').length;

        var addToCartButton = $(thisCombinationBlock).closest('.product-prices-widget').find('#add-to-cart-button');
        var oldPriceTag = $(thisCombinationBlock).find('#oldPrice');
        var newPriceTag = $(thisCombinationBlock).find('#newPrice');
        var currencyCode = $(newPriceTag).data('currency-code');
        var productImage = $('#main-image');

        var checkedValues = $(thisCombinationBlock).find('input:checked');
        var selectedValues = $(thisCombinationBlock).find('option:selected');

        var checkedValuesLables = checkedValues.parent('label');
        var values = [];
        for (var i = 0; i < checkedValues.length; i++) {
            values[i] = checkedValues[i].value;
        }
        for (var j = 0; j < selectedValues.length; j++) {
            values[checkedValues.length + j] = $(selectedValues[j]).val();
        }

        /**
         * Adds "active" class for selected element.
         */
        $(thisCombinationBlock).find('.active').removeClass('active');
        checkedValuesLables.addClass('active');

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
                    if (data) $(addToCartButton).removeAttr('disabled');
                    else $(addToCartButton).attr('disabled', 'disabled');

                    var oldPrice = (data.oldPrice) ? data.oldPrice.toLocaleString() + ' ' + currencyCode : '';
                    if (data.newPrice) {
                        var newPrice = data.newPrice.toLocaleString() + ' ' + currencyCode;
                        var dataSum = data.newPrice;
                    }
                    else {
                        newPrice = dataSum = '';
                    }

                    if (!data.oldPrice && !data.newPrice) {
                        newPrice = notAvailableText;
                        $(thisWidget).find('button[type="submit"]').prop('disabled', true);
                    }
                    else {
                        $(thisWidget).find('button[type="submit"]').prop('disabled', false);
                    }

                    oldPriceTag.text(oldPrice);
                    newPriceTag.fadeOut(125).text(newPrice).fadeIn(125);
                    newPriceTag.attr('data-sum', dataSum);

                    if (data.image) productImage.fadeOut(125).attr('src', data.image).fadeIn(125);
                    $('img.zoomImg').attr('src', data.image);

                    var skuText = (data.sku) ? data.sku : notAvailableText;
                    $('#sku').text(skuText);
                },
                error: function (data) {
                    $(addToCartButton).attr('disabled', 'disabled');
                    oldPriceTag.text();
                    newPriceTag.text(notAvailableText);
                }
            });
        }
    });
});