<!DOCTYPE html>
<html>
<?php
session_start();
include("connection.php");

// Add this function after includes
function sendOTPSMS($number, $message) {
	$api_key = '57bfbf50ef797842bae7b7e1d460c22d';
	$sender_name = 'SEMAPHORE'; // Optional, must be registered in Semaphore
	$url = 'https://semaphore.co/api/v4/messages';

	$payload = http_build_query([
		'apikey' => $api_key,
		'number' => $number,
		'message' => $message,
		'sendername' => $sender_name
	]);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return $http_code == 200;
}

$cate = $_POST['category'];
$state = $_POST['statement'];
$rece = is_array($_POST['mobile_numbers']) 
	? count($_POST['mobile_numbers']) . ' Recipients' 
	: '1 Recipient';

$mobile_numbers = $_POST['mobile_numbers'];
$target_dir = "image/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
$_SESSION['receiver'] = $rece;

$sql = "INSERT INTO announce(category, announcement, image, receiver) VALUES ('$cate', '$state', '$target_file', '$rece')" or die("Errors");
if ($db->query($sql) === TRUE) {
	if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
	}

	if (!empty($mobile_numbers)) {
		$numbers = is_array($mobile_numbers) ? $mobile_numbers : [$mobile_numbers];
		foreach ($numbers as $number) {
			$success = sendOTPSMS(trim($number), $state);
			if ($success) {
				echo "Sent SMS to $number successfully.<br>";
			} else {
				echo "Failed to send SMS to $number.<br>";
			}
		}
	}

	echo "<script>alert('You successfully added an announcement');</script>";
	echo "<script>window.location=\"index.php\";</script>";
}
?>
</html>
