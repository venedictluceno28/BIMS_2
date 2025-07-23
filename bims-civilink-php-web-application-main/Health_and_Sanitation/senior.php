<?php
session_start();
require 'db.php';

function get_latest_disease_summary($record) {
  $output = [];
  $diseases = explode(',', $record);
  foreach ($diseases as $entry) {
    $entry = trim($entry);
    if (strpos($entry, ':') === false) continue;

    [$name, $details] = explode(':', $entry, 2);
    $output[] = trim($name);
  }
  return implode(', ', $output);
}

function get_latest_entry($record, $stripDate = false) {
    $entries = array_map('trim', explode(',', $record));
    $latest = trim(end($entries));
    if (!$latest) return '-';
    
    if ($stripDate) {
        // Remove date and keep value (e.g., "2025-07-13: 99cm" -> "99cm")
        if (strpos($latest, ':') !== false) {
            $parts = explode(':', $latest, 2);
            return trim($parts[1]);
        }
    }
    return $latest;
}


// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $resident_id = $_POST['resident_id'];
    $past = $_POST['past_illnesses'];
    $allergies = $_POST['allergies'];
    $family = $_POST['family_medical_history'];
    $immun = $_POST['immunization_records'];
    $height = $_POST['height_records'];
    $weight = $_POST['weight_records'];
    $deworm = $_POST['deworming_records'];
    $maintenance = $_POST['maintenance_medicines'];
    $disease = $_POST['disease_occurrence'];

    $sql = "UPDATE medical_analytics SET 
        resident_id='$resident_id',
        past_illnesses='$past',
        allergies='$allergies',
        family_medical_history='$family',
        immunization_records='$immun',
        height_records='$height',
        weight_records='$weight',
        deworming_records='$deworm',
        maintenance_medicines='$maintenance',
        disease_occurrence='$disease',
        last_updated=NOW()
        WHERE id='$id'";
    mysqli_query($db, $sql);
    header("Location: medical.php");
    exit();
}

// Handle Insert
if (isset($_POST['add'])) {
    $resident_id = $_POST['resident_id'];
    $past = $_POST['past_illnesses'];
    $allergies = $_POST['allergies'];
    $family = $_POST['family_medical_history'];
    $immun = $_POST['immunization_records'];
    $height = $_POST['height_records'];
    $weight = $_POST['weight_records'];
    $deworm = $_POST['deworming_records'];
    $maintenance = $_POST['maintenance_medicines'];
    $disease = $_POST['disease_occurrence'];

    $sql = "INSERT INTO medical_analytics (
        resident_id, past_illnesses, allergies, family_medical_history, immunization_records,
        height_records, weight_records, deworming_records, maintenance_medicines, disease_occurrence, last_updated
    ) VALUES (
        '$resident_id', '$past', '$allergies', '$family', '$immun',
        '$height', '$weight', '$deworm', '$maintenance', '$disease', NOW()
    )";
    mysqli_query($db, $sql);
    header("Location: medical.php");
    exit();
}

