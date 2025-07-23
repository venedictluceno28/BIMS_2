<?php
session_start();
require_once('connections.php');

// Handle Participate
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'participate') {
  $program_id = intval($_POST["program_id"]);
  $user_id = intval($_SESSION['id']);
  $result = mysqli_query($db, "SELECT participants FROM programs WHERE program_id=$program_id");
  $row = mysqli_fetch_assoc($result);
  $participants = $row['participants'] ? explode(',', $row['participants']) : [];
  if (!in_array($user_id, $participants)) {
    $participants[] = $user_id;
    $participants_str = implode(',', array_filter($participants));
    mysqli_query($db, "UPDATE programs SET participants='$participants_str' WHERE program_id=$program_id");
  }
  exit('success');
}


// Handle Add Program
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
  $program_name = mysqli_real_escape_string($db, $_POST["program_name"]);
  $description = mysqli_real_escape_string($db, $_POST["description"]);
  $category = mysqli_real_escape_string($db, $_POST["category"]);
  $start_date = mysqli_real_escape_string($db, $_POST["start_date"]);
  $end_date = mysqli_real_escape_string($db, $_POST["end_date"]);
  $query = "INSERT INTO programs (program_name, description, category, start_date, end_date) VALUES ('$program_name', '$description', '$category', '$start_date', '$end_date')";
  mysqli_query($db, $query);
  exit('success');
}

// Handle Edit Program
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
  $program_id = intval($_POST["program_id"]);
  $program_name = mysqli_real_escape_string($db, $_POST["program_name"]);
  $description = mysqli_real_escape_string($db, $_POST["description"]);
  $category = mysqli_real_escape_string($db, $_POST["category"]);
  $start_date = mysqli_real_escape_string($db, $_POST["start_date"]);
  $end_date = mysqli_real_escape_string($db, $_POST["end_date"]);
  $query = "UPDATE programs SET program_name='$program_name', description='$description', category='$category', start_date='$start_date', end_date='$end_date' WHERE program_id=$program_id";
  mysqli_query($db, $query);
  exit('success');
}

// Handle fetch for edit modal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'fetch') {
  $program_id = intval($_POST["program_id"]);
  $result = mysqli_query($db, "SELECT * FROM programs WHERE program_id=$program_id");
  echo json_encode(mysqli_fetch_assoc($result));
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Programs Management</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body style="padding: 25px;">
  <style>
    body, html {
      background: linear-gradient(to right, #03AF34 0%, #FFF84A 100%) !important;
    }
    .container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 30px;
    }
  </style>
  <div class="container">
 <?php if ($_SESSION['position'] != 'Resident'): ?>
  <button type="button" class="btn btn-info btn-lg pull-right" data-toggle="modal" data-target="#addModal">
    <i class="fa fa-user-plus"></i>  
  </button><?php endif; ?>
      <h2>
      <?php echo ($_SESSION['position'] == 'Barangay Secretary') ? "PROGRAM LIST" : "PROGRAM LIST"; ?>
    </h2>
<table id="programsTable" class="table table-bordered">
  <thead>
    <tr>
      <th>Name</th>
      <th>Category</th>
      <th>Description</th>
      <th>Start Date</th>
      <th>End Date</th>
      <?php if ($_SESSION['position'] != 'Resident'): ?>
        <th>Participants</th>
      <?php endif; ?>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $result = mysqli_query($db, "SELECT * FROM programs ORDER BY program_id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
      ?>
      <tr>
        <td><?= htmlspecialchars($row['program_name']) ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= htmlspecialchars($row['start_date']) ?></td>
        <td><?= htmlspecialchars($row['end_date']) ?></td>
        <?php if ($_SESSION['position'] != 'Resident'): ?>
         <td>
        <?php
          $participant_count = 0;
          if (!empty($row['participants'])) {
            $ids = array_filter(explode(',', $row['participants']));
            $participant_count = count($ids);
          }
          echo $participant_count;
        ?>
      </td>

        <?php endif; ?>
        <td>
          <?php if ($_SESSION['position'] == 'Resident'): ?>
            <?php
              $user_id = $_SESSION['id'];
              $participants = $row['participants'] ? explode(',', $row['participants']) : [];
              $already = in_array($user_id, $participants);
            ?>
            <button class="btn btn-success btn-xs participateBtn" data-id="<?= $row['program_id'] ?>" <?= $already ? 'disabled' : '' ?>>
              <?= $already ? 'Participated' : 'Participate' ?>
            </button>
          <?php else: ?>
            <button class="btn btn-info btn-xs editBtn" data-id="<?= $row['program_id'] ?>"><i class="fa fa-edit"></i> Edit</button>
          <?php endif; ?>
        </td>
      </tr>
      <?php
    }
    ?>
  </tbody>
