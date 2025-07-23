/* English/UK initialisation for the jQuery UI date picker plugin. */
/* Written by Stuart. */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional[ "en-GB" ] = {
	closeText: "Done",
	prevText: "Prev",
	nextText: "Next",
	currentText: "Today",
	monthNames: [ "January","February","March","April","May","June",
	"July","August","September","October","November","December" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun",
	"Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
	dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
	dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
	dayNamesMin: [ "Su","Mo","Tu","We","Th","Fr","Sa" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "en-GB" ] );

return datepicker.regional[ "en-GB" ];

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
