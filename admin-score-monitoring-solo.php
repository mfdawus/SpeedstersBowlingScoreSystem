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
        // Get scores only for selected participants
        $sessionScores = getSessionScores($sessionId);
    }
} else {
    // If no session in URL, get the active session
    $activeSession = getActiveSession();
    if ($activeSession) {
        $sessionId = $activeSession['session_id'];
        $currentSession = $activeSession;
        // Get scores only for selected participants
        $sessionScores = getSessionScores($sessionId);
    }
}

// Get only selected participants for the current session
$allPlayers = [];
if ($sessionId) {
    $allPlayers = getSessionParticipantsForScoring($sessionId);
} else {
    // If no session, show empty array (no players)
    $allPlayers = [];
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Solo Players Score Monitoring - VIPERS VENOMS Bowling System</title>
  <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
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
    
    /* Prevent layout shifts during score updates */
    .table tbody tr {
      transition: background-color 0.3s ease;
    }
    
    .table tbody tr:hover {
      transform: none;
    }
    
    .table-responsive {
      min-height: 200px;
    }
    
    /* Ensure stable input field sizing */
    .score-input {
      min-width: 80px;
      transition: all 0.2s ease;
    }
    
    .score-input:focus {
      transform: none;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Lane Assignment Styling */
    .lane-assignment {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
      color: white;
      border-radius: 12px;
      padding: 8px 12px;
      min-width: 60px;
      box-shadow: 0 3px 8px rgba(0, 123, 255, 0.3);
      transition: all 0.3s ease;
    }
    
    .lane-assignment:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }
    
    .lane-number {
      font-size: 1.4rem;
      font-weight: bold;
      line-height: 1;
      margin-bottom: 2px;
    }
    
    .lane-label {
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      opacity: 0.9;
    }
    
    .lane-unassigned {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
      border-radius: 12px;
      padding: 8px 12px;
      min-width: 60px;
      box-shadow: 0 3px 8px rgba(108, 117, 125, 0.3);
      transition: all 0.3s ease;
    }
    
    .lane-unassigned:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
    }
    
    .lane-unassigned i {
      font-size: 1.2rem;
      margin-bottom: 2px;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">
   <?php include 'includes/app-topstrip.php'; ?>

    <?php include 'includes/sidebar.php'; ?>
    
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <?php include 'includes/header.php'; ?>
      
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
              WHERE DATE(session_date) = CURDATE() AND status = 'Active' AND game_mode = 'Solo'
              ORDER BY started_at DESC
              LIMIT 1
            ");
            $stmt->execute();
            $todaySession = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($todaySession): 
              // Get session participants count - use actual selected participants
              $participantCount = getSessionParticipantCount($todaySession['session_id']);
              
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
            
            // Initialize variables to prevent undefined variable errors
            $playersWithScores = 0;
            $activeChange = 0;
            $avgChange = 0;
            $gamesChange = 0;
            
            // Get total solo players
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE user_role = 'Player' AND status = 'Active'");
            $stmt->execute();
            $totalPlayers = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get players who played today (using session dates, Solo only)
            $stmt = $pdo->prepare("
              SELECT COUNT(DISTINCT gs.user_id) as count 
              FROM game_scores gs
              INNER JOIN game_sessions sess ON gs.session_id = sess.session_id
              WHERE DATE(sess.session_date) = CURDATE() AND gs.status = 'Completed' AND gs.game_mode = 'Solo'
            ");
            $stmt->execute();
            $activeToday = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            $playersWithScores = $activeToday; // Set this variable for the card display
            
            // Get average score today (using session dates, Solo only)
            $stmt = $pdo->prepare("
              SELECT AVG(gs.player_score) as avg_score 
              FROM game_scores gs
              INNER JOIN game_sessions sess ON gs.session_id = sess.session_id
              WHERE DATE(sess.session_date) = CURDATE() AND gs.status = 'Completed' AND gs.game_mode = 'Solo'
            ");
            $stmt->execute();
            $avgScoreToday = $stmt->fetch(PDO::FETCH_ASSOC)['avg_score'];
            $avgScoreToday = $avgScoreToday ? round($avgScoreToday, 1) : 0;
            
            // Get total games played today (using session dates, Solo only)
            $stmt = $pdo->prepare("
              SELECT COUNT(*) as count 
              FROM game_scores gs
              INNER JOIN game_sessions sess ON gs.session_id = sess.session_id
              WHERE DATE(sess.session_date) = CURDATE() AND gs.status = 'Completed' AND gs.game_mode = 'Solo'
            ");
            $stmt->execute();
            $gamesToday = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Get yesterday's stats for comparison (using session dates, Solo only)
            $stmt = $pdo->prepare("
              SELECT COUNT(DISTINCT gs.user_id) as active_yesterday,
                     COUNT(*) as games_yesterday,
                     AVG(gs.player_score) as avg_yesterday
              FROM game_scores gs
              INNER JOIN game_sessions sess ON gs.session_id = sess.session_id
              WHERE DATE(sess.session_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND gs.status = 'Completed' AND gs.game_mode = 'Solo'
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
                          
                          // Get session dates with score counts (Solo sessions only)
                          $stmt = $pdo->prepare("
                            SELECT 
                              DATE(gs.session_date) as match_date,
                              COUNT(DISTINCT gs.session_id) as session_count,
                              COUNT(gsc.score_id) as score_count
                            FROM game_sessions gs
                            LEFT JOIN game_scores gsc ON gs.session_id = gsc.session_id AND gsc.status = 'Completed'
                            WHERE (gs.status = 'Active' OR gs.status = 'Completed') AND gs.game_mode = 'Solo'
                            GROUP BY DATE(gs.session_date)
                            ORDER BY gs.session_date DESC
                          ");
                          $stmt->execute();
                          $sessionDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          // Debug: Log the dates found
                          error_log("Found " . count($sessionDates) . " session dates: " . json_encode($sessionDates));
                          
                          // Check for active session first
                          $activeSessionDate = null;
                          $stmt = $pdo->prepare("
                            SELECT DATE(session_date) as match_date
                            FROM game_sessions 
                            WHERE DATE(session_date) = CURDATE() AND status = 'Active' AND game_mode = 'Solo'
                            ORDER BY started_at DESC
                            LIMIT 1
                          ");
                          $stmt->execute();
                          $activeSession = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                          $selectedDate = null;
                          $selectedDateInfo = null;
                          
                          if ($activeSession) {
                            $activeSessionDate = $activeSession['match_date'];
                            // Find the active session in our dates list
                            foreach ($sessionDates as $date) {
                              if ($date['match_date'] === $activeSessionDate) {
                                $selectedDateInfo = $date;
                                $selectedDate = $date['match_date'];
                                break;
                              }
                            }
                          }
                          
                          // If no active session found, select the most recent date
                          if (!$selectedDateInfo && !empty($sessionDates)) {
                            $selectedDateInfo = $sessionDates[0];
                            $selectedDate = $sessionDates[0]['match_date'];
                          }
                          
                          // Display selected date first
                          if ($selectedDateInfo) {
                            $formattedDate = date('M j, Y', strtotime($selectedDateInfo['match_date']));
                            $scoreInfo = $selectedDateInfo['score_count'] > 0 ? " ({$selectedDateInfo['score_count']} scores)" : " (no scores)";
                            echo '<option value="' . $selectedDateInfo['match_date'] . '" selected>' . $formattedDate . $scoreInfo . '</option>';
                          } else {
                            echo '<option value="today" selected>Today</option>';
                          }
                          
                          // Add other dates (excluding the selected date)
                          foreach ($sessionDates as $date) {
                            if ($date['match_date'] !== $selectedDate) {
                              $formattedDate = date('M j, Y', strtotime($date['match_date']));
                              $scoreInfo = $date['score_count'] > 0 ? " ({$date['score_count']} scores)" : " (no scores)";
                              echo '<option value="' . $date['match_date'] . '">' . $formattedDate . $scoreInfo . '</option>';
                            }
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
                              <th scope="col">Lane</th>
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
                              // Get only selected participants for this session
                              $allPlayers = getSessionParticipantsForScoring($sessionId);
                              
                              if (!empty($allPlayers)): 
                                // Calculate rankings and stats for each player
                                $playerStats = [];
                                // Get all today's scores in one query (Solo only)
                                $stmt = $pdo->prepare("
                                  SELECT 
                                      user_id,
                                      player_score,
                                      strikes,
                                      spares,
                                      created_at
                                  FROM game_scores 
                                  WHERE status = 'Completed' AND game_mode = 'Solo' AND DATE(created_at) = CURDATE()
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
                                  <tr data-player-id="<?php echo $player['user_id']; ?>">
                              <td>
                                <div class="d-flex align-items-center">
                                        <img src="assets/images/profile/user-<?php echo ($player['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32">
                                  <div>
                                          <h6 class="mb-0"><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></h6>
                                          <small class="text-muted"><?php echo htmlspecialchars($player['user_role']); ?></small>
                                  </div>
                                </div>
                              </td>
                              <td>
                                <?php 
                                // Get lane assignment for this player in current session
                                $laneNumber = null;
                                if ($sessionId) {
                                    try {
                                        $pdo = getDBConnection();
                                        $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                                        $stmt->execute([$sessionId, $player['user_id']]);
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        if ($result) {
                                            $laneNumber = $result['lane_number'];
                                        }
                                    } catch (Exception $e) {
                                        // Handle error silently
                                    }
                                }
                                ?>
                                <?php if ($laneNumber): ?>
                                  <span>Lane <?php echo $laneNumber; ?></span>
                                <?php else: ?>
                                  <span class="text-muted">-</span>
                                <?php endif; ?>
                              </td>
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
                                  <td colspan="10" class="text-center text-muted py-4">
                                    <i class="ti ti-users fs-1 mb-3 d-block"></i>
                                    No VipersVenoms players found in the database.
                                  </td>
                                </tr>
                              <?php endif; ?>
                            <?php } catch (Exception $e) { ?>
                              <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                  <i class="ti ti-alert-triangle fs-1 mb-3 d-block"></i>
                                  Error loading Speedsters players: <?php echo htmlspecialchars($e->getMessage()); ?>
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
                                  <th scope="col" style="width: 10%;">Lane</th>
                                  <th scope="col" style="width: 12%;">Score</th>
                                  <th scope="col" style="width: 12%;">Strikes</th>
                                  <th scope="col" style="width: 12%;">Spares</th>
                                  <th scope="col" style="width: 12%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  // Use session participants instead of all Speedsters
                                  $allPlayers = getSessionParticipantsForScoring($sessionId);
                                  
                                  if (!empty($allPlayers)): 
                                    // Get all Game 1 scores for today in one query (Solo only)
                                    $stmt = $pdo->prepare("
                                      SELECT 
                                          user_id,
                                          player_score,
                                          strikes,
                                          spares,
                                          open_frames,
                                          created_at
                                      FROM game_scores 
                                      WHERE game_number = 1 AND status = 'Completed' AND game_mode = 'Solo' AND DATE(created_at) = CURDATE()
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
                                            <br><small class="text-muted"><?php echo htmlspecialchars($player['user_role']); ?></small>
                                  </div>
                                </div>
                                      </td>
                                      <td>
                                        <?php 
                                        // Get lane assignment for this player in current session
                                        $laneNumber = null;
                                        if ($sessionId) {
                                            try {
                                                $pdo = getDBConnection();
                                                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                                                $stmt->execute([$sessionId, $player['user_id']]);
                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                if ($result) {
                                                    $laneNumber = $result['lane_number'];
                                                }
                                            } catch (Exception $e) {
                                                // Handle error silently
                                            }
                                        }
                                        ?>
                                        <?php if ($laneNumber): ?>
                                          <span>Lane <?php echo $laneNumber; ?></span>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                  <th scope="col" style="width: 10%;">Lane</th>
                                  <th scope="col" style="width: 12%;">Score</th>
                                  <th scope="col" style="width: 12%;">Strikes</th>
                                  <th scope="col" style="width: 12%;">Spares</th>
                                  <th scope="col" style="width: 12%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  // Use session participants instead of all Speedsters
                                  $allPlayers = getSessionParticipantsForScoring($sessionId);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 2 score for this player (today only, Solo only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 2 AND status = 'Completed' AND game_mode = 'Solo' AND DATE(created_at) = CURDATE()
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
                                            <br><small class="text-muted"><?php echo htmlspecialchars($player['user_role']); ?></small>
                                  </div>
                                </div>
                                      </td>
                                      <td>
                                        <?php 
                                        // Get lane assignment for this player in current session
                                        $laneNumber = null;
                                        if ($sessionId) {
                                            try {
                                                $pdo = getDBConnection();
                                                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                                                $stmt->execute([$sessionId, $player['user_id']]);
                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                if ($result) {
                                                    $laneNumber = $result['lane_number'];
                                                }
                                            } catch (Exception $e) {
                                                // Handle error silently
                                            }
                                        }
                                        ?>
                                        <?php if ($laneNumber): ?>
                                          <span>Lane <?php echo $laneNumber; ?></span>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                  <th scope="col" style="width: 10%;">Lane</th>
                                  <th scope="col" style="width: 12%;">Score</th>
                                  <th scope="col" style="width: 12%;">Strikes</th>
                                  <th scope="col" style="width: 12%;">Spares</th>
                                  <th scope="col" style="width: 12%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  // Use session participants instead of all Speedsters
                                  $allPlayers = getSessionParticipantsForScoring($sessionId);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 3 score for this player (today only, Solo only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 3 AND status = 'Completed' AND game_mode = 'Solo' AND DATE(created_at) = CURDATE()
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
                                            <br><small class="text-muted"><?php echo htmlspecialchars($player['user_role']); ?></small>
                                  </div>
                                </div>
                                      </td>
                                      <td>
                                        <?php 
                                        // Get lane assignment for this player in current session
                                        $laneNumber = null;
                                        if ($sessionId) {
                                            try {
                                                $pdo = getDBConnection();
                                                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                                                $stmt->execute([$sessionId, $player['user_id']]);
                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                if ($result) {
                                                    $laneNumber = $result['lane_number'];
                                                }
                                            } catch (Exception $e) {
                                                // Handle error silently
                                            }
                                        }
                                        ?>
                                        <?php if ($laneNumber): ?>
                                          <span>Lane <?php echo $laneNumber; ?></span>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                  <th scope="col" style="width: 10%;">Lane</th>
                                  <th scope="col" style="width: 12%;">Score</th>
                                  <th scope="col" style="width: 12%;">Strikes</th>
                                  <th scope="col" style="width: 12%;">Spares</th>
                                  <th scope="col" style="width: 12%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                            </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  // Use session participants instead of all Speedsters
                                  $allPlayers = getSessionParticipantsForScoring($sessionId);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 4 score for this player (today only, Solo only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 4 AND status = 'Completed' AND game_mode = 'Solo' AND DATE(created_at) = CURDATE()
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
                                            <br><small class="text-muted"><?php echo htmlspecialchars($player['user_role']); ?></small>
                                  </div>
                                </div>
                                      </td>
                                      <td>
                                        <?php 
                                        // Get lane assignment for this player in current session
                                        $laneNumber = null;
                                        if ($sessionId) {
                                            try {
                                                $pdo = getDBConnection();
                                                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                                                $stmt->execute([$sessionId, $player['user_id']]);
                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                if ($result) {
                                                    $laneNumber = $result['lane_number'];
                                                }
                                            } catch (Exception $e) {
                                                // Handle error silently
                                            }
                                        }
                                        ?>
                                        <?php if ($laneNumber): ?>
                                          <span>Lane <?php echo $laneNumber; ?></span>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                  <th scope="col" style="width: 10%;">Lane</th>
                                  <th scope="col" style="width: 12%;">Score</th>
                                  <th scope="col" style="width: 12%;">Strikes</th>
                                  <th scope="col" style="width: 12%;">Spares</th>
                                  <th scope="col" style="width: 12%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 17%;">Actions</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                // Get all players from the database
                                try {
                                  $pdo = getDBConnection();
                                  // Use session participants instead of all Speedsters
                                  $allPlayers = getSessionParticipantsForScoring($sessionId);
                                  
                                  if (!empty($allPlayers)): 
                                    foreach ($allPlayers as $player):
                                      // Get Game 5 score for this player (today only, Solo only)
                                      $stmt = $pdo->prepare("
                                        SELECT 
                                            player_score,
                                            strikes,
                                            spares,
                                            open_frames,
                                            created_at
                                        FROM game_scores 
                                        WHERE user_id = ? AND game_number = 5 AND status = 'Completed' AND game_mode = 'Solo' AND DATE(created_at) = CURDATE()
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
                                      <td>
                                        <?php 
                                        // Get lane assignment for this player in current session
                                        $laneNumber = null;
                                        if ($sessionId) {
                                            try {
                                                $pdo = getDBConnection();
                                                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                                                $stmt->execute([$sessionId, $player['user_id']]);
                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                if ($result) {
                                                    $laneNumber = $result['lane_number'];
                                                }
                                            } catch (Exception $e) {
                                                // Handle error silently
                                            }
                                        }
                                        ?>
                                        <?php if ($laneNumber): ?>
                                          <span>Lane <?php echo $laneNumber; ?></span>
                                        <?php else: ?>
                                          <span class="text-muted">-</span>
                                        <?php endif; ?>
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
      // Find the player row to get current data - try multiple methods
      let playerRow = document.querySelector(`tr[data-player-id="${playerId}"]`);
      
      // If not found by data attribute, try to find by button onclick
      if (!playerRow) {
        const button = document.querySelector(`button[onclick*="viewPlayerDetails(${playerId})"]`);
        if (button) {
          playerRow = button.closest('tr');
        }
      }
      
      // If still not found, try to find by user ID in input fields
      if (!playerRow) {
        const input = document.querySelector(`input[data-user-id="${playerId}"]`);
        if (input) {
          playerRow = input.closest('tr');
        }
      }
      
      if (!playerRow) {
        showNotification('Player not found in current table. Please refresh the page and try again.', 'error');
        return;
      }
      
      // Extract player data from the row
      const playerName = playerRow.querySelector('h6')?.textContent || 'Unknown Player';
      const laneNumber = playerRow.querySelector('td:nth-child(3)')?.textContent || 'Not assigned';
      const totalScore = playerRow.querySelector('td:nth-child(4) span')?.textContent || '0';
      const avgScore = playerRow.querySelector('td:nth-child(5)')?.textContent || '0';
      const gamesPlayed = playerRow.querySelector('td:nth-child(6)')?.textContent || '0';
      const bestScore = playerRow.querySelector('td:nth-child(7) span')?.textContent || '0';
      const strikes = playerRow.querySelector('td:nth-child(8)')?.textContent || '0';
      const spares = playerRow.querySelector('td:nth-child(9)')?.textContent || '0';
      const lastUpdated = playerRow.querySelector('td:nth-child(10) small')?.textContent || 'Never';
      
      // Create and show detailed modal
      showPlayerDetailsModal({
        playerId: playerId,
        playerName: playerName,
        laneNumber: laneNumber,
        totalScore: totalScore,
        avgScore: avgScore,
        gamesPlayed: gamesPlayed,
        bestScore: bestScore,
        strikes: strikes,
        spares: spares,
        lastUpdated: lastUpdated
      });
    }

    function editPlayerScore(playerId) {
      // Find the player row - try multiple methods
      let playerRow = document.querySelector(`tr[data-player-id="${playerId}"]`);
      
      // If not found by data attribute, try to find by button onclick
      if (!playerRow) {
        const button = document.querySelector(`button[onclick*="editPlayerScore(${playerId})"]`);
        if (button) {
          playerRow = button.closest('tr');
        }
      }
      
      // If still not found, try to find by user ID in input fields
      if (!playerRow) {
        const input = document.querySelector(`input[data-user-id="${playerId}"]`);
        if (input) {
          playerRow = input.closest('tr');
        }
      }
      
      if (!playerRow) {
        showNotification('Player not found in current table. Please refresh the page and try again.', 'error');
        return;
      }
      
      const playerName = playerRow.querySelector('h6')?.textContent || 'Unknown Player';
      
      // Create and show score editor modal
      showScoreEditorModal(playerId, playerName);
    }

    function viewPlayerHistory(playerId) {
      // Find the player row - try multiple methods
      let playerRow = document.querySelector(`tr[data-player-id="${playerId}"]`);
      
      // If not found by data attribute, try to find by button onclick
      if (!playerRow) {
        const button = document.querySelector(`button[onclick*="viewPlayerHistory(${playerId})"]`);
        if (button) {
          playerRow = button.closest('tr');
        }
      }
      
      // If still not found, try to find by user ID in input fields
      if (!playerRow) {
        const input = document.querySelector(`input[data-user-id="${playerId}"]`);
        if (input) {
          playerRow = input.closest('tr');
        }
      }
      
      if (!playerRow) {
        showNotification('Player not found in current table. Please refresh the page and try again.', 'error');
        return;
      }
      
      const playerName = playerRow.querySelector('h6')?.textContent || 'Unknown Player';
      
      // Load and show player history
      loadPlayerHistory(playerId, playerName);
    }

    function showPlayerDetailsModal(playerData) {
      // Create modal HTML
      const modalHtml = `
        <div class="modal fade" id="playerDetailsModal" tabindex="-1" aria-labelledby="playerDetailsModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="playerDetailsModalLabel">
                  <i class="ti ti-user me-2"></i>Player Details - ${playerData.playerName}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Current Statistics</h6>
                    <table class="table table-sm">
                      <tr><td><strong>Player ID:</strong></td><td>${playerData.playerId}</td></tr>
                      <tr><td><strong>Lane:</strong></td><td>${playerData.laneNumber}</td></tr>
                      <tr><td><strong>Total Score:</strong></td><td><span class="badge bg-success">${playerData.totalScore}</span></td></tr>
                      <tr><td><strong>Average Score:</strong></td><td><span class="badge bg-info">${playerData.avgScore}</span></td></tr>
                      <tr><td><strong>Games Played:</strong></td><td><span class="badge bg-secondary">${playerData.gamesPlayed}</span></td></tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Performance Metrics</h6>
                    <table class="table table-sm">
                      <tr><td><strong>Best Game:</strong></td><td><span class="badge bg-warning">${playerData.bestScore}</span></td></tr>
                      <tr><td><strong>Total Strikes:</strong></td><td><span class="badge bg-danger">${playerData.strikes}</span></td></tr>
                      <tr><td><strong>Total Spares:</strong></td><td><span class="badge bg-info">${playerData.spares}</span></td></tr>
                      <tr><td><strong>Last Updated:</strong></td><td><small class="text-muted">${playerData.lastUpdated}</small></td></tr>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="editPlayerScore(${playerData.playerId}); $('#playerDetailsModal').modal('hide');">
                  <i class="ti ti-edit me-1"></i>Edit Scores
                </button>
                <button type="button" class="btn btn-info" onclick="viewPlayerHistory(${playerData.playerId}); $('#playerDetailsModal').modal('hide');">
                  <i class="ti ti-history me-1"></i>View History
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('playerDetailsModal');
      if (existingModal) {
        existingModal.remove();
      }
      
      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('playerDetailsModal'));
      modal.show();
    }

    function showScoreEditorModal(playerId, playerName) {
      // Create score editor modal
      const modalHtml = `
        <div class="modal fade" id="scoreEditorModal" tabindex="-1" aria-labelledby="scoreEditorModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="scoreEditorModalLabel">
                  <i class="ti ti-edit me-2"></i>Edit Scores - ${playerName}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Game 1</h6>
                    <div class="mb-3">
                      <label class="form-label">Score</label>
                      <input type="number" class="form-control" id="game1_score" min="0" max="300" placeholder="0-300">
                    </div>
                    <div class="row">
                      <div class="col-4">
                        <label class="form-label">Strikes</label>
                        <input type="number" class="form-control" id="game1_strikes" min="0" max="12" placeholder="0-12">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Spares</label>
                        <input type="number" class="form-control" id="game1_spares" min="0" max="10" placeholder="0-10">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Open Frames</label>
                        <input type="number" class="form-control" id="game1_open" min="0" max="10" placeholder="0-10">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Game 2</h6>
                    <div class="mb-3">
                      <label class="form-label">Score</label>
                      <input type="number" class="form-control" id="game2_score" min="0" max="300" placeholder="0-300">
                    </div>
                    <div class="row">
                      <div class="col-4">
                        <label class="form-label">Strikes</label>
                        <input type="number" class="form-control" id="game2_strikes" min="0" max="12" placeholder="0-12">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Spares</label>
                        <input type="number" class="form-control" id="game2_spares" min="0" max="10" placeholder="0-10">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Open Frames</label>
                        <input type="number" class="form-control" id="game2_open" min="0" max="10" placeholder="0-10">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Game 3</h6>
                    <div class="mb-3">
                      <label class="form-label">Score</label>
                      <input type="number" class="form-control" id="game3_score" min="0" max="300" placeholder="0-300">
                    </div>
                    <div class="row">
                      <div class="col-4">
                        <label class="form-label">Strikes</label>
                        <input type="number" class="form-control" id="game3_strikes" min="0" max="12" placeholder="0-12">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Spares</label>
                        <input type="number" class="form-control" id="game3_spares" min="0" max="10" placeholder="0-10">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Open Frames</label>
                        <input type="number" class="form-control" id="game3_open" min="0" max="10" placeholder="0-10">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Game 4</h6>
                    <div class="mb-3">
                      <label class="form-label">Score</label>
                      <input type="number" class="form-control" id="game4_score" min="0" max="300" placeholder="0-300">
                    </div>
                    <div class="row">
                      <div class="col-4">
                        <label class="form-label">Strikes</label>
                        <input type="number" class="form-control" id="game4_strikes" min="0" max="12" placeholder="0-12">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Spares</label>
                        <input type="number" class="form-control" id="game4_spares" min="0" max="10" placeholder="0-10">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Open Frames</label>
                        <input type="number" class="form-control" id="game4_open" min="0" max="10" placeholder="0-10">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">Game 5</h6>
                    <div class="mb-3">
                      <label class="form-label">Score</label>
                      <input type="number" class="form-control" id="game5_score" min="0" max="300" placeholder="0-300">
                    </div>
                    <div class="row">
                      <div class="col-4">
                        <label class="form-label">Strikes</label>
                        <input type="number" class="form-control" id="game5_strikes" min="0" max="12" placeholder="0-12">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Spares</label>
                        <input type="number" class="form-control" id="game5_spares" min="0" max="10" placeholder="0-10">
                      </div>
                      <div class="col-4">
                        <label class="form-label">Open Frames</label>
                        <input type="number" class="form-control" id="game5_open" min="0" max="10" placeholder="0-10">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveAllPlayerScores(${playerId})">
                  <i class="ti ti-device-floppy me-1"></i>Save All Scores
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('scoreEditorModal');
      if (existingModal) {
        existingModal.remove();
      }
      
      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Show modal first
      const modal = new bootstrap.Modal(document.getElementById('scoreEditorModal'));
      modal.show();
      
      // Load current scores after modal is shown
      setTimeout(() => {
        loadCurrentScores(playerId);
      }, 300);
    }

    function loadPlayerHistory(playerId, playerName) {
      // Show loading notification
      showNotification('Loading player history...', 'info');
      
      // Create history modal
      const modalHtml = `
        <div class="modal fade" id="playerHistoryModal" tabindex="-1" aria-labelledby="playerHistoryModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="playerHistoryModalLabel">
                  <i class="ti ti-history me-2"></i>Score History - ${playerName}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div id="historyContent">
                  <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading player history...</p>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportPlayerHistory(${playerId})">
                  <i class="ti ti-download me-1"></i>Export History
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('playerHistoryModal');
      if (existingModal) {
        existingModal.remove();
      }
      
      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('playerHistoryModal'));
      modal.show();
      
      // Load history data
      fetchPlayerHistoryData(playerId);
    }

    function editGameScore(playerId, gameNumber) {
      showNotification('Editing Game ' + gameNumber + ' score for player: ' + playerId, 'warning');
      // Here you would open a quick edit modal for the specific game score
    }

    function loadCurrentScores(playerId) {
      
      // Load current scores for the player from the table data
      for (let game = 1; game <= 5; game++) {
        let playerRow = null;
        
        // Try to find player row in the specific game table first
        const gameTable = document.getElementById(`game${game}Table`);
        if (gameTable) {
          playerRow = gameTable.querySelector(`tr[data-player-id="${playerId}"]`);
        }
        
        // If not found in game table, try to find by input field
        if (!playerRow) {
          const input = document.querySelector(`input[data-user-id="${playerId}"][data-game="${game}"]`);
          if (input) {
            playerRow = input.closest('tr');
          }
        }
        
        // If still not found, try to find by button onclick
        if (!playerRow) {
          const button = document.querySelector(`button[onclick*="savePlayerScore(${playerId}, ${game}"]`);
          if (button) {
            playerRow = button.closest('tr');
          }
        }
        
        if (playerRow) {
          const scoreInput = playerRow.querySelector('[data-field="score"]');
          const strikesInput = playerRow.querySelector('[data-field="strikes"]');
          const sparesInput = playerRow.querySelector('[data-field="spares"]');
          const openFramesInput = playerRow.querySelector('[data-field="open_frames"]');
          
          // Get the modal input elements
          const modalScoreInput = document.getElementById(`game${game}_score`);
          const modalStrikesInput = document.getElementById(`game${game}_strikes`);
          const modalSparesInput = document.getElementById(`game${game}_spares`);
          const modalOpenFramesInput = document.getElementById(`game${game}_open`);
          
          // Populate the modal inputs with existing values
          if (scoreInput && modalScoreInput) {
            modalScoreInput.value = scoreInput.value || '';
          }
          if (strikesInput && modalStrikesInput) {
            modalStrikesInput.value = strikesInput.value || '';
          }
          if (sparesInput && modalSparesInput) {
            modalSparesInput.value = sparesInput.value || '';
          }
          if (openFramesInput && modalOpenFramesInput) {
            modalOpenFramesInput.value = openFramesInput.value || '';
          }
        }
      }
      
      // Also try to load from the current data cache if available
      if (window.currentData && window.currentData.length > 0) {
        const playerData = window.currentData.find(p => p.user_id == playerId);
        if (playerData) {
          console.log('Loading from current data cache:', playerData);
          
          for (let game = 1; game <= 5; game++) {
            const gameScore = playerData[`game_${game}_score`];
            if (gameScore) {
              const modalScoreInput = document.getElementById(`game${game}_score`);
              const modalStrikesInput = document.getElementById(`game${game}_strikes`);
              const modalSparesInput = document.getElementById(`game${game}_spares`);
              const modalOpenFramesInput = document.getElementById(`game${game}_open`);
              
              if (modalScoreInput) modalScoreInput.value = gameScore.player_score || '';
              if (modalStrikesInput) modalStrikesInput.value = gameScore.strikes || '';
              if (modalSparesInput) modalSparesInput.value = gameScore.spares || '';
              if (modalOpenFramesInput) modalOpenFramesInput.value = gameScore.open_frames || '';
              
              console.log(`Loaded from cache - Game ${game}:`, gameScore);
            }
          }
        }
      }
    }

    function saveAllPlayerScores(playerId) {
      const saveBtn = document.querySelector('#scoreEditorModal .btn-success');
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Saving...';
      saveBtn.disabled = true;
      
      // Set saving flag to prevent other refreshes
      isSavingScore = true;
      
      let savedCount = 0;
      let totalGames = 0;
      
      // Save each game score
      for (let game = 1; game <= 5; game++) {
        const score = document.getElementById(`game${game}_score`).value;
        const strikes = document.getElementById(`game${game}_strikes`).value;
        const spares = document.getElementById(`game${game}_spares`).value;
        const openFrames = document.getElementById(`game${game}_open`).value;
        
        if (score && score > 0) {
          totalGames++;
          
          // Use the existing savePlayerScore function
          const formData = new FormData();
          formData.append('action', 'add_score');
          formData.append('session_id', window.currentSessionId || <?php echo $sessionId ? $sessionId : 'null'; ?>);
          formData.append('user_id', playerId);
          formData.append('game_number', game);
          formData.append('player_score', score);
          formData.append('strikes', strikes || 0);
          formData.append('spares', spares || 0);
          formData.append('open_frames', openFrames || 0);
          formData.append('game_mode', 'Solo');
          
          fetch('ajax/session-management.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              savedCount++;
              if (savedCount === totalGames) {
                showNotification(`All ${savedCount} scores saved successfully!`, 'success');
                $('#scoreEditorModal').modal('hide');
                // Update the table data without full refresh
                setTimeout(() => {
                  updateTableAfterSave(playerId);
                }, 500);
              }
            } else {
              showNotification(`Failed to save Game ${game}: ${data.message}`, 'error');
            }
          })
          .catch(error => {
            showNotification(`Error saving Game ${game}: ${error.message}`, 'error');
          });
        }
      }
      
      if (totalGames === 0) {
        showNotification('No scores to save', 'warning');
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        isSavingScore = false;
      }
    }

    function fetchPlayerHistoryData(playerId) {
      // Fetch player history from server
      const formData = new FormData();
      formData.append('action', 'get_player_history');
      formData.append('user_id', playerId);
      
      fetch('ajax/session-management.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        const historyContent = document.getElementById('historyContent');
        if (data.success && data.history) {
          // Display history data
          let historyHtml = `
            <div class="row">
              <div class="col-12">
                <h6 class="text-primary mb-3">Recent Games</h6>
                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Game</th>
                        <th>Score</th>
                        <th>Strikes</th>
                        <th>Spares</th>
                        <th>Open Frames</th>
                        <th>Session</th>
                      </tr>
                    </thead>
                    <tbody>
          `;
          
          data.history.forEach(score => {
            historyHtml += `
              <tr>
                <td>${new Date(score.game_date).toLocaleDateString()}</td>
                <td><span class="badge bg-primary">Game ${score.game_number}</span></td>
                <td><strong class="text-success">${score.player_score}</strong></td>
                <td>${score.strikes || 0}</td>
                <td>${score.spares || 0}</td>
                <td>${score.open_frames || 0}</td>
                <td><small class="text-muted">${score.session_name || 'N/A'}</small></td>
              </tr>
            `;
          });
          
          historyHtml += `
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          `;
          
          historyContent.innerHTML = historyHtml;
        } else {
          historyContent.innerHTML = `
            <div class="text-center text-muted">
              <i class="ti ti-history" style="font-size: 3rem; opacity: 0.3;"></i>
              <p class="mt-3">No score history found for this player.</p>
            </div>
          `;
        }
      })
      .catch(error => {
        const historyContent = document.getElementById('historyContent');
        historyContent.innerHTML = `
          <div class="text-center text-danger">
            <i class="ti ti-alert-circle" style="font-size: 3rem;"></i>
            <p class="mt-3">Error loading player history: ${error.message}</p>
          </div>
        `;
      });
    }

    function updateTableAfterSave(playerId) {
      // Update table data after save without full refresh
      
      // Reset the saving flag immediately
      isSavingScore = false;
      
      // Use a simple, direct approach - just refresh the data without clearing tables
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      
      
      // Clear cache and reload data using the stable method
      delete dataCache[selectedDate];
      refreshDataStable(selectedDate, true);
    }

    function refreshDataGentle(selectedDate) {
      // Fetch fresh data and update tables gently without layout disruption
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/session-management.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
              console.log('Refreshing data gently for:', selectedDate);
              
              // Update the data cache
              dataCache[selectedDate] = {
                players: data.players,
                session_id: data.session_id
              };
              
              // Store the session_id
              if (data.session_id) {
                window.currentSessionId = data.session_id;
              }
              
              // Update tables with gentle method
              updateTablesGentle(data.players, selectedDate);
              
            } else {
              console.error('Error refreshing data:', data.message);
            }
          } catch (e) {
            console.error('Error parsing refresh response:', e);
          }
        }
      };
      
      xhr.send('action=get_players_data&selected_date=' + encodeURIComponent(selectedDate) + '&session_type=Solo&t=' + Date.now());
    }

    function updateTablesGentle(players, selectedDate) {
      try {
        // Update tables gently without disrupting layout
        
        // Store current data and selected date globally
        window.currentData = players;
        window.selectedDate = selectedDate;
        
        // DON'T update table headers to avoid duplicate headers
        // updateTableHeaders(selectedDate);
        
        // Update Overall Rankings with dynamic lanes
        updateOverallRankingsTable(players);
        
        // Update Game tabs with gentle method
        for (let game = 1; game <= 5; game++) {
          updateGameTableGentle(players, game);
        }
      } catch (error) {
        console.error('Error in updateTablesGentle:', error);
      }
    }


    function updateGameTableGentle(players, gameNumber) {
      const table = document.getElementById(`game${gameNumber}Table`);
      if (!table) {
        return;
      }
      
      const tbody = table.querySelector('tbody');
      if (!tbody) {
        return;
      }
      
      // Check if table is empty or has "no data" message
      const hasNoData = tbody.innerHTML.includes('No players found') || 
                        tbody.innerHTML.includes('Loading') || 
                        tbody.children.length === 0 ||
                        tbody.children.length < players.length;
      
      // If table is empty or has wrong number of rows, recreate it
      if (hasNoData) {
        console.log(`Game ${gameNumber} table needs recreation (has ${tbody.children.length} rows, needs ${players.length})`);
        updateGameTable(gameNumber, players, window.selectedDate || 'today');
        return;
      }
      
      // Update existing rows instead of replacing them
      players.forEach((player, index) => {
        let row = tbody.children[index];
        if (row) {
          // Update the existing row data
          updateGameRow(row, player, gameNumber);
        } else {
          console.log(`Game ${gameNumber} row ${index} not found for player ${player.name || player.user_id}`);
        }
      });
    }

    function updatePlayerDataOnly(playerId, selectedDate) {
      // Fetch only the specific player's updated data
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/session-management.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            const data = JSON.parse(xhr.responseText);
            if (data.success && data.players) {
              // Find the updated player data
              const updatedPlayer = data.players.find(p => p.user_id == playerId);
              if (updatedPlayer) {
                console.log('Updating player data only:', updatedPlayer);
                
                // Update the player's row in Overall Rankings
                updatePlayerRowInTable('overallRankingsTable', updatedPlayer);
                
                // Update the player's rows in each game table
                for (let game = 1; game <= 5; game++) {
                  updatePlayerRowInTable(`game${game}Table`, updatedPlayer, game);
                }
                
                // Update the global data cache
                if (window.currentData) {
                  const playerIndex = window.currentData.findIndex(p => p.user_id == playerId);
                  if (playerIndex !== -1) {
                    window.currentData[playerIndex] = updatedPlayer;
                  }
                }
              }
            }
          } catch (e) {
            console.error('Error updating player data:', e);
          }
        }
      };
      
      xhr.send('action=get_players_data&selected_date=' + encodeURIComponent(selectedDate) + '&session_type=Solo&t=' + Date.now());
    }

    function updatePlayerRowInTable(tableId, playerData, gameNumber = null) {
      const table = document.getElementById(tableId);
      if (!table) return;
      
      // Find the player's row
      let playerRow = table.querySelector(`tr[data-player-id="${playerData.user_id}"]`);
      if (!playerRow) {
        const input = table.querySelector(`input[data-user-id="${playerData.user_id}"]`);
        if (input) {
          playerRow = input.closest('tr');
        }
      }
      
      if (!playerRow) return;
      
      // Update the row data based on table type
      if (tableId === 'overallRankingsTable') {
        // Update overall rankings row
        updateOverallRankingsRow(playerRow, playerData);
      } else if (tableId.startsWith('game')) {
        // Update game-specific row
        updateGameRow(playerRow, playerData, gameNumber);
      }
    }

    function updateOverallRankingsRow(row, playerData) {
      console.log('Updating overall rankings row for player:', playerData.name || playerData.user_id);
      console.log('Player data:', playerData);
      
      // Update total score
      const totalScoreCell = row.querySelector('.total-score');
      if (totalScoreCell) {
        totalScoreCell.textContent = playerData.total_score || 0;
        console.log('Updated total score to:', playerData.total_score || 0);
      } else {
        console.log('Total score cell not found');
      }
      
      // Update average
      const avgCell = row.querySelector('.avg-score');
      if (avgCell) {
        avgCell.textContent = playerData.average_score || 0;
        console.log('Updated average to:', playerData.average_score || 0);
      } else {
        console.log('Average cell not found');
      }
      
      // Update games played
      const gamesPlayedCell = row.querySelector('.games-played');
      if (gamesPlayedCell) {
        gamesPlayedCell.textContent = playerData.games_played || 0;
        console.log('Updated games played to:', playerData.games_played || 0);
      } else {
        console.log('Games played cell not found');
      }
      
      // Update best game
      const bestGameCell = row.querySelector('.best-game');
      if (bestGameCell) {
        bestGameCell.textContent = playerData.best_game || 0;
        console.log('Updated best game to:', playerData.best_game || 0);
      } else {
        console.log('Best game cell not found');
      }
      
      // Update strikes
      const strikesCell = row.querySelector('.total-strikes');
      if (strikesCell) {
        strikesCell.textContent = playerData.total_strikes || 0;
        console.log('Updated strikes to:', playerData.total_strikes || 0);
      } else {
        console.log('Strikes cell not found');
      }
      
      // Update spares
      const sparesCell = row.querySelector('.total-spares');
      if (sparesCell) {
        sparesCell.textContent = playerData.total_spares || 0;
        console.log('Updated spares to:', playerData.total_spares || 0);
      } else {
        console.log('Spares cell not found');
      }
    }

    function updateGameRow(row, playerData, gameNumber) {
      if (!gameNumber) return;
      
      console.log(`Updating game ${gameNumber} row for player:`, playerData.name || playerData.user_id);
      
      const gameScore = playerData[`game_${gameNumber}_score`];
      if (!gameScore) {
        console.log(`No game ${gameNumber} score data found for player:`, playerData.name || playerData.user_id);
        return;
      }
      
      console.log(`Game ${gameNumber} score data:`, gameScore);
      
      // Update lane number (find the cell that contains the lane number)
      const cells = row.querySelectorAll('td');
      if (cells.length > 1) {
        // The lane number is in the second cell (after player name)
        const laneCell = cells[1];
        const laneStrong = laneCell.querySelector('strong');
        if (laneStrong) {
          laneStrong.textContent = playerData.lane_number || '-';
          console.log(`Updated game ${gameNumber} lane to:`, playerData.lane_number || '-');
        }
      }
      
      // Update score input
      const scoreInput = row.querySelector('[data-field="score"]');
      if (scoreInput) {
        scoreInput.value = gameScore.player_score || '';
        console.log(`Updated game ${gameNumber} score to:`, gameScore.player_score || '');
      } else {
        console.log(`Game ${gameNumber} score input not found`);
      }
      
      // Update strikes input
      const strikesInput = row.querySelector('[data-field="strikes"]');
      if (strikesInput) {
        strikesInput.value = gameScore.strikes || '';
        console.log(`Updated game ${gameNumber} strikes to:`, gameScore.strikes || '');
      } else {
        console.log(`Game ${gameNumber} strikes input not found`);
      }
      
      // Update spares input
      const sparesInput = row.querySelector('[data-field="spares"]');
      if (sparesInput) {
        sparesInput.value = gameScore.spares || '';
        console.log(`Updated game ${gameNumber} spares to:`, gameScore.spares || '');
      } else {
        console.log(`Game ${gameNumber} spares input not found`);
      }
      
      // Update open frames input
      const openFramesInput = row.querySelector('[data-field="open_frames"]');
      if (openFramesInput) {
        openFramesInput.value = gameScore.open_frames || '';
        console.log(`Updated game ${gameNumber} open frames to:`, gameScore.open_frames || '');
      } else {
        console.log(`Game ${gameNumber} open frames input not found`);
      }
      
      // Update status
      updatePlayerStatus(row, gameScore.player_score, gameScore.strikes, gameScore.spares, gameScore.open_frames);
    }

    function updatePlayerLane(userId, laneNumber) {
      if (!userId) {
        showNotification('Invalid user ID', 'error');
        return;
      }
      
      // Show loading notification
      showNotification('Updating lane assignment...', 'info');
      
      // Get current session ID from multiple sources
      const urlParams = new URLSearchParams(window.location.search);
      let sessionId = urlParams.get('session');
      
      // Fallback 1: try to get from window.currentSessionId
      if (!sessionId && window.currentSessionId) {
        sessionId = window.currentSessionId;
      }
      
      // Fallback 2: try to get from window.currentData
      if (!sessionId && window.currentData && window.currentData.length > 0) {
        sessionId = window.currentData[0].session_id;
      }
      
      // Fallback 3: use PHP session ID as last resort
      if (!sessionId) {
        sessionId = <?php echo $sessionId ? $sessionId : 'null'; ?>;
      }
      
      
      if (!sessionId) {
        showNotification('Unable to determine current session. Please refresh the page.', 'error');
        return;
      }
      
      // Prepare form data
      const formData = new FormData();
      formData.append('action', 'update_player_lane');
      formData.append('user_id', userId);
      formData.append('lane_number', laneNumber);
      formData.append('session_id', sessionId);
      
      // Send AJAX request with cache-busting
      fetch('ajax/session-management.php?v=' + Date.now(), {
        method: 'POST',
        body: formData
      })
      .then(response => {
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showNotification(`Lane updated successfully! Player assigned to ${laneNumber ? 'Lane ' + laneNumber : 'No lane'}`, 'success');
          
          // Update the dropdown to show the new lane number
          const dropdown = document.querySelector(`select[data-user-id="${userId}"]`);
          if (dropdown) {
            dropdown.value = laneNumber;
            
            // Add visual feedback - highlight the dropdown briefly
            dropdown.style.backgroundColor = '#d4edda';
            dropdown.style.borderColor = '#28a745';
            setTimeout(() => {
              dropdown.style.backgroundColor = '';
              dropdown.style.borderColor = '';
            }, 2000);
          }
          
          // Update the lane number in the current data cache
          if (window.currentData) {
            const playerIndex = window.currentData.findIndex(p => p.user_id == userId);
            if (playerIndex !== -1) {
              window.currentData[playerIndex].lane_number = laneNumber;
              console.log('Updated player data cache for user:', userId, 'to lane:', laneNumber);
            }
          }
          
          // Auto-refresh the tables to show updated lane numbers
          const dateFilter = document.getElementById('dateFilter');
          const selectedDate = dateFilter ? dateFilter.value : 'today';
          setTimeout(() => {
            refreshDataGentle(selectedDate);
          }, 500);
        } else {
          console.error('Lane update failed:', data.message);
          showNotification('Error updating lane: ' + data.message, 'error');
          // Revert the select value
          const select = document.querySelector(`select[data-user-id="${userId}"]`);
          if (select) {
            select.value = '';
          }
        }
      })
      .catch(error => {
        console.error('Error updating lane:', error);
        showNotification('Error updating lane: ' + error.message, 'error');
        // Revert the select value
        const select = document.querySelector(`select[data-user-id="${userId}"]`);
        if (select) {
          select.value = '';
        }
      });
    }

    function exportPlayerHistory(playerId) {
      showNotification('Exporting player history...', 'info');
      // Export functionality placeholder
      setTimeout(() => {
        showNotification('Export feature coming soon!', 'info');
      }, 1000);
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

    // Session Management Functions
    // Track ongoing submissions to prevent duplicates
    const ongoingSubmissions = new Set();
    
    function savePlayerScore(userId, gameNumber, playerName) {
      
      // Set flag to prevent table refreshes during save
      isSavingScore = true;
      
      // Create unique submission key
      const submissionKey = `${userId}-${gameNumber}`;
      
      // Prevent duplicate submissions
      if (ongoingSubmissions.has(submissionKey)) {
        showNotification('Score is already being saved, please wait...', 'warning');
        isSavingScore = false;
        return;
      }
      
      // Add to ongoing submissions
      ongoingSubmissions.add(submissionKey);
      
      const tableId = `game${gameNumber}Table`;
      const table = document.getElementById(tableId);
      const row = table.querySelector(`tr [data-user-id="${userId}"]`).closest('tr');
      const inputs = row.querySelectorAll('.score-input');
      
      
      let scoreData = {
        user_id: userId,
        game_number: gameNumber,
        player_score: '',
        strikes: '',
        spares: '',
        open_frames: ''
      };
      
      let hasErrors = false;
      let errorMessages = [];
      
      inputs.forEach(input => {
        const field = input.getAttribute('data-field');
        const value = input.value.trim();
        
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
      
      // Reset all invalid states
      inputs.forEach(input => input.classList.remove('is-invalid'));
      
      // Validate Score (Required)
      if (!scoreData.player_score || scoreData.player_score === '') {
        const scoreInput = row.querySelector('[data-field="score"]');
        if (scoreInput) scoreInput.classList.add('is-invalid');
        errorMessages.push('Score is required');
        hasErrors = true;
      } else {
        const score = parseInt(scoreData.player_score);
        if (isNaN(score) || score < 0 || score > 300) {
          const scoreInput = row.querySelector('[data-field="score"]');
          if (scoreInput) scoreInput.classList.add('is-invalid');
          errorMessages.push('Score must be between 0 and 300');
          hasErrors = true;
        }
      }
      
      // Validate Strikes (Required)
      if (!scoreData.strikes || scoreData.strikes === '') {
        const strikesInput = row.querySelector('[data-field="strikes"]');
        if (strikesInput) strikesInput.classList.add('is-invalid');
        errorMessages.push('Strikes is required');
        hasErrors = true;
      } else {
        const strikes = parseInt(scoreData.strikes);
        if (isNaN(strikes) || strikes < 0 || strikes > 12) {
          const strikesInput = row.querySelector('[data-field="strikes"]');
          if (strikesInput) strikesInput.classList.add('is-invalid');
          errorMessages.push('Strikes must be between 0 and 12');
          hasErrors = true;
        }
      }
      
      // Validate Spares (Required)
      if (!scoreData.spares || scoreData.spares === '') {
        const sparesInput = row.querySelector('[data-field="spares"]');
        if (sparesInput) sparesInput.classList.add('is-invalid');
        errorMessages.push('Spares is required');
        hasErrors = true;
      } else {
        const spares = parseInt(scoreData.spares);
        if (isNaN(spares) || spares < 0 || spares > 10) {
          const sparesInput = row.querySelector('[data-field="spares"]');
          if (sparesInput) sparesInput.classList.add('is-invalid');
          errorMessages.push('Spares must be between 0 and 10');
          hasErrors = true;
        }
      }
      
      // Validate Open Frames (Required)
      if (!scoreData.open_frames || scoreData.open_frames === '') {
        const openFramesInput = row.querySelector('[data-field="open_frames"]');
        if (openFramesInput) openFramesInput.classList.add('is-invalid');
        errorMessages.push('Open Frames is required');
        hasErrors = true;
      } else {
        const openFrames = parseInt(scoreData.open_frames);
        if (isNaN(openFrames) || openFrames < 0 || openFrames > 10) {
          const openFramesInput = row.querySelector('[data-field="open_frames"]');
          if (openFramesInput) openFramesInput.classList.add('is-invalid');
          errorMessages.push('Open Frames must be between 0 and 10');
          hasErrors = true;
        }
      }
      
      // Logical validation: Check frame totals
      // Note: Total can be up to 12 due to bonus balls in 10th frame
      // Perfect game = 12 strikes (9 frames + 3 balls in 10th frame)
      if (!hasErrors) {
        const score = parseInt(scoreData.player_score) || 0;
        const strikes = parseInt(scoreData.strikes) || 0;
        const spares = parseInt(scoreData.spares) || 0;
        const openFrames = parseInt(scoreData.open_frames) || 0;
        const total = strikes + spares + openFrames;
        
        // Total should be between 10-12 (accounting for 10th frame bonus balls)
        if (total < 10) {
          errorMessages.push('Total frames must equal 10 (Strikes + Spares + Open Frames)');
          hasErrors = true;
          row.querySelector('[data-field="strikes"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="spares"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="open_frames"]')?.classList.add('is-invalid');
        } else if (total > 12) {
          errorMessages.push('Total frames cannot exceed 12 (max with 10th frame bonus)');
          hasErrors = true;
          row.querySelector('[data-field="strikes"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="spares"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="open_frames"]')?.classList.add('is-invalid');
        }
        
        // Score-based validation: Perfect game logic
        if (score === 300) {
          if (strikes !== 12 || spares !== 0 || openFrames !== 0) {
            errorMessages.push('A perfect 300 game must have exactly 12 strikes, 0 spares, and 0 open frames');
            hasErrors = true;
            row.querySelector('[data-field="strikes"]')?.classList.add('is-invalid');
            row.querySelector('[data-field="spares"]')?.classList.add('is-invalid');
            row.querySelector('[data-field="open_frames"]')?.classList.add('is-invalid');
          }
        }
        
        // Score-based validation: Maximum possible with spares or opens
        if (score >= 290 && (spares > 0 || openFrames > 0)) {
          errorMessages.push('Scores 290+ require all strikes (12 strikes, 0 spares, 0 open frames)');
          hasErrors = true;
          row.querySelector('[data-field="strikes"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="spares"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="open_frames"]')?.classList.add('is-invalid');
        }
        
        // Logical check: Cannot have more strikes than the score allows
        if (strikes === 12 && score < 300) {
          errorMessages.push('12 strikes (perfect game) must result in a score of 300');
          hasErrors = true;
          row.querySelector('[data-field="score"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="strikes"]')?.classList.add('is-invalid');
        }
        
        // Logical check: If all open frames, score should be low
        if (openFrames === 10 && score > 90) {
          errorMessages.push('All open frames cannot result in such a high score');
          hasErrors = true;
          row.querySelector('[data-field="score"]')?.classList.add('is-invalid');
          row.querySelector('[data-field="open_frames"]')?.classList.add('is-invalid');
        }
      }
      
      if (hasErrors) {
        const errorMsg = errorMessages.join('<br>');
        showNotification(errorMsg, 'error');
        ongoingSubmissions.delete(submissionKey);
        isSavingScore = false;
        return;
      }
      
      // Show loading on the specific save button
      const saveBtn = row.querySelector(`[onclick*="savePlayerScore(${userId}, ${gameNumber}"]`);
      
      if (!saveBtn) {
        showNotification('Save button not found', 'error');
        ongoingSubmissions.delete(submissionKey);
        return;
      }
      
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Saving...';
      saveBtn.disabled = true;
      saveBtn.classList.add('btn-loading');
      
      // Send single score
      const formData = new FormData();
      formData.append('action', 'add_score');
      // Use the session_id from the selected date, fallback to PHP session_id
      const sessionId = window.currentSessionId || <?php echo $sessionId ? $sessionId : 'null'; ?>;
      console.log('Using session_id for save:', sessionId);
      console.log('window.currentSessionId:', window.currentSessionId);
      formData.append('session_id', sessionId);
      formData.append('user_id', userId);
      formData.append('game_number', gameNumber);
      formData.append('player_score', scoreData.player_score);
      formData.append('strikes', scoreData.strikes || 0);
      formData.append('spares', scoreData.spares || 0);
      formData.append('open_frames', scoreData.open_frames || 0);
      formData.append('game_mode', 'Solo');
      
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
            // Check if this was an update or a new save
            const isUpdate = saveBtn.innerHTML.includes('Update');
            const actionText = isUpdate ? 'updated' : 'saved';
            
            showNotification(`Score ${actionText} for ${playerName}: ${scoreData.player_score}`, 'success');
            
            // Clear the cache to force fresh data from database
            const dateFilter = document.getElementById('dateFilter');
            const selectedDate = dateFilter ? dateFilter.value : 'today';
            if (window.dataCache && window.dataCache[selectedDate]) {
              delete window.dataCache[selectedDate];
            }
            
            // Wait a bit longer for database to update, then do full refresh
            // This ensures the status shows as "Completed" and rankings are correct
            setTimeout(() => {
              loadDataForDateFilter(selectedDate);
            }, 800);
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
        // Always clean up the ongoing submission
        ongoingSubmissions.delete(submissionKey);
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        saveBtn.classList.remove('btn-loading');
        
        // Reset the saving flag after a short delay
        setTimeout(() => {
          isSavingScore = false;
        }, 1000);
      });
    }

    function updatePlayerStatus(row, score, strikes, spares, openFrames) {
      // Update the status column to show "Completed"
      // Table structure: Player (1), Lane (2), Score (3), Strikes (4), Spares (5), Open Frames (6), Status (7), Actions (8)
      const statusCell = row.querySelector('td:nth-child(7)');
      if (statusCell) {
        statusCell.innerHTML = `
          <span class="badge bg-success">Completed</span>
          <br><small class="text-muted">${new Date().toLocaleTimeString()}</small>
        `;
      } else {
        console.error('Status cell not found in row');
      }
      
      // Change the save button to an "Update" button
      const saveBtn = row.querySelector('button[onclick*="savePlayerScore"]');
      if (saveBtn) {
        saveBtn.innerHTML = '<i class="ti ti-pencil me-1"></i>Update';
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-warning');
        // Button remains enabled so user can update the score
      } else {
        console.error('Save button not found in row');
      }
      
      // Update the Overall Rankings tab if it's visible
      updateOverallRankings();
    }

    function updateRowDataOnly(row, scoreData, gameNumber) {
      // This function updates only the specific row data without refreshing the entire table
      // It preserves the current layout and only updates the necessary fields
      
      try {
        // Get the user ID from the row
        const userId = row.querySelector('[data-user-id]')?.getAttribute('data-user-id');
        if (!userId) return;
        
        // Update the input values to reflect the saved data
        const scoreInput = row.querySelector('[data-field="score"]');
        const strikesInput = row.querySelector('[data-field="strikes"]');
        const sparesInput = row.querySelector('[data-field="spares"]');
        const openFramesInput = row.querySelector('[data-field="open_frames"]');
        
        if (scoreInput) scoreInput.value = scoreData.player_score;
        if (strikesInput) strikesInput.value = scoreData.strikes || 0;
        if (sparesInput) sparesInput.value = scoreData.spares || 0;
        if (openFramesInput) openFramesInput.value = scoreData.open_frames || 0;
        
        // Add a subtle highlight to show the row was updated
        row.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        setTimeout(() => {
          row.style.backgroundColor = '';
        }, 2000);
        
        console.log(`Row data updated for user ${userId} in game ${gameNumber}`);
        
      } catch (error) {
        console.error('Error updating row data:', error);
      }
    }

    function updateOverallRankings() {
      // This function updates the Overall Rankings tab without disrupting the layout
      const overallTab = document.getElementById('overall-tab');
      if (overallTab && overallTab.classList.contains('active')) {
        // Only update if we're on the overall tab and it's visible
        console.log('Overall rankings tab is active - updating rankings');
        
        // Add a subtle indicator that rankings have been updated
        const overallTable = document.getElementById('overallRankingsTable');
        if (overallTable) {
          overallTable.style.borderLeft = '3px solid #28a745';
          setTimeout(() => {
            overallTable.style.borderLeft = '';
          }, 2000);
        }
      }
    }

    function refreshCurrentTabData() {
      // Get the currently selected date
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      
      console.log('Auto-refreshing data for date:', selectedDate);
      
      // Show a subtle loading indicator without disrupting the layout
      const activeTab = document.querySelector('.nav-link.active');
      if (activeTab) {
        const originalText = activeTab.innerHTML;
        activeTab.innerHTML = originalText + ' <i class="ti ti-loader ti-spin"></i>';
        
        // Remove loading indicator after refresh
        setTimeout(() => {
          activeTab.innerHTML = originalText;
        }, 1500);
      }
      
      // Show a subtle notification
      showNotification('Refreshing data...', 'info');
      
      // Clear cache to force fresh data
      delete dataCache[selectedDate];
      
      // Use a more stable refresh method that doesn't clear the tables
      refreshDataStable(selectedDate, true);
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
      const sessionId = window.currentSessionId || <?php echo $sessionId ? $sessionId : 'null'; ?>;
      console.log('Using session_id for saveAllScores:', sessionId);
      console.log('window.currentSessionId:', window.currentSessionId);
      formData.append('session_id', sessionId);
      formData.append('scores', JSON.stringify(scoresToSave));
      formData.append('game_mode', 'Solo');
      
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
          
          // Update the saved rows without full table refresh
          scoresToSave.forEach(scoreData => {
            const row = table.querySelector(`tr [data-user-id="${scoreData.user_id}"]`).closest('tr');
            if (row) {
              updateRowDataOnly(row, scoreData, gameNumber);
            }
          });
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

    // Date filter functionality
    // Date filter change handler
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
      console.log('Date filter changed to:', selectedDate);
      showNotification('Loading data for ' + selectedDate + '...', 'info');
      
      // Simple approach - just load data for the selected date
      loadDataForDateFilter(selectedDate);
    });
    
    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      console.log('Page loaded, loading initial data for:', selectedDate);
      loadDataForDateFilter(selectedDate);
    });



    // Tab switching with data loading simulation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        
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
      // Check if countdown elements exist before trying to update them
      const daysEl = document.getElementById('days');
      const hoursEl = document.getElementById('hours');
      const minutesEl = document.getElementById('minutes');
      const secondsEl = document.getElementById('seconds');
      
      // If countdown elements don't exist, skip the countdown update
      if (!daysEl || !hoursEl || !minutesEl || !secondsEl) {
        return;
      }
      
      const now = new Date().getTime();
      const distance = targetDate - now;
      
      if (distance < 0) {
        // Event has passed
        daysEl.innerHTML = '00';
        hoursEl.innerHTML = '00';
        minutesEl.innerHTML = '00';
        secondsEl.innerHTML = '00';
        return;
      }
      
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      
      daysEl.innerHTML = days.toString().padStart(2, '0');
      hoursEl.innerHTML = hours.toString().padStart(2, '0');
      minutesEl.innerHTML = minutes.toString().padStart(2, '0');
      secondsEl.innerHTML = seconds.toString().padStart(2, '0');
    }
    
    // Update countdown every second
    setInterval(updateCountdown, 1000);
    
    // Initial call
    updateCountdown();


    function refreshScores() {
      location.reload();
    }

  </script>


  <script>
    
    // Cache for loaded data
    const dataCache = {};
    window.dataCache = dataCache; // Make accessible globally for cache clearing
    
    // Flag to prevent unnecessary refreshes during save operations
    let isSavingScore = false;
    
    function refreshDataStable(selectedDate, forceRefresh = false) {
      console.log('=== REFRESH DATA STABLE ===');
      console.log('Selected date:', selectedDate);
      console.log('Force refresh:', forceRefresh);
      console.log('isSavingScore flag:', isSavingScore);
      
      // Don't refresh if we're currently saving a score, unless forced
      if (isSavingScore && !forceRefresh) {
        console.log('Skipping refresh - score save in progress');
        return;
      }
      
      console.log('Proceeding with refresh...');
      
      // Set global flag for All Time view
      window.isAllTimeView = (selectedDate === 'all');
      
      // Simple AJAX request without clearing tables
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/session-management.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              if (data.success) {
                dataCache[selectedDate] = {
                  players: data.players,
                  session_id: data.session_id
                };
                // Store the session_id for this date
                if (data.session_id) {
                  window.currentSessionId = data.session_id;
                  console.log('Session ID for date', selectedDate, ':', data.session_id);
                }
                // Use gentle update to preserve table structure
                updateTablesGentle(data.players, selectedDate);
                
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
      
      xhr.send('action=get_players_data&selected_date=' + encodeURIComponent(selectedDate) + '&session_type=Solo&t=' + Date.now());
    }
    
    function loadDataForDateFilter(selectedDate) {
      // Set global flag for All Time view
      window.isAllTimeView = (selectedDate === 'all');
      
      // Clear cache to force fresh data load (temporary fix for score contamination)
      if (dataCache[selectedDate]) {
        delete dataCache[selectedDate];
      }
      
      // Always fetch fresh data for now
      // TODO: Implement smarter cache invalidation later
      
      // Show loading state without clearing existing data
      const tables = document.querySelectorAll('.table tbody');
      tables.forEach(table => {
        // Only show loading if table is empty or has loading message
        if (table.children.length === 0 || table.innerHTML.includes('Loading...')) {
        table.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>';
        }
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
                console.log('=== DATA LOADED FOR DATE:', selectedDate, '===');
                console.log('Players loaded:', data.players.length);
                console.log('Sample player data:', data.players[0]);
                
                dataCache[selectedDate] = {
                  players: data.players,
                  session_id: data.session_id
                };
                // Store the session_id for this date
                if (data.session_id) {
                  window.currentSessionId = data.session_id;
                  console.log('Session ID for date', selectedDate, ':', data.session_id);
                }
                // Use gentle update to preserve table structure
                updateTablesGentle(data.players, selectedDate);
                
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
      
      xhr.send('action=get_players_data&selected_date=' + encodeURIComponent(selectedDate) + '&session_type=Solo&t=' + Date.now());
    }
    
    function updateTablesWithData(players, selectedDate = 'today') {
      // Set global flag for All Time view
      window.isAllTimeView = (selectedDate === 'all');
      
      // Store current data globally for access by other functions
      window.currentData = players;
      
      // Update table headers based on view type
      updateTableHeaders(selectedDate);
      
      // Use the gentle update method that includes dynamic lanes
      updateTablesGentle(players, selectedDate);
      
      // Show/hide Save buttons based on view type
      const saveButtons = document.querySelectorAll('.save-all-btn');
      saveButtons.forEach(btn => {
        if (window.isAllTimeView) {
          btn.style.display = 'none';
        } else {
          btn.style.display = 'inline-block';
        }
      });
    }
    
    function updateTableHeaders(selectedDate) {
      const isAllTime = (selectedDate === 'all');
      
      // Update Overall Rankings table header
      const totalScoreHeader = document.querySelector('#overallRankingsTable thead th:nth-child(2)');
      if (totalScoreHeader) {
        totalScoreHeader.textContent = isAllTime ? 'Average Score' : 'Total Score';
      }
    }
    
    function updateOverallRankingsTable(players) {
      
      const tbody = document.querySelector('#overallRankingsTable tbody');
      if (!tbody) {
        return;
      }
      
      // Get available lanes for the current session
      getAvailableLanesForDropdown().then(availableLanes => {
      let html = '';
      
      players.forEach(player => {
        // Always show the actual data from the server - don't hide scores
        const totalScore = player.total_score || 0;
        const avgScore = player.avg_score || 0;
        const gamesPlayed = player.games_played || 0;
        const bestScore = player.best_score || 0;
        const totalStrikes = player.total_strikes || 0;
        const totalSpares = player.total_spares || 0;
        const lastUpdated = player.last_updated || 'Never';
        
        html += `
          <tr data-player-id="${player.user_id}">
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(player.user_id % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                  <small class="text-muted">${player.user_role}</small>
                </div>
              </div>
            </td>
            <td>
              <select class="form-select form-select-sm lane-selector" data-user-id="${player.user_id}" onchange="updatePlayerLane(${player.user_id}, this.value)" style="min-width: 80px;">
                <option value="">Select</option>
                ${availableLanes.map(lane => `<option value="${lane}" ${player.lane_number == lane ? 'selected' : ''}>Lane ${lane}</option>`).join('')}
              </select>
            </td>
            <td><span class="fw-bold text-success">${totalScore}</span></td>
            <td><span class="fw-bold text-primary">${avgScore}</span></td>
            <td>${gamesPlayed}</td>
            <td><span class="badge bg-info">${bestScore > 0 ? bestScore : '-'}</span></td>
            <td><span class="badge bg-warning">${totalStrikes}</span></td>
            <td><span class="badge bg-secondary">${totalSpares}</span></td>
            <td><span class="badge bg-success">Active</span></td>
            <td><small class="text-muted">${lastUpdated}</small></td>
            <td>
              <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewPlayerDetails(${player.user_id})" title="View Details">
                  <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning me-1" onclick="editPlayerScore(${player.user_id})" title="Edit Scores">
                  <i class="ti ti-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewPlayerHistory(${player.user_id})" title="View History">
                  <i class="ti ti-history"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      
      tbody.innerHTML = html || '<tr><td colspan="10" class="text-center text-muted py-4">No VipersVenoms data available for selected date range</td></tr>';
      }).catch(error => {
        console.error('Error getting available lanes:', error);
        // Fallback to default lanes 1-12 if there's an error
        const defaultLanes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        let html = '';
        
        players.forEach(player => {
        // Always show the actual data from the server - don't hide scores
        const totalScore = player.total_score || 0;
        const avgScore = player.avg_score || 0;
        const gamesPlayed = player.games_played || 0;
        const bestScore = player.best_score || 0;
        const totalStrikes = player.total_strikes || 0;
        const totalSpares = player.total_spares || 0;
        const lastUpdated = player.last_updated || 'Never';
        
        html += `
          <tr data-player-id="${player.user_id}">
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(player.user_id % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                  <small class="text-muted">${player.user_role}</small>
                </div>
              </div>
            </td>
            <td>
              <select class="form-select form-select-sm lane-selector" data-user-id="${player.user_id}" onchange="updatePlayerLane(${player.user_id}, this.value)" style="min-width: 80px;">
                <option value="">Select</option>
                ${defaultLanes.map(lane => `<option value="${lane}" ${player.lane_number == lane ? 'selected' : ''}>Lane ${lane}</option>`).join('')}
              </select>
            </td>
            <td><span class="fw-bold text-success">${totalScore}</span></td>
            <td><span class="fw-bold text-primary">${avgScore}</span></td>
            <td>${gamesPlayed}</td>
            <td><span class="badge bg-info">${bestScore > 0 ? bestScore : '-'}</span></td>
            <td><span class="badge bg-warning">${totalStrikes}</span></td>
            <td><span class="badge bg-secondary">${totalSpares}</span></td>
            <td><span class="badge bg-success">Active</span></td>
            <td><small class="text-muted">${lastUpdated}</small></td>
            <td>
              <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewPlayerDetails(${player.user_id})" title="View Details">
                  <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning me-1" onclick="editPlayerScore(${player.user_id})" title="Edit Scores">
                  <i class="ti ti-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewPlayerHistory(${player.user_id})" title="View History">
                  <i class="ti ti-history"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
        });
        
        tbody.innerHTML = html || '<tr><td colspan="10" class="text-center text-muted py-4">No VipersVenoms data available for selected date range</td></tr>';
      });
    }


    function getAvailableLanesForDropdown() {
      return new Promise((resolve, reject) => {
        // Get current session ID
        const dateFilter = document.getElementById('dateFilter');
        const selectedDate = dateFilter ? dateFilter.value : 'today';
        
        // For 'all' date, use default lanes
        if (selectedDate === 'all') {
          resolve([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
          return;
        }
        
        // Get session ID for the selected date
        const formData = new FormData();
        formData.append('action', 'get_players_data');
        formData.append('selected_date', selectedDate);
        formData.append('session_type', 'Solo');
        
        fetch('ajax/session-management.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success && data.session_id) {
            // Get available lanes for this session
            const laneFormData = new FormData();
            laneFormData.append('action', 'get_available_lanes');
            laneFormData.append('session_id', data.session_id);
            
            return fetch('ajax/session-management.php', {
              method: 'POST',
              body: laneFormData
            });
          } else {
            // No session found, use default lanes
            resolve([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
            return;
          }
        })
        .then(response => response.json())
        .then(laneData => {
          if (laneData.success && laneData.lanes) {
            resolve(laneData.lanes);
          } else {
            // Fallback to default lanes
            resolve([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
          }
        })
        .catch(error => {
          console.error('Error fetching available lanes:', error);
          // Fallback to default lanes
          resolve([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
        });
      });
    }
    
    function updateGameTable(gameNumber, players, selectedDate = 'today') {
      const tbody = document.querySelector(`#game${gameNumber}Table tbody`);
      if (!tbody) return;
      
      const isAllTime = (selectedDate === 'all');
      let html = '';
      
      players.forEach(player => {
        const gameScore = player[`game_${gameNumber}_score`] || null;
        const score = gameScore ? gameScore.player_score : '';
        const strikes = gameScore ? gameScore.strikes : '';
        const spares = gameScore ? gameScore.spares : '';
        const openFrames = gameScore ? gameScore.open_frames : '';
        const createdAt = gameScore ? gameScore.created_at : '';
        
        // Calculate average scores for All Time view
        const avgScore = player.avg_score || 0;
        const gamesPlayed = player.games_played || 0;
        const bestScore = player.best_score || 0;
        
        html += `
          <tr data-player-id="${player.user_id}" data-game="${gameNumber}" style="transition: all 0.3s ease;">
            <td>
              <div class="d-flex align-items-center">
                <img src="assets/images/profile/user-${(player.user_id % 8) + 1}.jpg" alt="Player" class="rounded-circle me-2" width="32">
                <div>
                  <strong>${player.first_name} ${player.last_name}</strong>
                  <br><small class="text-muted">${player.team_name || player.user_role}</small>
                </div>
              </div>
            </td>`;
        
        if (isAllTime) {
          // All Time view - show game-specific averages (read-only)
          const gameAvgScore = player[`game_${gameNumber}_avg_score`] || 0;
          const gameAvgStrikes = player[`game_${gameNumber}_avg_strikes`] || 0;
          const gameAvgSpares = player[`game_${gameNumber}_avg_spares`] || 0;
          const gameCount = player[`game_${gameNumber}_count`] || 0;
          
          html += `
            <td>
              <div class="text-center p-2 bg-light rounded">
                <strong class="text-primary fs-5">${gameAvgScore}</strong>
                <br><small class="text-muted">Game ${gameNumber} Avg</small>
              </div>
            </td>
            <td>
              <div class="text-center p-2 bg-light rounded">
                <strong class="text-success">${gameAvgStrikes}</strong>
                <br><small class="text-muted">Avg Strikes</small>
              </div>
            </td>
            <td>
              <div class="text-center p-2 bg-light rounded">
                <strong class="text-warning">${gameAvgSpares}</strong>
                <br><small class="text-muted">Avg Spares</small>
              </div>
            </td>
            <td>
              <div class="text-center p-2 bg-light rounded">
                <strong class="text-info">${gameCount}</strong>
                <br><small class="text-muted">Times Played</small>
              </div>
            </td>
            <td class="text-center">
              <span class="badge bg-info">Game ${gameNumber} History</span>
            </td>
            <td class="text-center">
              <div class="d-flex gap-1 justify-content-center">
                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(${player.user_id})" title="View Player History">
                  <i class="ti ti-chart-line"></i>
                </button>
              </div>
            </td>`;
        } else {
          // Session view - show editable inputs
          // Add Lane column
          html += `
            <td class="text-center">
              <strong class="text-primary">${player.lane_number || '-'}</strong>
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
                     value="${spares}" min="0" max="10" placeholder="0-10">
            </td>
            <td>
              <input type="number" class="form-control form-control-sm score-input" 
                     data-user-id="${player.user_id}" data-field="open_frames" data-game="${gameNumber}"
                     value="${openFrames}" min="0" max="10" placeholder="0-10">
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
            </td>`;
        }
        
        html += `</tr>`;
      });
      
      // Store current table structure to prevent layout shifts
      const currentRows = tbody.querySelectorAll('tr');
      const isFirstLoad = currentRows.length === 0 || (currentRows.length === 1 && currentRows[0].textContent.includes('Loading'));
      
      // Use requestAnimationFrame to ensure smooth updates
      requestAnimationFrame(() => {
      tbody.innerHTML = html || '<tr><td colspan="7" class="text-center text-muted py-4">No data available for selected date range</td></tr>';
        
        // Add a subtle animation to indicate the update
        if (!isFirstLoad && html) {
          const newRows = tbody.querySelectorAll('tr');
          newRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            setTimeout(() => {
              row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
              row.style.opacity = '1';
              row.style.transform = 'translateY(0)';
            }, index * 50);
          });
        }
      });
    }
    
    function refreshTable() {
      const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
      const icon = refreshBtn ? refreshBtn.querySelector('i') : null;
      
      if (icon) {
        icon.classList.add('ti-spin');
      }
      
      const dateFilter = document.getElementById('dateFilter');
      loadDataForDateFilter(dateFilter.value);
      
      // Remove spinning after data loads
      setTimeout(() => {
        if (icon) {
          icon.classList.remove('ti-spin');
        }
        showNotification('Solo table refreshed successfully!', 'success');
      }, 1500);
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
