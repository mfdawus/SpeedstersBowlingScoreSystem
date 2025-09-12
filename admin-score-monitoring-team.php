<?php
require_once 'includes/auth.php';
require_once 'includes/session-management.php';
requireAdmin(); // Ensure only admins can access this page

// Get current user info
$currentUser = getCurrentUser();
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
      margin-right: 10px;
    }
    .team-avatars img {
      margin-left: -5px;
      border: 2px solid white;
    }
    .team-avatars img:first-child {
      margin-left: 0;
    }
    .admin-actions {
      display: flex;
      gap: 5px;
    }
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
                    <li class="breadcrumb-item active">Team</li>
                  </ol>
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
                      <h5 class="card-title fw-semibold mb-1">Team Score Monitoring</h5>
                      <span class="fw-normal text-muted">Admin view with enhanced team management features</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-success btn-sm" onclick="exportTeamData()">
                        <i class="ti ti-file-excel me-1"></i>
                        Export
                      </button>
                      <button class="btn btn-warning btn-sm" onclick="bulkEditTeams()">
                        <i class="ti ti-edit me-1"></i>
                        Bulk Edit
                      </button>
                      <button class="btn btn-primary btn-sm" onclick="teamAnalytics()">
                        <i class="ti ti-chart-line me-1"></i>
                        Analytics
                      </button>
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <?php 
                        // Get available session dates
                        try {
                          $pdo = getDBConnection();
                          
                          // Get session dates with score counts (Team sessions only)
                          $stmt = $pdo->prepare("
                            SELECT 
                              DATE(gs.session_date) as match_date,
                              COUNT(DISTINCT gs.session_id) as session_count,
                              COUNT(gc.score_id) as score_count
                            FROM game_sessions gs
                            LEFT JOIN game_scores gc ON gs.session_id = gc.session_id AND gc.status = 'Completed'
                            WHERE (gs.status = 'Active' OR gs.status = 'Completed') AND gs.game_mode = 'Team'
                            GROUP BY DATE(gs.session_date)
                            ORDER BY gs.session_date DESC
                            LIMIT 20
                          ");
                          $stmt->execute();
                          $sessionDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          // Check for active session first
                          $activeSessionDate = null;
                          $stmt = $pdo->prepare("
                            SELECT DATE(session_date) as match_date
                            FROM game_sessions 
                            WHERE DATE(session_date) = CURDATE() AND status = 'Active' AND game_mode = 'Team'
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
                          <tbody id="overallTableBody">
                            <tr>
                              <td colspan="11" class="text-center py-4">
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
    </div>
  </div>
  
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Team Admin Functions
    function viewTeamDetails(teamName) {
      showNotification('Opening detailed view for team: ' + teamName, 'info');
    }

    function editTeamScore(teamName) {
      showNotification('Opening score editor for team: ' + teamName, 'info');
    }

    function viewTeamHistory(teamName) {
      showNotification('Loading score history for team: ' + teamName, 'info');
    }

    function manageTeamMembers(teamName) {
      showNotification('Opening team management for: ' + teamName, 'info');
    }

    function exportTeamData() {
      showNotification('Exporting team data...', 'info');
    }

    function bulkEditTeams() {
      showNotification('Opening bulk edit interface...', 'info');
    }

    function teamAnalytics() {
      showNotification('Opening team analytics...', 'info');
    }

    // Date filter functionality
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
        console.log('Date filter changed to:', selectedDate);
        showNotification('Loading data for ' + selectedDate + '...', 'info');
      loadDataForDateFilter(selectedDate);
    });

    // Refresh table functionality - removed duplicate function

    // Tab switching with data loading simulation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        console.log('Switched to team tab:', targetId);
        
        if (targetId !== '#overall') {
          const gameNumber = targetId.replace('#game', '');
          showNotification('Loading Game ' + gameNumber + ' team data...', 'info');
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

    // Cache for loaded data
    const dataCache = {};
    
    function loadDataForDateFilter(selectedDate) {
      console.log('loadDataForDateFilter called with:', selectedDate);
      // Check cache first
      if (dataCache[selectedDate]) {
        console.log('Using cached data for:', selectedDate);
        updateTablesWithData(dataCache[selectedDate]);
        return;
      }
      console.log('Fetching fresh data for:', selectedDate);
      
      // Show loading state
      const tables = document.querySelectorAll('.table tbody');
      tables.forEach(table => {
        table.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>';
      });
      
      // AJAX request to get team data
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'ajax/session-management.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          console.log('AJAX response received, status:', xhr.status);
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              console.log('Parsed response data:', data);
              if (data.success) {
                console.log('Data loaded successfully, players count:', data.players ? data.players.length : 0);
                dataCache[selectedDate] = data.players;
                // Store the session_id for this date
                if (data.session_id) {
                  window.currentSessionId = data.session_id;
                  console.log('Session ID for date', selectedDate, ':', data.session_id);
                }
                updateTablesWithData(data.players);
              } else {
                console.log('Server returned error:', data.message);
                showNotification('Error: ' + data.message, 'error');
              }
            } catch (e) {
              console.log('Error parsing response:', e);
              console.log('Raw response:', xhr.responseText);
              showNotification('Error parsing response', 'error');
            }
          } else {
            console.log('HTTP error:', xhr.status);
            showNotification('Error loading data', 'error');
          }
        }
      };
      
      xhr.send('action=get_players_data&selected_date=' + encodeURIComponent(selectedDate) + '&session_type=Team&t=' + Date.now());
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
      const tbody = document.getElementById('overallTableBody');
      if (!tbody) return;
      
      // Group players by team and calculate team statistics
      const teamStats = {};
      players.forEach(player => {
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
            bestScore: 0,
            playerNames: [],
            playerAvatars: ''
          };
        }
        
        teamStats[teamName].totalScore += player.total_score || 0;
        teamStats[teamName].totalGames += player.games_played || 0;
        teamStats[teamName].totalStrikes += player.total_strikes || 0;
        teamStats[teamName].totalSpares += player.total_spares || 0;
        teamStats[teamName].players.push(player);
        teamStats[teamName].playerNames.push(player.first_name + ' ' + player.last_name);
        
        // Generate player avatars
        const avatarNum = (player.user_id % 8) + 1;
        teamStats[teamName].playerAvatars += `<img src="assets/images/profile/user-${avatarNum}.jpg" alt="Player" class="player-avatar">`;
        
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
        const avgScore = team.players.length > 0 ? (team.totalScore / team.players.length).toFixed(1) : 0;
        
        html += `
          <tr>
            <td><span class="rank-badge ${rankClass}">${rank}</span></td>
            <td>
              <div class="d-flex align-items-center">
                <div class="d-flex me-2">${team.playerAvatars}</div>
                <div>
                  <h6 class="mb-0">${team.teamName}</h6>
                  <small class="text-muted">Team</small>
                </div>
              </div>
            </td>
            <td>${team.playerNames.join(', ')}</td>
            <td><span class="fw-bold text-success">${team.totalScore}</span></td>
            <td><span class="fw-bold text-primary">${avgScore}</span></td>
            <td>${team.totalGames}</td>
            <td><span class="badge bg-info">${team.bestScore > 0 ? team.bestScore : '-'}</span></td>
            <td>${team.totalStrikes}</td>
            <td>${team.totalSpares}</td>
            <td><span class="badge bg-success">Active</span></td>
            <td><small class="text-muted">Never</small></td>
            <td>
              <div class="admin-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="viewTeamDetails('${team.teamName}')" title="View Details">
                  <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="editTeamScore('${team.teamName}')" title="Edit Score">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewTeamHistory('${team.teamName}')" title="View History">
                  <i class="ti ti-history"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="manageTeamMembers('${team.teamName}')" title="Manage Team">
                  <i class="ti ti-users"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      
      tbody.innerHTML = html || '<tr><td colspan="11" class="text-center text-muted py-4">No team data available for selected date range</td></tr>';
    }
    
    function updateGameTable(gameNumber, players) {
      const tbody = document.getElementById(`game${gameNumber}TableBody`);
      if (!tbody) return;
      
      let html = '';
      
      players.forEach((player, index) => {
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
      
      tbody.innerHTML = html || '<tr><td colspan="8" class="text-center text-muted py-4">No player data available for selected date range</td></tr>';
    }
    
    function refreshTable() {
      console.log('refreshTable() called');
      const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
      const icon = refreshBtn ? refreshBtn.querySelector('i') : null;
      
      if (icon) {
        icon.classList.add('ti-spin');
      }
      
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      console.log('Refreshing with date:', selectedDate);
      
      loadDataForDateFilter(selectedDate);
      
      // Remove spinning after data loads
      setTimeout(() => {
        if (icon) {
          icon.classList.remove('ti-spin');
        }
        showNotification('Team table refreshed successfully!', 'success');
        console.log('Refresh completed');
      }, 1500);
    }
    
    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
      const dateFilter = document.getElementById('dateFilter');
      const selectedDate = dateFilter ? dateFilter.value : 'today';
      loadDataForDateFilter(selectedDate);
    });

    // Auto-refresh every 30 seconds
    setInterval(() => {
      if (!document.hidden) {
        console.log('Auto-refreshing team table...');
      }
    }, 30000);

    // Session Management Functions
    const ongoingSubmissions = new Set();
    
    function savePlayerScore(userId, gameNumber, playerName) {
      console.log('savePlayerScore called:', {userId, gameNumber, playerName});
      
      const submissionKey = `${userId}-${gameNumber}`;
      
      if (ongoingSubmissions.has(submissionKey)) {
        console.log('Submission already in progress for:', submissionKey);
        showNotification('Score is already being saved, please wait...', 'warning');
        return;
      }
      
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
      
      if (hasErrors) {
        showNotification('Please fix invalid score (0-300)', 'error');
        ongoingSubmissions.delete(submissionKey);
        return;
      }
      
      if (!scoreData.player_score) {
        showNotification('Please enter a score for ' + playerName, 'warning');
        ongoingSubmissions.delete(submissionKey);
        return;
      }
      
      const saveBtn = row.querySelector(`[onclick*="savePlayerScore(${userId}, ${gameNumber}"]`);
      
      if (!saveBtn) {
        showNotification('Save button not found', 'error');
        ongoingSubmissions.delete(submissionKey);
        return;
      }
      
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="ti ti-loader me-1"></i>Saving...';
      saveBtn.disabled = true;
      
      const formData = new FormData();
      formData.append('action', 'add_score');
      formData.append('session_id', window.currentSessionId || null);
      formData.append('user_id', userId);
      formData.append('game_number', gameNumber);
      formData.append('player_score', scoreData.player_score);
      formData.append('strikes', scoreData.strikes || 0);
      formData.append('spares', scoreData.spares || 0);
      formData.append('open_frames', scoreData.open_frames || 0);
      formData.append('game_mode', 'Team');
      
      console.log('Sending data:', {
        action: 'add_score',
        session_id: window.currentSessionId || null,
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
        console.log('Response headers:', response.headers);
        return response.text();
      })
      .then(text => {
        console.log('Raw response:', text);
        try {
          const data = JSON.parse(text);
          console.log('Parsed data:', data);
          if (data.success) {
            showNotification(`Score saved for ${playerName}: ${scoreData.player_score}`, 'success');
            updatePlayerStatus(row, scoreData.player_score, scoreData.strikes, scoreData.spares, scoreData.open_frames);
            // Clear cache to force fresh data fetch
            const dateFilter = document.getElementById('dateFilter');
            const selectedDate = dateFilter ? dateFilter.value : 'today';
            delete dataCache[selectedDate];
            console.log('Cache cleared for date:', selectedDate);
            setTimeout(() => {
              refreshTable();
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
        ongoingSubmissions.delete(submissionKey);
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
      });
    }

    function updatePlayerStatus(row, score, strikes, spares, openFrames) {
      const statusCell = row.querySelector('td:nth-last-child(2)');
      if (statusCell) {
        statusCell.innerHTML = `
          <span class="badge bg-success">Completed</span>
          <br><small class="text-muted">${new Date().toLocaleTimeString()}</small>
        `;
      }
      
      const saveBtn = row.querySelector('button[onclick*="savePlayerScore"]');
      if (saveBtn) {
        saveBtn.innerHTML = '<i class="ti ti-check me-1"></i>Saved';
        saveBtn.disabled = true;
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-outline-success');
      }
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
      
      const saveBtn = document.querySelector(`[onclick="saveAllScores(${gameNumber})"]`);
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="ti ti-loader me-1"></i>Saving...';
      saveBtn.disabled = true;
      
      const formData = new FormData();
      formData.append('action', 'save_multiple_scores');
      formData.append('session_id', window.currentSessionId || null);
      formData.append('scores', JSON.stringify(scoresToSave));
      formData.append('game_mode', 'Team');
      
      fetch('ajax/session-management.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(`Saved ${scoresToSave.length} scores for Game ${gameNumber}`, 'success');
          // Clear cache to force fresh data fetch
          const dateFilter = document.getElementById('dateFilter');
          const selectedDate = dateFilter ? dateFilter.value : 'today';
          delete dataCache[selectedDate];
          console.log('Cache cleared for date:', selectedDate);
          setTimeout(() => {
            refreshTable();
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
  </script>
</body>

</html>
