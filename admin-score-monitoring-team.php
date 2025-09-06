<?php
require_once 'includes/auth.php';
requireAdmin(); // Ensure only admins can access this page

// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('team-admin', 'Team Score Monitoring (Admin)');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Team Score Monitoring - SPEEDSTERS Bowling System</title>
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
    .rank-badge {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
    }
    .rank-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); }
    .rank-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%); }
    .rank-3 { background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%); }
    .rank-other { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
    .player-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      object-fit: cover;
    }
    .team-avatars {
      display: flex;
      align-items: center;
    }
    .team-avatars img {
      margin-right: -8px;
      border: 2px solid white;
    }
    .score-highlight {
      font-weight: bold;
      font-size: 1.1rem;
    }
    .score-excellent { color: #28a745; }
    .score-good { color: #17a2b8; }
    .score-average { color: #ffc107; }
    .score-below { color: #dc3545; }
    .admin-actions {
      display: flex;
      gap: 5px;
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
              <a class="sidebar-link" href="./admin-dashboard.php" aria-expanded="false">
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
                  <a href="./admin-score-monitoring-team.php" class="sidebar-link active">
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
              <a class="sidebar-link" href="javascript:void(0)" aria-expanded="false">
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
                    <li class="breadcrumb-item"><a href="./admin-dashboard.php">Admin Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Monitoring</a></li>
                    <li class="breadcrumb-item active">Team (4-6 Players)</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Admin Statistics Overview -->
          <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Total Teams</h6>
                      <h3 class="mb-0 text-primary">23</h3>
                      <small class="text-muted">+2 new this week</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users-group fs-1 text-muted"></i>
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
                      <h6 class="card-title text-muted mb-1">Active Today</h6>
                      <h3 class="mb-0 text-success">12</h3>
                      <small class="text-muted">+3 vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users-check fs-1 text-success"></i>
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
                      <h6 class="card-title text-muted mb-1">Avg Team Score</h6>
                      <h3 class="mb-0 text-warning">1,156.4</h3>
                      <small class="text-muted">+18.7 vs last week</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-target fs-1 text-warning"></i>
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
                      <h6 class="card-title text-muted mb-1">Games Today</h6>
                      <h3 class="mb-0 text-info">36</h3>
                      <small class="text-muted">+8% vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-bowling fs-1 text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Page Content -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">
                        <i class="ti ti-users me-2 text-primary"></i>
                        Team Score Monitoring (4-6 Players)
                      </h5>
                      <span class="fw-normal text-muted">Admin view with enhanced team management features</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-success btn-sm" onclick="exportData()">
                        <i class="ti ti-download me-1"></i>
                        Export
                      </button>
                      <button class="btn btn-warning btn-sm" onclick="bulkEdit()">
                        <i class="ti ti-edit me-1"></i>
                        Bulk Edit
                      </button>
                      <button class="btn btn-info btn-sm" onclick="manageTeams()">
                        <i class="ti ti-users-plus me-1"></i>
                        Manage Teams
                      </button>
                      <button class="btn btn-secondary btn-sm" onclick="teamAnalytics()">
                        <i class="ti ti-chart-line me-1"></i>
                        Analytics
                      </button>
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="all">All Time</option>
                        <option value="custom">Custom Date</option>
                      </select>
                      <input type="date" class="form-control form-control-sm" id="customDate" style="width: auto; display: none;">
                      <button class="btn btn-primary btn-sm" onclick="refreshTable()">
                        <i class="ti ti-refresh"></i>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Game Selection Tabs -->
                  <ul class="nav nav-tabs mb-3" id="gameTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="overall-tab" data-bs-toggle="tab" data-bs-target="#overall" type="button" role="tab">
                        Overall Rankings
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game1-tab" data-bs-toggle="tab" data-bs-target="#game1" type="button" role="tab">
                        Game 1
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game2-tab" data-bs-toggle="tab" data-bs-target="#game2" type="button" role="tab">
                        Game 2
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game3-tab" data-bs-toggle="tab" data-bs-target="#game3" type="button" role="tab">
                        Game 3
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game4-tab" data-bs-toggle="tab" data-bs-target="#game4" type="button" role="tab">
                        Game 4
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game5-tab" data-bs-toggle="tab" data-bs-target="#game5" type="button" role="tab">
                        Game 5
                      </button>
                    </li>
                  </ul>

                  <div class="tab-content" id="gameTabContent">
                    <!-- Overall Tab -->
                    <div class="tab-pane fade show active" id="overall" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Combined Strikes</th>
                              <th scope="col">Status</th>
                              <th scope="col">Last Updated</th>
                              <th scope="col">Admin Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="player-avatar">
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
                              <td>75</td>
                              <td><span class="badge bg-success">Active</span></td>
                              <td><small class="text-muted">2 hours ago</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewTeamDetails('team1')" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-warning" onclick="editTeamScore('team1')" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-info" onclick="viewTeamHistory('team1')" title="View History">
                                    <i class="ti ti-history"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-secondary" onclick="manageTeamMembers('team1')" title="Manage Team">
                                    <i class="ti ti-users"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="player-avatar">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 5" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Crushers</h6>
                                    <small class="text-muted">Pro Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>David, Lisa, James, Anna, Mark</td>
                              <td><span class="fw-bold text-success">2,145</span></td>
                              <td>214.5</td>
                              <td>4</td>
                              <td><span class="text-warning">485</span></td>
                              <td>72</td>
                              <td><span class="badge bg-success">Active</span></td>
                              <td><small class="text-muted">1 hour ago</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewTeamDetails('team2')" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-warning" onclick="editTeamScore('team2')" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-info" onclick="viewTeamHistory('team2')" title="View History">
                                    <i class="ti ti-history"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-secondary" onclick="manageTeamMembers('team2')" title="Manage Team">
                                    <i class="ti ti-users"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 4" class="player-avatar">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 5" class="player-avatar">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 6" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Strike Force</h6>
                                    <small class="text-muted">Advanced Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Chris, Sarah, Mike, Lisa, Tom, Emma</td>
                              <td><span class="fw-bold text-success">2,089</span></td>
                              <td>208.9</td>
                              <td>4</td>
                              <td><span class="text-warning">472</span></td>
                              <td>68</td>
                              <td><span class="badge bg-warning">Pending</span></td>
                              <td><small class="text-muted">30 min ago</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewTeamDetails('team3')" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-warning" onclick="editTeamScore('team3')" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-info" onclick="viewTeamHistory('team3')" title="View History">
                                    <i class="ti ti-history"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-secondary" onclick="manageTeamMembers('team3')" title="Manage Team">
                                    <i class="ti ti-users"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-info">4</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Seekers</h6>
                                    <small class="text-muted">Intermediate Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Alex, Maria, Chris, Wilson</td>
                              <td><span class="fw-bold text-success">1,956</span></td>
                              <td>195.6</td>
                              <td>4</td>
                              <td><span class="text-warning">445</span></td>
                              <td>62</td>
                              <td><span class="badge bg-success">Active</span></td>
                              <td><small class="text-muted">15 min ago</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewTeamDetails('team4')" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-warning" onclick="editTeamScore('team4')" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-info" onclick="viewTeamHistory('team4')" title="View History">
                                    <i class="ti ti-history"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-secondary" onclick="manageTeamMembers('team4')" title="Manage Team">
                                    <i class="ti ti-users"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-dark">5</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="player-avatar">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 5" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Gutter Guards</h6>
                                    <small class="text-muted">Beginner Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>David, Anna, Mark, Lisa, James</td>
                              <td><span class="fw-bold text-success">1,823</span></td>
                              <td>182.3</td>
                              <td>4</td>
                              <td><span class="text-warning">412</span></td>
                              <td>58</td>
                              <td><span class="badge bg-danger">Inactive</span></td>
                              <td><small class="text-muted">5 min ago</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewTeamDetails('team5')" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-warning" onclick="editTeamScore('team5')" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-info" onclick="viewTeamHistory('team5')" title="View History">
                                    <i class="ti ti-history"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-secondary" onclick="manageTeamMembers('team5')" title="Manage Team">
                                    <i class="ti ti-users"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 1 Tab -->
                    <div class="tab-pane fade" id="game1" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Combined Strikes</th>
                              <th scope="col">Time</th>
                              <th scope="col">Admin Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">498</span></td>
                              <td>125, 128, 120, 125</td>
                              <td>15</td>
                              <td><small class="text-muted">9:30 AM</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-warning" onclick="editGameScore('team1', 1)" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-danger" onclick="deleteGameScore('team1', 1)" title="Delete Score">
                                    <i class="ti ti-trash"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="player-avatar">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 5" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Crushers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>David, Lisa, James, Anna, Mark</td>
                              <td><span class="fw-bold text-success">485</span></td>
                              <td>98, 102, 95, 98, 92</td>
                              <td>14</td>
                              <td><small class="text-muted">9:45 AM</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-warning" onclick="editGameScore('team2', 1)" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-danger" onclick="deleteGameScore('team2', 1)" title="Delete Score">
                                    <i class="ti ti-trash"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 1" class="player-avatar">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 2" class="player-avatar">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 3" class="player-avatar">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 4" class="player-avatar">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 5" class="player-avatar">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 6" class="player-avatar">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Strike Force</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Chris, Sarah, Mike, Lisa, Tom, Emma</td>
                              <td><span class="fw-bold text-success">472</span></td>
                              <td>78, 82, 75, 78, 82, 77</td>
                              <td>13</td>
                              <td><small class="text-muted">10:00 AM</small></td>
                              <td>
                                <div class="admin-actions">
                                  <button class="btn btn-sm btn-outline-warning" onclick="editGameScore('team3', 1)" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-danger" onclick="deleteGameScore('team3', 1)" title="Delete Score">
                                    <i class="ti ti-trash"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Additional game tabs would follow similar pattern -->
                    <div class="tab-pane fade" id="game2" role="tabpanel">
                      <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Game 2 data would be loaded here with similar admin functionality.
                      </div>
                    </div>

                    <div class="tab-pane fade" id="game3" role="tabpanel">
                      <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Game 3 data would be loaded here with similar admin functionality.
                      </div>
                    </div>

                    <div class="tab-pane fade" id="game4" role="tabpanel">
                      <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Game 4 data would be loaded here with similar admin functionality.
                      </div>
                    </div>

                    <div class="tab-pane fade" id="game5" role="tabpanel">
                      <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        Game 5 data would be loaded here with similar admin functionality.
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
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Admin-specific functions for teams
    function viewTeamDetails(teamId) {
      showNotification('Opening detailed view for team: ' + teamId, 'info');
      // Here you would open a detailed team modal or navigate to team details page
    }

    function editTeamScore(teamId) {
      showNotification('Opening score editor for team: ' + teamId, 'info');
      // Here you would open the team score editing interface
    }

    function viewTeamHistory(teamId) {
      showNotification('Loading score history for team: ' + teamId, 'info');
      // Here you would load and display team's complete score history
    }

    function manageTeamMembers(teamId) {
      showNotification('Opening team member management for: ' + teamId, 'info');
      // Here you would open team member management interface
    }

    function editGameScore(teamId, gameNumber) {
      showNotification('Editing Game ' + gameNumber + ' score for team: ' + teamId, 'warning');
      // Here you would open a quick edit modal for the specific game score
    }

    function deleteGameScore(teamId, gameNumber) {
      if (confirm('Are you sure you want to delete Game ' + gameNumber + ' score for team: ' + teamId + '?')) {
        showNotification('Score deleted successfully!', 'success');
        // Here you would make the actual deletion
      }
    }

    function exportData() {
      showNotification('Exporting team data...', 'info');
      // Here you would generate and download the data export
    }

    function bulkEdit() {
      showNotification('Opening bulk edit interface for teams...', 'info');
      // Here you would open a bulk editing interface
    }

    function manageTeams() {
      showNotification('Opening team management interface...', 'info');
      // Here you would open team creation/management interface
    }

    function teamAnalytics() {
      showNotification('Opening team analytics dashboard...', 'info');
      // Here you would open team performance analytics
    }

    // Date filter functionality
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
      const customDateInput = document.getElementById('customDate');
      
      if (selectedDate === 'custom') {
        customDateInput.style.display = 'inline-block';
        customDateInput.focus();
      } else {
        customDateInput.style.display = 'none';
        console.log('Date filter changed to:', selectedDate);
        showNotification('Loading data for ' + selectedDate + '...', 'info');
      }
    });

    // Custom date input functionality
    document.getElementById('customDate').addEventListener('change', function() {
      const selectedDate = this.value;
      if (selectedDate) {
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        console.log('Custom date selected:', selectedDate);
        showNotification('Loading data for ' + formattedDate + '...', 'info');
      }
    });

    // Refresh table functionality
    function refreshTable() {
      const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
      const icon = refreshBtn.querySelector('i');
      
      // Add spinning animation
      icon.classList.add('ti-spin');
      
      // Simulate loading
      setTimeout(() => {
        icon.classList.remove('ti-spin');
        showNotification('Admin team table refreshed successfully!', 'success');
      }, 1000);
    }

    // Tab switching with data loading simulation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        console.log('Switched to admin team tab:', targetId);
        
        // Simulate loading data for specific game
        if (targetId !== '#overall') {
          const gameNumber = targetId.replace('#game', '');
          showNotification('Loading Game ' + gameNumber + ' admin team data...', 'info');
        }
      });
    });

    // Notification function
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

    // Auto-refresh table every 30 seconds
    setInterval(() => {
      if (!document.hidden) {
        console.log('Auto-refreshing admin team table...');
      }
    }, 30000);
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
  
  <?php include 'includes/admin-popup.php'; ?>
</body>

</html>
