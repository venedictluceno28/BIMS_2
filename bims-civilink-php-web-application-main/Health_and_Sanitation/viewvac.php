<?php
						session_start();

require 'db.php';
$status = "";
if(isset($_POST['new']) && $_POST['new']==1)
{
$rname = $_POST['resident_name'];
$date = $_POST['date'];
$vac = $_POST['vacName'];
$daterec= date("Y-m-d H:i:s");
$ins_query="INSERT INTO resident_vaccinated (`res_ID`,`vac_Date`,`vac_Date_Recorded`,`vac_Name`) VALUES ('$rname','$date','$daterec','$vac')";
mysqli_query($db, $ins_query) or die(mysqli_error($db));
$status = "New Record Inserted Successfully.</br></br><a href='viewvac.php'>View Inserted Record</a>";
}
?>

	<?php
        	if (isset($_POST['search'])) {
    $valueToSearch = $_POST['valueToSearch'];
    // search in all table columns
    // using concat mysql function
    $query         = "SELECT * From resident_detail INNER JOIN resident_vaccinated ON resident_detail.res_ID= resident_vaccinated.res_ID WHERE CONCAT(`vac_Name`,`res_fName`,`res_mName`,`res_lName`,`vac_Date`,`vac_Date_Recorded`,`vac_Name`) LIKE '%".$valueToSearch."%'";
    $search_result = filterTable($query,$db);
    
} else {
    $query         = "SELECT * From resident_detail INNER JOIN resident_vaccinated ON resident_detail.res_ID= resident_vaccinated.res_ID ";
    $search_result = filterTable($query,$db);
}

// function to connect and execute the query
function filterTable($query,$db)
{
    
    $filter_Result = mysqli_query($db, $query);
    return $filter_Result;
}

?> 

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>View Records</title>
</head>
<link href="css/design.css" rel="stylesheet" type="text/css"> 
<body>
		<?php require_once('sidebar.php'); ?>

        <br>
      
        	<meta charset="utf-8">
<div>
					<?php

						if ($_SESSION['position'] != 'Resident') { ?>
							<br>
							<h2>&nbsp&nbsp&nbsp&nbsp&nbspInsert New Record</h2>
							<form name="form" method="post" action="">
								<input type="hidden" name="new" value="1" />

								<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspResident's Name:
								<select name="resident_name" required>
									<option value="">Select Resident...</option>
									<?php 
									$count=1;
									$sel_query= "SELECT * From resident_detail ORDER BY res_ID asc";
									$result = mysqli_query($db,$sel_query);
									while($row = mysqli_fetch_assoc($result)) { ?>
										<option value="<?php echo $row["res_ID"]; ?>">
											<?php echo $row["res_fName"] . " " . $row["res_mName"] . " " . $row["res_lName"]; ?>
										</option>
									<?php $count++; }
									?>
								</select>

								&nbsp&nbspDate of Vaccination:&nbsp&nbsp<input id="date" type="date" name="date" required>
								&nbsp&nbsp&nbsp&nbspName of Vaccine:
								<input type="text" name="vacName" placeholder="Enter here..." required>

								<p>&nbsp&nbsp&nbsp&nbsp&nbsp<input name="submit" type="submit" value="Submit" /></p>
							</form>
							

<div id="header_container">		
		    <div class="container">
				<div id="header" class="row">	
				</div>
			</div>	
        </div>		
        <br>
						<?php } ?>


        
<h2>&nbsp&nbsp&nbsp&nbsp&nbspVaccination Records</h2>

<form action="viewvac.php" method="post">
            <p align="left">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" name="valueToSearch" placeholder="Value To Search">
            &nbsp&nbsp&nbsp<input type="submit" name="search" value="Search" ></p>
             <center>
<table width="95%" border="2" style="border-collapse:collapse;">
<thead>
<tr><th><strong>No.</strong></th><th><strong>Resident's Name</strong></th><th><strong>Height (cm)</strong></th><th><strong>Weight (kg)</strong></th><th><strong>Date of Vaccination </strong></th><th><strong>Name of Vaccine </strong></th><th><strong>Date Recorded </strong></th><th><strong></strong></th><th><strong></strong></th></tr>
</thead>
<script>
function deleletconfig(){

var del=confirm("Are you sure you want to delete this record?");
if (del==true){
   alert ("record deleted")
}
return del;
}
</script>
<tbody>
<?php 

$count=1;
while($row = mysqli_fetch_assoc($search_result)) { ?>
<tr>
	<td align="center"><?php echo $count; ?></td>
	<td align="center"><?php echo $row["res_fName"]; ?><?php echo " " ?><?php echo $row["res_mName"]; ?><?php echo " " ?><?php echo $row["res_lName"]; ?></td>
	<td align="center"><?php echo $row["res_Height"]; ?></td>
	<td align="center"><?php echo $row["res_weight"]; ?></td>
	<td align="center"><?php echo $row["vac_Date"]; ?></td>
	<td align="center"><?php echo $row["vac_Name"]; ?></td>
	<td align="center"><?php echo $row["vac_Date_Recorded"]; ?></td>
	<td align="center"><a style="text-decoration:none;color: blue" href="editvac.php?id=<?php echo $row["vac_ID"]; ?>">Edit</a></td>
	<td align="center"><a style="text-decoration:none;color: blue" onclick="return deleletconfig()" href="deletevac.php?id=<?php echo $row["vac_ID"]; ?>">Delete</a></td>
	</tr>
<?php $count++; }

?>
</tbody>
</table>

  <h2 align="left">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href="Print3.php" style="text-decoration:none;color: blue">Print in PDF</a></h2>

</center>
</div>
<br /><br /><br /><br />
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
</body>
</html>
