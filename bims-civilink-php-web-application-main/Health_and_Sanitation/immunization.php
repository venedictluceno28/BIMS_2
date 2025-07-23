<?php
session_start();
require 'db.php';

// Helper to extract latest record
function get_latest_entry($record) {
  $lines = explode(',', $record);
  $latest = trim(end($lines));
  return $latest ?: '-';
}

// Handle Update
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $resident_id = $_POST['resident_id'];
  $immun = $_POST['immunization_records'];

  $sql = "UPDATE medical_analytics SET 
    resident_id='$resident_id',
    immunization_records='$immun',
    last_updated=NOW()
    WHERE id='$id'";
  mysqli_query($db, $sql);
  header("Location: immunization.php");
  exit();
}

// Handle Insert
if (isset($_POST['add'])) {
  $resident_id = $_POST['resident_id'];
  $immun = $_POST['immunization_records'];

  $sql = "INSERT INTO medical_analytics (
    resident_id, immunization_records, last_updated
  ) VALUES (
    '$resident_id', '$immun', NOW()
  )";
  mysqli_query($db, $sql);
  header("Location: immunization.php");
  exit();
}

// Handle Delete
if (isset($_POST['delete'])) {
  $id = $_POST['id'];
  $sql = "DELETE FROM medical_analytics WHERE id='$id'";
  mysqli_query($db, $sql);
  header("Location: immunization.php");
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
  <title>Immunization Records</title>
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
  <h2 class="mb-4">Immunization Records</h2>
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">New</button>

  <div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Latest Immunization</th>
          <th>Last Updated</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
<?php
$query = "SELECT m.id, m.resident_id, m.immunization_records, m.last_updated, r.res_fName, r.res_mName, r.res_lName 
      FROM medical_analytics m 
      LEFT JOIN resident_detail r ON m.resident_id = r.res_ID";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
  $id = $row['id'];
  $fullname = $row['res_fName'].' '.$row['res_mName'].' '.$row['res_lName'];

  echo '<tr>
    <td>'.$fullname.'</td>
    <td>'.get_latest_entry($row['immunization_records']).'</td>
    <td>'.$row['last_updated'].'</td>
    <td>
      <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal'.$id.'">View</button>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal'.$id.'">Edit</button>
      <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal'.$id.'">Delete</button>
    </td>
  </tr>';

  // View Modal
  echo "<div class='modal fade' id='viewModal{$id}' tabindex='-1'>
    <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <div class='modal-header bg-primary text-white'>
      <h5 class='modal-title'>Immunization Timeline - {$fullname}</h5>
      <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
      </div>
      <div class='modal-body timeline'>
        <h6><strong>Immunization Records</strong></h6><ul>";
        foreach (explode(',', $row['immunization_records']) as $entry) echo "<li>".trim($entry)."</li>";
  echo   "</ul>
      </div>
      <div class='modal-footer'>
      <button class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
      </div>
    </div>
    </div>
  </div>";

  // Edit Modal
  $residentDropdown = '<option value="">-- Select Resident --</option>';
  foreach ($residentOptions as $res) {
    $selected = ($res['id'] == $row['resident_id']) ? 'selected' : '';
    $residentDropdown .= '<option value="'.$res['id'].'" '.$selected.'>'.htmlspecialchars($res['name']).'</option>';
  }
  echo '
  <div class="modal fade" id="editModal'.$id.'" tabindex="-1">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="immunization.php">
      <div class="modal-header">
        <h5 class="modal-title">Edit Immunization Record of '. $fullname .'</h5>
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
        '.generateTagInput("immunization_records", "Immunization Records", $row['immunization_records']).'
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
      <form method='POST' action='immunization.php'>
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
    <form method="POST" action="immunization.php">
    <div class="modal-header">
      <h5 class="modal-title">Add New Immunization Record</h5>
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
      <div class="col-md-6 mb-2">
        <label class="form-label">Immunization Records</label>
        <input name="immunization_records" class="form-control tag-input">
      </div>
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
  return '
  <div class="col-md-6 mb-2">
    <label class="form-label">'.$label.'</label>
    <input name="'.$name.'" class="form-control tag-input" value="'.htmlspecialchars($value).'">
  </div>';
}
?>

<!-- Tagify Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".tag-input").forEach(input => {
  new Tagify(input, {
    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(",")
  });
  });
});
</script>
