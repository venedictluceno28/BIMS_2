<?php
session_start();
include('../Resident_Profiling/connections.php');

if (isset($_GET['id']) && isset($_GET['table']) && isset($_GET['status'])) {
  $id = intval($_GET['id']);  // Ensure the ID is an integer for security.
  $table = $_GET['table'];    // Table name from the URL (assumed to be safe).
  $status = intval($_GET['status']); // Get the status from the URL and ensure it's an integer.

  // Update the status in the specified table.
  $query = "UPDATE $table SET status = ? WHERE id = ?";
  $stmt = $db->prepare($query);

  if ($stmt) {
    // Bind the status and id parameters.
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
    } else {
    }

    $stmt->close();
  } else {
    echo "Failed to prepare the statement.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

  <title>Clearance and Forms</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    nav {
      background-color: #14aa6c;
    }

    .nav-link {
      color: white !important;
    }

    .table-container {
      margin-top: 20px;
    }

    h2,
    h3 {
      color: #14aa6c;
    }

    .btn-success {
      margin-top: 10px;
    }

    .warning {
      color: red;
      font-weight: bold;
      text-align: center;
      margin-top: 20px;
    }

    .nav-link,
    .logo {
      color: white !important;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand font-weight-bold" href="#">Forms and Clearances</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Back</a></li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid mt-5">

    <table id="requestsTable" class="table table-striped">
      <thead>
        <tr>
          <th>Release Code</th>
          <th>Name</th>
          <th>Date of Birth</th>
          <th>Contact Number</th>
          <th>Form</th>
          <th>Email</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT id, request_code,status,first_name, last_name, date_of_birth, contact_number, clearance_form, email, created_at 
      FROM request_forms 
      ORDER BY status DESC, id ASC";


        $result = mysqli_query($db, $query);

        if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr data-first-name='" . htmlspecialchars($row['first_name']) . "' data-clearance-form='" . htmlspecialchars($row['clearance_form']) . "'>";
            echo "<td>" . htmlspecialchars($row['request_code']) . "</td>";
            echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
            echo "<td class='contact-number'>" . htmlspecialchars($row['contact_number']) . "</td>";
            echo "<td class='clearance-form'>" . htmlspecialchars($row['clearance_form']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            $status = $row['status'];
            $statusText = '';

            switch ($status) {
              case 0:
                $statusText = 'Pending';
                break;
              case 1:
                $statusText = 'Ready';
                break;
              case 2:
                $statusText = 'Released';
                break;
              default:
                $statusText = 'Unknown';
                break;
            }

            echo "<td>" . htmlspecialchars($statusText) . "</td>";

            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo '<td><button class="btn btn-success send-sms-btn">Send SMS</button> <a class="btn btn-warning" href="request.php?id=' . $row['id'] . '&table=request_forms&status=1">Ready</a>
<a class="btn btn-primary" href="request.php?id=' . $row['id'] . '&table=request_forms&status=2">Released</a>
</td>';

            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='7' class='warning'>No data found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- SMS Modal -->
  <div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="smsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="smsModalLabel">Send SMS</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="smsForm">
            <div class="form-group">
              <label for="phoneNumber">Phone Number</label>
              <input type="text" class="form-control" id="phoneNumber">
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea class="form-control" id="message" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="sendSmsButton">Send SMS</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      $('#requestsTable').DataTable();

      $('.send-sms-btn').on('click', function () {
        const contactNumber = $(this).closest('tr').find('.contact-number').text();
        const firstName = $(this).closest('tr').data('first-name');
        const clearanceForm = $(this).closest('tr').data('clearance-form');

        $('#phoneNumber').val(contactNumber);
        $('#message').val(`Good day, ${firstName}. We are pleased to inform you that your ${clearanceForm} request is ready for pickup. Please proceed to Project 6 Barangay Hall. Thank you!`);

        $('#smsModal').modal('show');
      });

      $('#sendSmsButton').on('click', function () {
        const phoneNumber = $('#phoneNumber').val();
        const message = $('#message').val();

        if (phoneNumber && message) {
          $.ajax({
            url: '../sms.php',
            method: 'POST',
            dataType: 'json',
            data: {
              phoneNumber: phoneNumber,
              message: message
            },
            success: function (response) {
              if (response.status === 'error') {
                alert('Error: ' + response.message);
              } else {
                alert('SMS sent successfully!');
                $('#smsModal').modal('hide');
              }
            },
            error: function () {
              alert('Failed to send SMS. Please try again.');
            }
          });
        } else {
          alert('Please enter both phone number and message.');
        }
      });
    });
  </script>

</body>

</html>