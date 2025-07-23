/* Azerbaijani (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Jamil Najafov (necefov33@gmail.com). */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional.az = {
	closeText: "Bağla",
	prevText: "&#x3C;Geri",
	nextText: "İrəli&#x3E;",
	currentText: "Bugün",
	monthNames: [ "Yanvar","Fevral","Mart","Aprel","May","İyun",
	"İyul","Avqust","Sentyabr","Oktyabr","Noyabr","Dekabr" ],
	monthNamesShort: [ "Yan","Fev","Mar","Apr","May","İyun",
	"İyul","Avq","Sen","Okt","Noy","Dek" ],
	dayNames: [ "Bazar","Bazar ertəsi","Çərşənbə axşamı","Çərşənbə","Cümə axşamı","Cümə","Şənbə" ],
	dayNamesShort: [ "B","Be","Ça","Ç","Ca","C","Ş" ],
	dayNamesMin: [ "B","B","Ç","С","Ç","C","Ş" ],
	weekHeader: "Hf",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.az );

return datepicker.regional.az;

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
