<?php
session_start();
include('connections.php');

// Get current session user id
$session_id = $_SESSION['id'] ?? null;

// Get resident_id from accounts table for current session user
$resident_id = null;
if ($session_id) {
    $query = "SELECT resident_id FROM accounts WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $session_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $resident_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Fetch medical analytics for the resident
$medical = [];
if ($resident_id) {
    $query = "SELECT * FROM medical_analytics WHERE resident_id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $resident_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $medical = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Count total residents
$query = "SELECT COUNT(res_ID) AS total FROM resident_detail";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$num_rows = $row['total'];

// Age group counters
$infants = 0;       // 0 years
$children = 0;      // 1-12 years
$teens = 0;         // 13-19 years
$adults = 0;        // 20-59 years
$seniors = 0;       // 60+ years

$query1 = "SELECT res_Bday FROM resident_detail";
$result1 = mysqli_query($db, $query1);
$today = date('Y-m-d');

while ($row = mysqli_fetch_array($result1)) {
    $dob = $row['res_Bday'];
    $diff = date_diff(date_create($dob), date_create($today));
    $age = $diff->format('%y');

    if ($age == 0) {
        $infants++;
    } elseif ($age >= 1 && $age <= 12) {
        $children++;
    } elseif ($age >= 13 && $age <= 19) {
        $teens++;
    } elseif ($age >= 20 && $age <= 59) {
        $adults++;
    } else {
        $seniors++;
    }
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
    <meta charset="utf-8">
    <title>Resident Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <style>
        body, html {
            background: linear-gradient(135deg, #03AF34 0%, #FFF84A 100%) !important;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .page-header {
            font-weight: 700;
            font-size: 2.5rem;
            color: #fff;
            margin-top: 30px;
            letter-spacing: 2px;
        }
        .stat-panel {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(3,175,52,0.12);
            margin-bottom: 24px;
            padding: 24px 0;
            text-align: center;
            transition: box-shadow 0.2s;
        }
        .stat-panel:hover {
            box-shadow: 0 8px 32px rgba(3,175,52,0.18);
        }
        .stat-title {
            font-size: 1.1rem;
            color: #03AF34;
            margin-top: 8px;
            font-weight: 600;
        }
        .stat-value {
            font-size: 2.8rem;
            color: #222;
            font-weight: 700;
        }
        .chart-container {
            background: rgba(255,255,255,0.97);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(3,175,52,0.10);
            padding: 24px;
            margin-bottom: 32px;
        }

    /* DARK MODE START */
    /* Headings and text */
    body.dark-mode h1,
    body.dark-mode h2,
    body.dark-mode h3,
    body.dark-mode h4,
    body.dark-mode h5,
    body.dark-mode h6,
    body.dark-mode .intro-text,
    body.dark-mode .brand,
    body.dark-mode .address-bar {
        color: #f0f0f0 !important;
        text-shadow: none !important;
    }

    /* Paragraphs and general text */
    body.dark-mode p {
        color: #cccccc !important;
    }

    /* Navbar */
    body.dark-mode .navbar-default {
        background: #1f1f1f !important;
        border: none !important;
    }
    body.dark-mode .navbar-nav > li > a {
        color: #e0e0e0 !important;
    }
    body.dark-mode .navbar-nav > li > a:hover {
        background-color: #333 !important;
        color: #4CAF50 !important;
    }

    /* Tables */
    body.dark-mode table, 
    body.dark-mode th, 
    body.dark-mode td {
        background: #2a2a2a !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }

    /* Links */
    body.dark-mode a {
        color: #66bb6a !important;
    }

    /* Responsive adjustments */
    @media screen and (min-width:768px) {
        body.dark-mode .brand,
        body.dark-mode .address-bar {
        color: #e0e0e0 !important;
        }
    }
    font {
        color: black;
    }

    /* Dark mode override */
    body.dark-mode font {
        color: #66bb6a;
    }

    /* Stat panels and chart container dark mode */
    body.dark-mode .stat-panel,
    body.dark-mode .chart-container {
        background: rgba(34,34,34,0.97);
        box-shadow: 0 4px 24px rgba(3,175,52,0.18);
    }
    body.dark-mode .stat-title {
        color: #66bb6a !important;
    }
    body.dark-mode .stat-value {
        color: #f0f0f0 !important;
    }
    body.dark-mode .page-header {
        color: #66bb6a !important;
    }
    /* DARK MODE END */

    /* FONT SIZE START */
    body.font-small, html.font-small {
        font-size: 12px !important;
    }
    body.font-medium, html.font-medium {
        font-size: 16px !important;
    }
    body.font-large, html.font-large {
        font-size: 20px !important;
    }
    /* FONT SIZE END */
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<?php
function format_date($date) {
    if (!$date) return '';
    $timestamp = strtotime($date);
    if (!$timestamp) return htmlspecialchars($date); // fallback
    return date('M d, Y', $timestamp); // e.g., Jan 10, 2024
}
?>
<div class="container">
    <center><h3 class="page-header">Resident Statistics</h3></center>
    <div class="row">
        <div class="col-md-2 col-sm-6">
            <div class="stat-panel">
                <div class="stat-value"><?php echo $num_rows; ?></div>
                <div class="stat-title"><i class="fa fa-users"></i> Total Residents</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-panel">
                <div class="stat-value"><?php echo $infants; ?></div>
                <div class="stat-title"><i class="fa fa-child"></i> Infants</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-panel">
                <div class="stat-value"><?php echo $children; ?></div>
                <div class="stat-title"><i class="fa fa-child"></i> Children</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-panel">
                <div class="stat-value"><?php echo $teens; ?></div>
                <div class="stat-title"><i class="fa fa-user"></i> Teens</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-panel">
                <div class="stat-value"><?php echo $adults; ?></div>
                <div class="stat-title"><i class="fa fa-user"></i> Adults</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="stat-panel">
                <div class="stat-value"><?php echo $seniors; ?></div>
                <div class="stat-title"><i class="fa fa-user"></i> Seniors</div>
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
   <?php if ($medical): ?>
<div class="row">
    <div class="col-md-12 chart-container">
        <h4>Medical Analytics</h4>
        <div style="margin-bottom:20px;">
            <strong>Past Illnesses:</strong>
            <ul> 
                <?php foreach (explode(',', $medical['past_illnesses']) as $illness): ?>
                    <li><?php echo htmlspecialchars(trim($illness)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Allergies:</strong>
            <ul>
                <?php foreach (explode(',', $medical['allergies']) as $allergy): ?>
                    <li><?php echo htmlspecialchars(trim($allergy)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Family Medical History:</strong>
            <ul>
                <?php foreach (explode(',', $medical['family_medical_history']) as $history): ?>
                    <li><?php echo htmlspecialchars(trim($history)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Immunization Records:</strong>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Vaccine</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php
                $immunizations = explode(',', $medical['immunization_records']);
                foreach ($immunizations as $record) {
                    list($vaccine, $date) = explode(':', $record);
                    echo "<tr><td>".htmlspecialchars(trim($vaccine))."</td><td>".format_date(trim($date))."</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Height Records:</strong>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Date</th><th>Height</th></tr>
                </thead>
                <tbody>
                <?php
                $heights = explode(',', $medical['height_records']);
                foreach ($heights as $record) {
                    list($date, $height) = explode(':', $record);
                    echo "<tr><td>".format_date(trim($date))."</td><td>".htmlspecialchars(trim($height))."</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Weight Records:</strong>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Date</th><th>Weight</th></tr>
                </thead>
                <tbody>
                <?php
                $weights = explode(',', $medical['weight_records']);
                foreach ($weights as $record) {
                    list($date, $weight) = explode(':', $record);
                    echo "<tr><td>".format_date(trim($date))."</td><td>".htmlspecialchars(trim($weight))."</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Deworming Records:</strong>
            <ul>
                <?php foreach (explode(',', $medical['deworming_records']) as $date): ?>
                    <li><?php echo format_date(trim($date)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Maintenance Medicines:</strong>
            <ul>
                <?php foreach (explode(',', $medical['maintenance_medicines']) as $medicine): ?>
                    <li><?php echo htmlspecialchars(trim($medicine)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Disease Occurrence:</strong>
            <ul>
                <?php foreach (explode(',', $medical['disease_occurrence']) as $occurrence): ?>
                    <li><?php echo htmlspecialchars(trim($occurrence)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="margin-bottom:20px;">
            <strong>Last Updated:</strong>
            <span><?php echo format_date($medical['last_updated']); ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

  <div class="row" hidden>
        <div class="col-md-8 col-md-offset-2 chart-container">
            <div id="resident_chart" style="width: 100%; height: 420px;"></div>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawResidentChart);

    function drawResidentChart() {
        var data = google.visualization.arrayToDataTable([
            ['Age Group', 'Population', { role: 'style' }],
            ['Infants', <?php echo $infants; ?>, '#FFB300'],
            ['Children', <?php echo $children; ?>, '#4CAF50'],
            ['Teens', <?php echo $teens; ?>, '#2196F3'],
            ['Adults', <?php echo $adults; ?>, '#9C27B0'],
            ['Seniors', <?php echo $seniors; ?>, '#F44336']
        ]);

        var options = {
            title: 'Resident Population by Age Group',
            pieHole: 0.45,
            legend: { position: 'right', textStyle: {fontSize: 14} },
            chartArea: {width: '70%', height: '80%'},
            colors: ['#FFB300', '#4CAF50', '#2196F3', '#9C27B0', '#F44336'],
            backgroundColor: 'transparent',
            fontName: 'Poppins',
            titleTextStyle: {fontSize: 22, bold: true}
        };

        var pieChart = new google.visualization.PieChart(document.getElementById('resident_chart'));
        pieChart.draw(data, options);
    }
</script>
</body>
</html>
