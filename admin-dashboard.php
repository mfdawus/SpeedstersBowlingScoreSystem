<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .admin-card {
      transition: all 0.3s ease;
      border-left: 4px solid #0d6efd;
    }
    .admin-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .stats-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    .team-card {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }
    .player-card {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }
    .admin-badge {
      background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
      color: #333;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">
    
    <!-- Tournament Countdown Banner -->
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
            <span class="text-muted fw-semibold mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Admin Panel</span>
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
              <span class="hide-menu">Admin Panel</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./admin-dashboard.php" aria-expanded="false">
                <i class="ti ti-dashboard"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-user-management.php">
                <i class="ti ti-users"></i>
                <span class="hide-menu">User Management</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-events.php">
                <i class="ti ti-calendar-event"></i>
                <span class="hide-menu">Events Management</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-chart-bar"></i>
                <span class="hide-menu">Score Monitoring</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Players</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles Teams</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-team.php" class="sidebar-link">
                    <i class="ti ti-users-group"></i>
                    <span class="hide-menu">Team (4-6 Players)</span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-score-update.php">
                <i class="ti ti-edit"></i>
                <span class="hide-menu">Update Scores</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-create-account.php">
                <i class="ti ti-user-plus"></i>
                <span class="hide-menu">Create Account</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-settings"></i>
                <span class="hide-menu">System Settings</span>
              </a>
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
                      <p class="mb-0 fs-3">Admin Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-settings fs-6"></i>
                      <p class="mb-0 fs-3">Settings</p>
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
          <!-- Page Header -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./index.php">Home</a></li>
                    <li class="breadcrumb-item active">Admin Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Admin Statistics Overview -->
          <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card stats-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-white-50 mb-1">Total Players</h6>
                      <h3 class="mb-0 text-white">1,247</h3>
                      <small class="text-white-50">+12% this month</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users fs-1 text-white-50"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card team-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-white-50 mb-1">Active Teams</h6>
                      <h3 class="mb-0 text-white">89</h3>
                      <small class="text-white-50">+5 new this week</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users-group fs-1 text-white-50"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card player-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-white-50 mb-1">Games Today</h6>
                      <h3 class="mb-0 text-white">156</h3>
                      <small class="text-white-50">+23% vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-bowling fs-1 text-white-50"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Revenue</h6>
                      <h3 class="mb-0 text-primary">$12,450</h3>
                      <small class="text-muted">+8% this month</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-currency-dollar fs-1 text-muted"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content Row -->
          <div class="row">
            <!-- Team Performance Monitoring -->
            <div class="col-lg-8">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Team Performance Overview</h5>
                      <span class="fw-normal text-muted">Real-time monitoring of all teams and players</span>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="timeFilter">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="all">All Time</option>
                      </select>
                      <button class="btn btn-primary btn-sm" onclick="refreshData()">
                        <i class="ti ti-refresh"></i>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Performance Tabs -->
                  <ul class="nav nav-tabs mb-3" id="performanceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="overall-tab" data-bs-toggle="tab" data-bs-target="#overall" type="button" role="tab">
                        Overall Rankings
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="solo-tab" data-bs-toggle="tab" data-bs-target="#solo" type="button" role="tab">
                        Solo Players
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="doubles-tab" data-bs-toggle="tab" data-bs-target="#doubles" type="button" role="tab">
                        Doubles Teams
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="teams-tab" data-bs-toggle="tab" data-bs-target="#teams" type="button" role="tab">
                        Team (4-6 Players)
                      </button>
                    </li>
                  </ul>

                  <div class="tab-content" id="performanceTabContent">
                    <!-- Overall Rankings Tab -->
                    <div class="tab-pane fade show active" id="overall" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team/Player</th>
                              <th scope="col">Type</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games</th>
                              <th scope="col">Best Score</th>
                              <th scope="col">Status</th>
                              <th scope="col">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                  <div>
                                    <h6 class="mb-0">Thunder Strikers</h6>
                                    <small class="text-muted">John & Sarah</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="badge bg-success">Doubles</span></td>
                              <td><span class="fw-bold text-success">2,443</span></td>
                              <td>244.3</td>
                              <td>5</td>
                              <td><span class="text-warning">547</span></td>
                              <td><span class="badge bg-success">Active</span></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('team1')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-3.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                  <div>
                                    <h6 class="mb-0">Mike Johnson</h6>
                                    <small class="text-muted">Solo Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="badge bg-primary">Solo</span></td>
                              <td><span class="fw-bold text-success">2,312</span></td>
                              <td>231.2</td>
                              <td>8</td>
                              <td><span class="text-warning">289</span></td>
                              <td><span class="badge bg-success">Active</span></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('player1')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-5.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                  <div>
                                    <h6 class="mb-0">Lane Masters</h6>
                                    <small class="text-muted">Tom, Emma, Alex, Maria</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="badge bg-warning">Team</span></td>
                              <td><span class="fw-bold text-success">2,178</span></td>
                              <td>217.8</td>
                              <td>4</td>
                              <td><span class="text-warning">498</span></td>
                              <td><span class="badge bg-warning">Pending</span></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('team2')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Solo Players Tab -->
                    <div class="tab-pane fade" id="solo" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Player</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games</th>
                              <th scope="col">Best Score</th>
                              <th scope="col">Strike Rate</th>
                              <th scope="col">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-3.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                  <div>
                                    <h6 class="mb-0">Mike Johnson</h6>
                                    <small class="text-muted">Pro Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">2,312</span></td>
                              <td>231.2</td>
                              <td>8</td>
                              <td><span class="text-warning">289</span></td>
                              <td>85%</td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('player1')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-2.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                  <div>
                                    <h6 class="mb-0">Sarah Wilson</h6>
                                    <small class="text-muted">Elite Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">2,198</span></td>
                              <td>219.8</td>
                              <td>7</td>
                              <td><span class="text-warning">275</span></td>
                              <td>82%</td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('player2')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Doubles Teams Tab -->
                    <div class="tab-pane fade" id="doubles" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games</th>
                              <th scope="col">Best Score</th>
                              <th scope="col">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="32">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Thunder Strikers</h6>
                                    <small class="text-muted">Pro Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>John & Sarah</td>
                              <td><span class="fw-bold text-success">2,443</span></td>
                              <td>244.3</td>
                              <td>5</td>
                              <td><span class="text-warning">547</span></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('team1')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Team (4-6 Players) Tab -->
                    <div class="tab-pane fade" id="teams" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games</th>
                              <th scope="col">Best Score</th>
                              <th scope="col">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="32">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Masters</h6>
                                    <small class="text-muted">Elite Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">2,178</span></td>
                              <td>217.8</td>
                              <td>4</td>
                              <td><span class="text-warning">498</span></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('team2')">
                                  <i class="ti ti-eye"></i>
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions & Account Management -->
            <div class="col-lg-4">
              <!-- Create New Account Card -->
              <div class="card admin-card mb-4">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <i class="ti ti-user-plus fs-4 text-primary me-2"></i>
                    <h5 class="card-title mb-0">Create New Account</h5>
                  </div>
                  <p class="text-muted mb-3">Add new players or teams to the system</p>
                  
                  <div class="d-grid gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlayerModal">
                      <i class="ti ti-user-plus me-2"></i>
                      Add New Player
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTeamModal">
                      <i class="ti ti-users-plus me-2"></i>
                      Create Team
                    </button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                      <i class="ti ti-upload me-2"></i>
                      Bulk Import
                    </button>
                  </div>
                </div>
              </div>


              <!-- Recent Activity -->
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <i class="ti ti-activity fs-4 text-info me-2"></i>
                    <h5 class="card-title mb-0">Recent Activity</h5>
                  </div>
                  
                  <div class="activity-list">
                    <div class="d-flex align-items-center mb-3">
                      <div class="bg-success rounded-circle p-2 me-3">
                        <i class="ti ti-user-plus text-white fs-6"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">New Player Added</h6>
                        <small class="text-muted">Mike Johnson joined - 2 hours ago</small>
                      </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                      <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="ti ti-trophy text-white fs-6"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">High Score Record</h6>
                        <small class="text-muted">Thunder Strikers scored 547 - 1 hour ago</small>
                      </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                      <div class="bg-warning rounded-circle p-2 me-3">
                        <i class="ti ti-users text-white fs-6"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">Team Created</h6>
                        <small class="text-muted">Lane Masters team formed - 3 hours ago</small>
                      </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                      <div class="bg-info rounded-circle p-2 me-3">
                        <i class="ti ti-calendar text-white fs-6"></i>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">Tournament Scheduled</h6>
                        <small class="text-muted">SPEEDSTERS Championship - Mar 15</small>
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

  <!-- Create Player Modal -->
  <div class="modal fade" id="createPlayerModal" tabindex="-1" aria-labelledby="createPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPlayerModalLabel">Create New Player Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="createPlayerForm">
            <div class="mb-3">
              <label for="playerName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="playerName" required>
            </div>
            <div class="mb-3">
              <label for="playerEmail" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="playerEmail" required>
            </div>
            <div class="mb-3">
              <label for="playerPhone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="playerPhone">
            </div>
            <div class="mb-3">
              <label for="playerSkill" class="form-label">Skill Level</label>
              <select class="form-select" id="playerSkill" required>
                <option value="">Select Skill Level</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="pro">Professional</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="playerPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="playerPassword" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="createPlayer()">Create Player</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Team Modal -->
  <div class="modal fade" id="createTeamModal" tabindex="-1" aria-labelledby="createTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createTeamModalLabel">Create New Team</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="createTeamForm">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="teamName" class="form-label">Team Name</label>
                <input type="text" class="form-control" id="teamName" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="teamType" class="form-label">Team Type</label>
                <select class="form-select" id="teamType" required>
                  <option value="">Select Team Type</option>
                  <option value="doubles">Doubles (2 Players)</option>
                  <option value="team">Team (4-6 Players)</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="teamDescription" class="form-label">Team Description</label>
              <textarea class="form-control" id="teamDescription" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Select Team Members</label>
              <div class="row" id="teamMembersList">
                <!-- Team members will be populated here -->
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="createTeam()">Create Team</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bulk Import Modal -->
  <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bulkImportModalLabel">Bulk Import Users</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="importFile" class="form-label">Upload CSV File</label>
            <input type="file" class="form-control" id="importFile" accept=".csv">
            <div class="form-text">Upload a CSV file with columns: Name, Email, Phone, Skill Level</div>
          </div>
          <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i>
            <strong>CSV Format:</strong> Name, Email, Phone, Skill Level (beginner/intermediate/advanced/pro)
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="bulkImport()">Import Users</button>
        </div>
      </div>
    </div>
  </div>


  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Admin Dashboard Functions
    function refreshData() {
      const refreshBtn = document.querySelector('button[onclick="refreshData()"]');
      const icon = refreshBtn.querySelector('i');
      
      icon.classList.add('ti-spin');
      
      setTimeout(() => {
        icon.classList.remove('ti-spin');
        showNotification('Data refreshed successfully!', 'success');
      }, 1000);
    }

    function viewDetails(id) {
      showNotification('Opening details for ' + id + '...', 'info');
      // Here you would typically open a detailed view or modal
    }

    function createPlayer() {
      const form = document.getElementById('createPlayerForm');
      const formData = new FormData(form);
      
      // Simulate API call
      setTimeout(() => {
        showNotification('Player account created successfully!', 'success');
        $('#createPlayerModal').modal('hide');
        form.reset();
      }, 1000);
    }

    function createTeam() {
      const form = document.getElementById('createTeamForm');
      const formData = new FormData(form);
      
      // Simulate API call
      setTimeout(() => {
        showNotification('Team created successfully!', 'success');
        $('#createTeamModal').modal('hide');
        form.reset();
      }, 1000);
    }

    function bulkImport() {
      const fileInput = document.getElementById('importFile');
      const file = fileInput.files[0];
      
      if (!file) {
        showNotification('Please select a CSV file to import', 'warning');
        return;
      }
      
      // Simulate file processing
      setTimeout(() => {
        showNotification('Users imported successfully!', 'success');
        $('#bulkImportModal').modal('hide');
        fileInput.value = '';
      }, 2000);
    }

    // Score Update Functions
    function updateScore() {
      const form = document.getElementById('updateScoreForm');
      const formData = new FormData(form);
      
      // Validate required fields
      const player = document.getElementById('scorePlayer').value;
      const gameType = document.getElementById('scoreGame').value;
      const gameDate = document.getElementById('scoreDate').value;
      
      if (!player || !gameType || !gameDate) {
        showNotification('Please fill in all required fields', 'warning');
        return;
      }
      
      // Check if it's a team or solo player
      const isTeam = player.startsWith('team');
      
      if (isTeam) {
        // Validate team scores
        if (!validateTeamScores()) {
          return;
        }
      } else {
        // Validate solo player scores
        const game1 = document.getElementById('scoreGame1').value;
        const game2 = document.getElementById('scoreGame2').value;
        const game3 = document.getElementById('scoreGame3').value;
        const game4 = document.getElementById('scoreGame4').value;
        const game5 = document.getElementById('scoreGame5').value;
        
        const scores = [game1, game2, game3, game4, game5].filter(score => score !== '');
        if (scores.length === 0) {
          showNotification('Please enter at least one game score', 'warning');
          return;
        }
        
        for (let score of scores) {
          if (score < 0 || score > 300) {
            showNotification('Scores must be between 0 and 300', 'warning');
            return;
          }
        }
      }
      
      // Simulate API call
      setTimeout(() => {
        showNotification('Score updated successfully!', 'success');
        $('#updateScoreModal').modal('hide');
        form.reset();
        document.getElementById('scoreTotal').value = '';
        refreshData(); // Refresh the dashboard data
      }, 1000);
    }

    function validateTeamScores() {
      // Get all team member scores and validate
      const teamMembers = ['team1', 'team2', 'team3', 'team4', 'team5', 'team6'];
      let hasAnyScores = false;
      
      for (let member of teamMembers) {
        const memberCard = document.getElementById(member + 'Card');
        if (memberCard && memberCard.style.display !== 'none') {
          let memberHasScores = false;
          for (let game = 1; game <= 5; game++) {
            const score = document.getElementById(member + 'Game' + game).value;
            if (score !== '') {
              memberHasScores = true;
              hasAnyScores = true;
              if (score < 0 || score > 300) {
                showNotification(`Invalid score for ${member} Game ${game}. Scores must be between 0 and 300`, 'warning');
                return false;
              }
            }
          }
        }
      }
      
      if (!hasAnyScores) {
        showNotification('Please enter at least one game score for any team member', 'warning');
        return false;
      }
      
      return true;
    }

    function bulkScoreUpdate() {
      const fileInput = document.getElementById('bulkScoreFile');
      const file = fileInput.files[0];
      
      if (!file) {
        showNotification('Please select a CSV file to upload', 'warning');
        return;
      }
      
      // Simulate file processing
      setTimeout(() => {
        showNotification('Scores uploaded and updated successfully!', 'success');
        $('#bulkScoreUpdateModal').modal('hide');
        fileInput.value = '';
        refreshData(); // Refresh the dashboard data
      }, 2000);
    }

    function exportScoreHistory() {
      const player = document.getElementById('historyPlayer').value;
      if (!player) {
        showNotification('Please select a player/team first', 'warning');
        return;
      }
      
      // Simulate export
      setTimeout(() => {
        showNotification('Score history exported successfully!', 'success');
      }, 1000);
    }

    function loadScoreHistory() {
      const player = document.getElementById('historyPlayer').value;
      const dateRange = document.getElementById('historyDateRange').value;
      
      if (!player) {
        document.getElementById('scoreHistoryTable').querySelector('tbody').innerHTML = 
          '<tr><td colspan="11" class="text-center text-muted">Select a player/team to view score history</td></tr>';
        return;
      }
      
      // Simulate loading score history
      const sampleData = [
        {
          date: '2024-01-15 14:30',
          gameType: 'Tournament',
          game1: 245,
          game2: 267,
          game3: 289,
          game4: 275,
          game5: 298,
          total: 1374,
          average: 274.8,
          notes: 'Championship match - 5 games'
        },
        {
          date: '2024-01-12 19:00',
          gameType: 'League',
          game1: 198,
          game2: 234,
          game3: 256,
          game4: 0,
          game5: 0,
          total: 688,
          average: 229.3,
          notes: 'Weekly league game - 3 games'
        },
        {
          date: '2024-01-10 16:45',
          gameType: 'Practice',
          game1: 189,
          game2: 201,
          game3: 223,
          game4: 195,
          game5: 0,
          total: 808,
          average: 202,
          notes: 'Practice session - 4 games'
        }
      ];
      
      const tbody = document.getElementById('scoreHistoryTable').querySelector('tbody');
      tbody.innerHTML = sampleData.map(game => `
        <tr>
          <td>${game.date}</td>
          <td><span class="badge bg-primary">${game.gameType}</span></td>
          <td>${game.game1 || '-'}</td>
          <td>${game.game2 || '-'}</td>
          <td>${game.game3 || '-'}</td>
          <td>${game.game4 || '-'}</td>
          <td>${game.game5 || '-'}</td>
          <td><strong>${game.total}</strong></td>
          <td>${game.average}</td>
          <td>${game.notes}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary" onclick="editScore(${game.total})">
              <i class="ti ti-edit"></i>
            </button>
          </td>
        </tr>
      `).join('');
    }

    function editScore(scoreId) {
      showNotification('Edit score functionality would open here', 'info');
    }


    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 3000);
    }

    // Auto-refresh data every 30 seconds
    setInterval(() => {
      if (!document.hidden) {
        console.log('Auto-refreshing admin data...');
      }
    }, 30000);

    // Load team members for team creation
    document.getElementById('teamType').addEventListener('change', function() {
      const teamType = this.value;
      const membersList = document.getElementById('teamMembersList');
      
      if (teamType === 'doubles') {
        membersList.innerHTML = `
          <div class="col-md-6 mb-2">
            <select class="form-select" name="player1">
              <option value="">Select Player 1</option>
              <option value="player1">Mike Johnson</option>
              <option value="player2">Sarah Wilson</option>
            </select>
          </div>
          <div class="col-md-6 mb-2">
            <select class="form-select" name="player2">
              <option value="">Select Player 2</option>
              <option value="player1">Mike Johnson</option>
              <option value="player2">Sarah Wilson</option>
            </select>
          </div>
        `;
      } else if (teamType === 'team') {
        membersList.innerHTML = `
          <div class="col-md-6 mb-2">
            <select class="form-select" name="player1">
              <option value="">Select Player 1</option>
              <option value="player1">Mike Johnson</option>
              <option value="player2">Sarah Wilson</option>
            </select>
          </div>
          <div class="col-md-6 mb-2">
            <select class="form-select" name="player2">
              <option value="">Select Player 2</option>
              <option value="player1">Mike Johnson</option>
              <option value="player2">Sarah Wilson</option>
            </select>
          </div>
          <div class="col-md-6 mb-2">
            <select class="form-select" name="player3">
              <option value="">Select Player 3</option>
              <option value="player1">Mike Johnson</option>
              <option value="player2">Sarah Wilson</option>
            </select>
          </div>
          <div class="col-md-6 mb-2">
            <select class="form-select" name="player4">
              <option value="">Select Player 4</option>
              <option value="player1">Mike Johnson</option>
              <option value="player2">Sarah Wilson</option>
            </select>
          </div>
        `;
      }
    });

    // Score calculation functionality
    function calculateTotalScore() {
      const game1 = parseInt(document.getElementById('scoreGame1').value) || 0;
      const game2 = parseInt(document.getElementById('scoreGame2').value) || 0;
      const game3 = parseInt(document.getElementById('scoreGame3').value) || 0;
      const game4 = parseInt(document.getElementById('scoreGame4').value) || 0;
      const game5 = parseInt(document.getElementById('scoreGame5').value) || 0;
      const total = game1 + game2 + game3 + game4 + game5;
      document.getElementById('scoreTotal').value = total;
    }

    function calculateTeamMemberScore(memberNumber) {
      let total = 0;
      for (let game = 1; game <= 5; game++) {
        const score = parseInt(document.getElementById(`team${memberNumber}Game${game}`).value) || 0;
        total += score;
      }
      document.getElementById(`team${memberNumber}Total`).value = total;
      calculateTeamTotal();
    }

    function calculateTeamTotal() {
      let teamTotal = 0;
      let activeMembers = 0;
      
      // Calculate total for all active team members
      for (let member = 1; member <= 6; member++) {
        const memberCard = document.getElementById(`teamMember${member}Card`);
        if (memberCard && memberCard.style.display !== 'none') {
          const memberTotal = parseInt(document.getElementById(`team${member}Total`).value) || 0;
          teamTotal += memberTotal;
          activeMembers++;
        }
      }
      
      document.getElementById('teamTotalScore').value = teamTotal;
      
      // Calculate team average
      const teamAverage = activeMembers > 0 ? (teamTotal / activeMembers).toFixed(1) : 0;
      document.getElementById('teamAverageScore').value = teamAverage;
    }

    function handlePlayerSelection() {
      const selectedPlayer = document.getElementById('scorePlayer').value;
      const soloInput = document.getElementById('soloScoreInput');
      const teamInput = document.getElementById('teamScoreInput');
      
      if (selectedPlayer.startsWith('team')) {
        // Show team input, hide solo input
        soloInput.style.display = 'none';
        teamInput.style.display = 'block';
        
        // Configure team members based on team type
        configureTeamMembers(selectedPlayer);
      } else {
        // Show solo input, hide team input
        soloInput.style.display = 'block';
        teamInput.style.display = 'none';
      }
    }

    function configureTeamMembers(teamId) {
      // Team member configurations
      const teamConfigs = {
        'team1': { // Thunder Strikers (Doubles)
          members: [
            { name: 'John Smith', color: 'bg-primary' },
            { name: 'Sarah Wilson', color: 'bg-success' }
          ]
        },
        'team2': { // Lightning Bolts (Doubles)
          members: [
            { name: 'Mike Johnson', color: 'bg-primary' },
            { name: 'Alex Rodriguez', color: 'bg-success' }
          ]
        },
        'team3': { // Lane Masters (4-6 Players)
          members: [
            { name: 'Tom Anderson', color: 'bg-primary' },
            { name: 'Emma Davis', color: 'bg-success' },
            { name: 'Alex Chen', color: 'bg-warning text-dark' },
            { name: 'Maria Garcia', color: 'bg-info' },
            { name: 'Chris Wilson', color: 'bg-danger' },
            { name: 'Sarah Johnson', color: 'bg-dark' }
          ]
        },
        'team4': { // Pin Crushers (4-6 Players)
          members: [
            { name: 'David Lee', color: 'bg-primary' },
            { name: 'Lisa Brown', color: 'bg-success' },
            { name: 'James Wilson', color: 'bg-warning text-dark' },
            { name: 'Anna Taylor', color: 'bg-info' },
            { name: 'Mark Thompson', color: 'bg-danger' }
          ]
        }
      };
      
      const config = teamConfigs[teamId];
      
      if (config) {
        // Show/hide team member cards based on team size
        for (let i = 1; i <= 6; i++) {
          const memberCard = document.getElementById(`teamMember${i}Card`);
          const memberName = document.getElementById(`teamMember${i}Name`);
          
          if (i <= config.members.length) {
            memberCard.style.display = 'block';
            memberName.textContent = config.members[i-1].name;
            memberCard.querySelector('.card-header').className = `card-header ${config.members[i-1].color} text-white`;
          } else {
            memberCard.style.display = 'none';
          }
        }
      }
    }

    // Add event listeners for score calculation
    document.addEventListener('DOMContentLoaded', function() {
      // Solo player score calculation listeners
      const scoreInputs = ['scoreGame1', 'scoreGame2', 'scoreGame3', 'scoreGame4', 'scoreGame5'];
      scoreInputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          element.addEventListener('input', calculateTotalScore);
        }
      });

      // Team member score calculation listeners
      for (let member = 1; member <= 6; member++) {
        for (let game = 1; game <= 5; game++) {
          const element = document.getElementById(`team${member}Game${game}`);
          if (element) {
            element.addEventListener('input', () => calculateTeamMemberScore(member));
          }
        }
      }

      // Player selection handler
      const scorePlayer = document.getElementById('scorePlayer');
      if (scorePlayer) {
        scorePlayer.addEventListener('change', handlePlayerSelection);
      }

      // Score history loading
      const historyPlayer = document.getElementById('historyPlayer');
      const historyDateRange = document.getElementById('historyDateRange');
      
      if (historyPlayer) {
        historyPlayer.addEventListener('change', loadScoreHistory);
      }
      if (historyDateRange) {
        historyDateRange.addEventListener('change', loadScoreHistory);
      }

      // Set default date to current date/time
      const scoreDate = document.getElementById('scoreDate');
      if (scoreDate) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        scoreDate.value = `${year}-${month}-${day}T${hours}:${minutes}`;
      }
    });

  </script>
  
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