</table>

<?php if ($_SESSION['position'] != 'Resident'): ?>
  <!-- Add Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="addForm" method="POST">
    <input type="hidden" name="action" value="add">
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Program</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <div class="form-group">
        <label>Program Name*</label>
        <input required type="text" name="program_name" class="form-control">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" class="form-control">
      </div>
      <div class="form-group">
        <label>Start Date</label>
        <input type="date" name="start_date" class="form-control">
      </div>
      <div class="form-group">
        <label>End Date</label>
        <input type="date" name="end_date" class="form-control">
      </div>
      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-info">Add</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
    </form>
  </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="editForm" method="POST">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="program_id" id="edit_program_id">
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Edit Program</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <div class="form-group">
        <label>Program Name*</label>
        <input required type="text" name="program_name" id="edit_program_name" class="form-control">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" id="edit_description" class="form-control"></textarea>
      </div>
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" id="edit_category" class="form-control">
      </div>
      <div class="form-group">
        <label>Start Date</label>
        <input type="date" name="start_date" id="edit_start_date" class="form-control">
      </div>
      <div class="form-group">
        <label>End Date</label>
        <input type="date" name="end_date" id="edit_end_date" class="form-control">
      </div>
      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-info">Save</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
    </form>
  </div>
  </div>
<?php endif; ?>

<script>
$(document).ready(function () {
  $('#programsTable').DataTable();

  <?php if ($_SESSION['position'] != 'Resident'): ?>
  // Add/Edit AJAX as before
  $('#addForm').submit(function (e) {
    e.preventDefault();
    $.post('', $(this).serialize(), function (resp) {
      if (resp === 'success') location.reload();
    });
  });
  $('.editBtn').click(function () {
    var id = $(this).data('id');
    $.post('', {action: 'fetch', program_id: id}, function (data) {
      var prog = JSON.parse(data);
      $('#edit_program_id').val(prog.program_id);
      $('#edit_program_name').val(prog.program_name);
      $('#edit_description').val(prog.description);
      $('#edit_category').val(prog.category);
      $('#edit_start_date').val(prog.start_date);
      $('#edit_end_date').val(prog.end_date);
      $('#editModal').modal('show');
    });
  });
  $('#editForm').submit(function (e) {
    e.preventDefault();
    $.post('', $(this).serialize(), function (resp) {
      if (resp === 'success') location.reload();
    });
  });
  <?php endif; ?>

  // Participate AJAX
  $('.participateBtn').click(function () {
    var btn = $(this);
    var id = btn.data('id');
    $.post('', {action: 'participate', program_id: id}, function (resp) {
      if (resp === 'success') {
        btn.text('Participated').prop('disabled', true);
      }
    });
  });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<script>
setInterval(function () {
  // DARK MODE TOGGLE
  var darkEnabled = localStorage.getItem('darkmode') === 'true';
  document.body.classList.toggle('dark-mode', darkEnabled);
  document.documentElement.classList.toggle('dark-mode', darkEnabled);
  console.log('Dark mode enabled:', darkEnabled);

  // FONT SIZE TOGGLE
  var fontSize = localStorage.getItem('fontsize'); // expected: small, medium, large

  // Remove all font size classes first
  document.body.classList.remove('font-small', 'font-medium', 'font-large');
  document.documentElement.classList.remove('font-small', 'font-medium', 'font-large');

  // Apply current font size class
  if (['small', 'medium', 'large'].includes(fontSize)) {
    document.body.classList.add('font-' + fontSize);
    document.documentElement.classList.add('font-' + fontSize);
  }
  console.log('Font size:', fontSize);
}, 500);
</script>
