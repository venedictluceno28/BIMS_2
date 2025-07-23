<?php
session_start();
require_once('connections.php');

// Function to get max ID from a table
function getMaxId($db, $table, $column)
{
  $result = mysqli_query($db, "SELECT MAX($column) AS max_id FROM `$table`");
  $row = mysqli_fetch_array($result);
  return $row['max_id'] + 1;
}

// Function to get reference ID from a lookup table
function getRefId($db, $table, $idColumn, $nameColumn, $value)
{
  $query = "SELECT $idColumn FROM `$table` WHERE $nameColumn = '$value'";
  $result = mysqli_query($db, $query);
  $row = mysqli_fetch_array($result);
  return $row[$idColumn] ?? null;
}

// Fetch max IDs for various tables
$rid = getMaxId($db, 'resident_detail', 'res_ID');
$oid = getMaxId($db, 'ref_occupation', 'occupation_ID');
$aid = getMaxId($db, 'resident_address', 'address_ID');
$cid = getMaxId($db, 'resident_contact', 'contact_ID');

// Initialize variables
$res_fname = $res_mname = $res_lname = $res_suffix = $res_gender = $res_bdate = "";
$res_civilstatus = $res_contactnum = $res_contacttype = $res_religion = "";
$res_occupationstatus = $res_occupation = $res_height = $res_weight = "";
$res_citizenship = $res_houseno = $res_purokno = $res_region = "";
$res_address = $res_brgy = $res_building = $res_lot = $res_block = "";
$res_phase = $res_street = $res_subd = $res_unit = "0";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and assign form data
  $res_fname = mysqli_real_escape_string($db, $_POST["res_fname"] ?? '');
  $res_mname = mysqli_real_escape_string($db, $_POST["res_mname"] ?? '');
  $res_lname = mysqli_real_escape_string($db, $_POST["res_lname"] ?? '');
  $res_suffix = mysqli_real_escape_string($db, $_POST["res_suffix"] ?? '');
  $res_gender = mysqli_real_escape_string($db, $_POST["res_gender"] ?? '');
  $res_bdate = mysqli_real_escape_string($db, $_POST["res_bdate"] ?? '');
  $res_civilstatus = mysqli_real_escape_string($db, $_POST["res_civilstatus"] ?? '');
  $res_contactnum = mysqli_real_escape_string($db, $_POST["res_contactnum"] ?? '');
  $res_contacttype = mysqli_real_escape_string($db, $_POST["res_contacttype"] ?? '');
  $res_religion = mysqli_real_escape_string($db, $_POST["res_religion"] ?? '');
  $res_occupationstatus = mysqli_real_escape_string($db, $_POST["res_occupationstatus"] ?? '');
  $res_occupation = mysqli_real_escape_string($db, $_POST["res_occupation"] ?? '0');
  $res_height = mysqli_real_escape_string($db, $_POST["res_height"] ?? '0');
  $res_weight = mysqli_real_escape_string($db, $_POST["res_weight"] ?? '0');
  $res_height = !empty($res_height) ? $res_height : '0';
  $res_weight = !empty($res_weight) ? $res_weight : '0';
  $res_citizenship = mysqli_real_escape_string($db, $_POST["res_citizenship"] ?? '');
  $res_unit = mysqli_real_escape_string($db, $_POST["res_unit"] ?? '0');
  $res_houseno = mysqli_real_escape_string($db, $_POST["res_houseno"] ?? '');
  $res_street = mysqli_real_escape_string($db, $_POST["res_street"] ?? '');
  $res_subd = mysqli_real_escape_string($db, $_POST["res_subd"] ?? '');
  $res_purokno = mysqli_real_escape_string($db, $_POST["res_purokno"] ?? '');
  $res_region = mysqli_real_escape_string($db, $_POST["res_region"] ?? '');
  $res_lot = mysqli_real_escape_string($db, $_POST["res_lot"] ?? '');
  $res_address = mysqli_real_escape_string($db, $_POST["res_address"] ?? '');
  $res_block = mysqli_real_escape_string($db, $_POST["res_block"] ?? '');
  $file = addslashes(file_get_contents($_FILES["image"]["tmp_name"] ?? ''));
  $res_email = mysqli_real_escape_string($db, $_POST["res_email"] ?? '');
  $res_password = mysqli_real_escape_string($db, $_POST["res_password"] ?? '');

  // Insert into ref_occupation if a new occupation is provided
  if (!empty($_POST["res_trabaho"])) {
    $query = "INSERT INTO ref_occupation (occupation_Name, occupation_ID) VALUES ('$res_trabaho', '$oid')";
    mysqli_query($db, $query);
    $res_occupation = $oid; // Use generated occupation ID after insert
  }

  // echo "<pre>";
  // print_r($_POST);
  // echo "</pre>";
  // Insert into resident_detail
  $query = "INSERT INTO resident_detail (res_ID, res_Img, res_fName, res_mName, res_lName, suffix_ID, gender_ID, res_Bday, marital_ID, religion_ID, res_Height, res_Weight, occuStat_ID, occupation_ID, country_ID, Status) 
              VALUES ('$rid', '$file', '$res_fname', '$res_mname', '$res_lname', '$res_suffix', '$res_gender', '$res_bdate', '$res_civilstatus', '$res_religion', '$res_height', '$res_weight', '$res_occupationstatus', '$res_occupation', '$res_citizenship', 'Active')";

  // Execute query and check for errors
  if (!mysqli_query($db, $query)) {
    // Handle error accordingly, maybe log it or display a user-friendly message
    // echo "Error: " . mysqli_error($db);
  }

  $fullname = $res_fname . ' ' . $res_mname . ' ' . $res_lname;
  // Use $rid (resident_detail max + 1) as resident_id in accounts
  $query = "INSERT INTO accounts (resident_id, Fullname, Username, Emailaddress, device_Id, Password, Position, Committee, position_id, arc, type) 
              VALUES ('$rid', '$fullname', '$res_email', '$res_email', '', '$res_password', '', '', '0', '0', '1')";


  // Execute query and check for errors
  if (!mysqli_query($db, $query)) {
    // Handle error accordingly, maybe log it or display a user-friendly message
    // echo "Error: " . mysqli_error($db);
  }

  // Insert into resident_contact
  $query = "INSERT INTO resident_contact (contact_ID, contact_telnum, res_ID, contactType_ID, country_ID) 
              VALUES ('$cid', '$res_contactnum', '$rid', '$res_contacttype', '$res_citizenship')";

  // Execute query and check for errors
  if (!mysqli_query($db, $query)) {
    // Handle error accordingly, maybe log it or display a user-friendly message
    // echo "Error: " . mysqli_error($db);
  }

  // Insert into resident_address
  $query = "INSERT INTO resident_address (address_ID, address_Unit_Room_Floor_num, res_ID, address_BuildingName, address_Lot_No, address_Block_No, address_Phase_No, address_House_No, address_Street_Name, address_Subdivision, country_ID, purok_ID, region_ID, addressType_ID) 
              VALUES ('$aid', '$res_unit', '$rid', '$res_building', '$res_lot', '$res_block', '$res_phase', '$res_houseno', '$res_street', '$res_subd', '$res_citizenship', '$res_purokno', '1', '$res_address')";

  // Execute query and check for errors
  if (!mysqli_query($db, $query)) {
    // Handle error accordingly, maybe log it or display a user-friendly message
    // echo "Error: " . mysqli_error($db);
  }

  // Redirect to profile page
  header('Location: profile.php');
  exit();
}
?>




