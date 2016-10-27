$(document).ready(function () {

    var areaRef = getAreaRef();
    console.log(areaRef);

    pastePostOfficeToField();


});


/*This function gets post office number from warehouse drop down list and paste it field*/
function pastePostOfficeToField() {
    var novaPoshtaWidget = $("#nova-poshta");

    var postOfficeField = $('#order-delivery_post_office');

    $(novaPoshtaWidget).change(function () {
        var selectedValue = $("#useraddress-postoffice").val();
        $(postOfficeField).val(selectedValue);
    });
}

/*This function gets area ref from areas drop down list*/
function getAreaRef() {
    var novaPoshtaWidget = $("#nova-poshta");

    $(novaPoshtaWidget).change(function () {
        return $("#np-areas").val();
    });
}