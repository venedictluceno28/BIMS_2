<?php
include_once('lib/init.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!-- style -->
    <link rel="stylesheet" href="assets/css/css.compiler.css?version=12">

    <!-- bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.min.css">

    <!-- moment -->
    <link rel="stylesheet" href="assets/css/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap-datetimepicker.min.css" />

    <!-- table export -->
    <link rel="stylesheet" href="assets/css/buttons.dataTables.min.css" />
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"/> -->

    <!-- fontawesome -->
    <link rel="stylesheet" href="assets/fonts/themify-icons/themify-icons.css">

    <!-- data table -->
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css"> -->

    <style>
        .user-panel .user-image img {
            width: 140px !important;
        }

        header .navbar-brand {
            color: #fff;
        }

        .nav-link span {
            color: #000 !important;
        }

        .navbar-nav .dropdown-menu {
            position: absolute !important;
            right: 0px;
        }

        .dropdown-item {
            line-height: 20px !important;
        }
    </style>

</head>

<body>
    <header>




        </li>
        </ul>
        </div>
        </nav>
    </header>


    <section class="content">
        <div class="row">
            <div class="col-md-12" style="padding: 0px 15px;">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">PEACE & ORDER (List of Incident)</h5>
                        <a class="btn btn-light btn-sm float-right" href="add_blotter.php"><i class="fas fa-plus"></i>
                            New
                            Incident Report</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover" id="blotter_table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Incident No.</th>
                                    <th scope="col">Blotter Type</th>
                                    <th scope="col">Complainant</th>
                                    <th scope="col">Offender/s</th>
                                    <th scope="col">Complainant Type</th>
                                    <th scope="col">Date Reported</th>
                                    <th scope="col">Date Occurred</th>
                                    <th scope="col" class="text-center">Status</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $results = $systems->getIncidentList();
                                if ($results) {
                                    while ($row = mysqli_fetch_assoc($results)) {
                                        $date_reported = date('F d, Y h:i a', strtotime($row['date_reported']));
                                        $date = date('F d, Y', strtotime($row['date_incident']));
                                        $time = date('h:i a', strtotime($row['time_incident']));
                                        $incident_occurred = $date . ' ' . $time;

                                        $offenders = $systems->getOffender($row['incident_id']);
                                        $offenderName = "N/A";
                                        $offenderNames = null;
                                        if ($offenders) {
                                            while ($offender = mysqli_fetch_assoc($offenders)) {
                                                if ($offender['off_complainantType'] == 2) {
                                                    $offenderNames[] = $offender['offender_name'];
                                                } else {
                                                    $offenderDetails = $systems->getResidentDetails($offender['off_res_ID']);
                                                    $offenderNames[] = $offenderDetails[0]['res_lName'] . ' ' . $offenderDetails[0]['res_fName'] . ' ' . $offenderDetails[0]['suffix'];
                                                }
                                            }
                                            $offenderName = implode(" , ", $offenderNames);
                                        }

                                        // Incident status mapping
                                        switch ($row['status']) {
                                            case '1':
                                                $status = 'Mediated 4a';
                                                break;
                                            case '2':
                                                $status = 'Conciliated 4b';
                                                break;
                                            case '3':
                                                $status = 'Arbitrated 4a';
                                                break;
                                            case '4':
                                                $status = 'Arbitrated 4b';
                                                break;
                                            case '5':
                                                $status = 'Dismissed 4c';
                                                break;
                                            case '6':
                                                $status = 'Certified Case 4d';
                                                break;
                                        }

                                        $blotterType = ($row['blotterType_id'] == 2) ? 'Incident' : 'Complaint';
                                        $complainantType = ($row['complainantType_ID'] == 2) ? 'Non Resident' : 'Resident';

                                        // Resident details if complainant is insider
                                        if ($row['complainantType_ID'] == 1) {
                                            $res = $systems->getResidentDetails($row['res_ID']);
                                        }

                                        // Generate the table rows
                                        echo '<tr>
                                        <td>#' . $row['incident_id'] . '</td>
                                        <td>' . $blotterType . '</td>
                                        <td>' . ($row['complainantType_ID'] == 2 ? $row['name'] : $res[0]['res_fName'] . ' ' . $res[0]['res_lName'] . ', ' . $res[0]['res_mName'] . ' ' . $res[0]['suffix']) . '</td>
                                        <td>' . $offenderName . '</td>
                                        <td>' . $complainantType . '</td>
                                        <td>' . $date_reported . '</td>
                                        <td>' . $incident_occurred . '</td>
                                        <td class="text-center">' . $status . '</td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="new_person.php?case=' . $row['incident_id'] . '"><i class="fas fa-user-plus"></i> Involve Person </a></li>
                                                    <li><a class="dropdown-item" href="involve_person.php?case=' . $row['incident_id'] . '"><i class="fas fa-eye"></i> Persons Involved</a></li>
                                                    <li><a class="dropdown-item" href="update_incident.php?edit=' . $row['incident_id'] . '"><i class="fas fa-edit"></i> Edit</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>';

                                    }
                                }
                                ?>
                            </tbody>
                            <!-- Bootstrap JS (required for dropdown functionality) -->
                            <script
                                src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- jquery -->
    <script src="assets/plugin/jquery-3.2.1.min.js"></script>

    <!-- moment -->
    <script src="assets/plugin/moment.js"></script>
    <script src="assets/plugin/bootstrap-datetimepicker.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script> -->
    <!-- validator -->
    <script src="assets/plugin/jquery.validate.min.js"></script>
    <script src="assets/plugin/jquery.validate.tooltip.min.js"></script>

    <!-- bootstrap -->
    <script src="assets/plugin/bootstrap/bootstrap.bundle.js"></script>

    <!-- chart js -->
    <script src="assets/plugin/Chart.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> -->

    <!-- data tables -->
    <!-- <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>  -->
    <script src="assets/plugin/jquery.dataTables.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>   -->
    <script src="assets/plugin/dataTables.bootstrap4.min.js"></script>

    <!-- table to excel -->
    <script src="assets/plugin/jquery.table2excel/jquery.table2excel.min.js"></script>

    <script src="assets/plugin/dataTables.buttons.min.js"></script>
    <script src="assets/plugin/buttons.flash.min.js"></script>
    <script src="assets/plugin/jszip.min.js"></script>
    <script src="assets/plugin/pdfmake.min.js"></script>
    <script src="assets/plugin/vfs_fonts.js"></script>
    <script src="assets/plugin/buttons.html5.min.js"></script>
    <script src="assets/plugin/buttons.print.min.js"></script>

    <script src="assets/js/script.js?version=28"></script>

</body>

</html>