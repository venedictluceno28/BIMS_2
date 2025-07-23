<html>
   s
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>SMS NOTIFICATIONS</title>
      <!-- Bootstrap Core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <!-- Custom CSS -->
      <link href="css/index.css" rel="stylesheet">
      <!-- Fonts -->
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
      <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css">
      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <?php
      include ("connection.php");
      
      $announceId = $_GET["id"];
      session_start();
      $_SESSION['id' ] = $announceId ;
      
      
      $result = mysqli_query($db, "SELECT * FROM announce WHERE announceId = '$announceId'");
      
      while ($row = mysqli_fetch_assoc($result)) 
      {
      	?>	
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
         <center>
            <h2>Edit An Alert</h2>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-7">
               <form class="form-horizontal" action="edit_category1.php" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="category">Category</label>
                     <div class="col-sm-10">
                        <select name="editCategory" value= "" class="form-control">
                           <option disabled=""><?php echo $row['category']; ?></option>
                           <?php
                              $sql = mysqli_query($db, "SELECT * FROM sms_category");
                              while($row1 = mysqli_fetch_assoc($sql))
                              {
                              ?>
                           <option><?php echo $row1['category']; ?></option>
                           <?php
                              }
                              ?>   
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="Receiver">Receiver</label>
                     <div class="col-sm-10">
                        <select name="editReceiver" value= "" class="form-control">
                           <option disabled=""><?php echo $row['category']; ?></option>
                           <?php
                              $sql = mysqli_query($db, "SELECT * FROM ref_position WHERE position_ID > 3");
                              while($row2 = mysqli_fetch_assoc($sql))
                              {
                              ?>
                           <option><?php echo $row2['position_Name']; ?></option>
                           <?php
                              }
                              ?>   
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="control-label col-sm-2" for="Statement">Statement</label>
                     <div class="col-sm-10"> 
                        <textarea name="editStatement" value= "" class="form-control"><?php echo $row['announcement'];?></textarea>
                     </div>
                  </div>
                  <div class="form-group">
                     <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" class="btn btn-default" value="Submmit">
                     </div>
                  </div>
               </form>
            </div>
         </center>
      </div>
   </body>
   <?php
      }
      ?>
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
</html>