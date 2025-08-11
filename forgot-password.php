<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPEEDSTERS - Forgot Password</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .step-indicator {
      display: flex;
      justify-content: center;
      margin-bottom: 2rem;
    }
    .step {
      display: flex;
      align-items: center;
      margin: 0 1rem;
    }
    .step-number {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-right: 0.5rem;
    }
    .step.active .step-number {
      background-color: #0d6efd;
      color: white;
    }
    .step.inactive .step-number {
      background-color: #e9ecef;
      color: #6c757d;
    }
    .step-line {
      width: 60px;
      height: 2px;
      background-color: #e9ecef;
      margin: 0 0.5rem;
    }
    .step.active .step-line {
      background-color: #0d6efd;
    }
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .btn-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
      border: none;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    .card {
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border-radius: 15px;
    }
    .card-body {
      padding: 2.5rem;
    }
    .logo-img img {
      transition: transform 0.3s ease;
    }
    .logo-img:hover img {
      transform: scale(1.05);
    }
    .input-group-text {
      background-color: #f8f9fa;
      border-right: none;
    }
    .input-group .form-control {
      border-left: none;
    }
    .input-group .form-control:focus {
      border-left: none;
    }
    .tac-input {
      letter-spacing: 0.5em;
      text-align: center;
      font-size: 1.2rem;
      font-weight: bold;
    }
    .countdown {
      color: #dc3545;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="./assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="200">
                </a>
                <h4 class="text-center mb-4">Forgot Password</h4>
                
                <!-- Step Indicator -->
                <div class="step-indicator">
                  <div class="step active" id="step1-indicator">
                    <div class="step-number">1</div>
                    <span>Phone</span>
                  </div>
                  <div class="step-line"></div>
                  <div class="step inactive" id="step2-indicator">
                    <div class="step-number">2</div>
                    <span>Reset</span>
                  </div>
                </div>

                <form id="forgotPasswordForm">
                  <!-- Step 1: Phone Number Entry -->
                  <div id="step1">
                    <div class="mb-4">
                      <label for="phoneNumber" class="form-label fw-bold">
                        <i class="iconify me-2" data-icon="mdi:phone"></i>
                        Phone Number
                      </label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="iconify" data-icon="mdi:phone"></i>
                        </span>
                        <input type="tel" class="form-control" id="phoneNumber" placeholder="Enter your registered phone number" required>
                      </div>
                      <small class="text-muted">We'll send a verification code to your phone</small>
                    </div>
                    <button type="button" class="btn btn-primary w-100 py-3 fs-5 mb-4 rounded-3" onclick="sendTAC()">
                      <i class="iconify me-2" data-icon="mdi:send"></i>
                      Send Verification Code
                    </button>
                  </div>

                  <!-- Step 2: TAC Entry -->
                  <div id="step2" style="display: none;">
                    <div class="mb-4">
                      <label for="tacCode" class="form-label fw-bold">
                        <i class="iconify me-2" data-icon="mdi:lock"></i>
                        Verification Code
                      </label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="iconify" data-icon="mdi:lock"></i>
                        </span>
                        <input type="text" class="form-control tac-input" id="tacCode" placeholder="000000" maxlength="6" required>
                      </div>
                      <small class="text-muted">Enter the 6-digit code sent to your phone</small>
                      <div class="countdown mt-2" id="countdown" style="display: none;">
                        <i class="iconify me-1" data-icon="mdi:clock-outline"></i>
                        <span id="timer">05:00</span>
                      </div>
                    </div>
                    
                    <div class="mb-4">
                      <label for="newPassword" class="form-label fw-bold">
                        <i class="iconify me-2" data-icon="mdi:key"></i>
                        New Password
                      </label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="iconify" data-icon="mdi:key"></i>
                        </span>
                        <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required>
                      </div>
                    </div>
                    
                    <div class="mb-4">
                      <label for="confirmPassword" class="form-label fw-bold">
                        <i class="iconify me-2" data-icon="mdi:key-check"></i>
                        Confirm Password
                      </label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="iconify" data-icon="mdi:key-check"></i>
                        </span>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password" required>
                      </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary w-100 py-3 fs-5 mb-3 rounded-3" onclick="resetPassword()">
                      <i class="iconify me-2" data-icon="mdi:check-circle"></i>
                      Reset Password
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100 py-3 fs-5 mb-4 rounded-3" onclick="backToStep1()">
                      <i class="iconify me-2" data-icon="mdi:arrow-left"></i>
                      Back to Phone Entry
                    </button>
                  </div>

                  <div class="text-center">
                    <a href="./authentication-login.php" class="text-primary fw-bold text-decoration-none">
                      <i class="iconify me-1" data-icon="mdi:arrow-left"></i>
                      Back to Login
                    </a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    function sendTAC() {
      const phoneNumber = document.getElementById('phoneNumber').value;
      
      if (!phoneNumber) {
        alert('Please enter your phone number');
        return;
      }

      // Show step 2 and update indicators
      document.getElementById('step1').style.display = 'none';
      document.getElementById('step2').style.display = 'block';
      document.getElementById('step1-indicator').classList.remove('active');
      document.getElementById('step1-indicator').classList.add('inactive');
      document.getElementById('step2-indicator').classList.remove('inactive');
      document.getElementById('step2-indicator').classList.add('active');
      
      // Start countdown timer
      startCountdown();
      
      alert('Verification code has been sent to your phone number');
    }

    function resetPassword() {
      const tacCode = document.getElementById('tacCode').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (!tacCode || !newPassword || !confirmPassword) {
        alert('Please fill in all fields');
        return;
      }

      if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return;
      }

      if (newPassword.length < 6) {
        alert('Password must be at least 6 characters long');
        return;
      }

      alert('Password has been reset successfully!');
      window.location.href = './authentication-login.php';
    }

    function backToStep1() {
      document.getElementById('step1').style.display = 'block';
      document.getElementById('step2').style.display = 'none';
      document.getElementById('step1-indicator').classList.remove('inactive');
      document.getElementById('step1-indicator').classList.add('active');
      document.getElementById('step2-indicator').classList.remove('active');
      document.getElementById('step2-indicator').classList.add('inactive');
      
      document.getElementById('tacCode').value = '';
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmPassword').value = '';
      
      // Stop countdown
      clearInterval(window.countdownInterval);
      document.getElementById('countdown').style.display = 'none';
    }

    function startCountdown() {
      let timeLeft = 300; // 5 minutes in seconds
      const countdownElement = document.getElementById('countdown');
      const timerElement = document.getElementById('timer');
      
      countdownElement.style.display = 'block';
      
      window.countdownInterval = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
          clearInterval(window.countdownInterval);
          countdownElement.innerHTML = '<span class="text-danger">Code expired. Please request a new one.</span>';
        }
        
        timeLeft--;
      }, 1000);
    }

    // Auto-format phone number input
    document.getElementById('phoneNumber').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 0) {
        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
      }
      e.target.value = value;
    });

    // Auto-format TAC code (numbers only)
    document.getElementById('tacCode').addEventListener('input', function(e) {
      e.target.value = e.target.value.replace(/\D/g, '');
    });
  </script>
</body>

</html> 