<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Collect account info
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $contact = mysqli_real_escape_string($db, $_POST['contact']);
    $fullname = mysqli_real_escape_string($db, $_POST['fullname']);

    // Collect resident_detail info
    $res_fName = mysqli_real_escape_string($db, $_POST['res_fName']);
    $res_mName = mysqli_real_escape_string($db, $_POST['res_mName']);
    $res_lName = mysqli_real_escape_string($db, $_POST['res_lName']);
    $res_Bday = isset($_POST['res_Bday']) ? mysqli_real_escape_string($db, $_POST['res_Bday']) : null;

    // Default IDs
    $position_ID = 1;
    $suffix_ID = 1;
    $marital_ID = 1;
    $country_ID = 1;
    $gender_ID = 1;
    $religion_ID = 1;
    $occupation_ID = 1;
    $occuStat_ID = 1;
    $occupant_ID = 1;

    // Insert into accounts
    // Get next resident_id
    $resIdQuery = "SELECT MAX(res_ID) AS max_id FROM resident_detail";
    $resIdResult = mysqli_query($db, $resIdQuery);
    $nextResidentId = 1;
    if ($resIdResult && mysqli_num_rows($resIdResult) > 0) {
        $row = mysqli_fetch_assoc($resIdResult);
        if ($row['max_id'] !== null) {
            $nextResidentId = $row['max_id'] + 1;
        }
    }

    $sqlAcc = "INSERT INTO accounts (Username, Password, Emailaddress, Fullname, type, device_Id, Position, Committee, arc, resident_id) VALUES ('$username', '$password', '$email', '$fullname', 1, 0, 0, 0, 0, $nextResidentId)";
    $accResult = mysqli_query($db, $sqlAcc);

    if ($accResult) {
        // Insert into resident_detail (no height/weight)
        if (empty($res_Bday)) {
            echo "<script>alert('Birthday is required.');</script>";
        } else {
            $sqlRes = "INSERT INTO resident_detail (
                res_fName, res_mName, res_lName, position_ID, suffix_ID, res_Bday, marital_ID, country_ID, gender_ID, religion_ID, occupation_ID, occuStat_ID, occupant_ID, Status
            ) VALUES (
                '$res_fName', '$res_mName', '$res_lName', $position_ID, $suffix_ID, '$res_Bday', $marital_ID, $country_ID, $gender_ID, $religion_ID, $occupation_ID, $occuStat_ID, $occupant_ID, 'Active'
            )";
            $resResult = mysqli_query($db, $sqlRes);

            if ($resResult) {
                echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Error inserting resident details: " . mysqli_error($db) . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Error creating account: " . mysqli_error($db) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Management Information System - Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="Picture/banaba.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #A0C49D;
        }
        .overlay-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,60,0,0.6);
            z-index: 1;
        }
        .register-main {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .register-box {
            background: #fff;
            padding: 30px 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .register-box img {
            display: block;
            margin: 0 auto 15px;
        }
        .form-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        @media (max-width: 600px) {
            .register-box { padding: 15px 5px; }
        }
    </style>
    <script>
        function updateFullName() {
            const fn = document.querySelector('input[name="res_fName"]').value.trim();
            const mn = document.querySelector('input[name="res_mName"]').value.trim();
            const ln = document.querySelector('input[name="res_lName"]').value.trim();
            let fullName = fn;
            if (mn) fullName += ' ' + mn;
            if (ln) fullName += ' ' + ln;
            document.querySelector('input[name="fullname"]').value = fullName;
        }
        document.addEventListener('DOMContentLoaded', function() {
            const fn = document.querySelector('input[name="res_fName"]');
            const mn = document.querySelector('input[name="res_mName"]');
            const ln = document.querySelector('input[name="res_lName"]');
            fn.addEventListener('input', updateFullName);
            mn.addEventListener('input', updateFullName);
            ln.addEventListener('input', updateFullName);
        });
    </script>
</head>
<body>
    <div class="overlay-bg"></div>
    <div class="register-main">
        <div class="register-box">
            <h3 class="text-center">Register Account</h3>
            <img src="Picture/banaba.png" height="80" width="80" alt="Logo">
            <form method="POST" action="register.php">
                <div class="form-section-title">Account & Resident Info</div>
                <div class="form-group">
                    <input type="text" name="username" class="form-control shadow-none" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control shadow-none" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control shadow-none" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="contact" class="form-control shadow-none" placeholder="Contact" required>
                </div>
                <div class="form-group">
                    <input type="text" name="fullname" class="form-control shadow-none" placeholder="Full Name" required readonly>
                </div>
                <div class="form-group">
                    <input type="text" name="res_fName" class="form-control shadow-none" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" name="res_mName" class="form-control shadow-none" placeholder="Middle Name">
                </div>
                <div class="form-group">
                    <input type="text" name="res_lName" class="form-control shadow-none" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <label for="res_Bday">Birthday</label>
                    <input type="date" name="res_Bday" id="res_Bday" class="form-control shadow-none" placeholder="Birthday">
                </div>
                <button type="submit" name="register" class="btn btn-primary btn-block shadow-none">Register</button>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-link shadow-none">Already have an account? Sign In</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
