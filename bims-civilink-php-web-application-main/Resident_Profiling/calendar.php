<?php
session_start();
require("../db.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $event_date = $_POST['event_date'];
  $location = $_POST['location'];

  $query = "INSERT INTO calendar (title, description, event_date, location) VALUES ('$title', '$description', '$event_date', '$location')";
  mysqli_query($db, $query);
  mysqli_close($db);
  header("Location: calendar.php");
  exit();
}

// Fetch events from the database
$query = "SELECT id, title, description, event_date, location FROM calendar";
$result = mysqli_query($db, $query);

$events = [];

while ($row = mysqli_fetch_assoc($result)) {
  $events[] = [
    'id' => $row['id'],
    'title' => $row['title'],
    'description' => $row['description'],
    'start' => $row['event_date'],
    'location' => $row['location']
  ];
}

mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Management Information System</title>

  <!-- Bootstrap CSS -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/css/dataTables.bootstrap.min.css" rel="stylesheet">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

  <!-- FullCalendar CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

  <!-- FullCalendar JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="../assets/js/bootstrap.min.js"></script>
  
</head>

<body style="padding: 25px;">

<?php if (
  (isset($_SESSION['position']) && $_SESSION['position'] === 'Barangay Health Worker') ||
  (isset($_SESSION['position']) && $_SESSION['position'] === 'Barangay Secretary')
): ?>
<!-- Add Event Button -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEventModal">
  Add Event
</button>
<?php endif; ?>
<br>
<br>
<div id="calendar"></div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="addEventModalLabel">Add Event</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addEventForm" method="POST" action="calendar.php">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
          </div>
          <div class="form-group">
            <label for="event_date">Date</label>
            <input type="datetime-local" class="form-control" id="event_date" name="event_date" required>
          </div>
          <div class="form-group">
            <label for="location">Location</label>
            <input type="text" class="form-control" id="location" name="location" required>
          </div>
          <button type="submit" class="btn btn-primary">Add Event</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="eventModalLabel">Event Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="eventTitle"></span></p>
        <p><strong>Description:</strong> <span id="eventDescription"></span></p>
        <p><strong>Date:</strong> <span id="eventDate"></span></p>
        <p><strong>Location:</strong> <span id="eventLocation"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    let events = <?php echo json_encode($events); ?>; // PHP array converted to JS

    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      editable: false,
      events: events, // Load events directly
      eventClick: function(event) {
        $('#eventTitle').text(event.title);
        $('#eventDescription').text(event.description);
        $('#eventDate').text(event.start.format('MMMM Do YYYY, h:mm a'));
        $('#eventLocation').text(event.location);
        $('#eventModal').modal('show');
      }
    });
  });
</script>

</body>
</html>


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
