<?php

require 'db.php';

$id = $_GET['id'];
$query = "SELECT * FROM resident_detail INNER JOIN resident_vaccinated ON resident_detail.res_ID = resident_vaccinated.res_ID WHERE vac_ID = '$id'";

$result = mysqli_query($db, $query) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Update Record</title>
</head>
<body>
<link href="css/design.css" rel="stylesheet" type="text/css"> 
<body>
	<div class="label"> Health and Sanitation /Admin Panel
						<div class="nav">
							<a href="index.php">Home</a>
							<a href="view.php">Drug Inventory</a>
							<a href="viewdrugrelease.php">Drug Distribution</a>
							<a href="viewvac.php">Vaccination</a>
							<a href="viewbirth.php">Newborn</a>
							<a href="viewpregnant.php">Pregnant</a>
							<a href="viewdeath.php">Death</a>
						</div>		

</div>
        <br>
<meta charset="utf-8">
<center><div class="borderMethod">

<h1>Update Record</h1>
<?php
$status = "";
if(isset($_POST['new']) && $_POST['new']==1)
{
$id=$_REQUEST['id'];
$name =$_REQUEST['resname'];
$vacname =$_REQUEST['vacName'];
$date =$_REQUEST['date'];

$update="update resident_vaccinated set res_ID='".$name."', vac_Date='".$date."', vac_Name='".$vacname."' where vac_ID='".$id."' ";

mysqli_query($db, $update) or die(mysqli_error($db));

$status = "Record Updated Successfully. </br></br><a href='viewvac.php'>View Updated Record</a>";
echo '<p style="color:blue;">'.$status.'</p>';

}else {
?>
<div>
<form name="form" method="post" action=""> 
<input type="hidden" name="new" value="1" />
<input name="id" type="hidden" value="<?php echo $row['vac_ID'];?>" />

Resident's Name:
<p>
<select name="resname" required>
<?php 
$count = 1;
$sel_query = "SELECT * FROM resident_detail ORDER BY res_ID ASC";
$res_result = mysqli_query($db, $sel_query);
while ($res_row = mysqli_fetch_assoc($res_result)) { 
	$selected = ($res_row["res_ID"] == $row["res_ID"]) ? "selected" : "";
	echo "<option value=\"{$res_row["res_ID"]}\" $selected>{$res_row["res_ID"]} {$res_row["res_fName"]} {$res_row["res_mName"]} {$res_row["res_lName"]}</option>";
	$count++;
}
?>
</select>
</p>

<?php

$id=$_GET['id'];
$query = "SELECT * from resident_vaccinated where vac_ID='".$id."'"; 
$result = mysqli_query($db, $query) or die ( mysqli_error($db));
$row = mysqli_fetch_assoc($result); 
?>
Date of Vaccination:
<p><input id="date" type="date" name="date" value="<?php echo $row['vac_Date'];?>" required></p>
Name of Vaccine:
<p>
<input type="text" name="vacName" value="<?php echo $row['vac_Name'];?>" required> </p>
<p><input name="submit" type="submit" value="Update" /></p>
</form>
<?php } ?>

<br /><br /><br /><br />

</div>
</div>
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