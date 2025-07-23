<?php

include("db.php");

$id = $_REQUEST['ID'];

// Update query to set `arc` to 1
mysqli_query($db, "UPDATE accounts SET arc = 1 WHERE ID = '$id'")
	or die(mysqli_error($db));

echo "<script>alert('Account Archived.');</script>";
echo '<script>window.location = "account.php";</script>';

?>