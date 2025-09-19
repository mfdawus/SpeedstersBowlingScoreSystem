<?php
require_once 'includes/auth.php';
require_once 'includes/dashboard.php';
require_once 'includes/session-management.php';
requireAdmin(); // Ensure user is admin

// Get current user info
$currentUser = getCurrentUser();

// Get admin statistics
$adminStats = getAdminStats();
$leaderboard = getLeaderboard(10);
$teamStats = getTeamStats();
$soloPlayersStats = getSoloPlayersStats();
$allPlayersStats = getAllPlayersStats();
$recentActivities = getRecentActivities();

// Get session data
$allSessions = getAllGameSessions();
$activeSession = getActiveSession();

// Debug: Let's see what data we're getting
// echo "<pre>All Players Stats Debug: "; print_r($allPlayersStats); echo "</pre>";
// echo "<pre>Solo Players Stats Debug: "; print_r($soloPlayersStats); echo "</pre>";
// echo "<pre>Team Stats Debug: "; print_r($teamStats); echo "</pre>";
?>
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
    
    /* Enhanced Dashboard Styling */
    .rank-badge {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
      font-size: 1.1rem;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
    }
    .rank-1 { 
      background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
      box-shadow: 0 6px 12px rgba(255, 215, 0, 0.4);
    }
    .rank-2 { 
      background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%);
      box-shadow: 0 6px 12px rgba(192, 192, 192, 0.4);
    }
    .rank-3 { 
      background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%);
      box-shadow: 0 6px 12px rgba(205, 127, 50, 0.4);
    }
    .rank-other { 
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }
    
    .player-avatar-container {
      position: relative;
      display: inline-block;
    }
    
    .player-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
    }
    
    .rank-crown {
      position: absolute;
      top: -8px;
      right: -8px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .rank-crown.rank-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); }
    .rank-crown.rank-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%); }
    .rank-crown.rank-3 { background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%); }
    
    .team-text {
      color: #28a745;
      font-weight: 600;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
    }
    
    .score-display, .stat-display, .best-score-display, .strike-rate-display {
      text-align: center;
      padding: 8px;
      border-radius: 8px;
      background: rgba(248, 249, 250, 0.5);
      transition: all 0.3s ease;
    }
    
    .score-display:hover, .stat-display:hover, .best-score-display:hover, .strike-rate-display:hover {
      background: rgba(248, 249, 250, 0.8);
      transform: translateY(-2px);
    }
    
    .score-highlight {
      font-weight: bold;
      font-size: 1.2rem;
      display: block;
    }
    .score-excellent { color: #28a745; }
    .score-good { color: #17a2b8; }
    .score-average { color: #ffc107; }
    .score-below { color: #dc3545; }
    
    .best-score-badge {
      background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-weight: bold;
      font-size: 0.9rem;
    }
    
    .status-text {
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
    }
    .status-active { 
      color: #28a745;
    }
    .status-inactive { 
      color: #6c757d;
    }
    
    .action-buttons {
      display: flex;
      justify-content: center;
    }
    
    .btn-action {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      transition: all 0.3s ease;
      box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }
    
    .btn-view {
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
      color: white;
    }
    .btn-view:hover {
      background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,123,255,0.3);
    }
    
    .btn-edit {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
      color: white;
    }
    .btn-edit:hover {
      background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(40,167,69,0.3);
    }
    
    /* Table Enhancements */
    .table tbody tr {
      transition: all 0.3s ease;
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .table tbody tr:hover {
      background: rgba(0,123,255,0.05);
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .table thead th {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 2px solid #dee2e6;
      font-weight: 600;
      color: #495057;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
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
                      <h3 class="mb-0 text-white" id="totalPlayersCount"><?php echo isset($adminStats['total_users']) ? $adminStats['total_users'] : '0'; ?></h3>
                      <small class="text-white-50">Active players</small>
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
                      <h6 class="card-title text-white-50 mb-1">Total Games</h6>
                      <h3 class="mb-0 text-white"><?php echo isset($adminStats['total_games']) ? $adminStats['total_games'] : '0'; ?></h3>
                      <small class="text-white-50">Games played</small>
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
                      <h6 class="card-title text-white-50 mb-1">Total Matches</h6>
                      <h3 class="mb-0 text-white"><?php echo isset($adminStats['total_matches']) ? $adminStats['total_matches'] : '0'; ?></h3>
                      <small class="text-white-50">Matches played</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-trophy fs-1 text-white-50"></i>
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
                      <h6 class="card-title text-white-50 mb-1">Players Played Today</h6>
                      <h3 class="mb-0 text-white" id="playersPlayedTodayCount"><?php echo isset($adminStats['players_played_today']) ? $adminStats['players_played_today'] : '0'; ?></h3>
                      <small class="text-white-50">Players with scores today</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-trophy fs-1 text-white-50"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Game Session Management -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-header">
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Weekly Game Session Management</h5>
                      <span class="fw-normal text-muted">Manage solo game sessions and score entry</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                        <i class="ti ti-plus me-1"></i>Create New Session
                      </button>
                      <button class="btn btn-primary btn-sm" onclick="refreshSessions()">
                        <i class="ti ti-refresh"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <?php if ($activeSession): ?>
                    <!-- Active Session -->
                    <div class="alert alert-success d-flex align-items-center mb-4">
                      <i class="ti ti-play-circle me-2 fs-4"></i>
                      <div class="flex-grow-1">
                        <strong>Active Session:</strong> <?php echo htmlspecialchars($activeSession['session_name']); ?>
                        <br>
                        <small>
                          📅 <?php echo date('l, M j, Y', strtotime($activeSession['session_date'])); ?> 
                          ⏰ <?php echo date('g:i A', strtotime($activeSession['session_time'])); ?> 
                          🎳 <?php echo $activeSession['game_mode']; ?> 
                          👥 <?php echo $activeSession['participant_count']; ?>/<?php echo $activeSession['max_players']; ?> registered
                          🏆 <?php echo $activeSession['players_played']; ?> played today
                        </small>
                      </div>
                      <div class="ms-3">
                        <a href="admin-score-monitoring-solo.php?session=<?php echo $activeSession['session_id']; ?>" class="btn btn-warning btn-sm me-2">
                          <i class="ti ti-edit me-1"></i>Enter Scores
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="endSession(<?php echo $activeSession['session_id']; ?>)">
                          <i class="ti ti-stop me-1"></i>End Session
                        </button>
                      </div>
                    </div>
                  <?php else: ?>
                    <!-- No Active Session -->
                    <div class="alert alert-info d-flex align-items-center mb-4">
                      <i class="ti ti-info-circle me-2 fs-4"></i>
                      <div class="flex-grow-1">
                        <strong>No Active Session</strong>
                        <br>
                        <small>Create a new session to start managing solo games</small>
                      </div>
                    </div>
                  <?php endif; ?>

                  <!-- Recent Sessions -->
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Session Name</th>
                          <th>Date & Time</th>
                          <th>Mode</th>
                          <th>Players</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach (array_slice($allSessions, 0, 5) as $session): ?>
                          <tr>
                            <td>
                              <strong><?php echo htmlspecialchars($session['session_name']); ?></strong>
                              <?php if ($session['notes']): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($session['notes']); ?></small>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php echo date('M j, Y', strtotime($session['session_date'])); ?><br>
                              <small class="text-muted"><?php echo date('g:i A', strtotime($session['session_time'])); ?></small>
                            </td>
                            <td><span class="badge bg-primary"><?php echo $session['game_mode']; ?></span></td>
                            <td>
                              <?php echo $session['participant_count']; ?>/<?php echo $session['max_players']; ?> registered
                              <br><small class="text-success"><?php echo $session['players_played'] ?? 0; ?> played</small>
                            </td>
                            <td>
                              <?php
                              $statusClass = match($session['status']) {
                                'Scheduled' => 'bg-secondary',
                                'Active' => 'bg-success',
                                'Paused' => 'bg-warning',
                                'Completed' => 'bg-info',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                              };
                              ?>
                              <span class="badge <?php echo $statusClass; ?>"><?php echo $session['status']; ?></span>
                            </td>
                            <td>
                              <?php if ($session['status'] === 'Scheduled'): ?>
                                <button class="btn btn-success btn-sm" onclick="startSession(<?php echo $session['session_id']; ?>)">
                                  <i class="ti ti-play me-1"></i>Start
                                </button>
                              <?php elseif ($session['status'] === 'Active'): ?>
                                <a href="<?php echo $session['game_mode'] === 'Solo' ? 'admin-score-monitoring-solo.php' : 'admin-score-monitoring-team.php'; ?>?session=<?php echo $session['session_id']; ?>" class="btn btn-warning btn-sm">
                                  <i class="ti ti-edit me-1"></i>Enter Scores
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="endSession(<?php echo $session['session_id']; ?>)" style="margin-left: 5px;">
                                  <i class="ti ti-stop me-1"></i>End Session
                                </button>
                              <?php else: ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
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
                        <?php 
                        // Get actual game dates from database - NO SESSION PARTICIPANTS NEEDED
                        try {
                          $pdo = getDBConnection();
                          $stmt = $pdo->prepare("
                            SELECT DISTINCT DATE(session_date) as session_date
                            FROM game_sessions 
                            WHERE status != 'Deleted'
                            ORDER BY session_date DESC
                            LIMIT 20
                          ");
                          $stmt->execute();
                          $sessionDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          // Add most recent date as first option (default)
                          if (!empty($sessionDates)) {
                            $mostRecentDate = $sessionDates[0]['session_date'];
                            echo '<option value="' . $mostRecentDate . '" selected>' . date('M j, Y', strtotime($mostRecentDate)) . '</option>';
                            
                            // Add other session dates
                            foreach (array_slice($sessionDates, 1) as $date) {
                              $formattedDate = date('M j, Y', strtotime($date['session_date']));
                              echo '<option value="' . $date['session_date'] . '">' . $formattedDate . '</option>';
                            }
                          } else {
                            echo '<option value="today" selected>' . date('M j, Y') . '</option>';
                          }
                          
                          // Add "All Time" option
                          echo '<option value="all">All Time</option>';
                        } catch (Exception $e) {
                          echo '<option value="today" selected>' . date('M j, Y') . '</option>';
                          echo '<option value="all">All Time</option>';
                        }
                        ?>
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
                      <button class="nav-link" id="trio-tab" data-bs-toggle="tab" data-bs-target="#trio" type="button" role="tab">
                        Trio Teams
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="teams-tab" data-bs-toggle="tab" data-bs-target="#teams" type="button" role="tab">
                        Team
                      </button>
                    </li>
                  </ul>

                  <div class="tab-content" id="performanceTabContent">
                    <!-- Overall Rankings Tab -->
                    <div class="tab-pane fade show active" id="overall" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover" id="leaderboardTable">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team/Player</th>
                              <th scope="col">Team Name</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games</th>
                              <th scope="col">Best Score</th>
                              <th scope="col">Strike Rate</th>
                              <th scope="col">Status</th>
                              <th scope="col">Actions</th>
                            </tr>
                          </thead>
                          <tbody id="leaderboardTable">
                            <tr>
                              <td colspan="10" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                  <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading dashboard data...</p>
                              </td>
                            </tr>
                          </tbody>
                          <tbody id="staticLeaderboardTable" style="display: none;">
                            <?php if (!empty($allPlayersStats) && !isset($allPlayersStats['error'])): ?>
                              <?php 
                              $rank = 1;
                              foreach ($allPlayersStats as $player): 
                                $totalScore = $player['avg_score'] * $player['total_games'];
                                $strikeRate = $player['total_games'] > 0 ? round(($player['strikes_count'] / $player['total_games']) * 100) : 0;
                                
                                // Show team name with green badge
                                $teamName = $player['team_name'] ?? 'No Team';
                                $badgeClass = 'bg-success';
                              ?>
                                <tr>
                                  <td><span class="badge bg-primary"><?php echo $rank; ?></span></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <img src="assets/images/profile/user-<?php echo ($rank % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                      <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></h6>
                                        <small class="text-muted">Team: <?php echo htmlspecialchars($player['team_name'] ?? 'No Team'); ?></small>
                                      </div>
                                    </div>
                                  </td>
                                  <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($teamName); ?></span></td>
                                  <td><span class="fw-bold text-success"><?php echo number_format($totalScore); ?></span></td>
                                  <td><?php echo number_format($player['avg_score'], 1); ?></td>
                                  <td><?php echo $player['total_games']; ?></td>
                                  <td><span class="badge bg-info"><?php echo $player['best_score']; ?></span></td>
                                  <td><span class="badge bg-warning text-dark"><?php echo $strikeRate; ?>%</span></td>
                                  <td><span class="badge bg-success">Active</span></td>
                                  <td>
                                    <div class="d-flex gap-1">
                                      <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('<?php echo $player['user_id']; ?>')" title="View Details">
                                        <i class="ti ti-eye"></i>
                                      </button>
                                      <button class="btn btn-sm btn-outline-success" onclick="editPlayer('<?php echo $player['user_id']; ?>')" title="Edit Player">
                                        <i class="ti ti-edit"></i>
                                      </button>
                                    </div>
                                  </td>
                                </tr>
                              <?php 
                              $rank++;
                              endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                  <i class="ti ti-user fs-1 mb-2"></i>
                                  <p class="mb-0">No player data available</p>
                                  <small>Players will appear here once they start playing</small>
                                </td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Solo Players Tab -->
                    <div class="tab-pane fade" id="solo" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover" id="soloStatsTable">
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
                            <?php if (!empty($soloPlayersStats) && !isset($soloPlayersStats['error'])): ?>
                              <?php 
                              $rank = 1;
                              foreach ($soloPlayersStats as $player): 
                                $totalScore = $player['avg_score'] * $player['total_games'];
                                $strikeRate = $player['total_games'] > 0 ? round(($player['strikes_count'] / $player['total_games']) * 100) : 0;
                              ?>
                                <tr>
                                  <td><span class="badge bg-primary"><?php echo $rank; ?></span></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <img src="assets/images/profile/user-<?php echo ($rank % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                      <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></h6>
                                        <small class="text-muted"><?php echo ucfirst($player['skill_level']); ?> Player</small>
                                      </div>
                                    </div>
                                  </td>
                                  <td><span class="fw-bold text-success"><?php echo number_format($totalScore); ?></span></td>
                                  <td><?php echo number_format($player['avg_score'], 1); ?></td>
                                  <td><?php echo $player['total_games']; ?></td>
                                  <td><span class="badge bg-info"><?php echo $player['best_score']; ?></span></td>
                                  <td><span class="badge bg-warning text-dark"><?php echo $strikeRate; ?>%</span></td>
                                  <td>
                                    <div class="d-flex gap-1">
                                      <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('<?php echo $player['user_id']; ?>')" title="View Details">
                                        <i class="ti ti-eye"></i>
                                      </button>
                                      <button class="btn btn-sm btn-outline-success" onclick="editPlayer('<?php echo $player['user_id']; ?>')" title="Edit Player">
                                        <i class="ti ti-edit"></i>
                                      </button>
                                      <button class="btn btn-sm btn-outline-info" onclick="viewScores('<?php echo $player['user_id']; ?>')" title="View Scores">
                                        <i class="ti ti-chart-line"></i>
                                      </button>
                                    </div>
                                  </td>
                                </tr>
                              <?php 
                              $rank++;
                              endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                  <i class="ti ti-user fs-1 mb-2"></i>
                                  <p class="mb-0">No solo players data available</p>
                                  <small>Players will appear here once they start playing solo games</small>
                                </td>
                              </tr>
                            <?php endif; ?>
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
                            <?php 
                            $doublesTeams = array_filter($teamStats, function($team) {
                                return $team['game_mode'] == 'Doubles';
                            });
                            ?>
                            <?php if (!empty($doublesTeams)): ?>
                              <?php 
                              $rank = 1;
                              foreach ($doublesTeams as $team): 
                                $totalScore = $team['team_average'] * $team['total_games'];
                              ?>
                                <tr>
                                  <td><span class="badge bg-primary"><?php echo $rank; ?></span></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <div class="d-flex me-2">
                                        <img src="assets/images/profile/user-<?php echo ($rank % 8) + 1; ?>.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="assets/images/profile/user-<?php echo (($rank + 1) % 8) + 1; ?>.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="32">
                                      </div>
                                      <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($team['team_name']); ?></h6>
                                        <small class="text-muted">Doubles Team</small>
                                      </div>
                                    </div>
                                  </td>
                                  <td><?php echo $team['players_count']; ?> players</td>
                                  <td><span class="fw-bold text-success"><?php echo number_format($totalScore); ?></span></td>
                                  <td><?php echo number_format($team['team_average'], 1); ?></td>
                                  <td><?php echo $team['total_games']; ?></td>
                                  <td><span class="badge bg-info"><?php echo $team['team_best']; ?></span></td>
                                  <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('<?php echo $team['team_name']; ?>')">
                                      <i class="ti ti-eye"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php 
                              $rank++;
                              endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                  <i class="ti ti-users fs-1 mb-2"></i>
                                  <p class="mb-0">No doubles teams data available</p>
                                  <small>Doubles teams will appear here once they start playing</small>
                                </td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Trio Teams Tab -->
                    <div class="tab-pane fade" id="trio" role="tabpanel">
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
                            <?php 
                            $trioTeams = array_filter($teamStats, function($team) {
                                return $team['game_mode'] == 'Trio';
                            });
                            ?>
                            <?php if (!empty($trioTeams)): ?>
                              <?php 
                              $rank = 1;
                              foreach ($trioTeams as $team): 
                                $totalScore = $team['team_average'] * $team['total_games'];
                              ?>
                                <tr>
                                  <td><span class="badge bg-primary"><?php echo $rank; ?></span></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <div class="d-flex me-2">
                                        <img src="assets/images/profile/user-<?php echo ($rank % 8) + 1; ?>.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="assets/images/profile/user-<?php echo (($rank + 1) % 8) + 1; ?>.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="assets/images/profile/user-<?php echo (($rank + 2) % 8) + 1; ?>.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="32">
                                      </div>
                                      <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($team['team_name']); ?></h6>
                                        <small class="text-muted">Trio Team</small>
                                      </div>
                                    </div>
                                  </td>
                                  <td><?php echo $team['players_count']; ?> players</td>
                                  <td><span class="fw-bold text-success"><?php echo number_format($totalScore); ?></span></td>
                                  <td><?php echo number_format($team['team_average'], 1); ?></td>
                                  <td><?php echo $team['total_games']; ?></td>
                                  <td><span class="badge bg-info"><?php echo $team['team_best']; ?></span></td>
                                  <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('<?php echo $team['team_name']; ?>')">
                                      <i class="ti ti-eye"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php 
                              $rank++;
                              endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                  <i class="ti ti-users fs-1 mb-2"></i>
                                  <p class="mb-0">No trio teams data available</p>
                                  <small>Trio teams will appear here once they start playing</small>
                                </td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Team Tab -->
                    <div class="tab-pane fade" id="teams" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover" id="teamStatsTable">
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
                            <?php 
                            $teamGroups = array_filter($teamStats, function($team) {
                                return $team['game_mode'] == 'Team';
                            });
                            ?>
                            <?php if (!empty($teamGroups)): ?>
                              <?php 
                              $rank = 1;
                              foreach ($teamGroups as $team): 
                                $totalScore = $team['team_average'] * $team['total_games'];
                              ?>
                                <tr>
                                  <td><span class="badge bg-primary"><?php echo $rank; ?></span></td>
                                  <td>
                                    <div class="d-flex align-items-center">
                                      <div class="d-flex me-2">
                                        <img src="assets/images/profile/user-<?php echo ($rank % 8) + 1; ?>.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="assets/images/profile/user-<?php echo (($rank + 1) % 8) + 1; ?>.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="assets/images/profile/user-<?php echo (($rank + 2) % 8) + 1; ?>.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="assets/images/profile/user-<?php echo (($rank + 3) % 8) + 1; ?>.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="32">
                                      </div>
                                      <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($team['team_name']); ?></h6>
                                        <small class="text-muted">Team</small>
                                      </div>
                                    </div>
                                  </td>
                                  <td><?php echo $team['players_count']; ?> players</td>
                                  <td><span class="fw-bold text-success"><?php echo number_format($totalScore); ?></span></td>
                                  <td><?php echo number_format($team['team_average'], 1); ?></td>
                                  <td><?php echo $team['total_games']; ?></td>
                                  <td><span class="badge bg-info"><?php echo $team['team_best']; ?></span></td>
                                  <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDetails('<?php echo $team['team_name']; ?>')">
                                      <i class="ti ti-eye"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php 
                              $rank++;
                              endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                  <i class="ti ti-users fs-1 mb-2"></i>
                                  <p class="mb-0">No team data available</p>
                                  <small>Teams will appear here once they start playing</small>
                                </td>
                              </tr>
                            <?php endif; ?>
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
              <!-- Recent Activity -->
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <i class="ti ti-activity fs-4 text-info me-2"></i>
                    <h5 class="card-title mb-0">Recent Activity</h5>
                  </div>
                  
                  <div class="activity-list" id="recentActivities">
                    <?php if (!empty($recentActivities) && !isset($recentActivities['error'])): ?>
                      <?php foreach ($recentActivities as $activity): ?>
                        <div class="d-flex align-items-center mb-3">
                          <div class="<?php echo $activity['icon_color']; ?> rounded-circle p-2 me-3">
                            <i class="<?php echo $activity['icon']; ?> text-white fs-6"></i>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold"><?php echo $activity['title']; ?></h6>
                            <small class="text-muted"><?php echo $activity['description']; ?> - <?php echo $activity['time']; ?></small>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="text-center text-muted py-4">
                        <i class="ti ti-activity fs-1 mb-2"></i>
                        <p class="mb-0">No recent activities</p>
                        <small>Activities will appear here as players achieve high scores</small>
                      </div>
                    <?php endif; ?>
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



  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Session Management Functions - Define globally first
    function startSession(sessionId) {
      console.log('Starting session:', sessionId);
      if (confirm('Are you sure you want to start this session?')) {
        console.log('User confirmed, sending request...');
        fetch('ajax/session-management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=start&session_id=' + sessionId
        })
        .then(response => {
          console.log('Response status:', response.status);
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.success) {
            console.log('Session started successfully, reloading page...');
            location.reload();
          } else {
            console.error('Start session failed:', data.message);
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          alert('An error occurred while starting the session');
        });
      }
    }

    function endSession(sessionId) {
      if (confirm('Are you sure you want to end this session? This will mark it as completed.')) {
        fetch('ajax/session-management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=end&session_id=' + sessionId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while ending the session');
        });
      }
    }

    function cancelSession(sessionId) {
      if (confirm('Are you sure you want to cancel this session? This action cannot be undone.')) {
        fetch('ajax/session-management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=cancel&session_id=' + sessionId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('Session cancelled successfully!', 'success');
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while cancelling the session');
        });
      }
    }

    function pauseSession(sessionId) {
      if (confirm('Are you sure you want to pause this session? Players won\'t be able to enter scores until resumed.')) {
        fetch('ajax/session-management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=pause&session_id=' + sessionId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('Session paused successfully!', 'success');
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while pausing the session');
        });
      }
    }

    function restartSession(sessionId) {
      if (confirm('Are you sure you want to restart this session? This will reset it to Scheduled status.')) {
        fetch('ajax/session-management.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=restart&session_id=' + sessionId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('Session restarted successfully!', 'success');
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while restarting the session');
        });
      }
    }

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

    // Handle time filter change and initial load
    document.addEventListener('DOMContentLoaded', function() {
      const timeFilter = document.getElementById('timeFilter');
      if (timeFilter) {
        // Load initial data for the selected date (should be active session date)
        const selectedDate = timeFilter.value;
        console.log('Loading initial data for date:', selectedDate);
        loadFilteredData(selectedDate);
        
        // Add change event listener
        timeFilter.addEventListener('change', function() {
          const selectedDate = this.value;
          const selectedText = this.options[this.selectedIndex].text;
          console.log('Date filter changed to:', selectedDate, selectedText);
          showNotification(`Loading data for ${selectedText}...`, 'info');
          loadFilteredData(selectedDate);
        });
      }
    });

    // Load filtered data based on selected date
    function loadFilteredData(selectedDate) {
      console.log('Loading data for date:', selectedDate);
      const formData = new FormData();
      formData.append('selected_date', selectedDate);
      
      fetch('ajax/admin-dashboard-data.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Response data:', data);
        if (data.debug) {
          console.log('Debug info:', data.debug);
        }
        if (data.success) {
          updateDashboardData(data.data);
          if (data.data.has_active_session) {
            if (data.data.leaderboard.length > 0) {
              showNotification('Data loaded successfully!', 'success');
            } else {
              showNotification('Active session found but no participants', 'info');
            }
          } else {
            showNotification('No active session found', 'info');
          }
        } else {
          showNotification('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading data', 'error');
      });
    }

    // Update dashboard data with filtered results
    function updateDashboardData(data) {
      console.log('updateDashboardData called with:', data);
      console.log('has_active_session:', data.has_active_session);
      console.log('leaderboard length:', data.leaderboard ? data.leaderboard.length : 'undefined');
      console.log('team_stats length:', data.team_stats ? data.team_stats.length : 'undefined');
      console.log('solo_stats length:', data.solo_stats ? data.solo_stats.length : 'undefined');
      
      // Check if there's an active session
      if (!data.has_active_session) {
        console.log('No active session, showing static content');
        showNoActiveSessionMessage();
        return;
      }
      
      // Check if there are any participants in the active session
      if (data.leaderboard.length === 0 && data.team_stats.length === 0 && data.solo_stats.length === 0) {
        console.log('No participants, showing static content');
        showNoParticipantsMessage();
        return;
      }
      
      // Update leaderboard
      updateLeaderboard(data.leaderboard);
      
      // Update team stats
      updateTeamStats(data.team_stats);
      
      // Update solo stats
      updateSoloStats(data.solo_stats);
      
      // Update recent activities
      updateRecentActivities(data.recent_activities);
      
      // Update statistics cards
      updateStatisticsCards(data.filtered_stats);
    }

    // Show static content when no dynamic data
    function showStaticContent() {
      const tbody = document.querySelector('#leaderboardTable');
      const staticTbody = document.querySelector('#staticLeaderboardTable');
      
      if (tbody) {
        tbody.style.display = 'none';
      }
      if (staticTbody) {
        staticTbody.style.display = 'table-row-group';
      }
    }

    // Show message when no active session
    function showNoActiveSessionMessage() {
      showStaticContent();
      // Update leaderboard
      const tbody = document.querySelector('#leaderboardTable tbody');
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4"><i class="ti ti-calendar-off fs-1 mb-2"></i><br>No Active Session<br><small>Create a session to start tracking scores</small></td></tr>';
      }
      
      // Update team stats
      const teamTbody = document.querySelector('#teamStatsTable tbody');
      if (teamTbody) {
        teamTbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="ti ti-calendar-off fs-1 mb-2"></i><br>No Active Session<br><small>Create a session to start tracking team scores</small></td></tr>';
      }
      
      // Update solo stats
      const soloTbody = document.querySelector('#soloStatsTable tbody');
      if (soloTbody) {
        soloTbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="ti ti-calendar-off fs-1 mb-2"></i><br>No Active Session<br><small>Create a session to start tracking solo scores</small></td></tr>';
      }
      
      // Update recent activities
      const activitiesContainer = document.querySelector('#recentActivities');
      if (activitiesContainer) {
        activitiesContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-calendar-off fs-1 mb-2"></i><br>No Active Session<br><small>Create a session to start tracking activities</small></div>';
      }
      
      // Update statistics cards
      updateStatisticsCards({
        total_players: 0,
        total_games: 0,
        players_played_today: 0,
        avg_score_today: 0
      });
    }

    // Show message when no participants in active session
    function showNoParticipantsMessage() {
      showStaticContent();
      // Update leaderboard
      const tbody = document.querySelector('#leaderboardTable tbody');
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4"><i class="ti ti-users-off fs-1 mb-2"></i><br>No Participants in Active Session<br><small>Add players to the session to start tracking scores</small></td></tr>';
      }
      
      // Update team stats
      const teamTbody = document.querySelector('#teamStatsTable tbody');
      if (teamTbody) {
        teamTbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="ti ti-users-off fs-1 mb-2"></i><br>No Participants in Active Session<br><small>Add players to the session to start tracking team scores</small></td></tr>';
      }
      
      // Update solo stats
      const soloTbody = document.querySelector('#soloStatsTable tbody');
      if (soloTbody) {
        soloTbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="ti ti-users-off fs-1 mb-2"></i><br>No Participants in Active Session<br><small>Add players to the session to start tracking solo scores</small></td></tr>';
      }
      
      // Update recent activities
      const activitiesContainer = document.querySelector('#recentActivities');
      if (activitiesContainer) {
        activitiesContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="ti ti-users-off fs-1 mb-2"></i><br>No Participants in Active Session<br><small>Add players to the session to start tracking activities</small></div>';
      }
      
      // Update statistics cards
      updateStatisticsCards({
        total_players: 0,
        total_games: 0,
        players_played_today: 0,
        avg_score_today: 0
      });
    }

    // Update leaderboard table
    function updateLeaderboard(leaderboard) {
      const tbody = document.querySelector('#leaderboardTable');
      const staticTbody = document.querySelector('#staticLeaderboardTable');
      if (!tbody) return;
      
      // Hide static content and show dynamic content
      if (staticTbody) {
        staticTbody.style.display = 'none';
      }
      tbody.style.display = 'table-row-group';
      
      tbody.innerHTML = '';
      
      leaderboard.forEach((player, index) => {
        const row = document.createElement('tr');
        const rank = index + 1;
        const rankClass = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-other';
        const scoreClass = player.total_score >= 200 ? 'score-excellent' : player.total_score >= 150 ? 'score-good' : player.total_score >= 100 ? 'score-average' : 'score-below';
        const strikeRateClass = player.strike_rate >= 80 ? 'text-success' : player.strike_rate >= 60 ? 'text-warning' : 'text-danger';
        
        row.innerHTML = `
          <td>
            <div class="rank-badge ${rankClass}">
              ${rank}
            </div>
          </td>
          <td>
            <div class="d-flex align-items-center">
              <div class="player-avatar-container me-3">
                <img src="./assets/images/profile/user-${(index % 8) + 1}.jpg" alt="user" class="player-avatar">
                ${rank <= 3 ? `<div class="rank-crown rank-${rank}"><i class="ti ti-crown"></i></div>` : ''}
              </div>
              <div>
                <h6 class="mb-1 fw-bold text-dark">${player.first_name} ${player.last_name}</h6>
                <small class="text-muted d-flex align-items-center">
                  <i class="ti ti-users me-1"></i>
                  ${player.team_name || 'Solo Player'}
                </small>
              </div>
            </div>
          </td>
          <td>
            <span class="team-text">
              <i class="ti ti-users me-1"></i>
              ${player.team_name || 'No Team'}
            </span>
          </td>
          <td>
            <div class="score-display">
              <span class="score-highlight ${scoreClass}">${player.total_score}</span>
              <small class="text-muted d-block">Total</small>
            </div>
          </td>
          <td>
            <div class="stat-display">
              <span class="fw-bold text-primary">${player.avg_score}</span>
              <small class="text-muted d-block">Avg/Game</small>
            </div>
          </td>
          <td>
            <div class="stat-display">
              <span class="fw-bold text-info">${player.games_played}</span>
              <small class="text-muted d-block">Games</small>
            </div>
          </td>
          <td>
            <div class="best-score-display">
              <span class="best-score-badge">${player.best_score}</span>
              <small class="text-muted d-block">Best</small>
            </div>
          </td>
          <td>
            <div class="strike-rate-display">
              <span class="fw-bold ${strikeRateClass}">${player.strike_rate || 0}%</span>
              <small class="text-muted d-block">Strikes</small>
            </div>
          </td>
          <td>
            <span class="status-text status-${player.status.toLowerCase()}">
              <i class="ti ti-circle-filled me-1"></i>
              ${player.status}
            </span>
          </td>
          <td>
            <div class="action-buttons">
              <button class="btn btn-sm btn-action btn-edit" onclick="goToScoreTable(${player.user_id})" title="Edit Scores">
                <i class="ti ti-edit"></i>
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    // Update team stats table
    function updateTeamStats(teamStats) {
      const tbody = document.querySelector('#teamStatsTable tbody');
      if (!tbody) return;
      
      tbody.innerHTML = '';
      
      teamStats.forEach((team, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td><span class="badge bg-primary">${index + 1}</span></td>
          <td><strong>${team.team_name}</strong></td>
          <td>${team.player_count}</td>
          <td><span class="text-success fw-bold">${team.total_score}</span></td>
          <td>${team.avg_score}</td>
          <td>${team.total_games}</td>
          <td><span class="badge bg-info">${team.best_score}</span></td>
        `;
        tbody.appendChild(row);
      });
    }

    // Update solo stats table
    function updateSoloStats(soloStats) {
      const tbody = document.querySelector('#soloStatsTable tbody');
      if (!tbody) return;
      
      tbody.innerHTML = '';
      
      soloStats.forEach((player, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td><span class="badge bg-primary">${index + 1}</span></td>
          <td><strong>${player.first_name} ${player.last_name}</strong></td>
          <td><span class="text-success fw-bold">${player.total_score}</span></td>
          <td>${player.avg_score}</td>
          <td>${player.games_played}</td>
          <td><span class="badge bg-info">${player.best_score}</span></td>
          <td><span class="badge bg-warning text-dark">${player.strike_rate || 0}%</span></td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(${player.user_id})" title="View Details">
                <i class="ti ti-eye"></i>
              </button>
              <button class="btn btn-sm btn-outline-success" onclick="editPlayer(${player.user_id})" title="Edit Player">
                <i class="ti ti-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-info" onclick="viewScores(${player.user_id})" title="View Scores">
                <i class="ti ti-chart-line"></i>
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    // Update recent activities
    function updateRecentActivities(activities) {
      const container = document.querySelector('#recentActivities');
      if (!container) return;
      
      container.innerHTML = '';
      
      activities.forEach(activity => {
        const activityDiv = document.createElement('div');
        activityDiv.className = 'd-flex align-items-center mb-3';
        activityDiv.innerHTML = `
          <div class="me-3">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
              <i class="ti ti-bowling text-white"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1">${activity.first_name} ${activity.last_name} scored ${activity.player_score}</h6>
            <p class="text-muted mb-0">Game ${activity.game_number} • ${activity.strikes} strikes • ${activity.spares} spares</p>
            <small class="text-muted">${getTimeAgo(activity.created_at)}</small>
          </div>
        `;
        container.appendChild(activityDiv);
      });
    }

    // Update statistics cards
    function updateStatisticsCards(stats) {
      // Update total players
      const totalPlayersEl = document.getElementById('totalPlayersCount');
      if (totalPlayersEl) {
        totalPlayersEl.textContent = stats.total_players || 0;
      }
      
      // Update players played today
      const playersPlayedEl = document.getElementById('playersPlayedTodayCount');
      if (playersPlayedEl) {
        playersPlayedEl.textContent = stats.players_played_today || 0;
      }
    }

    // Helper function for time ago
    function getTimeAgo(datetime) {
      const now = new Date();
      const time = new Date(datetime);
      const diffInSeconds = Math.floor((now - time) / 1000);
      
      if (diffInSeconds < 60) return 'just now';
      if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
      if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
      return Math.floor(diffInSeconds / 86400) + ' days ago';
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
        'team3': { // Lane Masters
          members: [
            { name: 'Tom Anderson', color: 'bg-primary' },
            { name: 'Emma Davis', color: 'bg-success' },
            { name: 'Alex Chen', color: 'bg-warning text-dark' },
            { name: 'Maria Garcia', color: 'bg-info' },
            { name: 'Chris Wilson', color: 'bg-danger' },
            { name: 'Sarah Johnson', color: 'bg-dark' }
          ]
        },
        'team4': { // Pin Crushers
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

  <!-- Create Session Modal -->
  <div class="modal fade" id="createSessionModal" tabindex="-1" aria-labelledby="createSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createSessionModalLabel">Create New Game Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="createSessionForm">
          <div class="modal-body">
            <div class="mb-3">
              <label for="sessionName" class="form-label">Session Name</label>
              <input type="text" class="form-control" id="sessionName" name="session_name" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="sessionDate" class="form-label">Date</label>
                <input type="date" class="form-control" id="sessionDate" name="session_date" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="sessionTime" class="form-label">Time</label>
                <input type="time" class="form-control" id="sessionTime" name="session_time" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="gameMode" class="form-label">Game Mode</label>
              <select class="form-select" id="gameMode" name="game_mode" required onchange="toggleTeamSelection()">
                <option value="Solo">Solo</option>
                <option value="Doubles" disabled>Doubles (Coming Soon)</option>
                <option value="Team">Team</option>
              </select>
            </div>
            
            <!-- Team Selection (hidden by default) -->
            <div id="teamSelectionSection" class="mb-3" style="display: none;">
              <label class="form-label">Select Teams</label>
              <div id="teamCheckboxes" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                <div class="text-center text-muted">
                  <i class="ti ti-loader-2 ti-spin"></i> Loading teams...
                </div>
              </div>
              <small class="form-text text-muted">Select which teams will participate in this session</small>
            </div>
            
            <div class="mb-3">
              <label for="maxPlayers" class="form-label">Max Players</label>
              <input type="number" class="form-control" id="maxPlayers" name="max_players" value="20" min="1" max="20" required>
            </div>
            <div class="mb-3">
              <label for="sessionNotes" class="form-label">Notes (Optional)</label>
              <textarea class="form-control" id="sessionNotes" name="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Next: Select Participants →</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Handle create session form submission
    document.getElementById('createSessionForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="ti ti-loader"></i> Creating Session...';
      
      const formData = new FormData(this);
      formData.append('action', 'create_session_draft');
      formData.append('created_by', <?php echo $currentUser['user_id']; ?>);
      
      fetch('ajax/simple-session-creation.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message, 'success');
          // Close modal and redirect to participant selection
          const modal = bootstrap.Modal.getInstance(document.getElementById('createSessionModal'));
          modal.hide();
          
          setTimeout(() => {
            window.location.href = data.redirect_url;
          }, 1000);
        } else {
          showNotification(data.message, 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while creating the session', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      });
    });

    // Set default date to next Saturday
    const today = new Date();
    const nextSaturday = new Date(today);
    nextSaturday.setDate(today.getDate() + (6 - today.getDay()) % 7);
    if (nextSaturday <= today) {
      nextSaturday.setDate(nextSaturday.getDate() + 7);
    }
    document.getElementById('sessionDate').value = nextSaturday.toISOString().split('T')[0];
    document.getElementById('sessionTime').value = '14:00';
    
    // Toggle team selection based on game mode
    function toggleTeamSelection() {
      const gameMode = document.getElementById('gameMode').value;
      const teamSection = document.getElementById('teamSelectionSection');
      
      if (gameMode === 'Team') {
        teamSection.style.display = 'block';
        loadTeams();
      } else {
        teamSection.style.display = 'none';
      }
    }
    
    // Load available teams
    function loadTeams() {
      const teamCheckboxes = document.getElementById('teamCheckboxes');
      teamCheckboxes.innerHTML = '<div class="text-center text-muted"><i class="ti ti-loader-2 ti-spin"></i> Loading teams...</div>';
      
      fetch('ajax/session-management.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_teams'
      })
      .then(response => response.json())
      .then(data => {
        console.log('Teams response:', data);
        if (data.success) {
          console.log('Teams loaded:', data.teams);
          if (data.teams.length === 0) {
            teamCheckboxes.innerHTML = '<div class="text-center text-muted"><i class="ti ti-users-off"></i> No teams found</div>';
          } else {
            let html = '';
            data.teams.forEach(team => {
              console.log('Adding team checkbox for:', team);
              html += `
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="${team}" id="team_${team.replace(/\s+/g, '_')}">
                  <label class="form-check-label" for="team_${team.replace(/\s+/g, '_')}">
                    ${team}
                  </label>
                </div>
              `;
            });
            teamCheckboxes.innerHTML = html;
            console.log('Team checkboxes HTML:', html);
          }
        } else {
          console.error('Error loading teams:', data.message);
          teamCheckboxes.innerHTML = '<div class="text-center text-danger"><i class="ti ti-alert-triangle"></i> Error loading teams: ' + data.message + '</div>';
        }
      })
      .catch(error => {
        console.error('Error loading teams:', error);
        teamCheckboxes.innerHTML = '<div class="text-center text-danger"><i class="ti ti-alert-triangle"></i> Error loading teams</div>';
      });
    }
    
    // View player details
    function viewDetails(userId) {
      showNotification('Viewing details for player ID: ' + userId, 'info');
      // This would typically open a modal or redirect to player details page
      // For now, just show a notification
    }
    
    // Go to score table for editing
    function goToScoreTable(userId) {
      // Redirect to the admin score monitoring page
      window.location.href = 'admin-score-monitoring-solo.php';
    }
    
    // View player scores
    function viewScores(userId) {
      showNotification('Viewing scores for player ID: ' + userId, 'info');
      // This would typically open a modal or redirect to player scores page
      // For now, just show a notification
    }
  </script>
</body>

</html>
