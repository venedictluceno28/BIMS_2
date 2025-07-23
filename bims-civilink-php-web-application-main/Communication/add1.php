<!DOCTYPE html>
<html>
<?php
session_start();
include("connection.php");

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

	$api_token = '870|h05YLghELQ8xSwBYKosPFx3w6svYs4EckHpQvsf9';
	$url = 'https://app.philsms.com/api/v3/sms/send';
	$headers = [
		'Authorization: Bearer ' . $api_token,
		'Content-Type: application/json',
		'Accept: application/json'
	];

	if (!empty($mobile_numbers)) {
		$numbers = is_array($mobile_numbers) ? $mobile_numbers : [$mobile_numbers];
		foreach ($numbers as $number) {
			$payload = json_encode([
				'recipient' => trim($number),
				'sender_id' => 'PhilSMS',
				'type' => 'plain',
				'message' => $state
			]);
	
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
	
			if ($http_code == 200) {
				echo "Sent SMS to $number successfully.<br>";
			} else {
				echo "Failed to send SMS to $number. Response: $response<br>";
			}
		}
	}
	

	echo "<script>alert('You successfully added an announcement');</script>";
	echo "<script>window.location=\"index.php\";</script>";
}
?>
</html>