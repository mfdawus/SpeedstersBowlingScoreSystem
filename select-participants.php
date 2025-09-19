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
                $teamName = $_POST['team_name'] ?? 'Speedsters';
                $excludeIds = json_decode($_POST['exclude_ids'] ?? '[]', true);
                
                $players = getPlayersByTeamWithAverages($teamName === 'all' ? null : $teamName, $excludeIds);
                echo json_encode(['success' => true, 'players' => $players]);
                exit();
                
            case 'search_players':
                $searchTerm = $_POST['search_term'] ?? '';
                $teamName = $_POST['team_name'] ?? 'Speedsters';
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
    <title>Select Participants - Speedsters Bowling</title>
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
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
        
        .participant-card.speedsters {
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
        
        .speedsters-section {
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
                                            <option value="Speedsters" selected>🏆 Speedsters (Primary)</option>
                                            <option value="all">👥 All Teams</option>
                                            <?php foreach ($teams as $team): ?>
                                                <?php if ($team['team_name'] !== 'Speedsters'): ?>
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
                                                            <img src="assets/images/profile/user-<?php echo (($participant['user_id'] % 8) + 1); ?>.jpg" alt="Player" class="player-avatar me-3">
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
                            <!-- Speedsters Section -->
                            <div class="card admin-card speedsters-section" id="speedstersSection">
                                <div class="card-header team-section-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title fw-semibold mb-1 text-white">
                                                <i class="ti ti-trophy me-2"></i>
                                                Speedsters Team (Primary Choice)
                                            </h5>
                                            <span class="fw-normal text-white-50">Recommended players for solo matches</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-dark" id="speedstersCount">0 players</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="speedstersPlayers">
                                        <!-- Speedsters players will be populated here -->
                                    </div>
                                    <div id="noSpeedstersMessage" style="display: none;" class="text-center py-4">
                                        <i class="ti ti-info-circle text-muted fs-1 mb-3"></i>
                                        <p class="text-muted">No Speedsters team members found. Showing all available players below.</p>
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
        let currentTeamFilter = 'Speedsters';
        let currentSearchTerm = '';

        // Initialize page
        $(document).ready(function() {
            loadPlayers();
            updateUI();
        });

        // Load players based on current filters
        function loadPlayers() {
            const speedstersPlayers = allPlayers.filter(p => {
                const userId = parseInt(p.user_id);
                const isSelected = selectedParticipants.includes(userId);
                return p.is_speedsters == 1 && !isSelected;
            });
            const otherPlayers = allPlayers.filter(p => {
                const userId = parseInt(p.user_id);
                return p.is_speedsters != 1 && !selectedParticipants.includes(userId);
            });

            // Apply search filter
            const filteredSpeedsters = filterPlayersBySearch(speedstersPlayers);
            const filteredOthers = filterPlayersBySearch(otherPlayers);

            // Populate sections
            populatePlayerSection('speedstersPlayers', filteredSpeedsters);
            
            // Update player count badges
            document.getElementById('speedstersCount').textContent = filteredSpeedsters.length + ' players';
            document.getElementById('otherTeamsCount').textContent = filteredOthers.length + ' players';
            
            // Show message if no Speedsters found
            if (filteredSpeedsters.length === 0) {
                $('#noSpeedstersMessage').show();
            } else {
                $('#noSpeedstersMessage').hide();
            }
            
            // Always show other teams section if we have other players or if no Speedsters
            if (currentTeamFilter === 'all' || currentTeamFilter !== 'Speedsters' || filteredSpeedsters.length === 0) {
                $('#otherTeamsSection').show();
                populatePlayerSection('otherTeamsPlayers', filteredOthers);
            } else {
                $('#otherTeamsSection').hide();
            }

            // Show/hide no results
            if (filteredSpeedsters.length === 0 && filteredOthers.length === 0) {
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
            
            const isSpeedsters = player.is_speedsters == 1;
            const skillBadgeClass = getSkillBadgeClass(player.skill_level);
            
            col.innerHTML = `
                <div class="card participant-card ${isSpeedsters ? 'speedsters' : ''}" onclick="toggleParticipant(${player.user_id})" data-user-id="${player.user_id}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <img src="assets/images/profile/user-${((player.user_id % 8) + 1)}.jpg" alt="Player" class="player-avatar me-3">
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
                                <img src="assets/images/profile/user-${((player.user_id % 8) + 1)}.jpg" alt="Player" class="player-avatar me-3">
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
            document.getElementById('teamFilter').value = 'Speedsters';
            currentSearchTerm = '';
            currentTeamFilter = 'Speedsters';
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


            $.ajax({
                url: 'ajax/save-participants.php',
                method: 'POST',
                data: {
                    session_id: <?php echo $sessionId; ?>,
                    participant_ids: JSON.stringify(selectedParticipants)
                },
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
