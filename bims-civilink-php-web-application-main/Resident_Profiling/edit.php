<?php
include('connections.php');

// Fetch user details based on the user_id
$user_id = $_REQUEST["id"] ?? '';
// Fetch resident details
$get_record = mysqli_query($db, "SELECT * FROM resident_detail WHERE res_ID='$user_id'");

// Initialize variables
$db_res_fName = $db_res_mName = $db_res_lName = $db_res_suffixname = '';
$db_gender_ID = $db_marital_ID = $db_country_ID = $db_religion_ID = '';
$db_res_Height = $db_res_Weight = $db_occupation_ID = $db_occuStat_ID = '';
$db_res_Bday = $db_res_contype = '';

// Fetch resident details
if ($row_edit = mysqli_fetch_assoc($get_record)) {
  $db_res_fName = $row_edit["res_fName"] ?? '';
  $db_res_mName = $row_edit["res_mName"] ?? '';
  $db_res_lName = $row_edit["res_lName"] ?? '';
  $db_suffix_ID = $row_edit["suffix_ID"] ?? '';
  $db_gender_ID = $row_edit["gender_ID"] ?? '';
  $db_res_Bday = $row_edit["res_Bday"] ?? '';
  $db_marital_ID = $row_edit["marital_ID"] ?? '';
  $db_country_ID = $row_edit["country_ID"] ?? '';
  $db_res_Height = $row_edit["res_Height"] ?? '';
  $db_res_Weight = $row_edit["res_weight"];
  $db_religion_ID = $row_edit["religion_ID"] ?? '';
  $db_occupation_ID = $row_edit["occupation_ID"] ?? '';
  $db_occuStat_ID = $row_edit["occuStat_ID"] ?? '';
}

// Fetch contact type
$get_contact_type = mysqli_query(
  $db,
  "SELECT ref_contact.contactType_Name 
     FROM resident_contact 
     JOIN ref_contact ON resident_contact.contactType_ID = ref_contact.contactType_ID 
     WHERE resident_contact.res_ID = '$user_id'"
);

if ($row_contact = mysqli_fetch_assoc($get_contact_type)) {
  $db_res_contype = $row_contact['contactType_Name'] ?? 'No contact type';
}

function fetch_reference($db, $table, $column, $value, $return_column)
{
  $query = mysqli_query($db, "SELECT $return_column FROM $table WHERE $column = '$value'");
  if ($row = mysqli_fetch_assoc($query)) {
    return $row[$return_column];
  }
  return '';
}

$db_res_suffixname = fetch_reference($db, 'ref_suffixname', 'suffix_ID', $db_suffix_ID, 'suffix');
$db_res_gender = fetch_reference($db, 'ref_gender', 'gender_ID', $db_gender_ID, 'gender_Name');

$db_res_marital = fetch_reference($db, 'ref_marital_status', 'marital_ID', $db_marital_ID, 'marital_Name');
$db_res_religion = fetch_reference($db, 'ref_religion', 'religion_ID', $db_religion_ID, 'religion_Name');
$db_res_occuStat = fetch_reference($db, 'ref_occupation_status', 'occuStat_ID', $db_occuStat_ID, 'occuStat_Name');
$db_res_occupation = fetch_reference($db, 'ref_occupation', 'occupation_ID', $db_occupation_ID, 'occupation_Name');
$db_res_citizenship = fetch_reference($db, 'ref_country', 'country_ID', $db_country_ID, 'country_citizenship');

