/* Galician localization for 'UI date picker' jQuery extension. */
/* Translated by Jorge Barreiro <yortx.barry@gmail.com>. */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional.gl = {
	closeText: "Pechar",
	prevText: "&#x3C;Ant",
	nextText: "Seg&#x3E;",
	currentText: "Hoxe",
	monthNames: [ "Xaneiro","Febreiro","Marzo","Abril","Maio","Xuño",
	"Xullo","Agosto","Setembro","Outubro","Novembro","Decembro" ],
	monthNamesShort: [ "Xan","Feb","Mar","Abr","Mai","Xuñ",
	"Xul","Ago","Set","Out","Nov","Dec" ],
	dayNames: [ "Domingo","Luns","Martes","Mércores","Xoves","Venres","Sábado" ],
	dayNamesShort: [ "Dom","Lun","Mar","Mér","Xov","Ven","Sáb" ],
	dayNamesMin: [ "Do","Lu","Ma","Mé","Xo","Ve","Sá" ],
	weekHeader: "Sm",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.gl );

return datepicker.regional.gl;

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
