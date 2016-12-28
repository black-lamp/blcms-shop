$(document).ready(function () {

    var widget= $('.product-prices-widget');


    widget.change(function () {

        var priceTag = $(this).find('#newPrice');

        var countInput = $(this).find('#cartform-count');
        if (countInput.length) {

            var oneItemPrice = $(priceTag).attr('data-sum');

            var currencyCode = $(priceTag).data('currency-code');
            var number = countInput.val();
            var newPrice = (oneItemPrice * number).toLocaleString();
            priceTag.text(newPrice + ' ' + currencyCode);
        }
    });

});