// Handle POST requests to update suffix, gender, etc.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $isuffix = $_POST["res_suffix"] ?? '';
  $igender = $_POST["new_gender"] ?? '';
  $imarital = $_POST["new_marital"] ?? '';
  $icountry = $_POST["res_citizenship"] ?? '';
  $ictype = $_POST["res_contacttype"] ?? '';
  $irel = $_POST["res_religion"] ?? '';
  $ioccStat = $_POST["new_occuStat"] ?? '';
  $iocc = $_POST["res_occupation"] ?? '';
  $ipurok = $_POST["new_purok"] ?? '';
  $iaddress = $_POST["new_address"] ?? '';

  // Fetch updated reference data based on POST inputs
  $res_suffix = fetch_reference($db, 'ref_suffixname', 'suffix', $isuffix, 'suffix_ID');
  $res_gender = fetch_reference($db, 'ref_gender', 'gender_Name', $igender, 'gender_ID');
  $res_marital = fetch_reference($db, 'ref_marital_status', 'marital_Name', $imarital, 'marital_ID');
  $res_countryID = fetch_reference($db, 'ref_country', 'country_citizenship', $icountry, 'country_ID');
  $res_ctype = fetch_reference($db, 'ref_contact', 'contactType_Name', $ictype, 'contactType_ID');
  $res_religion = fetch_reference($db, 'ref_religion', 'religion_Name', $irel, 'religion_ID');
  $res_occuStat = fetch_reference($db, 'ref_occupation_status', 'occuStat_Name', $ioccStat, 'occuStat_ID');
  $res_occu = fetch_reference($db, 'ref_occupation', 'occupation_Name', $iocc, 'occupation_ID');
  $res_purokname = fetch_reference($db, 'ref_purok', 'purok_Name', $ipurok, 'purok_ID');
  $res_addressname = fetch_reference($db, 'ref_address', 'addressType_Name', $iaddress, 'addressType_ID');

  // Handle file upload for images
  if (!empty($_FILES["image"]["tmp_name"])) {
    $file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
  }

  $nStatus = $_POST["new_status"] ?? '';
}

// Fetch resident address and contact details
$db_res_unit = $db_res_build = $db_res_lot = $db_res_block = $db_res_phase = $db_res_house = $db_res_street = $db_res_sub = '';
$db_res_brgy = $db_res_purok = $db_res_addtype = '';
$res_address = mysqli_query($db, "SELECT * FROM resident_address WHERE res_ID='$user_id'");
if ($row = mysqli_fetch_assoc($res_address)) {
  $db_res_unit = $row["address_Unit_Room_Floor_num"];
  $db_res_build = $row["address_BuildingName"];
  $db_res_lot = $row["address_Lot_No"];
  $db_res_block = $row["address_Block_No"];
  $db_res_phase = $row["address_Phase_No"];
  $db_res_house = $row["address_House_No"];
  $db_res_street = $row["address_Street_Name"];
  $db_res_sub = $row["address_Subdivision"];
  $db_res_brgy = $row["brgy_ID"];
  $db_res_purok = $row["purok_ID"];
  $db_res_addtype = $row["addressType_ID"];
}

