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
            
        case 'get_session':
            $sessionId = $_POST['session_id'] ?? null;
            if (!$sessionId) {
                echo json_encode(['success' => false, 'message' => 'Session ID is required']);
                exit;
            }
            
            $session = getSessionById($sessionId);
            if ($session) {
                echo json_encode(['success' => true, 'session' => $session]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Session not found']);
            }
            exit;
            
        case 'update_session':
            $sessionId = $_POST['session_id'] ?? null;
            if (!$sessionId) {
                echo json_encode(['success' => false, 'message' => 'Session ID is required']);
                exit;
            }
            
            $updateData = [
                'session_name' => $_POST['session_name'] ?? '',
                'session_date' => $_POST['session_date'] ?? '',
                'session_time' => $_POST['session_time'] ?? '',
                'game_mode' => $_POST['game_mode'] ?? 'Solo',
                'max_players' => $_POST['max_players'] ?? 20,
                'status' => $_POST['status'] ?? 'Scheduled',
                'notes' => $_POST['notes'] ?? ''
            ];
            
            if (updateGameSession($sessionId, $updateData)) {
                echo json_encode(['success' => true, 'message' => 'Session updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update session']);
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
    
    /* Mobile Responsiveness */
    @media (max-width: 768px) {
      .container-fluid {
        padding-left: 8px;
        padding-right: 8px;
      }
      
      .body-wrapper-inner {
        padding-top: 10px;
      }
      
      /* Page header adjustments */
      .page-title-box {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 5px;
        margin-bottom: 0.5rem;
      }
      
      .breadcrumb {
        margin-bottom: 0 !important;
        font-size: 0.8rem;
        padding: 0.25rem 0;
      }
      
      /* Reduce row margins */
      .row {
        margin-bottom: 0.75rem !important;
      }
      
      /* Session banner mobile */
    .session-banner {
        margin-bottom: 0.75rem !important;
      }
      
      .session-banner .card-body {
        padding: 0.75rem !important;
      }
      
      .session-banner .d-flex {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
      
      .session-banner .btn-group {
        width: 100%;
        justify-content: center;
      }
      
      .session-banner h4 {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
      }
      
      /* Stats cards mobile */
      .col-lg-3.col-md-6 {
        margin-bottom: 0.5rem !important;
      }
      
      .stats-card .card-body,
      .team-card .card-body,
      .player-card .card-body {
        padding: 0.75rem;
        text-align: center;
      }
      
      .stats-card h3,
      .team-card h3,
      .player-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
      }
      
      .stats-card h6,
      .team-card h6,
      .player-card h6 {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
      }
      
      .stats-card small,
      .team-card small,
      .player-card small {
        font-size: 0.7rem;
      }
      
      /* Main card header mobile */
      .card-title {
        font-size: 1rem;
        margin-bottom: 0.25rem;
      }
      
      .card .d-flex.justify-content-between {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
      
      .card-body {
        padding: 0.75rem;
      }
      
      /* Button groups mobile */
      .btn-group {
        width: 100%;
        flex-wrap: wrap;
        gap: 5px;
      }
      
      .btn-group .btn {
        flex: 1;
        min-width: auto;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        margin: 0;
      }
      
      /* Table mobile improvements */
      .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin: 0 -10px;
        padding: 0 10px;
      }
      
      .table {
        margin-bottom: 0;
        font-size: 0.875rem;
    }
    
    .table th {
        border-top: none;
        font-size: 0.8rem;
        padding: 0.75rem 0.5rem;
        white-space: nowrap;
        text-align: center;
      }
      
      .table td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
      text-align: center;
      }
      
      /* Action buttons in table */
      .table .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.8rem;
        margin: 0.1rem;
      }
      
      .table .d-flex.gap-1 {
        gap: 0.25rem !important;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
      }
      
      /* Badge adjustments */
      .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
      }
      
      /* Modal mobile */
      .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
      }
      
      .modal-body .row {
        margin: 0;
      }
      
      .modal-body .col-md-6 {
        padding: 0 0.5rem;
      margin-bottom: 1rem;
      }
      
      /* Form controls mobile */
      .form-control,
      .form-select {
        font-size: 1rem;
        padding: 0.75rem;
      }
      
      .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
      }
      
      /* Alert mobile */
      .alert {
        padding: 0.75rem;
        font-size: 0.875rem;
        margin: 0 -8px 0.75rem -8px;
      }
      
      /* Reduce overall spacing */
      .mb-4 {
        margin-bottom: 0.75rem !important;
      }
      
      .p-4 {
        padding: 0.75rem !important;
      }
      
      /* Compact table */
      .table-responsive {
        margin-top: 0.5rem;
      }
    }
    
    /* Small mobile screens */
    @media (max-width: 576px) {
      .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
      }
      
      .body-wrapper-inner {
        padding-top: 5px;
      }
      
      .row {
        margin-bottom: 0.5rem !important;
      }
      
      .col-lg-3.col-md-6 {
        margin-bottom: 0.25rem !important;
      }
      
      /* Hide some table columns on very small screens */
      .table th:nth-child(3), /* Mode column */
      .table td:nth-child(3) {
        display: none;
      }
      
      .table th:nth-child(4), /* Players column */
      .table td:nth-child(4) {
        font-size: 0.75rem;
        padding: 0.5rem 0.25rem;
      }
      
      /* Smaller action buttons */
      .table .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        min-width: 32px;
        height: 32px;
      }
      
      .btn .ti {
        font-size: 1rem;
      }
      
      /* Stats cards smaller text */
      .stats-card h6,
      .team-card h6,
      .player-card h6 {
        font-size: 0.8rem;
      }
      
      .stats-card small,
      .team-card small,
      .player-card small {
        font-size: 0.7rem;
      }
      
      .stats-card h3,
      .team-card h3,
      .player-card h3 {
        font-size: 1.25rem;
      }
      
      /* Modal adjustments for small screens */
      .modal-body .col-md-6 {
        padding: 0 0.25rem;
        margin-bottom: 0.75rem;
      }
      
      .modal-header .modal-title {
        font-size: 1rem;
      }
      
      .modal-footer .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
      }
    }
    
    /* Tablet adjustments */
    @media (min-width: 769px) and (max-width: 992px) {
      .table th,
      .table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
      }
      
      .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
      }
      
      .stats-card .card-body,
      .team-card .card-body,
      .player-card .card-body {
        padding: 1.25rem;
      }
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
                  <li class="breadcrumb-item active">Session Management</li>
                </ol>
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

          <!-- Session Statistics Overview -->
          <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card stats-card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                      <h6 class="card-title text-white-50 mb-1">Total Sessions</h6>
                      <h3 class="mb-0 text-white"><?php echo count($allSessions); ?></h3>
                      <small class="text-white-50">All time</small>
                  </div>
                  <div class="ms-3">
                      <i class="ti ti-calendar-event fs-1 text-white-50"></i>
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
                      <h6 class="card-title text-white-50 mb-1">Active Sessions</h6>
                      <h3 class="mb-0 text-white"><?php echo count(array_filter($allSessions, fn($s) => $s['status'] === 'Active')); ?></h3>
                      <small class="text-white-50">Currently running</small>
                  </div>
                  <div class="ms-3">
                      <i class="ti ti-play-circle fs-1 text-white-50"></i>
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
                      <h6 class="card-title text-white-50 mb-1">Completed Sessions</h6>
                      <h3 class="mb-0 text-white"><?php echo count(array_filter($allSessions, fn($s) => $s['status'] === 'Completed')); ?></h3>
                      <small class="text-white-50">Finished sessions</small>
                  </div>
                  <div class="ms-3">
                      <i class="ti ti-check-circle fs-1 text-white-50"></i>
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
                      <h6 class="card-title text-white-50 mb-1">Total Participants</h6>
                      <h3 class="mb-0 text-white"><?php echo array_sum(array_column($allSessions, 'participant_count')); ?></h3>
                      <small class="text-white-50">All participants</small>
                  </div>
                  <div class="ms-3">
                      <i class="ti ti-users fs-1 text-white-50"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

          <!-- Main Content Row -->
          <div class="row">
            <!-- Session Management -->
            <div class="col-lg-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Session Management</h5>
                      <span class="fw-normal text-muted">Create and manage bowling sessions with participant selection</span>
                </div>
                  <div class="d-flex gap-2">
                      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                        <i class="ti ti-plus me-1"></i>Create New Session
                    </button>
                      <button class="btn btn-primary btn-sm" onclick="refreshData()">
                        <i class="ti ti-refresh"></i>
                    </button>
                  </div>
                </div>
                  
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
                    <table class="table table-hover" id="sessionsTable">
                    <thead>
                      <tr>
                          <th style="min-width: 120px;">Session Name</th>
                          <th style="min-width: 100px;">Date & Time</th>
                          <th style="min-width: 70px;">Mode</th>
                          <th style="min-width: 80px;">Players</th>
                          <th style="min-width: 80px;">Status</th>
                          <th style="min-width: 120px;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($allSessions, 0, 10) as $session): ?>
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
                          <div class="d-flex gap-1">
                            <?php if ($session['status'] === 'Scheduled'): ?>
                                  <a href="select-participants.php?session_id=<?php echo $session['session_id']; ?>" class="btn btn-primary btn-sm" title="Select Participants">
                                    <i class="ti ti-users"></i>
                                  </a>
                                  <button class="btn btn-success btn-sm" onclick="startSession(<?php echo $session['session_id']; ?>)" title="Start Session">
                                    <i class="ti ti-player-play"></i>
                            </button>
                                  <button class="btn btn-info btn-sm" onclick="editSession(<?php echo $session['session_id']; ?>)" title="Edit Session">
                                    <i class="ti ti-edit"></i>
                                  </button>
                                <?php elseif ($session['status'] === 'Active'): ?>
                                  <a href="<?php echo $session['game_mode'] === 'Solo' ? 'admin-score-monitoring-solo.php' : 'admin-score-monitoring-team.php'; ?>?session=<?php echo $session['session_id']; ?>" class="btn btn-warning btn-sm" title="Enter Scores">
                              <i class="ti ti-edit"></i>
                            </a>
                                  <a href="select-participants.php?session_id=<?php echo $session['session_id']; ?>" class="btn btn-primary btn-sm" title="Manage Participants">
                                    <i class="ti ti-users"></i>
                                  </a>
                                  <button class="btn btn-danger btn-sm" onclick="endSession(<?php echo $session['session_id']; ?>)" title="End Session">
                                    <i class="ti ti-player-stop"></i>
                            </button>
                                <?php elseif ($session['status'] === 'Completed'): ?>
                                  <a href="<?php echo $session['game_mode'] === 'Solo' ? 'admin-score-monitoring-solo.php' : 'admin-score-monitoring-team.php'; ?>?session=<?php echo $session['session_id']; ?>" class="btn btn-info btn-sm" title="View Scores">
                              <i class="ti ti-chart-bar"></i>
                            </a>
                                  <button class="btn btn-secondary btn-sm" onclick="editSession(<?php echo $session['session_id']; ?>)" title="Edit Session">
                                    <i class="ti ti-edit"></i>
                                  </button>
                            <?php endif; ?>
                            
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteSession(<?php echo $session['session_id']; ?>, '<?php echo htmlspecialchars($session['session_name']); ?>')" title="Delete Session">
                              <i class="ti ti-trash"></i>
                            </button>
                          </div>
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
            <button type="submit" class="btn btn-primary">Next: Select Participants →</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Session Modal -->
  <div class="modal fade" id="editSessionModal" tabindex="-1" aria-labelledby="editSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editSessionModalLabel">Edit Session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editSessionForm">
          <input type="hidden" id="editSessionId" name="session_id">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editSessionName" class="form-label">Session Name</label>
                  <input type="text" class="form-control" id="editSessionName" name="session_name" 
                         placeholder="Enter session name" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editGameMode" class="form-label">Game Mode</label>
                  <select class="form-select" id="editGameMode" name="game_mode" required>
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
                  <label for="editSessionDate" class="form-label">Session Date</label>
                  <input type="date" class="form-control" id="editSessionDate" name="session_date" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editSessionTime" class="form-label">Session Time</label>
                  <input type="time" class="form-control" id="editSessionTime" name="session_time" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editMaxPlayers" class="form-label">Max Players</label>
                  <input type="number" class="form-control" id="editMaxPlayers" name="max_players" 
                         min="1" max="50" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editSessionStatus" class="form-label">Session Status</label>
                  <select class="form-select" id="editSessionStatus" name="status" required>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Active">Active</option>
                    <option value="Paused">Paused</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                  <small class="form-text text-muted">
                    <strong>Tip:</strong> Changing status affects available actions and score entry permissions.
                  </small>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="editSessionNotes" class="form-label">Notes (Optional)</label>
                  <input type="text" class="form-control" id="editSessionNotes" name="notes" 
                         placeholder="Additional notes...">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Update Session</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Session Modal -->
  <div class="modal fade" id="deleteSessionModal" tabindex="-1" aria-labelledby="deleteSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteSessionModalLabel">
            <i class="ti ti-trash me-2 text-danger"></i>
            Delete Session
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="ti ti-alert-triangle me-2"></i>
            <strong>Warning!</strong> This action cannot be undone.
          </div>
          
          <p>You are about to delete the session: <strong id="deleteSessionName"></strong></p>
          
          <div class="mb-3">
            <label for="deleteSessionConfirm" class="form-label">
              Type the session name to confirm deletion:
            </label>
            <input type="text" class="form-control" id="deleteSessionConfirm" 
                   placeholder="Enter session name exactly" 
                   autocomplete="off" 
                   spellcheck="false"
                   style="background-color: white !important; pointer-events: auto !important;">
                  <small class="form-text text-muted">This helps prevent accidental deletions</small>
          </div>
          
          <div class="mb-3">
            <label for="deleteType" class="form-label">Deletion Type:</label>
            <select class="form-select" id="deleteType">
              <option value="soft">Soft Delete (Mark as deleted, keep data)</option>
              <option value="hard">Hard Delete (Permanently remove all data)</option>
            </select>
            <small class="form-text text-muted">
              <strong>Soft Delete:</strong> Hides the session but keeps all data for recovery<br>
              <strong>Hard Delete:</strong> Permanently removes session and all associated scores
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ti ti-x me-1"></i>Cancel
          </button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDeleteSession()" disabled>
            <i class="ti ti-trash me-1"></i>Delete Session
          </button>
        </div>
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
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="ti ti-loader"></i> Creating Session...';
      
      const formData = new FormData(this);
      formData.append('action', 'create_session_draft');
      
      try {
        const response = await fetch('ajax/session-creation.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification(result.message, 'success');
          // Close modal and redirect to participant selection
          const modal = bootstrap.Modal.getInstance(document.getElementById('createSessionModal'));
          modal.hide();
          
          setTimeout(() => {
            window.location.href = result.redirect_url;
          }, 1000);
        } else {
          showNotification(result.message, 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      } catch (error) {
        showNotification('An error occurred while creating the session', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
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

    // Edit Session
    async function editSession(sessionId) {
      try {
        // First, get the session data
        const response = await fetch('', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=get_session&session_id=${sessionId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
          const session = result.session;
          
          // Populate the edit form
          document.getElementById('editSessionId').value = session.session_id;
          document.getElementById('editSessionName').value = session.session_name;
          document.getElementById('editGameMode').value = session.game_mode;
          document.getElementById('editSessionDate').value = session.session_date;
          document.getElementById('editSessionTime').value = session.session_time;
          document.getElementById('editMaxPlayers').value = session.max_players;
          document.getElementById('editSessionStatus').value = session.status;
          document.getElementById('editSessionNotes').value = session.notes || '';
          
          // Show the modal
          const modal = new bootstrap.Modal(document.getElementById('editSessionModal'));
          modal.show();
        } else {
          showNotification('Error loading session data: ' + result.message, 'error');
        }
      } catch (error) {
        showNotification('An error occurred while loading session data', 'error');
      }
    }

    // Delete Session - Open Modal
    let currentDeleteSessionId = null;
    let currentDeleteSessionName = null;
    
    function deleteSession(sessionId, sessionName) {
      currentDeleteSessionId = sessionId;
      currentDeleteSessionName = sessionName;
      
      // Populate modal
      document.getElementById('deleteSessionName').textContent = sessionName;
      document.getElementById('deleteSessionConfirm').value = '';
      document.getElementById('deleteType').value = 'soft';
      document.getElementById('confirmDeleteBtn').disabled = true;
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('deleteSessionModal'));
      modal.show();
      
      // Focus on the input field after modal is shown
      setTimeout(() => {
        const inputField = document.getElementById('deleteSessionConfirm');
        
        inputField.focus();
        inputField.click();
        
      }, 300);
    }
    
    // Confirm Delete Session
    function confirmDeleteSession() {
      const confirmInput = document.getElementById('deleteSessionConfirm').value;
      const deleteType = document.getElementById('deleteType').value;
      
      if (confirmInput !== currentDeleteSessionName) {
        showNotification('Session name does not match. Please type the exact session name.', 'warning');
        return;
      }
      
      const confirmBtn = document.getElementById('confirmDeleteBtn');
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = '<i class="ti ti-loader"></i> Deleting...';
      
      // Send delete request
      const formData = new FormData();
      formData.append('action', 'delete_session');
      formData.append('session_id', currentDeleteSessionId);
      formData.append('delete_type', deleteType);
      
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message, 'success');
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('deleteSessionModal'));
          modal.hide();
          // Reload page
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification('Error: ' + data.message, 'error');
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = '<i class="ti ti-trash me-1"></i>Delete Session';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while deleting session', 'error');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="ti ti-trash me-1"></i>Delete Session';
      });
    }
    
    // Enable/disable delete button based on input
    document.addEventListener('DOMContentLoaded', function() {
      const deleteConfirmInput = document.getElementById('deleteSessionConfirm');
      const confirmBtn = document.getElementById('confirmDeleteBtn');
      
      if (deleteConfirmInput) {
        deleteConfirmInput.addEventListener('input', function() {
          const inputValue = this.value.trim();
          
          if (inputValue === currentDeleteSessionName) {
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('btn-secondary');
            confirmBtn.classList.add('btn-danger');
          } else {
            confirmBtn.disabled = true;
            confirmBtn.classList.remove('btn-danger');
            confirmBtn.classList.add('btn-secondary');
          }
        });
        
        // Also handle keyup and paste events
        deleteConfirmInput.addEventListener('keyup', function() {
          this.dispatchEvent(new Event('input'));
        });
        
        deleteConfirmInput.addEventListener('paste', function() {
          setTimeout(() => {
            this.dispatchEvent(new Event('input'));
          }, 10);
        });
      }
    });

    // Edit Session Form Handler
    document.getElementById('editSessionForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="ti ti-loader"></i> Updating...';
      
      const formData = new FormData(this);
      formData.append('action', 'update_session');
      
      try {
        const response = await fetch('', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          showNotification(result.message, 'success');
          const modal = bootstrap.Modal.getInstance(document.getElementById('editSessionModal'));
          modal.hide();
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification(result.message, 'error');
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      } catch (error) {
        showNotification('An error occurred while updating the session', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });

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
