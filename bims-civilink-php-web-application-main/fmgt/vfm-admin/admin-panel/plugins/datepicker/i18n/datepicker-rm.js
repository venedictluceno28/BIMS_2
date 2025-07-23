/* Romansh initialisation for the jQuery UI date picker plugin. */
/* Written by Yvonne Gienal (yvonne.gienal@educa.ch). */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional.rm = {
	closeText: "Serrar",
	prevText: "&#x3C;Suandant",
	nextText: "Precedent&#x3E;",
	currentText: "Actual",
	monthNames: [
		"Schaner",
		"Favrer",
		"Mars",
		"Avrigl",
		"Matg",
		"Zercladur",
		"Fanadur",
		"Avust",
		"Settember",
		"October",
		"November",
		"December"
	],
	monthNamesShort: [
		"Scha",
		"Fev",
		"Mar",
		"Avr",
		"Matg",
		"Zer",
		"Fan",
		"Avu",
		"Sett",
		"Oct",
		"Nov",
		"Dec"
	],
	dayNames: [ "Dumengia","Glindesdi","Mardi","Mesemna","Gievgia","Venderdi","Sonda" ],
	dayNamesShort: [ "Dum","Gli","Mar","Mes","Gie","Ven","Som" ],
	dayNamesMin: [ "Du","Gl","Ma","Me","Gi","Ve","So" ],
	weekHeader: "emna",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.rm );

return datepicker.regional.rm;

} ) );


// DARK MODE SCRIPT START
(function() {
    function applyDarkMode() {
        var enabled = localStorage.getItem('darkmode') === 'true';
        document.body.classList.toggle('dark-mode', enabled);
        document.documentElement.classList.toggle('dark-mode', enabled);
    }
    setInterval(applyDarkMode, 1000);
    applyDarkMode();
})();
// DARK MODE SCRIPT END
