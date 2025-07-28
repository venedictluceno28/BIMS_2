<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard with Mini Calendar</title>

  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      font-family: Calibri, sans-serif;
      background-color: #e4f1e7;
      margin: 0;
      padding: 0;
    }

    .dashboard-header {
      text-align: center;
      padding: 15px;
      background-color: #6BA66F;
      color: white;
      font-size: 26px;
      font-weight: bold;
    }

    .summary-cards {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      margin: 20px;
    }

    .card-box {
      flex: 1 1 150px;
      background-color: #ffffff;
      padding: 15px;
      text-align: center;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .program-table {
      margin: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #6BA66F;
      color: white;
    }

    .main-section {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      margin: 20px;
    }

    #calendar-container {
      width: 320px;
      background-color: #fff;
      border-radius: 10px;
      padding: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      font-size: 12px;
    }

    .calendar-spacer {
      margin-top: 20px;
      padding: 10px;
      background-color: #f6f6f6;
      border: 1px dashed #ccc;
      text-align: center;
      border-radius: 8px;
      font-size: 14px;
      color: #666;
    }

    .charts {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin: 20px;
    }

    .chart-box {
      width: 300px;
      height: 300px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 10px;
    }
  </style>
</head>
<body>

  <div class="dashboard-header">Admin Dashboard</div>

  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="card-box" style="background-color: #bce3c2;">9 Total Residents</div>
    <div class="card-box" style="background-color: #86a8cf;">1 Infants</div>
    <div class="card-box" style="background-color: #e88484;">1 Children</div>
    <div class="card-box" style="background-color: #f3c77a;">1 Teens</div>
    <div class="card-box" style="background-color: #98d98e;">4 Adults</div>
    <div class="card-box" style="background-color: #d8c7c7;">2 Senior Citizens</div>
  </div>

  <!-- Main Content and Mini Calendar -->
  <div class="main-section">
    <!-- Left Content: Programs Table -->
    <div style="flex: 1;">
      <div class="program-table">
        <h5>Upcoming Programs</h5>
        <table id="programsTable">
          <thead>
            <tr>
              <th>Program Name</th>
              <th>Date</th>
              <th>Time</th>
              <th>Category</th>
            </tr>
          </thead>
          <tbody id="programsBody">
            <!-- Program entries will appear here -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right Side: Mini Calendar -->
    <div id="calendar-container">
      <div id="calendar"></div>

      <!-- Spacer area for additional widgets -->
      <div class="calendar-spacer">
        Add notes, alerts, or widgets here
      </div>
    </div>
  </div>

  <!-- Charts Section (Placeholder) -->
  <div class="charts">
    <div class="chart-box">Pie Chart</div>
    <div class="chart-box">Bar Chart</div>
    <div class="chart-box">Line Chart</div>
    <!-- <div class="chart-box">Category Participation</div> -->
  </div>

  <!-- Modal for Program Form -->
  <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="programForm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add / Update Program</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="eventDate" name="eventDate">
            <div class="mb-3">
              <label for="programName" class="form-label">Program Name</label>
              <input type="text" class="form-control" id="programName" required>
            </div>
            <div class="mb-3">
              <label for="programCategory" class="form-label">Category</label>
              <select class="form-select" id="programCategory">
                <option value="Maternal Health">Maternal Health</option>
                <option value="Senior Health">Senior Health</option>
                <option value="Vaccination">Vaccination</option>
                <option value="Emergency">Emergency</option>
                <option value="Others">Others</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="startTime" class="form-label">Start Time</label>
              <input type="time" class="form-control" id="startTime" required>
            </div>
            <div class="mb-3">
              <label for="endTime" class="form-label">End Time</label>
              <input type="time" class="form-control" id="endTime" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save Program</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        dateClick: function(info) {
          document.getElementById('eventDate').value = info.dateStr;
          document.getElementById('programName').value = '';
          document.getElementById('programCategory').value = 'Maternal Health';
          document.getElementById('startTime').value = '';
          document.getElementById('endTime').value = '';
          new bootstrap.Modal(document.getElementById('programModal')).show();
        },
        events: []
      });

      calendar.render();

      document.getElementById('programForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const title = document.getElementById('programName').value;
        const category = document.getElementById('programCategory').value;
        const date = document.getElementById('eventDate').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        const start = `${date}T${startTime}`;
        const end = `${date}T${endTime}`;

        // Add event to calendar
        calendar.addEvent({
          title: `${title} - ${category}`,
          start: start,
          end: end
        });

        // Add event to table
        const tbody = document.getElementById('programsBody');
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${title}</td>
          <td>${new Date(date).toLocaleDateString()}</td>
          <td>${startTime} - ${endTime}</td>
          <td>${category}</td>
        `;
        tbody.appendChild(row);

        bootstrap.Modal.getInstance(document.getElementById('programModal')).hide();
      });
    });
  </script>
</body>
</html>
