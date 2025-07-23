 <?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('db.php');

// LOGIN
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $qry = mysqli_query($db, "SELECT * FROM accounts WHERE Username ='$username'");
    if (!$qry) {
        echo "<script>alert('SQL Error: " . mysqli_error($db) . "');</script>";
    } else {
        $count = mysqli_num_rows($qry);
        if ($count > 0) {
            $accnt = mysqli_fetch_array($qry);
            $pass = $accnt['Password'];
            $_SESSION['id'] = $accnt['ID'];
            if ($pass == $password) {
                $pos = $accnt['position_id'];
                $position = $accnt['Position'];
                $committee = $accnt['Committee'];
                $fullname = $accnt['Fullname'];
                $_SESSION['LOGIN_STATUS'] = true;
                $_SESSION['position'] = $position;
                if ($_SESSION['position'] === 'Barangay Secretary') {
                    $_SESSION['position2'] = 'Barangay Admin';
                }
                $_SESSION['USER'] = $username;
                $_SESSION['committee'] = $committee;
                $_SESSION['password'] = $password;
                $_SESSION['emailaddress'] = $accnt['Emailaddress'];
                $_SESSION['device_Id'] = $accnt['Position'];
                $_SESSION['positionID'] = $pos;
                $_SESSION['fullname'] = $fullname;  
                $_SESSION['position_id'] = $accnt['position_id'];
                if ($accnt['type'] == 1) {
                    $_SESSION['position'] = 'Resident';
                    $_SESSION['position2'] = 'Resident';
                    echo '<script>window.location = "home.php?dash=resident";</script>';
                } elseif ($position === 'Barangay Health Worker') {
                    echo '<script>window.location = "home.php?dash=health";</script>';
                } else {
                    echo '<script>window.location = "home.php";</script>';
                }
            }
            } else {
                echo "<script>alert('Incorrect Password.');</script>";
            }
            echo "<script>alert('Invalid username.');</script>";
        }
    }

// OTP SEND HANDLER
if (isset($_POST['otp']) && isset($_POST['destination']) && isset($_POST['method'])) {
    $_SESSION['otp'] = $_POST['otp'];
    $_SESSION['otp_method'] = $_POST['method'];
    $_SESSION['otp_destination'] = $_POST['destination'];
    $_SESSION['otp_created_at'] = time();

    $message = "Your OTP is: " . $_SESSION['otp'];

    if ($_POST['method'] === 'email') {
        sendOTPEmail($_POST['destination'], $message);
    } elseif ($_POST['method'] === 'sms') {
        sendOTPSMS($_POST['destination'], $message);
    }

    echo 'OTP stored and sent.';
    exit;
}

// VERIFY OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    if ($_POST['verify_otp'] == $_SESSION['otp']) {
        echo 'success';
    } else {
        echo 'fail';
    }
    exit;
}

// PASSWORD RESET
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password']) && isset($_POST['reset'])) {
    $password = $_POST['password'];
    $destination = $_SESSION['otp_destination'];

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $field = $_SESSION['otp_method'] === 'email' ? 'Emailaddress' : 'Contact';
        $sqlCustomer = "SELECT * FROM accounts WHERE $field = '$destination'";
        $result = mysqli_query($db, $sqlCustomer);

        if (mysqli_num_rows($result) > 0) {
            $sqlUpdate = "UPDATE accounts SET Password = '$password' WHERE $field = '$destination'";
            $updateResult = mysqli_query($db, $sqlUpdate);
            echo $updateResult ? 'success' : 'error';
        } else {
            echo 'emailnotexist';
        }
        exit;
    }
}

// SEND EMAIL OTP
function sendOTPEmail($email, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'support@tcuregistrarrequest.site';
        $mail->Password = '#228JyiuS';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('support@tcuregistrarrequest.site', 'BPC SUPPORT');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = $message;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// SEND SMS OTP
function sendOTPSMS($number, $message) {
    $api_token = '870|h05YLghELQ8xSwBYKosPFx3w6svYs4EckHpQvsf9';
    $url = 'https://app.philsms.com/api/v3/sms/send';
    $headers = [
        'Authorization: Bearer ' . $api_token,
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    $payload = json_encode([
        'recipient' => $number,
        'sender_id' => 'PhilSMS',
        'type' => 'plain',
        'message' => $message
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    curl_exec($ch);
    curl_close($ch);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Barangay Management Information System</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="icon" href="Picture/banaba.png" type="image/png">

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css">
	<style>
            /* ...existing styles... */
    .green-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 60, 0, 0.6);
        z-index: 2;
        pointer-events: none;
    }
		.pnlm-container {
			background: #f4f4f4;
		}

		#panorama {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}

		.login-container {
			position: relative;
			z-index: 10;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
		}

		.login-box {
			background: rgba(255, 255, 255, 0.9);
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			width: 100%;
			max-width: 400px;
		}

		.login-box img {
			display: block;
			margin: 0 auto 20px;
		}
	</style>
</head>

<body>
	<div id="panorama"></div>
<div class="green-overlay"></div>
	<div class="login-container">
		<div class="login-box">
			<h3 class="text-center">Sign In</h3>
			<img src="Picture/banaba.png" height="100" width="100" alt="Logo">

		<form method="POST" action="login.php">
				<div class="form-group">
					<input type="text" class="form-control shadow-none" name="username" placeholder="Enter Username"
						required autofocus>
                        </div>  
                <div class="form-group position-relative">
                    <input type="password" class="form-control shadow-none" name="password" id="password" placeholder="Enter Password" required>
                    <span class="fa fa-eye position-absolute" id="togglePassword" style="top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
                </div>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $('#togglePassword').on('click', function() {
                            const passwordInput = $('#password');
                            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                            passwordInput.attr('type', type);
                            $(this).toggleClass('fa-eye fa-eye-slash');
                        });
                    });
                </script>
				<button type="submit" name="submit" class="btn btn-primary btn-block shadow-none">Enter</button>
			</form>
			<button onclick="askForInput()" class="btn btn-link btn-block shadow-none">Forgot Password?</button>
		</div>
	</div>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
    function generateOTP() {
        return Math.floor(1000 + Math.random() * 9000);
    }

    function askForInput() {
        Swal.fire({
            title: 'Select OTP Method',
            input: 'radio',
            inputOptions: {
                email: 'Email',
                sms: 'SMS'
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to choose a method!';
                }
            }
        }).then((result) => {
            const method = result.value;
            if (method === 'email') {
                askForEmail();
            } else if (method === 'sms') {
                askForPhone();
            }
        });
    }

    function askForEmail() {
        Swal.fire({
            title: 'Enter Your Email Address',
            input: 'email',
            showCancelButton: false,
            confirmButtonText: 'Next',
            preConfirm: (email) => {
                if (!email) {
                    Swal.showValidationMessage('Email address is required');
                }
                return email;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const email = result.value;
                const otp = generateOTP();
                storeOTPInSession(otp, email, 'email');
            }
        });
    }
