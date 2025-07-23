<?php
include 'connection.php';
$s1 = "";
$res_conf = []; // Initialize the variable to avoid undefined variable error

// Fetch all records when the page loads
$squery = "SELECT brgy_Name, citymun_Name, province_Name
           FROM ref_brgy rb
           LEFT JOIN ref_citymun rf ON rb.citymun_Code = rf.citymun_Code
           LEFT JOIN ref_province rp ON rp.province_Code = rb.province_Code
           ORDER BY brgy_Name ASC";

$res_conf = mysqli_query($db, $squery);
$conf_check = mysqli_num_rows($res_conf);

// Handle form submission
if (isset($_POST['labuleh'])) {
  $brgy_Name = $_POST['a1'];
  $citymun_Name = $_POST['a2'];
  $province_Name = $_POST['a3'];

  if (empty($brgy_Name)) {
    $s1 = "Null!! Please Search and Select from the table!";
  } else {
    $sql_sub = "UPDATE brgy_address_info SET brgy_Name='$brgy_Name',
                     citymun_Name='$citymun_Name', province_Name='$province_Name'
                     WHERE caller_Code='setter'";

    if (mysqli_query($db, $sql_sub)) {
      $s1 = "Update Success!!";
    } else {
      $s1 = "Update Failed";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Set up Barangay Address</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

</head>

<body>
  <div ng-app="app" ng-controller="ctrl" class="wrapper">
    <nav class="navbar navbar-expand-lg" style="background: #14aa6c">
      <a class="navbar-brand text-white font-weight-bold" href="#">Barangay Address</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link text-white font-weight-bold" href="index.php">Back</a>
          </li>
        </ul>
      </div>
    </nav>

    <section class="sec1 mt-4">
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <div class="table-responsive border p-4 rounded bg-light">
              <table id="tableex" class="table table-striped table-bordered" width="100%">
                <thead>
                  <tr>
                    <th class="text-center">Barangay</th>
                    <th class="text-center">Municipality</th>
                    <th class="text-center">Province</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($conf_check > 0): ?>
                    <?php while ($row6 = mysqli_fetch_assoc($res_conf)): ?>
                      <tr>
                        <td><?php echo $row6['brgy_Name']; ?></td>
                        <td><?php echo $row6['citymun_Name']; ?></td>
                        <td><?php echo $row6['province_Name']; ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="3" class="text-center">No Records Found!</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-md-4">
            <form action="setter.php" method="post" class="bg-white p-4 rounded border">
              <div class="form-group">
                <label for="a1">Barangay Name:</label>
                <input class="form-control" type="text" id="a1" readonly="readonly" name="a1">
              </div>
              <div class="form-group">
                <label for="a2">Municipality:</label>
                <input class="form-control" type="text" id="a2" readonly="readonly" name="a2">
              </div>
              <div class="form-group">
                <label for="a3">Province:</label>
                <input class="form-control" type="text" id="a3" readonly="readonly" name="a3">
              </div>
              <button class="btn btn-success btn-block" type="submit" id="labuleh" name="labuleh">Submit</button>
            </form>

            <div class="warning text-danger mt-2 text-center">
              <?php echo $s1; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#tableex').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true
      });

      // Row click event to fill form fields
      $('#tableex tbody').on('click', 'tr', function () {
        var rowData = $(this).children('td').map(function () {
          return $(this).text();
        }).get();

        $('#a1').val(rowData[0]);
        $('#a2').val(rowData[1]);
        $('#a3').val(rowData[2]);
      });
    });
  </script>
</body>

</html>