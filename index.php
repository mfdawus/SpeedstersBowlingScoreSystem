<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPEEDSTERS - Bowling Score System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .welcome-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .welcome-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    .logo-link {
      text-decoration: none;
      transition: transform 0.3s ease;
    }
    .logo-link:hover {
      transform: scale(1.05);
    }
  </style>
</head>

<body>
  <!-- Tournament Banner -->
  <div class="app-topstrip bg-gradient-primary py-3 px-3 w-100 d-flex align-items-center justify-content-between flex-wrap">
    <!-- Left side: Tournament Info -->
    <div class="d-flex align-items-center gap-2 mb-2 mb-lg-0">
      <i class="ti ti-trophy text-warning fs-4"></i>
      <div>
        <h6 class="mb-0 fw-bold text-white">SPEEDSTERS Championship 2025</h6>
        <small class="text-white-50 d-block">Next Bowling Tournament</small>
      </div>
    </div>

    <!-- Right side: Countdown + Register Button -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div class="d-flex gap-2">
       <div class="text-center"> <div class="bg-white bg-opacity-20 rounded p-n2 mt-1">
            <span class="text-white fw-bold fs-6" id="days">00</span>
          </div>
          <small class="text-white-50" style="font-size: 10px;">Days</small>
        </div>
       <div class="text-center"> <div class="bg-white bg-opacity-20 rounded p-n2 mt-1">
            <span class="text-white fw-bold fs-6" id="hours">00</span>
          </div>
          <small class="text-white-50" style="font-size: 10px;">Hours</small>
        </div>
       <div class="text-center"> <div class="bg-white bg-opacity-20 rounded p-n2 mt-1">
            <span class="text-white fw-bold fs-6" id="minutes">00</span>
          </div>
          <small class="text-white-50" style="font-size: 10px;">Min</small>
        </div>
       <div class="text-center"> <div class="bg-white bg-opacity-20 rounded p-n2 mt-1">
            <span class="text-white fw-bold fs-6" id="seconds">00</span>
          </div>
          <small class="text-white-50" style="font-size: 10px;">Sec</small>
        </div>
      </div>

      <a class="btn btn-warning btn-sm d-flex align-items-center gap-1">
        <i class="ti ti-calendar-event fs-6"></i>
        Register
      </a>
    </div>
  </div>

  <!-- Main Welcome Content -->
  <div class="welcome-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
          <div class="welcome-card p-5 text-center">
            <div class="mb-4">
              <a href="./homepage.php" class="logo-link">
                <img src="assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="120" class="mb-3" />
              </a>
              <h1 class="display-4 fw-bold text-primary mb-3">Welcome to SPEEDSTERS</h1>
              <p class="lead text-muted mb-4">Your complete bowling score management system</p>
            </div>
            
            <div class="row g-4 mb-5">
              <div class="col-md-4">
                <div class="p-3">
                  <i class="ti ti-atom text-primary fs-1 mb-3"></i>
                  <h5>Dashboard</h5>
                  <p class="text-muted small">Track your performance and statistics</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3">
                  <i class="ti ti-calendar-plus text-success fs-1 mb-3"></i>
                  <h5>Lane Booking</h5>
                  <p class="text-muted small">Book lanes for your games</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3">
                  <i class="ti ti-table text-warning fs-1 mb-3"></i>
                  <h5>Score Tables</h5>
                  <p class="text-muted small">View rankings and scores</p>
                </div>
              </div>
            </div>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap">
              <a href="./authentication-login.php" class="btn btn-primary btn-lg">
                <i class="ti ti-login me-2"></i>
                Login
              </a>
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
  
  <!-- Countdown Timer Script -->
  <script>
    // Set the target date for the tournament (you can change this)
    const targetDate = new Date('2025-12-15T18:00:00').getTime();
    
    function updateCountdown() {
      const now = new Date().getTime();
      const distance = targetDate - now;
      
      if (distance < 0) {
        // Event has passed
        document.getElementById('days').innerHTML = '00';
        document.getElementById('hours').innerHTML = '00';
        document.getElementById('minutes').innerHTML = '00';
        document.getElementById('seconds').innerHTML = '00';
        return;
      }
      
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      
      document.getElementById('days').innerHTML = days.toString().padStart(2, '0');
      document.getElementById('hours').innerHTML = hours.toString().padStart(2, '0');
      document.getElementById('minutes').innerHTML = minutes.toString().padStart(2, '0');
      document.getElementById('seconds').innerHTML = seconds.toString().padStart(2, '0');
    }
    
    // Update countdown every second
    setInterval(updateCountdown, 1000);
    
    // Initial call
    updateCountdown();
  </script>
</body>

</html>