// Fetch additional address and contact information
$db_res_brgypurok = fetch_reference($db, 'ref_purok', 'purok_ID', $db_res_purok, 'purok_Name');
$db_res_barangay = fetch_reference($db, 'ref_brgy', 'brgy_ID', $db_res_brgy, 'brgy_Name');
$db_res_addressType = fetch_reference($db, 'ref_address', 'addressType_ID', $db_res_addtype, 'addressType_Name');
$db_res_contactnumber = fetch_reference($db, 'resident_contact', 'res_ID', $user_id, 'contact_telnum');
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Management Information System</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/css/mis.css" rel="stylesheet">
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
  <div class="container">
    <h4 class="modal-title" style="text-align: center; font-style: normal;font-size: 18px;font-family: Verdana">EDIT
      PERSONAL INFORMATION</h4>
    <br>
    <br>

    <div class="clearfix"></div>

      <form method="POST" enctype="multipart/form-data" action="<?php htmlspecialchars("PHP_SELF"); ?>">

    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

    <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <style type="text/css">
      .thumb-image {
        float: left;
        width: 250px;
        height: 200px;
        position: relative;
        padding: 6px;
        margin-left: 50px;
      }
    </style>

    <div class="clearfix"></div>
    <div class="col-lg-offset-4" id="image-holder">
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-lg-offset-5 col-md-4">
      <div class="upload">
        <input type="file" name="image" id="image" />
      </div>

    </div>

    <style type="text/css">
      div.upload {
        width: 113px;
        height: 29px;
        background: url("images/Choose-photo-edit.png");
        overflow: hidden;
      }

      div.upload input {
        display: block !important;
        width: 157px !important;
        height: 57px !important;
        opacity: 0 !important;
        overflow: hidden !important;
      }
    </style>



    <div class="clearfix"></div>
    <div required class="form-group col-md-4">
      <label for="res_fname">First name</label>
      <input type="text" class="form-control" id="res_fname" name="new_fname" placeholder="First name"
        value="<?php echo $db_res_fName; ?>" required>
    </div>


    <div class="form-group col-md-4">
      <label for="res_mname">Middle name</label>
      <input type="text" class="form-control" id="res_mname" name="new_mname" placeholder="Middlename"
        value="<?php echo $db_res_mName; ?>">
    </div>


    <div class="form-group col-md-4">
      <label for="res_lname">Last name</label>
      <input type="text" class="form-control" id="res_lname" name="new_lname" placeholder="Lastname"
        value="<?php echo $db_res_lName; ?>" required>
    </div>


    <div class="form-group col-md-3">
      <label for="res_suffix">Suffix</label>
      <select class="form-control" id="res_suffix" name="res_suffix">
        <option style="display:none;"><?php echo $db_res_suffixname; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_suffixname");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["suffix"]; ?></option>

          <?php
        }

        ?>
      </select>
    </div>



    <div class="form-group col-md-2">
      <label for="res_gender">Sex</label>
      <select class="form-control" id="new_gender" name="new_gender">
        <option style="display:none;"><?php echo $db_res_gender; ?></option>

        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_gender");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["gender_Name"]; ?></option>

          <?php
        }

        ?>

      </select>
    </div>

    <div class="form-group col-md-2">
      <label for="res_bdate">Birthdate</label>
      <input placeholder="Birthdate" class="form-control" type="date" onblur="getAge();" id="res_bdate" name="new_bday"
        value="<?php echo $db_res_Bday; ?>"> <!-- onblur="(this.type='text')" -->
    </div>



    <div class="form-group col-md-2">
      <label for="res_civilstatus">Civil status</label>
      <select class="form-control" id="res_civilstatus" name="new_marital" value="<?php echo $db_marital_ID; ?>">
        <option style="display:none;"><?php echo $db_res_marital; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_marital_status");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["marital_Name"]; ?></option>


          <?php
        }

        ?>
      </select>
    </div>


    <div class="form-group col-md-2">
      <label for="res_contacttype">Contact type</label>
      <select class="form-control" id="res_contacttype" name="res_contacttype" onchange="maxLengthFunction()">
        <option style="display:none;"><?php echo $db_res_contype; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_contact");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["contactType_Name"]; ?></option>


          <?php
        }

        ?>
      </select>
    </div>

    <script>
      $(function () {
        $("#res_contacttype").change(function () {
          if ($(this).val() == "N/A") {
            document.getElementById("res_contactnum").disabled = true;

          }
          else {
            document.getElementById("res_contactnum").disabled = false;
          }
        });
      });
    </script>


   

    <div class="clearfix"></div>
    <div class="form-group col-md-3">
      <label for="res_contactnum">Contact</label>
      <input type="text"  class="form-control" id="res_contactnum" name="res_contactnum"
        value="<?php echo $db_res_contactnumber; ?>">
    </div>



    <div class="form-group col-md-2">
      <label for="res_lname">Height</label>
      <input type="text" class="form-control" id="res_height" name="new_height" placeholder="Meter/Centimeter"
        value="<?php echo $db_res_Height; ?>">
    </div>

    <div class="form-group col-md-2">
      <label for="res_lname">Weight</label>
      <input type="text" class="form-control" id="res_weight" name="new_weight" placeholder="Kilogram"
        value="<?php echo $db_res_Weight; ?>">
    </div>



    <div class="form-group col-md-2">
      <label for="res_citizenship">Citizenship</label>
      <select class="form-control" id="res_citizenship" name="res_citizenship">
        <option style="display:none;"><?php echo $db_res_citizenship; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_country");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["country_citizenship"]; ?></option>



          <?php
        }

        ?>

      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="res_religion">Religion</label>
      <select class="form-control" id="res_religion" name="res_religion">
        <option style="display:none;"><?php echo $db_res_religion; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_religion");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["religion_Name"]; ?></option>


          <?php
        }

        ?>

      </select>
    </div>




    <div class="form-group col-md-3">
      <label for="res_occupationstatus">Occupation status</label>
      <select class="form-control" id="new_occuStat" name="new_occuStat">
        <option style="display:none;"><?php echo $db_res_occuStat; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_occupation_status");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["occuStat_Name"]; ?></option>


          <?php
        }

        ?>
      </select>
    </div>


    <script>
      $(function () {
        $("#new_occuStat").change(function () {
          if ($(this).val() == "Unemployed") {
            document.getElementById("res_occupation").disabled = true;
            document.getElementById('res_occupation').value = "Not Applicable";
          } else {
            document.getElementById("res_occupation").disabled = false;
          }
        });
      });
    </script>


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
      <label for="mname">Occupation</label>
      <select class="form-control" id="res_occupation" name="res_occupation">
        <option style="display:none;"><?php echo $db_res_occupation; ?> </option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_occupation");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["occupation_Name"]; ?></option>


          <?php
        }

        ?>
        <option value="Others">Others</option>
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="mname">Adding Occupation</label>
      <input type="text" class="form-control" id="res_trabaho" name="res_trabaho"
        placeholder="Add Occupation" disabled>
    </div>
    <div class="clearfix"></div>
    <br>
    <br>

    <h4 style="text-align: center; font-style: normal;font-size: 18px;font-family: Verdana">RESIDENT ADDRESS</h4>
    <br>

    <div class="form-group col-md-2">
      <label for="res_unit">Phase</label>
      <input type="text" class="form-control" id="res_unit" name="new_addressUnit"
        value="<?php echo $db_res_unit; ?>" placeholder="Unit-Room-Floor">
    </div>


    <div class="form-group col-md-4">
      <label for="res_building">BLK LT</label>
      <input type="text"  class="form-control" id="res_building" name="new_addressBuilding"
        value="<?php echo $db_res_build; ?>" placeholder="Building name">
    </div>

    <div class="form-group col-md-2">
      <label for="res_lot">Complex / Subdivision</label>
      <input type="text"  class="form-control" id="res_lot" name="res_addressLot"
        value="<?php echo $db_res_lot; ?>" placeholder="Lot">
    </div>

    <div class="form-group col-md-2">
      <label for="res_block">Barangay</label>
      <input type="text"  class="form-control" id="res_block" name="new_addressBlock"
        value="<?php echo $db_res_block; ?>" placeholder="Block">
    </div>

    <div class="form-group col-md-2">
      <label for="res_phase">City/Municipality</label>
      <input type="text"  class="form-control" id="res_phase" name="new_addressPhase"
        value="<?php echo $db_res_phase; ?>" placeholder="Phase">
    </div>




    <div class="form-group col-md-3">
      <label for="res_houseno">Region</label>
      <input type="text"  class="form-control" id="res_houseno" name="new_addressHouse"
        value="<?php echo $db_res_house; ?>" placeholder="House number">
    </div>


    <div class="form-group col-md-4">
      <label for="res_street">Province</label>
      <input type="text"  class="form-control" id="res_street" name="new_addressStreet"
        value="<?php echo $db_res_street; ?>" placeholder="Street">
    </div>

    <div class="form-group col-md-3">
      <label for="res_subdmname">ZIP Code</label>
      <input type="text" class="form-control" id="res_subd" name="new_addressSubdi"
        value="<?php echo $db_res_sub; ?>" placeholder="Subdivision">
    </div>



    <div class="form-group col-md-3" hidden>
      <label for="res_purokno"> Purok no.</label>
      <select class="form-control" id="res_purokno" name="new_purok">
        <option style="display:none;"><?php echo $db_res_brgypurok; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_purok");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["purok_Name"]; ?></option>


          <?php
        }

        ?>
      </select>
    </div>





    <div class="form-group col-md-3">
      <label for="res_address">Address type</label>
      <select class="form-control" id="new_address" name="new_address">
        <option style="display:none;"><?php echo $db_res_addressType; ?></option>
        <?php
        $res = mysqli_query($db, "SELECT * FROM ref_address");
        while ($row = mysqli_fetch_array($res)) {
          ?>
          <option><?php echo $row["addressType_Name"]; ?></option>

          <?php
        }

        ?>
      </select>
    </div>


    <div class="form-group col-md-3">
      <label for="res_address">Status</label>
      <select class="form-control" id="new_address" name="new_status">
        <option>Active</option>
        <option>Not Active</option>

      </select>
    </div>


    <div class="clearfix"></div>

    <br><br>    <button type="button" onclick="location.href='resident.php'" class="btn btn-danger  col-lg-offset-5"><span
        class="glyphicon glyphicon-home" aria-hidden="true"></span> Back</button>
    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"
        aria-hidden="true"></span> Update</button>
  
    <a href="profile.php?res_id=<?php echo urlencode($user_id); ?>" class="btn btn-info">
      <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Update Parent
    </a>
    </div>

    </div>
  </form>


