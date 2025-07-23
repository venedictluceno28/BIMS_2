<?php
session_start();
require 'db.php';
$id = $_REQUEST['ID'];

$result = mysqli_query($db, "SELECT * FROM accounts WHERE ID = '$id'");
$test = mysqli_fetch_array($result);
if (!$result) {
    die("Error: Data not found..");
}

$Emailaddress = $test['Emailaddress'];
$Password = $test['Password'];
$Position = $test['Position'];
$Committee = $test['Committee'];
$Profile = $test['profile'];  // Fetch profile image path
$Birthdate = $test['birthdate'];  // Fetch birthdate

if (isset($_POST['save'])) {
    $emailaddress_save = $_POST['emailaddress'];
    $password_save = $_POST['password'];
    $position_save = $_POST['position'];
    $committee_save = $_POST['committee'];
    $birthdate_save = $_POST['birthdate'];

    // Handle image upload
    if ($_FILES['profile']['name']) {
        $profile_image = $_FILES['profile']['name'];
        $profile_image_tmp = $_FILES['profile']['tmp_name'];
        $profile_image_path = "uploads/" . basename($profile_image);
        
		// Ensure the uploads directory exists
		if (!is_dir('uploads')) {
			mkdir('uploads', 0777, true);
		}

		// Move the uploaded file to the server's directory
        if (move_uploaded_file($profile_image_tmp, $profile_image_path)) {
            $profile_save = $profile_image_path; // Store the file path
        } else {
            die("Error uploading image.");
        }
    } else {
        $profile_save = $Profile; // Keep the old image if no new file is uploaded
    }

    // Update the database
    mysqli_query($db, "UPDATE accounts SET 
        Emailaddress='$emailaddress_save', 
        Password='$password_save', 
        Position='$position_save', 
        Committee='$committee_save', 
        profile='$profile_save', 
        birthdate='$birthdate_save' 
        WHERE ID = '$id'") or die(mysqli_error($db));

    echo '<script>window.location = "account.php";</script>';
}

mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Edit Account</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

	<div class="container mt-5">
		<div class="card">
			<div class="card-header bg-success text-white">
				<h4 class="text-center">Edit Account</h4>
			</div>
			<div class="card-body">
			<form method="post" enctype="multipart/form-data">

					<div class="form-group">
						<label for="emailaddress">Email Address</label>
						<input type="email" class="form-control" id="emailaddress" name="emailaddress"
							value="<?php echo $Emailaddress; ?>" required>
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" id="password" name="password" value="<?php echo $Password; ?>" required>
					</div>

					<div class="form-group">
						<label for="profile">Profile</label>
						<input type="file" class="form-control" id="profile" name="profile">
					</div>

					<div class="form-group">
						<label for="birthdate">Birthdate</label>
						<input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $Birthdate; ?>">
					</div>

					<div class="form-group" hidden>
						<label for="position">Position</label>
						<select class="form-control" id="position" name="position" required>
							<option <?php echo ($Position == 'Barangay Captain') ? 'selected' : ''; ?>>Barangay Captain
							</option>
							<option <?php echo ($Position == 'Barangay Councilor') ? 'selected' : ''; ?>>Barangay
								Councilor</option>
							<option <?php echo ($Position == 'Barangay Health Worker') ? 'selected' : ''; ?>>Barangay
								Health Worker</option>
							<option <?php echo ($Position == 'Barangay Treasurer') ? 'selected' : ''; ?>>Barangay
								Treasurer</option>
							<option <?php echo ($Position == 'SK Chairman') ? 'selected' : ''; ?>>SK Chairman</option>
						</select>
					</div>
					<div class="form-group" hidden>
						<label for="committee">Committee</label>
						<select class="form-control" id="committee" name="committee" required>
							<option <?php echo ($Committee == 'None') ? 'selected' : ''; ?>>None</option>
							<option <?php echo ($Committee == 'Peace and Order') ? 'selected' : ''; ?>>Peace and Order
							</option>
							<option <?php echo ($Committee == 'Health,Education & Sport') ? 'selected' : ''; ?>>Health,
								Education & Sport</option>
							<option <?php echo ($Committee == 'Agriculture') ? 'selected' : ''; ?>>Agriculture</option>
							<option <?php echo ($Committee == 'Infrastructure') ? 'selected' : ''; ?>>Infrastructure
							</option>
							<option <?php echo ($Committee == 'Budget & Appropriation') ? 'selected' : ''; ?>>Budget &
								Appropriation</option>
							<option <?php echo ($Committee == 'Clean and Green') ? 'selected' : ''; ?>>Clean and Green
							</option>
							<option <?php echo ($Committee == 'Ways and Means') ? 'selected' : ''; ?>>Ways and Means
							</option>
						</select>
					</div>
					<div class="form-group text-center">
						<button type="submit" name="save" class="btn btn-primary col">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>