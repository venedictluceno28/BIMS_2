<?php
session_start();
require_once('db.php');
$sesID = $_SESSION['id'];
$z = mysqli_query($db, "SELECT * FROM `accounts` WHERE ID = '$sesID'");
$data = mysqli_fetch_array($z);

// Handle theme/font size changes via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['theme'])) {
		$_SESSION['theme'] = $_POST['theme'];
		exit;
	}
	if (isset($_POST['fontSize'])) {
		$_SESSION['fontSize'] = $_POST['fontSize'];
		exit;
	}
}

// Set defaults if not set
if (!isset($_SESSION['theme'])) $_SESSION['theme'] = 'light';
if (!isset($_SESSION['fontSize'])) $_SESSION['fontSize'] = 'medium';
?>
<html>
<head>
	<link rel="shortcut icon" href="Icon/indang logo.png">
	<!-- Font Awesome CDN -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<style type="text/css">
		html, body, h1, h2, h3, h4, h5, h6, a, p, div, span, input, button, label, select, textarea, table, th, td {
			font-family: calibri;
			color: grey;
			transition: background 0.3s, color 0.3s;
		}
		body {
			margin: 0;
			font-size: 16px;
		}
		.fa-fw { width: 1.25em; text-align: center; }
		h1 { color: white; }
		a {
			display: block;
			text-decoration: none;
			padding: 10px 5px;
			font-size: 18px;
			color: gray;
		}
		a:hover {
			background: white;
			color: gray;
			transition: background-color 0.3s ease-in-out;
		}
		.banner {
			width: auto;
			padding: 12px 0 15px 5px;
			font-size: 25px;
			text-align: left;
			color: grey;
			background: white;
			font-weight: bold;
			text-transform: uppercase;
		}
		.holder {
			text-indent: 20px;
			background: #212121;
			width: 100%;
			padding: 10px 0;
			text-align: left;
		}
		.avatar { border-radius: 30px; }
		/* DARK MODE & FONT SIZE START */
		body.dark-mode {
			background: #181818;
			color: #e0e0e0;
		}
		body.dark-mode .banner {
			background: #232323;
			color: #e0e0e0;
		}
		body.dark-mode .holder {
			background: #333;
			color: #e0e0e0;
		}
		body.dark-mode a {
			color: #e0e0e0;
		}
		body.dark-mode a:hover {
			background: #333;
			color: #4CAF50;
		}
		/* Font size classes */
		.font-small html, .font-small body, .font-small h1, .font-small h2, .font-small h3, .font-small h4, .font-small h5, .font-small h6, .font-small a, .font-small p, .font-small div, .font-small span, .font-small input, .font-small button, .font-small label, .font-small select, .font-small textarea, .font-small table, .font-small th, .font-small td { font-size: 14px !important; }
		.font-medium html, .font-medium body, .font-medium h1, .font-medium h2, .font-medium h3, .font-medium h4, .font-medium h5, .font-medium h6, .font-medium a, .font-medium p, .font-medium div, .font-medium span, .font-medium input, .font-medium button, .font-medium label, .font-medium select, .font-medium textarea, .font-medium table, .font-medium th, .font-medium td { font-size: 16px !important; }
		.font-large html, .font-large body, .font-large h1, .font-large h2, .font-large h3, .font-large h4, .font-large h5, .font-large h6, .font-large a, .font-large p, .font-large div, .font-large span, .font-large input, .font-large button, .font-large label, .font-large select, .font-large textarea, .font-large table, .font-large th, .font-large td { font-size: 20px !important; }
		.toggle-bar {
	padding: 10px 0 10px 15px;
	background: #f5f5f5;
	border-top: 1px solid #ddd;
	position: fixed;
	bottom: 0;
	left: 0;
	width: 250px; /* match sidebar width */
}

		body.dark-mode .toggle-bar {
			background: #232323;
			border-top: 1px solid #444;
		}
		.toggle-btn {
			margin-right: 10px;
			padding: 5px 12px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			background: #4CAF50;
			color: #fff;
			font-size: 14px;
		}
		.toggle-btn:active {
			background: #388e3c;
		}
		/* DARK MODE & FONT SIZE END */
		a.active {
	background: #e0e0e0; /* light mode active background */
	color: #000;
	font-weight: bold;
}

body.dark-mode a.active {
	background: #333; /* dark mode active background */
	color: #4CAF50;
	font-weight: bold;
}

	</style>
</head>
<body class="<?php echo ($_SESSION['theme'] === 'dark' ? 'dark-mode' : ''); ?> font-<?php echo htmlspecialchars($_SESSION['fontSize']); ?>">
	<div class="banner"> 
		<img src="Picture/banaba.png" style="float:left; border-radius: 50%; filter: invert(0%)!important; margin-right: 5%" width="50" height="50">
		<p style="font-size: 15px; word-wrap: break-word;">Welcome 
		<?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['fullname']; ?>
	</div>

	<?php
