<?php
// Auth / DB
require_once 'includes/auth.php';
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: authentication-login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all Doubles sessions this user is participating in
$duoSessions = [];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT 
            gs.session_id,
            gs.session_name,
            gs.session_date,
            gs.game_mode,
            gs.status,
            gs.max_players,
            (
                SELECT COUNT(DISTINCT user_id) 
                FROM session_participants 
                WHERE session_id = gs.session_id
            ) AS participants_count
        FROM game_sessions gs
        INNER JOIN session_participants sp 
            ON gs.session_id = sp.session_id
        WHERE gs.game_mode = 'Doubles'
          AND gs.status IN ('Pending', 'Active', 'Scheduled')
          AND sp.user_id = ?
        ORDER BY gs.session_date DESC
    ");
    $stmt->execute([$userId]);
    $duoSessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching duo sessions in group-selection.php: ' . $e->getMessage());
    $duoSessions = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Group Selection - VipersVenoms Bowling</title>
  <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .group-card {
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .group-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .group-card.selected {
      border: 2px solid #007bff;
      background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    }
    .skill-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .player-count {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .group-stats {
      background: rgba(0,123,255,0.1);
      border-radius: 8px;
      padding: 1rem;
    }
    .join-button {
      width: 100%;
      margin-top: 1rem;
    }
    .filter-section {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    .search-box {
      position: relative;
    }
    .search-box .ti {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
    .search-box input {
      padding-left: 40px;
    }
    
    /* Group Logo Styles */
    .group-logo {
      position: relative;
    }
    
    .logo-circle {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
      position: relative;
      overflow: hidden;
    }
    
    .logo-circle::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      animation: logoShine 3s infinite;
    }
    
    .logo-circle i {
      font-size: 24px;
      color: white;
      z-index: 1;
      position: relative;
    }
    
    @keyframes logoShine {
      0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
      50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
      100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    }
    
    .logo-circle:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
      transition: all 0.3s ease;
    }
    
    /* Team Type Card Styles */
    .team-type-card {
      transition: all 0.3s ease;
      cursor: pointer;
      border: 2px solid transparent;
    }
    .team-type-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .team-type-card.selected {
      border-color: #007bff;
      background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">
    <?php include 'includes/app-topstrip.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main wrapper -->
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
                    <li class="breadcrumb-item active">Join Group</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Team Type Selection -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <div class="group-logo me-3">
                      <div class="logo-circle">
                        <i class="ti ti-users-group"></i>
                      </div>
                    </div>
                    <div>
                      <h4 class="fw-semibold mb-1">Join a Bowling Team</h4>
                      <span class="fw-normal text-muted">First, select your preferred team size</span>
                    </div>
                  </div>
                
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="card team-type-card" onclick="selectTeamType('duo')" id="duoCard">
                      <div class="card-body text-center">
                        <i class="ti ti-users fs-1 text-primary mb-3"></i>
                        <h6 class="card-title">Duo Team</h6>
                        <p class="text-muted small">2 players per team</p>
                        <span class="badge bg-primary">2 Players</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="card team-type-card" onclick="selectTeamType('trio')" id="trioCard">
                      <div class="card-body text-center">
                        <i class="ti ti-users-group fs-1 text-success mb-3"></i>
                        <h6 class="card-title">Trio Team</h6>
                        <p class="text-muted small">3 players per team</p>
                        <span class="badge bg-success">3 Players</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="card team-type-card" onclick="selectTeamType('team')" id="teamCard">
                      <div class="card-body text-center">
                        <i class="ti ti-users-group fs-1 text-warning mb-3"></i>
                        <h6 class="card-title">Team</h6>
                        <p class="text-muted small">10 or More players per team</p>
                        <span class="badge bg-warning">10 or More Players</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Skill Level Display -->
        <div class="row mb-4" id="skillDisplaySection" style="display: none;">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-3">Your Bowling Profile</h5>
                <p class="text-muted mb-3">Based on your bowling history, you've been placed in the following skill group:</p>
                
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                      <h4 class="text-primary mb-1" id="userAverageScore">-</h4>
                      <small class="text-muted">Average Score</small>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                      <h4 class="text-success mb-1" id="userSkillGroup">-</h4>
                      <small class="text-muted">Skill Group</small>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                      <h4 class="text-warning mb-1" id="userGamesPlayed">-</h4>
                      <small class="text-muted">Games Played</small>
                    </div>
                  </div>
                </div>
                
                <div class="alert alert-info">
                  <h6 class="alert-heading">Skill Group Classification:</h6>
                  <div class="row">
                    <div class="col-md-6">
                      <small>
                        <strong>A:</strong> 200-300 (Professional)<br>
                        <strong>B:</strong> 180-199 (Advanced)<br>
                        <strong>C:</strong> 160-179 (Above Average)<br>
                        <strong>D:</strong> 140-159 (Intermediate)
                      </small>
                    </div>
                    <div class="col-md-6">
                      <small>
                        <strong>E:</strong> 120-139 (Casual)<br>
                        <strong>F:</strong> 100-119 (Beginner)<br>
                        <strong>G:</strong> 80-99 (New/Inexperienced)<br>
                        <strong>H:</strong> Below 80 (Absolute Beginner)
                      </small>
                    </div>
                  </div>
                </div>
                
                <div class="d-flex gap-2">
                  <button class="btn btn-primary" onclick="findMatchingGroups()">
                    <i class="ti ti-search me-1"></i>
                    Find Matching Groups
                  </button>
                  <button class="btn btn-outline-secondary" onclick="resetTeamSelection()">
                    <i class="ti ti-arrow-left me-1"></i>
                    Back to Team Selection
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Groups Display Section -->
        <div id="groupsDisplaySection" style="display: none;">
          <!-- Page Header -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <h5 class="fw-semibold mb-1">Available Groups for <span id="selectedTeamType"></span></h5>
                  <span class="fw-normal text-muted">Groups matching your skill level: <span id="userSkillGroup" class="badge bg-primary"></span></span>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-outline-primary" onclick="refreshGroups()">
                    <i class="ti ti-refresh me-1"></i>
                    Refresh
                  </button>
                  <button class="btn btn-primary" onclick="requestRandomGroup()">
                    <i class="ti ti-dice me-1"></i>
                    Random Group
                  </button>
                </div>
              </div>
            </div>
          </div>

        <!-- Filter Section -->
        <div class="filter-section">
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="search-box">
                <i class="ti ti-search"></i>
                <input type="text" class="form-control" id="searchGroups" placeholder="Search groups..." onkeyup="filterGroups()">
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <select class="form-select" id="skillFilter" onchange="filterGroups()">
                <option value="">All Skill Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="pro">Professional</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <select class="form-select" id="availabilityFilter" onchange="filterGroups()">
                <option value="">All Availability</option>
                <option value="available">Available Spots</option>
                <option value="full">Full Groups</option>
              </select>
            </div>
            <div class="col-md-2 mb-3">
              <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                <i class="ti ti-x me-1"></i>
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Groups Grid -->
        <div class="row" id="groupsContainer">
          <!-- Groups will be dynamically populated here -->
        </div>

        <!-- Selected Group Summary -->
        <div class="row mt-4" id="selectedGroupSummary" style="display: none;">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="ti ti-check-circle me-2"></i>Selected Group</h5>
              </div>
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h6 class="mb-1" id="selectedGroupName">Group Name</h6>
                    <p class="text-muted mb-2" id="selectedGroupDescription">Group description</p>
                    <div class="d-flex gap-3">
                      <span class="badge bg-primary" id="selectedGroupSkill">Skill Level</span>
                      <span class="text-muted">
                        <i class="ti ti-users me-1"></i>
                        <span id="selectedGroupCount">0</span>/<span id="selectedGroupMax">0</span> players
                      </span>
                      <span class="text-muted">
                        <i class="ti ti-target me-1"></i>
                        Avg: <span id="selectedGroupAvg">0</span>
                      </span>
                    </div>
                  </div>
                  <div class="col-md-4 text-end">
                    <button class="btn btn-success btn-lg" onclick="joinSelectedGroup()">
                      <i class="ti ti-user-plus me-2"></i>
                      Join Group
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Random Group Assignment Modal -->
  <div class="modal fade" id="randomGroupModal" tabindex="-1" aria-labelledby="randomGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="randomGroupModalLabel">
            <i class="ti ti-dice me-2"></i>
            Random Group Assignment
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="mb-4">
            <i class="ti ti-dice fs-1 text-primary"></i>
          </div>
          <h6 class="mb-3">You've been assigned to:</h6>
          <div class="card border-primary">
            <div class="card-body">
              <h5 class="text-primary mb-2" id="randomGroupName">Group Name</h5>
              <p class="text-muted mb-2" id="randomGroupDesc">Group description</p>
              <div class="d-flex justify-content-center gap-3">
                <span class="badge bg-primary" id="randomGroupSkill">Skill Level</span>
                <span class="text-muted">
                  <i class="ti ti-users me-1"></i>
                  <span id="randomGroupCount">0</span>/<span id="randomGroupMax">0</span> players
                </span>
              </div>
            </div>
          </div>
          <p class="text-muted mt-3">This group was randomly selected based on available spots and your skill level.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="confirmRandomGroup()">
            <i class="ti ti-check me-1"></i>
            Join This Group
          </button>
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
  
  <!-- Countdown Timer Script (safe on pages without countdown elements) -->
  <script>
    // Set the target date for the tournament (you can change this)
    const targetDate = new Date('2025-03-15T18:00:00').getTime();
    
    function updateCountdown() {
      // Safely get elements; if they don't exist, do nothing
      const daysEl = document.getElementById('days');
      const hoursEl = document.getElementById('hours');
      const minutesEl = document.getElementById('minutes');
      const secondsEl = document.getElementById('seconds');

      if (!daysEl || !hoursEl || !minutesEl || !secondsEl) {
        // This page doesn't have countdown elements, so skip
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
    
    // Only start countdown if elements exist on the page
    document.addEventListener('DOMContentLoaded', function () {
      if (document.getElementById('days') &&
          document.getElementById('hours') &&
          document.getElementById('minutes') &&
          document.getElementById('seconds')) {
    updateCountdown();
        setInterval(updateCountdown, 1000);
      }
    });
  </script>
  
  <script>
    // Sample groups data organized by skill groups and team types (used for Trio/Team demos)
    const availableGroups = {
      duo: {
        A: [
          { id: 1, name: 'Pro Duos', skillGroup: 'A', avgScore: 250, playerCount: 1, maxPlayers: 2, description: 'Professional level duo teams', available: true },
          { id: 2, name: 'Elite Pairs', skillGroup: 'A', avgScore: 240, playerCount: 1, maxPlayers: 2, description: 'Advanced professional duos', available: true }
        ],
        B: [
          { id: 3, name: 'Advanced Duos', skillGroup: 'B', avgScore: 190, playerCount: 1, maxPlayers: 2, description: 'Advanced level duo teams', available: true },
          { id: 4, name: 'Skilled Pairs', skillGroup: 'B', avgScore: 185, playerCount: 1, maxPlayers: 2, description: 'Skilled duo partnerships', available: true }
        ],
        C: [
          { id: 5, name: 'Above Average Duos', skillGroup: 'C', avgScore: 170, playerCount: 1, maxPlayers: 2, description: 'Above average duo teams', available: true },
          { id: 6, name: 'Improving Pairs', skillGroup: 'C', avgScore: 165, playerCount: 1, maxPlayers: 2, description: 'Duos working on consistency', available: true }
        ],
        D: [
          { id: 7, name: 'Intermediate Duos', skillGroup: 'D', avgScore: 150, playerCount: 1, maxPlayers: 2, description: 'Intermediate level duos', available: true },
          { id: 8, name: 'Developing Pairs', skillGroup: 'D', avgScore: 145, playerCount: 1, maxPlayers: 2, description: 'Developing duo teams', available: true }
        ],
        E: [
          { id: 9, name: 'Casual Duos', skillGroup: 'E', avgScore: 130, playerCount: 1, maxPlayers: 2, description: 'Casual bowling duos', available: true },
          { id: 10, name: 'Fun Pairs', skillGroup: 'E', avgScore: 125, playerCount: 1, maxPlayers: 2, description: 'Fun-loving duo teams', available: true }
        ],
        F: [
          { id: 11, name: 'Beginner Duos', skillGroup: 'F', avgScore: 110, playerCount: 1, maxPlayers: 2, description: 'Beginner level duos', available: true },
          { id: 12, name: 'Learning Pairs', skillGroup: 'F', avgScore: 105, playerCount: 1, maxPlayers: 2, description: 'Learning duo teams', available: true }
        ],
        G: [
          { id: 13, name: 'New Duos', skillGroup: 'G', avgScore: 90, playerCount: 1, maxPlayers: 2, description: 'New to bowling duos', available: true },
          { id: 14, name: 'Fresh Pairs', skillGroup: 'G', avgScore: 85, playerCount: 1, maxPlayers: 2, description: 'Fresh duo teams', available: true }
        ],
        H: [
          { id: 15, name: 'Starting Duos', skillGroup: 'H', avgScore: 70, playerCount: 1, maxPlayers: 2, description: 'Just starting out duos', available: true },
          { id: 16, name: 'First Pairs', skillGroup: 'H', avgScore: 65, playerCount: 1, maxPlayers: 2, description: 'First-time duo teams', available: true }
        ]
      },
      trio: {
        A: [
          { id: 17, name: 'Pro Trios', skillGroup: 'A', avgScore: 250, playerCount: 2, maxPlayers: 3, description: 'Professional level trio teams', available: true },
          { id: 18, name: 'Elite Threes', skillGroup: 'A', avgScore: 240, playerCount: 2, maxPlayers: 3, description: 'Advanced professional trios', available: true }
        ],
        B: [
          { id: 19, name: 'Advanced Trios', skillGroup: 'B', avgScore: 190, playerCount: 2, maxPlayers: 3, description: 'Advanced level trio teams', available: true },
          { id: 20, name: 'Skilled Threes', skillGroup: 'B', avgScore: 185, playerCount: 2, maxPlayers: 3, description: 'Skilled trio partnerships', available: true }
        ],
        C: [
          { id: 21, name: 'Above Average Trios', skillGroup: 'C', avgScore: 170, playerCount: 2, maxPlayers: 3, description: 'Above average trio teams', available: true },
          { id: 22, name: 'Improving Threes', skillGroup: 'C', avgScore: 165, playerCount: 2, maxPlayers: 3, description: 'Trios working on consistency', available: true }
        ],
        D: [
          { id: 23, name: 'Intermediate Trios', skillGroup: 'D', avgScore: 150, playerCount: 2, maxPlayers: 3, description: 'Intermediate level trios', available: true },
          { id: 24, name: 'Developing Threes', skillGroup: 'D', avgScore: 145, playerCount: 2, maxPlayers: 3, description: 'Developing trio teams', available: true }
        ],
        E: [
          { id: 25, name: 'Casual Trios', skillGroup: 'E', avgScore: 130, playerCount: 2, maxPlayers: 3, description: 'Casual bowling trios', available: true },
          { id: 26, name: 'Fun Threes', skillGroup: 'E', avgScore: 125, playerCount: 2, maxPlayers: 3, description: 'Fun-loving trio teams', available: true }
        ],
        F: [
          { id: 27, name: 'Beginner Trios', skillGroup: 'F', avgScore: 110, playerCount: 2, maxPlayers: 3, description: 'Beginner level trios', available: true },
          { id: 28, name: 'Learning Threes', skillGroup: 'F', avgScore: 105, playerCount: 2, maxPlayers: 3, description: 'Learning trio teams', available: true }
        ],
        G: [
          { id: 29, name: 'New Trios', skillGroup: 'G', avgScore: 90, playerCount: 2, maxPlayers: 3, description: 'New to bowling trios', available: true },
          { id: 30, name: 'Fresh Threes', skillGroup: 'G', avgScore: 85, playerCount: 2, maxPlayers: 3, description: 'Fresh trio teams', available: true }
        ],
        H: [
          { id: 31, name: 'Starting Trios', skillGroup: 'H', avgScore: 70, playerCount: 2, maxPlayers: 3, description: 'Just starting out trios', available: true },
          { id: 32, name: 'First Threes', skillGroup: 'H', avgScore: 65, playerCount: 2, maxPlayers: 3, description: 'First-time trio teams', available: true }
        ]
      },
      team: {
        A: [
          { id: 33, name: 'Pro Teams', skillGroup: 'A', avgScore: 250, playerCount: 4, maxPlayers: 6, description: 'Professional level teams', available: true },
          { id: 34, name: 'Elite Squads', skillGroup: 'A', avgScore: 240, playerCount: 5, maxPlayers: 6, description: 'Advanced professional teams', available: true }
        ],
        B: [
          { id: 35, name: 'Advanced Teams', skillGroup: 'B', avgScore: 190, playerCount: 4, maxPlayers: 6, description: 'Advanced level teams', available: true },
          { id: 36, name: 'Skilled Squads', skillGroup: 'B', avgScore: 185, playerCount: 5, maxPlayers: 6, description: 'Skilled team partnerships', available: true }
        ],
        C: [
          { id: 37, name: 'Above Average Teams', skillGroup: 'C', avgScore: 170, playerCount: 4, maxPlayers: 6, description: 'Above average teams', available: true },
          { id: 38, name: 'Improving Squads', skillGroup: 'C', avgScore: 165, playerCount: 5, maxPlayers: 6, description: 'Teams working on consistency', available: true }
        ],
        D: [
          { id: 39, name: 'Intermediate Teams', skillGroup: 'D', avgScore: 150, playerCount: 4, maxPlayers: 6, description: 'Intermediate level teams', available: true },
          { id: 40, name: 'Developing Squads', skillGroup: 'D', avgScore: 145, playerCount: 5, maxPlayers: 6, description: 'Developing teams', available: true }
        ],
        E: [
          { id: 41, name: 'Casual Teams', skillGroup: 'E', avgScore: 130, playerCount: 4, maxPlayers: 6, description: 'Casual bowling teams', available: true },
          { id: 42, name: 'Fun Squads', skillGroup: 'E', avgScore: 125, playerCount: 5, maxPlayers: 6, description: 'Fun-loving teams', available: true }
        ],
        F: [
          { id: 43, name: 'Beginner Teams', skillGroup: 'F', avgScore: 110, playerCount: 4, maxPlayers: 6, description: 'Beginner level teams', available: true },
          { id: 44, name: 'Learning Squads', skillGroup: 'F', avgScore: 105, playerCount: 5, maxPlayers: 6, description: 'Learning teams', available: true }
        ],
        G: [
          { id: 45, name: 'New Teams', skillGroup: 'G', avgScore: 90, playerCount: 4, maxPlayers: 6, description: 'New to bowling teams', available: true },
          { id: 46, name: 'Fresh Squads', skillGroup: 'G', avgScore: 85, playerCount: 5, maxPlayers: 6, description: 'Fresh teams', available: true }
        ],
        H: [
          { id: 47, name: 'Starting Teams', skillGroup: 'H', avgScore: 70, playerCount: 4, maxPlayers: 6, description: 'Just starting out teams', available: true },
          { id: 48, name: 'First Squads', skillGroup: 'H', avgScore: 65, playerCount: 5, maxPlayers: 6, description: 'First-time teams', available: true }
        ]
      }
    };

    // Real duo sessions for the logged-in player (from PHP)
    const duoSessions = <?php echo json_encode($duoSessions); ?> || [];

    let selectedTeamType = null;
    let userSkillGroup = null;
    let userScore = null;
    let selectedGroupId = null;
    let filteredGroups = [];

    // Duo session/lobby state
    let currentSessionId = null;
    let currentDuoId = null;
    let duoPollInterval = null;

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      // Page starts with team type selection
      // Load user's bowling history automatically
      loadUserBowlingHistory();
    });

    // Team type selection functions
    function selectTeamType(type) {
      selectedTeamType = type;
      
      // Update visual selection
      document.querySelectorAll('.team-type-card').forEach(card => {
        card.classList.remove('selected');
      });
      document.getElementById(type + 'Card').classList.add('selected');
      
      // Show skill display section
      document.getElementById('skillDisplaySection').style.display = 'block';
      
      // Scroll to skill display
      document.getElementById('skillDisplaySection').scrollIntoView({ behavior: 'smooth' });
    }

    // Sample user bowling history (in real app, this would come from database)
    const userBowlingHistory = {
      averageScore: 165,
      gamesPlayed: 24,
      recentScores: [180, 155, 170, 160, 175, 150, 185, 165, 170, 160],
      skillGroup: 'C' // Will be calculated automatically
    };

    function loadUserBowlingHistory() {
      // Calculate skill group based on average score
      const avgScore = userBowlingHistory.averageScore;
      
      if (avgScore >= 200) {
        userSkillGroup = 'A';
      } else if (avgScore >= 180) {
        userSkillGroup = 'B';
      } else if (avgScore >= 160) {
        userSkillGroup = 'C';
      } else if (avgScore >= 140) {
        userSkillGroup = 'D';
      } else if (avgScore >= 120) {
        userSkillGroup = 'E';
      } else if (avgScore >= 100) {
        userSkillGroup = 'F';
      } else if (avgScore >= 80) {
        userSkillGroup = 'G';
      } else {
        userSkillGroup = 'H';
      }
      
      userScore = avgScore;
      
      // Update the display elements
      document.getElementById('userAverageScore').textContent = avgScore;
      document.getElementById('userSkillGroup').textContent = userSkillGroup;
      document.getElementById('userGamesPlayed').textContent = userBowlingHistory.gamesPlayed;
      
      // Update userBowlingHistory object
      userBowlingHistory.skillGroup = userSkillGroup;
    }

    function findMatchingGroups() {
      if (!selectedTeamType) {
        alert('Please select a team type first.');
        return;
      }

      // Duo flow: show real sessions list
      if (selectedTeamType === 'duo') {
        loadDuoSessions();
        return;
      }

      // Trio / Team: keep existing demo behaviour
      if (!userSkillGroup) {
        alert('Please wait for your skill level to be calculated.');
        return;
      }
      
      // Get groups for the selected team type and skill group
      filteredGroups = availableGroups[selectedTeamType][userSkillGroup] || [];
      
      // Update display
      document.getElementById('selectedTeamType').textContent = getTeamTypeName(selectedTeamType);
      document.getElementById('userSkillGroup').textContent = userSkillGroup;
      
      // Show groups section
      document.getElementById('groupsDisplaySection').style.display = 'block';
      
      // Populate groups
      populateGroups();
      
      // Scroll to groups
      document.getElementById('groupsDisplaySection').scrollIntoView({ behavior: 'smooth' });
    }

    // ==============================
    // Duo Sessions – real data
    // ==============================
    function loadDuoSessions() {
      const groupsSection = document.getElementById('groupsDisplaySection');
      const container = document.getElementById('groupsContainer');

      if (!groupsSection || !container) {
        console.error('groupsDisplaySection or groupsContainer not found');
        alert('Unable to show sessions right now. Please refresh the page.');
        return;
      }

      // Clear previous groups
      filteredGroups = [];
      container.innerHTML = '';

      if (!duoSessions || duoSessions.length === 0) {
        // No sessions this player is in
        groupsSection.style.display = 'block';
        container.innerHTML = `
          <div class="col-12">
            <div class="card">
              <div class="card-body text-center py-5">
                <i class="ti ti-alert-circle fs-1 text-warning mb-3"></i>
                <h5 class="text-warning mb-2">No Doubles Sessions Found</h5>
                <p class="text-muted mb-0">
                  You are not currently registered in any upcoming Doubles sessions.
                  Please ask the admin to add you to a Doubles session first.
                </p>
              </div>
            </div>
          </div>
        `;
        groupsSection.style.display = 'block';
        groupsSection.scrollIntoView({ behavior: 'smooth' });
        return;
      }

      // Map backend sessions into the generic group format used by populateGroups()
      filteredGroups = duoSessions.map(session => {
        const sessionDate = session.session_date ? new Date(session.session_date) : null;
        const formattedDate = sessionDate ? sessionDate.toLocaleString() : 'Date TBD';
        const playerCount = parseInt(session.participants_count, 10) || 0;
        const maxPlayers = parseInt(session.max_players, 10) || 0;

        return {
          id: session.session_id,
          session_id: session.session_id,
          name: session.session_name || 'Doubles Session',
          description: `Doubles session on ${formattedDate}`,
          skillGroup: userSkillGroup || '-',
          avgScore: userScore || '-',
          playerCount: playerCount,
          maxPlayers: maxPlayers,
          // Since you are already a participant in these sessions,
          // you should always be allowed to enter the lobby,
          // even if the session is at max capacity.
          available: true
        };
      });

      // Update header
      document.getElementById('selectedTeamType').textContent = 'Duo Sessions';
      document.getElementById('userSkillGroup').textContent = userSkillGroup || '-';

      groupsSection.style.display = 'block';
      populateGroups();
      groupsSection.scrollIntoView({ behavior: 'smooth' });
    }

    function getTeamTypeName(type) {
      switch(type) {
        case 'duo': return 'Duo Teams';
        case 'trio': return 'Trio Teams';
        case 'team': return 'Teams';
        default: return 'Teams';
      }
    }

    function resetTeamSelection() {
      selectedTeamType = null;
      selectedGroupId = null;
      filteredGroups = [];
      
      // Reset UI
      document.querySelectorAll('.team-type-card').forEach(card => {
        card.classList.remove('selected');
      });
      document.getElementById('skillDisplaySection').style.display = 'none';
      document.getElementById('groupsDisplaySection').style.display = 'none';
      
      // Scroll to top
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function populateGroups() {
      const container = document.getElementById('groupsContainer');
      container.innerHTML = '';

      if (filteredGroups.length === 0) {
        container.innerHTML = `
          <div class="col-12">
            <div class="card">
              <div class="card-body text-center py-5">
                <i class="ti ti-users-group fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">No Groups Available</h5>
                <p class="text-muted">There are currently no groups available for your skill level and team type.</p>
                <button class="btn btn-primary" onclick="requestRandomGroup()">
                  <i class="ti ti-dice me-1"></i>
                  Request Random Assignment
                </button>
              </div>
            </div>
          </div>
        `;
        return;
      }

      filteredGroups.forEach(group => {
        const groupCard = document.createElement('div');
        groupCard.className = 'col-md-6 col-lg-4 mb-4';
        groupCard.innerHTML = `
          <div class="card group-card h-100" onclick="selectGroup(${group.id})" data-group-id="${group.id}">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="mb-0">${group.name}</h6>
              <span class="badge ${getSkillBadgeClass(group.skillGroup)} skill-badge">
                ${group.skillGroup}
              </span>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">${group.description}</p>
              
              <div class="group-stats mb-3">
                <div class="row text-center">
                  <div class="col-4">
                    <div class="player-count text-primary">${group.playerCount}</div>
                    <small class="text-muted">Players</small>
                  </div>
                  <div class="col-4">
                    <div class="player-count text-success">${group.avgScore}</div>
                    <small class="text-muted">Avg Score</small>
                  </div>
                  <div class="col-4">
                    <div class="player-count text-warning">${group.maxPlayers}</div>
                    <small class="text-muted">Max</small>
                  </div>
                </div>
              </div>
              
              <div class="progress mb-2" style="height: 8px;">
                <div class="progress-bar ${getProgressBarClass(group.playerCount, group.maxPlayers)}" 
                     style="width: ${(group.playerCount / group.maxPlayers) * 100}%"></div>
              </div>
              
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">${group.playerCount}/${group.maxPlayers} players</small>
                ${group.available ? 
                  '<span class="badge bg-success">Available</span>' : 
                  '<span class="badge bg-danger">Full</span>'
                }
              </div>
              
              <button class="btn btn-primary join-button" ${!group.available ? 'disabled' : ''} onclick="event.stopPropagation(); joinGroup(${group.id})">
                <i class="ti ti-user-plus me-1"></i>
                ${group.available ? (selectedTeamType === 'duo' ? 'Join Session' : 'Join Group') : 'Group Full'}
              </button>
            </div>
          </div>
        `;
        container.appendChild(groupCard);
      });
    }

    function selectGroup(groupId) {
      // Remove previous selection
      document.querySelectorAll('.group-card').forEach(card => {
        card.classList.remove('selected');
      });
      
      // Add selection to clicked card
      const selectedCard = document.querySelector(`[data-group-id="${groupId}"]`);
      if (selectedCard) {
        selectedCard.classList.add('selected');
        selectedGroupId = groupId;
        showSelectedGroupSummary(groupId);
      }
    }

    function showSelectedGroupSummary(groupId) {
      let group = null;

      // For Duo sessions, use filteredGroups (real data)
      if (selectedTeamType === 'duo') {
        group = filteredGroups.find(g => g.id === groupId || g.session_id === groupId);
      } else if (selectedTeamType && userSkillGroup && 
                 availableGroups[selectedTeamType] && 
                 availableGroups[selectedTeamType][userSkillGroup]) {
        // For Trio / Team, use sample groups
        group = availableGroups[selectedTeamType][userSkillGroup].find(g => g.id === groupId);
      }

      if (!group) {
        console.warn('showSelectedGroupSummary: group not found for id', groupId);
        return;
      }

      // Normalise fields between duo sessions and sample groups
      const skill = group.skillGroup || userSkillGroup || '-';
      const playerCount = group.playerCount ?? 0;
      const maxPlayers = group.maxPlayers ?? 0;
      const avgScore = group.avgScore ?? '-';

      document.getElementById('selectedGroupName').textContent = group.name || 'Group';
      document.getElementById('selectedGroupDescription').textContent = group.description || '';
      document.getElementById('selectedGroupSkill').textContent = skill;
      document.getElementById('selectedGroupCount').textContent = playerCount;
      document.getElementById('selectedGroupMax').textContent = maxPlayers;
      document.getElementById('selectedGroupAvg').textContent = avgScore;
      
      document.getElementById('selectedGroupSummary').style.display = 'block';
    }

    function joinGroup(groupId) {
      // For Duo: groupId corresponds to session_id from filteredGroups or duoSessions
      if (selectedTeamType === 'duo') {
        // Try to find session in filteredGroups first
        let session = filteredGroups.find(g => g.id === groupId || g.session_id === groupId);
        
        // If not found, try duoSessions array directly
        if (!session && duoSessions && duoSessions.length > 0) {
          session = duoSessions.find(s => s.session_id == groupId);
        }
        
        if (!session) {
          alert('Session information not found. Please refresh the page.');
          return;
        }

        // Use session_id from the found session
        const sessionId = session.session_id || session.id || groupId;

        // Call backend to join the duo lobby for this session
        const formData = new FormData();
        formData.append('action', 'join_lobby');
        formData.append('session_id', sessionId);

        fetch('ajax/duo-management.php', {
          method: 'POST',
          body: formData
        })
          .then(res => res.json())
          .then(data => {
            // Three cases where we should still open the lobby:
            // 1) Joined successfully
            // 2) Already in a duo for this session
            // 3) Already joined the lobby earlier
            if (data.success || data.already_paired || data.already_joined) {
              if (data.success) {
                showNotification('Joined duo session lobby successfully.', 'success');
              } else if (data.already_paired) {
                showNotification('You are already in a duo for this session. Opening lobby...', 'info');
              } else if (data.already_joined) {
                showNotification('You have already joined this lobby. Opening lobby...', 'info');
              }
              showDuoLobby(session);
            } else {
              const message = data.message || 'Failed to join duo lobby.';
              showNotification(message, 'error');
            }
          })
          .catch(err => {
            console.error('Error joining duo lobby:', err);
            showNotification('Connection error while joining duo lobby.', 'error');
          });
        return;
      }

      // Trio / Team demo behaviour (kept simple)
      showNotification('Joining Trio/Team groups is demo-only in this version.', 'info');
    }

    function showDuoLobby(session) {
      currentSessionId = session.session_id;

      const groupsSection = document.getElementById('groupsDisplaySection');
      if (groupsSection) {
        groupsSection.style.display = 'none';
      }

      let lobbySection = document.getElementById('duoLobbySection');
      if (!lobbySection) {
        lobbySection = document.createElement('div');
        lobbySection.id = 'duoLobbySection';
        document.querySelector('.container-fluid').appendChild(lobbySection);
      }

      const sessionDate = session.session_date ? new Date(session.session_date) : null;
      const formattedDate = sessionDate ? sessionDate.toLocaleString() : 'Date TBD';
      
      // Handle both property name formats
      const playerCount = session.playerCount ?? session.participants_count ?? 0;
      const maxPlayers = session.maxPlayers ?? session.max_players ?? 0;

      lobbySection.innerHTML = `
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="ti ti-users me-2"></i>
                  Duo Lobby – ${session.name || session.session_name || 'Doubles Session'}
                </h5>
              </div>
              <div class="card-body">
                <p class="text-muted">
                  Session date: <strong>${formattedDate}</strong><br>
                  Players in session: <strong>${playerCount}/${maxPlayers}</strong>
                </p>

                <div class="text-center mb-4">
                  <h6>Your latest average (5 games)</h6>
                  <div class="d-inline-block px-4 py-2 bg-light rounded">
                    <span style="font-size: 32px; font-weight: bold;" id="duoUserAverage">
                      ${userScore || '-'}
                    </span>
                  </div>
                  <p class="text-muted small mt-2">
                    This value is used to form your duo pairing.
                  </p>
                </div>

                <div id="duoStatusArea" class="mb-3">
                  <div class="alert alert-info mb-2" id="duoStatusMessage">
                    Waiting for admin to form duos for this session...
                  </div>
                  <div id="duoPartnerInfo" style="display:none;"></div>
                </div>

                <div id="laneVotingArea" class="mb-3" style="display:none;">
                  <h6>Lane Voting</h6>
                  <p class="text-muted small mb-2" id="laneVotingHelpText">
                    One player from your duo can randomize your lane. Once chosen, the lane is locked for your team.
                  </p>
                  <div class="d-flex gap-2">
                    <button id="randomLaneBtn" class="btn btn-outline-primary" onclick="voteForRandomLane()">
                      Randomize Lane
                    </button>
                  </div>
                  <p class="text-muted small mt-2" id="laneStatusText"></p>
                </div>

                <button class="btn btn-outline-secondary" onclick="location.reload()">
                  <i class="ti ti-arrow-left me-1"></i>
                  Back to Session List
                </button>
              </div>
            </div>
          </div>
        </div>
      `;

      lobbySection.scrollIntoView({ behavior: 'smooth' });

      // Start polling for duo + lane status
      startDuoPolling();
    }

    function startDuoPolling() {
      if (!currentSessionId) return;

      if (duoPollInterval) {
        clearInterval(duoPollInterval);
      }

      // Check immediately, then poll every 5 seconds
      checkMyDuoStatus();
      duoPollInterval = setInterval(checkMyDuoStatus, 5000);
    }

    function checkMyDuoStatus() {
      if (!currentSessionId) return;

      // First, try to auto-pair if possible (system-driven, not admin)
      autoPairIfPossible();

      fetch('ajax/duo-management.php?action=get_my_duo&session_id=' + encodeURIComponent(currentSessionId))
        .then(res => res.json())
        .then(data => {
          const statusEl = document.getElementById('duoStatusMessage');
          const partnerInfoEl = document.getElementById('duoPartnerInfo');
          const laneArea = document.getElementById('laneVotingArea');
          const laneStatusText = document.getElementById('laneStatusText');
          const laneHelpText = document.getElementById('laneVotingHelpText');
          const randomLaneBtn = document.getElementById('randomLaneBtn');

          if (!statusEl || !partnerInfoEl || !laneArea) {
            // Lobby might have been left / page reloaded
            return;
          }

          if (!data.success) {
            statusEl.className = 'alert alert-danger mb-2';
            statusEl.textContent = data.message || 'Error checking duo status.';
            return;
          }

          if (!data.in_duo) {
            statusEl.className = 'alert alert-info mb-2';
            // Show how many players are in the lobby
            const playersInLobby = data.players_in_lobby || 0;
            const totalPlayers = data.total_players || 8;
            statusEl.innerHTML = `
              <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div>
                  Waiting for all players to join... (${playersInLobby}/${totalPlayers} players ready)
                  <br><small class="text-muted">Pairing will happen automatically once everyone has joined.</small>
                </div>
              </div>
            `;
            partnerInfoEl.style.display = 'none';
            laneArea.style.display = 'none';
            return;
          }

          // We are in a duo
          const duo = data.duo;
          currentDuoId = duo.duo_id;

          statusEl.className = 'alert alert-success mb-2';
          statusEl.innerHTML = `
            <strong>✓ You have been paired!</strong>
            <br>
            <small>Duo Team: <strong>${duo.duo_name || 'Duo 1'}</strong></small>
          `;

          const youLabel = duo.is_player1 ? 
            (duo.player1_first_name + ' ' + duo.player1_last_name) :
            (duo.player2_first_name + ' ' + duo.player2_last_name);

          // Preserve current input value if it exists (user might be typing)
          const existingInput = document.getElementById('duoNameInput');
          const preservedValue = existingInput ? existingInput.value : (duo.duo_name || 'Duo 1');
          
          partnerInfoEl.innerHTML = `
            <div class="card mb-3">
              <div class="card-body">
                <h6 class="card-title mb-3">Duo Team Name</h6>
                <div class="input-group mb-3">
                  <input type="text" 
                         class="form-control" 
                         id="duoNameInput" 
                         value="${preservedValue.replace(/"/g, '&quot;')}" 
                         placeholder="Enter duo name"
                         maxlength="50">
                  <button class="btn btn-primary" id="saveDuoNameBtn" type="button">
                    <i class="ti ti-check me-1"></i>Save
                  </button>
                </div>
                <hr>
                <p class="mb-1">
                  <strong>You:</strong> ${youLabel}
                </p>
                <p class="mb-1">
                  <strong>Partner:</strong> ${duo.partner_name}
                </p>
              </div>
            </div>
          `;
          partnerInfoEl.style.display = 'block';
          
          // Attach event listener after HTML is created
          const saveBtn = document.getElementById('saveDuoNameBtn');
          if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
              e.preventDefault();
              updateDuoName(e);
            });
          }

          // Lane info and button visibility
          if (duo.lane_number) {
            laneArea.style.display = 'block';
            laneStatusText.textContent = 'Lane assigned: ' + duo.lane_number + '. Lane voting is closed.';
            if (randomLaneBtn) randomLaneBtn.disabled = true;
            if (laneHelpText) laneHelpText.textContent = 'Lane has been set for your duo.';
          } else {
            laneArea.style.display = 'block';
            if (duo.is_player1) {
              // This player is allowed to randomize the lane
              if (randomLaneBtn) randomLaneBtn.disabled = false;
              if (laneHelpText) laneHelpText.textContent =
                'You can randomize the lane once for your duo. Your partner cannot change it.';
              laneStatusText.textContent = 'No lane assigned yet. Click "Randomize Lane" once.';
            } else {
              // Partner will randomize
              if (randomLaneBtn) randomLaneBtn.disabled = true;
              if (laneHelpText) laneHelpText.textContent =
                'Your partner will randomize the lane for your duo.';
              laneStatusText.textContent = 'Waiting for your partner to randomize the lane.';
            }
          }
        })
        .catch(err => {
          console.error('Error checking duo status:', err);
        });
    }

    function autoPairIfPossible() {
      if (!currentSessionId) return;

      const formData = new FormData();
      formData.append('action', 'auto_pair_now');
      formData.append('session_id', currentSessionId);

      fetch('ajax/duo-management.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          // We don't need to show anything here; checkMyDuoStatus()
          // will pick up any new duos that were created.
        })
        .catch(err => {
          console.error('Error attempting auto pair:', err);
        });
    }

    // Make function globally accessible
    window.updateDuoName = function(event) {
      if (!currentDuoId) {
        alert('Duo not found yet. Please wait for pairing to complete.');
        return;
      }

      const duoNameInput = document.getElementById('duoNameInput');
      if (!duoNameInput) {
        console.error('Duo name input not found');
        return;
      }

      const newName = duoNameInput.value.trim();
      if (!newName) {
        alert('Please enter a duo name.');
        return;
      }

      // Disable button while saving
      const saveBtn = event && event.target ? event.target : document.getElementById('saveDuoNameBtn');
      if (!saveBtn) {
        console.error('Save button not found');
        return;
      }
      const originalHTML = saveBtn.innerHTML;
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

      const formData = new FormData();
      formData.append('action', 'update_duo_name');
      formData.append('duo_id', currentDuoId);
      formData.append('duo_name', newName);

      fetch('ajax/duo-management.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Update the status message immediately
            const statusEl = document.getElementById('duoStatusMessage');
            if (statusEl) {
              statusEl.innerHTML = `
                <strong>✓ You have been paired!</strong>
                <br>
                <small>Duo Team: <strong>${newName}</strong></small>
              `;
            }
            
            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
              ✓ Duo name updated to "${newName}"
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
            
            // Refresh duo status to ensure everything is in sync
            setTimeout(() => checkMyDuoStatus(), 500);
          } else {
            alert(data.message || 'Failed to update duo name.');
          }
        })
        .catch(err => {
          console.error('Error updating duo name:', err);
          alert('Error updating duo name.');
        })
        .finally(() => {
          // Re-enable button
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalHTML;
        });
    };

    function voteForRandomLane() {
      if (!currentDuoId) {
        alert('Duo not found yet. Please wait for pairing to complete.');
        return;
      }

      // Randomly choose lane 1 or 2
      const laneNumber = Math.random() < 0.5 ? 1 : 2;

      const formData = new FormData();
      formData.append('action', 'vote_lane');
      formData.append('duo_id', currentDuoId);
      formData.append('lane_number', laneNumber);

      fetch('ajax/duo-management.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          const laneStatusText = document.getElementById('laneStatusText');
          if (!laneStatusText) return;

          if (!data.success) {
            laneStatusText.textContent = data.message || 'Failed to record lane vote.';
            return;
          }

          laneStatusText.textContent = data.message || ('Lane chosen: ' + laneNumber + '.');
          // Immediately refresh duo info to show assigned lane
          checkMyDuoStatus();
        })
        .catch(err => {
          console.error('Error voting for lane:', err);
        });
    }

    function joinSelectedGroup() {
      if (selectedGroupId) {
        joinGroup(selectedGroupId);
      }
    }

    function requestRandomGroup() {
      if (!selectedTeamType || !userSkillGroup) {
        alert('Please select a team type first.');
        return;
      }
      
      const availableGroupsOnly = availableGroups[selectedTeamType][userSkillGroup].filter(g => g.available);
      if (availableGroupsOnly.length === 0) {
        showNotification('No available groups for your skill level at the moment', 'warning');
        return;
      }
      
      const randomGroup = availableGroupsOnly[Math.floor(Math.random() * availableGroupsOnly.length)];
      
      // Populate modal
      document.getElementById('randomGroupName').textContent = randomGroup.name;
      document.getElementById('randomGroupDesc').textContent = randomGroup.description;
      document.getElementById('randomGroupSkill').textContent = randomGroup.skillGroup;
      document.getElementById('randomGroupCount').textContent = randomGroup.playerCount;
      document.getElementById('randomGroupMax').textContent = randomGroup.maxPlayers;
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('randomGroupModal'));
      modal.show();
    }

    function confirmRandomGroup() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('randomGroupModal'));
      modal.hide();
      
      const randomGroupName = document.getElementById('randomGroupName').textContent;
      showNotification(`Successfully joined ${randomGroupName}!`, 'success');
    }

    function filterGroups() {
      const searchTerm = document.getElementById('searchGroups').value.toLowerCase();
      const skillFilter = document.getElementById('skillFilter').value;
      const availabilityFilter = document.getElementById('availabilityFilter').value;
      
      filteredGroups = availableGroups.filter(group => {
        const matchesSearch = group.name.toLowerCase().includes(searchTerm) || 
                             group.description.toLowerCase().includes(searchTerm);
        const matchesSkill = !skillFilter || group.skillLevel === skillFilter;
        const matchesAvailability = !availabilityFilter || 
          (availabilityFilter === 'available' && group.available) ||
          (availabilityFilter === 'full' && !group.available);
        
        return matchesSearch && matchesSkill && matchesAvailability;
      });
      
      populateGroups();
    }

    function clearFilters() {
      document.getElementById('searchGroups').value = '';
      document.getElementById('skillFilter').value = '';
      document.getElementById('availabilityFilter').value = '';
      filteredGroups = [...availableGroups];
      populateGroups();
    }

    function refreshGroups() {
      showNotification('Groups refreshed successfully!', 'info');
      clearFilters();
    }

    function getSkillBadgeClass(skillGroup) {
      const classes = {
        'A': 'bg-danger',      // Professional (200-300)
        'B': 'bg-warning',    // Advanced (180-199)
        'C': 'bg-info',       // Above Average (160-179)
        'D': 'bg-success',    // Intermediate (140-159)
        'E': 'bg-primary',    // Casual (120-139)
        'F': 'bg-secondary',  // Beginner (100-119)
        'G': 'bg-dark',       // New/Inexperienced (80-99)
        'H': 'bg-light text-dark' // Absolute Beginner (Below 80)
      };
      return classes[skillGroup] || 'bg-secondary';
    }

    function getProgressBarClass(playerCount, maxPlayers) {
      const percentage = (playerCount / maxPlayers) * 100;
      if (percentage >= 90) return 'bg-danger';
      if (percentage >= 75) return 'bg-warning';
      return 'bg-success';
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
  </script>
  
  <?php include 'includes/admin-popup.php'; ?>
</body>

</html>
