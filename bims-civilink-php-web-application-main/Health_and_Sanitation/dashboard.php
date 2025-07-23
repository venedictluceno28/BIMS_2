<?php
session_start();
require_once('db.php');

$illnessCounts = [];
$ageGroupsPerDisease = [];
$allergyCounts = [];
$maintenanceMeds = [];
$vaccineCount = 0;
$totalResidents = 0;
$diseaseMonthlyStats = [];
$ageGroupDiseaseStats = [];
$heightWeightBMIs = [];
$dewormingMonthly = [];

// Fetch all analytics data
$res = mysqli_query($db, "SELECT ma.*, rd.res_Bday FROM medical_analytics ma 
    JOIN resident_detail rd ON ma.resident_id = rd.res_ID");

while ($row = mysqli_fetch_assoc($res)) {
    $diseases = explode(",", $row['disease_occurrence']);
    foreach ($diseases as $entry) {
        if (preg_match('/^(.*?):\s*([\d\-]+).*?\((\d+)/', $entry, $matches)) {
            $disease = trim($matches[1]);
            $date = $matches[2];
            $caseCount = (int)$matches[3];

            $illnessCounts[$disease] = ($illnessCounts[$disease] ?? 0) + $caseCount;

            $month = substr($date, 0, 7);
            $diseaseMonthlyStats[$month][$disease] = ($diseaseMonthlyStats[$month][$disease] ?? 0) + 1;

            if (!empty($row['res_Bday'])) {
                $age = floor((strtotime($date) - strtotime($row['res_Bday'])) / (365.25 * 24 * 60 * 60));
                $group = ($age <= 5) ? '0-5' : (($age <= 12) ? '6-12' : (($age <= 18) ? '13-18' : '18+'));
                $ageGroupsPerDisease[$disease][$group] = ($ageGroupsPerDisease[$disease][$group] ?? 0) + 1;
                $ageGroupDiseaseStats[$group][$disease] = ($ageGroupDiseaseStats[$group][$disease] ?? 0) + 1;
            }
        }
    }

    // Vaccine coverage
    if (!empty(trim($row['immunization_records']))) $vaccineCount++;
    $totalResidents++;

    // Allergies
    if (!empty($row['allergies'])) {
        foreach (explode(',', $row['allergies']) as $allergy) {
            $a = trim($allergy);
            if ($a !== '') $allergyCounts[$a] = ($allergyCounts[$a] ?? 0) + 1;
        }
    }

    // Maintenance Medicines
    if (!empty($row['maintenance_medicines'])) {
        foreach (explode(',', $row['maintenance_medicines']) as $med) {
            $m = trim($med);
            if ($m !== '') $maintenanceMeds[$m] = ($maintenanceMeds[$m] ?? 0) + 1;
        }
    }

    // BMI
    if (!empty($row['height_records']) && !empty($row['weight_records'])) {
        $heights = explode(',', $row['height_records']);
        $weights = explode(',', $row['weight_records']);
        foreach ($heights as $i => $hEntry) {
            if (!isset($weights[$i])) continue;
            if (preg_match('/([\d\-]+):\s*(\d+)/', $hEntry, $hMatch) &&
                preg_match('/([\d\-]+):\s*(\d+\.?\d*)/', $weights[$i], $wMatch)) {
                $heightM = (float)$hMatch[2] / 100;
                $weight = (float)$wMatch[2];
                if ($heightM > 0) {
                    $bmi = round($weight / ($heightM * $heightM), 1);
                    $heightWeightBMIs[] = $bmi;
                }
            }
        }
    }

    // Deworming
    if (!empty($row['deworming_records'])) {
        foreach (explode(',', $row['deworming_records']) as $date) {
            $month = substr(trim($date), 0, 7);
            $dewormingMonthly[$month] = ($dewormingMonthly[$month] ?? 0) + 1;
        }
    }
}

// Top 5 illnesses
arsort($illnessCounts);
$common_illnesses = array_slice(array_map(fn($k, $v) => ['name' => $k, 'count' => $v], array_keys($illnessCounts), $illnessCounts), 0, 5);

// Most vulnerable age group
$vulnerable_age_groups = [];
foreach ($ageGroupsPerDisease as $disease => $groupData) {
    arsort($groupData);
    $topGroup = array_key_first($groupData);
    $vulnerable_age_groups[] = ['disease' => $disease, 'age_group' => $topGroup];
}
$age_group_labels = array_column($vulnerable_age_groups, 'disease');
$age_group_data = array_column($vulnerable_age_groups, 'age_group');

// Vaccine coverage
$vaccine_coverage = ($totalResidents > 0) ? round(($vaccineCount / $totalResidents) * 100) : 0;

// Dummy trend (you can replace this)
$health_trends = [
    'months' => ['Mar', 'Apr', 'May', 'Jun', 'Jul'],
    'improvement' => [50, 60, 65, 72, $vaccine_coverage]
];

// JSON output (for debugging or AJAX)
$analytics_data = [
    'common_illnesses' => $common_illnesses,
    'age_group_labels' => $age_group_labels,
    'age_group_data' => $age_group_data,
    'vaccine_coverage' => $vaccine_coverage,
    'health_trends' => $health_trends,
    'disease_monthly' => $diseaseMonthlyStats,
    'age_group_disease' => $ageGroupDiseaseStats,
    'allergies' => $allergyCounts,
    'maintenance_meds' => $maintenanceMeds,
    'bmis' => $heightWeightBMIs,
    'deworming_monthly' => $dewormingMonthly
];

// You can uncomment this if using AJAX:
// header('Content-Type: application/json');
// echo json_encode($analytics_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Health Analytics Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="css/design.css" rel="stylesheet" type="text/css"> 
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      max-width: 1400px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .chart-container {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }
    canvas {
      max-width: 100%;
      height: 250px;
    }
    body.dark-mode {
      background: #181a1b;
      color: #e0e0e0;
    }
    .chart-container.dark-mode {
      background: #232526;
      color: #e0e0e0;
    }
  </style>
  <script>
    setInterval(function () {
      let darkEnabled = localStorage.getItem('darkmode') === 'true';
      document.body.classList.toggle('dark-mode', darkEnabled);
      document.querySelectorAll('.chart-container').forEach(e => {
        e.classList.toggle('dark-mode', darkEnabled);
      });
    }, 500);
  </script>
</head>
<body>
<?php require_once('sidebar.php'); ?>

<div class="dashboard-grid">
  <div class="chart-container"><h3>Top 5 Common Illnesses</h3><canvas id="illnessChart"></canvas></div>
  <div class="chart-container"><h3>Vulnerable Age Group</h3><canvas id="ageGroupChart"></canvas></div>
  <div class="chart-container"><h3>Vaccine Coverage</h3><canvas id="vaccineChart"></canvas></div>

  <div class="chart-container"><h3>Health Trends</h3><canvas id="trendChart"></canvas></div>
  <div class="chart-container"><h3>Monthly Disease Trends</h3><canvas id="monthlyDiseaseChart"></canvas></div>
  <div class="chart-container"><h3>Allergy Distribution</h3><canvas id="allergyChart"></canvas></div>

  <div class="chart-container"><h3>Disease by Age Group</h3><canvas id="ageDiseaseStackedChart"></canvas></div>
  <div class="chart-container"><h3>Maintenance Meds</h3><canvas id="medsChart"></canvas></div>
  <div class="chart-container"><h3>BMI Distribution</h3><canvas id="bmiChart"></canvas></div>
</div>

<script>
  // Top Illnesses
  new Chart(document.getElementById('illnessChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_column($common_illnesses, 'name')) ?>,
      datasets: [{
        label: 'Cases',
        data: <?= json_encode(array_column($common_illnesses, 'count')) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)'
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });

  // Vulnerable Age Groups
  new Chart(document.getElementById('ageGroupChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($age_group_labels) ?>,
      datasets: [{
        label: 'Top Age Group',
        data: Array(<?= count($age_group_labels) ?>).fill(1),
        backgroundColor: 'rgba(255, 206, 86, 0.6)'
      }]
    },
    options: {
      indexAxis: 'y',
      plugins: {
        tooltip: {
          callbacks: {
            label: function(ctx) {
              const ageGroups = <?= json_encode($age_group_data) ?>;
              return 'Age Group: ' + ageGroups[ctx.dataIndex];
            }
          }
        }
      },
      scales: { x: { display: false }, y: { beginAtZero: true } }
    }
  });

  // Vaccine Coverage
  new Chart(document.getElementById('vaccineChart'), {
    type: 'doughnut',
    data: {
      labels: ['Covered', 'Uncovered'],
      datasets: [{
        data: [<?= $vaccine_coverage ?>, <?= 100 - $vaccine_coverage ?>],
        backgroundColor: ['#4bc0c0', '#ff6384']
      }]
    },
    options: { responsive: true }
  });

  // Health Trend
  new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($health_trends['months']) ?>,
      datasets: [{
        label: 'Improvement %',
        data: <?= json_encode($health_trends['improvement']) ?>,
        fill: false,
        borderColor: '#9966ff',
        tension: 0.3
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
  });

  // Monthly Disease Trend
  new Chart(document.getElementById('monthlyDiseaseChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode(array_keys($disease_monthly = $diseaseMonthlyStats)) ?>,
      datasets: <?= json_encode(array_map(function ($disease) use ($disease_monthly) {
        return [
          'label' => $disease,
          'data' => array_map(function ($month) use ($disease, $disease_monthly) {
            return $disease_monthly[$month][$disease] ?? 0;
          }, array_keys($disease_monthly)),
          'fill' => false
        ];
      }, array_keys(array_column($disease_monthly, 0)))) ?>
    },
    options: { responsive: true, tension: 0.4 }
  });

  // Allergy Chart
  new Chart(document.getElementById('allergyChart'), {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_keys($allergyCounts)) ?>,
      datasets: [{
        data: <?= json_encode(array_values($allergyCounts)) ?>,
        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#8bc34a', '#9c27b0']
      }]
    },
    options: { responsive: true }
  });

  // Disease by Age Group (Stacked Bar)
  new Chart(document.getElementById('ageDiseaseStackedChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_keys($ageGroupDiseaseStats)) ?>,
      datasets: <?= json_encode(array_map(function ($disease) use ($ageGroupDiseaseStats) {
        return [
          'label' => $disease,
          'data' => array_map(function ($group) use ($disease, $ageGroupDiseaseStats) {
            return $ageGroupDiseaseStats[$group][$disease] ?? 0;
          }, array_keys($ageGroupDiseaseStats))
        ];
      }, array_keys(array_column($ageGroupDiseaseStats, 0)))) ?>
    },
    options: {
      responsive: true,
      plugins: { title: { display: true, text: 'Disease by Age Group' } },
      scales: { x: { stacked: true }, y: { stacked: true } }
    }
  });

  // Maintenance Medicines
  new Chart(document.getElementById('medsChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_keys($maintenanceMeds)) ?>,
      datasets: [{
        label: 'Patients',
        data: <?= json_encode(array_values($maintenanceMeds)) ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.6)'
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });

  // BMI Chart
  new Chart(document.getElementById('bmiChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_map(fn($v, $i) => "BMI $i", $heightWeightBMIs, array_keys($heightWeightBMIs))) ?>,
      datasets: [{
        label: 'BMI',
        data: <?= json_encode($heightWeightBMIs) ?>,
        backgroundColor: '#ff9f40'
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, suggestedMax: 40 } } }
  });
</script>
</body>
</html>