<?php

  include("connections.php");
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $res_trabaho = isset($_POST["res_trabaho"]) ? $_POST["res_trabaho"] : '';
  }
  $new_gender = "";
  if ($_SERVER["REQUEST_METHOD"] == "POST") {



    if (empty($res_trabaho)) {
      $new_occupation = $res_occu;

    } else {
      $new_occupation = $oid;
      $query = mysqli_query($db, "INSERT INTO ref_occupation(occupation_Name,occupation_ID) VALUES('$res_trabaho','$oid') ");
    }


    $res_Img = isset($file) ? $file : '';
    $new_image = $res_Img;
    $new_fname = $_POST["new_fname"];
    $new_mname = $_POST["new_mname"];
    $new_lname = $_POST["new_lname"];
    $new_suffix = $res_suffix;
    $new_gender = $res_gender;
    $new_bday = $_POST["new_bday"];
    $new_marital = $res_marital;
    $new_country = $res_countryID;
    $new_height = $_POST["new_height"];
    $new_weight = $_POST["new_weight"];
    $new_religion = $res_religion;

    $new_occuStat = $res_occuStat;
    $new_addressUnit = $_POST["new_addressUnit"];
    $new_addressBuilding = $_POST["new_addressBuilding"];
    $new_addressLot = $_POST["res_addressLot"];
    $new_addressBlock = $_POST["new_addressBlock"];
    $new_addressPhase = $_POST["new_addressPhase"];
    $new_addressHouse = $_POST["new_addressHouse"];
    $new_addressStreet = $_POST["new_addressStreet"];
    $new_addressSubdi = $_POST["new_addressSubdi"];

    $new_purok = $res_purokname;
    $new_addresstype = $res_addressname;
    $new_contacttel = $_POST["res_contactnum"];
    $new_contacttype = $res_ctype;
    $new_status = $_POST["new_status"];

  }



  ?>
  <?php

  if (empty($_FILES['image']['name'])) {

    if ($new_gender) {

      // Handle potential empty values for integers
      $new_occupation = !empty($new_occupation) ? $new_occupation : 'NULL';

      // Assign height and weight directly
      $sanitized_height = $new_height;
      $sanitized_weight = $new_weight;

      // Construct the query
      $query = "
        UPDATE resident_detail 
        SET 
          res_fName='$new_fname', 
          res_mName='$new_mname', 
          res_lName='$new_lname', 
          suffix_ID='$new_suffix', 
          gender_ID='$new_gender', 
          res_Bday='$new_bday', 
          marital_ID='$new_marital', 
          country_ID='$new_country', 
          religion_ID='$new_religion', 
          occuStat_ID='$new_occuStat', 
          res_Height='" . mysqli_real_escape_string($db, $sanitized_height) . "', 
          res_Weight='" . mysqli_real_escape_string($db, $sanitized_weight) . "', 
          occupation_ID=$new_occupation, 
          status='$new_status'  
        WHERE res_ID='$user_id'
      ";

      // Execute the query
      mysqli_query($db, $query) or die(mysqli_error($db));

      mysqli_query($db, "UPDATE resident_address SET address_Unit_Room_Floor_num='$new_addressUnit', address_BuildingName='$new_addressBuilding', address_Lot_No='$new_addressLot', address_Block_No='$new_addressBlock', address_Phase_No='$new_addressPhase', address_House_No='$new_addressHouse', address_Street_Name='$new_addressStreet', address_Subdivision='$new_addressSubdi', purok_ID='$new_purok', addressType_ID='$new_addresstype' WHERE res_ID = '$user_id' ");

      // Handle empty integer value for contactType_ID
      $new_contacttype = !empty($new_contacttype) ? $new_contacttype : 'NULL';

      // Construct the query
      $query = "
        UPDATE resident_contact 
        SET 
          contact_telnum='$new_contacttel', 
          contactType_ID=$new_contacttype 
        WHERE res_ID='$user_id'
      ";

      // Execute the query
      mysqli_query($db, $query) or die(mysqli_error($db));

      echo "<script language='javascript'>alert('Record has been Updated!')</script>";
      echo "<script>window.location.href='resident.php';</script>";
    }
  } else {
    if ($new_gender) {

      // Sanitize height and weight to keep only numbers and decimal point
      $sanitized_height = preg_replace('/[^0-9.]/', '', $new_height);
      $sanitized_weight = preg_replace('/[^0-9.]/', '', $new_weight);

      mysqli_query($db, "UPDATE resident_detail SET res_Img='$file',res_fName='$new_fname', res_mName='$new_mname', res_lName='$new_lname', suffix_ID='$new_suffix', gender_ID='$new_gender', res_Bday='$new_bday' , marital_ID='$new_marital', country_ID='$new_country' , religion_ID='$new_religion', occuStat_ID='$new_occuStat', res_Height='$sanitized_height', res_Weight='$sanitized_weight', occupation_ID='$new_occupation' , status='$new_status'  where res_ID = '$user_id'");

      mysqli_query($db, "UPDATE resident_address SET address_Unit_Room_Floor_num='$new_addressUnit', address_BuildingName='$new_addressBuilding', address_Lot_No='$new_addressLot', address_Block_No='$new_addressBlock', address_Phase_No='$new_addressPhase', address_House_No='$new_addressHouse', address_Street_Name='$new_addressStreet', address_Subdivision='$new_addressSubdi', purok_ID='$new_purok', addressType_ID='$new_addresstype' WHERE res_ID = '$user_id' ");
      mysqli_query($db, "UPDATE resident_contact SET contact_telnum='$new_contacttel', contactType_ID='$new_contacttype' WHERE res_ID='$user_id'");

      echo "<script language='javascript'>alert('Record has been Updated!')</script>";
      echo "<script>window.location.href='resident.php';</script>";
    }
  }

  ?>

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

      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="jquery/jquery-3.3.1.min.js"></script>
      <script src="js/bootstrap.min.js"></script>

      <script>$(document).ready(function () {
          var table = $('#mytable').removeAttr('width').DataTable();
        });</script>

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
          $("#image").on('change', function () {
            //Get count of selected files
            var countFiles = $(this)[0].files.length;
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#image-holder");
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
              if (typeof (FileReader) != "undefined") {
                //loop for each file selected for uploaded.
                for (var i = 0; i < countFiles; i++) {
                  var reader = new FileReader();
                  reader.onload = function (e) {
                    $("<img />", {
                      "src": e.target.result,
                      "class": "thumb-image"
                    }).appendTo(image_holder);
                  }
                  image_holder.show();
                  reader.readAsDataURL($(this)[0].files[i]);
                }
              } else {
                swal("ERROR", "This browser does not support FileReader.");
              }
            } else {
              swal("ERROR", "Check the size of your image it must be less than 300 kb size or check the file you've selected it must be an image file type.");
            }
          });
        });
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

      <br>
      <br>
    </form>
  </div>
</body>

</html>