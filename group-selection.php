<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Group Selection - Speedsters Bowling</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
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
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 15;">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img d-flex flex-column align-items-start text-decoration-none">
            <img src="assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="90" />
            <span class="text-muted fw-semibold mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Bowling Score System</span>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./dashboard.php" aria-expanded="false">
                <i class="ti ti-atom"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./lane-booking.php" aria-expanded="false">
                <i class="ti ti-calendar-plus"></i>
                <span class="hide-menu">Lane Booking</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./group-selection.php" aria-expanded="false">
                <i class="ti ti-users"></i>
                <span class="hide-menu">Join Group</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-table"></i>
                <span class="hide-menu">Score Table</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="./score-table-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Games</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-team.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Team (4-6 Players)</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <!-- Sidebar End -->

    <!-- Main wrapper -->
    <div class="body-wrapper">
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
      <!-- Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a>
                    <a href="./authentication-login.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Header End -->

      

      <div class="container-fluid" style="margin-top: 30px;">
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
                        <h6 class="card-title">Team (4-6 Players)</h6>
                        <p class="text-muted small">4-6 players per team</p>
                        <span class="badge bg-warning">4-6 Players</span>
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

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
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
  
  <script>
    // Sample groups data organized by skill groups and team types
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

    let selectedTeamType = null;
    let userSkillGroup = null;
    let userScore = null;
    let selectedGroupId = null;
    let filteredGroups = [];

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
      if (!selectedTeamType || !userSkillGroup) {
        alert('Please select a team type first.');
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

    function getTeamTypeName(type) {
      switch(type) {
        case 'duo': return 'Duo Teams';
        case 'trio': return 'Trio Teams';
        case 'team': return 'Teams (4-6 Players)';
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
                ${group.available ? 'Join Group' : 'Group Full'}
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
      const group = availableGroups.find(g => g.id === groupId);
      if (group) {
        document.getElementById('selectedGroupName').textContent = group.name;
        document.getElementById('selectedGroupDescription').textContent = group.description;
        document.getElementById('selectedGroupSkill').textContent = group.skillLevel.charAt(0).toUpperCase() + group.skillLevel.slice(1);
        document.getElementById('selectedGroupCount').textContent = group.playerCount;
        document.getElementById('selectedGroupMax').textContent = group.maxPlayers;
        document.getElementById('selectedGroupAvg').textContent = group.avgScore;
        
        document.getElementById('selectedGroupSummary').style.display = 'block';
      }
    }

    function joinGroup(groupId) {
      const group = availableGroups.find(g => g.id === groupId);
      if (group && group.available) {
        showNotification(`Successfully joined ${group.name}!`, 'success');
        // Update group count
        group.playerCount++;
        if (group.playerCount >= group.maxPlayers) {
          group.available = false;
        }
        populateGroups();
        document.getElementById('selectedGroupSummary').style.display = 'none';
      }
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
</body>

</html>
