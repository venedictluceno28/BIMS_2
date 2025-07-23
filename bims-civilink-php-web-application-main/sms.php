<?php
function sendSMS($recipient, $message)
{
    // API endpoint and token
    $url = 'https://app.philsms.com/api/v3/sms/send';
    $api_token = '821|fIAkc64uhsb7YWnwMNYRsKJMKvDy0sDqAL32CJIB';

    // Message details
    $data = [
        'recipient' => $recipient,
        'sender_id' => 'PhilSMS',  // Replace with your sender ID if you have one
        'type' => 'plain',
        'message' => $message,
    ];

    // cURL initialization
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_token,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute and get the response
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check response
    if ($status_code === 200) {
        return json_decode($response, true);
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to send SMS. Status code: ' . $status_code,
            'response' => $response
        ];
    }
}

// Example usage in PHP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phoneNumber']) && isset($_POST['message'])) {
    $recipient = $_POST['phoneNumber'];
    $message = $_POST['message'];

    $result = sendSMS($recipient, $message);

    // Respond back with the result in JSON
    echo json_encode($result);
}
?>