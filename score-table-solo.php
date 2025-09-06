<?php
require_once 'includes/auth.php';
require_once 'database.php';

// Require login
requireLogin();

// Get current user
$currentUser = getCurrentUser();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Temporary Team Table - SPEEDSTERS Bowling System</title>
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
    .loading-spinner {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
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
                  <a href="./score-table-solo.php" class="sidebar-link active">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Team Temporary Table</span>
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
                    <a href="./my-profile.php" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="./logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
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
                    <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Table</a></li>
                    <li class="breadcrumb-item active">Team Temporary Table</li>
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
                      <h5 class="card-title fw-semibold mb-1" id="sessionTitle">Loading...</h5>
                      <span class="fw-normal text-muted">Team Temporary Table</span>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <?php 
                        // Get available session dates
                        try {
                          $pdo = getDBConnection();
                          
                          // Get session dates with score counts
                          $stmt = $pdo->prepare("
                            SELECT DISTINCT DATE(gs.session_date) as match_date,
                                   COUNT(DISTINCT gc.user_id) as player_count,
                                   COUNT(gc.score_id) as score_count
                            FROM game_sessions gs
                            INNER JOIN game_scores gc ON gs.session_id = gc.session_id
                            WHERE gc.status = 'Completed'
                            GROUP BY DATE(gs.session_date)
                            ORDER BY match_date DESC
                            LIMIT 20
                          ");
                          $stmt->execute();
                          $sessionDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          // Add today's date if it has scores
                          $today = date('Y-m-d');
                          $hasToday = false;
                          foreach ($sessionDates as $date) {
                            if ($date['match_date'] === $today) {
                              $hasToday = true;
                              break;
                            }
                          }
                          
                          // Add today as default if it has scores
                          if ($hasToday) {
                            echo '<option value="' . $today . '" selected>' . date('M j, Y', strtotime($today)) . '</option>';
                          } else {
                            // Select the most recent date
                            if (!empty($sessionDates)) {
                              $mostRecent = $sessionDates[0]['match_date'];
                              echo '<option value="' . $mostRecent . '" selected>' . date('M j, Y', strtotime($mostRecent)) . '</option>';
                            } else {
                              echo '<option value="today" selected>Today</option>';
                            }
                          }
                          
                          // Add other dates
                          foreach ($sessionDates as $date) {
                            if ($date['match_date'] !== $today) {
                              $formattedDate = date('M j, Y', strtotime($date['match_date']));
                              echo '<option value="' . $date['match_date'] . '">' . $formattedDate . '</option>';
                            }
                          }
                          
                          // Add All Time option
                          echo '<option value="all">All Time</option>';
                          
                        } catch (Exception $e) {
                          // Fallback options if database query fails
                          echo '<option value="today" selected>Today</option>';
                          echo '<option value="all">All Time</option>';
                        }
                        ?>
                      </select>
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
                      <button class="nav-link" id="overall-team-tab" data-bs-toggle="tab" data-bs-target="#overall-team" type="button" role="tab">
                        Overall Team
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
                              <th scope="col">Team</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Last Updated</th>
                            </tr>
                          </thead>
                          <tbody id="overallTableBody">
                            <tr>
                              <td colspan="10" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading data...</span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Overall Team Tab -->
                    <div class="tab-pane fade" id="overall-team" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Player</th>
                              <th scope="col">Players</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Player</th>
                              <th scope="col">Best Score</th>
                              <th scope="col">Total Strikes</th>
                              <th scope="col">Total Spares</th>
                            </tr>
                          </thead>
                          <tbody id="overallTeamTableBody">
                            <tr>
                              <td colspan="10" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading team data...</span>
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
                              <th scope="col">Player</th>
                              <th scope="col">Team</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game1TableBody">
                            <tr>
                              <td colspan="8" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading Game 1 data...</span>
                              </td>
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
                              <th scope="col">Team</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game2TableBody">
                            <tr>
                              <td colspan="8" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading Game 2 data...</span>
                              </td>
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
                              <th scope="col">Team</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game3TableBody">
                            <tr>
                              <td colspan="8" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading Game 3 data...</span>
                              </td>
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
                              <th scope="col">Team</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game4TableBody">
                            <tr>
                              <td colspan="8" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading Game 4 data...</span>
                              </td>
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
                              <th scope="col">Team</th>
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game5TableBody">
                            <tr>
                              <td colspan="8" class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <span class="ms-2">Loading Game 5 data...</span>
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
    let currentData = null;
    let currentDateFilter = 'today';

    // Load data from backend
    async function loadData(dateFilter = 'today') {
      try {
        currentDateFilter = dateFilter;
        
        const formData = new FormData();
        formData.append('action', 'get_players_data');
        formData.append('selected_date', dateFilter);
        
        const response = await fetch('ajax/player-score-data.php', {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          currentData = data.players;
          updateAllTables();
          
          // Update the session title
          const sessionTitle = document.getElementById('sessionTitle');
          if (sessionTitle && data.session_name) {
            sessionTitle.textContent = data.session_name;
          }
          
          showNotification('Data loaded successfully!', 'success');
        } else {
          showNotification('Error loading data: ' + data.message, 'error');
        }
      } catch (error) {
        console.error('Error loading data:', error);
        showNotification('Error loading data: ' + error.message, 'error');
      }
    }

    // Update overall rankings table
    function updateOverallTable() {
      const tbody = document.getElementById('overallTableBody');
      if (!currentData || currentData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No data available</td></tr>';
        return;
      }

      let html = '';
      currentData.forEach((player, index) => {
        const rank = index + 1;
        const rankClass = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-other';
        const teamName = player.team_name || 'No Team';
        const totalScore = player.total_score || 0;
        const avgScore = player.avg_score || 0;
        const gamesPlayed = player.games_played || 0;
        const bestScore = player.best_score || 0;
        const totalStrikes = player.total_strikes || 0;
        const totalSpares = player.total_spares || 0;
        const lastUpdated = player.last_updated || 'Never';

        html += `
          <tr>
            <td><span class="rank-badge ${rankClass}">${rank}</span></td>
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(index % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                  <small class="text-muted">${player.user_role}</small>
                </div>
              </div>
            </td>
            <td><span class="badge bg-info">${teamName}</span></td>
            <td><span class="fw-bold text-success">${totalScore}</span></td>
            <td>${avgScore}</td>
            <td>${gamesPlayed}</td>
            <td><span class="text-warning">${bestScore}</span></td>
            <td>${totalStrikes}</td>
            <td>${totalSpares}</td>
            <td><small class="text-muted">${lastUpdated}</small></td>
          </tr>
        `;
      });

      tbody.innerHTML = html;
    }

    // Update individual game tables
    function updateGameTable(gameNumber) {
      const tbody = document.getElementById(`game${gameNumber}TableBody`);
      if (!currentData || currentData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">No data available for Game ${gameNumber}</td></tr>`;
        return;
      }

      // Filter players who have scores for this game
      const gamePlayers = currentData.filter(player => {
        const gameScore = player[`game_${gameNumber}_score`];
        return gameScore && gameScore.player_score > 0;
      });

      if (gamePlayers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">No scores available for Game ${gameNumber}</td></tr>`;
        return;
      }

      // Sort by score for this game
      gamePlayers.sort((a, b) => {
        const scoreA = a[`game_${gameNumber}_score`].player_score;
        const scoreB = b[`game_${gameNumber}_score`].player_score;
        return scoreB - scoreA;
      });

      let html = '';
      gamePlayers.forEach((player, index) => {
        const rank = index + 1;
        const rankClass = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-other';
        const teamName = player.team_name || 'No Team';
        const gameScore = player[`game_${gameNumber}_score`];
        const score = gameScore.player_score;
        const strikes = gameScore.strikes || 0;
        const spares = gameScore.spares || 0;
        const openFrames = gameScore.open_frames || 0;
        const time = gameScore.created_at ? new Date(gameScore.created_at).toLocaleTimeString() : 'N/A';

        html += `
          <tr>
            <td><span class="rank-badge ${rankClass}">${rank}</span></td>
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(index % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                  <small class="text-muted">${player.user_role}</small>
                </div>
              </div>
            </td>
            <td><span class="badge bg-info">${teamName}</span></td>
            <td><span class="fw-bold text-success">${score}</span></td>
            <td>${strikes}</td>
            <td>${spares}</td>
            <td>${openFrames}</td>
            <td><small class="text-muted">${time}</small></td>
          </tr>
        `;
      });

      tbody.innerHTML = html;
    }

    // Update overall team rankings table
    function updateOverallTeamTable() {
      const tbody = document.getElementById('overallTeamTableBody');
      if (!currentData || currentData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No team data available</td></tr>';
        return;
      }

      // Group players by team
      const teamStats = {};
      currentData.forEach(player => {
        const teamName = player.team_name || 'No Team';
        
        if (!teamStats[teamName]) {
          teamStats[teamName] = {
            teamName: teamName,
            totalScore: 0,
            totalGames: 0,
            players: [],
            totalStrikes: 0,
            totalSpares: 0,
            bestPlayer: null,
            bestScore: 0
          };
        }
        
        teamStats[teamName].totalScore += player.total_score || 0;
        teamStats[teamName].totalGames += player.games_played || 0;
        teamStats[teamName].totalStrikes += player.total_strikes || 0;
        teamStats[teamName].totalSpares += player.total_spares || 0;
        teamStats[teamName].players.push(player);
        
        // Track best player
        if ((player.best_score || 0) > teamStats[teamName].bestScore) {
          teamStats[teamName].bestScore = player.best_score || 0;
          teamStats[teamName].bestPlayer = player.first_name + ' ' + player.last_name;
        }
      });

      // Convert to array and sort by total score
      const teamArray = Object.values(teamStats).sort((a, b) => b.totalScore - a.totalScore);

      let html = '';
      teamArray.forEach((team, index) => {
        const rank = index + 1;
        const rankClass = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-other';
        const avgPerPlayer = team.players.length > 0 ? (team.totalScore / team.players.length).toFixed(1) : 0;

        html += `
          <tr>
            <td><span class="rank-badge ${rankClass}">${rank}</span></td>
            <td><span class="badge bg-primary fs-6">${team.teamName}</span></td>
            <td><span class="fw-bold text-success fs-5">${team.totalScore}</span></td>
            <td><span class="fw-bold text-info">${avgPerPlayer}</span></td>
            <td><span class="badge bg-secondary">${team.players.length}</span></td>
            <td>${team.totalGames}</td>
            <td><span class="text-primary">${team.bestPlayer || 'N/A'}</span></td>
            <td><span class="text-warning fw-bold">${team.bestScore}</span></td>
            <td><span class="text-danger">${team.totalStrikes}</span></td>
            <td><span class="text-info">${team.totalSpares}</span></td>
          </tr>
        `;
      });

      tbody.innerHTML = html;
    }

    // Update all tables
    function updateAllTables() {
      updateOverallTable();
      updateOverallTeamTable();
      updateGameTable(1);
      updateGameTable(2);
      updateGameTable(3);
      updateGameTable(4);
      updateGameTable(5);
    }

    // Date filter functionality
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
      loadData(selectedDate);
    });

    // Refresh table functionality
    function refreshTable() {
      const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
      const icon = refreshBtn.querySelector('i');
      
      // Add spinning animation
      icon.classList.add('ti-spin');
      
      // Reload data
      loadData(currentDateFilter).finally(() => {
        icon.classList.remove('ti-spin');
      });
    }

    // Tab switching with data loading
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        console.log('Switched to tab:', targetId);
        
        // Update the specific table if needed
        if (targetId.startsWith('#game')) {
          const gameNumber = targetId.replace('#game', '');
          updateGameTable(gameNumber);
        } else if (targetId === '#overall-team') {
          updateOverallTeamTable();
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

    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      loadData(selectedDate);
    });

    // Auto-refresh every 30 seconds
    setInterval(() => {
      if (!document.hidden) {
        loadData(currentDateFilter);
      }
    }, 30000);
  </script>
</body>

</html>