// Handle Delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM medical_analytics WHERE id='$id'";
    mysqli_query($db, $sql);
    header("Location: medical.php");
    exit();
}
$residentOptions = [];
$residentQuery = mysqli_query($db, "SELECT res_ID, res_fName, res_mName, res_lName FROM resident_detail");
while ($r = mysqli_fetch_assoc($residentQuery)) {
    $residentOptions[] = [
        'id' => $r['res_ID'],
        'name' => $r['res_fName'] . ' ' . $r['res_mName'] . ' ' . $r['res_lName']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medical History & Logs</title>
    <link href="css/design.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    .timeline ul { list-style: none; padding: 0; }
    .timeline ul li { padding: 6px 0; border-left: 3px solid #0d6efd; margin-left: 10px; padding-left: 10px; position: relative; }
    .timeline ul li::before { content: "•"; color: #0d6efd; position: absolute; left: -10px; font-size: 20px; }
    </style>
</head>
<body>
<?php require_once 'sidebar.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<div class="container mt-4">
    <h2 class="mb-4">Medical History & Logs</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">New</button>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Past Illnesses</th>
                    <th>Allergies</th>
                    <th>Family History</th>
                    <th>Latest Height</th>
                    <th>Latest Weight</th>
                    <th>Medicines</th>
                    <th>Diseases</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
$query = "SELECT m.*, r.res_fName, r.res_mName, r.res_lName, r.res_Img 
          FROM medical_analytics m 
          LEFT JOIN resident_detail r ON m.resident_id = r.res_ID
          WHERE r.res_Bday <= DATE_SUB(CURDATE(), INTERVAL 60 YEAR)";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $fullname = $row['res_fName'].' '.$row['res_mName'].' '.$row['res_lName'];
    $image = $row['res_Img'] ? '<img src="data:image/jpeg;base64,'.base64_encode($row['res_Img']).'" width="60" height="60" style="object-fit:cover;border-radius:50%;">' : 'No Image';

    echo '<tr>
        <td>'.$image.'</td>
        <td>'.$fullname.'</td>
        <td>'.$row['past_illnesses'].'</td>
        <td>'.$row['allergies'].'</td>
        <td>'.$row['family_medical_history'].'</td>
<td>'.get_latest_entry($row['height_records'], true).'</td>
<td>'.get_latest_entry($row['weight_records'], true).'</td>
<td>'.htmlspecialchars($row['maintenance_medicines']).'</td>
<td>'.get_latest_disease_summary($row['disease_occurrence']).'</td>

        <td>'.date('F j, Y g:i A', strtotime($row['last_updated'])).'</td>
        <td>
            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal'.$id.'">View</button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal'.$id.'">Edit</button>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal'.$id.'">Delete</button>
        </td>
    </tr>';
echo "<div class='modal fade' id='viewModal{$id}' tabindex='-1'>
  <div class='modal-dialog modal-xl'>
    <div class='modal-content'>
      <div class='modal-header bg-primary text-white'>
        <h5 class='modal-title'>Full Timeline for Senior - {$fullname}</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
      </div>
      <div class='modal-body timeline'>
        <div class='row'>
";

// Step 1: Build a timeline by date to merge height/weight and calculate BMI
$height_entries = explode(',', $row['height_records']);
$weight_entries = explode(',', $row['weight_records']);
$timeline = [];

foreach ($height_entries as $entry) {
    $parts = explode(':', $entry);
    if (count($parts) == 2) {
        $date = trim($parts[0]);
        $value = trim($parts[1]);
        $timeline[$date]['height'] = $value;
    }
}

foreach ($weight_entries as $entry) {
    $parts = explode(':', $entry);
    if (count($parts) == 2) {
        $date = trim($parts[0]);
        $value = trim($parts[1]);
        $timeline[$date]['weight'] = $value;
    }
}

// Assume birthday is stored or fetched, or set a placeholder
$bday = isset($row['birthdate']) ? new DateTime($row['birthdate']) : new DateTime('1950-01-01');

echo "<div class='col-md-6'><h6><strong>Physical Growth (Height, Weight, BMI)</strong></h6><ul class='list-group mb-3'>";
ksort($timeline);
foreach ($timeline as $date => $data) {
    $dt = new DateTime($date);
    $age = $dt->diff($bday)->y;
    $formatted_date = $dt->format('F j, Y');

    $height = isset($data['height']) ? floatval($data['height']) : null;
    $weight = isset($data['weight']) ? floatval($data['weight']) : null;

    $bmi = ($height && $weight) ? round($weight / pow($height / 100, 2), 1) : '-';

    echo "<li class='list-group-item'>
      <strong>Date:</strong> {$formatted_date} <br>
      <strong>Age:</strong> {$age} yrs<br>
      <strong>Height:</strong> " . ($height ? "{$height} cm" : "-") . "<br>
      <strong>Weight:</strong> " . ($weight ? "{$weight} kg" : "-") . "<br>
      <strong>BMI:</strong> {$bmi}
    </li>";
}
echo "</ul></div>";

// Step 2: Immunization & Deworming
echo "<div class='col-md-6'><h6><strong>Immunization & Deworming</strong></h6><ul class='list-group mb-3'>";

foreach (explode(',', $row['immunization_records']) as $entry) {
    if (preg_match('/^([^:]+):\s*(\d{4}-\d{2}-\d{2})$/', trim($entry), $match)) {
        $vaccine = htmlspecialchars($match[1]);
        $date = new DateTime($match[2]);
        $formatted = $date->format('F j, Y');
        $age = $date->diff($bday)->y;
        $ago = $date->diff(new DateTime());

        $ago_str = ($ago->y > 0 ? $ago->y . " yr" . ($ago->y > 1 ? "s" : "") : "") .
                   ($ago->m > 0 ? ", " . $ago->m . " mo" . ($ago->m > 1 ? "s" : "") : "");

        echo "<li class='list-group-item'>
          <strong>{$vaccine}</strong><br>
          <strong>Date:</strong> {$formatted}<br>
          <strong>Age:</strong> {$age} yrs<br>
          <strong>Given:</strong> {$ago_str} ago
        </li>";
    }
}

foreach (explode(',', $row['deworming_records']) as $entry) {
    $entry = trim($entry);
    if (preg_match('/\d{4}-\d{2}-\d{2}/', $entry, $match)) {
        $date = new DateTime($match[0]);
        $formatted = $date->format('F j, Y');
        $age = $date->diff($bday)->y;
        $ago = $date->diff(new DateTime());

        $ago_str = ($ago->y > 0 ? $ago->y . " yr" . ($ago->y > 1 ? "s" : "") : "") .
                   ($ago->m > 0 ? ", " . $ago->m . " mo" . ($ago->m > 1 ? "s" : "") : "");

        echo "<li class='list-group-item'>
          <strong>Dewormed on:</strong> {$formatted}<br>
          <strong>Age:</strong> {$age} yrs<br>
          <strong>Time Ago:</strong> {$ago_str} ago
        </li>";
    }
}
echo "</ul></div>";

// Step 3: Maintenance Medicines
echo "<div class='col-md-6'><h6><strong>Maintenance Medicines</strong></h6><ul class='list-group mb-3'>";
foreach (explode(',', $row['maintenance_medicines']) as $med) {
  echo "<li class='list-group-item'>" . htmlspecialchars(trim($med)) . "</li>";
}
echo "</ul></div>";

// Step 4: Disease Occurrences
echo "<div class='col-md-6'><h6><strong>Disease Occurrence Timeline</strong></h6>";
$diseases = explode(',', $row['disease_occurrence']);
foreach ($diseases as $entry) {
    if (strpos($entry, ':') === false) continue;
    [$name, $details] = explode(':', $entry, 2);
    echo "<strong>" . htmlspecialchars($name) . "</strong><ul class='list-group mb-2'>";

    if (preg_match('/(\d{4}-\d{2}-\d{2})\s*\(([^)]+)\)/', trim($details), $matches)) {
        $date = new DateTime($matches[1]);
        $formatted = $date->format('F j, Y');
        $age = $date->diff($bday)->y;
        $desc = htmlspecialchars($matches[2]);
        echo "<li class='list-group-item'>{$formatted} (Age: {$age}) — {$desc}</li>";
    } else {
        echo "<li class='list-group-item'>" . htmlspecialchars(trim($details)) . "</li>";
    }

    echo "</ul>";
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" action="medical.php">
            <div class="modal-header">
              <h5 class="modal-title">Add New Medical Record</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row g-2">
            <div class="col-md-6 mb-2">
  <label class="form-label">Select Resident</label>
  <select name="resident_id" class="form-select" required>
    <option value="">-- Select Resident --</option>
    <?php foreach ($residentOptions as $res): ?>
      <option value="<?= $res['id'] ?>"><?= htmlspecialchars($res['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>

                <div class="col-md-6 mb-2"><label class="form-label">Past Illnesses</label><textarea name="past_illnesses" class="form-control" rows="2" placeholder="Past Illnesses"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Allergies</label><textarea name="allergies" class="form-control" rows="2" placeholder="Allergies"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Family Medical History</label><textarea name="family_medical_history" class="form-control" rows="2" placeholder="Family Medical History"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Immunization Records</label><textarea name="immunization_records" class="form-control" rows="2" placeholder="Immunization Records"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Height Records</label><textarea name="height_records" class="form-control" rows="2" placeholder="Height Records"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Weight Records</label><textarea name="weight_records" class="form-control" rows="2" placeholder="Weight Records"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Deworming Records</label><textarea name="deworming_records" class="form-control" rows="2" placeholder="Deworming Records"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Maintenance Medicines</label><textarea name="maintenance_medicines" class="form-control" rows="2" placeholder="Maintenance Medicines"></textarea></div>
                <div class="col-md-6 mb-2"><label class="form-label">Disease Occurrence</label><textarea name="disease_occurrence" class="form-control" rows="2" placeholder="Disease Occurrence"></textarea></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="add" class="btn btn-success">Add Record</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
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
