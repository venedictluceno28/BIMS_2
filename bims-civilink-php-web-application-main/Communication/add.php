<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>SMS NOTIFICATIONS</title>
   <!-- Bootstrap Core CSS -->
   <link rel="stylesheet" type="text/css" href="css/index.css" >
   <link href="css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/bootstrap.min.css" />
   <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
   <link rel="stylesheet" href="css/bootstrap.css" />
   <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
   <script src="js/jquery2.js"></script>
   <script src="js/bootstrap.min.js"></script>
   <!-- Custom CSS -->
   <link href="css/index.css" rel="stylesheet">
   <!-- Fonts -->
   <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800"
      rel="stylesheet" type="text/css">
   <link
      href="https://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic"
      rel="stylesheet" type="text/css">
   <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
   <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
   <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
</head>
<?php
include("connection.php");
?>

<head>
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
   <!-- Font Awesome CDN for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

   <div class="container">
      <div class="jumbotron">
         <center>
            <h2>Send an SMS</h2>
            <div class="btn-group text-center"> 
               <a href="index.php" class="btn btn-success btn-lg"><i class="fa fa-home"></i> HOME</a>
               <!-- <a href="sms_log.php" class="btn btn-success btn-lg">SMS LOG</a> -->
               <a href="category_option.php" class="btn btn-success btn-lg"><i class="fa fa-list"></i> Category Option</a>
               <a href="add.php" class="btn btn-success btn-lg"><i class="fa fa-plus"></i> Send SMS</a>
            </div>
            <hr>
         </center>
      </div>
      <div class="col-md-3"></div>
      <form class="form-horizontal col-md-6" action="add1.php" method="post" enctype="multipart/form-data">
         <div class="form-group">
            <label class="control-label col-sm-2" for="Category">Category</label>
            <div class="col-sm-10">
               <select name="category" class="form-control" required>
                  <?php
                  $sql = mysqli_query($db, "SELECT * FROM sms_category");
                  while ($row = mysqli_fetch_assoc($sql)) {
                     ?>
                     <option><?php echo $row['category']; ?></option>
                     <?php
                  }
                  ?>
               </select>
            </div>
         </div>
         <div class="form-group" hidden>
            <label class="control-label col-sm-2" for="receiver">Receiver</label>
            <div class="col-sm-10">
               <select name="receiver" class="form-control" required>
                  <option>All</option>
                  <?php
                  $sql = mysqli_query($db, "SELECT * FROM ref_position");
                  while ($row = mysqli_fetch_assoc($sql)) {
                     ?>
                     <option><?php echo $row['position_Name']; ?></option>
                     <?php
                  }
                  ?>
               </select>
            </div>
         </div>
         <div class="form-group">
            <label class="control-label col-sm-2" for="Statement">Statement</label>
            <div class="col-sm-10">
               <textarea required class="form-control" rows="6" id="pwd" name="statement"></textarea>
            </div>
         </div>
         <div class="form-group">
            <label class="control-label col-sm-2" for="Image">Image</label>
            <div class="col-sm-10" title="This Picture wont appear in the message (for the page only)">
               <input class="form-control" type="file" name="image" value=""
                  title="This Picture wont appear in the message (for the page only)">
            </div>
         </div>
         <div class="form-group">
            <label class="control-label col-sm-2" for="MobileNumbers">Mobile Numbers</label>
            <div class="col-sm-10">
               <select name="mobile_numbers[]" id="mobile_numbers" class="form-control" multiple>
                  <option value="all">All</option>
                  <?php
                  $sql = mysqli_query($db, "SELECT DISTINCT rc.contact_telnum, rd.res_fName, rd.res_mName, rd.res_lName FROM resident_detail rd JOIN resident_contact rc ON rd.res_ID = rc.res_ID");
                  $uniqueNames = [];
                  while ($row = mysqli_fetch_assoc($sql)) {
                      $fullName = trim($row['res_fName'] . ' ' . $row['res_mName'] . ' ' . $row['res_lName']);
                      if (!in_array($fullName, $uniqueNames)) {
                          $uniqueNames[] = $fullName;
                          ?>
                          <option value="<?php echo $row['contact_telnum']; ?>"><?php echo $fullName . ' (' . $row['contact_telnum'] . ')'; ?></option>
                          <?php
                      }
                  }
                  ?>
               </select>
               <script>
                  document.getElementById('mobile_numbers').addEventListener('change', function() {
                      let options = this.options;
                      let allSelected = options[0].selected; // Check if "All" is selected

                      if (allSelected) {
                          // Select all options except "All"
                          for (let i = 1; i < options.length; i++) {
                              options[i].selected = true;
                          }
                      } else {
                          // If any other option is deselected, unselect "All"
                          let hasSelection = false;
                          for (let i = 1; i < options.length; i++) {
                              if (options[i].selected) {
                                  hasSelection = true;
                                  break;
                              }
                          }
                          options[0].selected = !hasSelection; // Select "All" only if nothing is selected
                      }
                  });
               </script>
            </div>
         </div>
         <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
               <button type="submit" class="btn btn-default">Submit</button>
            </div>
         </div>
      </form>
   </div>
   <script src="js/jquery.js"></script>
   <!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
   <!-- Script to Activate the Carousel -->
   <script>
      $('.carousel').carousel({
         interval: 5000 //changes the speed
      })
   </script>
   <?php
   // }
   // }
   // else
   // {
   //     echo "<script>alert('Your account must be logged in');</script>";
   //      echo "<script>window.location=\"login_sms_account.php\";</script>";
   // }
   ?>
</body>

</html>
