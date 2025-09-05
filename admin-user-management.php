<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Management - SPEEDSTERS Bowling System</title>
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
              <a class="sidebar-link" href="./admin-dashboard.php">
                <i class="ti ti-dashboard"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./admin-user-management.php">
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
                  <a href="./score-table-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Players</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles Teams</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-team.php" class="sidebar-link">
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
              <a class="sidebar-link" href="javascript:void(0)" aria-expanded="false">
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
                    <li class="breadcrumb-item active">User Management</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- User Management Section -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">User Management</h5>
                      <span class="fw-normal text-muted">Manage all players and their information</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlayerModal">
                        <i class="ti ti-user-plus me-2"></i>
                        Add New Player
                      </button>
                      <button class="btn btn-success" onclick="exportUsers()">
                        <i class="ti ti-download me-2"></i>
                        Export Users
                      </button>
                      <button class="btn btn-info" onclick="refreshUsers()">
                        <i class="ti ti-refresh me-2"></i>
                        Refresh
                      </button>
                    </div>
                  </div>

                  <!-- Search and Filter Bar -->
                  <div class="row mb-4">
                    <div class="col-md-4">
                      <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="userSearch" placeholder="Search players...">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <select class="form-select" id="skillFilter">
                        <option value="">All Skill Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="pro">Professional</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="ti ti-x me-1"></i>
                        Clear
                      </button>
                    </div>
                  </div>

                  <!-- Users Table -->
                  <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                      <thead>
                        <tr>
                          <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                          </th>
                          <th>Player</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Skill Level</th>
                          <th>Status</th>
                          <th>Games Played</th>
                          <th>Best Score</th>
                          <th>Average</th>
                          <th>Joined</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="usersTableBody">
                        <!-- Users will be populated here -->
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination -->
                  <nav aria-label="Users pagination">
                    <ul class="pagination justify-content-center" id="usersPagination">
                      <!-- Pagination will be generated here -->
                    </ul>
                  </nav>
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

  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit Player Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editUserForm">
            <input type="hidden" id="editUserId">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="editPlayerName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="editPlayerName" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="editPlayerEmail" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="editPlayerEmail" required>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="editPlayerPhone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="editPlayerPhone">
              </div>
              <div class="col-md-6 mb-3">
                <label for="editPlayerSkill" class="form-label">Skill Level</label>
                <select class="form-select" id="editPlayerSkill" required>
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                  <option value="pro">Professional</option>
                </select>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="editPlayerStatus" class="form-label">Status</label>
                <select class="form-select" id="editPlayerStatus" required>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="editPlayerJoinDate" class="form-label">Join Date</label>
                <input type="date" class="form-control" id="editPlayerJoinDate" readonly>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="editPlayerGames" class="form-label">Games Played</label>
                <input type="number" class="form-control" id="editPlayerGames" readonly>
              </div>
              <div class="col-md-4 mb-3">
                <label for="editPlayerBestScore" class="form-label">Best Score</label>
                <input type="number" class="form-control" id="editPlayerBestScore" readonly>
              </div>
              <div class="col-md-4 mb-3">
                <label for="editPlayerAverage" class="form-label">Average Score</label>
                <input type="number" class="form-control" id="editPlayerAverage" readonly step="0.1">
              </div>
            </div>
            
            <div class="mb-3">
              <label for="editPlayerNotes" class="form-label">Notes</label>
              <textarea class="form-control" id="editPlayerNotes" rows="3" placeholder="Additional notes about the player..."></textarea>
            </div>
            
            <div class="alert alert-info">
              <i class="ti ti-info-circle me-2"></i>
              <strong>Note:</strong> Statistics (Games Played, Best Score, Average) are calculated automatically from game records.
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-warning" onclick="updateUser()">Update Player</button>
        </div>
      </div>
    </div>
  </div>

  <!-- User Details Modal -->
  <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userDetailsModalLabel">Player Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="text-center mb-4">
                <img src="assets/images/profile/user-1.jpg" alt="Player" class="rounded-circle mb-3" width="120" height="120">
                <h4 id="detailPlayerName">Player Name</h4>
                <span class="badge bg-primary" id="detailPlayerSkill">Skill Level</span>
                <span class="badge bg-success ms-2" id="detailPlayerStatus">Status</span>
              </div>
            </div>
            <div class="col-md-8">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Email</label>
                  <p id="detailPlayerEmail">email@example.com</p>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Phone</label>
                  <p id="detailPlayerPhone">+1 234 567 8900</p>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Games Played</label>
                  <p id="detailPlayerGames">0</p>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Best Score</label>
                  <p id="detailPlayerBestScore">0</p>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Average Score</label>
                  <p id="detailPlayerAverage">0.0</p>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Joined</label>
                  <p id="detailPlayerJoinDate">2024-01-01</p>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Notes</label>
                <p id="detailPlayerNotes">No notes available</p>
              </div>
            </div>
          </div>
          
          <!-- Recent Games -->
          <div class="mt-4">
            <h6 class="fw-bold mb-3">Recent Games</h6>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Game Type</th>
                    <th>Game 1</th>
                    <th>Game 2</th>
                    <th>Game 3</th>
                    <th>Game 4</th>
                    <th>Game 5</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="detailPlayerGamesTable">
                  <tr>
                    <td colspan="8" class="text-center text-muted">No games recorded</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-warning" onclick="editUserFromDetails()">Edit Player</button>
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
    // User Management Functions
    let currentUsers = [];
    let filteredUsers = [];
    let currentPage = 1;
    const usersPerPage = 10;

    // Sample user data
    const sampleUsers = [
      {
        id: 1,
        name: 'Mike Johnson',
        email: 'mike.johnson@email.com',
        phone: '+1 555 0123',
        skill: 'pro',
        status: 'active',
        gamesPlayed: 8,
        bestScore: 289,
        average: 231.2,
        joinDate: '2024-01-15',
        notes: 'Professional player with excellent technique',
        avatar: 'assets/images/profile/user-3.jpg'
      },
      {
        id: 2,
        name: 'Sarah Wilson',
        email: 'sarah.wilson@email.com',
        phone: '+1 555 0124',
        skill: 'advanced',
        status: 'active',
        gamesPlayed: 7,
        bestScore: 275,
        average: 219.8,
        joinDate: '2024-01-20',
        notes: 'Consistent performer, team player',
        avatar: 'assets/images/profile/user-2.jpg'
      },
      {
        id: 3,
        name: 'Alex Rodriguez',
        email: 'alex.rodriguez@email.com',
        phone: '+1 555 0125',
        skill: 'intermediate',
        status: 'active',
        gamesPlayed: 5,
        bestScore: 245,
        average: 198.4,
        joinDate: '2024-02-01',
        notes: 'Improving rapidly, great potential',
        avatar: 'assets/images/profile/user-4.jpg'
      },
      {
        id: 4,
        name: 'Emma Davis',
        email: 'emma.davis@email.com',
        phone: '+1 555 0126',
        skill: 'beginner',
        status: 'active',
        gamesPlayed: 3,
        bestScore: 189,
        average: 156.7,
        joinDate: '2024-02-15',
        notes: 'New player, learning the basics',
        avatar: 'assets/images/profile/user-5.jpg'
      },
      {
        id: 5,
        name: 'Tom Anderson',
        email: 'tom.anderson@email.com',
        phone: '+1 555 0127',
        skill: 'advanced',
        status: 'inactive',
        gamesPlayed: 12,
        bestScore: 298,
        average: 245.3,
        joinDate: '2023-12-10',
        notes: 'Experienced player, currently on break',
        avatar: 'assets/images/profile/user-6.jpg'
      },
      {
        id: 6,
        name: 'Maria Garcia',
        email: 'maria.garcia@email.com',
        phone: '+1 555 0128',
        skill: 'pro',
        status: 'active',
        gamesPlayed: 15,
        bestScore: 300,
        average: 267.8,
        joinDate: '2023-11-20',
        notes: 'Elite player, tournament champion',
        avatar: 'assets/images/profile/user-7.jpg'
      }
    ];

    function loadUsers() {
      currentUsers = [...sampleUsers];
      filteredUsers = [...currentUsers];
      currentPage = 1;
      renderUsers();
      renderPagination();
    }

    function renderUsers() {
      const tbody = document.getElementById('usersTableBody');
      const startIndex = (currentPage - 1) * usersPerPage;
      const endIndex = startIndex + usersPerPage;
      const pageUsers = filteredUsers.slice(startIndex, endIndex);

      if (pageUsers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted">No users found</td></tr>';
        return;
      }

      tbody.innerHTML = pageUsers.map(user => `
        <tr>
          <td>
            <input type="checkbox" class="user-checkbox" value="${user.id}">
          </td>
          <td>
            <div class="d-flex align-items-center">
              <img src="${user.avatar}" alt="Player" class="rounded-circle me-2" width="32" height="32">
              <div>
                <h6 class="mb-0">${user.name}</h6>
                <small class="text-muted">ID: ${user.id}</small>
              </div>
            </div>
          </td>
          <td>${user.email}</td>
          <td>${user.phone}</td>
          <td><span class="badge bg-${getSkillBadgeColor(user.skill)}">${capitalizeFirst(user.skill)}</span></td>
          <td><span class="badge bg-${getStatusBadgeColor(user.status)}">${capitalizeFirst(user.status)}</span></td>
          <td>${user.gamesPlayed}</td>
          <td><span class="fw-bold text-success">${user.bestScore}</span></td>
          <td>${user.average}</td>
          <td>${formatDate(user.joinDate)}</td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-sm btn-outline-primary" onclick="viewUserDetails(${user.id})" title="View Details">
                <i class="ti ti-eye"></i>
              </button>
              <button class="btn btn-sm btn-outline-warning" onclick="editUser(${user.id})" title="Edit">
                <i class="ti ti-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id})" title="Delete">
                <i class="ti ti-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
    }

    function renderPagination() {
      const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
      const pagination = document.getElementById('usersPagination');
      
      if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
      }

      let paginationHTML = '';
      
      // Previous button
      paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
          <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
        </li>
      `;
      
      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
          paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
              <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
          `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
          paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
      }
      
      // Next button
      paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
          <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
        </li>
      `;
      
      pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
      const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
      if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderUsers();
        renderPagination();
      }
    }

    function searchUsers() {
      const searchTerm = document.getElementById('userSearch').value.toLowerCase();
      const skillFilter = document.getElementById('skillFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      
      filteredUsers = currentUsers.filter(user => {
        const matchesSearch = user.name.toLowerCase().includes(searchTerm) || 
                            user.email.toLowerCase().includes(searchTerm);
        const matchesSkill = !skillFilter || user.skill === skillFilter;
        const matchesStatus = !statusFilter || user.status === statusFilter;
        
        return matchesSearch && matchesSkill && matchesStatus;
      });
      
      currentPage = 1;
      renderUsers();
      renderPagination();
    }

    function clearFilters() {
      document.getElementById('userSearch').value = '';
      document.getElementById('skillFilter').value = '';
      document.getElementById('statusFilter').value = '';
      filteredUsers = [...currentUsers];
      currentPage = 1;
      renderUsers();
      renderPagination();
    }

    function toggleSelectAll() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.querySelectorAll('.user-checkbox');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
      });
    }

    function viewUserDetails(userId) {
      const user = currentUsers.find(u => u.id === userId);
      if (!user) return;
      
      // Populate details modal
      document.getElementById('detailPlayerName').textContent = user.name;
      document.getElementById('detailPlayerEmail').textContent = user.email;
      document.getElementById('detailPlayerPhone').textContent = user.phone;
      document.getElementById('detailPlayerSkill').textContent = capitalizeFirst(user.skill);
      document.getElementById('detailPlayerStatus').textContent = capitalizeFirst(user.status);
      document.getElementById('detailPlayerGames').textContent = user.gamesPlayed;
      document.getElementById('detailPlayerBestScore').textContent = user.bestScore;
      document.getElementById('detailPlayerAverage').textContent = user.average;
      document.getElementById('detailPlayerJoinDate').textContent = formatDate(user.joinDate);
      document.getElementById('detailPlayerNotes').textContent = user.notes || 'No notes available';
      
      // Update avatar
      document.querySelector('#userDetailsModal img').src = user.avatar;
      
      // Update skill and status badge colors
      const skillBadge = document.getElementById('detailPlayerSkill');
      const statusBadge = document.getElementById('detailPlayerStatus');
      skillBadge.className = `badge bg-${getSkillBadgeColor(user.skill)}`;
      statusBadge.className = `badge bg-${getStatusBadgeColor(user.status)} ms-2`;
      
      // Show recent games (sample data)
      const gamesTable = document.getElementById('detailPlayerGamesTable');
      const recentGames = [
        { date: '2024-01-15', type: 'Tournament', game1: 245, game2: 267, game3: 289, game4: 275, game5: 298, total: 1374 },
        { date: '2024-01-12', type: 'League', game1: 198, game2: 234, game3: 256, game4: 0, game5: 0, total: 688 },
        { date: '2024-01-10', type: 'Practice', game1: 189, game2: 201, game3: 223, game4: 195, game5: 0, total: 808 }
      ];
      
      gamesTable.innerHTML = recentGames.map(game => `
        <tr>
          <td>${formatDate(game.date)}</td>
          <td><span class="badge bg-primary">${game.type}</span></td>
          <td>${game.game1 || '-'}</td>
          <td>${game.game2 || '-'}</td>
          <td>${game.game3 || '-'}</td>
          <td>${game.game4 || '-'}</td>
          <td>${game.game5 || '-'}</td>
          <td><strong>${game.total}</strong></td>
        </tr>
      `).join('');
      
      $('#userDetailsModal').modal('show');
    }

    function editUser(userId) {
      const user = currentUsers.find(u => u.id === userId);
      if (!user) return;
      
      // Populate edit form
      document.getElementById('editUserId').value = user.id;
      document.getElementById('editPlayerName').value = user.name;
      document.getElementById('editPlayerEmail').value = user.email;
      document.getElementById('editPlayerPhone').value = user.phone;
      document.getElementById('editPlayerSkill').value = user.skill;
      document.getElementById('editPlayerStatus').value = user.status;
      document.getElementById('editPlayerJoinDate').value = user.joinDate;
      document.getElementById('editPlayerGames').value = user.gamesPlayed;
      document.getElementById('editPlayerBestScore').value = user.bestScore;
      document.getElementById('editPlayerAverage').value = user.average;
      document.getElementById('editPlayerNotes').value = user.notes || '';
      
      $('#editUserModal').modal('show');
    }

    function editUserFromDetails() {
      const userId = document.getElementById('editUserId').value || 
                    currentUsers.find(u => u.name === document.getElementById('detailPlayerName').textContent)?.id;
      if (userId) {
        editUser(userId);
        $('#userDetailsModal').modal('hide');
      }
    }

    function updateUser() {
      const userId = parseInt(document.getElementById('editUserId').value);
      const userIndex = currentUsers.findIndex(u => u.id === userId);
      
      if (userIndex === -1) return;
      
      // Update user data
      currentUsers[userIndex] = {
        ...currentUsers[userIndex],
        name: document.getElementById('editPlayerName').value,
        email: document.getElementById('editPlayerEmail').value,
        phone: document.getElementById('editPlayerPhone').value,
        skill: document.getElementById('editPlayerSkill').value,
        status: document.getElementById('editPlayerStatus').value,
        notes: document.getElementById('editPlayerNotes').value
      };
      
      // Refresh display
      searchUsers();
      $('#editUserModal').modal('hide');
      showNotification('Player updated successfully!', 'success');
    }

    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this player? This action cannot be undone.')) {
        const userIndex = currentUsers.findIndex(u => u.id === userId);
        if (userIndex !== -1) {
          currentUsers.splice(userIndex, 1);
          searchUsers();
          showNotification('Player deleted successfully!', 'success');
        }
      }
    }

    function createPlayer() {
      const form = document.getElementById('createPlayerForm');
      const formData = new FormData(form);
      
      // Simulate API call
      setTimeout(() => {
        showNotification('Player account created successfully!', 'success');
        $('#createPlayerModal').modal('hide');
        form.reset();
        loadUsers(); // Refresh the user list
      }, 1000);
    }

    function exportUsers() {
      // Simulate export
      showNotification('Users exported successfully!', 'success');
    }

    function refreshUsers() {
      loadUsers();
      showNotification('Users refreshed!', 'info');
    }

    // Helper functions
    function getSkillBadgeColor(skill) {
      const colors = {
        'beginner': 'secondary',
        'intermediate': 'info',
        'advanced': 'warning',
        'pro': 'danger'
      };
      return colors[skill] || 'secondary';
    }

    function getStatusBadgeColor(status) {
      const colors = {
        'active': 'success',
        'inactive': 'secondary',
        'suspended': 'danger'
      };
      return colors[status] || 'secondary';
    }

    function capitalizeFirst(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
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
      loadUsers();
      
      // Search functionality
      const userSearch = document.getElementById('userSearch');
      const skillFilter = document.getElementById('skillFilter');
      const statusFilter = document.getElementById('statusFilter');
      
      if (userSearch) {
        userSearch.addEventListener('input', searchUsers);
      }
      if (skillFilter) {
        skillFilter.addEventListener('change', searchUsers);
      }
      if (statusFilter) {
        statusFilter.addEventListener('change', searchUsers);
      }
    });
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
