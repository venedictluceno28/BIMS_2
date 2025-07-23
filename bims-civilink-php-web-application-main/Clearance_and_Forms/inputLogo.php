<?php
include 'connection.php';
$s1 = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Forms and Clearances - Input Logo</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

    .btn-success {
      width: 100%;
      margin-top: 10px;
    }

    .jumbotron {
      padding: 2rem 2rem;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand logo" href="#">Input Logo</a>
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


        <form action="inputLogo.php" method="post" enctype="multipart/form-data" class="form-group">
          <div class="form-group">
            <label for="logo_type">Select Logo Type</label>
            <select name="logo_type" class="form-control">
              <option value="Municipal Logo">Municipal Logo</option>
              <option value="Barangay Logo">Barangay Logo</option>
            </select>
          </div>

          <div class="form-group">
            <label for="logo">Upload Logo</label>
            <input type="file" name="logo" class="form-control-file">
          </div>

          <button type="submit" name="submit" class="btn btn-success">Upload</button>
        </form>

        <div class="warning">
          <?php
          if (isset($_POST['submit'])) {
            if ($_FILES["logo"]["size"] == 0) {
              echo "Please choose a file first!";
            } else {
              $logo = addslashes(file_get_contents($_FILES['logo']['tmp_name']));
              $logo_type = $_POST['logo_type'];
              $sql = "UPDATE `ref_logo` SET logo_img='$logo' WHERE logo_Name='$logo_type';";

              if (mysqli_query($db, $sql)) {
                echo "Upload successful!";
              } else {
                echo "Upload failed!";
              }
            }
          }
          ?>
        </div>

      </div>
    </div>
  </section>

</body>

</html>