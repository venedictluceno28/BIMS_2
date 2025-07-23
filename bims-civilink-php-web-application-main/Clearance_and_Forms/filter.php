<?php
$s3 = "";
include 'connection.php';

if (isset($_POST['print'])) {
  $startd = $_POST['from'];
  $endd = $_POST['to'];
  if ($startd == "" || $endd == "") {
    $s3 = "Please choose a date range to print!";
  } else {
    header("Location:logs.php?startd=$startd&endd=$endd");
  }
}
$s1 = "";
?>

<?php
include 'connection.php';

$s3 = "";
if (isset($_POST['print'])) {
  $startd = $_POST['from'];
  $endd = $_POST['to'];
  if (empty($startd) || empty($endd)) {
    $s3 = "Please choose a date range to print!";
  } else {
    header("Location: logs.php?startd=$startd&endd=$endd");
    exit(); // Use exit after header redirect
  }
}

$s1 = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Forms and Clearances - Released Forms</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }

    nav {
      background-color: #14aa6c;
    }

    .nav-link,
    .logo {
      color: white !important;
      font-weight: bold;
    }

    .container {
      margin-top: 50px;
    }

    h3 {
      color: #14aa6c;
      text-align: center;
      margin-bottom: 20px;
    }

    .warning {
      color: red;
      font-weight: bold;
      text-align: center;
      margin-top: 10px;
    }

    .btn-success,
    .btn-primary {
      width: 48%;
      margin-top: 10px;
    }

    .table-container {
      margin-top: 20px;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand logo" href="#">Filter Log to Print</a>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Back</a>
      </li>
    </ul>
  </nav>

  <!-- Main Section -->
  <section class="sec1">
    <div class="container">
      <div class="jumbotron">

        <!-- Form Section -->
        <form method="post" class="form-group">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="from">From Date</label>
              <input type="date" class="form-control" name="from" placeholder="From Date">
            </div>
            <div class="form-group col-md-6">
              <label for="to">To Date</label>
              <input type="date" class="form-control" name="to" placeholder="To Date">
            </div>
          </div>

          <button type="submit" name="search" class="btn btn-primary">Search</button>
          <button type="submit" name="print" class="btn btn-success">Print</button>
        </form>

        <!-- Table Section -->
        <div class="table-container">
          <?php
          if (isset($_POST['search'])) {
            $startd = $_POST['from'];
            $endd = $_POST['to'];

            // Prepared statement to prevent SQL injection
            $stmt = $db->prepare("
                            SELECT res_fName, res_mName, res_lName, release_Date
                            FROM form_release
                            LEFT JOIN resident_detail rd ON form_release.res_ID = rd.res_ID
                            WHERE release_Date BETWEEN ? AND ?
                        ");
            $stmt->bind_param("ss", $startd, $endd);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->num_rows;
            ?>
            <table id="releasedFormsTable" class="table table-striped table-bordered text-center">
              <thead class="thead-dark">
                <tr>
                  <th>Name</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($count > 0) {
                  while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                      <td>
                        <?php echo htmlspecialchars($row['res_fName'] . " " . $row['res_mName'] . " " . $row['res_lName']); ?>
                      </td>
                      <td><?php echo htmlspecialchars($row['release_Date']); ?></td>
                    </tr>
                    <?php
                  }
                } else {
                  echo "<tr><td colspan='2' class='warning'>No Record Found!</td></tr>";
                }
                ?>
              </tbody>
            </table>
            <?php
            $stmt->close(); // Close the prepared statement
          }
          ?>

          <!-- Warning Message -->
          <div class="warning">
            <?php echo $s3; ?>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- JavaScript -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#releasedFormsTable').DataTable();
    });
  </script>
</body>

</html>