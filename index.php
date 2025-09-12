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
  
</body>

</html>