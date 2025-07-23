<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Barangay Monitoring System</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <div class="wrapper">
    <div class="overlay">
      <img src="logo.png" alt="Logo" class="logo" />
      <div class="title">Barangay Monitoring System</div>
      <div class="subtitle">Efficiently manage residents, health programs, and emergency alerts.</div>

      <div class="button-group">
        <a href="login.php" class="btn" oncl >Login</a>
      </div>

      <h2 class="section-title">Key Features</h2>
      <div class="features">
        <div class="feature-box"><i class="fas fa-users"></i><p>Resident Management</p></div>
        <div class="feature-box"><i class="fas fa-heartbeat"></i><p>Health Program Tracking</p></div>
        <div class="feature-box"><i class="fas fa-syringe"></i><p>Vaccination Records</p></div>
        <div class="feature-box"><i class="fas fa-bell"></i><p>Emergency Alerts</p></div>
        <div class="feature-box"><i class="fas fa-sms"></i><p>SMS Notifications</p></div>
        <div class="feature-box"><i class="fas fa-chart-line"></i><p>Reports & Analytics</p></div>
      </div>
    </div>
    <footer class="footer">© 2025 Barangay Monitoring System. All Rights Reserved.</footer>
  </div>

  <!-- Login Modal -->
  <div id="loginModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal('loginModal')">&times;</span>
      <h2 class="modal-title">Login</h2>
      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="loginUsername">Username</label>
          <input type="text" id="loginUsername" name="username" required />
        </div>
        <div class="form-group">
          <label for="loginPassword">Password</label>
          <input type="password" id="loginPassword" name="password" required />
        </div>
        <button class="login-btn" type="submit">Login</button>
        <div class="forgot-password"><a href="#">Forgot Password?</a></div>
      </form>
    </div>
  </div>

  <!-- Register Modal -->
  <div id="registerModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal('registerModal')">&times;</span>
      <h2 class="modal-title">Register</h2>
      <form action="register.php" method="POST">
        <div class="form-group">
          <label for="regFullName">Full Name</label>
          <input type="text" id="regFullName" name="full_name" required />
        </div>
        <div class="form-group">
          <label for="regUsername">Username</label>
          <input type="text" id="regUsername" name="username" required />
        </div>
        <div class="form-group">
          <label for="regEmail">Email</label>
          <input type="email" id="regEmail" name="email" required />
        </div>
        <div class="form-group">
          <label for="regPassword">Password</label>
          <input type="password" id="regPassword" name="password" required />
        </div>
        <button class="login-btn" type="submit">Register</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }
  </script>
</body>
</html>
