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
    <style>
        body, html {
            			background: linear-gradient(to right, #03AF34 0%, #FFF84A 100%) !important;

        }
    </style>
    <div class="container" style="background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 30px;">
        <!-- Font Awesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        <?php if ($_SESSION['position'] == 'Barangay Secretary'): ?>
            <!-- Button to trigger modal -->
            <button type="button" class="btn btn-info btn-lg pull-right" data-toggle="modal" data-target="#myModal" title="Add Account"> 
                <i class="fa fa-user-plus"></i>
            </button>
            <button onclick="window.location.href='archive.php'" class="btn btn-info btn-lg pull-right" style="margin-right:10px;" title="Archive Account">
                <i class="fa fa-archive"></i>
            </button>
        <?php endif; ?>

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
            $birthdate = $_POST['birthdate'];

            // Handle image upload
            $profile_image = $_FILES['profile']['name'];
            $profile_image_tmp = $_FILES['profile']['tmp_name'];
            $profile_image_path = "uploads/" . basename($profile_image);

            // Ensure the uploads directory exists
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            // Move the uploaded file to the server's directory
            if (move_uploaded_file($profile_image_tmp, $profile_image_path)) {
                $profile = $profile_image_path; // Store the file path
            } else {
                die("Error uploading image.");
            }

            $query = "INSERT INTO accounts (Fullname, Username, Emailaddress, device_Id, Password, Position, Committee, profile, birthdate) 
                  VALUES ('$fullname', '$username', '$emailaddress', '$device_Id', '$password', '$position', '$committee', '$profile', '$birthdate')";
            mysqli_query($db, $query);
        }
        ?>

        <!-- Accounts Table -->
        <table id="tableex" class="table table-bordered">
            <thead>
                <tr>
                    <th>Email Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include("db.php");

                $query = "SELECT * FROM accounts WHERE type = 1 AND arc = 0 or Position = 'Barangay Secretary'";
                $result = mysqli_query($db, $query);

                while ($row = mysqli_fetch_array($result)):
                    $id = $row['ID'];
                    ?>
                    <tr align="center"></tr>
                        <td><?= $row['Emailaddress'] ?></td>
                        <td>
                            <div class="btn-group"></div>
                                <a href="accountedit.php?ID=<?= $id ?>" class="btn btn-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['position'] == 'Barangay Secretary'): ?>
                                    <a href="accountdelete.php?ID=<?= $id ?>" class="btn btn-danger" title="Archive">
                                        <i class="fa fa-archive"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
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
    </div>
</body>

</html>

<!-- Add Account Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #b1cbbb; color: black;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Account</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="accountinsert.php" method="post" enctype="multipart/form-data">
                    <!-- Fullname -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fullname</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="fullname" placeholder="Enter Fullname"
                                required>
                        </div>
                    </div>
                    <!-- Username -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Username</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="username" placeholder="Enter Username"
                                required>
                        </div>
                    </div>
                    <!-- Profile Image -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Profile Image</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="file" name="profile" required>
                        </div>
                    </div>
                    <!-- Birthdate -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Birthdate</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="date" name="birthdate" required>
                        </div>
                    </div>
                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email Address</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="email" name="emailaddress"
                                placeholder="Enter Email Address" required>
                        </div>
                    </div>
                    <!-- Device ID -->
                    <div class="form-group" hidden>
                        <label class="col-sm-2 control-label">Device ID</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="device_Id" value="0"
                                placeholder="Enter Device ID">
                        </div>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="password" name="password" placeholder="Enter Password"
                                required>
                        </div>
                    </div>
                    <!-- Position -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Position</label>
                        <div class="col-sm-10">
                            <select name="position" class="form-control">
                                <optgroup>
                                    <option>Barangay Captain</option>
                                    <option>Barangay Councilor</option>
                                    <option>Barangay Health Worker</option>
                                    <option>Barangay Treasurer</option>
                                    <option>SK Chairman</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <!-- Committee -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Committee</label>
                        <div class="col-sm-10">
                            <select name="committee" class="form-control">
                                <optgroup>
                                    <option>None</option>
                                    <option>Peace and Order</option>
                                    <option>Health, Education & Sport</option>
                                    <option>Agriculture</option>
                                    <option>Infrastructure</option>
                                    <option>Budget & Appropriation</option>
                                    <option>Ways and Means</option>
                                    <option>Clean and Green</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success " name="submit">Add</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

            </div>
            </form>

        </div>
    </div>
</div>