<?php
require_once 'includes/auth.php';
require_once 'includes/session-management.php';

// Require admin access
requireAdmin();

// Get current user info
$currentUser = getCurrentUser();

// Get active session and all sessions
$activeSession = getActiveSession();
$allSessions = getAllGameSessions();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_session':
            $sessionData = [
                'session_name' => $_POST['session_name'] ?? 'New Session',
                'session_date' => $_POST['session_date'] ?? date('Y-m-d'),
                'session_time' => $_POST['session_time'] ?? '14:00',
                'game_mode' => $_POST['game_mode'] ?? 'Solo',
                'max_players' => $_POST['max_players'] ?? 20,
                'created_by' => $currentUser['user_id'],
                'notes' => $_POST['notes'] ?? ''
            ];
            
            $sessionId = createGameSession($sessionData);
            if ($sessionId) {
                echo json_encode(['success' => true, 'session_id' => $sessionId, 'message' => 'Session created successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create session']);
            }
            exit;
            
        case 'start_session':
            $sessionId = $_POST['session_id'] ?? null;
            if ($sessionId && startSession($sessionId)) {
                echo json_encode(['success' => true, 'message' => 'Session started successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to start session']);
            }
            exit;
            
        case 'end_session':
            $sessionId = $_POST['session_id'] ?? null;
            if ($sessionId && endSession($sessionId)) {
                echo json_encode(['success' => true, 'message' => 'Session ended successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to end session']);
            }
            exit;
            
        case 'delete_session':
            $sessionId = $_POST['session_id'] ?? null;
            $deleteType = $_POST['delete_type'] ?? 'soft'; // 'soft' or 'hard'
            
            if (!$sessionId) {
                echo json_encode(['success' => false, 'message' => 'Session ID is required']);
                exit;
            }
            
            if ($deleteType === 'hard') {
                // Hard delete - removes all data permanently
                if (deleteGameSession($sessionId)) {
                    echo json_encode(['success' => true, 'message' => 'Session and all data deleted permanently!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete session']);
                }
            } else {
                // Soft delete - marks as deleted but keeps data
                if (softDeleteGameSession($sessionId)) {
                    echo json_encode(['success' => true, 'message' => 'Session marked as deleted!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete session']);
                }
            }
            exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Weekly Game Session Management - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .session-banner {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: 1px solid #10b981;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
    
    .session-card {
      transition: all 0.3s ease;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
    }
    
    .session-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .status-badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.375rem 0.75rem;
      border-radius: 6px;
    }
    
    .mode-badge {
      font-size: 0.75rem;
      font-weight: 500;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
    }
    
    .action-btn {
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .action-btn:hover {
      transform: translateY(-1px);
    }
    
    .stats-card {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border: 1px solid #e2e8f0;
      border-radius: 12px;
    }
    
    .create-session-btn {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      border: none;
      border-radius: 10px;
      font-weight: 600;
      padding: 0.75rem 1.5rem;
      transition: all 0.3s ease;
    }
    
    .create-session-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }
    
    .filter-section {
      background: #f8fafc;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .session-table {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .table th {
      background: #f8fafc;
      border-bottom: 2px solid #e5e7eb;
      font-weight: 600;
      color: #374151;
    }
    
    .no-sessions {
      text-align: center;
      padding: 4rem 2rem;
      color: #6b7280;
    }
    
    .no-sessions i {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

    <!-- Sidebar -->
    <aside class="left-sidebar" data-sidebarbg="skin6">
      <!-- Sidebar scroll-->
      <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
          <ul id="sidebarnav">
            <!-- User Profile-->
            <li class="sidebar-item text-center p-40 upgrade-btn">
              <a href="admin-dashboard.php" class="w-100 btn btn-dnger text-white d-flex align-items-center justify-content-center">
                <img src="./assets/images/logos/speedster main logo.png" alt="logo" width="30" class="me-2">
                <span class="fw-bold">SPEEDSTERS</span>
              </a>
            </li>
            
            <!-- Dashboard -->
            <li class="sidebar-item">
              <a href="./admin-dashboard.php" class="sidebar-link">
                <i class="ti ti-dashboard"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            
            <!-- Session Management -->
            <li class="sidebar-item">
              <a href="./admin-session-management.php" class="sidebar-link active">
                <i class="ti ti-calendar-event"></i>
                <span class="hide-menu">Session Management</span>
              </a>
            </li>
            
            <!-- Score Monitoring -->
            <li class="sidebar-item has-sub">
              <a class="sidebar-link" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-chart-line"></i>
                <span class="hide-menu">Score Monitoring</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level submenu">
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Players</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-team.php" class="sidebar-link">
                    <i class="ti ti-users-group"></i>
                    <span class="hide-menu">Team</span>
                  </a>
                </li>
              </ul>
            </li>
            
            <!-- User Management -->
            <li class="sidebar-item">
              <a href="./admin-user-management.php" class="sidebar-link">
                <i class="ti ti-users"></i>
                <span class="hide-menu">User Management</span>
              </a>
            </li>
            
            <!-- Events -->
            <li class="sidebar-item">
              <a href="./admin-events.php" class="sidebar-link">
                <i class="ti ti-calendar"></i>
                <span class="hide-menu">Events</span>
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
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
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
      <!--  Header End -->

      <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
          <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0 font-size-18">Weekly Game Session Management</h4>
              <div class="page-title-right">
                <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="./index.php">Home</a></li>
                  <li class="breadcrumb-item"><a href="./admin-dashboard.php">Admin Dashboard</a></li>
                  <li class="breadcrumb-item active">Session Management</li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h2 class="fw-bold text-dark mb-1">Weekly Game Session Management</h2>
                <p class="text-muted mb-0">Manage solo game sessions and score entry with enhanced controls</p>
              </div>
              <div class="d-flex gap-2">
                <button class="btn create-session-btn text-white" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                  <i class="ti ti-plus me-2"></i>Create New Session
                </button>
                <button class="btn btn-outline-primary action-btn" onclick="refreshData()">
                  <i class="ti ti-refresh"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Active Session Banner -->
        <?php if ($activeSession): ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="card session-banner text-white">
              <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i class="ti ti-calendar-event fs-1"></i>
                    </div>
                    <div>
                      <h4 class="mb-1 fw-bold">Active Session: <?php echo htmlspecialchars($activeSession['session_name']); ?></h4>
                      <div class="d-flex align-items-center gap-4">
                        <div class="d-flex align-items-center">
                          <i class="ti ti-calendar me-2"></i>
                          <span><?php echo date('l, M j, Y', strtotime($activeSession['session_date'])); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                          <i class="ti ti-clock me-2"></i>
                          <span><?php echo date('g:i A', strtotime($activeSession['session_time'])); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                          <i class="ti ti-user me-2"></i>
                          <span><?php echo ucfirst($activeSession['game_mode']); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                          <i class="ti ti-users me-2"></i>
                          <span><?php echo $activeSession['participant_count']; ?>/<?php echo $activeSession['max_players']; ?> registered</span>
                        </div>
                        <div class="d-flex align-items-center">
                          <i class="ti ti-trophy me-2"></i>
                          <span><?php echo $activeSession['players_played']; ?> played today</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <a href="admin-score-monitoring-solo.php" class="btn btn-warning action-btn">
                      <i class="ti ti-edit me-2"></i>Enter Scores
                    </a>
                    <button class="btn btn-danger action-btn" onclick="endSession(<?php echo $activeSession['session_id']; ?>)">
                      <i class="ti ti-square me-2"></i>End Session
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php else: ?>
        <div class="row mb-4">
          <div class="col-12">
            <div class="alert alert-info">
              <i class="ti ti-info-circle me-2"></i>
              <strong>No Active Session</strong> - Create a new session to start managing games and scores.
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card stats-card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="card-title text-muted mb-1">Total Sessions</h6>
                    <h3 class="mb-0 text-primary"><?php echo count($allSessions); ?></h3>
                    <small class="text-muted">All time</small>
                  </div>
                  <div class="ms-3">
                    <i class="ti ti-calendar-event fs-1 text-primary"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stats-card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="card-title text-muted mb-1">Active Sessions</h6>
                    <h3 class="mb-0 text-success"><?php echo count(array_filter($allSessions, fn($s) => $s['status'] === 'Active')); ?></h3>
                    <small class="text-muted">Currently running</small>
                  </div>
                  <div class="ms-3">
                    <i class="ti ti-play-circle fs-1 text-success"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stats-card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="card-title text-muted mb-1">Completed Sessions</h6>
                    <h3 class="mb-0 text-info"><?php echo count(array_filter($allSessions, fn($s) => $s['status'] === 'Completed')); ?></h3>
                    <small class="text-muted">Finished sessions</small>
                  </div>
                  <div class="ms-3">
                    <i class="ti ti-check-circle fs-1 text-info"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stats-card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="card-title text-muted mb-1">Total Players</h6>
                    <h3 class="mb-0 text-warning"><?php echo array_sum(array_column($allSessions, 'participant_count')); ?></h3>
                    <small class="text-muted">All participants</small>
                  </div>
                  <div class="ms-3">
                    <i class="ti ti-users fs-1 text-warning"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="filter-section">
              <div class="row align-items-center">
                <div class="col-md-3">
                  <label class="form-label fw-semibold">Filter by Status</label>
                  <select class="form-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Completed">Completed</option>
                    <option value="Scheduled">Scheduled</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold">Filter by Mode</label>
                  <select class="form-select" id="modeFilter">
                    <option value="all">All Modes</option>
                    <option value="Solo">Solo</option>
                    <option value="Doubles">Doubles</option>
                    <option value="Team">Team</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold">Sort by</label>
                  <select class="form-select" id="sortFilter">
                    <option value="date_desc">Date (Newest First)</option>
                    <option value="date_asc">Date (Oldest First)</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                    <option value="players_desc">Players (Most First)</option>
                    <option value="players_asc">Players (Least First)</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold">&nbsp;</label>
                  <div class="d-flex gap-2">
                    <button class="btn btn-primary action-btn" onclick="applyFilters()">
                      <i class="ti ti-filter me-2"></i>Apply Filters
                    </button>
                    <button class="btn btn-outline-secondary action-btn" onclick="clearFilters()">
                      <i class="ti ti-x me-2"></i>Clear
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sessions Table -->
        <div class="row">
          <div class="col-12">
            <div class="card session-table">
              <div class="card-header">
                <h5 class="card-title fw-semibold mb-0">Game Sessions</h5>
              </div>
              <div class="card-body p-0">
                <?php if (!empty($allSessions)): ?>
                <div class="table-responsive">
                  <table class="table table-hover mb-0" id="sessionsTable">
                    <thead>
                      <tr>
                        <th scope="col">Session Name</th>
                        <th scope="col">Date & Time</th>
                        <th scope="col">Mode</th>
                        <th scope="col">Players</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($allSessions as $session): ?>
                      <tr data-session-id="<?php echo $session['session_id']; ?>" 
                          data-status="<?php echo $session['status']; ?>" 
                          data-mode="<?php echo $session['game_mode']; ?>">
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="me-3">
                              <i class="ti ti-calendar-event text-primary"></i>
                            </div>
                            <div>
                              <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($session['session_name']); ?></h6>
                              <small class="text-muted">Created by <?php echo htmlspecialchars($session['created_by_name'] ?? 'Admin'); ?></small>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div>
                            <div class="fw-semibold"><?php echo date('M j, Y', strtotime($session['session_date'])); ?></div>
                            <small class="text-muted"><?php echo date('g:i A', strtotime($session['session_time'])); ?></small>
                          </div>
                        </td>
                        <td>
                          <span class="mode-badge bg-info text-white"><?php echo ucfirst($session['game_mode']); ?></span>
                        </td>
                        <td>
                          <div>
                            <div class="fw-semibold"><?php echo $session['participant_count']; ?>/<?php echo $session['max_players']; ?> registered</div>
                            <?php if ($session['players_played'] > 0): ?>
                            <small class="text-success fw-semibold"><?php echo $session['players_played']; ?> played</small>
                            <?php else: ?>
                            <small class="text-muted">No games played</small>
                            <?php endif; ?>
                          </div>
                        </td>
                        <td>
                          <?php
                          $statusClass = match($session['status']) {
                              'Active' => 'bg-success',
                              'Completed' => 'bg-info',
                              'Scheduled' => 'bg-warning',
                              default => 'bg-secondary'
                          };
                          ?>
                          <span class="status-badge <?php echo $statusClass; ?> text-white"><?php echo ucfirst($session['status']); ?></span>
                        </td>
                        <td>
                          <div class="d-flex gap-1">
                            <?php if ($session['status'] === 'Scheduled'): ?>
                            <button class="btn btn-success btn-sm action-btn" onclick="startSession(<?php echo $session['session_id']; ?>)">
                              <i class="ti ti-play"></i>
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($session['status'] === 'Active'): ?>
                            <a href="admin-score-monitoring-solo.php" class="btn btn-warning btn-sm action-btn">
                              <i class="ti ti-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm action-btn" onclick="endSession(<?php echo $session['session_id']; ?>)">
                              <i class="ti ti-square"></i>
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($session['status'] === 'Completed'): ?>
                            <a href="admin-score-monitoring-solo.php" class="btn btn-primary btn-sm action-btn" title="View Scores">
                              <i class="ti ti-chart-bar"></i>
                            </a>
                            <?php endif; ?>
                            
                            <button class="btn btn-info btn-sm action-btn" onclick="viewSessionDetails(<?php echo $session['session_id']; ?>)">
                              <i class="ti ti-eye"></i>
                            </button>
                            
                            <button class="btn btn-outline-danger btn-sm action-btn" onclick="deleteSession(<?php echo $session['session_id']; ?>, '<?php echo $session['session_name']; ?>')">
                              <i class="ti ti-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                <?php else: ?>
                <div class="no-sessions">
                  <i class="ti ti-calendar-off"></i>
                  <h4 class="mb-2">No Sessions Found</h4>
                  <p class="mb-3">Create your first game session to get started with managing bowling games.</p>
                  <button class="btn create-session-btn text-white" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                    <i class="ti ti-plus me-2"></i>Create New Session
                  </button>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Session Modal -->
  <div class="modal fade" id="createSessionModal" tabindex="-1" aria-labelledby="createSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createSessionModalLabel">Create New Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="createSessionForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sessionName" class="form-label">Session Name</label>
                  <input type="text" class="form-control" id="sessionName" name="session_name" 
                         placeholder="Enter session name" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="gameMode" class="form-label">Game Mode</label>
                  <select class="form-select" id="gameMode" name="game_mode" required>
                    <option value="Solo">Solo</option>
                    <option value="Doubles">Doubles</option>
                    <option value="Team">Team</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sessionDate" class="form-label">Session Date</label>
                  <input type="date" class="form-control" id="sessionDate" name="session_date" 
                         value="<?php echo date('Y-m-d'); ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sessionTime" class="form-label">Session Time</label>
                  <input type="time" class="form-control" id="sessionTime" name="session_time" 
                         value="14:00" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="maxPlayers" class="form-label">Max Players</label>
                  <input type="number" class="form-control" id="maxPlayers" name="max_players" 
                         value="20" min="1" max="50" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="sessionNotes" class="form-label">Notes (Optional)</label>
                  <input type="text" class="form-control" id="sessionNotes" name="notes" 
                         placeholder="Additional notes...">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Session</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>

  <script>
    // Create Session Form Handler
    document.getElementById('createSessionForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      formData.append('action', 'create_session');
      
      try {
        const response = await fetch('', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification(result.message, 'success');
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification(result.message, 'error');
        }
      } catch (error) {
        showNotification('An error occurred while creating the session', 'error');
      }
    });

    // Start Session
    async function startSession(sessionId) {
      if (!confirm('Are you sure you want to start this session?')) return;
      
      const formData = new FormData();
      formData.append('action', 'start_session');
      formData.append('session_id', sessionId);
      
      try {
        const response = await fetch('', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification(result.message, 'success');
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification(result.message, 'error');
        }
      } catch (error) {
        showNotification('An error occurred while starting the session', 'error');
      }
    }

    // End Session
    async function endSession(sessionId) {
      if (!confirm('Are you sure you want to end this session? This action cannot be undone.')) return;
      
      const formData = new FormData();
      formData.append('action', 'end_session');
      formData.append('session_id', sessionId);
      
      try {
        const response = await fetch('', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification(result.message, 'success');
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification(result.message, 'error');
        }
      } catch (error) {
        showNotification('An error occurred while ending the session', 'error');
      }
    }

    // View Session Details
    function viewSessionDetails(sessionId) {
      // This would open a modal or redirect to a details page
      showNotification('Session details feature coming soon!', 'info');
    }

    // Delete Session
    function deleteSession(sessionId, sessionName) {
      // Show confirmation dialog
      const confirmed = confirm(`Are you sure you want to delete the session "${sessionName}"?\n\nThis action cannot be undone and will remove all associated scores.`);
      
      if (!confirmed) {
        return;
      }
      
      // Show second confirmation for hard delete
      const hardDelete = confirm(`Choose deletion type:\n\nOK = Hard Delete (permanently removes all data)\nCancel = Soft Delete (marks as deleted but keeps data)`);
      
      const deleteType = hardDelete ? 'hard' : 'soft';
      const actionText = hardDelete ? 'permanently deleting' : 'marking as deleted';
      
      showNotification(`${actionText} session...`, 'info');
      
      // Send delete request
      const formData = new FormData();
      formData.append('action', 'delete_session');
      formData.append('session_id', sessionId);
      formData.append('delete_type', deleteType);
      
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message, 'success');
          // Remove the row from the table
          const row = document.querySelector(`tr[data-session-id="${sessionId}"]`);
          if (row) {
            row.remove();
          }
        } else {
          showNotification('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while deleting session', 'error');
      });
    }

    // Apply Filters
    function applyFilters() {
      const statusFilter = document.getElementById('statusFilter').value;
      const modeFilter = document.getElementById('modeFilter').value;
      const sortFilter = document.getElementById('sortFilter').value;
      
      const rows = document.querySelectorAll('#sessionsTable tbody tr');
      
      rows.forEach(row => {
        const status = row.dataset.status;
        const mode = row.dataset.mode;
        
        let showRow = true;
        
        if (statusFilter !== 'all' && status !== statusFilter) {
          showRow = false;
        }
        
        if (modeFilter !== 'all' && mode !== modeFilter) {
          showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
      });
      
      showNotification('Filters applied successfully!', 'success');
    }

    // Clear Filters
    function clearFilters() {
      document.getElementById('statusFilter').value = 'all';
      document.getElementById('modeFilter').value = 'all';
      document.getElementById('sortFilter').value = 'date_desc';
      
      const rows = document.querySelectorAll('#sessionsTable tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
      
      showNotification('Filters cleared!', 'info');
    }

    // Refresh Data
    function refreshData() {
      location.reload();
    }

    // Notification System
    function showNotification(message, type = 'info') {
      const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
      }[type] || 'alert-info';
      
      const notification = document.createElement('div');
      notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      document.body.appendChild(notification);
      
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification);
        }
      }, 5000);
    }
  </script>
</body>

</html>
