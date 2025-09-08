<?php
require_once 'includes/auth.php';
require_once 'includes/dashboard.php';
requireLogin(); // Ensure user is logged in

// Get current user info
$currentUser = getCurrentUser();

// Get player statistics
$playerData = getPlayerStats($currentUser['user_id']);

// Debug: Let's see what data we're getting
// echo "<pre>Player Data: "; print_r($playerData); echo "</pre>";
$leaderboard = getLeaderboard(5);
$weeklyTopPlayers = getWeeklyTopPlayers(3);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPEEDSTERS - Bowling System Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .countdown-box {
      transition: all 0.3s ease;
    }
    .countdown-box:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }
    /* Fixed positioning for dashboard elements */
    .page-wrapper {
      position: relative;
      min-height: 100vh;
    }
    .body-wrapper {
      position: relative;
      min-height: calc(100vh - 70px);
    }
    .body-wrapper-inner {
      position: relative;
      padding: 20px;
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
        <div class="container-fluid" >
          <!--  Row 1 - Player Dashboard -->
          <div class="row">
            <!-- Personal Bowling Statistics -->
            <div class="col-lg-8">
              <div class="card w-100">
                <div class="card-body">
                  <div class="d-md-flex align-items-center">
                    <div>
                      <h4 class="card-title">My Bowling Performance</h4>
                      <p class="card-subtitle">
                        Your personal bowling statistics
                      </p>
                    </div>
                    <div class="ms-auto">
                      <button class="btn btn-primary btn-sm">
                        <i class="ti ti-refresh me-1"></i>
                        Update Stats
                      </button>
                    </div>
                  </div>
                  
                  <!-- Performance Stats Grid -->
                  <div class="row mt-4">
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-primary fw-bold"><?php echo isset($playerData['stats']['average_score']) ? number_format($playerData['stats']['average_score'], 1) : '0.0'; ?></div>
                        <small class="text-muted">Average Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-success fw-bold"><?php echo isset($playerData['stats']['best_score']) ? $playerData['stats']['best_score'] : '0'; ?></div>
                        <small class="text-muted">Best Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-warning fw-bold"><?php echo isset($playerData['stats']['total_games']) ? $playerData['stats']['total_games'] : '0'; ?></div>
                        <small class="text-muted">Games Played</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-info fw-bold"><?php echo isset($playerData['stats']['total_strikes']) ? $playerData['stats']['total_strikes'] : '0'; ?></div>
                        <small class="text-muted">Total Strikes</small>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Team Format Scores -->
                  <div class="row mt-4">
                    <div class="col-12">
                      <h6 class="mb-3">Team Format Performance</h6>
                      <div class="row">
                        <div class="col-md-3 mb-3">
                          <div class="card border-primary">
                            <div class="card-body text-center">
                              <h6 class="card-title text-primary">Solo Games</h6>
                              <div class="display-6 text-primary fw-bold"><?php echo isset($playerData['stats']['solo_games']) && $playerData['stats']['solo_games'] > 0 ? number_format($playerData['stats']['solo_games'], 0) : '0'; ?></div>
                              <small class="text-muted">Games Played</small>
                              <div class="mt-2">
                                <span class="badge bg-primary">Best: <?php echo isset($playerData['stats']['best_score']) ? $playerData['stats']['best_score'] : '0'; ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="card border-success">
                            <div class="card-body text-center">
                              <h6 class="card-title text-success">Doubles</h6>
                              <div class="display-6 text-success fw-bold"><?php echo isset($playerData['stats']['doubles_games']) && $playerData['stats']['doubles_games'] > 0 ? number_format($playerData['stats']['doubles_games'], 0) : '0'; ?></div>
                              <small class="text-muted">Games Played</small>
                              <div class="mt-2">
                                <span class="badge bg-success">Best: 0</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="card border-info">
                            <div class="card-body text-center">
                              <h6 class="card-title text-info">Trio Games</h6>
                              <div class="display-6 text-info fw-bold">0</div>
                              <small class="text-muted">Games Played</small>
                              <div class="mt-2">
                                <span class="badge bg-info">Best: 0</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 mb-3">
                          <div class="card border-warning">
                            <div class="card-body text-center">
                              <h6 class="card-title text-warning">Team</h6>
                              <div class="display-6 text-warning fw-bold"><?php echo isset($playerData['stats']['team_games']) && $playerData['stats']['team_games'] > 0 ? number_format($playerData['stats']['team_games'], 0) : '0'; ?></div>
                              <small class="text-muted">Games Played</small>
                              <div class="mt-2">
                                <span class="badge bg-warning">Best: 0</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Latest Games -->
                  <div class="mt-4">
                    <h6 class="mb-3">Latest Games</h6>
                    <?php 
                    // Get latest 10 individual games
                    try {
                        $pdo = getDBConnection();
                        $stmt = $pdo->prepare("
                            SELECT game_date, game_time, player_score, game_mode, team_name, lane_number, game_number
                            FROM game_scores 
                            WHERE user_id = ? AND status = 'Completed'
                            ORDER BY game_date DESC, game_time DESC
                            LIMIT 10
                        ");
                        $stmt->execute([$currentUser['user_id']]);
                        $latestGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch(PDOException $e) {
                        $latestGames = [];
                    }
                    ?>
                    
                    <?php if (!empty($latestGames)): ?>
                      <div class="row">
                        <?php foreach ($latestGames as $game): ?>
                          <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light">
                              <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <h6 class="mb-1 fw-bold">Game <?php echo $game['game_number']; ?></h6>
                                    <small class="text-muted"><?php echo ucfirst($game['game_mode']); ?> • <?php echo date('M j', strtotime($game['game_date'])); ?></small>
                                  </div>
                                  <div class="text-end">
                                    <span class="fw-bold text-primary fs-5"><?php echo $game['player_score']; ?></span>
                                    <br>
                                    <small class="text-muted">Lane <?php echo $game['lane_number']; ?></small>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php else: ?>
                      <div class="text-center text-muted py-4">
                        <i class="ti ti-bowling fs-1 mb-2"></i>
                        <p class="mb-0">No games played yet</p>
                        <small>Start playing to see your latest games!</small>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Quick Actions & Recent Activity -->
            <div class="col-lg-4">
              <div class="card overflow-hidden">
                <div class="card-body pb-0">
                  <div class="d-flex align-items-start">
                    <div>
                      <h4 class="card-title">Quick Actions</h4>
                      <p class="card-subtitle">Book lanes & join events</p>
                    </div>
                  </div>
                  
                  <!-- Quick Action Buttons -->
                  <div class="mt-4">
                    <button class="btn btn-primary w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                      <i class="ti ti-calendar-plus fs-5"></i>
                      Book a Lane
                    </button>
                    <button class="btn btn-warning w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                      <i class="ti ti-trophy fs-5"></i>
                      Join Tournament
                    </button>
                    <button class="btn btn-success w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                      <i class="ti ti-users fs-5"></i>
                      View Schedule
                    </button>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          
          <!-- Recent Games & Upcoming Events -->
          <div class="row mt-4">
            <!-- Recent Games -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-md-flex align-items-center">
                    <div>
                      <h4 class="card-title">Recent Matches</h4>
                      <p class="card-subtitle">Your last 5 bowling matches</p>
                    </div>
                    <div class="ms-auto">
                      <button class="btn btn-outline-primary btn-sm">View All</button>
                    </div>
                  </div>
                  <div class="table-responsive mt-4">
                    <table class="table mb-0 text-nowrap align-middle fs-3">
                      <thead>
                        <tr>
                          <th scope="col" class="px-0 text-muted">Date</th>
                          <th scope="col" class="px-0 text-muted">Lane</th>
                          <th scope="col" class="px-0 text-muted">Format</th>
                          <th scope="col" class="px-0 text-muted">Total Score</th>
                          <th scope="col" class="px-0 text-muted text-end">Games</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($playerData['recent_matches'])): ?>
                          <?php foreach ($playerData['recent_matches'] as $match): ?>
                            <tr>
                              <td class="px-0">
                                <div>
                                  <h6 class="mb-0 fw-bolder"><?php echo date('M j', strtotime($match['game_date'])); ?></h6>
                                  <small class="text-muted">Match</small>
                                </div>
                              </td>
                              <td class="px-0">Lane <?php echo $match['lane_number'] ?? 'N/A'; ?></td>
                              <td class="px-0">
                                <?php 
                                $badgeClass = 'bg-primary';
                                if ($match['game_mode'] == 'Doubles') $badgeClass = 'bg-success';
                                if ($match['game_mode'] == 'Team') $badgeClass = 'bg-warning';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($match['game_mode']); ?></span>
                              </td>
                              <td class="px-0">
                                <span class="fw-bold text-primary"><?php echo $match['total_score']; ?></span>
                              </td>
                              <td class="px-0 text-end">
                                <span class="badge bg-info"><?php echo $match['games_played']; ?> games</span>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                              <i class="ti ti-bowling fs-1 mb-2"></i>
                              <p class="mb-0">No matches played yet</p>
                              <small>Start playing to see your match history!</small>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Top Players Leaderboard -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-md-flex align-items-center">
                    <div>
                      <h4 class="card-title">Top 3 This Week</h4>
                      <p class="card-subtitle">Highest scores of the week</p>
                    </div>
                    <div class="ms-auto">
                      <a href="score-table-solo.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                  </div>
                  <div class="mt-4">
                    <?php if (!empty($weeklyTopPlayers)): ?>
                      <?php foreach ($weeklyTopPlayers as $index => $player): ?>
                        <div class="d-flex align-items-center p-3 border rounded mb-3">
                          <div class="bg-<?php echo $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info'); ?> rounded-circle p-2 me-3">
                            <i class="ti ti-trophy text-white fs-5"></i>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($player['first_name'] . ' ' . $player['last_name']); ?></h6>
                            <small class="text-muted"><?php echo ucfirst($player['skill_level']); ?> • <?php echo ucfirst($player['game_mode']); ?></small>
                            <div class="mt-1">
                              <span class="badge bg-success">Score: <?php echo $player['player_score']; ?></span>
                              <small class="text-muted ms-2"><?php echo date('M j', strtotime($player['game_date'])); ?></small>
                            </div>
                          </div>
                          <div class="text-end">
                            <span class="badge bg-primary fs-6">#<?php echo $index + 1; ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="text-center text-muted py-4">
                        <i class="ti ti-trophy fs-1 mb-2"></i>
                        <p class="mb-0">No games this week</p>
                        <small>Players will appear here once they play this week!</small>
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
  
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="./assets/js/dashboard.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
</body>

</html>
