<html>

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <meta name="author" content="">
   <title>Category Option</title>
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
      <div class="jumbotron">
         <center>
            <h2>Category</h2>

            <div class="btn-group text-center">
               <a href="index.php" class="btn btn-success btn-lg"><i class="fa fa-home"></i> HOME</a>
               <!-- <a href="sms_log.php" class="btn btn-success btn-lg">SMS LOG</a> -->
               <a href="category_option.php" class="btn btn-success btn-lg"><i class="fa fa-list"></i> Category Option</a>
               <a href="add.php" class="btn btn-success btn-lg"><i class="fa fa-plus"></i> Send SMS</a>
            </div><br>
            <hr>

            <button type="button" class="btn btn-info btn-lg pull-left" data-toggle="modal" data-target="#myModal">
               <i class="fa fa-plus"></i> Add Category
            </button>
            <br><br><br>

            <center>
               <table class="table table-bordered" border="2px" width="20%">
                  <thead>
                     <tr>
                        <th class="text-center">CATEGORY NAME</th>
                        <th class="text-center col-sm-3">ACTION</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     include("connection.php");

                     $res = mysqli_query($db, "SELECT * FROM sms_category");
                     while ($row = mysqli_fetch_assoc($res)) {
                        if ($row['category'] != null) {
                           ?>
                           <tr>
                              <td><?php echo $row['category'] ?></td>
                              <td class="text-center">
                                 <div class="btn-group">
                                    <a href="edit_category.php?id=<?php echo $row['id']; ?>" class="btn btn-success"><i class="fa fa-edit"></i> EDIT</a>
                                    <a href="delete_category1.php?id=<?php echo $row['id']; ?>" class="btn btn-warning"><i class="fa fa-trash"></i> DELETE</a>
                                 </div>
                              </td>
                           </tr>
                           <?php
                        }
                     }
                     ?>
                  </tbody>
               </table>
            </center>
         </center>
      </div>
      <!-- Modal -->
      <div id="myModal" class="modal fade" role="dialog">
         <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">CATEGORY</h4>
               </div>
               <form class="form-inline" action="add_category.php" method="POST">
                  <div class="modal-body">
                     <label>Category Name&nbsp;&nbsp;&nbsp;</label><input type="text" name="category" required> <br><br>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     <input type="submit" value="ADD" class="btn btn-success">
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</body>

</html>