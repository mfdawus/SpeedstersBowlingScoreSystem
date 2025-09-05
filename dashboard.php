<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPEEDSTERS - Bowling System Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .countdown-box {
      transition: all 0.3s ease;
    }
    .countdown-box:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }
    /* Fixed positioning for dashboard elements */
    .page-wrapper {
      position: relative;
      min-height: 100vh;
    }
    .body-wrapper {
      position: relative;
      min-height: calc(100vh - 70px);
    }
    .body-wrapper-inner {
      position: relative;
      padding: 20px;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">

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
              <a class="sidebar-link" href="./group-selection.php" aria-expanded="false">
                <i class="ti ti-users"></i>
                <span class="hide-menu">Join Group</span>
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
                    <i class="ti ti-users"></i>
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
        <!-- Tournament Countdown Banner -->
        <div class="bg-gradient-primary py-2 px-3 text-white">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
              <i class="ti ti-trophy text-warning fs-4"></i>
              <div>
                <h6 class="mb-0 fw-bold">SPEEDSTERS Championship 2025</h6>
                <small class="text-white-50">Next Bowling Tournament</small>
              </div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div class="d-flex gap-2">
                <div class="text-center">
                  <div class="bg-white bg-opacity-20 rounded px-2 py-1">
                    <span class="text-white fw-bold fs-6" id="days">00</span>
                  </div>
                  <small class="text-white-50" style="font-size: 10px;">Days</small>
                </div>
                <div class="text-center">
                  <div class="bg-white bg-opacity-20 rounded px-2 py-1">
                    <span class="text-white fw-bold fs-6" id="hours">00</span>
                  </div>
                  <small class="text-white-50" style="font-size: 10px;">Hours</small>
                </div>
                <div class="text-center">
                  <div class="bg-white bg-opacity-20 rounded px-2 py-1">
                    <span class="text-white fw-bold fs-6" id="minutes">00</span>
                  </div>
                  <small class="text-white-50" style="font-size: 10px;">Min</small>
                </div>
                <div class="text-center">
                  <div class="bg-white bg-opacity-20 rounded px-2 py-1">
                    <span class="text-white fw-bold fs-6" id="seconds">00</span>
                  </div>
                  <small class="text-white-50" style="font-size: 10px;">Sec</small>
                </div>
              </div>
              <a class="btn btn-warning btn-sm d-flex align-items-center gap-1" href="javascript:void(0)">
                <i class="ti ti-calendar-event fs-6"></i>
                Register
              </a>
            </div>
          </div>
        </div>
        
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
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
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
      
      <div class="body-wrapper-inner">
        <div class="container-fluid">
          <!--  Row 1 - Player Dashboard -->
          <div class="row">
            <!-- Personal Bowling Statistics -->
            <div class="col-lg-8">
              <div class="card w-100">
                <div class="card-body">
                  <div class="d-md-flex align-items-center">
                    <div>
                      <h4 class="card-title">My Bowling Performance</h4>
                      <p class="card-subtitle">
                        Your personal bowling statistics
                      </p>
                    </div>
                    <div class="ms-auto">
                      <button class="btn btn-primary btn-sm">
                        <i class="ti ti-refresh me-1"></i>
                        Update Stats
                      </button>
                    </div>
                  </div>
                  
                  <!-- Performance Stats Grid -->
                  <div class="row mt-4">
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-primary fw-bold">187</div>
                        <small class="text-muted">Average Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-success fw-bold">245</div>
                        <small class="text-muted">Best Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-warning fw-bold">156</div>
                        <small class="text-muted">Games Played</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-info fw-bold">78%</div>
                        <small class="text-muted">Strike Rate</small>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Team Format Scores -->
                  <div class="row mt-4">
                    <div class="col-12">
                      <h6 class="mb-3">Team Format Performance</h6>
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <div class="card border-primary">
                            <div class="card-body text-center">
                              <h6 class="card-title text-primary">Solo Games</h6>
                              <div class="display-6 text-primary fw-bold">189</div>
                              <small class="text-muted">Average Score</small>
                              <div class="mt-2">
                                <span class="badge bg-primary">Best: 245</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="card border-success">
                            <div class="card-body text-center">
                              <h6 class="card-title text-success">Doubles</h6>
                              <div class="display-6 text-success fw-bold">175</div>
                              <small class="text-muted">Average Score</small>
                              <div class="mt-2">
                                <span class="badge bg-success">Best: 198</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="card border-warning">
                            <div class="card-body text-center">
                              <h6 class="card-title text-warning">Team (4-6 Players)</h6>
                              <div class="display-6 text-warning fw-bold">162</div>
                              <small class="text-muted">Average Score</small>
                              <div class="mt-2">
                                <span class="badge bg-warning">Best: 185</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Performance Chart -->
                  <div class="mt-4">
                    <h6 class="mb-3">Last 10 Games Performance</h6>
                    <div id="performance-chart" style="height: 200px;"></div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Quick Actions & Recent Activity -->
            <div class="col-lg-4">
              <div class="card overflow-hidden">
                <div class="card-body pb-0">
                  <div class="d-flex align-items-start">
                    <div>
                      <h4 class="card-title">Quick Actions</h4>
                      <p class="card-subtitle">Book lanes & join events</p>
                    </div>
                  </div>
                  
                  <!-- Quick Action Buttons -->
                  <div class="mt-4">
                    <button class="btn btn-primary w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                      <i class="ti ti-calendar-plus fs-5"></i>
                      Book a Lane
                    </button>
                    <button class="btn btn-warning w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                      <i class="ti ti-trophy fs-5"></i>
                      Join Tournament
                    </button>
                    <button class="btn btn-success w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                      <i class="ti ti-users fs-5"></i>
                      View Schedule
                    </button>
                  </div>
                  
                  <!-- Recent Activity -->
                  <div class="mt-4">
                    <h6 class="mb-3">Recent Activity</h6>
                    <div class="d-flex align-items-center mb-3">
                      <span class="btn btn-outline-primary rounded-circle round-32 hstack justify-content-center me-3">
                        <i class="ti ti-bowling fs-6"></i>
                      </span>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">Game Completed</h6>
                        <small class="text-muted">Score: 189 - 2 hours ago</small>
                      </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                      <span class="btn btn-outline-success rounded-circle round-32 hstack justify-content-center me-3">
                        <i class="ti ti-trophy fs-6"></i>
                      </span>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">Tournament Won</h6>
                        <small class="text-muted">SPEEDSTERS Cup - Yesterday</small>
                      </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                      <span class="btn btn-outline-warning rounded-circle round-32 hstack justify-content-center me-3">
                        <i class="ti ti-calendar fs-6"></i>
                      </span>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">Lane Booked</h6>
                        <small class="text-muted">Lane 5 - Tomorrow 2:00 PM</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Recent Games & Upcoming Events -->
          <div class="row mt-4">
            <!-- Recent Games -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-md-flex align-items-center">
                    <div>
                      <h4 class="card-title">Recent Games</h4>
                      <p class="card-subtitle">Your last 5 bowling games</p>
                    </div>
                    <div class="ms-auto">
                      <button class="btn btn-outline-primary btn-sm">View All</button>
                    </div>
                  </div>
                  <div class="table-responsive mt-4">
                    <table class="table mb-0 text-nowrap align-middle fs-3">
                      <thead>
                        <tr>
                          <th scope="col" class="px-0 text-muted">Date</th>
                          <th scope="col" class="px-0 text-muted">Lane</th>
                          <th scope="col" class="px-0 text-muted">Format</th>
                          <th scope="col" class="px-0 text-muted">Score</th>
                          <th scope="col" class="px-0 text-muted text-end">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="px-0">
                            <div>
                              <h6 class="mb-0 fw-bolder">Today</h6>
                              <small class="text-muted">2:30 PM</small>
                            </div>
                          </td>
                          <td class="px-0">Lane 3</td>
                          <td class="px-0">
                            <span class="badge bg-primary">Solo</span>
                          </td>
                          <td class="px-0">
                            <span class="fw-bold text-primary">189</span>
                          </td>
                          <td class="px-0 text-end">
                            <span class="badge bg-success">Completed</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-0">
                            <div>
                              <h6 class="mb-0 fw-bolder">Yesterday</h6>
                              <small class="text-muted">7:15 PM</small>
                            </div>
                          </td>
                          <td class="px-0">Lane 7</td>
                          <td class="px-0">
                            <span class="badge bg-success">Doubles</span>
                          </td>
                          <td class="px-0">
                            <span class="fw-bold text-success">245</span>
                          </td>
                          <td class="px-0 text-end">
                            <span class="badge bg-success">Completed</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-0">
                            <div>
                              <h6 class="mb-0 fw-bolder">Mar 12</h6>
                              <small class="text-muted">4:45 PM</small>
                            </div>
                          </td>
                          <td class="px-0">Lane 2</td>
                          <td class="px-0">
                            <span class="badge bg-warning">Team</span>
                          </td>
                          <td class="px-0">
                            <span class="fw-bold text-warning">167</span>
                          </td>
                          <td class="px-0 text-end">
                            <span class="badge bg-success">Completed</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-0">
                            <div>
                              <h6 class="mb-0 fw-bolder">Mar 10</h6>
                              <small class="text-muted">6:20 PM</small>
                            </div>
                          </td>
                          <td class="px-0">Lane 5</td>
                          <td class="px-0">
                            <span class="badge bg-primary">Solo</span>
                          </td>
                          <td class="px-0">
                            <span class="fw-bold text-info">198</span>
                          </td>
                          <td class="px-0 text-end">
                            <span class="badge bg-success">Completed</span>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-0">
                            <div>
                              <h6 class="mb-0 fw-bolder">Mar 8</h6>
                              <small class="text-muted">3:10 PM</small>
                            </div>
                          </td>
                          <td class="px-0">Lane 1</td>
                          <td class="px-0">
                            <span class="badge bg-success">Doubles</span>
                          </td>
                          <td class="px-0">
                            <span class="fw-bold text-primary">176</span>
                          </td>
                          <td class="px-0 text-end">
                            <span class="badge bg-success">Completed</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Upcoming Events -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-md-flex align-items-center">
                    <div>
                      <h4 class="card-title">Upcoming Events</h4>
                      <p class="card-subtitle">Tournaments & bookings</p>
                    </div>
                    <div class="ms-auto">
                      <button class="btn btn-outline-primary btn-sm">View All</button>
                    </div>
                  </div>
                  <div class="mt-4">
                    <!-- Event Item 1 -->
                    <div class="d-flex align-items-center p-3 border rounded mb-3">
                      <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="ti ti-trophy text-white fs-5"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">SPEEDSTERS Championship</h6>
                        <small class="text-muted">Mar 15, 2025 • 6:00 PM</small>
                        <div class="mt-1">
                          <span class="badge bg-warning">Registered</span>
                          <small class="text-muted ms-2">Prize: $500</small>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Event Item 2 -->
                    <div class="d-flex align-items-center p-3 border rounded mb-3">
                      <div class="bg-success rounded-circle p-2 me-3">
                        <i class="ti ti-calendar text-white fs-5"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">Lane Booking</h6>
                        <small class="text-muted">Tomorrow • 2:00 PM</small>
                        <div class="mt-1">
                          <span class="badge bg-info">Lane 5</span>
                          <small class="text-muted ms-2">2 hours</small>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Event Item 3 -->
                    <div class="d-flex align-items-center p-3 border rounded mb-3">
                      <div class="bg-warning rounded-circle p-2 me-3">
                        <i class="ti ti-users text-white fs-5"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">Team League Match</h6>
                        <small class="text-muted">Mar 20, 2025 • 7:30 PM</small>
                        <div class="mt-1">
                          <span class="badge bg-secondary">Pending</span>
                          <small class="text-muted ms-2">vs Team Thunder</small>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Event Item 4 -->
                    <div class="d-flex align-items-center p-3 border rounded">
                      <div class="bg-info rounded-circle p-2 me-3">
                        <i class="ti ti-star text-white fs-5"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">Practice Session</h6>
                        <small class="text-muted">Mar 22, 2025 • 4:00 PM</small>
                        <div class="mt-1">
                          <span class="badge bg-success">Confirmed</span>
                          <small class="text-muted ms-2">Lane 3</small>
                        </div>
                      </div>
                    </div>
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
  <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="./assets/js/dashboard.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <!-- Countdown Timer Script -->
  <script>
    // Set the target date for the tournament (you can change this)
    const targetDate = new Date('2025-03-15T18:00:00').getTime();
    
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
