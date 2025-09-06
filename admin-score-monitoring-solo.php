<?php
require_once 'includes/auth.php';
require_once 'includes/session-management.php';
requireAdmin(); // Ensure only admins can access this page

// Get current user info
$currentUser = getCurrentUser();

// Get session info from URL parameter or active session
$sessionId = $_GET['session'] ?? null;
$currentSession = null;
$sessionScores = [];

if ($sessionId) {
    $currentSession = getSessionById($sessionId);
    if ($currentSession) {
        // No need for session participants - all players can join any session
        $sessionScores = getSessionScores($sessionId);
    }
} else {
    // If no session in URL, get the active session
    $activeSession = getActiveSession();
    if ($activeSession) {
        $sessionId = $activeSession['session_id'];
        $currentSession = $activeSession;
        // No need for session participants - all players can join any session
        $sessionScores = getSessionScores($sessionId);
    }
}

// Get all players for participant selection (include both Player and Admin roles)
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT user_id, username, first_name, last_name, user_role, team_name FROM users WHERE (user_role = 'Player' OR user_role = 'Admin') AND status = 'Active' ORDER BY first_name, last_name");
$stmt->execute();
$allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Solo Players Score Monitoring - SPEEDSTERS Bowling System</title>
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
                  <a href="./admin-score-monitoring-solo.php" class="sidebar-link active">
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
                    <li class="breadcrumb-item active">Solo Players</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Management Section -->
          <?php 
          // Get today's active session or create a default display
          try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
              SELECT 
                  session_id,
                  session_name,
                  session_date,
                  session_time,
                  game_mode,
                  status,
                  started_at,
                  ended_at,
                  created_at
              FROM game_sessions 
              WHERE DATE(session_date) = CURDATE() AND status = 'Active'
              ORDER BY started_at DESC
              LIMIT 1
            ");
            $stmt->execute();
            $todaySession = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($todaySession): 
              // Get session participants count
              $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM session_participants WHERE session_id = ?");
              $stmt->execute([$todaySession['session_id']]);
              $participantCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
              
              // Get session scores count
              $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM game_scores WHERE session_id = ? AND DATE(created_at) = CURDATE()");
              $stmt->execute([$todaySession['session_id']]);
              $scoreCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
              
              // Get count of players who actually played (have scores)
              $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as count FROM game_scores WHERE session_id = ? AND DATE(created_at) = CURDATE()");
              $stmt->execute([$todaySession['session_id']]);
              $playersWithScores = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
          ?>
            <div class="row mb-4">
              <div class="col-12">
                <div class="alert alert-success d-flex align-items-center">
                  <i class="ti ti-play-circle me-2 fs-4"></i>
                  <div class="flex-grow-1">
                    <strong>Today's Active Event:</strong> <?php echo htmlspecialchars($todaySession['session_name']); ?> - <?php echo ucfirst($todaySession['game_mode']); ?>
                    <br>
                    <small>
                      📅 <?php echo date('l, M j, Y', strtotime($todaySession['session_date'])); ?> 
                      ⏰ <?php echo date('g:i A', strtotime($todaySession['session_time'])); ?> 
                      🎳 <?php echo ucfirst($todaySession['game_mode']); ?> 
                      👥 <?php echo $participantCount; ?> players registered
                      🎯 <?php echo $scoreCount; ?> scores entered today
                      🏆 <?php echo $playersWithScores; ?> players played today
                    </small>
                  </div>
                  <div class="ms-3">
                    <button class="btn btn-primary btn-sm" onclick="refreshScores()">
                      <i class="ti ti-refresh"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="row mb-4">
              <div class="col-12">
                <div class="alert alert-info">
                  <i class="ti ti-info-circle me-2"></i>
                  <strong>No Active Session Today</strong> - You can still enter scores for individual players. All players are listed below for score entry.
                  <a href="admin-dashboard.php" class="btn btn-primary btn-sm ms-2">Create Session</a>
                </div>
              </div>
            </div>
          <?php endif; ?>
          <?php } catch (Exception $e) { ?>
            <div class="row mb-4">
              <div class="col-12">
                <div class="alert alert-warning">
                  <i class="ti ti-alert-triangle me-2"></i>
                  <strong>Session Error</strong> - Unable to load session information: <?php echo htmlspecialchars($e->getMessage()); ?>
                </div>
              </div>
            </div>
          <?php } ?>

          <!-- Admin Statistics Overview -->
          <?php 
          try {
            $pdo = getDBConnection();
            
            // Get total solo players
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE user_role = 'Player' AND status = 'Active'");
            $stmt->execute();
            $totalPlayers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get players who played today
            $stmt = $pdo->prepare("
              SELECT COUNT(DISTINCT user_id) as count 
              FROM game_scores 
              WHERE DATE(created_at) = CURDATE() AND status = 'Completed'
            ");
            $stmt->execute();
            $activeToday = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get average score today
            $stmt = $pdo->prepare("
              SELECT AVG(player_score) as avg_score 
              FROM game_scores 
              WHERE DATE(created_at) = CURDATE() AND status = 'Completed'
            ");
            $stmt->execute();
            $avgScoreToday = $stmt->fetch(PDO::FETCH_ASSOC)['avg_score'];
            $avgScoreToday = $avgScoreToday ? round($avgScoreToday, 1) : 0;
            
            // Get total games played today
            $stmt = $pdo->prepare("
              SELECT COUNT(*) as count 
              FROM game_scores 
              WHERE DATE(created_at) = CURDATE() AND status = 'Completed'
            ");
            $stmt->execute();
            $gamesToday = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get yesterday's stats for comparison
            $stmt = $pdo->prepare("
              SELECT COUNT(DISTINCT user_id) as active_yesterday,
                     COUNT(*) as games_yesterday,
                     AVG(player_score) as avg_yesterday
              FROM game_scores 
              WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND status = 'Completed'
            ");
            $stmt->execute();
            $yesterdayStats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $activeYesterday = $yesterdayStats['active_yesterday'] ?: 0;
            $gamesYesterday = $yesterdayStats['games_yesterday'] ?: 0;
            $avgYesterday = $yesterdayStats['avg_yesterday'] ? round($yesterdayStats['avg_yesterday'], 1) : 0;
            
            // Calculate changes
            $activeChange = $activeYesterday > 0 ? round((($activeToday - $activeYesterday) / $activeYesterday) * 100, 1) : 0;
            $gamesChange = $gamesYesterday > 0 ? round((($gamesToday - $gamesYesterday) / $gamesYesterday) * 100, 1) : 0;
            $avgChange = $avgYesterday > 0 ? round($avgScoreToday - $avgYesterday, 1) : 0;
          ?>
          <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Total Solo Players</h6>
                      <h3 class="mb-0 text-primary"><?php echo $totalPlayers; ?></h3>
                      <small class="text-muted">All active players</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-user fs-1 text-muted"></i>
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
                      <h6 class="card-title text-muted mb-1">Players Played Today</h6>
                      <h3 class="mb-0 text-success"><?php echo $playersWithScores; ?></h3>
                      <small class="text-muted"><?php echo $activeChange >= 0 ? '+' : ''; ?><?php echo $activeChange; ?>% vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-trophy fs-1 text-success"></i>
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
                      <h6 class="card-title text-muted mb-1">Avg Score Today</h6>
                      <h3 class="mb-0 text-warning"><?php echo $avgScoreToday; ?></h3>
                      <small class="text-muted"><?php echo $avgChange >= 0 ? '+' : ''; ?><?php echo $avgChange; ?> vs yesterday</small>
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
                      <h3 class="mb-0 text-info"><?php echo $gamesToday; ?></h3>
                      <small class="text-muted"><?php echo $gamesChange >= 0 ? '+' : ''; ?><?php echo $gamesChange; ?>% vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-bowling fs-1 text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } catch (Exception $e) { ?>
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-warning">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>Statistics Error</strong> - Unable to load statistics: <?php echo htmlspecialchars($e->getMessage()); ?>
              </div>
            </div>
          </div>
          <?php } ?>

          <!-- Page Content -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Solo Players Score Monitoring</h5>
                      <span class="fw-normal text-muted">Admin view with enhanced management features</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="ti ti-file-excel me-1"></i>
                        Export to CSV
                      </button>
                      <button class="btn btn-warning btn-sm" onclick="bulkEdit()">
                        <i class="ti ti-edit me-1"></i>
                        Bulk Edit
                      </button>
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <?php 
                        // Get session dates only (since we're now working with sessions)
                        try {
                          $pdo = getDBConnection();
                          
                          // Get session dates with score counts
                          $stmt = $pdo->prepare("
                            SELECT 
                              DATE(gs.session_date) as match_date,
                              gs.session_id,
                              gs.session_name,
                              COUNT(gsc.score_id) as score_count
                            FROM game_sessions gs
                            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND gsc.status = 'Completed'
                            WHERE gs.status = 'Active' OR gs.status = 'Completed'
                            GROUP BY gs.session_id, DATE(gs.session_date), gs.session_name
                            ORDER BY gs.session_date DESC
                          ");
                          $stmt->execute();
                          $sessionDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          // Debug: Log the dates found
                          error_log("Found " . count($sessionDates) . " session dates: " . json_encode($sessionDates));
                          
                          // Add dates to dropdown
                          $first = true;
                          foreach ($sessionDates as $session) {
                            $formattedDate = date('M j, Y', strtotime($session['match_date']));
                            $selected = $first ? ' selected' : '';
                            $scoreInfo = $session['score_count'] > 0 ? " ({$session['score_count']} scores)" : " (no scores)";
                            echo '<option value="' . $session['match_date'] . '"' . $selected . '>' . $formattedDate . $scoreInfo . '</option>';
                            $first = false;
                          }
                          
                          // Add "All Time" option
                          echo '<option value="all">All Time</option>';
                        } catch (Exception $e) {
                          // Fallback options if database error
                          echo '<option value="' . date('Y-m-d') . '">' . date('M j, Y') . '</option>';
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
                        <table class="table table-hover" id="overallRankingsTable">
                          <thead>
                            <tr>
                              <th scope="col">Player</th>
                              <th scope="col">Team Name</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Status</th>
                              <th scope="col">Last Updated</th>
                              <th scope="col">Admin Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                            // Get all players from the database
                            try {
                              $pdo = getDBConnection();
                              $stmt = $pdo->prepare("
                                SELECT 
                                    u.user_id,
                                    u.username,
                                    u.first_name,
                                    u.last_name,
                                    u.email,
                                    u.phone,
                                    u.user_role,
                                    u.status,
                                    u.team_name,
                                    u.created_at
                                FROM users u 
                                WHERE u.user_role = 'Player' AND u.status = 'Active'
                                ORDER BY u.first_name, u.last_name
                              ");
                              $stmt->execute();
                              $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                              
                              if (!empty($allPlayers)): 
                                // Calculate rankings and stats for each player
                                $playerStats = [];
                                // Get all today's scores in one query
                                $stmt = $pdo->prepare("
                                  SELECT 
                                      user_id,
                                      player_score,
                                      strikes,
                                      spares,
                                      created_at
                                  FROM game_scores 
                                  WHERE status = 'Completed' AND DATE(created_at) = CURDATE()
                                  ORDER BY user_id, created_at DESC
                                ");
                                $stmt->execute();
                                $allTodayScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                // Group scores by user_id
                                $scoresByUser = [];
                                foreach ($allTodayScores as $score) {
                                  $userId = $score['user_id'];
                                  if (!isset($scoresByUser[$userId])) {
                                    $scoresByUser[$userId] = [];
                                  }
                                  $scoresByUser[$userId][] = $score;
                                }
                                
                                foreach ($allPlayers as $player) {
                                  $playerGames = $scoresByUser[$player['user_id']] ?? [];
                                  
                                  $playerScores = array_column($playerGames, 'player_score');
                                  $totalScore = array_sum($playerScores);
                                  $avgScore = !empty($playerScores) ? round($totalScore / count($playerScores), 1) : 0;
                                  $bestScore = !empty($playerScores) ? max($playerScores) : 0;
                                  $totalStrikes = array_sum(array_column($playerGames, 'strikes'));
                                  $totalSpares = array_sum(array_column($playerGames, 'spares'));
                                  
                                  $playerStats[] = [
                                    'player' => $player,
                                    'total_score' => $totalScore,
                                    'avg_score' => $avgScore,
                                    'best_score' => $bestScore,
                                    'games_played' => count($playerGames),
                                    'total_strikes' => $totalStrikes,
                                    'total_spares' => $totalSpares,
                                    'last_updated' => !empty($playerGames) ? $playerGames[0]['created_at'] : null
                                  ];
                                }
                                
                                // Sort by total score descending
                                usort($playerStats, function($a, $b) {
                                  return $b['total_score'] <=> $a['total_score'];
                                });
                                
                                foreach ($playerStats as $stats):
                                  $player = $stats['player'];
                                ?>
                                  <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                        <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                          <h6 class="mb-0"><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></h6>
                                  </div>
                                </div>
                              </td>
                                    <td><span class="badge bg-info"><?php echo $player['team_name'] ?: 'No Team'; ?></span></td>
                                    <td><span class="fw-bold text-success"><?php echo $stats['total_score']; ?></span></td>
                                    <td><span class="fw-bold text-primary"><?php echo $stats['avg_score']; ?></span></td>
                                    <td><?php echo $stats['games_played']; ?></td>
                                    <td><span class="badge bg-info"><?php echo $stats['best_score'] > 0 ? $stats['best_score'] : '-'; ?></span></td>
                                    <td><?php echo $stats['total_strikes']; ?></td>
                                    <td><?php echo $stats['total_spares']; ?></td>
                              <td><span class="badge bg-success">Active</span></td>
                                    <td><small class="text-muted"><?php echo $stats['last_updated'] ? date('M j, g:i A', strtotime($stats['last_updated'])) : 'Never'; ?></small></td>
                              <td>
                                <div class="admin-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewPlayerDetails(<?php echo $player['user_id']; ?>)" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="editPlayerScore(<?php echo $player['user_id']; ?>)" title="Edit Score">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewPlayerHistory(<?php echo $player['user_id']; ?>)" title="View History">
                                    <i class="ti ti-history"></i>
                                  </button>
                                </div>
                              </td>
                                  </tr>
                                <?php 
                                endforeach; ?>
                              <?php else: ?>
                                <tr>
                                  <td colspan="11" class="text-center text-muted py-4">
                                    <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                    No players found in the database.
                                  </td>
                                </tr>
                              <?php endif; ?>
                            <?php } catch (Exception $e) { ?>
                              <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                  <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                  Error loading players: <?php echo htmlspecialchars($e->getMessage()); ?>
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                        
                                  </div>
                                </div>

                    <!-- Game 1 Tab -->
                    <div class="tab-pane fade" id="game1" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Game 1 Score Entry</h5>
                            <button class="btn btn-success btn-sm" onclick="saveAllScores(1)">
                              <i class="ti ti-device-floppy me-1"></i>Save All Scores
                                  </button>
                                </div>
                        </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table table-bordered" id="game1Table">
                              <thead class="table-dark">
                                <tr>
                                  <th scope="col" style="width: 25%;">Player</th>
                                  <th scope="col" style="width: 8%;">Team Name</th>
                                  <th scope="col" style="width: 10%;">Score</th>
                                  <th scope="col" style="width: 10%;">Strikes</th>
                                  <th scope="col" style="width: 10%;">Spares</th>
                                  <th scope="col" style="width: 10%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  $stmt = $pdo->prepare("
                                    SELECT 
                                        u.user_id,
                                        u.username,
                                        u.first_name,
                                        u.last_name,
                                        u.email,
                                        u.phone,
                                        u.user_role,
                                        u.status,
                                        u.team_name,
                                        u.created_at
                                    FROM users u 
                                    WHERE u.user_role = 'Player' AND u.status = 'Active'
                                    ORDER BY u.first_name, u.last_name
                                  ");
                                  $stmt->execute();
                                  $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                  
                                  if (!empty($allPlayers)): 
                                    // Get all Game 1 scores for today in one query
                                    $stmt = $pdo->prepare("
                                      SELECT 
                                          user_id,
                                          player_score,
                                          strikes,
                                          spares,
                                          open_frames,
                                          created_at
                                      FROM game_scores 
                                      WHERE game_number = 1 AND status = 'Completed' AND DATE(created_at) = CURDATE()
                                      ORDER BY user_id, created_at DESC
                                    ");
                                    $stmt->execute();
                                    $allGame1Scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    // Group by user_id (keep most recent)
                                    $game1ScoresByUser = [];
                                    foreach ($allGame1Scores as $score) {
                                      $userId = $score['user_id'];
                                      if (!isset($game1ScoresByUser[$userId])) {
                                        $game1ScoresByUser[$userId] = $score;
                                      }
                                    }
                                    
                                    foreach ($allPlayers as $player):
                                      $score = $game1ScoresByUser[$player['user_id']] ?? null;
                                ?>
                                    <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                          <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                            <strong><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></strong>
                                  </div>
                                </div>
                              </td>
                                      <td class="text-center">
                                        <span class="badge bg-info"><?php echo $player['team_name'] ?: 'No Team'; ?></span>
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="score" 
                                               data-game="1"
                                               value="<?php echo $score ? $score['player_score'] : ''; ?>" 
                                               min="0" 
                                               max="300" 
                                               placeholder="0-300">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="strikes" 
                                               data-game="1"
                                               value="<?php echo $score ? $score['strikes'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="spares" 
                                               data-game="1"
                                               value="<?php echo $score ? $score['spares'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="open_frames" 
                                               data-game="1"
                                               value="<?php echo $score ? $score['open_frames'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td class="text-center">
                                        <?php if ($score): ?>
                                          <span class="badge bg-success">Completed</span>
                                          <br><small class="text-muted"><?php echo date('g:i A', strtotime($score['created_at'])); ?></small>
                                        <?php else: ?>
                                          <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                      </td>
                                      <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="savePlayerScore(<?php echo $player['user_id']; ?>, 1, '<?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?>')" title="Save Score">
                                          <i class="ti ti-device-floppy me-1"></i>Save
                                  </button>
                              </td>
                            </tr>
                                  <?php 
                                  endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                      No players found in the database.
                                    </td>
                                  </tr>
                                <?php endif; ?>
                                <?php } catch (Exception $e) { ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                      Error loading players: <?php echo htmlspecialchars($e->getMessage()); ?>
                                    </td>
                                  </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                                  </div>
                                </div>
                      </div>
                    </div>

                    <!-- Game 2 Tab -->
                    <div class="tab-pane fade" id="game2" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Game 2 Score Entry</h5>
                            <button class="btn btn-success btn-sm" onclick="saveAllScores(2)">
                              <i class="ti ti-device-floppy me-1"></i>Save All Scores
                                  </button>
                                </div>
                        </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table table-bordered" id="game2Table">
                              <thead class="table-dark">
                                <tr>
                                  <th scope="col" style="width: 25%;">Player</th>
                                  <th scope="col" style="width: 8%;">Team Name</th>
                                  <th scope="col" style="width: 10%;">Score</th>
                                  <th scope="col" style="width: 10%;">Strikes</th>
                                  <th scope="col" style="width: 10%;">Spares</th>
                                  <th scope="col" style="width: 10%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  $stmt = $pdo->prepare("
                                    SELECT 
                                        u.user_id,
                                        u.username,
                                        u.first_name,
                                        u.last_name,
                                        u.email,
                                        u.phone,
                                        u.user_role,
                                        u.status,
                                        u.team_name,
                                        u.created_at
                                    FROM users u 
                                    WHERE u.user_role = 'Player' AND u.status = 'Active'
                                    ORDER BY u.first_name, u.last_name
                                  ");
                                  $stmt->execute();
                                  $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 2 score for this player (today only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 2 AND status = 'Completed' AND DATE(created_at) = CURDATE()
                                        ORDER BY created_at DESC
                                        LIMIT 1
                                      ");
                                      $stmt->execute([$player['user_id']]);
                                      $score = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                    <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                          <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                            <strong><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></strong>
                                  </div>
                                </div>
                              </td>
                                      <td class="text-center">
                                        <span class="badge bg-info"><?php echo $player['team_name'] ?: 'No Team'; ?></span>
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="score" 
                                               data-game="2"
                                               value="<?php echo $score ? $score['player_score'] : ''; ?>" 
                                               min="0" 
                                               max="300" 
                                               placeholder="0-300">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="strikes" 
                                               data-game="2"
                                               value="<?php echo $score ? $score['strikes'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="spares" 
                                               data-game="2"
                                               value="<?php echo $score ? $score['spares'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="open_frames" 
                                               data-game="2"
                                               value="<?php echo $score ? $score['open_frames'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td class="text-center">
                                        <?php if ($score): ?>
                                          <span class="badge bg-success">Completed</span>
                                          <br><small class="text-muted"><?php echo date('g:i A', strtotime($score['created_at'])); ?></small>
                                        <?php else: ?>
                                          <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                      </td>
                                      <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="savePlayerScore(<?php echo $player['user_id']; ?>, 2, '<?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?>')" title="Save Score">
                                          <i class="ti ti-device-floppy me-1"></i>Save
                                  </button>
                              </td>
                            </tr>
                                  <?php 
                                  endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                      No players found in the database.
                                    </td>
                                  </tr>
                                <?php endif; ?>
                                <?php } catch (Exception $e) { ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                      Error loading players: <?php echo htmlspecialchars($e->getMessage()); ?>
                                    </td>
                                  </tr>
                                <?php } ?>
                          </tbody>
                        </table>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Game 3 Tab -->
                    <div class="tab-pane fade" id="game3" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Game 3 Score Entry</h5>
                            <button class="btn btn-success btn-sm" onclick="saveAllScores(3)">
                              <i class="ti ti-device-floppy me-1"></i>Save All Scores
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                      <div class="table-responsive">
                            <table class="table table-bordered" id="game3Table">
                              <thead class="table-dark">
                                <tr>
                                  <th scope="col" style="width: 25%;">Player</th>
                                  <th scope="col" style="width: 8%;">Team Name</th>
                                  <th scope="col" style="width: 10%;">Score</th>
                                  <th scope="col" style="width: 10%;">Strikes</th>
                                  <th scope="col" style="width: 10%;">Spares</th>
                                  <th scope="col" style="width: 10%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  $stmt = $pdo->prepare("
                                    SELECT 
                                        u.user_id,
                                        u.username,
                                        u.first_name,
                                        u.last_name,
                                        u.email,
                                        u.phone,
                                        u.user_role,
                                        u.status,
                                        u.team_name,
                                        u.created_at
                                    FROM users u 
                                    WHERE u.user_role = 'Player' AND u.status = 'Active'
                                    ORDER BY u.first_name, u.last_name
                                  ");
                                  $stmt->execute();
                                  $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 3 score for this player (today only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 3 AND status = 'Completed' AND DATE(created_at) = CURDATE()
                                        ORDER BY created_at DESC
                                        LIMIT 1
                                      ");
                                      $stmt->execute([$player['user_id']]);
                                      $score = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                    <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                          <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                            <strong><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></strong>
                                  </div>
                                </div>
                              </td>
                                      <td class="text-center">
                                        <span class="badge bg-info"><?php echo $player['team_name'] ?: 'No Team'; ?></span>
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="score" 
                                               data-game="3"
                                               value="<?php echo $score ? $score['player_score'] : ''; ?>" 
                                               min="0" 
                                               max="300" 
                                               placeholder="0-300">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="strikes" 
                                               data-game="3"
                                               value="<?php echo $score ? $score['strikes'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="spares" 
                                               data-game="3"
                                               value="<?php echo $score ? $score['spares'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="open_frames" 
                                               data-game="3"
                                               value="<?php echo $score ? $score['open_frames'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td class="text-center">
                                        <?php if ($score): ?>
                                          <span class="badge bg-success">Completed</span>
                                          <br><small class="text-muted"><?php echo date('g:i A', strtotime($score['created_at'])); ?></small>
                                        <?php else: ?>
                                          <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                      </td>
                                      <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="savePlayerScore(<?php echo $player['user_id']; ?>, 3, '<?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?>')" title="Save Score">
                                          <i class="ti ti-device-floppy me-1"></i>Save
                                  </button>
                              </td>
                            </tr>
                                  <?php 
                                  endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                      No players found in the database.
                                    </td>
                                  </tr>
                                <?php endif; ?>
                                <?php } catch (Exception $e) { ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                      Error loading players: <?php echo htmlspecialchars($e->getMessage()); ?>
                                    </td>
                                  </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                                  </div>
                                </div>
                      </div>
                    </div>

                    <!-- Game 4 Tab -->
                    <div class="tab-pane fade" id="game4" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Game 4 Score Entry</h5>
                            <button class="btn btn-success btn-sm" onclick="saveAllScores(4)">
                              <i class="ti ti-device-floppy me-1"></i>Save All Scores
                                  </button>
                                </div>
                        </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table table-bordered" id="game4Table">
                              <thead class="table-dark">
                                <tr>
                                  <th scope="col" style="width: 25%;">Player</th>
                                  <th scope="col" style="width: 8%;">Team Name</th>
                                  <th scope="col" style="width: 10%;">Score</th>
                                  <th scope="col" style="width: 10%;">Strikes</th>
                                  <th scope="col" style="width: 10%;">Spares</th>
                                  <th scope="col" style="width: 10%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  $stmt = $pdo->prepare("
                                    SELECT 
                                        u.user_id,
                                        u.username,
                                        u.first_name,
                                        u.last_name,
                                        u.email,
                                        u.phone,
                                        u.user_role,
                                        u.status,
                                        u.team_name,
                                        u.created_at
                                    FROM users u 
                                    WHERE u.user_role = 'Player' AND u.status = 'Active'
                                    ORDER BY u.first_name, u.last_name
                                  ");
                                  $stmt->execute();
                                  $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 4 score for this player (today only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 4 AND status = 'Completed' AND DATE(created_at) = CURDATE()
                                        ORDER BY created_at DESC
                                        LIMIT 1
                                      ");
                                      $stmt->execute([$player['user_id']]);
                                      $score = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                    <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                          <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                            <strong><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></strong>
                                  </div>
                                </div>
                              </td>
                                      <td class="text-center">
                                        <span class="badge bg-info"><?php echo $player['team_name'] ?: 'No Team'; ?></span>
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="score" 
                                               data-game="4"
                                               value="<?php echo $score ? $score['player_score'] : ''; ?>" 
                                               min="0" 
                                               max="300" 
                                               placeholder="0-300">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="strikes" 
                                               data-game="4"
                                               value="<?php echo $score ? $score['strikes'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="spares" 
                                               data-game="4"
                                               value="<?php echo $score ? $score['spares'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="open_frames" 
                                               data-game="4"
                                               value="<?php echo $score ? $score['open_frames'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td class="text-center">
                                        <?php if ($score): ?>
                                          <span class="badge bg-success">Completed</span>
                                          <br><small class="text-muted"><?php echo date('g:i A', strtotime($score['created_at'])); ?></small>
                                        <?php else: ?>
                                          <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                      </td>
                                      <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="savePlayerScore(<?php echo $player['user_id']; ?>, 4, '<?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?>')" title="Save Score">
                                          <i class="ti ti-device-floppy me-1"></i>Save
                                  </button>
                              </td>
                            </tr>
                                  <?php 
                                  endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                      No players found in the database.
                                    </td>
                                  </tr>
                                <?php endif; ?>
                                <?php } catch (Exception $e) { ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                      Error loading players: <?php echo htmlspecialchars($e->getMessage()); ?>
                                    </td>
                                  </tr>
                                <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                      </div>
                    </div>

                    <!-- Game 5 Tab -->
                    <div class="tab-pane fade" id="game5" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Game 5 Score Entry</h5>
                            <button class="btn btn-success btn-sm" onclick="saveAllScores(5)">
                              <i class="ti ti-device-floppy me-1"></i>Save All Scores
                            </button>
                      </div>
                    </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table table-bordered" id="game5Table">
                              <thead class="table-dark">
                                <tr>
                                  <th scope="col" style="width: 25%;">Player</th>
                                  <th scope="col" style="width: 8%;">Team Name</th>
                                  <th scope="col" style="width: 10%;">Score</th>
                                  <th scope="col" style="width: 10%;">Strikes</th>
                                  <th scope="col" style="width: 10%;">Spares</th>
                                  <th scope="col" style="width: 10%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  $stmt = $pdo->prepare("
                                    SELECT 
                                        u.user_id,
                                        u.username,
                                        u.first_name,
                                        u.last_name,
                                        u.email,
                                        u.phone,
                                        u.user_role,
                                        u.status,
                                        u.team_name,
                                        u.created_at
                                    FROM users u 
                                    WHERE u.user_role = 'Player' AND u.status = 'Active'
                                    ORDER BY u.first_name, u.last_name
                                  ");
                                  $stmt->execute();
                                  $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 5 score for this player (today only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 5 AND status = 'Completed' AND DATE(created_at) = CURDATE()
                                        ORDER BY created_at DESC
                                        LIMIT 1
                                      ");
                                      $stmt->execute([$player['user_id']]);
                                      $score = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                    <tr>
                                      <td>
                                        <div class="d-flex align-items-center">
                                          <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                          <div>
                                            <strong><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></strong>
                      </div>
                    </div>
                                      </td>
                                      <td class="text-center">
                                        <span class="badge bg-info"><?php echo $player['team_name'] ?: 'No Team'; ?></span>
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="score" 
                                               data-game="5"
                                               value="<?php echo $score ? $score['player_score'] : ''; ?>" 
                                               min="0" 
                                               max="300" 
                                               placeholder="0-300">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="strikes" 
                                               data-game="5"
                                               value="<?php echo $score ? $score['strikes'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="spares" 
                                               data-game="5"
                                               value="<?php echo $score ? $score['spares'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-user-id="<?php echo $player['user_id']; ?>" 
                                               data-field="open_frames" 
                                               data-game="5"
                                               value="<?php echo $score ? $score['open_frames'] : ''; ?>" 
                                               min="0" 
                                               max="12" 
                                               placeholder="0-12">
                                      </td>
                                      <td class="text-center">
                                        <?php if ($score): ?>
                                          <span class="badge bg-success">Completed</span>
                                          <br><small class="text-muted"><?php echo date('g:i A', strtotime($score['created_at'])); ?></small>
                                        <?php else: ?>
                                          <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                      </td>
                                      <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="savePlayerScore(<?php echo $player['user_id']; ?>, 5, '<?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?>')" title="Save Score">
                                          <i class="ti ti-device-floppy me-1"></i>Save
                                        </button>
                                      </td>
                                    </tr>
                                  <?php 
                                  endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                      No players found in the database.
                                    </td>
                                  </tr>
                                <?php endif; ?>
                                <?php } catch (Exception $e) { ?>
                                  <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                      <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                      Error loading players: <?php echo htmlspecialchars($e->getMessage()); ?>
                                    </td>
                                  </tr>
                                <?php } ?>
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
    </div>
  </div>
  
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Admin-specific functions
    function viewPlayerDetails(playerId) {
      showNotification('Opening detailed view for player: ' + playerId, 'info');
      // Here you would open a detailed player modal or navigate to player details page
    }

    function editPlayerScore(playerId) {
      showNotification('Opening score editor for player: ' + playerId, 'info');
      // Here you would open the score editing interface
    }

    function viewPlayerHistory(playerId) {
      showNotification('Loading score history for player: ' + playerId, 'info');
      // Here you would load and display player's complete score history
    }

    function editGameScore(playerId, gameNumber) {
      showNotification('Editing Game ' + gameNumber + ' score for player: ' + playerId, 'warning');
      // Here you would open a quick edit modal for the specific game score
    }

    function deleteGameScore(playerId, gameNumber) {
      if (confirm('Are you sure you want to delete Game ' + gameNumber + ' score for player: ' + playerId + '?')) {
        showNotification('Score deleted successfully!', 'success');
        // Here you would make the actual deletion
      }
    }

    function exportData() {
      showNotification('Exporting solo players data...', 'info');
      // Here you would generate and download the data export
    }

    function bulkEdit() {
      showNotification('Opening bulk edit interface...', 'info');
      // Here you would open a bulk editing interface
    }

    // Date filter functionality
    // Date filter change handler
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
      console.log('Date filter changed to:', selectedDate);
      showNotification('Loading data for ' + selectedDate + '...', 'info');
      loadDataForDateFilter(selectedDate);
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
        showNotification('Admin table refreshed successfully!', 'success');
      }, 1000);
    }

    // Tab switching with data loading simulation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        console.log('Switched to admin tab:', targetId);
        
        // Simulate loading data for specific game
        if (targetId !== '#overall') {
          const gameNumber = targetId.replace('#game', '');
          showNotification('Loading Game ' + gameNumber + ' admin data...', 'info');
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
        console.log('Auto-refreshing admin table...');
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

    // Session Management Functions
    function savePlayerScore(userId, gameNumber, playerName) {
      console.log('savePlayerScore called:', {userId, gameNumber, playerName});
      
      const tableId = `game${gameNumber}Table`;
      const table = document.getElementById(tableId);
      const row = table.querySelector(`tr [data-user-id="${userId}"]`).closest('tr');
      const inputs = row.querySelectorAll('.score-input');
      
      console.log('Table ID:', tableId);
      console.log('Table found:', table);
      console.log('Row found:', row);
      console.log('Inputs found:', inputs.length);
      
      let scoreData = {
        user_id: userId,
        game_number: gameNumber,
        player_score: '',
        strikes: '',
        spares: '',
        open_frames: ''
      };
      
      let hasErrors = false;
      
      inputs.forEach(input => {
        const field = input.getAttribute('data-field');
        const value = input.value.trim();
        
        console.log(`Input field: ${field}, value: "${value}"`);
        
        if (field === 'score' && value && (value < 0 || value > 300)) {
          input.classList.add('is-invalid');
          hasErrors = true;
          return;
        } else {
          input.classList.remove('is-invalid');
        }
        
        // Map the field names correctly
        if (field === 'score') {
          scoreData.player_score = value;
        } else if (field === 'strikes') {
          scoreData.strikes = value;
        } else if (field === 'spares') {
          scoreData.spares = value;
        } else if (field === 'open_frames') {
          scoreData.open_frames = value;
        }
      });
      
      console.log('Score data:', scoreData);
      
      if (hasErrors) {
        showNotification('Please fix invalid score (0-300)', 'error');
        return;
      }
      
      if (!scoreData.player_score) {
        showNotification('Please enter a score for ' + playerName, 'warning');
        return;
      }
      
      // Show loading on the specific save button
      const saveBtn = row.querySelector(`[onclick*="savePlayerScore(${userId}, ${gameNumber}"]`);
      console.log('Save button found:', saveBtn);
      
      if (!saveBtn) {
        showNotification('Save button not found', 'error');
        return;
      }
      
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="ti ti-loader me-1"></i>Saving...';
      saveBtn.disabled = true;
      
      // Send single score
      const formData = new FormData();
      formData.append('action', 'add_score');
      // Use the session_id from the selected date, fallback to PHP session_id
      const sessionId = window.currentSessionId || <?php echo $sessionId ? $sessionId : 'null'; ?>;
      formData.append('session_id', sessionId);
      formData.append('user_id', userId);
      formData.append('game_number', gameNumber);
      formData.append('player_score', scoreData.player_score);
      formData.append('strikes', scoreData.strikes || 0);
      formData.append('spares', scoreData.spares || 0);
      formData.append('open_frames', scoreData.open_frames || 0);
      
      console.log('Sending data:', {
        action: 'add_score',
        session_id: sessionId,
        user_id: userId,
        game_number: gameNumber,
        player_score: scoreData.player_score,
        strikes: scoreData.strikes || 0,
        spares: scoreData.spares || 0,
        open_frames: scoreData.open_frames || 0
      });
      
      fetch('ajax/session-management.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.text();
      })
      .then(text => {
        console.log('Raw response:', text);
        try {
          const data = JSON.parse(text);
          console.log('Parsed data:', data);
          if (data.success) {
            showNotification(`Score saved for ${playerName}: ${scoreData.player_score}`, 'success');
            
            // Update the UI dynamically instead of refreshing the page
            updatePlayerStatus(row, scoreData.player_score, scoreData.strikes, scoreData.spares, scoreData.open_frames);
            
            // Auto-refresh the current tab data after a short delay
            setTimeout(() => {
              refreshCurrentTabData();
            }, 1000);
          } else {
            showNotification('Error: ' + data.message, 'error');
          }
        } catch (e) {
          console.error('JSON parse error:', e);
          showNotification('Server error: ' + text, 'error');
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        showNotification('An error occurred while saving score', 'error');
      })
      .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
      });
    }

    function updatePlayerStatus(row, score, strikes, spares, openFrames) {
      // Update the status column to show "Completed"
      const statusCell = row.querySelector('td:last-child').previousElementSibling;
      if (statusCell) {
        statusCell.innerHTML = `
          <span class="badge bg-success">Completed</span>
          <br><small class="text-muted">${new Date().toLocaleTimeString()}</small>
        `;
      }
      
      // Disable the save button since score is now saved
      const saveBtn = row.querySelector('button[onclick*="savePlayerScore"]');
      if (saveBtn) {
        saveBtn.innerHTML = '<i class="ti ti-check me-1"></i>Saved';
        saveBtn.disabled = true;
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-outline-success');
      }
      
      // Update the Overall Rankings tab if it's visible
      updateOverallRankings();
    }

    function updateOverallRankings() {
      // This function would update the Overall Rankings tab
      // For now, we'll just show a message that rankings need to be refreshed
      const overallTab = document.getElementById('overall-tab');
      if (overallTab && overallTab.classList.contains('active')) {
        // If we're on the overall tab, we could update it here
        // For now, we'll just add a small indicator
        console.log('Overall rankings tab is active - would update here');
      }
    }

    function refreshCurrentTabData() {
      // Get the currently selected date
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      
      console.log('Auto-refreshing data for date:', selectedDate);
      
      // Show a subtle loading indicator
      const activeTab = document.querySelector('.nav-link.active');
      if (activeTab) {
        const originalText = activeTab.innerHTML;
        activeTab.innerHTML = originalText + ' <i class="ti ti-loader ti-spin"></i>';
        
        // Remove loading indicator after refresh
        setTimeout(() => {
          activeTab.innerHTML = originalText;
        }, 2000);
      }
      
      // Show a subtle notification
      showNotification('Refreshing data...', 'info');
      
      // Clear cache to force fresh data
      delete dataCache[selectedDate];
      
      // Reload the data for the current date
      loadDataForDateFilter(selectedDate);
    }

    function saveAllScores(gameNumber) {
      const tableId = `game${gameNumber}Table`;
      const table = document.getElementById(tableId);
      const rows = table.querySelectorAll('tbody tr');
      
      let scoresToSave = [];
      let hasErrors = false;
      
      rows.forEach((row, index) => {
        const inputs = row.querySelectorAll('.score-input');
        const userId = inputs[0].getAttribute('data-user-id');
        
        let scoreData = {
          user_id: userId,
          game_number: gameNumber,
          player_score: '',
          strikes: '',
          spares: '',
          open_frames: ''
        };
        
        inputs.forEach(input => {
          const field = input.getAttribute('data-field');
          const value = input.value.trim();
          
          if (field === 'score' && value && (value < 0 || value > 300)) {
            input.classList.add('is-invalid');
            hasErrors = true;
            return;
          } else {
            input.classList.remove('is-invalid');
          }
          
          scoreData[field] = value;
        });
        
        // Only save if score is provided
        if (scoreData.player_score) {
          scoresToSave.push(scoreData);
        }
      });
      
      if (hasErrors) {
        showNotification('Please fix invalid scores (0-300)', 'error');
        return;
      }
      
      if (scoresToSave.length === 0) {
        showNotification('No scores to save', 'warning');
        return;
      }
      
      // Show loading
      const saveBtn = document.querySelector(`[onclick="saveAllScores(${gameNumber})"]`);
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="ti ti-loader me-1"></i>Saving...';
      saveBtn.disabled = true;
      
      // Send all scores
      const formData = new FormData();
      formData.append('action', 'save_multiple_scores');
      formData.append('session_id', <?php echo $sessionId ? $sessionId : 'null'; ?>);
      formData.append('scores', JSON.stringify(scoresToSave));
      
      fetch('ajax/session-management.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(`Saved ${scoresToSave.length} scores for Game ${gameNumber}`, 'success');
          
          // Update all saved rows dynamically
          rows.forEach((row, index) => {
            const inputs = row.querySelectorAll('.score-input');
            const userId = inputs[0].getAttribute('data-user-id');
            
            // Check if this row was saved
            const wasSaved = scoresToSave.some(score => score.user_id == userId);
            if (wasSaved) {
              const scoreInput = row.querySelector('[data-field="score"]');
              const strikesInput = row.querySelector('[data-field="strikes"]');
              const sparesInput = row.querySelector('[data-field="spares"]');
              const openFramesInput = row.querySelector('[data-field="open_frames"]');
              
              updatePlayerStatus(row, scoreInput.value, strikesInput.value, sparesInput.value, openFramesInput.value);
            }
          });
          
          // Auto-refresh the current tab data after a short delay
          setTimeout(() => {
            refreshCurrentTabData();
          }, 1000);
        } else {
          showNotification('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while saving scores', 'error');
      })
      .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
      });
    }

    // Auto-save individual scores on Enter key
    document.addEventListener('DOMContentLoaded', function() {
      // Load most recent data on page load
      console.log('Page loaded, loading most recent data...');
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      loadDataForDateFilter(selectedDate);
      
      document.addEventListener('keypress', function(e) {
        if (e.target.classList.contains('score-input') && e.key === 'Enter') {
          const input = e.target;
          const userId = input.getAttribute('data-user-id');
          const gameNumber = input.getAttribute('data-game');
          const field = input.getAttribute('data-field');
          const value = input.value.trim();
          
          if (value && field === 'score' && (value < 0 || value > 300)) {
            input.classList.add('is-invalid');
            showNotification('Score must be between 0-300', 'error');
            return;
          }
          
          input.classList.remove('is-invalid');
          
          // Move to next input
          const nextInput = input.parentElement.nextElementSibling?.querySelector('.score-input');
          if (nextInput) {
            nextInput.focus();
          }
        }
      });
    });

    function refreshScores() {
      location.reload();
    }

  </script>


  <script>
    
    // Cache for loaded data
    const dataCache = {};
    
    function loadDataForDateFilter(selectedDate) {
      // Check cache first
      if (dataCache[selectedDate]) {
        updateTablesWithData(dataCache[selectedDate]);
        return;
      }
      
      // Show simple loading state
      const tables = document.querySelectorAll('.table tbody');
      tables.forEach(table => {
        table.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>';
      });
      
      // Simple AJAX request
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/session-management.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              if (data.success) {
                dataCache[selectedDate] = data.players;
                // Store the session_id for this date
                if (data.session_id) {
                  window.currentSessionId = data.session_id;
                  console.log('Session ID for date', selectedDate, ':', data.session_id);
                }
                updateTablesWithData(data.players);
                
                // Show debug info in console
                if (data.debug) {
                  console.log('Loading Performance:', data.debug);
                  console.log(`Query 1: ${data.debug.query1_time}ms, Query 2: ${data.debug.query2_time}ms, Process: ${data.debug.process_time}ms, Total: ${data.debug.total_time}ms`);
                }
              } else {
                showNotification('Error: ' + data.message, 'error');
              }
            } catch (e) {
              showNotification('Error parsing response', 'error');
            }
          } else {
            showNotification('Error loading data', 'error');
          }
        }
      };
      
      xhr.send('action=get_players_data&selected_date=' + encodeURIComponent(selectedDate) + '&t=' + Date.now());
    }
    
    function updateTablesWithData(players) {
      // Update Overall Rankings tab
      updateOverallRankingsTable(players);
      
      // Update Game tabs
      for (let game = 1; game <= 5; game++) {
        updateGameTable(game, players);
      }
    }
    
    function updateOverallRankingsTable(players) {
      const tbody = document.querySelector('#overallRankingsTable tbody');
      if (!tbody) return;
      
      let html = '';
      
      players.forEach(player => {
        const totalScore = player.total_score || 0;
        const avgScore = player.avg_score || 0;
        const gamesPlayed = player.games_played || 0;
        const bestScore = player.best_score || 0;
        const totalStrikes = player.total_strikes || 0;
        const totalSpares = player.total_spares || 0;
        const lastUpdated = player.last_updated || 'Never';
        
        html += `
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(player.user_id % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                </div>
              </div>
            </td>
            <td><span class="badge bg-info">${player.team_name || 'No Team'}</span></td>
            <td><span class="fw-bold text-success">${totalScore}</span></td>
            <td><span class="fw-bold text-primary">${avgScore}</span></td>
            <td>${gamesPlayed}</td>
            <td><span class="badge bg-info">${bestScore > 0 ? bestScore : '-'}</span></td>
            <td>${totalStrikes}</td>
            <td>${totalSpares}</td>
            <td><span class="badge bg-success">Active</span></td>
            <td><small class="text-muted">${lastUpdated}</small></td>
            <td>
              <div class="admin-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="viewPlayerDetails(${player.user_id})" title="View Details">
                  <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="editPlayerScore(${player.user_id})" title="Edit Score">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewPlayerHistory(${player.user_id})" title="View History">
                  <i class="ti ti-history"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      
      tbody.innerHTML = html || '<tr><td colspan="11" class="text-center text-muted py-4">No data available for selected date range</td></tr>';
    }
    
    function updateGameTable(gameNumber, players) {
      const tbody = document.querySelector(`#game${gameNumber}Table tbody`);
      if (!tbody) return;
      
      let html = '';
      
      players.forEach(player => {
        const gameScore = player[`game_${gameNumber}_score`] || null;
        const score = gameScore ? gameScore.player_score : '';
        const strikes = gameScore ? gameScore.strikes : '';
        const spares = gameScore ? gameScore.spares : '';
        const openFrames = gameScore ? gameScore.open_frames : '';
        const createdAt = gameScore ? gameScore.created_at : '';
        
        html += `
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(player.user_id % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <strong>${player.first_name} ${player.last_name}</strong>
                </div>
              </div>
            </td>
            <td class="text-center">
              <span class="badge bg-info">${player.team_name || 'No Team'}</span>
            </td>
            <td>
              <input type="number" class="form-control form-control-sm score-input" 
                     data-user-id="${player.user_id}" data-field="score" data-game="${gameNumber}"
                     value="${score}" min="0" max="300" placeholder="0-300">
            </td>
            <td>
              <input type="number" class="form-control form-control-sm score-input" 
                     data-user-id="${player.user_id}" data-field="strikes" data-game="${gameNumber}"
                     value="${strikes}" min="0" max="12" placeholder="0-12">
            </td>
            <td>
              <input type="number" class="form-control form-control-sm score-input" 
                     data-user-id="${player.user_id}" data-field="spares" data-game="${gameNumber}"
                     value="${spares}" min="0" max="12" placeholder="0-12">
            </td>
            <td>
              <input type="number" class="form-control form-control-sm score-input" 
                     data-user-id="${player.user_id}" data-field="open_frames" data-game="${gameNumber}"
                     value="${openFrames}" min="0" max="12" placeholder="0-12">
            </td>
            <td class="text-center">
              ${gameScore ? 
                `<span class="badge bg-success">Completed</span><br><small class="text-muted">${new Date(createdAt).toLocaleTimeString()}</small>` : 
                '<span class="badge bg-warning">Pending</span>'
              }
            </td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" onclick="savePlayerScore(${player.user_id}, ${gameNumber}, '${player.first_name} ${player.last_name}')" title="Save Score">
                <i class="ti ti-device-floppy me-1"></i>Save
              </button>
            </td>
          </tr>
        `;
      });
      
      tbody.innerHTML = html || '<tr><td colspan="8" class="text-center text-muted py-4">No data available for selected date range</td></tr>';
    }
    
    function refreshTable() {
      const dateFilter = document.getElementById('dateFilter');
      loadDataForDateFilter(dateFilter.value);
    }
    
    // Export to CSV function
    function exportToExcel() {
      try {
        // Show loading notification
        showNotification('Preparing CSV file...', 'info');
        
        // Create a temporary form to submit the export request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'ajax/export-scores-excel.php';
        form.target = '_blank';
        
        // Add any necessary parameters
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter && dateFilter.value) {
          const dateInput = document.createElement('input');
          dateInput.type = 'hidden';
          dateInput.name = 'selected_date';
          dateInput.value = dateFilter.value;
          form.appendChild(dateInput);
        }
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        
        // Show success notification
        setTimeout(() => {
          showNotification('CSV file downloaded successfully!', 'success');
        }, 1000);
        
      } catch (error) {
        console.error('Export error:', error);
        showNotification('Error exporting to CSV: ' + error.message, 'error');
      }
    }
  </script>
</body>

</html>
