<?php
if (!isset($_SESSION['position'])) {
	header('Location: ../');
	exit();
}
?>

<div class="label"> Health and Sanitation / Admin Panel
		<div class="nav">
			<a href="index.php">Health Analytics</a>
			<a href="medical.php">Medical History & Logs</a>
		 
			<a href="child.php">Child Nutrition & Growth</a>
			<a href="senior.php">Senior Citizen Monitoring</a>
			<a href="view.php">Drug Inventory</a>
			<a href="viewdrugrelease.php">Drug Distribution</a>
			<a href="viewvac.php">Vaccination</a>
			<a href="viewbirth.php">Newborn</a>
		</div>
	</div>

    <script>
	setInterval(function () {
	  var darkEnabled = localStorage.getItem('darkmode') === 'true';
	  document.body.classList.toggle('dark-mode', darkEnabled);
	  document.documentElement.classList.toggle('dark-mode', darkEnabled);

	  var fontSize = localStorage.getItem('fontsize');
	  document.body.classList.remove('font-small', 'font-medium', 'font-large');
	  document.documentElement.classList.remove('font-small', 'font-medium', 'font-large');
	  if (['small', 'medium', 'large'].includes(fontSize)) {
		document.body.classList.add('font-' + fontSize);
		document.documentElement.classList.add('font-' + fontSize);
	  }
	}, 500);
	</script>

	
<!-- DataTables Import & Initialization -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
	$('.table').DataTable({
		responsive: true,
		pageLength: 10
	});
});
</script>
