<?php
require_once 'includes/auth.php';
require_once 'includes/dashboard.php';
require_once 'includes/user-management.php';
requireAdmin(); // Ensure user is admin

// Get current user info
$currentUser = getCurrentUser();

// Get all users data
function getAllUsersData() {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.skill_level,
                u.user_role,
                u.status,
                u.team_name,
                u.created_at,
                COUNT(gs.score_id) as total_games,
                AVG(gs.player_score) as avg_score,
                MAX(gs.player_score) as best_score
            FROM users u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id AND gs.status = 'Completed'
            WHERE u.user_role = 'Player'
            GROUP BY u.user_id, u.username, u.first_name, u.last_name, u.email, u.phone, u.skill_level, u.user_role, u.status, u.team_name, u.created_at
            ORDER BY u.first_name, u.last_name
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

$allUsers = getAllUsersData();
?>
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
                      <p class="mb-0 fs-3"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?> (Admin)</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-settings fs-6"></i>
                      <p class="mb-0 fs-3">Settings</p>
                    </a>
                    <a href="./logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
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
                      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
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
                        <?php if (!empty($allUsers) && !isset($allUsers['error'])): ?>
                          <?php foreach ($allUsers as $user): ?>
                            <tr>
                              <td>
                                <input type="checkbox" class="user-checkbox" value="<?php echo $user['user_id']; ?>">
                              </td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <img src="assets/images/profile/user-<?php echo ($user['user_id'] % 8) + 1; ?>.jpg" alt="Player" class="rounded-circle me-2" width="32" height="32">
                                  <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h6>
                                    <small class="text-muted">Team: <?php echo htmlspecialchars($user['team_name'] ?? 'No Team'); ?></small>
                                  </div>
                                </div>
                              </td>
                              <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                              <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                              <td>
                                <?php 
                                $skillBadgeClass = 'bg-secondary';
                                if ($user['skill_level'] == 'beginner') $skillBadgeClass = 'bg-info';
                                if ($user['skill_level'] == 'intermediate') $skillBadgeClass = 'bg-warning';
                                if ($user['skill_level'] == 'advanced') $skillBadgeClass = 'bg-primary';
                                if ($user['skill_level'] == 'pro') $skillBadgeClass = 'bg-success';
                                ?>
                                <span class="badge <?php echo $skillBadgeClass; ?>"><?php echo ucfirst($user['skill_level'] ?? 'Unknown'); ?></span>
                              </td>
                              <td>
                                <?php 
                                $statusBadgeClass = 'bg-secondary';
                                if ($user['status'] == 'Active') $statusBadgeClass = 'bg-success';
                                if ($user['status'] == 'Inactive') $statusBadgeClass = 'bg-warning';
                                if ($user['status'] == 'Suspended') $statusBadgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $statusBadgeClass; ?>"><?php echo ucfirst($user['status'] ?? 'Unknown'); ?></span>
                              </td>
                              <td><?php echo $user['total_games'] ?? '0'; ?></td>
                              <td><span class="fw-bold text-warning"><?php echo $user['best_score'] ?? '0'; ?></span></td>
                              <td><?php echo $user['avg_score'] ? number_format($user['avg_score'], 1) : '0.0'; ?></td>
                              <td><?php echo $user['created_at'] ? date('M j, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                              <td>
                                <div class="btn-group" role="group">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewUser(<?php echo $user['user_id']; ?>)" title="View Details">
                                    <i class="ti ti-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-warning" onclick="editUser(<?php echo $user['user_id']; ?>)" title="Edit User">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>)" title="Delete User">
                                    <i class="ti ti-trash"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                              <i class="ti ti-users fs-1 mb-2"></i>
                              <p class="mb-0">No users found</p>
                              <small>Users will appear here once they are added to the system</small>
                            </td>
                          </tr>
                        <?php endif; ?>
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

    // No dummy data - using PHP data from database
    // Table is rendered by PHP - no JavaScript rendering needed

    // Simplified functions for user management
    function toggleSelectAll() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.querySelectorAll('.user-checkbox');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
      });
    }

    // CRUD Functions
    function viewUser(userId) {
      console.log('Attempting to view user:', userId);
      
      fetch('ajax/user-crud.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=view&user_id=' + userId
      })
      .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
          throw new Error('HTTP error! status: ' + response.status);
        }
        
        return response.text(); // Get as text first to see what we're getting
      })
      .then(text => {
        console.log('Raw response:', text);
        
        try {
          const data = JSON.parse(text);
          console.log('Parsed response data:', data);
          
          if (data.success) {
            console.log('User data received:', data.data);
            populateViewModal(data.data);
            $('#viewUserModal').modal('show');
          } else {
            console.error('Server error:', data.message);
            alert('Error: ' + data.message);
          }
        } catch (e) {
          console.error('JSON parse error:', e);
          console.error('Raw response text:', text);
          alert('Invalid response from server: ' + text.substring(0, 100));
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        alert('An error occurred while fetching user details: ' + error.message);
      });
    }

    function editUser(userId) {
      fetch('ajax/user-crud.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=view&user_id=' + userId
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          populateEditModal(data.data);
          $('#editUserModal').modal('show');
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while fetching user details');
      });
    }

    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch('ajax/user-crud.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=delete&user_id=' + userId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('User deleted successfully');
            location.reload(); // Refresh the page
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting user');
        });
      }
    }

    function populateViewModal(user) {
      console.log('Populating modal with user data:', user);
      
      // Handle undefined values safely
      const firstName = user.first_name || '';
      const lastName = user.last_name || '';
      const fullName = (firstName + ' ' + lastName).trim() || 'Unknown User';
      
      document.getElementById('viewUserName').textContent = fullName;
      document.getElementById('viewUserTeam').textContent = 'Team: ' + (user.team_name || 'No Team');
      document.getElementById('viewUserUsername').textContent = user.username || 'N/A';
      document.getElementById('viewUserEmail').textContent = user.email || 'N/A';
      document.getElementById('viewUserPhone').textContent = user.phone || 'N/A';
      document.getElementById('viewUserSkill').textContent = user.skill_level || 'N/A';
      document.getElementById('viewUserStatus').textContent = user.status || 'N/A';
      document.getElementById('viewUserGames').textContent = user.total_games || '0';
      document.getElementById('viewUserBestScore').textContent = user.best_score || '0';
      document.getElementById('viewUserAvgScore').textContent = user.avg_score ? parseFloat(user.avg_score).toFixed(1) : '0.0';
      document.getElementById('viewUserJoined').textContent = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
      
      // Update avatar
      document.getElementById('viewUserAvatar').src = 'assets/images/profile/user-' + ((user.user_id % 8) + 1) + '.jpg';
      
      // Populate recent games
      const gamesTable = document.getElementById('viewUserGamesTable');
      if (user.recent_games && user.recent_games.length > 0) {
        gamesTable.innerHTML = user.recent_games.map(game => `
          <tr>
            <td>${new Date(game.game_date).toLocaleDateString()}</td>
            <td><span class="badge bg-primary">${game.game_mode}</span></td>
            <td>${game.player_score}</td>
            <td>${game.team_name || 'Solo'}</td>
            <td>${game.lane_number}</td>
        </tr>
      `).join('');
      } else {
        gamesTable.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent games</td></tr>';
      }
    }

    function populateEditModal(user) {
      document.getElementById('editUserId').value = user.user_id;
      document.getElementById('editUsername').value = user.username;
      document.getElementById('editFirstName').value = user.first_name;
      document.getElementById('editLastName').value = user.last_name;
      document.getElementById('editEmail').value = user.email;
      document.getElementById('editPhone').value = user.phone || '';
      document.getElementById('editSkillLevel').value = user.skill_level;
      document.getElementById('editStatus').value = user.status;
      document.getElementById('editTeamName').value = user.team_name || '';
    }

    function editUserFromView() {
      $('#viewUserModal').modal('hide');
      setTimeout(() => {
        editUser(document.getElementById('editUserId').value);
      }, 300);
    }

    function exportUsers() {
      alert('Export users functionality');
    }

    function refreshUsers() {
      location.reload();
    }

    function clearFilters() {
      document.getElementById('userSearch').value = '';
      document.getElementById('skillFilter').value = '';
      document.getElementById('statusFilter').value = '';
    }

    // Form submission handlers
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('action', 'update');
      
      fetch('ajax/user-crud.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('User updated successfully');
          $('#editUserModal').modal('hide');
          location.reload(); // Refresh the page
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating user');
      });
    });

    document.getElementById('createUserForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('action', 'create');
      
      fetch('ajax/user-crud.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('User created successfully');
          $('#createUserModal').modal('hide');
          this.reset(); // Reset form
          location.reload(); // Refresh the page
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating user');
      });
    });

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      // Page is ready - PHP data is already loaded
    });




    // All old functions removed - using PHP data only

    // Tournament countdown functionality
    function updateCountdown() {
      const tournamentDate = new Date('2024-12-25T00:00:00').getTime();
      const now = new Date().getTime();
      const distance = tournamentDate - now;
      
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

  <?php include 'modals/user-management-modals.php'; ?>

</body>

</html>
