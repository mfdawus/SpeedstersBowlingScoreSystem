<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Team Score Table - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
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
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
    }
    .team-avatars {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
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
                  <a href="./score-table-team.php" class="sidebar-link active">
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
                    <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Table</a></li>
                    <li class="breadcrumb-item active">Team (4-6 Players)</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Page Content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Team Score Table (4-6 Players)</h5>
                      <span class="fw-normal text-muted">Multi-player team rankings and scores</span>
                    </div>
                    <div class="d-flex gap-2">
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
                        Overall
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
                              <th scope="col">Team Size</th>
                              <th scope="col">Last Updated</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                    <small class="text-muted">Pro Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">4,856</span></td>
                              <td>242.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,089</span></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">2 hours ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                    <small class="text-muted">Elite Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">5,923</span></td>
                              <td>236.9</td>
                              <td>5</td>
                              <td><span class="text-warning">1,245</span></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">1 hour ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                    <small class="text-muted">Advanced Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">7,134</span></td>
                              <td>237.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,456</span></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">30 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-info">4</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                    <small class="text-muted">Intermediate Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">4,567</span></td>
                              <td>228.4</td>
                              <td>5</td>
                              <td><span class="text-warning">1,023</span></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">15 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-dark">5</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Gutter Gang</h6>
                                    <small class="text-muted">Beginner Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Lisa, Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">5,234</span></td>
                              <td>209.4</td>
                              <td>5</td>
                              <td><span class="text-warning">1,156</span></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">5 min ago</small></td>
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
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,089</span></td>
                              <td><small>279, 268, 275, 267</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">9:30 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,245</span></td>
                              <td><small>248, 256, 242, 251, 248</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">9:45 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">1,456</span></td>
                              <td><small>242, 238, 245, 240, 243, 248</small></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">10:00 AM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 2 Tab -->
                    <div class="tab-pane fade" id="game2" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,267</span></td>
                              <td><small>255, 262, 248, 256, 246</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">10:15 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,234</span></td>
                              <td><small>262, 260, 268, 244</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">10:30 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">1,156</span></td>
                              <td><small>248, 260, 242, 206</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">10:45 AM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 3 Tab -->
                    <div class="tab-pane fade" id="game3" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,145</span></td>
                              <td><small>285, 270, 265, 325</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">11:00 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">1,378</span></td>
                              <td><small>245, 238, 252, 240, 248, 255</small></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">11:15 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,234</span></td>
                              <td><small>248, 256, 242, 251, 237</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">11:30 AM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 4 Tab -->
                    <div class="tab-pane fade" id="game4" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,289</span></td>
                              <td><small>258, 262, 248, 256, 265</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">11:45 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,267</span></td>
                              <td><small>265, 270, 268, 264</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">12:00 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Gutter Gang</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Lisa, Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">1,156</span></td>
                              <td><small>232, 248, 240, 236, 200</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">12:15 PM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 5 Tab -->
                    <div class="tab-pane fade" id="game5" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">1,478</span></td>
                              <td><small>248, 245, 252, 248, 250, 235</small></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">12:30 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,121</span></td>
                              <td><small>269, 280, 265, 307</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">12:45 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">1,089</span></td>
                              <td><small>256, 278, 242, 313</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">1:00 PM</small></td>
                            </tr>
                          </tbody>
                        </table>
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

  <!-- Score Table Functionality -->
  <script>
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
        // Here you would typically make an AJAX call to get filtered data
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
        showNotification('Table refreshed successfully!', 'success');
      }, 1000);
    }

    // Tab switching with data loading simulation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        console.log('Switched to tab:', targetId);
        
        // Simulate loading data for specific game
        if (targetId !== '#overall') {
          const gameNumber = targetId.replace('#game', '');
          showNotification('Loading Game ' + gameNumber + ' data...', 'info');
        }
      });
    });

    // Notification function
    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      document.body.appendChild(notification);
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 3000);
    }

    // Auto-refresh table every 30 seconds
    setInterval(() => {
      // Only refresh if user is on the page
      if (!document.hidden) {
        console.log('Auto-refreshing table...');
      }
    }, 30000);
  </script>
</body>

</html>