function generateNavLink($href, $faIcon, $text)
{
$currentPage = basename($_SERVER['PHP_SELF']);
$isActive = (basename($href) === $currentPage) ? 'active' : '';
return '<a href="' . $href . '" target="FraDisplay" class="' . $isActive . '" onclick="saveLastPage(\'' . $href . '\')">
	<i class="fa-fw ' . $faIcon . '"></i>&nbsp;' . $text . '</a>';
}

	if ($_SESSION['position'] == 'Barangay Secretary' ) {
		echo '<b><div class="holder"><b>ADMINISTRATOR</b></div>';

		echo generateNavLink("Resident_Profiling/Dash/index.php", "fa-solid fa-house", "Dashboard");
		// Removed Account button for admin
		// echo generateNavLink("account.php", "fa-solid fa-user-pen", "Account");

		echo generateNavLink("Resident_Profiling/resident.php", "fa-solid fa-users", "Manage Residents");
		echo generateNavLink("Resident_Profiling/programs.php", "fa-solid fa-list-check", "Barangay Programs");
		echo generateNavLink("Communication/index.php", "fa-solid fa-comment-sms", "SMS Notifications");
		echo generateNavLink("Health_and_Sanitation/index.php", "fa-solid fa-syringe", "Health Managemnent");
		echo generateNavLink("Resident_Profiling/calendar.php", "fa-solid fa-calendar-days", "Calendar");
		
		echo generateNavLink("adminedit.php?ID=" . $_SESSION['id'], "fa-solid fa-gear", "Settings");
		// echo generateNavLink("Clearance_and_Forms/index.php", "fa-solid fa-file-lines", "DC (Addon)");

	}

 

	if ($_SESSION['position'] == 'Resident') {
		echo '<b><div class="holder"><b>RESIDENT</b></div>';

		echo generateNavLink("Resident_Profiling/Dash/pages/dashboard.php", "fa-solid fa-house", "Dashboard");

		echo generateNavLink("Communication/index.php", "fa-solid fa-comment-sms", "SMS Notifications");
		echo generateNavLink("Resident_Profiling/programs.php", "fa-solid fa-list-check", "Barangay Health Program");
		echo generateNavLink("Resident_Profiling/calendar.php", "fa-solid fa-calendar-days", "Calendar");
		// echo generateNavLink("request.php", "fa-solid fa-file-lines", "Request Document");
		echo generateNavLink("adminedit.php?ID=" . $_SESSION['id'], "fa-solid fa-gear", "Settings");
	}
	if ($_SESSION['position'] == 'Barangay Health Worker') {
		echo '<b><div class="holder"><b>HEALTH WORKER</b></div>';
		echo generateNavLink("Health_and_Sanitation/index.php", "fa-solid fa-syringe", "Health Managemnent");
		echo generateNavLink("Resident_Profiling/calendar.php", "fa-solid fa-calendar-days", "Calendar");
		echo generateNavLink("Communication/index.php", "fa-solid fa-bullhorn", "Announcements");
		echo generateNavLink("adminedit.php?ID=" . $_SESSION['id'], "fa-solid fa-gear", "Account Setting");
	}


	?>
	<a href="accountlogout.php" target="_Parent" class="nav-link logout-link">
		<i class="fa-fw fa-solid fa-right-from-bracket"></i> Logout
	</a>

	<!-- DARK MODE & FONT SIZE CONTROLS BELOW SIDEBAR -->
	<div class="toggle-bar"> 
		<button class="toggle-btn" onclick="toggleDarkMode()" id="themeBtn" title="Toggle dark mode">
			<i class="fa-solid fa-moon"></i>
		</button>
		<button class="toggle-btn" onclick="changeFontSize('small')" title="Small font">
			<i class="fa-solid fa-a" style="font-size: 12px;"></i> 
		</button>
		<button class="toggle-btn" onclick="changeFontSize('medium')" title="Medium font">
			<i class="fa-solid fa-a" style="font-size: 16px;"></i>
		</button>
		<button class="toggle-btn" onclick="changeFontSize('large')" title="Large font">
			<i class="fa-solid fa-a" style="font-size: 22px;"></i> 
		</button>
		<!-- Import Font Awesome icons if not already included -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	</div>
	<script>
		function saveLastPage(page) {
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "save_last_page.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("last_page=" + encodeURIComponent(page));
		}

		// DARK MODE & FONT SIZE START
		function setTheme(theme) {
			document.body.classList.toggle('dark-mode', theme === 'dark');
			document.documentElement.classList.toggle('dark-mode', theme === 'dark');
			localStorage.setItem('darkmode', theme === 'dark');
			// Save to session via AJAX
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("theme=" + theme);
		}
		function toggleDarkMode() {
			var isDark = !document.body.classList.contains('dark-mode');
			setTheme(isDark ? 'dark' : 'light');
		}
		function setFontSize(size) {
			document.body.classList.remove('font-small', 'font-medium', 'font-large');
			document.body.classList.add('font-' + size);
			localStorage.setItem('fontsize', size); // Store font size in localStorage
			// Save to session via AJAX
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("fontSize=" + size);
		}
		function changeFontSize(size) {
			setFontSize(size);
		}
		// Apply dark mode and font size from localStorage on load
		document.addEventListener('DOMContentLoaded', function() {
			var enabled = localStorage.getItem('darkmode') === 'true';
			document.body.classList.toggle('dark-mode', enabled);
			document.documentElement.classList.toggle('dark-mode', enabled);

			var fontSize = localStorage.getItem('fontsize');
			if (fontSize) {
				document.body.classList.remove('font-small', 'font-medium', 'font-large');
				document.body.classList.add('font-' + fontSize);
		 }
		});
	</script>
</body>
</html>
