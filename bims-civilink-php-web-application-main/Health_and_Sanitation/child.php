<?php
session_start();
require 'db.php';

// Helper: Get latest entry from a comma-separated record
function get_latest_entry($record, $index = -1) {
    $parts = array_map('trim', explode(',', $record));
    $count = count($parts);
    return $count > abs($index) ? $parts[$count + $index] : '-';
}

// Growth rate for height (cm/mo)
function calculate_growth_rate($record) {
    $parts = array_filter(array_map('trim', explode(',', $record)));
    $count = count($parts);
    if ($count >= 2) {
        // Extract numeric values after colon and before 'cm'
        if (preg_match('/:\s*(\d+(\.\d+)?)(?:\s*cm)?/', $parts[$count - 2], $prev) &&
            preg_match('/:\s*(\d+(\.\d+)?)(?:\s*cm)?/', $parts[$count - 1], $latest)) {
            return round($latest[1] - $prev[1], 2) . ' cm/mo';
        }
    }
    return '-';
}

// Resident dropdown options
$residentOptions = [];
$residentQuery = mysqli_query($db, "SELECT res_ID, res_fName, res_mName, res_lName FROM resident_detail");
while ($r = mysqli_fetch_assoc($residentQuery)) {
    $residentOptions[] = [
        'id' => $r['res_ID'],
        'name' => $r['res_fName'] . ' ' . $r['res_mName'] . ' ' . $r['res_lName']
    ];
}

// Get latest value (number) from record
function get_latest_value_number($record) {
    $parts = array_filter(array_map('trim', explode(',', $record)));
    if (!$parts) return null;
    $latest_entry = end($parts);
    // Extract number after colon (e.g., "2024-01-10: 95cm" => 95)
    if (preg_match('/:(\s*)(\d+(\.\d+)?)/', $latest_entry, $match)) {
        return (float)$match[2];
    }
    // Fallback: extract any number
    if (preg_match('/(\d+(\.\d+)?)/', $latest_entry, $match)) {
        return (float)$match[1];
    }
    return null;
}

// Growth rate for weight (kg/mo)
function calculate_weight_growth($record) {
    $parts = array_filter(array_map('trim', explode(',', $record)));
    $count = count($parts);
    if ($count >= 2) {
        // Extract numeric values after colon
        if (preg_match('/:(\s*)(\d+(\.\d+)?)/', $parts[$count - 2], $prev) &&
            preg_match('/:(\s*)(\d+(\.\d+)?)/', $parts[$count - 1], $latest)) {
            return round($latest[2] - $prev[2], 2) . ' kg/mo';
        }
    }
    return '-';
}

// BMI calculation
function calculate_bmi($weight, $height_cm) {
    if ($weight > 0 && $height_cm > 0) {
        $height_m = $height_cm / 100;
        return round($weight / ($height_m * $height_m), 2);
    }
    return '-';
}

// Expected height by age (simple model)
function expected_height_by_age($age) {
    if ($age <= 2) return 50 + ($age * 10);
    return 75 + (($age - 2) * 5);
}

// Growth percentage by age (0-18 years)
function growth_percentage($age) {
    if (!is_numeric($age)) return '-';
    if ($age < 0) $age = 0;
    if ($age > 18) $age = 18;
    $percent = ($age / 18) * 100;
    return round($percent, 1) . '%';
}

// Health risk score: count illnesses
function count_illnesses($illnesses) {
    $list = array_filter(array_map('trim', explode(',', $illnesses)));
    return count($list);
}

