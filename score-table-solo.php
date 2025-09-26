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
  <title>Solo Score Table - SPEEDSTERS Bowling System</title>
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
<?php include 'includes/app-topstrip.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <?php include 'includes/header.php'; ?>
      
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
                    <li class="breadcrumb-item active">Solo Players</li>
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
                      <span class="fw-normal text-muted">Solo Players Score Table</span>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <?php 
                        // Get available session dates
                        try {
                          $pdo = getDBConnection();
                          
                          // Get session dates with score counts (Solo sessions only)
                          $stmt = $pdo->prepare("
                            SELECT 
                              DATE(gs.session_date) as match_date,
                              COUNT(DISTINCT gs.session_id) as session_count,
                              COUNT(gc.score_id) as score_count
                            FROM game_sessions gs
                            LEFT JOIN game_scores gc ON gs.session_id = gc.session_id AND gc.status = 'Completed'
                            WHERE (gs.status = 'Active' OR gs.status = 'Completed') AND gs.game_mode = 'Solo'
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
                            WHERE DATE(session_date) = CURDATE() AND status = 'Active' AND game_mode = 'Solo'
                            ORDER BY started_at DESC
                            LIMIT 1
                          ");
                          $stmt->execute();
                          $activeSession = $stmt->fetch(PDO::FETCH_ASSOC);
                          
                          if ($activeSession) {
                            $activeSessionDate = $activeSession['match_date'];
                            // Find the active session in our dates list
                            $activeDateInfo = null;
                            foreach ($sessionDates as $date) {
                              if ($date['match_date'] === $activeSessionDate) {
                                $activeDateInfo = $date;
                                break;
                              }
                            }
                            
                            if ($activeDateInfo) {
                              $formattedDate = date('M j, Y', strtotime($activeDateInfo['match_date']));
                              $scoreInfo = $activeDateInfo['score_count'] > 0 ? " ({$activeDateInfo['score_count']} scores)" : " (no scores)";
                              echo '<option value="' . $activeDateInfo['match_date'] . '" selected>' . $formattedDate . $scoreInfo . '</option>';
                            }
                          } else {
                            // No active session, select the most recent date
                            if (!empty($sessionDates)) {
                              $mostRecent = $sessionDates[0];
                              $scoreInfo = $mostRecent['score_count'] > 0 ? " ({$mostRecent['score_count']} scores)" : " (no scores)";
                              echo '<option value="' . $mostRecent['match_date'] . '" selected>' . date('M j, Y', strtotime($mostRecent['match_date'])) . $scoreInfo . '</option>';
                            } else {
                              echo '<option value="today" selected>Today</option>';
                            }
                          }
                          
                          // Add other dates (excluding active session if it was already added)
                          foreach ($sessionDates as $date) {
                            if (!$activeSessionDate || $date['match_date'] !== $activeSessionDate) {
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
                              <th scope="col">Lane</th>
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
                          <tbody id="game1TableBody">
                            <tr>
                              <td colspan="7" class="text-center py-4">
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
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game2TableBody">
                            <tr>
                              <td colspan="7" class="text-center py-4">
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
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game3TableBody">
                            <tr>
                              <td colspan="7" class="text-center py-4">
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
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game4TableBody">
                            <tr>
                              <td colspan="7" class="text-center py-4">
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
                              <th scope="col">Score</th>
                              <th scope="col">Strikes</th>
                              <th scope="col">Spares</th>
                              <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody id="game5TableBody">
                            <tr>
                              <td colspan="7" class="text-center py-4">
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
        formData.append('session_type', 'Solo');
        
        const response = await fetch('ajax/session-management.php', {
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
            <td>
              ${player.lane_number ? 
                `Lane ${player.lane_number}` : 
                `-`
              }
            </td>
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
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No data available for Game ${gameNumber}</td></tr>`;
        return;
      }

      // Filter players who have scores for this game
      const gamePlayers = currentData.filter(player => {
        const gameScore = player[`game_${gameNumber}_score`];
        return gameScore && gameScore.player_score > 0;
      });

      if (gamePlayers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No scores available for Game ${gameNumber}</td></tr>`;
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


    // Update all tables
    function updateAllTables() {
      updateOverallTable();
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