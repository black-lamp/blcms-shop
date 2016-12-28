$(document).ready(function() {

    var pricesBlock = $('div.prices-block');

    pricesBlock.change(function () {
        var thisPricesBlock = this;
        var checkedPriceId = $(thisPricesBlock).find('input:checked').val();
        var checkedInputLabel = $(thisPricesBlock).find('input:checked').parent('label');

        /**
         * Adds "active" class for selected element.
         */
        $(thisPricesBlock).find('.active').removeClass('active');
        checkedInputLabel.addClass('active');

        /**
         * Gets price and sale price and inserts its.
         */
        $.ajax({
            type: "GET",
            url: '/shop/product/get-product-price',
            data: {
                priceId: checkedPriceId
            },

            success: function (data) {
                var prices = JSON.parse(data);

                var newPriceTag = $(thisPricesBlock).find('#newPrice');
                var currencyCode = $(newPriceTag).data('currency-code');

                $(newPriceTag).fadeOut(250).text(prices['salePrice'].toLocaleString() + ' ' + currencyCode).fadeIn(250);
                $(thisPricesBlock).find('#oldPrice').text(prices['price'].toLocaleString() + ' ' + currencyCode);

            },
            error: function (data) {
                console.log(data);
            }
        });
    });
});