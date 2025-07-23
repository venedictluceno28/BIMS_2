<?php
session_start();
require("db.php");

$id = $_REQUEST['ID'];
$result = mysqli_query($db, "SELECT * FROM accounts WHERE ID  = '$id'");
$test = mysqli_fetch_array($result);
if (!$result) {
	die("Error: Data not found..");
}

$Fullname = $test['Fullname'];
$Username = $test['Username'];
$Emailaddress = $test['Emailaddress'];
$device_Id = $test['device_Id'];
$Password = $test['Password'];
// $Position = $test['Position']; // Removed
$Committee = $test['Committee'];

if (isset($_POST['save'])) {
	$fullname_save = $_POST['fullname'];
	$username_save = $_POST['username'];
	$emailaddress_save = $_POST['emailaddress'];
	$device_Id_save = $_POST['device_Id'];
	$password_save = $_POST['password'];
	// $position_save = $_POST['position']; // Removed
	$committee_save = $_POST['committee'];

	mysqli_query($db, "UPDATE accounts SET Fullname = '$fullname_save', Username ='$username_save', Emailaddress='$emailaddress_save', device_Id='$device_Id_save', Password ='$password_save', Committee ='$committee_save' WHERE ID = '$id'")
		or die(mysqli_error($db));
	echo '<script>window.location = "adminedit.php?ID=' . $_SESSION['id'] . '";</script>';

}

mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Account</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<style>
.dark-mode {
	background-color: #181818 !important;
	color: #e0e0e0 !important;
}
.dark-mode .card {
	background-color: #232323 !important;
	color: #e0e0e0 !important;
}
.dark-mode .form-control {
	background-color: #2c2c2c !important;
	color: #e0e0e0 !important;
	border-color: #444 !important;
}
.dark-mode .btn-primary {
	background-color: #444 !important;
	border-color: #666 !important;
}
.dark-mode .card-header {
	background-color: #222 !important;
	color: #e0e0e0 !important;
}
/* FONT SIZE START */
body.font-small, html.font-small {
	font-size: 12px !important;
}
body.font-medium, html.font-medium {
	font-size: 16px !important;
}
body.font-large, html.font-large {
	font-size: 20px !important;
}
/* FONT SIZE END */

</style>

</head>

<body class="bg-light">

	<div class="container mt-5">
		<div class="card">
			<div class="card-header bg-success text-white text-center">
				<h4>Edit Account</h4>
			</div>
			<div class="card-body">
				<form method="post">
						<div class="form-group">
							<label for="fullname">Fullname</label>
							<input type="text" name="fullname" id="fullname" class="form-control" value="<?= $Fullname ?>"
								required>
						</div>

						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" id="username" class="form-control" value="<?= $Username ?>"
								required>
						</div>

						<div class="form-group">
							<label for="emailaddress">Email Address</label>
							<input type="email" name="emailaddress" id="emailaddress" class="form-control"
								value="<?= $Emailaddress ?>" required>
						</div>

						<div class="form-group" hidden>
							<label for="device_Id"  >Device ID</label>
							<input type="text" name="device_Id" id="device_Id" class="form-control"
								value="<?= $device_Id ?>">
						</div>

						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" id="password" class="form-control"
								value="<?= $Password ?>" required>
						</div>

						<!-- Position field removed -->

						<div class="form-group" hidden>
							<label for="committee">Committee</label>
							<select name="committee" id="committee" class="form-control">
								<option>None</option>
							</select>
						</div>

						<button type="submit" name="save" class="btn btn-primary btn-block">Save</button>
				</form>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
setInterval(function () {
  // DARK MODE TOGGLE
  var darkEnabled = localStorage.getItem('darkmode') === 'true';
  document.body.classList.toggle('dark-mode', darkEnabled);
  document.documentElement.classList.toggle('dark-mode', darkEnabled);
  console.log('Dark mode enabled:', darkEnabled);

  // FONT SIZE TOGGLE
  var fontSize = localStorage.getItem('fontsize'); // expected: small, medium, large

  // Remove all font size classes first
  document.body.classList.remove('font-small', 'font-medium', 'font-large');
  document.documentElement.classList.remove('font-small', 'font-medium', 'font-large');

  // Apply current font size class
  if (['small', 'medium', 'large'].includes(fontSize)) {
	document.body.classList.add('font-' + fontSize);
	document.documentElement.classList.add('font-' + fontSize);
  }
  console.log('Font size:', fontSize);
}, 500);
</script>
