/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * Gets attribute values by attribute id
 */

$(document).ready(function() {
    getAttributeValues();

});


function getAttributeValues() {

    $('#productcombinationattribute-attribute_id').change(function() {
        var selectedAttributeId = $( "#productcombinationattribute option:selected" ).attr('value');
        console.log(selectedAttributeId);

        $.ajax({
            type: "GET",
            url: "/admin/shop/attribute/get-attribute-values",
            data: {
                'attributeId': selectedAttributeId
            },
            success: function (result) {
                var attributeValues = JSON.parse(result);
                $.each(attributeValues, function(i, value) {
                    $(new Option(value['translation']['title'], value['id'])).appendTo('#productcombinationattribute-attribute_value_id');
                });
            },
            error: function () {
                alert('You have not permissions to get attribute values.')
            }
        });

    });
}
