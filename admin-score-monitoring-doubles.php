<?php
require_once 'includes/auth.php';
requireAdmin(); // Ensure only admins can access this page

require_once 'database.php';
require_once 'includes/duo-helper.php';

// Setup DB
$pdo = getDBConnection();

// Determine which Doubles session to show (latest by default, or via ?session_id=)
$selectedSessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
$selectedSession   = null;

if ($selectedSessionId > 0) {
  $stmt = $pdo->prepare("
      SELECT session_id, session_name, session_date
      FROM game_sessions
      WHERE session_id = ? AND game_mode = 'Doubles'
      LIMIT 1
  ");
  $stmt->execute([$selectedSessionId]);
  $selectedSession = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

if (!$selectedSession) {
  // Fallback to latest Doubles session
  $stmt = $pdo->prepare("
      SELECT session_id, session_name, session_date
      FROM game_sessions
      WHERE game_mode = 'Doubles'
      ORDER BY session_date DESC
      LIMIT 1
  ");
  $stmt->execute();
  $selectedSession = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  if ($selectedSession) {
    $selectedSessionId = (int)$selectedSession['session_id'];
  }
}

// Load duo teams for this session - USE DIRECT QUERY (more reliable on web servers)
$adminDuoTeams = [];
$playersInLobby = 0;
$totalDuosInDB = 0;
$queryError = null;
$duoCount = 0;

if ($selectedSession) {
  // ALWAYS use direct query (more reliable than helper function on web servers)
  try {
    $directStmt = $pdo->prepare("
      SELECT 
        dt.duo_id,
        dt.duo_name,
        dt.combined_total_score,
        dt.lane_number,
        dt.lane_vote_player1,
        dt.lane_vote_player2,
        dt.status,
        dt.player1_avg,
        dt.player2_avg,
        u1.user_id as player1_id,
        u1.first_name as player1_first_name,
        u1.last_name as player1_last_name,
        COALESCE(u1.profile_picture, '') as player1_picture,
        u2.user_id as player2_id,
        u2.first_name as player2_first_name,
        u2.last_name as player2_last_name,
        COALESCE(u2.profile_picture, '') as player2_picture
      FROM duo_teams dt
      INNER JOIN users u1 ON dt.player1_id = u1.user_id
      INNER JOIN users u2 ON dt.player2_id = u2.user_id
      WHERE dt.session_id = ?
      ORDER BY dt.combined_total_score DESC, dt.duo_name
    ");
    $directStmt->execute([$selectedSessionId]);
    $adminDuoTeams = $directStmt->fetchAll(PDO::FETCH_ASSOC);
    $duoCount = count($adminDuoTeams);
  } catch (PDOException $e) {
    $queryError = $e->getMessage();
    error_log("Error fetching duos: " . $queryError);
    $adminDuoTeams = [];
    $duoCount = 0;
  }
  
  // Check how many players are in lobby but not paired
  try {
    $lobbyStmt = $pdo->prepare("
      SELECT COUNT(*) as count 
      FROM duo_join_lobby 
      WHERE session_id = ? AND is_paired = FALSE
    ");
    $lobbyStmt->execute([$selectedSessionId]);
    $lobbyResult = $lobbyStmt->fetch(PDO::FETCH_ASSOC);
    $playersInLobby = (int)$lobbyResult['count'];
  } catch (PDOException $e) {
    error_log("Error checking lobby: " . $e->getMessage());
  }
  
  // Debug: Count total duo teams in database for this session
  $totalDuosInDB = 0;
  try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM duo_teams WHERE session_id = ?");
    $countStmt->execute([$selectedSessionId]);
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalDuosInDB = (int)$countResult['count'];
  } catch (PDOException $e) {
    error_log("Error counting duos: " . $e->getMessage());
  }
  
  // PERFORMANCE FIX: Fetch ALL scores for ALL duos and ALL games in ONE query
  // Instead of querying inside loops (N+1 problem)
  $allScoresByDuoAndGame = [];
  if (!empty($adminDuoTeams)) {
    try {
      $duoIds = array_column($adminDuoTeams, 'duo_id');
      if (!empty($duoIds)) {
        $placeholders = implode(',', array_fill(0, count($duoIds), '?'));
        $scoresStmt = $pdo->prepare("
          SELECT 
            duo_id,
            game_number,
            COALESCE(SUM(player_score), 0) as team_score,
            COALESCE(SUM(strikes), 0) as total_strikes,
            COALESCE(SUM(spares), 0) as total_spares,
            COALESCE(SUM(open_frames), 0) as total_open_frames,
            MAX(created_at) as last_updated
          FROM game_scores
          WHERE duo_id IN ($placeholders) 
            AND game_number BETWEEN 1 AND 6 
            AND status = 'Completed'
          GROUP BY duo_id, game_number
        ");
        $scoresStmt->execute($duoIds);
        $allScores = $scoresStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize scores by duo_id and game_number for fast lookup
        foreach ($allScores as $score) {
          $duoId = (int)$score['duo_id'];
          $gameNum = (int)$score['game_number'];
          $allScoresByDuoAndGame[$duoId][$gameNum] = $score;
        }
      }
    } catch (PDOException $e) {
      error_log("Error fetching all scores: " . $e->getMessage());
    }
  }
} else {
  $totalDuosInDB = 0;
  $allScoresByDuoAndGame = [];
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Admin - Doubles Teams Score Monitoring - VIPERS VENOMS Bowling System</title>
  <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
  
  <!-- Define functions early so onclick handlers can find them -->
  <script>
    // PHP data for JS - defined early
    const SELECTED_SESSION_ID = <?php echo (int)$selectedSessionId; ?>;
    // BASE_PATH will be declared in includes/header.php (line 201)
    // Don't declare it here to avoid duplicate declaration error
    
    // Initialize placeholder functions immediately to prevent "not defined" errors
    // These will be replaced by full implementations below
    window.saveTeamScore = function(duoId, gameNumber) {
      // Placeholder - will be replaced by full implementation
    };
    
    window.saveAllScores = function(gameNumber) {
      // Placeholder - will be replaced by full implementation
    };
    
    window.refreshTable = function() {
      location.reload();
    };
    
    window.showNotification = function(message, type) {
      // Placeholder - will be replaced by full implementation
    };
    
    window.changeSession = function(sessionId) {
      if (!sessionId) return;
      // Simple redirect - no blocking operations
      window.location.href = 'admin-score-monitoring-doubles.php?session_id=' + encodeURIComponent(sessionId);
    };
    
    window.adminAutoPair = function() {
      alert('Auto-pairing will be available after page loads');
    };
    
    // Simple tab switching - available immediately
    window.switchTab = function(tabId) {
      // Hide all tab panes
      const panes = document.querySelectorAll('.tab-pane');
      for (let i = 0; i < panes.length; i++) {
        panes[i].classList.remove('show', 'active');
        panes[i].style.display = 'none';
        panes[i].style.opacity = '0';
        panes[i].setAttribute('aria-hidden', 'true');
      }
      
      // Remove active from all nav links
      const links = document.querySelectorAll('.nav-link');
      for (let i = 0; i < links.length; i++) {
        links[i].classList.remove('active');
        links[i].setAttribute('aria-selected', 'false');
      }
      
      // Show target tab pane
      const targetPane = document.getElementById(tabId);
      if (targetPane) {
        targetPane.classList.add('show', 'active');
        targetPane.style.display = 'block';
        targetPane.style.opacity = '1';
        targetPane.setAttribute('aria-hidden', 'false');
      }
      
      // Activate corresponding nav link
      const targetLink = document.querySelector('[data-bs-target="#' + tabId + '"]');
      if (targetLink) {
        targetLink.classList.add('active');
        targetLink.setAttribute('aria-selected', 'true');
      }
    };
  </script>
  
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
    .score-input {
      min-width: 80px;
      transition: all 0.2s ease;
    }
    .score-input:focus {
      transform: none;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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
    
    /* Force tab panes to be visible when active */
    .tab-pane.show.active {
      display: block !important;
      opacity: 1 !important;
      visibility: visible !important;
      height: auto !important;
    }
    .tab-pane:not(.show):not(.active) {
      display: none !important;
      opacity: 0 !important;
      visibility: hidden !important;
    }
    /* Override Bootstrap fade transition if it's causing issues */
    .tab-pane.fade {
      transition: opacity 0.15s linear;
    }
    .tab-pane.fade.show {
      opacity: 1 !important;
      display: block !important;
    }
    /* Ensure card content inside tabs is visible */
    .tab-pane.show.active .card,
    .tab-pane.show.active .card-body,
    .tab-pane.show.active .table-responsive,
    .tab-pane.show.active table {
      display: block !important;
      visibility: visible !important;
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
                    <li class="breadcrumb-item active">Doubles Teams</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Session Info Banner -->
          <?php if ($selectedSession): ?>
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info d-flex align-items-center">
                <i class="ti ti-info-circle me-2 fs-4"></i>
                    <div class="flex-grow-1">
                  <strong>Current Session:</strong> <?php echo htmlspecialchars($selectedSession['session_name']); ?> 
                  - <?php echo date('M j, Y', strtotime($selectedSession['session_date'])); ?>
                  <br>
                  <small>
                    🎳 Doubles Mode | 
                    👥 <?php echo count($adminDuoTeams); ?> duo teams
                  </small>
                    </div>
                    </div>
                  </div>
                </div>
          <?php else: ?>
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-warning">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>No Doubles Session Found</strong> - Please create a Doubles session before monitoring scores.
              </div>
            </div>
                    </div>
          <?php endif; ?>

          <!-- Page Content -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Doubles Teams Score Monitoring</h5>
                      <span class="fw-normal text-muted">Enter scores for each duo team by game</span>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="sessionFilter" style="width: auto;" onchange="changeSession(this.value)">
                        <?php 
                        // Get all Doubles sessions
                        try {
                          $stmt = $pdo->prepare("
                            SELECT session_id, session_name, session_date, status
                            FROM game_sessions 
                            WHERE game_mode = 'Doubles'
                            ORDER BY session_date DESC
                            LIMIT 20
                          ");
                          $stmt->execute();
                          $allSessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          foreach ($allSessions as $sess) {
                            $selected = ($sess['session_id'] == $selectedSessionId) ? 'selected' : '';
                            $statusBadge = $sess['status'] == 'Active' ? '🟢' : ($sess['status'] == 'Completed' ? '✅' : '⏳');
                            $formattedDate = date('M j, Y', strtotime($sess['session_date']));
                            echo '<option value="' . $sess['session_id'] . '" ' . $selected . '>';
                            echo $statusBadge . ' ' . htmlspecialchars($sess['session_name']) . ' - ' . $formattedDate;
                            echo '</option>';
                          }
                        } catch (Exception $e) {
                          echo '<option value="">Error loading sessions</option>';
                        }
                        ?>
                      </select>
                      <button class="btn btn-success btn-sm" onclick="adminAutoPair()" title="Auto-assign duos based on balanced grouping">
                        <i class="ti ti-users-group me-1"></i>Auto Pair Duos
                      </button>
                      <button class="btn btn-primary btn-sm" onclick="refreshTable()">
                        <i class="ti ti-refresh"></i>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Game Selection Tabs -->
                  <ul class="nav nav-tabs mb-3" id="gameTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="overall-tab" data-bs-toggle="tab" data-bs-target="#overall" type="button" role="tab" onclick="switchTab('overall'); return false;">
                        Overall Rankings
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game1-tab" data-bs-toggle="tab" data-bs-target="#game1" type="button" role="tab" onclick="switchTab('game1'); return false;">
                        Game 1
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game2-tab" data-bs-toggle="tab" data-bs-target="#game2" type="button" role="tab" onclick="switchTab('game2'); return false;">
                        Game 2
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game3-tab" data-bs-toggle="tab" data-bs-target="#game3" type="button" role="tab" onclick="switchTab('game3'); return false;">
                        Game 3
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game4-tab" data-bs-toggle="tab" data-bs-target="#game4" type="button" role="tab" onclick="switchTab('game4'); return false;">
                        Game 4
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game5-tab" data-bs-toggle="tab" data-bs-target="#game5" type="button" role="tab" onclick="switchTab('game5'); return false;">
                        Game 5
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game6-tab" data-bs-toggle="tab" data-bs-target="#game6" type="button" role="tab" onclick="switchTab('game6'); return false;">
                        Game 6
                      </button>
                    </li>
                  </ul>

                  <div class="tab-content" id="gameTabContent">
                    <!-- Overall Rankings Tab -->
                    <div class="tab-pane fade show active" id="overall" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <h5 class="card-title mb-0">Overall Duo Rankings</h5>
                        </div>
                        <div class="card-body">
                      <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                              <thead class="table-dark">
                            <tr>
                              <th scope="col">Rank</th>
                                  <th scope="col">Duo Team</th>
                              <th scope="col">Players</th>
                                  <th scope="col">Lane</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                                  <th scope="col">Total Strikes</th>
                                  <th scope="col">Total Spares</th>
                              <th scope="col">Status</th>
                                  <th scope="col">Actions</th>
                            </tr>
                          </thead>
                              <tbody id="overallTableBody">
                                <?php if (!empty($adminDuoTeams)): ?>
                                  <?php 
                                  $rank = 1;
                                  foreach ($adminDuoTeams as $duo): 
                                    $basePath = defined('BASE_PATH') ? BASE_PATH : '';
                                    $p1Pic = (!empty($duo['player1_picture']) && $duo['player1_picture'] !== 'default-avatar.png')
                                        ? $basePath . '/uploads/profile_pictures/' . $duo['player1_picture']
                                        : $basePath . '/assets/images/profile/user-' . (($duo['player1_id'] % 8) + 1) . '.jpg';
                                    $p2Pic = (!empty($duo['player2_picture']) && $duo['player2_picture'] !== 'default-avatar.png')
                                        ? $basePath . '/uploads/profile_pictures/' . $duo['player2_picture']
                                        : $basePath . '/assets/images/profile/user-' . (($duo['player2_id'] % 8) + 1) . '.jpg';
                                    
                                    // Get all scores for this duo
                                    $duoScores = getDuoScores($duo['duo_id']);
                                    $totalScore = 0;
                                    $gamesPlayed = 0;
                                    $bestGame = 0;
                                    $totalStrikes = 0;
                                    $totalSpares = 0;
                                    
                                    foreach ($duoScores as $scoreData) {
                                      if (!empty($scoreData['combined_score']) && $scoreData['combined_score'] > 0) {
                                        $totalScore += $scoreData['combined_score'];
                                        $gamesPlayed++;
                                        if ($scoreData['combined_score'] > $bestGame) {
                                          $bestGame = $scoreData['combined_score'];
                                        }
                                        $totalStrikes += $scoreData['total_strikes'];
                                        $totalSpares += $scoreData['total_spares'];
                                      }
                                    }
                                    
                                    $avgScore = $gamesPlayed > 0 ? round($totalScore / $gamesPlayed, 1) : 0;
                                    $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                                  ?>
                                    <tr>
                                      <td><span class="rank-badge <?php echo $rankClass; ?>"><?php echo $rank; ?></span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                          <img src="<?php echo htmlspecialchars($p1Pic); ?>" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                          <img src="<?php echo htmlspecialchars($p2Pic); ?>" alt="Player 2" class="rounded-circle border border-2 border-white" width="32">
                                          <div class="ms-2">
                                            <strong><?php echo htmlspecialchars($duo['duo_name']); ?></strong>
                                  </div>
                                </div>
                              </td>
                                      <td>
                                        <small>
                                          <?php echo htmlspecialchars(trim($duo['player1_first_name'] . ' ' . $duo['player1_last_name'])); ?>
                                          <br>
                                          <?php echo htmlspecialchars(trim($duo['player2_first_name'] . ' ' . $duo['player2_last_name'])); ?>
                                        </small>
                              </td>
                                      <td class="text-center">
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               value="<?php echo $duo['lane_number'] ?: ''; ?>" 
                                               min="1" 
                                               max="10"
                                               style="width: 60px;"
                                               onchange="updateLane(<?php echo $duo['duo_id']; ?>, this.value)"
                                               placeholder="Lane">
                              </td>
                                      <td><span class="fw-bold text-success fs-5"><?php echo $totalScore; ?></span></td>
                                      <td><span class="text-info"><?php echo $avgScore; ?></span></td>
                                      <td><?php echo $gamesPlayed; ?>/6</td>
                                      <td><span class="text-warning fw-bold"><?php echo $bestGame; ?></span></td>
                                      <td><?php echo $totalStrikes; ?></td>
                                      <td><?php echo $totalSpares; ?></td>
                                      <td>
                                        <?php if ($gamesPlayed >= 6): ?>
                                          <span class="badge bg-success">Completed</span>
                                        <?php elseif ($gamesPlayed > 0): ?>
                                          <span class="badge bg-warning">In Progress</span>
                                        <?php else: ?>
                                          <span class="badge bg-secondary">Pending</span>
                                        <?php endif; ?>
                              </td>
                                      <td>
                                        <button class="btn btn-sm btn-primary" onclick="viewDuoDetails(<?php echo $duo['duo_id']; ?>)">
                                          <i class="ti ti-eye"></i> View
                                  </button>
                              </td>
                            </tr>
                                  <?php 
                                    $rank++;
                                  endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                      No duo teams found for this session.
                              </td>
                            </tr>
                                <?php endif; ?>
                          </tbody>
                        </table>
                                  </div>
                                  </div>
                                </div>
                    </div>

                    <?php for ($gameNum = 1; $gameNum <= 6; $gameNum++): ?>
                    <!-- Game <?php echo $gameNum; ?> Tab -->
                    <div class="tab-pane fade" id="game<?php echo $gameNum; ?>" role="tabpanel">
                      <div class="card">
                        <div class="card-header">
                          <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Game <?php echo $gameNum; ?> Score Entry</h5>
                            <button class="btn btn-success btn-sm" onclick="saveAllScores(<?php echo $gameNum; ?>)">
                              <i class="ti ti-device-floppy me-1"></i>Save All Scores
                                  </button>
                                </div>
                      </div>
                        <div class="card-body">
                      <div class="table-responsive">
                            <table class="table table-bordered" id="game<?php echo $gameNum; ?>Table">
                              <thead class="table-dark">
                                <tr>
                                  <th scope="col" style="width: 20%;">Duo Team</th>
                                  <th scope="col" style="width: 15%;">Players</th>
                                  <th scope="col" style="width: 6%;">Lane</th>
                                  <th scope="col" style="width: 10%;">Score</th>
                                  <th scope="col" style="width: 8%;">Strikes</th>
                                  <th scope="col" style="width: 8%;">Spares</th>
                                  <th scope="col" style="width: 10%;">Open Frames</th>
                                  <th scope="col" style="width: 10%;">Status</th>
                                  <th scope="col" style="width: 13%;">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                                <?php if (!empty($adminDuoTeams)): ?>
                                  <?php foreach ($adminDuoTeams as $duo): ?>
                                    <?php
                                      // PERFORMANCE FIX: Use pre-fetched scores instead of querying
                                      $duoId = (int)$duo['duo_id'];
                                      $scoreData = $allScoresByDuoAndGame[$duoId][$gameNum] ?? null;
                                      
                                      // Handle NULL values - if no scores exist, scoreData will be null
                                      if ($scoreData && $scoreData['team_score'] > 0) {
                                        $teamScore = (int)$scoreData['team_score'];
                                        $teamStrikes = (int)$scoreData['total_strikes'];
                                        $teamSpares = (int)$scoreData['total_spares'];
                                        $teamOpenFrames = (int)$scoreData['total_open_frames'];
                                        $hasScores = true;
                                        $lastUpdated = $scoreData['last_updated'];
                                      } else {
                                        $teamScore = '';
                                        $teamStrikes = '';
                                        $teamSpares = '';
                                        $teamOpenFrames = '';
                                        $hasScores = false;
                                        $lastUpdated = null;
                                      }
                                    ?>
                                    <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                            <?php
                                              $basePath = defined('BASE_PATH') ? BASE_PATH : '';
                                              $p1Pic = (!empty($duo['player1_picture']) && $duo['player1_picture'] !== 'default-avatar.png')
                                                ? $basePath . '/uploads/profile_pictures/' . $duo['player1_picture']
                                                : $basePath . '/assets/images/profile/user-' . (($duo['player1_id'] % 8) + 1) . '.jpg';
                                              $p2Pic = (!empty($duo['player2_picture']) && $duo['player2_picture'] !== 'default-avatar.png')
                                                ? $basePath . '/uploads/profile_pictures/' . $duo['player2_picture']
                                                : $basePath . '/assets/images/profile/user-' . (($duo['player2_id'] % 8) + 1) . '.jpg';
                                            ?>
                                            <img src="<?php echo htmlspecialchars($p1Pic); ?>" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                            <img src="<?php echo htmlspecialchars($p2Pic); ?>" alt="Player 2" class="rounded-circle border border-2 border-white" width="32">
                                  </div>
                                  <div>
                                            <strong><?php echo htmlspecialchars($duo['duo_name']); ?></strong>
                                  </div>
                                </div>
                              </td>
                                      <td>
                                        <small>
                                          <?php echo htmlspecialchars(trim($duo['player1_first_name'] . ' ' . $duo['player1_last_name'])); ?>
                                          <br>
                                          <?php echo htmlspecialchars(trim($duo['player2_first_name'] . ' ' . $duo['player2_last_name'])); ?>
                                        </small>
                              </td>
                                      <td class="text-center">
                                        <span class="badge bg-primary">Lane <?php echo $duo['lane_number'] ?: '-'; ?></span>
                              </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-duo-id="<?php echo $duo['duo_id']; ?>" 
                                               data-player1-id="<?php echo $duo['player1_id']; ?>"
                                               data-player2-id="<?php echo $duo['player2_id']; ?>"
                                               data-field="score" 
                                               data-game="<?php echo $gameNum; ?>"
                                               value="<?php echo $hasScores ? $teamScore : ''; ?>" 
                                               min="0" 
                                               max="600" 
                                               placeholder="0-600">
                              </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-duo-id="<?php echo $duo['duo_id']; ?>" 
                                               data-field="strikes" 
                                               data-game="<?php echo $gameNum; ?>"
                                               value="<?php echo $hasScores ? $teamStrikes : ''; ?>" 
                                               min="0" 
                                               max="24" 
                                               placeholder="0-24">
                              </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-duo-id="<?php echo $duo['duo_id']; ?>" 
                                               data-field="spares" 
                                               data-game="<?php echo $gameNum; ?>"
                                               value="<?php echo $hasScores ? $teamSpares : ''; ?>" 
                                               min="0" 
                                               max="20" 
                                               placeholder="0-20">
                              </td>
                                      <td>
                                        <input type="number" 
                                               class="form-control form-control-sm score-input" 
                                               data-duo-id="<?php echo $duo['duo_id']; ?>" 
                                               data-field="open_frames" 
                                               data-game="<?php echo $gameNum; ?>"
                                               value="<?php echo $hasScores ? $teamOpenFrames : ''; ?>" 
                                               min="0" 
                                               max="20" 
                                               placeholder="0-20">
                              </td>
                                      <td class="text-center">
                                        <?php if ($hasScores): ?>
                                          <span class="badge bg-success">Completed</span>
                                          <br><small class="text-muted"><?php echo date('g:i A', strtotime($lastUpdated)); ?></small>
                                        <?php else: ?>
                                          <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                              </td>
                                      <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="saveTeamScore(<?php echo $duo['duo_id']; ?>, <?php echo $gameNum; ?>)" title="Save Team Score">
                                          <i class="ti ti-device-floppy me-1"></i>Save
                                  </button>
                              </td>
                            </tr>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                      <i class="ti ti-users fs-1 mb-3 d-block text-warning"></i>
                                      <h5 class="mb-2">No duo teams found for this session.</h5>
                                      <?php if ($playersInLobby > 0): ?>
                                        <div class="alert alert-info mt-3 mb-3">
                                          <strong><?php echo $playersInLobby; ?> player(s)</strong> are waiting in the lobby to be paired.
                                          <br><strong>Click the "Auto Pair Duos" button above</strong> to create duo teams automatically.
                                  </div>
                                      <?php else: ?>
                                        <p class="mb-0">No players in the lobby. Players need to join the duo session first via the Group Selection page.</p>
                                      <?php endif; ?>
                                      <div class="mt-3">
                                        <small class="text-muted d-block">Session ID: <?php echo $selectedSessionId; ?></small>
                                        <small class="text-muted d-block">Duo Teams Loaded: <?php echo $duoCount; ?> | Total in DB: <?php echo $totalDuosInDB; ?> | Players in Lobby: <?php echo $playersInLobby; ?></small>
                                        <?php if ($queryError): ?>
                                          <small class="text-danger d-block mt-2"><strong>⚠️ Query Error: <?php echo htmlspecialchars($queryError); ?></strong></small>
                                        <?php elseif ($totalDuosInDB > 0 && $duoCount == 0): ?>
                                          <small class="text-danger d-block mt-2"><strong>⚠️ Data exists in database but not loading. Check database connection or refresh page.</strong></small>
                                        <?php endif; ?>
                                </div>
                              </td>
                            </tr>
                                <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                      </div>
                    </div>
                    <?php endfor; ?>
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
    // ===== ADMIN DOUBLES SCORE MONITORING SCRIPT - FULL IMPLEMENTATION =====
    // Last Updated: <?php echo date('Y-m-d H:i:s'); ?>
    // Version: 2.0 - Team-based score entry
    
    // Override placeholder with full implementation
    window.saveTeamScore = function(duoId, gameNumber) {
      
      if (!SELECTED_SESSION_ID) {
        showNotification('No active Doubles session selected.', 'danger');
        return;
      }

      const table = document.getElementById(`game${gameNumber}Table`);
      if (!table) {
        console.error('Table not found:', `game${gameNumber}Table`);
        showNotification('Table not found', 'danger');
        return;
      }
      
      const row = table.querySelector(`input[data-duo-id="${duoId}"][data-game="${gameNumber}"]`).closest('tr');
      if (!row) {
        console.error('Row not found for duo:', duoId);
        showNotification('Row not found', 'danger');
        return;
      }
      
      const scoreInput = row.querySelector('[data-field="score"]');
      const strikesInput = row.querySelector('[data-field="strikes"]');
      const sparesInput = row.querySelector('[data-field="spares"]');
      const openFramesInput = row.querySelector('[data-field="open_frames"]');
      
      const teamScore = parseInt(scoreInput.value || '0', 10);
      const teamStrikes = parseInt(strikesInput.value || '0', 10);
      const teamSpares = parseInt(sparesInput.value || '0', 10);
      const teamOpenFrames = parseInt(openFramesInput.value || '0', 10);
      
      const player1Id = parseInt(scoreInput.getAttribute('data-player1-id'), 10);
      const player2Id = parseInt(scoreInput.getAttribute('data-player2-id'), 10);


      // Validation
      if (!teamScore || teamScore <= 0) {
        showNotification('Please enter a team score.', 'warning');
        return;
      }
      
      if (!player1Id || !player2Id) {
        console.error('Missing player IDs:', player1Id, player2Id);
        showNotification('Missing player information', 'danger');
        return;
      }

      // Split the team score equally between both players (or you can adjust this logic)
      const player1Score = Math.floor(teamScore / 2);
      const player2Score = teamScore - player1Score;
      
      // Split strikes, spares, open frames equally
      const player1Strikes = Math.floor(teamStrikes / 2);
      const player2Strikes = teamStrikes - player1Strikes;
      
      const player1Spares = Math.floor(teamSpares / 2);
      const player2Spares = teamSpares - player1Spares;
      
      const player1OpenFrames = Math.floor(teamOpenFrames / 2);
      const player2OpenFrames = teamOpenFrames - player1OpenFrames;

      const requests = [];
      const basePath = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : (window.BASE_PATH || ''));
      const url = basePath + '/ajax/duo-management.php';

      // Save for player 1
      requests.push(fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'save_duo_score',
          duo_id: duoId,
          user_id: player1Id,
          game_number: gameNumber,
          player_score: player1Score,
          strikes: player1Strikes,
          spares: player1Spares,
          open_frames: player1OpenFrames,
          session_id: SELECTED_SESSION_ID
        })
      }));

      // Save for player 2
      requests.push(fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'save_duo_score',
          duo_id: duoId,
          user_id: player2Id,
          game_number: gameNumber,
          player_score: player2Score,
          strikes: player2Strikes,
          spares: player2Spares,
          open_frames: player2OpenFrames,
          session_id: SELECTED_SESSION_ID
        })
      }));

      Promise.all(requests)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
          const anyError = results.find(r => !r.success);
          if (anyError) {
            console.error('Error saving duo scores:', anyError);
            showNotification(anyError.message || 'Failed to save team score.', 'danger');
            return;
          }

          showNotification('Team score saved successfully!', 'success');
          
          // Update status
          const statusCell = row.querySelector('td:nth-child(8)');
          if (statusCell) {
            statusCell.innerHTML = `
              <span class="badge bg-success">Completed</span>
              <br><small class="text-muted">${new Date().toLocaleTimeString()}</small>
            `;
          }
        })
        .catch(err => {
          console.error('Error in saveTeamScore:', err);
          showNotification('Error while saving team score.', 'danger');
        });
    };

    window.saveAllScores = function(gameNumber) {
      showNotification('Saving all scores for Game ' + gameNumber + '...', 'info');
      // Implement bulk save if needed
    };

    window.refreshTable = function() {
      location.reload();
    };

    // Notification function
    window.showNotification = function(message, type = 'info') {
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
    };

    // Change session
    window.changeSession = function(sessionId) {
      if (sessionId) {
        // Show loading indicator
        showNotification('Loading session data...', 'info');
        // Redirect to new session
        window.location.href = 'admin-score-monitoring-doubles.php?session_id=' + sessionId;
      }
    };

    // Admin manually trigger auto-pairing
    window.adminAutoPair = function() {
      if (!SELECTED_SESSION_ID) {
        showNotification('No session selected', 'warning');
        return;
      }

      if (!confirm('This will automatically pair all unpaired players in the lobby based on balanced grouping (Group A + Group B). Continue?')) {
        return;
      }

      const basePath = (typeof BASE_PATH !== 'undefined' ? BASE_PATH : (window.BASE_PATH || ''));
      const url = basePath + '/ajax/duo-management.php';
      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'admin_force_pair',
          session_id: SELECTED_SESSION_ID
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message || 'Duos paired successfully!', 'success');
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification(data.message || 'Failed to pair duos', 'danger');
        }
      })
      .catch(err => {
        console.error('Error auto-pairing:', err);
        showNotification('Error auto-pairing duos', 'danger');
      });
    };

    // Update lane number for a duo
    window.updateLane = function(duoId, laneNumber) {
      
      if (!laneNumber || laneNumber < 1) {
        showNotification('Please enter a valid lane number', 'warning');
        return;
      }
      
      const url = BASE_PATH + '/ajax/duo-management.php';
      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'update_duo_lane',
          duo_id: duoId,
          lane_number: laneNumber
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Lane updated successfully!', 'success');
        } else {
          showNotification(data.message || 'Failed to update lane', 'danger');
        }
      })
      .catch(err => {
        console.error('Error updating lane:', err);
        showNotification('Error updating lane', 'danger');
      });
    };

    // View duo details (navigate to specific game tabs)
    window.viewDuoDetails = function(duoId) {
      // Switch to Game 1 tab
      const game1Tab = document.getElementById('game1-tab');
      if (game1Tab) {
        game1Tab.click();
        showNotification('Viewing duo details in Game tabs', 'info');
      }
    };

    // Simple tab switching function that always works
    window.switchTab = function(tabId) {
      console.log('Switching to tab:', tabId);
      
      // Hide all tab panes - remove both Bootstrap classes and inline styles
      document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.remove('show', 'active');
        pane.style.display = 'none';
        pane.style.opacity = '0';
      });
      
      // Remove active from all nav links
      document.querySelectorAll('.nav-link').forEach(function(link) {
        link.classList.remove('active');
        link.setAttribute('aria-selected', 'false');
      });
      
      // Show target tab pane
      const targetPane = document.getElementById(tabId);
      if (targetPane) {
        // Add Bootstrap classes
        targetPane.classList.add('show', 'active');
        // Force display with inline style (overrides any CSS)
        targetPane.style.display = 'block';
        targetPane.style.opacity = '1';
        targetPane.setAttribute('aria-hidden', 'false');
        console.log('Tab pane found and shown:', tabId, targetPane);
      } else {
        console.error('Tab pane not found:', tabId);
        // Debug: list all available tab panes
        const allPanes = document.querySelectorAll('.tab-pane');
        console.log('Available tab panes:', Array.from(allPanes).map(p => p.id));
      }
      
      // Activate corresponding nav link
      const targetLink = document.querySelector('[data-bs-target="#' + tabId + '"]');
      if (targetLink) {
        targetLink.classList.add('active');
        targetLink.setAttribute('aria-selected', 'true');
      } else {
        console.error('Tab link not found for:', tabId);
      }
    };

    // Initialize tabs and session filter on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Ensure all game tabs are initially hidden (except overall)
      document.querySelectorAll('.tab-pane').forEach(function(pane) {
        if (pane.id !== 'overall') {
          pane.classList.remove('show', 'active');
          pane.style.display = 'none';
          pane.style.opacity = '0';
          pane.setAttribute('aria-hidden', 'true');
        } else {
          pane.classList.add('show', 'active');
          pane.style.display = 'block';
          pane.style.opacity = '1';
          pane.setAttribute('aria-hidden', 'false');
        }
      });
      
      // Debug: Log all tab panes found
      const allPanes = document.querySelectorAll('.tab-pane');
      console.log('Found tab panes on load:', Array.from(allPanes).map(p => ({
        id: p.id,
        display: p.style.display,
        classes: p.className
      })));
      
      // Make all tabs clickable with simple switching
      document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
          e.preventDefault();
          const targetId = this.getAttribute('data-bs-target');
          if (targetId) {
            // Remove # from targetId
            const tabId = targetId.replace('#', '');
            window.switchTab(tabId);
          }
        });
      });
      
      // Ensure session filter works
      const sessionFilter = document.getElementById('sessionFilter');
      if (sessionFilter) {
        sessionFilter.addEventListener('change', function() {
          const sessionId = this.value;
          if (sessionId) {
            window.location.href = 'admin-score-monitoring-doubles.php?session_id=' + encodeURIComponent(sessionId);
          }
        });
      }
    });

  </script>
  
  <?php include 'includes/admin-popup.php'; ?>
</body>

</html>
