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
      min-height: calc(100vh - 70px);
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
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 100; padding-top: 0;">
    <div class="app-topstrip bg-gradient-primary py-0 px-3 w-100 d-flex align-items-center justify-content-between flex-wrap">
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

    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img d-flex flex-column align-items-start text-decoration-none">
            <img src="assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="90" />
            <span class="text-muted fw-semibold mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Bowling Score System</span>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./dashboard.php" aria-expanded="false">
                <i class="ti ti-atom"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./lane-booking.php" aria-expanded="false">
                <i class="ti ti-calendar-plus"></i>
                <span class="hide-menu">Lane Booking</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./events.php" aria-expanded="false">
                <i class="ti ti-calendar-event"></i>
                <span class="hide-menu">Events</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-table"></i>
                <span class="hide-menu">Score Table</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="./score-table-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Games</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-team.php" class="sidebar-link">
                    <i class="ti ti-users-group"></i>
                    <span class="hide-menu">Team (4-6 Players)</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">   
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-bell"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="message-body">
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 1
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 2
                  </a>
                </div>
              </div>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
               
              <li class="nav-item dropdown">
                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="./my-profile.php" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="./authentication-login.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      
      <div class="body-wrapper-inner" style="margin-top: 80px;">
        <div class="welcome-section">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8 col-md-10">
                <div class="welcome-card p-10 text-center">
                  <div class="mb-4">
                    <img src="assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="120" class="mb-3" />
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
                  
                  <div class="d-flex gap-3 justify-content-center">
                    <a href="./dashboard.php" class="btn btn-primary btn-lg">
                      <i class="ti ti-atom me-2"></i>
                      Go to Dashboard
                    </a>
                    <a href="./lane-booking.php" class="btn btn-outline-primary btn-lg">
                      <i class="ti ti-calendar-plus me-2"></i>
                      Book a Lane
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
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
