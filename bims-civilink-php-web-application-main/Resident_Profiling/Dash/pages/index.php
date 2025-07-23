<?php
include('connections.php');

// Count total residents
$query = "SELECT COUNT(res_ID) AS total FROM resident_detail";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$num_rows = $row['total'];

// Initialize age group counters
$query11 = 0;       // Infants (0 years)
$query111 = 0;      // Teens (13-19 years)
$query1111 = 0;     // Adults (20-59 years)
$query11111 = 0;    // Senior Citizens (60+)
$query1111111 = 0;  // Children (1-12 years)

// Get resident birthdays and calculate age groups
$query1 = "SELECT res_Bday FROM resident_detail";
$result1 = mysqli_query($db, $query1);
$today = date('Y-m-d');

while ($row = mysqli_fetch_array($result1)) {
    $dob = $row['res_Bday'];
    $diff = date_diff(date_create($dob), date_create($today));
    $age = $diff->format('%y');

    if ($age == 0) {
        $query11++;
    } elseif ($age >= 1 && $age <= 12) {
        $query1111111++;
    } elseif ($age >= 13 && $age <= 19) {
        $query111++;
    } elseif ($age >= 20 && $age <= 59) {
        $query1111++;
    } else {
        $query11111++;
    }
}

$programs_per_month = [];
$query = "SELECT DATE_FORMAT(start_date, '%b') AS month, COUNT(*) AS count
          FROM programs
          WHERE start_date IS NOT NULL
          GROUP BY month
          ORDER BY MIN(start_date)";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $programs_per_month[] = [$row['month'], (int)$row['count']];
}

$participants_per_category = [];
$query = "SELECT category, participants FROM programs";
$result = mysqli_query($db, $query);
$category_participants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $category = $row['category'] ?: 'Uncategorized';
    $participants_str = trim($row['participants']);
    $count = 0;
    if ($participants_str !== '') {
        $ids = array_filter(array_map('trim', explode(',', $participants_str)));
        $count = count($ids);
    }
    if (!isset($category_participants[$category])) {
        $category_participants[$category] = 0;
    }
    $category_participants[$category] += $count;
}
foreach ($category_participants as $cat => $total) {
    $participants_per_category[] = [$cat, $total];
}

// Fetch upcoming programs
$upcoming_programs = [];
$query = "SELECT program_name, start_date, category FROM programs WHERE start_date >= CURDATE() ORDER BY start_date ASC";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $upcoming_programs[] = $row;
}

 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:700&display=swap" rel="stylesheet">
    <style>
        .page-header {
            font-family: 'Poppins', Arial, sans-serif !important;
            font-weight: 700 !important;
            font-size: 2.5rem;
            font-style: normal;
        }
    </style>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap and styles -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Google Charts Loader -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <style>
        body, html {
            background: linear-gradient(to right, #03AF34 0%, #FFF84A 100%) !important;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <center><h3 class="page-header " style="color: white;">STATISTICS</h3></center>
        </div>
    </div>

    <!-- Dashboard Panels -->
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $num_rows; ?></div>
                            <div>Total Residents</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $query11; ?></div>
                            <div>Infants</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $query1111111; ?></div>
                            <div>Children</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $query111; ?></div>
                            <div>Teens</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $query1111; ?></div>
                            <div>Adults</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo $query11111; ?></div>
                            <div>Senior Citizens</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="font-weight:bold;">
                    Upcoming Programs
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Program Name</th>
                                    <th>Start Date</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($upcoming_programs)): ?>
                                    <?php foreach ($upcoming_programs as $program): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($program['program_name']); ?></td>
                                            <td><?php echo htmlspecialchars(date('M d, Y', strtotime($program['start_date']))); ?></td>
                                            <td><?php echo htmlspecialchars($program['category']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No upcoming programs found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts -->
    <div class="row">
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 col-sm-12 col-xs-12">
            <div id="piechart" style="width: 100%; height: 400px;"></div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 col-sm-12 col-xs-12">
            <div id="barchart" style="width: 100%; height: 400px;"></div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 col-sm-12 col-xs-12">
            <div id="dummychart1" style="width: 100%; height: 400px;"></div>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 col-sm-12 col-xs-12">
            <div id="dummychart2" style="width: 100%; height: 400px;"></div>
        </div>
    </div>
    <!-- Upcoming Programs Table -->

</div>

<!-- JS Dependencies -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../vendor/metisMenu/metisMenu.min.js"></script>
<script src="../dist/js/sb-admin-2.js"></script>

<!-- Google Charts Script -->
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        // Pie and Bar Chart Data (Resident Age Groups)
        var chartData = [
            ['Age Group', 'Population'],
            ['Infant', <?php echo $query11; ?>],
            ['Children', <?php echo $query1111111; ?>],
            ['Teens', <?php echo $query111; ?>],
            ['Adults', <?php echo $query1111; ?>],
            ['Senior Citizens', <?php echo $query11111; ?>]
        ];
        var data = google.visualization.arrayToDataTable(chartData);

        var pieOptions = {
            title: 'Resident Population (Pie Chart)'
        };
        var pieChart = new google.visualization.PieChart(document.getElementById('piechart'));
        pieChart.draw(data, pieOptions);

        var barOptions = {
            title: 'Resident Population (Bar Chart)',
            legend: {position: 'none'},
            hAxis: { title: 'Age Group' },
            vAxis: { title: 'Population' },
            colors: ['#4CAF50']
        };
        var barChart = new google.visualization.BarChart(document.getElementById('barchart'));
        barChart.draw(data, barOptions);

        // Line Chart: Programs per Month
        var programsPerMonth = [
            ['Month', 'Programs']
            <?php
            foreach ($programs_per_month as $row) {
                echo ",\n['" . $row[0] . "', " . $row[1] . "]";
            }
            ?>
        ];
        var dummyData1 = google.visualization.arrayToDataTable(programsPerMonth);
        var dummyOptions1 = {
            title: 'Programs per Month',
            curveType: 'function',
            legend: { position: 'bottom' }
        };
        var dummyChart1 = new google.visualization.LineChart(document.getElementById('dummychart1'));
        dummyChart1.draw(dummyData1, dummyOptions1);

        // Column Chart: Participants per Category
        var participantsPerCategory = [
            ['Category', 'Participants']
            <?php
            foreach ($participants_per_category as $row) {
                echo ",\n['" . addslashes($row[0]) . "', " . $row[1] . "]";
            }
            ?>
        ];
        var dummyData2 = google.visualization.arrayToDataTable(participantsPerCategory);
        var dummyOptions2 = {
            title: 'Participants per Program Category',
            legend: { position: 'none' },
            colors: ['#FF9800']
        };
        var dummyChart2 = new google.visualization.ColumnChart(document.getElementById('dummychart2'));
        dummyChart2.draw(dummyData2, dummyOptions2);
    }
</script>

</body>
</html>

