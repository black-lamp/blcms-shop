/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * Gets attribute values by attribute id
 */

$(document).ready(function() {
    getAttributeValues();

    var addButton = $('#add-attribute-value');
    addButton.click(function(e){
        e.preventDefault();
        addToInputs();
    });
});

/*GETS ATTRIBUTE VALUES BY AJAX WHEN OPTION HAS SELECTED*/
function getAttributeValues() {

    $('#productcombinationattribute').change(function() {
        var selectedAttributeId = $( "#productcombinationattribute option:selected" ).attr('value');

        $.ajax({
            type: "GET",
            url: "/admin/shop/attribute/get-attribute-values",
            data: {
                'attributeId': selectedAttributeId
            },
            success: function (result) {
                var attributeValues = JSON.parse(result);
                $.each(attributeValues, function(i, value) {
                    $(new Option(value['translation']['title'], value['id'])).appendTo('#productcombinationvalue');
                });
            },
            error: function () {
                alert('You have not permissions to get attribute values.')
            }
        });

    });
}

/*ADDS ATTRIBUTE ID AND VALUE TO HIDDEN INPUTS AND CREATES NEW HIDDEN INPUTS*/
function addToInputs() {
    var attributeInput = $('.hidden-attribute').last();
    var valueInput = $('.hidden-value').last();

    var selectedAttributeId = $( "#productcombinationattribute option:selected" ).attr('value');
    var selectedValueId = $( "#productcombinationvalue option:selected" ).attr('value');

    if (selectedAttributeId && selectedValueId) {

        $(attributeInput).clone().appendTo("#attribute-inputs");
        $(valueInput).clone().appendTo("#value-inputs");

        $(attributeInput[attributeInput.length - 1]).val(selectedAttributeId);
        $(valueInput[valueInput.length - 1]).val(selectedValueId);

        addToTable($( "#productcombinationattribute option:selected" ).text(), $( "#productcombinationvalue option:selected" ).text());
    }
    else {
        alert('You must fill in all fields');
    }

    if (selectedAttributeId && selectedValueId) {
        attributeInput.val(selectedAttributeId);
        valueInput.val(selectedValueId);
    }
}

function addToTable(attributeTdText, valueTdText) {
    var lastTr = $('#attributes-list tr').last();
    var attributeTd = $(lastTr).children()[0];
    var valueTd = $(lastTr).children()[1];
    lastTr.clone().appendTo('#attributes-list');

    $(attributeTd).text(attributeTdText);
    $(valueTd).text(valueTdText);
}