// Healthiness: count allergies and risk percent
function calculate_fragility($allergies) {
    $list = array_filter(array_map('trim', explode(',', $allergies)));
    $count = count($list);
    $percent_risk = $count * 5; // 5% risk per allergy
    return "{$count} allergies / {$percent_risk}% risk";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Child Nutrition & Growth</title>
    <link href="css/design.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php require_once 'sidebar.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Child Nutrition & Growth</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">New</button>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle">
          <thead class="table-dark">
    <tr>
        <th>Photo</th>
        <th>Name</th>
        <th>Latest Height</th>
        <th>Latest Weight</th>
        <th>Height Growth</th>
        <th>Weight Growth</th>
        <th>BMI</th>
        <th>Growth %</th>
        <th>Healthiness</th>
        <th>Health Risk Score</th>
        <th>Last Updated</th>
        <th>Actions</th>
    </tr>
</thead>

<tbody>
<?php
$query = "SELECT m.*, r.res_fName, r.res_mName, r.res_lName, r.res_Img, r.res_bday 
          FROM medical_analytics m 
          LEFT JOIN resident_detail r ON m.resident_id = r.res_ID
          WHERE TIMESTAMPDIFF(YEAR, r.res_bday, CURDATE()) < 18";

$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $fullname = $row['res_fName'].' '.$row['res_mName'].' '.$row['res_lName'];
    $image = $row['res_Img'] ? '<img src="data:image/jpeg;base64,'.base64_encode($row['res_Img']).'" width="60" height="60" style="object-fit:cover;border-radius:50%;">' : 'No Image';

    // Parse height & weight
    $latest_height = get_latest_value_number($row['height_records']);
    $latest_weight = get_latest_value_number($row['weight_records']);
    $age_years = (new DateTime())->diff(new DateTime($row['res_bday']))->y;

    echo '<tr>
        <td>'.$image.'</td>
        <td>'.$fullname.'</td>
        <td>'.($latest_height ? $latest_height.' cm' : '-').'</td>
        <td>'.($latest_weight ? $latest_weight.' kg' : '-').'</td>
        <td>'.calculate_growth_rate($row['height_records']).'</td>
        <td>'.calculate_weight_growth($row['weight_records']).'</td>
        <td>'.calculate_bmi($latest_weight, $latest_height).'</td>
        <td>'.growth_percentage( $age_years).'</td>
        <td>'.count(array_filter(explode(',', $row['past_illnesses']))).' illnesses</td>
        <td>'.calculate_fragility($row['allergies']).'</td>
        <td>'.$row['last_updated'].'</td>
        <td>
            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal'.$id.'">View</button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal'.$id.'">Edit</button>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal'.$id.'">Delete</button>
        </td>
    </tr>';


echo "<div class='modal fade' id='viewModal{$id}' tabindex='-1'>
    <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
        <div class='modal-header bg-primary text-white'>
        <h5 class='modal-title'>View Full Timeline - {$fullname}</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
        </div>
        <div class='modal-body timeline'>";

// Parse height records
$heights = [];
foreach (explode(',', $row['height_records']) as $entry) {
    $parts = explode(':', $entry, 2);
    if (count($parts) == 2) {
        $date = date('Y-m-d', strtotime(trim($parts[0])));
        $heights[$date] = floatval(str_replace('cm', '', trim($parts[1])));
    }
}

// Parse weight records
$weights = [];
foreach (explode(',', $row['weight_records']) as $entry) {
    $parts = explode(':', $entry, 2);
    if (count($parts) == 2) {
        $date = date('Y-m-d', strtotime(trim($parts[0])));
        $weights[$date] = floatval(str_replace('kg', '', trim($parts[1])));
    }
}

// Merge and display
$all_dates = array_unique(array_merge(array_keys($heights), array_keys($weights)));
sort($all_dates);

echo "<h6><strong>Height / Weight / BMI Timeline</strong></h6><ul>";
foreach ($all_dates as $date) {
    $formattedDate = date('F j, Y', strtotime($date));
    $heightText = isset($heights[$date]) ? $heights[$date] . ' cm' : 'N/A';
    $weightText = isset($weights[$date]) ? $weights[$date] . ' kg' : 'N/A';
    $bmi = '-';

    if (isset($heights[$date]) && isset($weights[$date])) {
        $height_m = $heights[$date] / 100;
        $bmi = $height_m > 0 ? number_format($weights[$date] / ($height_m ** 2), 2) : '-';
    }

    echo "<li>{$formattedDate} - Height: {$heightText}, Weight: {$weightText}, BMI: {$bmi}</li>";
}

        echo "</ul><hr><h6><strong>Immunization Records</strong></h6><ul>";
        foreach (explode(',', $row['immunization_records']) as $entry) {
          $entry = trim($entry);
          // Try to extract vaccine name and date (format: Vaccine:YYYY-MM-DD)
          if (preg_match('/^([^:]+):\s*(\d{4}-\d{2}-\d{2})$/', $entry, $match)) {
            $vaccine = htmlspecialchars($match[1]);
            $date = date('F j, Y', strtotime($match[2]));
            $dateObj = new DateTime($match[2]);
            $now = new DateTime();
            $diff = $dateObj->diff($now);
            $ago = '';
            if ($diff->y > 0) {
              $ago = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
              if ($diff->m > 0) $ago .= ', ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
            } elseif ($diff->m > 0) {
              $ago = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
              if ($diff->d > 0) $ago .= ', ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            } else {
              $ago = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            }
            echo "<li>{$vaccine}: {$date} ({$ago} ago)</li>";
          } else {
            echo "<li>".htmlspecialchars($entry)."</li>";
          }
        }

        echo "</ul><hr><h6><strong>Deworming Records</strong></h6><ul>";
        foreach (explode(',', $row['deworming_records']) as $entry) {
          $entry = trim($entry);
          if (preg_match('/(\d{4}-\d{2}-\d{2})/', $entry, $match)) {
            $date = date('F j, Y', strtotime($match[1]));
            $dateObj = new DateTime($match[1]);
            $now = new DateTime();
            $diff = $dateObj->diff($now);
            $ago = '';
            if ($diff->y > 0) {
              $ago = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
              if ($diff->m > 0) $ago .= ', ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
            } elseif ($diff->m > 0) {
              $ago = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
              if ($diff->d > 0) $ago .= ', ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            } else {
              $ago = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
            }
            echo "<li>{$date} ({$ago} ago)</li>";
          } else {
            echo "<li>".htmlspecialchars($entry)."</li>";
          }
        }
 
echo "</div>
    <div class='modal-footer'>
    <button class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
    </div>
</div>
</div>
</div>";

    $residentDropdown = '<option value="">-- Select Resident --</option>';
foreach ($residentOptions as $res) {
    $selected = ($res['id'] == $row['resident_id']) ? 'selected' : '';
    $residentDropdown .= '<option value="'.$res['id'].'" '.$selected.'>'.htmlspecialchars($res['name']).'</option>';
}
 
echo '
<div class="modal fade" id="editModal'.$id.'" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="medical.php">
        <div class="modal-header">
          <h5 class="modal-title">Edit Medical Record of '. $res['name'].'</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" value="'.$id.'">
          <div class="row g-2">
            <div class="col-md-6 mb-2">
              <label class="form-label">Select Resident</label>
              <select name="resident_id" class="form-select" required>
                '.$residentDropdown.'
              </select>
            </div>

            '.generateTagInput("past_illnesses", "Past Illnesses", $row['past_illnesses']).'
            '.generateTagInput("allergies", "Allergies", $row['allergies']).'
            '.generateTagInput("family_medical_history", "Family Medical History", $row['family_medical_history']).'
            '.generateTagInput("immunization_records", "Immunization Records", $row['immunization_records']).'
            '.generateTagInput("height_records", "Height Records", $row['height_records']).'
            '.generateTagInput("weight_records", "Weight Records", $row['weight_records']).'
            '.generateTagInput("deworming_records", "Deworming Records", $row['deworming_records']).'
            '.generateTagInput("maintenance_medicines", "Maintenance Medicines", $row['maintenance_medicines']).'
            '.generateTagInput("disease_occurrence", "Disease Occurrence", $row['disease_occurrence']).'
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>';
                    // Delete Modal
                    echo "
                    <div class='modal fade' id='deleteModal{$id}' tabindex='-1'>
                      <div class='modal-dialog'>
                        <div class='modal-content'>
                          <form method='POST' action='medical.php'>
                            <div class='modal-header'>
                              <h5 class='modal-title'>Confirm Delete</h5>
                              <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                            </div>
                            <div class='modal-body'>
                              <p>Are you sure you want to delete record ID {$id}?</p>
                              <input type='hidden' name='id' value='{$id}'>
                            </div>
                            <div class='modal-footer'>
                              <button type='submit' name='delete' class='btn btn-danger'>Yes, Delete</button>
                              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<!-- Helper PHP Function -->
<?php
function generateTagInput($name, $label, $value = '') {
  $templateBtn = '';
  $isDateField = in_array($name, [
    'height_records', 'weight_records',
    'immunization_records', 'deworming_records',
    'disease_occurrence'
  ]);

  if ($isDateField) {
   $templateBtn = '<button type="button" class="btn btn-sm btn-outline-secondary insert-today" data-target="'.$name.'">Insert Today</button>';

  }

  return '
    <div class="col-md-6 mb-2">
      <label class="form-label d-flex justify-content-between">
        <span>'.$label.'</span>
        '.$templateBtn.'
      </label>
      <input name="'.$name.'" id="input-'.$name.'" class="form-control tag-input" value="'.htmlspecialchars($value).'">
    </div>';
}

?>

<!-- Tagify Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const tagifyMap = {}; // To track Tagify instances by input name

  // Initialize Tagify for all tag-input fields
  document.querySelectorAll(".tag-input").forEach(input => {
    const tagify = new Tagify(input, {
      originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(",")
    });

    tagifyMap[input.name] = tagify;
  });

  // Handle Insert Today button
  document.querySelectorAll(".insert-today").forEach(button => {
    button.addEventListener("click", function () {
      const targetName = this.dataset.target;
      const tagify = tagifyMap[targetName];
      if (!tagify) return;

      const today = new Date().toISOString().split('T')[0];

      let template = '';
      switch (targetName) {
        case 'height_records':
          template = `${today}: `;
          break;
        case 'weight_records':
          template = `${today}: `;
          break;
        case 'deworming_records':
          template = `${today}`;
          break;
        case 'immunization_records':
          template = `Vaccine:${today}`;
          break;
        case 'disease_occurrence':
          template = `Disease: ${today} (1 case)`;
          break;
      }

      if (template) tagify.addTags([template]);
    });
  });
});
</script>
