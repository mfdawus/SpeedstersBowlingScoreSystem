<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Account - SPEEDSTERS Bowling System</title>
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
    .account-type-card {
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    .account-type-card:hover {
      border-color: #0d6efd;
      transform: translateY(-2px);
    }
    .account-type-card.selected {
      border-color: #0d6efd;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    }
    .form-section {
      display: none;
    }
    .form-section.active {
      display: block;
    }
    .admin-badge {
      background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
      color: #333;
    }
    .skill-level-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .team-member-card {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
      background: #f8f9fa;
    }
    .team-member-card.selected {
      border-color: #0d6efd;
      background: #e3f2fd;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">
    
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
              <a class="sidebar-link" href="./admin-dashboard.php" aria-expanded="false">
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
              <a class="sidebar-link" href="./admin-events.php">
                <i class="ti ti-calendar-event"></i>
                <span class="hide-menu">Events Management</span>
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
              <a class="sidebar-link" href="./admin-score-update.php">
                <i class="ti ti-edit"></i>
                <span class="hide-menu">Update Scores</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./admin-create-account.php">
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
                    <li class="breadcrumb-item active">Create Account</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Creation Statistics -->
          <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Total Accounts</h6>
                      <h3 class="mb-0 text-primary">1,247</h3>
                      <small class="text-muted">+12% this month</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users fs-1 text-muted"></i>
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
                      <h6 class="card-title text-muted mb-1">Created Today</h6>
                      <h3 class="mb-0 text-success">23</h3>
                      <small class="text-muted">+5 vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-user-plus fs-1 text-success"></i>
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
                      <h6 class="card-title text-muted mb-1">Active Teams</h6>
                      <h3 class="mb-0 text-warning">89</h3>
                      <small class="text-muted">+3 new this week</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users-group fs-1 text-warning"></i>
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
                      <h6 class="card-title text-muted mb-1">Pending Approval</h6>
                      <h3 class="mb-0 text-info">7</h3>
                      <small class="text-muted">Requires review</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-clock fs-1 text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Creation Header -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body text-center">
                  <i class="ti ti-user-plus fs-1 text-primary mb-3"></i>
                  <h5 class="card-title fw-semibold mb-2">Create New Player Account</h5>
                  <p class="text-muted mb-0">Add a new individual bowling player to the system</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Player Account Creation Form -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Player Information</h5>
                      <span class="fw-normal text-muted">Enter the details for the new bowling player</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-secondary btn-sm" onclick="clearForm()">
                        <i class="ti ti-refresh me-1"></i>
                        Clear Form
                      </button>
                      <button class="btn btn-info btn-sm" onclick="importFromCSV()">
                        <i class="ti ti-upload me-1"></i>
                        Import CSV
                      </button>
                    </div>
                  </div>

                  <form id="playerForm">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">First Name *</label>
                        <input type="text" class="form-control" id="firstName" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">Last Name *</label>
                        <input type="text" class="form-control" id="lastName" required>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone">
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="skill" class="form-label">Skill Level *</label>
                        <select class="form-select" id="skill" required onchange="updateGroupingOptions()">
                          <option value="">Select Skill Level</option>
                          <option value="beginner">Beginner (0-150 avg)</option>
                          <option value="intermediate">Intermediate (150-200 avg)</option>
                          <option value="advanced">Advanced (200-250 avg)</option>
                          <option value="pro">Professional (250+ avg)</option>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" min="5" max="100">
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="averageScore" class="form-label">Average Score</label>
                        <input type="number" class="form-control" id="averageScore" min="0" max="300" placeholder="e.g., 180" onchange="updateGroupingOptions()">
                        <small class="form-text text-muted">Enter player's average bowling score for better grouping</small>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="groupingPreference" class="form-label">Grouping Preference</label>
                        <select class="form-select" id="groupingPreference" onchange="updateGroupingOptions()">
                          <option value="auto">Auto-assign based on skill</option>
                          <option value="balanced">Balanced groups (mixed skill levels)</option>
                          <option value="competitive">Competitive groups (similar skill levels)</option>
                          <option value="social">Social groups (mixed ages/skill)</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label for="notes" class="form-label">Notes (Optional)</label>
                      <textarea class="form-control" id="notes" rows="3" placeholder="Any additional notes about the player..."></textarea>
                    </div>
                    
                    <!-- Grouping Preview Section -->
                    <div class="card mt-4" id="groupingPreview" style="display: none;">
                      <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="ti ti-users-group me-2"></i>Suggested Group Assignment</h6>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h6 class="text-primary">Recommended Group:</h6>
                            <div class="d-flex align-items-center mb-2">
                              <span class="badge bg-primary me-2" id="suggestedGroupBadge">Group A</span>
                              <span class="text-muted" id="suggestedGroupInfo">Beginner Level</span>
                            </div>
                            <small class="text-muted" id="groupingReason">Based on skill level and average score</small>
                          </div>
                          <div class="col-md-6">
                            <h6 class="text-success">Group Statistics:</h6>
                            <div class="row text-center">
                              <div class="col-4">
                                <div class="border rounded p-2">
                                  <div class="fw-bold text-primary" id="groupPlayerCount">0</div>
                                  <small class="text-muted">Players</small>
                                </div>
                              </div>
                              <div class="col-4">
                                <div class="border rounded p-2">
                                  <div class="fw-bold text-success" id="groupAvgScore">0</div>
                                  <small class="text-muted">Avg Score</small>
                                </div>
                              </div>
                              <div class="col-4">
                                <div class="border rounded p-2">
                                  <div class="fw-bold text-warning" id="groupSkillLevel">-</div>
                                  <small class="text-muted">Skill Level</small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="mt-3">
                          <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshGrouping()">
                            <i class="ti ti-refresh me-1"></i>
                            Refresh Grouping
                          </button>
                          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="viewAllGroups()">
                            <i class="ti ti-eye me-1"></i>
                            View All Groups
                          </button>
                          <button type="button" class="btn btn-outline-success btn-sm" onclick="createNewGroup()">
                            <i class="ti ti-plus me-1"></i>
                            Create New Group
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <div class="alert alert-info">
                      <i class="ti ti-info-circle me-2"></i>
                      <strong>Note:</strong> The player will receive an email with their login credentials and can start playing immediately. Group assignment will be finalized after account creation.
                    </div>
                  </form>

                  <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-secondary" onclick="clearForm()">Clear Form</button>
                    <button class="btn btn-primary" onclick="createPlayer()">
                      <i class="ti ti-user-plus me-2"></i>
                      Create Player Account
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

<!-- All Groups Modal -->
<div class="modal fade" id="allGroupsModal" tabindex="-1" aria-labelledby="allGroupsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="allGroupsModalLabel">
          <i class="ti ti-users-group me-2"></i>
          All Player Groups
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row" id="allGroupsContainer">
          <!-- Groups will be dynamically populated here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="createNewGroup()">
          <i class="ti ti-plus me-1"></i>
          Create New Group
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Create New Group Modal -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createGroupModalLabel">
          <i class="ti ti-plus me-2"></i>
          Create New Group
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createGroupForm">
          <div class="mb-3">
            <label for="newGroupName" class="form-label">Group Name *</label>
            <input type="text" class="form-control" id="newGroupName" required>
          </div>
          <div class="mb-3">
            <label for="newGroupSkillLevel" class="form-label">Target Skill Level *</label>
            <select class="form-select" id="newGroupSkillLevel" required>
              <option value="">Select Skill Level</option>
              <option value="beginner">Beginner (0-150 avg)</option>
              <option value="intermediate">Intermediate (150-200 avg)</option>
              <option value="advanced">Advanced (200-250 avg)</option>
              <option value="pro">Professional (250+ avg)</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="newGroupMaxPlayers" class="form-label">Maximum Players *</label>
            <input type="number" class="form-control" id="newGroupMaxPlayers" min="4" max="12" value="8" required>
          </div>
          <div class="mb-3">
            <label for="newGroupDescription" class="form-label">Group Description</label>
            <textarea class="form-control" id="newGroupDescription" rows="3" placeholder="Describe the group's purpose or characteristics..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveNewGroup()">
          <i class="ti ti-check me-1"></i>
          Create Group
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
  
  <script>
    // Sample groups data (in real app, this would come from database)
    const sampleGroups = [
      { id: 1, name: 'Group A', skillLevel: 'beginner', avgScore: 120, playerCount: 8, maxPlayers: 12, description: 'Beginner-friendly group for new players' },
      { id: 2, name: 'Group B', skillLevel: 'beginner', avgScore: 135, playerCount: 6, maxPlayers: 12, description: 'Casual beginner group' },
      { id: 3, name: 'Group C', skillLevel: 'intermediate', avgScore: 175, playerCount: 10, maxPlayers: 12, description: 'Intermediate competitive group' },
      { id: 4, name: 'Group D', skillLevel: 'intermediate', avgScore: 185, playerCount: 7, maxPlayers: 12, description: 'Balanced intermediate group' },
      { id: 5, name: 'Group E', skillLevel: 'advanced', avgScore: 220, playerCount: 9, maxPlayers: 12, description: 'Advanced players group' },
      { id: 6, name: 'Group F', skillLevel: 'pro', avgScore: 265, playerCount: 5, maxPlayers: 12, description: 'Professional level group' }
    ];

    // Player account creation
    function createPlayer() {
      const form = document.getElementById('playerForm');
      
      // Validate required fields
      const firstName = document.getElementById('firstName').value;
      const lastName = document.getElementById('lastName').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const skill = document.getElementById('skill').value;
      
      if (!firstName || !lastName || !email || !password || !skill) {
        showNotification('Please fill in all required fields', 'warning');
        return;
      }
      
      if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'warning');
        return;
      }
      
      if (password.length < 6) {
        showNotification('Password must be at least 6 characters long', 'warning');
        return;
      }
      
      // Validate email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'warning');
        return;
      }
      
      // Get grouping information
      const averageScore = document.getElementById('averageScore').value;
      const groupingPreference = document.getElementById('groupingPreference').value;
      
      // Simulate API call with grouping
      setTimeout(() => {
        const suggestedGroup = getSuggestedGroup(skill, averageScore, groupingPreference);
        showNotification(`Player account created successfully! Assigned to ${suggestedGroup.name}`, 'success');
        clearForm();
      }, 1000);
    }

    // Grouping functions
    function updateGroupingOptions() {
      const skill = document.getElementById('skill').value;
      const averageScore = document.getElementById('averageScore').value;
      const groupingPreference = document.getElementById('groupingPreference').value;
      
      if (skill || averageScore) {
        const suggestedGroup = getSuggestedGroup(skill, averageScore, groupingPreference);
        updateGroupingPreview(suggestedGroup);
        document.getElementById('groupingPreview').style.display = 'block';
      } else {
        document.getElementById('groupingPreview').style.display = 'none';
      }
    }

    function getSuggestedGroup(skill, averageScore, preference) {
      let targetGroups = sampleGroups;
      
      // Filter groups based on preference
      switch(preference) {
        case 'competitive':
          // Find groups with similar skill level
          targetGroups = sampleGroups.filter(group => group.skillLevel === skill);
          break;
        case 'balanced':
          // Prefer groups with mixed skill levels (lower player count)
          targetGroups = sampleGroups.filter(group => group.playerCount < 8);
          break;
        case 'social':
          // Prefer groups with more players
          targetGroups = sampleGroups.filter(group => group.playerCount > 6);
          break;
        default: // auto
          targetGroups = sampleGroups.filter(group => group.skillLevel === skill);
      }
      
      // If no groups match skill level, find closest
      if (targetGroups.length === 0) {
        targetGroups = sampleGroups.filter(group => {
          const skillOrder = ['beginner', 'intermediate', 'advanced', 'pro'];
          const currentSkillIndex = skillOrder.indexOf(skill);
          const groupSkillIndex = skillOrder.indexOf(group.skillLevel);
          return Math.abs(currentSkillIndex - groupSkillIndex) <= 1;
        });
      }
      
      // Find group with space and closest average score
      let bestGroup = targetGroups[0];
      let minScoreDiff = Math.abs((bestGroup.avgScore || 0) - (parseInt(averageScore) || 0));
      
      for (let group of targetGroups) {
        if (group.playerCount < group.maxPlayers) {
          const scoreDiff = Math.abs((group.avgScore || 0) - (parseInt(averageScore) || 0));
          if (scoreDiff < minScoreDiff) {
            bestGroup = group;
            minScoreDiff = scoreDiff;
          }
        }
      }
      
      return bestGroup || sampleGroups[0];
    }

    function updateGroupingPreview(group) {
      document.getElementById('suggestedGroupBadge').textContent = group.name;
      document.getElementById('suggestedGroupInfo').textContent = `${group.skillLevel.charAt(0).toUpperCase() + group.skillLevel.slice(1)} Level`;
      document.getElementById('groupingReason').textContent = `Based on skill level and average score (${group.avgScore})`;
      document.getElementById('groupPlayerCount').textContent = group.playerCount;
      document.getElementById('groupAvgScore').textContent = group.avgScore;
      document.getElementById('groupSkillLevel').textContent = group.skillLevel.charAt(0).toUpperCase() + group.skillLevel.slice(1);
    }

    function refreshGrouping() {
      updateGroupingOptions();
      showNotification('Grouping options refreshed', 'info');
    }

    function viewAllGroups() {
      populateAllGroupsModal();
      const modal = new bootstrap.Modal(document.getElementById('allGroupsModal'));
      modal.show();
    }

    function populateAllGroupsModal() {
      const container = document.getElementById('allGroupsContainer');
      container.innerHTML = '';
      
      sampleGroups.forEach(group => {
        const groupCard = document.createElement('div');
        groupCard.className = 'col-md-6 mb-3';
        groupCard.innerHTML = `
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="mb-0">${group.name}</h6>
              <span class="badge ${getSkillBadgeClass(group.skillLevel)}">${group.skillLevel.charAt(0).toUpperCase() + group.skillLevel.slice(1)}</span>
            </div>
            <div class="card-body">
              <p class="text-muted small mb-3">${group.description}</p>
              <div class="row text-center mb-3">
                <div class="col-4">
                  <div class="border rounded p-2">
                    <div class="fw-bold text-primary">${group.playerCount}</div>
                    <small class="text-muted">Players</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="border rounded p-2">
                    <div class="fw-bold text-success">${group.avgScore}</div>
                    <small class="text-muted">Avg Score</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="border rounded p-2">
                    <div class="fw-bold text-warning">${group.maxPlayers}</div>
                    <small class="text-muted">Max</small>
                  </div>
                </div>
              </div>
              <div class="progress mb-2" style="height: 8px;">
                <div class="progress-bar ${getProgressBarClass(group.playerCount, group.maxPlayers)}" 
                     style="width: ${(group.playerCount / group.maxPlayers) * 100}%"></div>
              </div>
              <small class="text-muted">${group.playerCount}/${group.maxPlayers} players</small>
            </div>
            <div class="card-footer">
              <button class="btn btn-sm btn-outline-primary" onclick="assignToGroup(${group.id})">
                <i class="ti ti-user-plus me-1"></i>
                Assign Player
              </button>
            </div>
          </div>
        `;
        container.appendChild(groupCard);
      });
    }

    function getSkillBadgeClass(skillLevel) {
      const classes = {
        'beginner': 'bg-primary',
        'intermediate': 'bg-success',
        'advanced': 'bg-warning',
        'pro': 'bg-danger'
      };
      return classes[skillLevel] || 'bg-secondary';
    }

    function getProgressBarClass(playerCount, maxPlayers) {
      const percentage = (playerCount / maxPlayers) * 100;
      if (percentage >= 90) return 'bg-danger';
      if (percentage >= 75) return 'bg-warning';
      return 'bg-success';
    }

    function assignToGroup(groupId) {
      const group = sampleGroups.find(g => g.id === groupId);
      if (group) {
        showNotification(`Player will be assigned to ${group.name}`, 'info');
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('allGroupsModal'));
        modal.hide();
      }
    }

    function createNewGroup() {
      const modal = new bootstrap.Modal(document.getElementById('createGroupModal'));
      modal.show();
    }

    function saveNewGroup() {
      const name = document.getElementById('newGroupName').value;
      const skillLevel = document.getElementById('newGroupSkillLevel').value;
      const maxPlayers = document.getElementById('newGroupMaxPlayers').value;
      const description = document.getElementById('newGroupDescription').value;
      
      if (!name || !skillLevel || !maxPlayers) {
        showNotification('Please fill in all required fields', 'warning');
        return;
      }
      
      // Add new group to sampleGroups array
      const newGroup = {
        id: sampleGroups.length + 1,
        name: name,
        skillLevel: skillLevel,
        avgScore: 0,
        playerCount: 0,
        maxPlayers: parseInt(maxPlayers),
        description: description
      };
      
      sampleGroups.push(newGroup);
      
      // Close modal and reset form
      const modal = bootstrap.Modal.getInstance(document.getElementById('createGroupModal'));
      modal.hide();
      document.getElementById('createGroupForm').reset();
      
      showNotification(`New group "${name}" created successfully!`, 'success');
      
      // Refresh grouping if form has data
      updateGroupingOptions();
    }

    // Utility functions
    function clearForm() {
      document.getElementById('playerForm').reset();
      document.getElementById('groupingPreview').style.display = 'none';
      showNotification('Form cleared', 'info');
    }

    function importFromCSV() {
      showNotification('CSV import functionality would open here', 'info');
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
</body>

</html>
