<?php
session_start();
include("session.php");
include 'connection.php';
$s1 = "";
$s2 = "";
$s3 = "";

if (isset($_POST['verify_Res'])) {
  $var_id = $_POST['a1'];
  if (empty($var_id)) {
    $s2 = "Please Select Resident First!";
  } else {
    $sql_blot = "SELECT * FROM ms_incident msi
                    LEFT JOIN ms_incident_offender mso ON msi.incident_id = mso.incident_id
                    WHERE msi.status!=5 AND mso.res_ID='$var_id'";

    $result1 = mysqli_query($db, $sql_blot);
    $resultCheck1 = mysqli_num_rows($result1);

    if ($resultCheck1 > 0) {
      $s1 = "Denied!! Unsettled Case detected!";
    } else {
      $s1 = "Resident is clear!";
    }

  }
}
if (isset($_POST['labuleh'])) {
  $var_id = $_POST['a1'];
  $var_or = $_POST['or'];
  $var_crc = $_POST['crc'];
  if (empty($var_or) || empty($var_crc)) {
    $s2 = "Please input CTC or OR no.";
  } else {
    if (empty($var_id)) {
      $var_id = $_POST['a1'];
      $var_forms = $_POST['forms'];
      $s2 = "Please Search & Select resident!";
      if (empty($var_or) && $var_forms == "Film Making Permit" || $var_forms == "Film Making" || $var_forms == "Barangay Film Making" || $var_forms == "Barangay Film Making Permit") {
        $var_or = $_POST['or'];
        $var_crc = $_POST['crc'];
        header("Location:Creator/CreateFilmMakingPermit.php?or=$var_or&crc=$var_crc");
      }
      $var_or = $_POST['or'];
      $var_crc = $_POST['crc'];
      if (empty($var_id) && $var_forms == "Barangay Transient Information" || $var_forms == "Transient Information" || $var_forms == "Barangay Transient") {
        header("Location:Creator/CreateTransientInformation.php?or=$var_or&ctc=$var_crc");
      }
      if (empty($var_id) && $var_forms == "Working Permit" || $var_forms == "Barangay Working Permit") {
        header("Location:Creator/CreateWorkingPermit.php?res_ID=$var_id&or=$var_or&crc=$var_crc");

      }
    } else {

      $var_forms = $_POST['forms'];
      $var_or = $_POST['or'];
      $var_crc = $_POST['crc'];

      $sql_blot = "SELECT * FROM ms_incident msi
                    LEFT JOIN ms_incident_offender mso ON msi.incident_id = mso.incident_id
                    WHERE msi.status!=5 AND mso.res_ID='$var_id'";

      $result1 = mysqli_query($db, $sql_blot);
      $resultCheck1 = mysqli_num_rows($result1);

      if ($resultCheck1 > 0) {
        $s1 = "Can't Issue Clearance!! Please Check the Blotter Records";
      } else {

        if ($var_forms == "Barangay Clearance") {
          header("Location:Clearances/BarangayClearance.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        } else if ($var_forms == "Certificate of Indigency") {
          header("Location:Clearances/CertificateOfIndigency.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        } else if ($var_forms == "Residency Certificate") {
          header("Location:Clearances/ResidencyCertificate.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        }

        // else if ($var_forms == "Building Permit" || $var_forms == "Barangay Building Permit") {
        //   header("Location:Creator/CreateBuildingPermit.php?res_ID=$var_id&or=$var_or&ctc=$var_crc");
        // } else if ($var_forms == "Barangay Business Permit" || $var_forms == "Business Permit") {
        //   header("Location:Creator/CreateBusinessPermit.php?res_ID=$var_id&or=$var_or&ctc=$var_crc");
        // } else if ($var_forms == "Barangay Logging" || $var_forms == "Logging Permit" || $var_forms == "Logging" || $var_forms == "Tree Cutting" || $var_forms == "Cutting Trees") {
        //   header("Location:Clearances/CuttingTrees.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        // } else if ($var_forms == "Electrical Permit" || $var_forms == "Barangay Electrical Permit") {
        //   header("Location:Clearances/ElectricalPermit.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        // } else if ($var_forms == "Barangay Fencing" || $var_forms == "Fencing" || $var_forms == "Fencing Permit" || $var_forms == "Barangay Fencing Permit") {
        //   header("Location:Creator/CreateFencingPermit.php?res_ID=$var_id&or=$var_or&ctc=$var_crc");
        // } else if ($var_forms == "Working Permit" || $var_forms == "Barangay Working Permit") {
        //   header("Location:Creator/CreateWorkingPermit.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        // } else if ($var_forms == "Film Making" || $var_forms == "Film Making Permit" || $var_forms == "Shooting Permit") {
        //   header("Location:Creator/CreateFilmMakingPermit.php?res_ID=$var_id&or=$var_or&crc=$var_crc");
        // } else if ($var_forms == "Barangay Transient Information" || $var_forms == "Transient Information" || $var_forms == "Barangay Transient") {
        //   header("Location:Creator/CreateTransientInformation.php?or=$var_or&ctc=$var_crc");
        // } else {
        //   $s3 = "template not available!! ";
        // }
      }


    }
  }

} ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Forms and Clearances</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
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
  </style>
  
</head>

<body >
  <style>
    body, html {
      background: #A0C49D !important;
    }
    .container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 30px;
    }
  </style>
  <!-- Font Awesome CDN for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand font-weight-bold" href="#">
      <i class="fa fa-file-alt"></i> Forms and Clearances
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link" href="inputLogo.php"><i class="fa fa-image"></i> Input Logo</a></li>
        <!-- <li class="nav-item"><a class="nav-link" href="filter.php">Logs</a></li> -->
        <li class="nav-item"><a class="nav-link" href="request.php"><i class="fa fa-envelope"></i> Request</a></li>
      </ul>
    </div>
  </nav>

  <div class="container mt-4">

    <div class="row">
      <!-- Table Section -->
      <div class="col-md-9 table-container">
        <table id="tableex" class="table table-striped table-responsive">
          <thead>
            <tr>
              <th>Blotter Record</th>
              <th>First Name</th>
              <th>Middle Name</th>
              <th>Last Name</th>
              <th>Civil Status</th>
              <th>Phase</th>
              <th>Lot</th>
              <th>Block</th>
              <th>House No.</th>
              <th>Street</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $squery = "SELECT resident_detail.res_ID, res_fName, res_mName, res_lName, marital_Name,
                          address_Lot_No, address_Block_No, address_Phase_No, address_House_No, address_Street_Name
                    FROM resident_detail
                    LEFT JOIN resident_address ON resident_detail.res_ID = resident_address.res_ID
                    LEFT JOIN ref_marital_status ON resident_detail.marital_ID = ref_marital_status.marital_ID
                    ORDER BY res_lName ASC";

            $res_conf = mysqli_query($db, $squery);

            if (mysqli_num_rows($res_conf) > 0) {
              while ($row6 = mysqli_fetch_assoc($res_conf)) {
                $var_id = $row6['res_ID'];

                // Check for unsettled cases
                $sql_blot = "SELECT * FROM ms_incident msi
                             LEFT JOIN ms_incident_offender mso ON msi.incident_id = mso.incident_id
                             WHERE msi.status != 5 AND mso.res_ID = '$var_id'";
                $result1 = mysqli_query($db, $sql_blot);
                $blotterRecord = (mysqli_num_rows($result1) > 0) ? "Unsettled Case Detected!" : "Resident is clear!";

                echo "<tr style='cursor:pointer;' data-resid='{$row6['res_ID']}'>
                          <td>{$blotterRecord}</td>
                          <td>{$row6['res_fName']}</td>
                          <td>{$row6['res_mName']}</td>
                          <td>{$row6['res_lName']}</td>
                          <td>{$row6['marital_Name']}</td>
                          <td>{$row6['address_Phase_No']}</td>
                          <td>{$row6['address_Lot_No']}</td>
                          <td>{$row6['address_Block_No']}</td>
                          <td>{$row6['address_House_No']}</td>
                          <td>{$row6['address_Street_Name']}</td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='10' class='text-center'>No residents found.</td></tr>";
            }
            ?>
          </tbody>
        </table>

      </div>


      <div class="col-md-3">
        <form action="index.php" method="post">
          <input type="hidden" id="a1" readonly name="a1">
          <div class="form-group">
            <label>First Name</label>
            <input class="form-control" id="a2" readonly name="a2">
          </div>
          <div class="form-group">
            <label>Middle Name</label>
            <input class="form-control" id="a3" readonly name="a3">
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input class="form-control" id="a4" readonly name="a4">
          </div>
          <div class="form-group">
            <label>Select Clearance/Form</label>
            <select class="form-control" id="sel1" name="forms">
              <?php
              $sql_ret = "SELECT clearance_form FROM finance_clearance_list
                            LEFT JOIN finance_clearance_set ON finance_clearance_list.clearance_id = finance_clearance_set.clearance_id
                            WHERE clearance_form IN ('Barangay Clearance', 'Certificate of Indigency', 'Residency Certificate')";
              $result_ret = mysqli_query($db, $sql_ret);

              while ($row_ret = mysqli_fetch_assoc($result_ret)) {
                echo "<option value='{$row_ret['clearance_form']}'>{$row_ret['clearance_form']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label>OR Number</label>
            <input class="form-control" type="number" required id="or" placeholder="O.R Number" name="or">
          </div>
          <div class="form-group">
            <label>CTC Number</label>
            <input class="form-control" type="number" required id="crc" placeholder="CTC Number" name="crc">
          </div>
          <!-- <button type="submit" class="btn btn-success" name="verify_Res">Verify Resident</button> -->
          <button type="submit" class="btn btn-success" name="labuleh">Generate Document</button>
        </form>
        <div class="warning">
          <?php echo $s1;
          echo $s2;
          echo $s3; ?>
        </div>
      </div>
      <!-- Form Section -->

    </div>
  </div>

  <!-- jQuery and DataTables Scripts -->
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

      $('#tableex tbody').on('click', 'tr', function () {
        var row_data = $(this).children('td').map(function () { return $(this).text(); }).get();
        var resID = $(this).data('resid');  // Get the resident ID from the data attribute
        $('#a1').val(resID);                // Set the value of resID
        $('#a2').val(row_data[1]);
        $('#a3').val(row_data[2]);
        $('#a4').val(row_data[3]);
      });
    });
  </script>

</body>

</html>