function askForPhone() {
    Swal.fire({
        title: 'Enter Your Mobile Number',
        input: 'tel',
        inputValue: '63',
        inputAttributes: {
            pattern: '\\639\\d{9}',
            maxlength: 12
        },
        inputPlaceholder: '639XXXXXXXXX',
        confirmButtonText: 'Next',
        preConfirm: (phone) => {
            if (!phone || !/^639\d{9}$/.test(phone)) {
                Swal.showValidationMessage('Valid PH mobile number required (639XXXXXXXXX)');
            }
            return phone;
        },
        didOpen: () => {
            const input = Swal.getInput();
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);
                if (!this.value.startsWith('63')) this.value = '63' + this.value.slice(2);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const phone = result.value;
            const otp = generateOTP();
            storeOTPInSession(otp, phone, 'sms');
        }
    });
}

    function storeOTPInSession(otp, destination, method) {
        $.ajax({
            url: 'login.php',
            method: 'POST',
            data: {
                otp: otp,
                destination: destination,
                method: method
            },
            success: function () {
                askForOTP();
            }
        });
    }

    function askForOTP(invalidMessage = '') {
        Swal.fire({
            title: 'Enter Your OTP',
            input: 'text',
            inputPlaceholder: 'Enter the 4-digit code',
            text: invalidMessage,
            confirmButtonText: 'Submit',
            preConfirm: (otp) => {
                if (!otp || otp.length !== 4) {
                    Swal.showValidationMessage('OTP must be 4 digits');
                }
                return otp;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                verifyOTP(result.value);
            }
        });
    }

    function verifyOTP(inputOTP) {
        $.ajax({
            url: 'login.php',
            method: 'POST',
            data: { verify_otp: inputOTP },
            success: function (response) {
                if (response.trim() === 'success') {
                    askForPassword();
                } else {
                    askForOTP('Invalid OTP. Please try again.');
                }
            }
        });
    }

    function askForPassword() {
        Swal.fire({
            title: 'Enter New Password',
            input: 'password',
            inputPlaceholder: 'New password',
            confirmButtonText: 'Submit',
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitPassword(result.value);
            }
        });
    }

    function submitPassword(password) {
        $.ajax({
            url: 'login.php',
            method: 'POST',
            data: {
                password: password,
                reset: true
            },
            success: function (response) {
                if (response.trim() === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Changed Successfully'
                    });
                } else if (response.trim() === 'emailnotexist') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Account not found'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to reset password'
                    });
                }
            }
        });
    }
</script>


        <!-- jQuery (necessary for Toastr) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Toastr Script -->
        <script>
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            <?php if (!empty($toastrMessage)) { ?>
                toastr.error("<?php echo $toastrMessage; ?>");
            <?php } ?>
        </script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
    <script>
        function loadBase64FromTextFile(url) {
            return fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                });
        }

        // Ensure the panorama container fits the viewport and image doesn't overflow
        document.addEventListener('DOMContentLoaded', function () {
            const panorama = document.getElementById('panorama');
            panorama.style.width = '100vw';
            panorama.style.height = '100vh';
            panorama.style.overflow = 'hidden';
        });

        loadBase64FromTextFile('base64.txt')
            .then(base64 => {
                const imageSrc = 'Picture/bg - Copy.png';
                pannellum.viewer('panorama', {
                    "type": "equirectangular",
                    "panorama": imageSrc,
                    "autoLoad": true,
                    "autoRotate": 5,
                    "showControls": false,
                    "mouseZoom": false,
                    "keyboardZoom": false,
                    "showFullscreenCtrl": false,
                    "showZoomCtrl": false,
                    "showCompass": false,
                    "disableKeyboardCtrl": true,
                    "hfov": 110, // Adjust field of view to fit image better
                    "minHfov": 50,
                    "maxHfov": 120
                });
            })
            .catch(error => {
                console.error('Error loading Base64 from text file:', error);
            });
    </script>

</body>

</html>