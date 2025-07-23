<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Officers</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/css/dataTables.bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <style>
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
</head>

<body style="padding: 25px;">


    <h2>
    PUBLIC OFFCIALS
    </h2>

    <?php
    if (isset($_POST['submit'])) {
        include 'db.php';

        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $emailaddress = $_POST['emailaddress'];
        $device_Id = $_POST['device_Id'];
        $password = $_POST['password'];
        $position = $_POST['position'];
        $committee = $_POST['committee'];

        $query = "INSERT INTO accounts (Fullname, Username, Emailaddress, device_Id, Password, Position, Committee) 
              VALUES ('$fullname', '$username', '$emailaddress', '$device_Id', '$password', '$position', '$committee')";
        mysqli_query($db, $query);
    }
    ?>

    <!-- Accounts Cards -->
    <div class="row">
        <?php
        include("db.php");
        $query = "SELECT * FROM accounts WHERE arc = 0 AND id != 1 AND type != 1";  // Removed condition
        $result = mysqli_query($db, $query);

        while ($row = mysqli_fetch_array($result)):
            $id = $row['ID'];
            $birthdate = $row['birthdate'] ? new DateTime($row['birthdate']) : null;
            $today = new DateTime();
            $age = $birthdate ? $today->diff($birthdate)->y : 0;
            $profileImage = $row['profile'] ? $row['profile'] : 'https://placehold.co/150';
            ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?= $profileImage ?>" class="card-img-top profile-img" alt="Profile Image">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['Fullname'] ?></h5>
                        <p class="card-text"><?= $row['Position'] ?></p>
                        <p class="card-text">
                            <span class="badge bg-success"><?= $row['status'] ?></span> | Age: <?= $age ?> | 
                            <a href="view.php?id=<?= $id ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> View
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Bootstrap and DataTables Scripts -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/vendor/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/js/dataTables.bootstrap.min.js"></script>
</body>

</html>
