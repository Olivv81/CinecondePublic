const jquery = require("jquery");

// add-collection-widget.js

jQuery(document).ready(function () {

    console.log('hello');
    jQuery('.add_item_link').click(function (e) {
        console.log('hello!!!');

        var list = jQuery(jQuery(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter

        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = jQuery(list.attr('data-widget-horaires')).html(newWidget);
        newElem.appendTo(list);


        $('#item-fields-list .horaire:last-child > div').append('<button type = "button" class= "del_item_link">supprimer horaire</button>');


        $('.del_item_link').click(function (e) {
            console.log('hello!!!');
            var horaire = $($(this).parent());
            horaire.parent().remove();
        });
        // $('.horaire:last-of-type').append('<button type="button" class="add_horaire_link" data-list-selector="#horaire-fields-list">ajouter un horaire</button>');

    });

    $('.horaire > div').prepend('<button type="button" class="del_item_link">supprimer la JQ2 horaire</button>');

    // $('.del_item_link').click(function (e) {
    //     console.log('hello!!!');
    //     var horaire = $($(this).parent());
    //     horaire.parent().remove();
    // });
    // $('#item-fields-list > div > button').click(function (e) {
    //     var horaire = $($(this).parent());
    //     horaire.remove();
    // });

});
$('.del_item_link').click(function (e) {
    console.log('hello!!!');
    var horaire = $($(this).parent());
    horaire.parent().remove();
});







