<?php
session_start();
include('db.php');

if (!isset($_SESSION['id'])) {
	header('location:index.php');
	exit;
}

// Check if the last visited page exists in the session, default to index.php if not
$lastVisitedPage = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : "Resident_Profiling/Dash/pages/index.php";
?>

<html>
<title>Admin Panel</title>
<link rel="icon" href="Picture/banaba.png" type="image/png">

<link rel="shortcut icon" href="indang logo.png">

<Style>
	body {
		background-color: #A0C49D;
	}
</style>

<head>
	<?php
	// Check for 'dash' parameter and redirect frame if needed
	if (isset($_GET['dash']) && $_GET['dash'] === 'health') {
		$lastVisitedPage = "Health_and_Sanitation/dashboard.php";
	} elseif (isset($_GET['dash']) && $_GET['dash'] === 'resident') {
		$lastVisitedPage = "Resident_Profiling/Dash/pages/dashboard.php";
	}
	?>
	<frameset rows="80%,5.5%" frameborder="0">
		<frameset cols="12%,80%">
			<frame src="modules.php" name="FraLink">
			<frame src="<?php echo $lastVisitedPage; ?>" name="FraDisplay">
		</frameset>
		<frame src="footer.php" name="FraFooter">
	</frameset>
</head>

</html>

<!-- DARK MODE SCRIPT START -->
<script>
(function() {
    function applyDarkMode() {
        var enabled = localStorage.getItem('darkmode') === 'true';
        document.body.classList.toggle('dark-mode', enabled);
        document.documentElement.classList.toggle('dark-mode', enabled);
    }
    setInterval(applyDarkMode, 1000);
    applyDarkMode();
})();
</script>
<!-- DARK MODE SCRIPT END -->
