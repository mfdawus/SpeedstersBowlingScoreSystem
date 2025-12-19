<?php
// Start session safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/session-management.php';
require_once 'includes/auth.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: authentication-login.php');
    exit();
}

// Get session draft data
$sessionDraft = null;
$sessionId = null;

if (isset($_GET['session_id'])) {
    $sessionId = (int)$_GET['session_id'];
    $sessionDraft = getSessionById($sessionId);
    
    if (!$sessionDraft) {
        header('Location: admin-session-management.php?error=session_not_found');
        exit();
    }
} else {
    header('Location: admin-session-management.php?error=no_session_id');
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_participants':
                try {
                    error_log("Saving participants - POST data: " . json_encode($_POST));
                    
                    $participantIds = json_decode($_POST['participant_ids'], true);
                    error_log("Decoded participant IDs: " . json_encode($participantIds));
                    
                    if (empty($participantIds)) {
                        echo json_encode(['success' => false, 'message' => 'No participants selected']);
                        exit();
                    }
                    
                    if (count($participantIds) > $sessionDraft['max_players']) {
                        echo json_encode(['success' => false, 'message' => 'Too many participants selected']);
                        exit();
                    }
                    
                    // Use direct database insertion instead of function call
                    $pdo = getDBConnection();
                    
                    // Clear existing participants first
                    $clearStmt = $pdo->prepare("DELETE FROM session_participants WHERE session_id = ?");
                    $clearResult = $clearStmt->execute([$sessionId]);
                    error_log("Clear existing participants result: " . ($clearResult ? 'success' : 'failed'));
                    
                    // Add new participants
                    $stmt = $pdo->prepare("INSERT INTO session_participants (session_id, user_id, joined_at) VALUES (?, ?, NOW())");
                    
                    $successCount = 0;
                    foreach ($participantIds as $userId) {
                        $result = $stmt->execute([$sessionId, $userId]);
                        if ($result) {
                            $successCount++;
                        }
                        error_log("Added participant $userId: " . ($result ? 'success' : 'failed'));
                    }
                    
                    if ($successCount === count($participantIds)) {
                        echo json_encode(['success' => true, 'message' => 'Participants saved successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => "Only $successCount of " . count($participantIds) . " participants saved"]);
                    }
                    
                } catch (Exception $e) {
                    error_log("Error saving participants: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
                exit();
                
            case 'get_players_by_team':
                $teamName = $_POST['team_name'] ?? 'VipersVenoms';
                $excludeIds = json_decode($_POST['exclude_ids'] ?? '[]', true);
                
                $players = getPlayersByTeamWithAverages($teamName === 'all' ? null : $teamName, $excludeIds);
                echo json_encode(['success' => true, 'players' => $players]);
                exit();
                
            case 'search_players':
                $searchTerm = $_POST['search_term'] ?? '';
                $teamName = $_POST['team_name'] ?? 'VipersVenoms';
                $excludeIds = json_decode($_POST['exclude_ids'] ?? '[]', true);
                
                $players = getPlayersByTeamWithAverages($teamName === 'all' ? null : $teamName, $excludeIds);
                
                // Filter by search term
                if (!empty($searchTerm)) {
                    $players = array_filter($players, function($player) use ($searchTerm) {
                        $fullName = $player['first_name'] . ' ' . $player['last_name'];
                        return stripos($fullName, $searchTerm) !== false || 
                               stripos($player['username'], $searchTerm) !== false;
                    });
                }
                
                echo json_encode(['success' => true, 'players' => array_values($players)]);
                exit();
        }
    }
}

// Get initial data
$teams = getAllTeamNames();
$currentParticipants = getSessionParticipants($sessionId);
$currentParticipantIds = array_column($currentParticipants, 'user_id');
$availablePlayers = getAvailablePlayersWithAverages([], true); // Get ALL players, don't exclude any

// Debug output
error_log("Session ID: " . $sessionId);
error_log("Current participants count: " . count($currentParticipants));
error_log("Available players count: " . count($availablePlayers));
error_log("Teams count: " . count($teams));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Select Participants - VipersVenoms Bowling</title>
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
    <style>
        /* Match admin dashboard styling */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
        
        .admin-card {
            transition: all 0.3s ease;
            border-left: 4px solid #0d6efd;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .participant-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .participant-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .participant-card.selected {
            border-color: #0d6efd;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
        }
        
        .participant-card.vipersvenoms {
            border-left: 4px solid #28a745;
        }
        
        .selected-participants {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: 1px solid #10b981;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
            color: white;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .player-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .skill-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
        }
        
        .average-score {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0d6efd;
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
            border-radius: 7px;
            border-color: #dfe5ef;
        }
        
        .team-section-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .vipersvenoms-section {
            border: 2px solid #28a745;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
        }
        
        .other-teams-section {
            border: 2px solid #6c757d;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15);
        }
        
        .other-teams-section .team-section-header {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .selection-counter {
            position: sticky;
            top: 20px;
            z-index: 100;
        }
        
        .filter-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
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
        
        /* Table styling to match admin pages */
        .table th {
            background: #f8fafc;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
            color: #374151;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .table tbody tr:hover {
            background: rgba(0,123,255,0.05);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        /* Group assignment styles */
        .group-container {
            min-height: 200px;
            transition: background-color 0.2s;
        }
        
        .group-container.drag-over {
            background-color: #e3f2fd !important;
            border-color: #2196F3 !important;
        }
        
        .player-card {
            transition: all 0.2s;
            user-select: none;
        }
        
        .player-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .player-card.dragging {
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <?php include 'includes/app-topstrip.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="body-wrapper">
            <?php include 'includes/header.php'; ?>
            
            <div class="body-wrapper-inner">
                <div class="container-fluid" style="margin-top: 30px;">
                    <!-- Breadcrumb -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Select Participants for Session</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="./admin-dashboard.php">Admin Dashboard</a></li>
                                        <li class="breadcrumb-item active">Select Participants</li>
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
                                    <h2 class="fw-bold text-dark mb-1">Select Participants for "<?php echo htmlspecialchars($sessionDraft['session_name']); ?>"</h2>
                                    <p class="text-muted mb-0">Choose players for this solo game session</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="admin-dashboard.php" class="btn btn-outline-secondary action-btn">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                    <button class="btn btn-outline-primary action-btn" onclick="resetFilters()">
                                        <i class="ti ti-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Session Info -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1">Session Information</h5>
                                            <span class="fw-normal text-muted">Session details and configuration</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-primary"><?php echo ucfirst($sessionDraft['status']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="stats-card p-3 text-center">
                                                <i class="ti ti-calendar text-primary fs-1 mb-2"></i>
                                                <h6 class="mb-1 fw-semibold">Date & Time</h6>
                                                <p class="text-muted mb-0">
                                                    <?php echo date('M j, Y', strtotime($sessionDraft['session_date'])); ?><br>
                                                    <small><?php echo date('g:i A', strtotime($sessionDraft['session_time'])); ?></small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stats-card p-3 text-center">
                                                <i class="ti ti-users text-success fs-1 mb-2"></i>
                                                <h6 class="mb-1 fw-semibold">Max Players</h6>
                                                <p class="text-muted mb-0">
                                                    <span class="fs-4 fw-bold text-success"><?php echo $sessionDraft['max_players']; ?></span><br>
                                                    <small>players</small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stats-card p-3 text-center">
                                                <i class="ti ti-target text-warning fs-1 mb-2"></i>
                                                <h6 class="mb-1 fw-semibold">Game Mode</h6>
                                                <p class="text-muted mb-0">
                                                    <span class="badge bg-warning text-dark"><?php echo $sessionDraft['game_mode']; ?></span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stats-card p-3 text-center">
                                                <i class="ti ti-clock text-info fs-1 mb-2"></i>
                                                <h6 class="mb-1 fw-semibold">Created</h6>
                                                <p class="text-muted mb-0">
                                                    <?php echo date('M j, g:i A', strtotime($sessionDraft['created_at'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selection Counter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card admin-card selection-counter">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1">Participant Selection</h5>
                                            <span class="fw-normal text-muted">
                                                Selected: <span id="selectedCount"><?php echo count($currentParticipants); ?></span>/<span id="maxPlayers"><?php echo $sessionDraft['max_players']; ?></span> players
                                            </span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-danger action-btn me-2" onclick="clearAllSelections()" id="clearBtn" style="display: none;">
                                                <i class="ti ti-x me-1"></i> Clear All
                                            </button>
                                            <button class="btn create-session-btn text-white" onclick="saveParticipants()" id="saveBtn" disabled>
                                                <i class="ti ti-check me-1"></i> Save Participants
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($sessionDraft['game_mode'] === 'Doubles'): ?>
                    <!-- Pairing Mode Selection (Only for Doubles) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5 class="card-title fw-semibold mb-1">Pairing Mode</h5>
                                    <span class="fw-normal text-muted">Choose how players will be paired into duos</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check pairing-mode-option">
                                                <input class="form-check-input" type="radio" name="pairingMode" id="pairingAuto" value="auto" checked>
                                                <label class="form-check-label w-100" for="pairingAuto">
                                                    <div class="p-3 border rounded">
                                                        <h6 class="mb-2"><i class="ti ti-robot text-primary me-2"></i>Auto</h6>
                                                        <small class="text-muted">Players paired automatically by skill level (highest with next highest)</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check pairing-mode-option">
                                                <input class="form-check-input" type="radio" name="pairingMode" id="pairingSemiAuto" value="semi_auto">
                                                <label class="form-check-label w-100" for="pairingSemiAuto">
                                                    <div class="p-3 border rounded">
                                                        <h6 class="mb-2"><i class="ti ti-adjustments text-warning me-2"></i>Semi-Auto</h6>
                                                        <small class="text-muted">Divide into Group A & B, then pair A with B by skill</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check pairing-mode-option">
                                                <input class="form-check-input" type="radio" name="pairingMode" id="pairingManual" value="manual">
                                                <label class="form-check-label w-100" for="pairingManual">
                                                    <div class="p-3 border rounded">
                                                        <h6 class="mb-2"><i class="ti ti-hand-click text-success me-2"></i>Manual</h6>
                                                        <small class="text-muted">Admin manually pairs players into duos</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Semi-Auto Group Assignment (Hidden by default) -->
                    <div class="row mb-4" id="semiAutoSection" style="display: none;">
                        <div class="col-12">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1">Group Assignment</h5>
                                            <span class="fw-normal text-muted">Assign players to Group A or B</span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="autoAssignGroups()">
                                            <i class="ti ti-adjustments me-1"></i> Auto-Assign by Skill
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><span class="badge bg-primary">Group A</span></h6>
                                            <div id="groupA" class="group-container border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                                                <p class="text-muted text-center">Drag players here or click to move</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><span class="badge bg-warning">Group B</span></h6>
                                            <div id="groupB" class="group-container border rounded p-3" style="min-height: 200px; background: #f8f9fa;">
                                                <p class="text-muted text-center">Drag players here or click to move</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Pairing Interface (Hidden by default) -->
                    <div class="row mb-4" id="manualPairingSection" style="display: none;">
                        <div class="col-12">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1">Manual Pairing</h5>
                                            <span class="fw-normal text-muted">Select two players to create a duo</span>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger" onclick="clearAllPairs()">
                                            <i class="ti ti-x me-1"></i> Clear All Pairs
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Available Players</h6>
                                            <div id="availablePlayers" class="available-players-container border rounded p-3" style="min-height: 300px; max-height: 500px; overflow-y: auto;">
                                                <p class="text-muted text-center">Select participants first</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Paired Teams (<span id="pairedCount">0</span>)</h6>
                                            <div id="pairedTeams" class="paired-teams-container border rounded p-3" style="min-height: 300px; max-height: 500px; overflow-y: auto;">
                                                <p class="text-muted text-center">No pairs created yet</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="filter-section">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Search Players</label>
                                        <div class="search-box">
                                            <i class="ti ti-search"></i>
                                            <input type="text" class="form-control" id="searchInput" placeholder="Search players by name..." onkeyup="searchPlayers()">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Filter by Team</label>
                                        <select class="form-select" id="teamFilter" onchange="filterByTeam()">
                                            <option value="VipersVenoms" selected>🏆 VipersVenoms (Primary)</option>
                                            <option value="all">👥 All Teams</option>
                                            <?php foreach ($teams as $team): ?>
                                                <?php if ($team['team_name'] !== 'VipersVenoms'): ?>
                                                    <option value="<?php echo htmlspecialchars($team['team_name']); ?>">
                                                        <?php echo htmlspecialchars($team['team_name']); ?> (<?php echo $team['player_count']; ?>)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">&nbsp;</label>
                                        <button class="btn btn-outline-secondary action-btn w-100" onclick="resetFilters()">
                                            <i class="ti ti-refresh me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Participants -->
                    <div class="row mb-4" id="selectedSection" style="display: <?php echo !empty($currentParticipants) ? 'block' : 'none'; ?>;">
                        <div class="col-12">
                            <div class="selected-participants">
                                <h5 class="mb-3">
                                    <i class="ti ti-check-circle text-success me-2"></i>
                                    Selected Participants (<span id="selectedCountDisplay"><?php echo count($currentParticipants); ?></span>)
                                </h5>
                                <div class="row" id="selectedParticipants">
                                    <?php foreach ($currentParticipants as $participant): ?>
                                        <div class="col-md-4 mb-3" data-user-id="<?php echo $participant['user_id']; ?>">
                                            <div class="card border-success">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo (defined('BASE_PATH') ? BASE_PATH : '') . '/assets/images/profile/user-' . (($participant['user_id'] % 8) + 1) . '.jpg'; ?>" alt="Player" class="player-avatar me-3">
                                                            <div>
                                                                <h6 class="mb-0"><?php echo htmlspecialchars($participant['first_name'] . ' ' . $participant['last_name']); ?></h6>
                                                                <small class="text-muted"><?php echo htmlspecialchars($participant['team_name']); ?></small>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="removeParticipant(<?php echo $participant['user_id']; ?>)">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Players -->
                    <div class="row">
                        <div class="col-12">
                            <!-- VipersVenoms Section -->
                            <div class="card admin-card vipersvenoms-section" id="vipersvenomsSection">
                                <div class="card-header team-section-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1 text-white">
                                                <i class="ti ti-trophy me-2"></i>
                                                VipersVenoms Team (Primary Choice)
                                            </h5>
                                            <span class="fw-normal text-white-50">Recommended players for solo matches</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark" id="vipersvenomsCount">0 players</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="vipersvenomsPlayers">
                                        <!-- VipersVenoms players will be populated here -->
                                    </div>
                                    <div id="noVipersVenomsMessage" style="display: none;" class="text-center py-4">
                                        <i class="ti ti-info-circle text-muted fs-1 mb-3"></i>
                                        <p class="text-muted">No VipersVenoms team members found. Showing all available players below.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Other Teams Section -->
                            <div class="card admin-card other-teams-section" id="otherTeamsSection" style="display: none;">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1 text-white">
                                                <i class="ti ti-users me-2"></i>
                                                Other Teams (Available as Substitutes)
                                            </h5>
                                            <span class="fw-normal text-white-50">Additional players from other teams</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark" id="otherTeamsCount">0 players</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="otherTeamsPlayers">
                                        <!-- Other team players will be populated here -->
                                    </div>
                                </div>
                            </div>

                            <!-- No Results -->
                            <div class="card" id="noResultsSection" style="display: none;">
                                <div class="card-body text-center py-5">
                                    <i class="ti ti-users-group fs-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">No Players Found</h5>
                                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
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

    <script>
        // Global variables
        let selectedParticipants = <?php echo json_encode($currentParticipantIds); ?>;
        let maxPlayers = <?php echo $sessionDraft['max_players']; ?>;
        let allPlayers = <?php echo json_encode($availablePlayers); ?>;
        let currentTeamFilter = 'VipersVenoms';
        let currentSearchTerm = '';

        // Initialize page
        $(document).ready(function() {
            loadPlayers();
            updateUI();
        });

        // Load players based on current filters
        function loadPlayers() {
            const vipersvenomsPlayers = allPlayers.filter(p => {
                const userId = parseInt(p.user_id);
                const isSelected = selectedParticipants.includes(userId);
                return p.is_speedsters == 1 && !isSelected;
            });
            const otherPlayers = allPlayers.filter(p => {
                const userId = parseInt(p.user_id);
                return p.is_speedsters != 1 && !selectedParticipants.includes(userId);
            });

            // Apply search filter
            const filteredVipersVenoms = filterPlayersBySearch(vipersvenomsPlayers);
            const filteredOthers = filterPlayersBySearch(otherPlayers);

            // Populate sections
            populatePlayerSection('vipersvenomsPlayers', filteredVipersVenoms);
            
            // Update player count badges
            document.getElementById('vipersvenomsCount').textContent = filteredVipersVenoms.length + ' players';
            document.getElementById('otherTeamsCount').textContent = filteredOthers.length + ' players';
            
            // Show message if no VipersVenoms found
            if (filteredVipersVenoms.length === 0) {
                $('#noVipersVenomsMessage').show();
            } else {
                $('#noVipersVenomsMessage').hide();
            }
            
            // Always show other teams section if we have other players or if no VipersVenoms
            if (currentTeamFilter === 'all' || currentTeamFilter !== 'VipersVenoms' || filteredVipersVenoms.length === 0) {
                $('#otherTeamsSection').show();
                populatePlayerSection('otherTeamsPlayers', filteredOthers);
            } else {
                $('#otherTeamsSection').hide();
            }

            // Show/hide no results
            if (filteredVipersVenoms.length === 0 && filteredOthers.length === 0) {
                $('#noResultsSection').show();
            } else {
                $('#noResultsSection').hide();
            }
        }

        // Filter players by search term
        function filterPlayersBySearch(players) {
            if (!currentSearchTerm) return players;
            
            return players.filter(player => {
                const fullName = `${player.first_name} ${player.last_name}`.toLowerCase();
                const username = player.username.toLowerCase();
                const searchLower = currentSearchTerm.toLowerCase();
                
                return fullName.includes(searchLower) || username.includes(searchLower);
            });
        }

        // Populate player section
        function populatePlayerSection(containerId, players) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            players.forEach(player => {
                const playerCard = createPlayerCard(player);
                container.appendChild(playerCard);
            });
        }

        // Create player card element
        function createPlayerCard(player) {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4 mb-3';
            
            const isVipersVenoms = player.is_speedsters == 1;
            const skillBadgeClass = getSkillBadgeClass(player.skill_level);
            
            col.innerHTML = `
                <div class="card participant-card ${isVipersVenoms ? 'vipersvenoms' : ''}" onclick="toggleParticipant(${player.user_id})" data-user-id="${player.user_id}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <img src="${(typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/assets/images/profile/user-' + ((player.user_id % 8) + 1)}.jpg" alt="Player" class="player-avatar me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                                <small class="text-muted">${player.team_name || 'No Team'}</small>
                            </div>
                            <span class="badge ${skillBadgeClass} skill-badge">${player.skill_level}</span>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="average-score">${player.average_score}</div>
                                <small class="text-muted">Avg Score</small>
                            </div>
                            <div class="col-4">
                                <div class="text-primary fw-bold">${player.games_played}</div>
                                <small class="text-muted">Games</small>
                            </div>
                            <div class="col-4">
                                <div class="text-success fw-bold">${player.last_played ? new Date(player.last_played).toLocaleDateString() : 'Never'}</div>
                                <small class="text-muted">Last Played</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            return col;
        }

        // Toggle participant selection
        function toggleParticipant(userId) {
            userId = parseInt(userId); // Ensure consistent data type
            const index = selectedParticipants.indexOf(userId);
            
            if (index > -1) {
                // Remove from selection
                selectedParticipants.splice(index, 1);
                removeParticipantFromUI(userId);
            } else {
                // Add to selection
                if (selectedParticipants.length >= maxPlayers) {
                    showNotification('Maximum number of participants reached!', 'warning');
                    return;
                }
                
                selectedParticipants.push(userId);
                addParticipantToUI(userId);
            }
            
            updateUI();
            loadPlayers(); // Refresh to hide/show selected players
        }

        // Remove participant
        function removeParticipant(userId) {
            userId = parseInt(userId); // Ensure consistent data type
            
            const index = selectedParticipants.indexOf(userId);
            if (index > -1) {
                selectedParticipants.splice(index, 1);
                removeParticipantFromUI(userId);
                updateUI();
                loadPlayers(); // This should make the player reappear in available list
            }
        }

        // Add participant to selected UI
        function addParticipantToUI(userId) {
            const player = allPlayers.find(p => p.user_id == userId);
            if (!player) return;

            const selectedContainer = document.getElementById('selectedParticipants');
            
            const col = document.createElement('div');
            col.className = 'col-md-4 mb-3';
            col.setAttribute('data-user-id', userId);
            
            col.innerHTML = `
                <div class="card border-success">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="${(typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/assets/images/profile/user-' + ((player.user_id % 8) + 1)}.jpg" alt="Player" class="player-avatar me-3">
                                <div>
                                    <h6 class="mb-0">${player.first_name} ${player.last_name}</h6>
                                    <small class="text-muted">${player.team_name || 'No Team'}</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeParticipant(${userId})">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            selectedContainer.appendChild(col);
        }

        // Remove participant from selected UI
        function removeParticipantFromUI(userId) {
            const element = document.querySelector(`#selectedParticipants [data-user-id="${userId}"]`);
            if (element) {
                element.remove();
            }
        }

        // Update UI elements
        function updateUI() {
            const selectedCount = selectedParticipants.length;
            
            document.getElementById('selectedCount').textContent = selectedCount;
            document.getElementById('selectedCountDisplay').textContent = selectedCount;
            
            // Show/hide sections
            if (selectedCount > 0) {
                document.getElementById('selectedSection').style.display = 'block';
                document.getElementById('clearBtn').style.display = 'inline-block';
            } else {
                document.getElementById('selectedSection').style.display = 'none';
                document.getElementById('clearBtn').style.display = 'none';
            }
            
            // Enable/disable save button
            document.getElementById('saveBtn').disabled = selectedCount === 0;
            
            // Update pairing UI if in Doubles mode
            <?php if ($sessionDraft['game_mode'] === 'Doubles'): ?>
            if (typeof updatePairingUI === 'function') {
                updatePairingUI();
            }
            <?php endif; ?>
        }

        // Search players
        function searchPlayers() {
            currentSearchTerm = document.getElementById('searchInput').value;
            loadPlayers();
        }

        // Filter by team
        function filterByTeam() {
            currentTeamFilter = document.getElementById('teamFilter').value;
            loadPlayers();
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('teamFilter').value = 'VipersVenoms';
            currentSearchTerm = '';
            currentTeamFilter = 'VipersVenoms';
            loadPlayers();
        }

        // Clear all selections
        function clearAllSelections() {
            
            selectedParticipants = [];
            document.getElementById('selectedParticipants').innerHTML = '';
            
            
            updateUI();
            
            // Reload ALL players from server since we cleared selections
            reloadAllPlayers();
            
            showNotification('All participants cleared successfully!', 'success');
        }

        // Reload all players from server
        function reloadAllPlayers() {
            
            // Since we now load ALL players initially, just call loadPlayers()
            loadPlayers();
        }

        // Save participants
        function saveParticipants() {
            
            if (selectedParticipants.length === 0) {
                showNotification('Please select at least one participant', 'warning');
                return;
            }

            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="ti ti-loader"></i> Saving...';


            // Prepare data based on pairing mode
            const postData = {
                session_id: <?php echo $sessionId; ?>,
                participant_ids: JSON.stringify(selectedParticipants)
            };
            
            <?php if ($sessionDraft['game_mode'] === 'Doubles'): ?>
            postData.pairing_mode = pairingMode;
            
            if (pairingMode === 'semi_auto') {
                postData.group_assignments = JSON.stringify(groupAssignments);
            } else if (pairingMode === 'manual') {
                postData.manual_pairs = JSON.stringify(manualPairs);
            }
            <?php endif; ?>

            $.ajax({
                url: 'ajax/save-participants.php',
                method: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification('Participants saved successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = 'admin-dashboard.php?success=participants_saved';
                        }, 1500);
                    } else {
                        showNotification(response.message || 'Failed to save participants', 'error');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="ti ti-check me-1"></i> Save Participants';
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('An error occurred while saving: ' + error, 'error');
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ti ti-check me-1"></i> Save Participants';
                }
            });
        }

        // Get skill badge class
        function getSkillBadgeClass(skillLevel) {
            const classes = {
                'Pro': 'bg-danger',
                'Elite': 'bg-warning',
                'Advanced': 'bg-info',
                'Intermediate': 'bg-success',
                'Beginner': 'bg-secondary'
            };
            return classes[skillLevel] || 'bg-secondary';
        }

        // Pairing mode state
        let pairingMode = 'auto';
        let groupAssignments = {}; // {userId: 'A' or 'B'}
        let manualPairs = []; // [{player1_id, player2_id, duo_name}]

        // Initialize pairing mode handlers (only for Doubles)
        <?php if ($sessionDraft['game_mode'] === 'Doubles'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle pairing mode change
            document.querySelectorAll('input[name="pairingMode"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    pairingMode = this.value;
                    updatePairingUI();
                });
            });
            
            // Initialize UI
            updatePairingUI();
        });

        function updatePairingUI() {
            const semiAutoSection = document.getElementById('semiAutoSection');
            const manualSection = document.getElementById('manualPairingSection');
            
            if (pairingMode === 'semi_auto') {
                semiAutoSection.style.display = 'block';
                manualSection.style.display = 'none';
                updateGroupAssignmentUI();
            } else if (pairingMode === 'manual') {
                semiAutoSection.style.display = 'none';
                manualSection.style.display = 'block';
                updateManualPairingUI();
            } else {
                semiAutoSection.style.display = 'none';
                manualSection.style.display = 'none';
            }
        }

        function updateGroupAssignmentUI() {
            const groupA = document.getElementById('groupA');
            const groupB = document.getElementById('groupB');
            
            // Clear groups but keep placeholder if empty
            const groupAItems = groupA.querySelectorAll('.player-card');
            const groupBItems = groupB.querySelectorAll('.player-card');
            
            // Remove existing player cards
            groupAItems.forEach(item => item.remove());
            groupBItems.forEach(item => item.remove());
            
            // Show placeholder if group is empty
            if (groupA.querySelectorAll('.player-card').length === 0) {
                const placeholderA = document.createElement('p');
                placeholderA.className = 'text-muted text-center mb-0';
                placeholderA.textContent = 'No players assigned. Click "Auto-Assign" or drag players here.';
                placeholderA.id = 'groupAPlaceholder';
                groupA.appendChild(placeholderA);
            }
            
            if (groupB.querySelectorAll('.player-card').length === 0) {
                const placeholderB = document.createElement('p');
                placeholderB.className = 'text-muted text-center mb-0';
                placeholderB.textContent = 'No players assigned. Click "Auto-Assign" or drag players here.';
                placeholderB.id = 'groupBPlaceholder';
                groupB.appendChild(placeholderB);
            }
            
            // Add players to their assigned groups
            selectedParticipants.forEach(userId => {
                const player = allPlayers.find(p => p.user_id == userId);
                if (!player) return;
                
                const group = groupAssignments[userId] || null;
                
                if (group === 'A') {
                    const placeholder = groupA.querySelector('#groupAPlaceholder');
                    if (placeholder) placeholder.remove();
                    const card = createGroupPlayerCard(player, 'A');
                    groupA.appendChild(card);
                } else if (group === 'B') {
                    const placeholder = groupB.querySelector('#groupBPlaceholder');
                    if (placeholder) placeholder.remove();
                    const card = createGroupPlayerCard(player, 'B');
                    groupB.appendChild(card);
                }
            });
            
            // Make groups droppable
            setupDragAndDrop();
        }

        function createGroupPlayerCard(player, currentGroup) {
            const div = document.createElement('div');
            div.className = 'player-card mb-2 p-2 border rounded bg-white';
            div.setAttribute('data-user-id', player.user_id);
            div.setAttribute('draggable', 'true');
            div.style.cursor = 'move';
            
            div.innerHTML = `
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="${(typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/assets/images/profile/user-' + ((player.user_id % 8) + 1)}.jpg" 
                             alt="Player" class="rounded-circle me-2" width="32" height="32">
                        <div>
                            <small class="fw-semibold">${player.first_name} ${player.last_name}</small><br>
                            <small class="text-muted">Avg: ${player.average_score || 0}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleGroup(${player.user_id}); event.stopPropagation();">
                        ${currentGroup === 'A' ? '→ B' : '→ A'}
                    </button>
                </div>
            `;
            
            // Drag events
            div.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', player.user_id.toString());
                e.dataTransfer.effectAllowed = 'move';
                this.style.opacity = '0.5';
            });
            
            div.addEventListener('dragend', function(e) {
                this.style.opacity = '1';
            });
            
            // Click to toggle (but not on button)
            div.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                    toggleGroup(player.user_id);
                }
            });
            
            return div;
        }

        function setupDragAndDrop() {
            const groupA = document.getElementById('groupA');
            const groupB = document.getElementById('groupB');
            
            // Setup drop zones (will only add listeners once due to check)
            setupDropZone(groupA, 'A');
            setupDropZone(groupB, 'B');
        }

        function setupDropZone(element, groupLetter) {
            // Remove existing listeners by checking if already set up
            if (element.dataset.dropzoneSetup === 'true') {
                return;
            }
            element.dataset.dropzoneSetup = 'true';
            
            element.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('drag-over');
            });
            
            element.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drag-over');
            });
            
            element.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drag-over');
                
                const userId = parseInt(e.dataTransfer.getData('text/plain'));
                if (userId && selectedParticipants.includes(userId)) {
                    groupAssignments[userId] = groupLetter;
                    updateGroupAssignmentUI();
                    showNotification(`Player moved to Group ${groupLetter}`, 'success');
                }
            });
        }

        function toggleGroup(userId) {
            userId = parseInt(userId);
            const currentGroup = groupAssignments[userId];
            if (currentGroup === 'A') {
                groupAssignments[userId] = 'B';
            } else if (currentGroup === 'B') {
                groupAssignments[userId] = 'A';
            } else {
                // Assign to group with fewer players
                const groupACount = Object.values(groupAssignments).filter(g => g === 'A').length;
                const groupBCount = Object.values(groupAssignments).filter(g => g === 'B').length;
                groupAssignments[userId] = groupACount <= groupBCount ? 'A' : 'B';
            }
            updateGroupAssignmentUI();
        }

        function autoAssignGroups() {
            // Get selected players with their averages
            const playersWithAvg = selectedParticipants.map(userId => {
                const player = allPlayers.find(p => p.user_id == userId);
                return {
                    user_id: userId,
                    average_score: parseFloat(player.average_score || 0)
                };
            }).sort((a, b) => b.average_score - a.average_score);
            
            // Alternate assignment: 1st→A, 2nd→B, 3rd→A, 4th→B, etc.
            groupAssignments = {};
            playersWithAvg.forEach((player, index) => {
                groupAssignments[player.user_id] = (index % 2 === 0) ? 'A' : 'B';
            });
            
            updateGroupAssignmentUI();
            showNotification('Players auto-assigned to groups by skill level', 'success');
        }

        function updateManualPairingUI() {
            const availableContainer = document.getElementById('availablePlayers');
            const pairedContainer = document.getElementById('pairedTeams');
            
            // Get unpaired players
            const pairedPlayerIds = new Set();
            manualPairs.forEach(pair => {
                pairedPlayerIds.add(pair.player1_id);
                pairedPlayerIds.add(pair.player2_id);
            });
            
            const unpairedPlayers = selectedParticipants.filter(userId => !pairedPlayerIds.has(userId));
            
            // Show available players
            if (unpairedPlayers.length === 0) {
                availableContainer.innerHTML = '<p class="text-muted text-center">All players have been paired</p>';
            } else {
                availableContainer.innerHTML = unpairedPlayers.map(userId => {
                    const player = allPlayers.find(p => p.user_id == userId);
                    if (!player) return '';
                    return `
                        <div class="player-select-card mb-2 p-2 border rounded bg-white" data-user-id="${userId}" onclick="selectPlayerForPairing(${userId})">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="player_${userId}" onchange="togglePlayerSelection(${userId})">
                                <label class="form-check-label w-100" for="player_${userId}">
                                    <div class="d-flex align-items-center">
                                        <img src="${(typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/assets/images/profile/user-' + ((player.user_id % 8) + 1)}.jpg" 
                                             alt="Player" class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <small class="fw-semibold">${player.first_name} ${player.last_name}</small><br>
                                            <small class="text-muted">Avg: ${player.average_score || 0}</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            // Show paired teams
            if (manualPairs.length === 0) {
                pairedContainer.innerHTML = '<p class="text-muted text-center">No pairs created yet</p>';
            } else {
                pairedContainer.innerHTML = manualPairs.map((pair, index) => {
                    const p1 = allPlayers.find(p => p.user_id == pair.player1_id);
                    const p2 = allPlayers.find(p => p.user_id == pair.player2_id);
                    return `
                        <div class="paired-team-card mb-2 p-2 border rounded bg-light">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <small class="fw-semibold">${pair.duo_name}</small>
                                    <div class="d-flex align-items-center mt-1">
                                        <img src="${(typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/assets/images/profile/user-' + ((p1.user_id % 8) + 1)}.jpg" 
                                             class="rounded-circle me-1" width="24" height="24">
                                        <small>${p1.first_name} ${p1.last_name}</small>
                                        <span class="mx-1">+</span>
                                        <img src="${(typeof BASE_PATH !== 'undefined' ? BASE_PATH : '') + '/assets/images/profile/user-' + ((p2.user_id % 8) + 1)}.jpg" 
                                             class="rounded-circle me-1" width="24" height="24">
                                        <small>${p2.first_name} ${p2.last_name}</small>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="removePair(${index})">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
            }
            
            document.getElementById('pairedCount').textContent = manualPairs.length;
        }

        let selectedPlayersForPairing = [];
        
        function togglePlayerSelection(userId) {
            const index = selectedPlayersForPairing.indexOf(userId);
            if (index > -1) {
                selectedPlayersForPairing.splice(index, 1);
            } else {
                if (selectedPlayersForPairing.length >= 2) {
                    showNotification('You can only select 2 players at a time', 'warning');
                    document.getElementById(`player_${userId}`).checked = false;
                    return;
                }
                selectedPlayersForPairing.push(userId);
            }
            
            if (selectedPlayersForPairing.length === 2) {
                createManualPair();
            }
        }

        function createManualPair() {
            if (selectedPlayersForPairing.length !== 2) {
                showNotification('Please select exactly 2 players', 'warning');
                return;
            }
            
            const p1 = allPlayers.find(p => p.user_id == selectedPlayersForPairing[0]);
            const p2 = allPlayers.find(p => p.user_id == selectedPlayersForPairing[1]);
            
            const duoName = `Duo ${manualPairs.length + 1}`;
            
            manualPairs.push({
                player1_id: selectedPlayersForPairing[0],
                player2_id: selectedPlayersForPairing[1],
                duo_name: duoName
            });
            
            selectedPlayersForPairing = [];
            updateManualPairingUI();
            showNotification(`Pair created: ${p1.first_name} + ${p2.first_name}`, 'success');
        }

        function removePair(index) {
            manualPairs.splice(index, 1);
            updateManualPairingUI();
        }

        function clearAllPairs() {
            if (confirm('Are you sure you want to clear all pairs?')) {
                manualPairs = [];
                selectedPlayersForPairing = [];
                updateManualPairingUI();
            }
        }
        <?php endif; ?>

        // Show notification
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
