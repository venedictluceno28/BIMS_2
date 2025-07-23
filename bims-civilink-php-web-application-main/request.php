<?php
session_start();
include 'Clearance_and_Forms/connection.php';

if (isset($_POST['submit_request'])) {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $date_of_birth = $_POST['date_of_birth'];
  $contact_number = $_POST['contact_number'];
  $form = $_POST['forms'];
  $email = $_POST['email'];

  // Generate random request code in 4-4-4-4 format
  function generateRequestCode()
  {
    return strtoupper(implode('-', array_map(
      fn() => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4),
      range(1, 4)
    )));
  }

  // Generate the request code
  $code = generateRequestCode();

  // Dynamically fetch the current scheme and host
  $current_url = "http";
  if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    $current_url .= "s"; // If HTTPS, add 's' to make it https://
  }
  $current_url .= "://";
  $current_url .= $_SERVER["HTTP_HOST"]; // Host (domain)
  $current_url .= $_SERVER["REQUEST_URI"]; // Request URI (path and query string)


  // Construct the dynamic URL
  $request_code = $current_url . '?id=' . $code;

  // Insert the request into the database
  $sql = "INSERT INTO request_forms (first_name, last_name, date_of_birth, contact_number, clearance_form, email, request_code)
            VALUES ('$first_name', '$last_name', '$date_of_birth', '$contact_number', '$form', '$email', '$code')";

  if (mysqli_query($db, $sql)) {
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=$request_code&size=150x150";
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Request Submitted!',
            html: `<p>Your request code is:</p><h3>$code</h3>
                   <p>Scan the QR code below to track request:</p>
                   <img src='{$qrCodeUrl}' alt='QR Code'>`,
            icon: 'success',
            confirmButtonText: 'OK'
        });
      });
    </script>";

  } else {
    $error_message = mysqli_error($db);
    echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred: $error_message',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
  }
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Fetch the data from the database based on the ID
  $sql = "SELECT * FROM request_forms WHERE request_code = '$id'";
  $result = mysqli_query($db, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the request data
    $request = mysqli_fetch_assoc($result);

    $first_name = $request['first_name'];
    $last_name = $request['last_name'];
    $request_code = $request['request_code'];
    $clearance_form = $request['clearance_form'];
    $status = $request['status']; // assuming the status field exists

    // Set the status message based on the value of status
    $status_message = '';
    switch ($status) {
      case 0:
        $status_message = 'Pending';
        break;
      case 1:
        $status_message = 'Ready';
        break;
      case 2:
        $status_message = 'Released';
        break;
      default:
        $status_message = 'Unknown status';
        break;
    }

    // Generate the SweetAlert script to display the message
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Request Status',
                    html: `<p>The Request clearance form for <b>$first_name $last_name</b> with request code <b>$request_code</b> is <b>$status_message</b>.</p>`,
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Request ID not found.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Form</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- Ensure SweetAlert2 is loaded before the script calls it -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .form-control:focus {
      box-shadow: none !important;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      background: url('bgrequest.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.8);
    }

    .container {
      position: relative;
      z-index: 1;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    nav {
      background-color: #14aa6c;
    }

    .nav-link {
      color: white !important;
    }

    h2 {
      color: #14aa6c;
    }

    .btn-success {
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="overlay"></div>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand font-weight-bold" href="#">Request Forms and Clearances</a>
  </nav>

  <div class="container mt-4">
    <h2>Request Form</h2>
    <form action="request.php" method="post">
    <div class="form-group">
    <label>First Name</label>
    <input 
      class="form-control" 
      type="text" 
      name="first_name" 
      required 
      id="first_name"
      oninput="validateText(this)"
    >
  </div>
  <div class="form-group">
    <label>Last Name</label>
    <input 
      class="form-control" 
      type="text" 
      name="last_name" 
      required 
      id="last_name"
      oninput="validateText(this)"
    >
  </div>

  <script>
  function validateText(input) {
    input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
  }
  </script>

      <div class="form-group">
        <label>Date of Birth</label>
        <input class="form-control" type="date" name="date_of_birth" required>
      </div>
      <div class="form-group">
        <label>Contact Number</label>
        <input class="form-control" name="contact_number" placeholder="Contact Number" required value="63" type="tel"
          pattern="\639\d{9}"
          oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12); if (!this.value.startsWith('63')) this.value = '63' + this.value.slice(2);">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Select Clearance/Form</label>
        <select class="form-control" name="forms" required>
          <option value="Barangay Clearance">Barangay Clearance</option>
          <option value="Certificate of Indigency">Certificate of Indigency</option>
          <option value="Residency Certificate">Residency Certificate</option>
          <option value="Barangay ID">Barangay ID</option>
          <option value="Barangay Permit">Barangay Permit</option>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary" name="submit_request">Submit</button>
      <button class="btn btn-primary" id="trackRequestBtn">Track Request</button>

    </form>

    <!-- Track Request Button -->
  </div>

  <script>
    $(document).ready(function () {
      $('#trackRequestBtn').on('click', function () {
        Swal.fire({
          title: 'Enter your Tracking ID',
          input: 'text',
          inputPlaceholder: 'Tracking ID',
          showCancelButton: true,
          confirmButtonText: 'Track',
          cancelButtonText: 'Cancel',
          inputValidator: (value) => {
            if (!value) {
              return 'Please enter a tracking ID';
            }
          }
        }).then((result) => {
          if (result.isConfirmed) {
            const trackingID = result.value;
            const currentURL = window.location.href.split('?')[0]; // Get the current URL without query parameters
            window.location.href = `${currentURL}?id=${trackingID}`; // Redirect with tracking ID as query parameter
          }
        });
      });
    });
  </script>
</body>

</html>