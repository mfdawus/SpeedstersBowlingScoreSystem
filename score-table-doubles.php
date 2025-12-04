<?php
require_once 'includes/auth.php';
require_once 'database.php';

// Require login
requireLogin();

// Get current user
$currentUser = getCurrentUser();

$pdo = getDBConnection();

// Determine which Doubles session to show
$selectedSessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;

if ($selectedSessionId <= 0) {
    // Get latest Doubles session
    $stmt = $pdo->prepare("
        SELECT session_id, session_name, session_date
        FROM game_sessions
        WHERE game_mode = 'Doubles'
        ORDER BY session_date DESC
        LIMIT 1
    ");
    $stmt->execute();
    $latest = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($latest) {
        $selectedSessionId = (int)$latest['session_id'];
        $selectedSession = $latest;
    } else {
        $selectedSession = null;
    }
} else {
    $stmt = $pdo->prepare("
        SELECT session_id, session_name, session_date
        FROM game_sessions
        WHERE session_id = ? AND game_mode = 'Doubles'
        LIMIT 1
    ");
    $stmt->execute([$selectedSessionId]);
    $selectedSession = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Load duo teams for this session
$duoTableData = [];
if ($selectedSession) {
    // Fetch duos
    $duoStmt = $pdo->prepare("
        SELECT 
            dt.duo_id,
            dt.duo_name,
            dt.session_id,
            dt.lane_number,
            dt.status,
            u1.first_name AS p1_first,
            u1.last_name AS p1_last,
            u1.profile_picture AS p1_pic,
            u2.first_name AS p2_first,
            u2.last_name AS p2_last,
            u2.profile_picture AS p2_pic
        FROM duo_teams dt
        JOIN users u1 ON dt.player1_id = u1.user_id
        JOIN users u2 ON dt.player2_id = u2.user_id
        WHERE dt.session_id = ?
        ORDER BY dt.duo_id ASC
    ");
    $duoStmt->execute([$selectedSessionId]);
    $duos = $duoStmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize structure
    foreach ($duos as $d) {
        $duoId = (int)$d['duo_id'];
        $duoTableData[$duoId] = [
            'duo_id' => $duoId,
            'duo_name' => $d['duo_name'],
            'lane_number' => $d['lane_number'],
            'status' => $d['status'],
            'players' => [
                [
                    'name' => trim($d['p1_first'] . ' ' . $d['p1_last']),
                    'pic' => $d['p1_pic'],
                ],
                [
                    'name' => trim($d['p2_first'] . ' ' . $d['p2_last']),
                    'pic' => $d['p2_pic'],
                ],
            ],
            'games' => [],         // game_number => ['score' => ..., 'strikes' => ..., 'time' => ...]
            'total_score' => 0,
            'games_played' => 0,
            'best_game' => 0,
            'combined_strikes' => 0,
            'last_updated' => null,
        ];
    }

    if (!empty($duoTableData)) {
        // Fetch team scores from game_scores (games 1-6) - aggregate by duo and game
        try {
            $scoreStmt = $pdo->prepare("
                SELECT 
                    duo_id,
                    game_number,
                    SUM(player_score) as team_score,
                    SUM(COALESCE(strikes, 0)) as team_strikes,
                    SUM(COALESCE(spares, 0)) as team_spares,
                    SUM(COALESCE(open_frames, 0)) as team_open_frames,
                    MAX(created_at) as created_at
                FROM game_scores
                WHERE session_id = ?
                  AND duo_id IS NOT NULL
                  AND status = 'Completed'
                  AND game_number BETWEEN 1 AND 6
                GROUP BY duo_id, game_number
            ");
            $scoreStmt->execute([$selectedSessionId]);
            $scores = $scoreStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log error but don't break the page
            error_log('Error fetching duo scores: ' . $e->getMessage());
            $scores = [];
        }

        foreach ($scores as $s) {
            $duoId = (int)$s['duo_id'];
            if (!isset($duoTableData[$duoId])) {
                continue;
            }
            $g = (int)$s['game_number'];
            $score = (int)($s['team_score'] ?? 0);
            $strikes = (int)($s['team_strikes'] ?? 0);
            $spares = (int)($s['team_spares'] ?? 0);
            $openFrames = (int)($s['team_open_frames'] ?? 0);
            $time = $s['created_at'] ?? null;

            $duo = &$duoTableData[$duoId];
            $duo['games'][$g] = [
                'score' => $score,
                'strikes' => $strikes,
                'spares' => $spares,
                'open_frames' => $openFrames,
                'time' => $time,
            ];
            $duo['total_score'] += $score;
            $duo['combined_strikes'] += $strikes;
            $duo['games_played'] = count($duo['games']);
            if ($score > $duo['best_game']) {
                $duo['best_game'] = $score;
            }
            if (!$duo['last_updated'] || $time > $duo['last_updated']) {
                $duo['last_updated'] = $time;
            }
        }
        unset($duo); // break reference
    }
}

// Build ranking list for overall tab (sorted by total_score DESC)
$overallRanking = array_values($duoTableData);
usort($overallRanking, function ($a, $b) {
    return $b['total_score'] <=> $a['total_score'];
});
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Doubles Score Table - VIPERS VENOMS Bowling System</title>
  <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
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
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
    .team-avatars {
      display: flex;
      align-items: center;
    }
    .team-avatars img:first-child {
      margin-right: -10px;
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
    /* Table stability improvements */
    .table-responsive {
      min-height: 200px;
      transition: all 0.3s ease;
    }
    .table tbody tr {
      transition: all 0.3s ease;
    }
    .table tbody tr:hover {
      transform: none;
    }
    /* Prevent layout shifts during updates */
    .table tbody {
      position: relative;
    }
    .table tbody tr[data-duo-id] {
      will-change: transform, opacity;
    }
    /* Smooth transitions for score updates */
    .score-update {
      animation: scoreUpdate 0.5s ease-in-out;
    }
    @keyframes scoreUpdate {
      0% { background-color: rgba(40, 167, 69, 0.1); }
      100% { background-color: transparent; }
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
                    <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Table</a></li>
                    <li class="breadcrumb-item active">Doubles</li>
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
                      <h5 class="card-title fw-semibold mb-1">
                        <?php echo $selectedSession ? htmlspecialchars($selectedSession['session_name']) : 'Doubles Score Table'; ?>
                      </h5>
                      <span class="fw-normal text-muted">
                        <?php 
                        if ($selectedSession) {
                          echo date('l, F j, Y', strtotime($selectedSession['session_date']));
                        } else {
                          echo 'Two-player team rankings and scores';
                        }
                        ?>
                      </span>
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
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game6-tab" data-bs-toggle="tab" data-bs-target="#game6" type="button" role="tab">
                        Game 6
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
                              <th scope="col">Lane</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Pin Diff</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Combined Strikes</th>
                              <th scope="col">Last Updated</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!$selectedSession || empty($overallRanking)): ?>
                              <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                  No doubles results available yet.
                              </td>
                            </tr>
                            <?php else: ?>
                              <?php 
                              $rank = 1;
                              $firstPlaceScore = !empty($overallRanking) ? $overallRanking[0]['total_score'] : 0;
                              ?>
                              <?php foreach ($overallRanking as $duo): ?>
                                <?php
                                  $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                                  $pinDiff = $duo['total_score'] - $firstPlaceScore;
                                ?>
                                <tr>
                                  <td><span class="rank-badge <?php echo $rankClass; ?>"><?php echo $rank; ?></span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                        <?php
                                          $basePath = defined('BASE_PATH') ? BASE_PATH : '';
                                          $p1Pic = (!empty($duo['players'][0]['pic']) && $duo['players'][0]['pic'] !== 'default-avatar.png')
                                            ? $basePath . '/uploads/profile_pictures/' . $duo['players'][0]['pic']
                                            : $basePath . '/assets/images/profile/user-' . (($duo['players'][0]['id'] % 8) + 1) . '.jpg';
                                          $p2Pic = (!empty($duo['players'][1]['pic']) && $duo['players'][1]['pic'] !== 'default-avatar.png')
                                            ? $basePath . '/uploads/profile_pictures/' . $duo['players'][1]['pic']
                                            : $basePath . '/assets/images/profile/user-' . (($duo['players'][1]['id'] % 8) + 1) . '.jpg';
                                        ?>
                                        <img src="<?php echo htmlspecialchars($p1Pic); ?>" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="<?php echo htmlspecialchars($p2Pic); ?>" alt="Player 2" class="rounded-circle border border-2 border-white" width="32">
                                  </div>
                                  <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($duo['duo_name'] ?: 'Duo #' . $duo['duo_id']); ?></h6>
                                  </div>
                                </div>
                              </td>
                                  <td>
                                    <small>
                                      <?php echo htmlspecialchars($duo['players'][0]['name']); ?>
                                      <br>
                                      <?php echo htmlspecialchars($duo['players'][1]['name']); ?>
                                    </small>
                              </td>
                                  <td><span class="badge bg-primary">Lane <?php echo $duo['lane_number'] ?: '-'; ?></span></td>
                                  <td><span class="fw-bold text-success"><?php echo (int)$duo['total_score']; ?></span></td>
                                  <td>
                                    <?php if ($pinDiff === 0): ?>
                                      <span class="badge bg-success">Leader</span>
                                    <?php else: ?>
                                      <span class="text-danger"><?php echo $pinDiff; ?></span>
                                    <?php endif; ?>
                              </td>
                                  <td>
                                    <?php
                                      $avg = $duo['games_played'] > 0
                                        ? round($duo['total_score'] / $duo['games_played'], 1)
                                        : 0;
                                      echo $avg;
                                    ?>
                              </td>
                                  <td><?php echo (int)$duo['games_played']; ?>/6</td>
                                  <td><span class="text-warning"><?php echo (int)$duo['best_game']; ?></span></td>
                                  <td><?php echo (int)$duo['combined_strikes']; ?></td>
                                  <td>
                                    <small class="text-muted">
                                      <?php echo $duo['last_updated'] ? date('g:i A', strtotime($duo['last_updated'])) : '-'; ?>
                                    </small>
                                  </td>
                            </tr>
                                <?php $rank++; ?>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <?php
                    // Function to render game tab
                    function renderGameTab($gameNumber, $duoTableData) {
                      // Get scores for this game
                      $gameScores = [];
                      foreach ($duoTableData as $duo) {
                        if (isset($duo['games'][$gameNumber])) {
                          $gameScores[] = [
                            'duo' => $duo,
                            'score' => $duo['games'][$gameNumber]['score'],
                            'strikes' => $duo['games'][$gameNumber]['strikes'],
                            'spares' => $duo['games'][$gameNumber]['spares'],
                            'open_frames' => $duo['games'][$gameNumber]['open_frames'],
                            'time' => $duo['games'][$gameNumber]['time']
                          ];
                        }
                      }
                      
                      // Sort by score DESC
                      usort($gameScores, function($a, $b) {
                        return $b['score'] <=> $a['score'];
                      });
                      
                      $basePath = defined('BASE_PATH') ? BASE_PATH : '';
                      ?>
                      <div class="tab-pane fade" id="game<?php echo $gameNumber; ?>" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                                <th scope="col">Lane</th>
                              <th scope="col">Score</th>
                                <th scope="col">Pin Diff</th>
                                <th scope="col">Strikes</th>
                                <th scope="col">Spares</th>
                                <th scope="col">Open Frames</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                              <?php if (empty($gameScores)): ?>
                                <tr>
                                  <td colspan="10" class="text-center text-muted py-4">
                                    No scores available for Game <?php echo $gameNumber; ?> yet.
                              </td>
                            </tr>
                              <?php else:
                                $rank = 1;
                                $firstScore = $gameScores[0]['score'];
                                foreach ($gameScores as $entry):
                                  $duo = $entry['duo'];
                                  $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                                  $pinDiff = $entry['score'] - $firstScore;
                                  $p1Pic = (!empty($duo['players'][0]['pic']) && $duo['players'][0]['pic'] !== 'default-avatar.png')
                                    ? $basePath . '/uploads/profile_pictures/' . $duo['players'][0]['pic']
                                    : $basePath . '/assets/images/profile/user-' . (($duo['players'][0]['id'] % 8) + 1) . '.jpg';
                                  $p2Pic = (!empty($duo['players'][1]['pic']) && $duo['players'][1]['pic'] !== 'default-avatar.png')
                                    ? $basePath . '/uploads/profile_pictures/' . $duo['players'][1]['pic']
                                    : $basePath . '/assets/images/profile/user-' . (($duo['players'][1]['id'] % 8) + 1) . '.jpg';
                              ?>
                                <tr>
                                  <td><span class="rank-badge <?php echo $rankClass; ?>"><?php echo $rank; ?></span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                        <img src="<?php echo htmlspecialchars($p1Pic); ?>" alt="Player 1" class="rounded-circle border border-2 border-white" width="32" style="margin-right: -8px;">
                                        <img src="<?php echo htmlspecialchars($p2Pic); ?>" alt="Player 2" class="rounded-circle border border-2 border-white" width="32">
                                  </div>
                                  <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($duo['duo_name'] ?: 'Duo #' . $duo['duo_id']); ?></h6>
                                  </div>
                                </div>
                              </td>
                                  <td>
                                    <small>
                                      <?php echo htmlspecialchars($duo['players'][0]['name']); ?>
                                      <br>
                                      <?php echo htmlspecialchars($duo['players'][1]['name']); ?>
                                    </small>
                              </td>
                                  <td><span class="badge bg-primary">Lane <?php echo $duo['lane_number'] ?: '-'; ?></span></td>
                                  <td><span class="fw-bold text-success"><?php echo $entry['score']; ?></span></td>
                                  <td>
                                    <?php if ($pinDiff === 0): ?>
                                      <span class="badge bg-success">Leader</span>
                                    <?php else: ?>
                                      <span class="text-danger"><?php echo $pinDiff; ?></span>
                                    <?php endif; ?>
                                  </td>
                                  <td><?php echo $entry['strikes']; ?></td>
                                  <td><?php echo $entry['spares']; ?></td>
                                  <td><?php echo $entry['open_frames']; ?></td>
                                  <td><small class="text-muted"><?php echo date('g:i A', strtotime($entry['time'])); ?></small></td>
                            </tr>
                              <?php 
                                $rank++;
                                endforeach;
                              endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                      <?php
                    }
                    
                    // Render Game 1-6 tabs
                    if (isset($duoTableData) && is_array($duoTableData)) {
                      for ($gameNum = 1; $gameNum <= 6; $gameNum++) {
                        renderGameTab($gameNum, $duoTableData);
                      }
                    } else {
                      // Fallback if $duoTableData is not set
                      for ($gameNum = 1; $gameNum <= 6; $gameNum++) {
                        echo '<div class="tab-pane fade" id="game' . $gameNum . '" role="tabpanel">';
                        echo '<div class="table-responsive"><table class="table table-hover"><tbody>';
                        echo '<tr><td colspan="10" class="text-center text-muted py-4">No data available</td></tr>';
                        echo '</tbody></table></div></div>';
                      }
                    }
                    ?>
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
  </script>

  <!-- Score Table Functionality -->
  <script>
    // Change session
    function changeSession(sessionId) {
      if (sessionId) {
        window.location.href = 'score-table-doubles.php?session_id=' + sessionId;
      }
    }

    // Refresh table functionality
    function refreshTable() {
      location.reload();
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
  
  <?php include 'includes/admin-popup.php'; ?>
</body>

</html>
