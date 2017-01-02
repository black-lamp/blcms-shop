/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 */

$(document).ready(function () {

    var attributeListTableBody = $('table#attributes-list tbody');
    var lastPriceInputs = attributeListTableBody.find('tr.prices-inputs').last();

    var userGroupSelect = $('#price-user_group_id option:selected');

    /**
     * Adding fields
     */
    var addMoreButton = $('.add-fields');
    addMoreButton.on('click', function () {
        var clone = $(lastPriceInputs).clone();
        clone.find('input').each(function () {
            $(this).val('');
        });
        clone.appendTo(attributeListTableBody);
    });

    /**
     * Reset fields
     */
    attributeListTableBody.on('click', '.clear-prices-tr', function () {
        var clearPricesTr = $(this).closest('tr');
        clearPricesTr.find('input').each(function () {
            $(this).val('');
        });
        clearPricesTr.find('select').each(function () {
            $(this).val('');
        });
    });


    /**
     * Removing fields
     */
    attributeListTableBody.on('click', '.remove-prices-tr', function () {
        $(this).closest('tr').remove();
    });




    // getAttributeValues();
    //
    // var addButton = $('#add-attribute-value');
    // addButton.click(function (e) {
    //     e.preventDefault();
    //     addToInputs();
    // });
});

// /*GETS ATTRIBUTE VALUES BY AJAX WHEN OPTION HAS SELECTED*/
// function getAttributeValues() {
//
//     $('#productcombinationattribute').change(function () {
//         var selectedAttributeId = $("#productcombinationattribute option:selected").attr('value');
//
//         $.ajax({
//             type: "GET",
//             url: "/admin/shop/attribute/get-attribute-values",
//             data: {
//                 'attributeId': selectedAttributeId
//             },
//             success: function (result) {
//                 var attributeValues = JSON.parse(result);
//
//                 $('#productcombinationvalue').children().remove();
//
//                 $.each(attributeValues, function (i, value) {
//                     var label = $('<label>');
//                     var radioBtn = $('<label class="btn btn-default">' +
//                         '<input type="radio" name="value" value="' + value['id'] + '">' +
//                         '<div>' + value['translation']['value'] + '</div>' +
//                         '</label>');
//                     radioBtn.appendTo('#productcombinationvalue');
//
//                 });
//             },
//             error: function (data) {
//                 console.log(data);
//                 alert('You have not permissions to get attribute values.')
//             }
//         });
//
//     });
// }
//
// /*ADDS ATTRIBUTE ID AND VALUE TO HIDDEN INPUTS AND CREATES NEW HIDDEN INPUTS*/
// function addToInputs() {
//     var attributeInput = $('.hidden-attribute').last();
//     var valueInput = $('.hidden-value').last();
//
//     var selectedAttribute = $("#productcombinationattribute option:selected");
//     var selectedValue = $("#productcombinationvalue input:checked");
//
//     var selectedAttributeId = $(selectedAttribute).attr('value');
//     var selectedValueId = $(selectedValue).attr('value');
//
//     if (selectedAttributeId && selectedValueId) {
//
//         $(attributeInput).clone().appendTo("#attribute-inputs");
//         $(valueInput).clone().appendTo("#value-inputs");
//
//         var key = $('.hidden-attribute').length - 1;
//         $(attributeInput[attributeInput.length - 1]).val(selectedAttributeId).attr('data-key', key);
//         $(valueInput[valueInput.length - 1]).val(selectedValueId).attr('data-key', key);
//         ;
//
//         addToTable(
//             $(selectedAttribute).text(),
//             $(selectedValue).next()[0],
//             key
//         );
//     }
//     else {
//         alert('You must fill in all fields');
//     }
//
//     if (selectedAttributeId && selectedValueId) {
//         attributeInput.val(selectedAttributeId);
//         valueInput.val(selectedValueId);
//     }
// }
//
// function addToTable(attributeTdText, valueTdText, removeTdText) {
//     var lastTr = $('#attributes-list tr').last();
//     var attributeTd = $(lastTr).children()[0];
//     var valueTd = $(lastTr).children()[1];
//
//     var removeButton = $($(lastTr).children()[2]).children();
//     $(removeButton).attr('data-key', removeTdText);
//     $(removeButton).click(function () {
//         var key = $(this).attr('data-key');
//
//         $('input[data-key="' + (key) + '"]').remove();
//
//         $(this).parent().parent().remove();
//     });
//
//     $(lastTr).show();
//     lastTr.clone().appendTo('#attributes-list').hide();
//
//     $(attributeTd).text(attributeTdText);
//     $(valueTdText).clone().appendTo(valueTd);
//
// }