<script>
  function getAge() {
    var birthDate = new Date(document.getElementById('res_bdate').value);
    var today = new Date();
    var age = today.getFullYear() - birthDate.getFullYear();
    var monthDifference = today.getMonth() - birthDate.getMonth();
    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    document.getElementById('res_age').value = age;
  }
</script>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Management Information System</title>
  <!-- Bootstrap CSS -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/css/dataTables.bootstrap.min.css" rel="stylesheet">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

</head>

<body style="padding: 25px;">
  <style>
    body, html {
      background: linear-gradient(to right, #03AF34 0%, #FFF84A 100%) !important;
    }
    .container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 30px;
    }
  </style>
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <div class="container">
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #b1cbbb; color: black;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Resident</h4>
          </div>
          <form method="POST" enctype="multipart/form-data" action="<?php htmlspecialchars("PHP_SELF"); ?>">
            <div class="modal-body">

              <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
              <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
              <style type="text/css">
                .thumb-image {
                  float: left;
                  width: 250px;
                  height: 200px;
                  position: relative;
                  padding: 6px;
                }
              </style>
              <h4 style="text-align: center; font-style: normal;font-size: 18px;font-family: Verdana">RESIDENT INFORMATION
              </h4>
              <div class="form-group"
                style="text-align: center; display: flex; flex-direction: column; align-items: center;">
                <div class="upload">
                  <input required type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)" />
                  <img id="previewimage" src="images/profile.jpg" alt="Image" />
                </div>
              </div>

              <script>
                document.getElementById('image').addEventListener('change', function (event) {
                  const reader = new FileReader();
                  const preview = document.getElementById('previewimage');

                  reader.onload = function () {
                    preview.src = reader.result;
                  }

                  if (event.target.files && event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                  } else {
                    console.log('No file selected');
                  }
                });
              </script>

              <style type="text/css">
                div.upload {
                  width: 150px;
                  height: 150px;
                  background: #f0f0f0;
                  border-radius: 50%;
                  position: relative;
                  overflow: hidden;
                  text-align: center;
                  line-height: 150px;
                  cursor: pointer;
                }

                div.upload img {
                  width: 100%;
                  height: 100%;
                  object-fit: cover;
                  border-radius: 50%;
                  display: block;
                }

                div.upload input {
                  position: absolute;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  opacity: 0;
                  cursor: pointer;
                }
              </style>

              <script>
                function numbersOnly(input) {
                  var regex = /[^0-9]/gi;
                  input.value = input.value.replace(regex, "");
                }
              </script>

              <div class="clearfix"></div>
              <div required class="form-group col-md-4">
                <label for="res_fname">First name*</label>
                <input required type="text" maxlength="20" required class="form-control" id="res_fname" name="res_fname"
                  placeholder="First name" required>
              </div>

              <div class="form-group col-md-4">
                <label for="res_mname">Middle name </label>
                <input required type="text" maxlength="20" class="form-control" id="res_mname" name="res_mname"
                placeholder="Middle name">
              </div>

              <div class="form-group col-md-4">
                <label for="res_lname">Last name*</label>
                <input required type="text" maxlength="20" class="form-control" id="res_lname" name="res_lname"
                  placeholder="Last name" required>
              </div>

              <div class="form-group col-md-4">
                <label for="res_suffix">Suffix</label>
                <select class="form-control" id="res_suffix" name="res_suffix">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_suffixname");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["suffix_ID"]; ?>" l><?php echo $row["suffix"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="res_gender">Sex*</label>
                <select required class="form-control" id="res_gender" name="res_gender">
                  <option value="" disabled selected>Sex</option>
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_gender");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["gender_ID"]; ?>"><?php echo $row["gender_Name"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="res_bdate">Birthdate*</label>
                <input placeholder="Birthdate" required class="form-control" type="date" id="res_bdate" name="res_bdate"
                  onblur="getAge();">
              </div>

              <div class="form-group col-md-4">
                <label for="res_age">Age*</label>
                <input required type="number" readonly maxlength="3" class="form-control" id="res_age" placeholder="Age">
              </div>

              <div class="form-group col-md-4">
                <label for="res_civilstatus">Civil status*</label>
                <select required class="form-control" id="res_civilstatus" name="res_civilstatus">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_marital_status");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["marital_ID"]; ?>"><?php echo $row["marital_Name"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="res_contacttype">Contact type</label>
                <select class="form-control" id="res_contacttype" name="res_contacttype" onchange="maxLengthFunction()">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_contact");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["contactType_ID"]; ?>"><?php echo $row["contactType_Name"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <script>
                $(function () {
                  $("#res_contacttype").change(function () {
                    if ($(this).val() == "1") {
                      document.getElementById("res_contactnum").disabled = true;
                    }
                    else {
                      document.getElementById("res_contactnum").disabled = false;
                    }
                  });
                });
              </script>

              <div class="form-group col-md-4">
                <label for="res_contactnum">Contact</label>
                <input value="63" required type="text"   class="form-control" id="res_contactnum"
                  name="res_contactnum" onkeyup="numbersOnly(this)" placeholder="Contact number">
              </div>

              <div class="form-group col-md-4">
                <label for="res_mname">Height</label><label>
                  <font size="2">&nbsp; (Optional)</font>
                </label>
                <input type="text" class="form-control" id="res_height" name="res_height"
                  placeholder="Meter/Centimeter">
              </div>

              <div class="form-group col-md-4">
                <label for="res_mname">Weight</label><label>
                  <font size="2">&nbsp; (Optional)</font>
                </label>
                <input type="text" class="form-control" id="res_weight" name="res_weight" placeholder="Kilogram">
              </div>

              <div class="form-group col-md-4">
                <label for="res_citizenship">Citizenship*</label>
                <select required class="form-control" id="res_citizenship" name="res_citizenship">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_country where country_ID=169");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["country_ID"]; ?>"><?php echo $row["country_citizenship"]; ?>
                    </option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="res_religion">Religion*</label>
                <select required class="form-control" id="res_religion" name="res_religion">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_religion");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["religion_ID"]; ?>"><?php echo $row["religion_Name"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="res_occupationstatus">Occupation status</label>
                <select class="form-control" id="res_occupationstatus" name="res_occupationstatus">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_occupation_status");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["occuStat_ID"]; ?>"><?php echo $row["occuStat_Name"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>
              <script>
                $(function () {
                  $("#res_occupationstatus").change(function () {
                    // Change the comparison to the actual ID of "Unemployed"
                    if ($(this).val() == "6") { // replace "1" with the actual ID for "Unemployed"
                      document.getElementById("res_occupation").disabled = true;
                    } else {
                      document.getElementById("res_occupation").disabled = false;
                    }
                  });
                });
              </script>

              <div class="form-group col-md-4">
                <label for="mname">Occupation</label>
                <select class="form-control" id="res_occupation" name="res_occupation">
                  <option value="" disabled selected>Occupational </option>
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_occupation");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["occupation_ID"]; ?>"><?php echo $row["occupation_Name"]; ?></option>
                    <?php
                  }
                  ?>
                  <option value="Others">Others</option>
                </select>
              </div>

              <script>
                $(function () {
                  $("#res_occupation").change(function () {
                    if ($(this).val() == "Others") {
                      document.getElementById("res_trabaho").disabled = false;
                    } else {
                      document.getElementById("res_trabaho").disabled = true;
                    }
                  });
                });
              </script>

              <div class="form-group col-md-3">
                <label for="mname">Adding Occupation</label>
                <input required type="text" maxlength="20" class="form-control" id="res_trabaho" name="res_trabaho"
                  placeholder="Add Occupation" disabled>
              </div>

              <div class="clearfix"></div>
              <hr>
              <h4 style="text-align: center; font-style: normal;font-size: 18px;font-family: Verdana">RESIDENT ADDRESS
              </h4>
              <br>

              <div class="form-group col-md-4">
                <label for="res_unit">Unit-Room-Floor</label>
                <input type="text" maxlength="20" class="form-control" id="res_unit" name="res_unit"
                  placeholder="Unit-Room-Floor" value="1">
              </div>

              <div class="form-group col-md-4">
                <label for="res_building">Building name</label>
                <input type="text" maxlength="15" class="form-control" id="res_building" name="res_building"
                  placeholder="Building name" value="1">
              </div>

              <div class="form-group col-md-4">
                <label for="res_lot">Lot</label>
                <input required type="number" onkeypress="return isNumberKey(event)" maxlength="15" class="form-control"
                  id="res_lot" name="res_lot" placeholder="Lot">
              </div>

              <div class="form-group col-md-4">
                <label for="res_block">Block</label>
                <input required type="number" onkeypress="return isNumberKey(event)" maxlength="15" class="form-control"
                  id="res_block" name="res_block" placeholder="Block">
              </div>

              <div class="form-group col-md-4">
                <label for="res_phase">Phase</label>
                <input required type="text" onkeypress="return isNumberKey(event)" maxlength="15" class="form-control"
                  id="res_phase" name="res_phase" placeholder="Phase">
              </div>

              <div class="form-group col-md-4">
                <label for="res_houseno">House number*</label>
                <input type="text" maxlength="15" class="form-control" id="res_houseno" name="res_houseno" placeholder="House number" value="1">
              </div>

              <div class="form-group col-md-4">
                <label for="res_street">Street</label>
                <input type="text" maxlength="15" class="form-control" id="res_street" name="res_street"
                  placeholder="Street">
              </div>

              <div class="form-group col-md-4">
                <label for="res_subdmname">Subdivision</label>
                <input required type="text" maxlength="20" class="form-control" id="res_subd" name="res_subd"
                  placeholder="Subdivision">
              </div>

              <div class="form-group col-md-4">
                <label for="res_purokno">Barangay*</label>
                <input required type="text" maxlength="50" class="form-control" id="res_purokno" name="res_purokno" placeholder="Barangay">
              </div>

              <div class="form-group col-md-4">
                <label for="res_address">Address type*</label>
                <select required class="form-control" id="res_address" name="res_address">
                  <?php
                  $res = mysqli_query($db, "SELECT * FROM ref_address");
                  while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <option value="<?php echo $row["addressType_ID"]; ?>"><?php echo $row["addressType_Name"]; ?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <br><br>
            </div>
            <div class="clearfix"></div>
            <hr>
            <h4 style="text-align: center; font-style: normal;font-size: 18px;font-family: Verdana">ACCOUNT
            </h4>
            <br>
            <div class="form-group col-md-4">
              <label for="res_email">Contact Number</label>
              <input required type="email" maxlength="50" class="form-control" id="res_email" name="res_email"
              placeholder="Mobile Number">
            </div>
            <div class="form-group col-md-4">
              <label for="res_password">Password*</label>
              <input required type="password" maxlength="20" class="form-control" id="res_password" name="res_password"
              placeholder="Password">
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
              <div class="clearfix"></div>
              <div class="row-bttn">
                &nbsp;&nbsp; <p>
                  <center><a href="profile.php"> <input required type="submit" name="insert" id="insert" value="Insert"
                        class="btn btn-info" /> </a></center>
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php if ($_SESSION['position'] == 'Barangay Secretary'): ?>
      <!-- Button to trigger modal -->
      <button type="button" class="btn btn-info btn-lg pull-right" data-toggle="modal" data-target="#myModal" title="Add Resident">
        <i class="fa fa-user-plus"></i>
      </button>
    <?php endif; ?>

    <h2>
      <?php echo ($_SESSION['position'] == 'Barangay Secretary') ? "LIST OF RESIDENTS" : "RESIDENTS"; ?>
    </h2>
<button class="btn btn-success" onclick="printTable()" style="margin-bottom:15px;">
  <i class="fa fa-print"></i> Print Table
</button>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
  function printTable() {
    // Clone the table to manipulate for printing
    var table = document.getElementById('tableex').cloneNode(true);

    // Remove the last column (Action) from header, body, and footer
    // Remove last <th> in <thead>
    var thead = table.querySelector('thead tr');
    if (thead) thead.removeChild(thead.lastElementChild);

    // Remove last <td> in each <tbody> row
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
      row.removeChild(row.lastElementChild);
    });

    // Remove <tfoot> entirely or just the columns
    var tfoot = table.querySelector('tfoot');
    if (tfoot) tfoot.parentNode.removeChild(tfoot);

    // Prepare print window
    var win = window.open('', '', 'height=700,width=900');
    win.document.write('<html><head><title>Print Table</title>');
    win.document.write('<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">');
    win.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">');
    win.document.write('<style>th:last-child, td:last-child { display: none !important; }</style>');
    win.document.write('</head><body>');
    win.document.write(table.outerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    win.print();
    win.close();
  }
</script>

<?php
$query = "SELECT rd.res_ID , rd.res_fName , rd.res_mName , rd.res_lName , sfx.suffix, rd.res_Bday , rms.marital_Name, rg.gender_Name, rr.religion_Name, rc.country_nationality, rc.country_citizenship, ro.occupation_Name, ros.occuStat_Name, rd.res_Date_Record FROM resident_detail rd LEFT JOIN ref_suffixname sfx ON rd.suffix_ID = sfx.suffix_ID LEFT JOIN ref_marital_status rms ON rd.marital_ID = rms.marital_ID LEFT JOIN ref_gender rg ON rd.gender_ID = rg.gender_ID LEFT JOIN ref_religion rr ON rd.religion_ID = rr.religion_ID LEFT JOIN ref_occupation ro ON rd.occupation_ID = ro.occupation_ID LEFT JOIN ref_occupation_status ros ON rd.occuStat_ID = ros.occuStat_ID LEFT JOIN ref_country rc ON rd.country_ID = rc.country_ID where rd.res_ID NOT IN (Select res_ID from resident_death) && rd.Status='Active'";
$result = mysqli_query($db, $query);
?>
<table id="tableex" class="table table-bordered" id="mytable">
  <thead>
    <tr>
      <th scope="col-2">Name</th>
      <th scope="col">Sex</th>
      <th scope="col">Citizenship</th>
      <th scope="col">Religion</th>
      <th scope="col">Civil Status</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
  <?php
  while ($row = mysqli_fetch_array($result)) {
    ?>
    <tr>
      <td><?php echo $row["res_lName"] . ", " ?>   <?php echo $row["res_fName"] ?>
        <?php echo "( " . $row["res_mName"] . " )" ?>   <?php echo $row["suffix"] ?>
      </td>
      <td><?php echo $row["gender_Name"] ?></td>
      <td><?php echo $row["country_citizenship"] ?></td>
      <td><?php echo $row["religion_Name"] ?></td>
      <td><?php echo $row["marital_Name"] ?></td>
      <td>
        <div class="btn-group">
          <a href="profile-final.php?id=<?php echo $row['res_ID'] ?>" class="btn btn-primary btn-xs" title="View">
            <i class="fa fa-eye"></i>
          </a>
          <?php if ($_SESSION['position'] != "Barangay Captain") {
            echo '<a href="edit.php?id=' . $row['res_ID'] . '" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>';
          } ?>
        </div>
      </td>
    </tr>
    <?php
  }
  ?>
  </tbody>
  <!-- Remove the tfoot so it doesn't appear in print -->
</table>
</div>

<script>
  $(document).ready(function () {
    $('#tableex').DataTable({
      paging: true,
      searching: true,
      ordering: true,
      info: true
    });
  });
</script>

  <script src="../assets/js/bootstrap.min.js"></script>
  <script src="../assets/vendor/js/jquery.dataTables.min.js"></script>
  <script src="../assets/vendor/js/dataTables.bootstrap.min.js"></script>
</body>

</html>


<script>
  $('#image').bind('change', function () {
    var filename = $("#image").val();
    if (/^\s*$/.test(filename)) {
      $(".file-upload").removeClass('active');
      $("#noFile").text("No file chosen...");
    }
    else {
      $(".file-upload").addClass('active');
      $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
    }
  });
</script>

<script type="text/javascript">

  function getAge() {
    var dob = document.getElementById('res_bdate').value;
    dob = new Date(dob);
    var today = new Date();
    var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
    document.getElementById('res_age').value = age;
  }

</script>

<script type="text/javascript">
  var uploadField = document.getElementById("image");

  uploadField.onchange = function () {
    if (this.files[0].size > 307200) {
      swal("ERROR", "Check the size of your image it must be less than 300 kb size or check the file you've selected it must be an image file type.");
      this.value = "";
    };
  };
</script>





<script>
  $(document).ready(function () {
    $('#insert').click(function () {
      var image_name = $('#image').val();
      if (image_name == '') {
        swal("ERROR", "Please select a image file.");
        return false;
      }
      else {
        var extension = $('#image').val().split('.').pop().toLowerCase();
        if (jQuery.inArray(extension, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
          swal("ERROR", "Invalid image file.");
          $('#image').val('');
          return false;
        }
      }
    });
  });  
</script>

<script type="text/javascript">
  $(function () {
    var dtToday = new Date();

    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if (month < 10)
      month = '0' + month.toString();
    if (day < 10)
      day = '0' + day.toString();

    var maxDate = year + '-' + month + '-' + day;
    $('#res_bdate').attr('max', maxDate);
  });
</script>

<script>
setInterval(function () {
  // DARK MODE TOGGLE
  var darkEnabled = localStorage.getItem('darkmode') === 'true';
  document.body.classList.toggle('dark-mode', darkEnabled);
  document.documentElement.classList.toggle('dark-mode', darkEnabled);
  console.log('Dark mode enabled:', darkEnabled);

  // FONT SIZE TOGGLE
  var fontSize = localStorage.getItem('fontsize'); // expected: small, medium, large

  // Remove all font size classes first
  document.body.classList.remove('font-small', 'font-medium', 'font-large');
  document.documentElement.classList.remove('font-small', 'font-medium', 'font-large');

  // Apply current font size class
  if (['small', 'medium', 'large'].includes(fontSize)) {
    document.body.classList.add('font-' + fontSize);
    document.documentElement.classList.add('font-' + fontSize);
  }
  console.log('Font size:', fontSize);
}, 500);
</script>