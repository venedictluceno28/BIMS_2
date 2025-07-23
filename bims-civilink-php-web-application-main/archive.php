<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/css/dataTables.bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
</head>

<body style="padding: 25px;">



    <h2>
        <?php echo ($_SESSION['position'] == 'Barangay Secretary') ? "LIST OF ACCOUNTS" : "ACCOUNTS"; ?>
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

    <!-- Accounts Table -->
    <table id="tableex" class="table table-bordered">
        <thead>
            <tr>
                <th>Fullname</th>
                <th>Username</th>
                <th>Email Address</th>
                <th>Position</th>
                <th>Committee</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include("db.php");

               $query = "SELECT * FROM accounts WHERE type = 1 AND arc = 1 ";
            $result = mysqli_query($db, $query);

            while ($row = mysqli_fetch_array($result)):
                $id = $row['ID'];
                ?>
                <tr align="center">
                    <td><?= $row['Fullname'] ?></td>
                    <td><?= $row['Username'] ?></td>
                    <td><?= $row['Emailaddress'] ?></td>
                    <td><?= $row['Position'] ?></td>
                    <td><?= $row['Committee'] ?></td>

                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- DataTable Script -->
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



    <!-- Bootstrap and DataTables Scripts -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/vendor/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/js/dataTables.bootstrap.min.js"></script>
</body>

</html>