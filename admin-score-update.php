<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Scores - SPEEDSTERS Bowling System</title>
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
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">

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
              <a class="sidebar-link" href="./admin-dashboard.php">
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
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-chart-bar"></i>
                <span class="hide-menu">Score Monitoring</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-solo.php" class="sidebar-link">
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
              <a class="sidebar-link active" href="./admin-score-update.php">
                <i class="ti ti-edit"></i>
                <span class="hide-menu">Update Scores</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-create-account.php">
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
                    <li class="breadcrumb-item active">Update Scores</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Score Update Section -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Update Player Scores</h5>
                      <span class="fw-normal text-muted">Enter and update bowling scores for players and teams</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkScoreUpdateModal">
                        <i class="ti ti-upload me-2"></i>
                        Bulk Update
                      </button>
                      <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#viewScoreHistoryModal">
                        <i class="ti ti-history me-2"></i>
                        Score History
                      </button>
                    </div>
                  </div>

                  <!-- Score Update Form -->
                  <form id="updateScoreForm">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="scorePlayer" class="form-label">Select Player/Team</label>
                        <select class="form-select" id="scorePlayer" required>
                          <option value="">Choose Player or Team</option>
                          <optgroup label="Solo Players">
                            <option value="player1">Mike Johnson</option>
                            <option value="player2">Sarah Wilson</option>
                            <option value="player3">Alex Rodriguez</option>
                          </optgroup>
                          <optgroup label="Doubles Teams">
                            <option value="team1">Thunder Strikers (John & Sarah)</option>
                            <option value="team2">Lightning Bolts (Mike & Alex)</option>
                          </optgroup>
                          <optgroup label="Teams (4-6 Players)">
                            <option value="team3">Lane Masters (Tom, Emma, Alex, Maria)</option>
                            <option value="team4">Pin Crushers (5 players)</option>
                          </optgroup>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="scoreGame" class="form-label">Game Type</label>
                        <select class="form-select" id="scoreGame" required>
                          <option value="">Select Game Type</option>
                          <option value="practice">Practice Game</option>
                          <option value="tournament">Tournament</option>
                          <option value="league">League Match</option>
                          <option value="friendly">Friendly Match</option>
                        </select>
                      </div>
                    </div>
                    
                    <!-- Solo Player Score Input -->
                    <div id="soloScoreInput">
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <label for="scoreGame1" class="form-label">Game 1 Score</label>
                          <input type="number" class="form-control" id="scoreGame1" min="0" max="300" placeholder="0-300">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="scoreGame2" class="form-label">Game 2 Score</label>
                          <input type="number" class="form-control" id="scoreGame2" min="0" max="300" placeholder="0-300">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="scoreGame3" class="form-label">Game 3 Score</label>
                          <input type="number" class="form-control" id="scoreGame3" min="0" max="300" placeholder="0-300">
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label for="scoreGame4" class="form-label">Game 4 Score</label>
                          <input type="number" class="form-control" id="scoreGame4" min="0" max="300" placeholder="0-300">
                        </div>
                        <div class="col-md-6 mb-3">
                          <label for="scoreGame5" class="form-label">Game 5 Score</label>
                          <input type="number" class="form-control" id="scoreGame5" min="0" max="300" placeholder="0-300">
                        </div>
                      </div>
                    </div>

                    <!-- Team Score Input -->
                    <div id="teamScoreInput" style="display: none;">
                      <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Team Score Entry:</strong> Enter scores for each team member. Team total will be calculated automatically.
                      </div>
                      
                      <!-- Team Member Cards will be populated here -->
                      <div id="teamMembersContainer">
                        <!-- Team members will be dynamically added here -->
                      </div>

                      <!-- Team Total Summary -->
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <h6 class="mb-2">Team Total Score</h6>
                              <input type="number" class="form-control" id="teamTotalScore" readonly>
                            </div>
                            <div class="col-md-6">
                              <h6 class="mb-2">Team Average per Player</h6>
                              <input type="number" class="form-control" id="teamAverageScore" readonly step="0.1">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="scoreTotal" class="form-label">Total Score</label>
                        <input type="number" class="form-control" id="scoreTotal" readonly>
                        <div class="form-text">Auto-calculated from individual games</div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="scoreDate" class="form-label">Game Date</label>
                        <input type="datetime-local" class="form-control" id="scoreDate" required>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label for="scoreNotes" class="form-label">Notes (Optional)</label>
                      <textarea class="form-control" id="scoreNotes" rows="3" placeholder="Any additional notes about the game..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                      <i class="ti ti-info-circle me-2"></i>
                      <strong>Note:</strong> Scores will be automatically validated and added to the player's/team's statistics.
                    </div>
                  </form>

                  <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-secondary" onclick="clearForm()">Clear Form</button>
                    <button class="btn btn-warning" onclick="updateScore()">Update Score</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Include modals and scripts -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Score Update Functions
    function updateScore() {
      const form = document.getElementById('updateScoreForm');
      const player = document.getElementById('scorePlayer').value;
      const gameType = document.getElementById('scoreGame').value;
      const gameDate = document.getElementById('scoreDate').value;
      
      if (!player || !gameType || !gameDate) {
        showNotification('Please fill in all required fields', 'warning');
        return;
      }
      
      const isTeam = player.startsWith('team');
      
      if (isTeam) {
        if (!validateTeamScores()) {
          return;
        }
      } else {
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
      
      setTimeout(() => {
        showNotification('Score updated successfully!', 'success');
        clearForm();
      }, 1000);
    }

    function validateTeamScores() {
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

    function clearForm() {
      document.getElementById('updateScoreForm').reset();
      document.getElementById('scoreTotal').value = '';
      document.getElementById('teamTotalScore').value = '';
      document.getElementById('teamAverageScore').value = '';
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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
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
</body>

</html>
