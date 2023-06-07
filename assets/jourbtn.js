// const jourbt = document.getElementById("jour");


// jourbt.addEventListener('change', function () {

//     document.dayFilter.submit();
// });


// const jourbt = document.getElementById("jour");


// jourbt.addEventListener('change', function () {

//     document.dayFilter.submit(); 
// });

const $ = require('jquery');


$(function () {
    $.datepicker.regional['fr'] = {
        closeText: 'Fermer',
        prevText: '&#x3c;Préc',
        nextText: 'Suiv&#x3e;',
        currentText: 'Aujourd\'hui',
        monthNames: ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],
        monthNamesShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun',
            'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
        dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
        weekHeader: 'Sm',
        dateFormat: 'dd-mm-yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        minDate: 0,
        maxDate: '+12M +0D',
        numberOfMonths: 1,
        showButtonPanel: false
    };
    $.datepicker.setDefaults($.datepicker.regional["fr"]);
    $("#datepicker").datepicker({
        onSelect: function () { document.dayFilter.submit() }
    });

});