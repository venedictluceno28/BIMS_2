function HTMLtoPDF(){
var pdf = new jsPDF('p', 'pt', 'letter');
source = $('#HTMLtoPDF')[0];
specialElementHandlers = {
	'#bypassme': function(element, renderer){
		return true
	}
}
margins = {
    top: 50,
    left: 60,
    width: 545
  };
pdf.fromHTML(
  	source // HTML string or DOM elem ref.
  	, margins.left // x coord
  	, margins.top // y coord
  	, {
  		'width': margins.width // max width of content on PDF
  		, 'elementHandlers': specialElementHandlers
  	},
  	function (dispose) {
  	  // dispose: object with X, Y of the last line add to the PDF
  	  //          this allow the insertion of new lines after html
        pdf.save('html2pdf.pdf');
      }
  )
}


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
