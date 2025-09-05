<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Solo Games Score Table - SPEEDSTERS Bowling System</title>
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
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
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
                  <a href="./score-table-solo.php" class="sidebar-link active">
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
        <div class="container-fluid" style="margin-top: 30px;">
          <!-- Page Header -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Table</a></li>
                    <li class="breadcrumb-item active">Solo Games</li>
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
                      <h5 class="card-title fw-semibold mb-1">Solo Games Score Table</h5>
                      <span class="fw-normal text-muted">Individual player rankings and scores</span>
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
                              <th scope="col">Player</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Last Updated</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">John Smith</h6>
                                    <small class="text-muted">Pro Bowler</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">1,245</span></td>
                              <td>249.0</td>
                              <td>5</td>
                              <td><span class="text-warning">279</span></td>
                              <td>45</td>
                              <td>28</td>
                              <td><small class="text-muted">2 hours ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-2.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">Elite Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">1,198</span></td>
                              <td>239.6</td>
                              <td>5</td>
                              <td><span class="text-warning">268</span></td>
                              <td>42</td>
                              <td>31</td>
                              <td><small class="text-muted">1 hour ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-3.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Mike Davis</h6>
                                    <small class="text-muted">Advanced</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">1,156</span></td>
                              <td>231.2</td>
                              <td>5</td>
                              <td><span class="text-warning">255</span></td>
                              <td>38</td>
                              <td>35</td>
                              <td><small class="text-muted">30 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-info">4</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-4.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Lisa Chen</h6>
                                    <small class="text-muted">Intermediate</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">1,089</span></td>
                              <td>217.8</td>
                              <td>5</td>
                              <td><span class="text-warning">242</span></td>
                              <td>35</td>
                              <td>40</td>
                              <td><small class="text-muted">15 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-dark">5</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-5.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Tom Wilson</h6>
                                    <small class="text-muted">Beginner</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">1,023</span></td>
                              <td>204.6</td>
                              <td>5</td>
                              <td><span class="text-warning">228</span></td>
                              <td>32</td>
                              <td>42</td>
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
                              <th scope="col">Player</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">John Smith</h6>
                                    <small class="text-muted">Pro Bowler</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">279</span></td>
                              <td>10</td>
                              <td>2</td>
                              <td>0</td>
                              <td><small class="text-muted">9:30 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-2.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">Elite Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">268</span></td>
                              <td>9</td>
                              <td>3</td>
                              <td>0</td>
                              <td><small class="text-muted">9:45 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-3.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Mike Davis</h6>
                                    <small class="text-muted">Advanced</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">255</span></td>
                              <td>8</td>
                              <td>4</td>
                              <td>0</td>
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
                              <th scope="col">Player</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-2.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">Elite Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">275</span></td>
                              <td>10</td>
                              <td>2</td>
                              <td>0</td>
                              <td><small class="text-muted">10:15 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">John Smith</h6>
                                    <small class="text-muted">Pro Bowler</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">262</span></td>
                              <td>9</td>
                              <td>3</td>
                              <td>0</td>
                              <td><small class="text-muted">10:30 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-4.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Lisa Chen</h6>
                                    <small class="text-muted">Intermediate</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">248</span></td>
                              <td>8</td>
                              <td>4</td>
                              <td>0</td>
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
                              <th scope="col">Player</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">John Smith</h6>
                                    <small class="text-muted">Pro Bowler</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">285</span></td>
                              <td>10</td>
                              <td>2</td>
                              <td>0</td>
                              <td><small class="text-muted">11:00 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-3.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Mike Davis</h6>
                                    <small class="text-muted">Advanced</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">272</span></td>
                              <td>9</td>
                              <td>3</td>
                              <td>0</td>
                              <td><small class="text-muted">11:15 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-2.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">Elite Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">258</span></td>
                              <td>8</td>
                              <td>4</td>
                              <td>0</td>
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
                              <th scope="col">Player</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-3.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Mike Davis</h6>
                                    <small class="text-muted">Advanced</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">278</span></td>
                              <td>10</td>
                              <td>2</td>
                              <td>0</td>
                              <td><small class="text-muted">11:45 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">John Smith</h6>
                                    <small class="text-muted">Pro Bowler</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">265</span></td>
                              <td>9</td>
                              <td>3</td>
                              <td>0</td>
                              <td><small class="text-muted">12:00 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-5.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Tom Wilson</h6>
                                    <small class="text-muted">Beginner</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">252</span></td>
                              <td>8</td>
                              <td>4</td>
                              <td>0</td>
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
                              <th scope="col">Player</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-2.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                    <small class="text-muted">Elite Player</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">282</span></td>
                              <td>10</td>
                              <td>2</td>
                              <td>0</td>
                              <td><small class="text-muted">12:30 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">John Smith</h6>
                                    <small class="text-muted">Pro Bowler</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">269</span></td>
                              <td>9</td>
                              <td>3</td>
                              <td>0</td>
                              <td><small class="text-muted">12:45 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-4.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                    <h6 class="mb-0">Lisa Chen</h6>
                                    <small class="text-muted">Intermediate</small>
                                  </div>
                                </div>
                              </td>
                              <td><span class="fw-bold text-success">256</span></td>
                              <td>8</td>
                              <td>4</td>
                              <td>0</td>
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
