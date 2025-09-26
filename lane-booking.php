<?php
// Get current user and their active session
require_once 'includes/auth.php';
require_once 'includes/session-management.php';

$currentUser = getCurrentUser();
$userActiveSession = null;
$userLaneAssignment = null;

if ($currentUser) {
    // Get active session
    $activeSession = getActiveSession();
    
    if ($activeSession) {
        // Check if user is participating in this session
        if (canUserParticipateInSession($currentUser['user_id'], $activeSession['session_id'])) {
            $userActiveSession = $activeSession;
            
            // Get user's current lane assignment/preference
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT lane_number FROM session_participants WHERE session_id = ? AND user_id = ?");
                $stmt->execute([$activeSession['session_id'], $currentUser['user_id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $userLaneAssignment = $result['lane_number'];
                }
            } catch (Exception $e) {
                // Handle error silently
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lane Booking - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
     <style>
     .bg-gradient-primary {
       background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
     }
     
     /* Fixed positioning and layout */
     .page-wrapper {
       position: relative;
       min-height: 100vh;
     }
     
     .body-wrapper {
       position: relative;
       min-height: calc(100vh - 70px);
     }
     
     .body-wrapper-inner {
       padding: 20px;
       position: relative;
     }
     
     /* Card positioning */
     .card {
       position: relative;
       margin-bottom: 20px;
       box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
     }
     
     /* Lane card styles */
     .lane-card {
       transition: all 0.3s ease;
       border: 2px solid transparent;
       position: relative;
       height: 120px;
       display: flex;
       flex-direction: column;
       justify-content: center;
       align-items: center;
     }
     .lane-card:hover {
       transform: translateY(-2px);
       box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
     }
     .lane-card.selected {
       border-color: #0d6efd;
       background-color: #f8f9ff;
     }
     
     /* Time slot styles */
     .time-slot {
       transition: all 0.2s ease;
       cursor: pointer;
       position: relative;
       min-height: 45px;
       display: flex;
       align-items: center;
       justify-content: center;
     }
     .time-slot:hover {
       background-color: #e9ecef;
     }
     .time-slot.selected {
       background-color: #0d6efd;
       color: white;
     }
     .time-slot.booked {
       background-color: #dc3545;
       color: white;
       cursor: not-allowed;
     }
     
     /* Calendar styles */
     .calendar-day {
       transition: all 0.2s ease;
       cursor: pointer;
       position: relative;
       min-height: 50px;
       display: flex;
       align-items: center;
       justify-content: center;
     }
     .calendar-day:hover {
       background-color: #e9ecef;
     }
     .calendar-day.selected {
       background-color: #0d6efd;
       color: white;
     }
     .calendar-day.today {
       border: 2px solid #0d6efd;
     }
     
     /* Grid layouts */
     .lanes-grid {
       display: grid;
       grid-template-columns: repeat(2, 1fr);
       gap: 15px;
       position: relative;
     }
     
     .calendar-grid {
       position: relative;
       border: 1px solid #dee2e6;
       border-radius: 8px;
       overflow: hidden;
     }
     
     .calendar-days {
       display: grid;
       grid-template-columns: repeat(7, 1fr);
       position: relative;
     }
     
     /* Responsive fixes */
     @media (max-width: 768px) {
       .lanes-grid {
         grid-template-columns: 1fr;
       }
       
       .body-wrapper-inner {
         padding: 10px;
       }
       
       .card {
         margin-bottom: 15px;
       }
     }
     
     /* Prevent content shifting */
     .time-slots {
       position: relative;
       min-height: 300px;
     }
     
     .calendar-container {
       position: relative;
     }
     
     /* Mobile responsiveness fixes */
     @media (max-width: 768px) {
       /* Active Session Card Mobile Fixes */
       .card.bg-primary .card-body {
         padding: 1rem;
       }
       
       .card.bg-primary .d-flex.align-items-center.justify-content-between {
         flex-direction: column;
         align-items: flex-start !important;
         gap: 1rem;
       }
       
       .card.bg-primary .d-flex.align-items-center.gap-4 {
         flex-direction: column;
         gap: 0.5rem !important;
         align-items: flex-start !important;
       }
       
       .card.bg-primary .d-flex.align-items-center.gap-4 > div {
         margin-bottom: 0.5rem;
       }
       
       /* Lane Assignment Card Mobile Fixes */
       .card .row {
         margin: 0;
       }
       
       .card .col-md-8,
       .card .col-md-4 {
         padding: 0;
         margin-bottom: 1rem;
       }
       
       .card .d-flex.align-items-center.justify-content-end {
         justify-content: flex-start !important;
       }
       
       /* Lane Assignment Info Mobile Layout */
       .card .d-flex.align-items-center {
         flex-direction: column;
         align-items: flex-start !important;
         text-align: left;
       }
       
       .card .bg-primary.rounded-circle.p-3.me-3 {
         margin-right: 0 !important;
         margin-bottom: 1rem;
         align-self: center;
       }
       
       /* Button Mobile Fixes */
       .btn {
         width: 100%;
         margin-bottom: 0.5rem;
       }
       
       /* Table Mobile Fixes */
       .table-responsive {
         font-size: 0.875rem;
       }
       
       .table th,
       .table td {
         padding: 0.5rem 0.25rem;
       }
       
       /* Breadcrumb Mobile Fixes */
       .page-title-right {
         display: none;
       }
       
       .page-title-box h4 {
         font-size: 1.25rem;
       }
     }
     
     @media (max-width: 576px) {
       /* Extra small screens */
       .card-body {
         padding: 0.75rem;
       }
       
       .card.bg-primary h4 {
         font-size: 1.1rem;
       }
       
       .card h5 {
         font-size: 1rem;
       }
       
       .card h4 {
         font-size: 1.25rem;
       }
       
       .btn {
         font-size: 0.875rem;
         padding: 0.5rem 1rem;
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
        <div class="container-fluid" style="margin-top: 30px;">
          <!-- Page Header -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Lane Booking System</h4>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Lane Booking</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <?php if ($userActiveSession): ?>
          <!-- Active Session Information -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                      <div class="me-3">
                        <i class="ti ti-calendar-event fs-1"></i>
                      </div>
                      <div>
                        <h4 class="mb-1 fw-bold">Active Session: <?php echo htmlspecialchars($userActiveSession['session_name']); ?></h4>
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 gap-md-4">
                          <div class="d-flex align-items-center">
                            <i class="ti ti-calendar me-2"></i>
                            <span><?php echo date('l, M j, Y', strtotime($userActiveSession['session_date'])); ?></span>
                          </div>
                          <div class="d-flex align-items-center">
                            <i class="ti ti-clock me-2"></i>
                            <span><?php echo date('g:i A', strtotime($userActiveSession['session_time'])); ?></span>
                          </div>
                          <div class="d-flex align-items-center">
                            <i class="ti ti-user me-2"></i>
                            <span><?php echo ucfirst($userActiveSession['game_mode']); ?></span>
                          </div>
                          <?php if ($userLaneAssignment): ?>
                          <div class="d-flex align-items-center">
                            <i class="ti ti-target me-2"></i>
                            <span>
                              <?php if ($userActiveSession['status'] === 'Scheduled'): ?>
                                Preferred Lane <?php echo $userLaneAssignment; ?> (will be randomized when session starts)
                              <?php else: ?>
                                Assigned to Lane <?php echo $userLaneAssignment; ?>
                              <?php endif; ?>
                            </span>
                          </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2">
                      <?php if (!$userLaneAssignment): ?>
                      <button class="btn btn-primary" onclick="drawRandomLane()">
                        <i class="ti ti-target me-2"></i>Draw Your Lane
                      </button>
                      <?php else: ?>
                      <span class="badge bg-success fs-6">
                        <i class="ti ti-check me-1"></i>Assigned to Lane <?php echo $userLaneAssignment; ?>
                      </span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php else: ?>
          <!-- No Active Session -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-info">
                <i class="ti ti-info-circle me-2"></i>
                <strong>No Active Session</strong> - You are not currently participating in any active bowling session.
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($userActiveSession): ?>
          <!-- Current Lane Assignment -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-target me-2"></i>
                    Your Lane Assignment
                  </h5>
                  
                  <div class="row">
                    <div class="col-12 col-md-8">
                      <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle p-3 me-3">
                          <i class="ti ti-target text-white fs-3"></i>
                        </div>
                        <div>
                          <h6 class="mb-0">Your Lane Assignment</h6>
                          <h4 class="mb-0 text-primary">
                            <?php if ($userLaneAssignment): ?>
                              Lane <?php echo $userLaneAssignment; ?>
                            <?php else: ?>
                              Not Assigned
                            <?php endif; ?>
                          </h4>
                          <small class="text-muted">
                            <?php if ($userLaneAssignment): ?>
                              You have been assigned to this lane
                            <?php else: ?>
                              Click "Draw Your Lane" to get assigned
                            <?php endif; ?>
                          </small>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-4 mt-3 mt-md-0">
                      <div class="d-flex align-items-center justify-content-md-end">
                        <?php if (!$userLaneAssignment): ?>
                        <button class="btn btn-primary btn-lg" onclick="drawRandomLane()">
                          <i class="ti ti-target me-2"></i>Draw Your Lane
                        </button>
                        <?php else: ?>
                        <span class="badge bg-success fs-5 p-3">
                          <i class="ti ti-check me-2"></i>Assigned
                        </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Lane History -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-history me-2"></i>
                    Your Lane History
                  </h5>
                  
                  <?php
                  // Get user's lane history from sessions
                  $laneHistory = [];
                  if ($currentUser) {
                      try {
                          $pdo = getDBConnection();
                          $stmt = $pdo->prepare("
                              SELECT 
                                  gs.session_id,
                                  gs.session_name,
                                  gs.session_date,
                                  gs.session_time,
                                  gs.game_mode,
                                  gs.status as session_status,
                                  sp.lane_number,
                                  sp.joined_at
                              FROM game_sessions gs
                              JOIN session_participants sp ON gs.session_id = sp.session_id
                              WHERE sp.user_id = ? AND sp.lane_number IS NOT NULL
                              ORDER BY gs.session_date DESC, gs.session_time DESC
                              LIMIT 10
                          ");
                          $stmt->execute([$currentUser['user_id']]);
                          $laneHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      } catch (Exception $e) {
                          // Handle error silently
                      }
                  }
                  ?>
                  
                  <?php if (!empty($laneHistory)): ?>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Session</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Lane</th>
                          <th>Game Mode</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($laneHistory as $history): ?>
                        <tr>
                          <td>
                            <strong><?php echo htmlspecialchars($history['session_name']); ?></strong>
                          </td>
                          <td><?php echo date('M j, Y', strtotime($history['session_date'])); ?></td>
                          <td><?php echo date('g:i A', strtotime($history['session_time'])); ?></td>
                          <td>
                            <span class="badge bg-primary">Lane <?php echo $history['lane_number']; ?></span>
                          </td>
                          <td><?php echo ucfirst($history['game_mode']); ?></td>
                          <td>
                            <?php
                            $statusClass = '';
                            switch ($history['session_status']) {
                                case 'Scheduled':
                                    $statusClass = 'bg-warning';
                                    break;
                                case 'Active':
                                    $statusClass = 'bg-info';
                                    break;
                                case 'Completed':
                                    $statusClass = 'bg-success';
                                    break;
                                case 'Cancelled':
                                    $statusClass = 'bg-danger';
                                    break;
                                default:
                                    $statusClass = 'bg-secondary';
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($history['session_status']); ?></span>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                  <?php else: ?>
                  <div class="text-center py-4">
                    <i class="ti ti-target fs-1 text-muted mb-3"></i>
                    <h6 class="text-muted">No Lane History</h6>
                    <p class="text-muted mb-0">You haven't been assigned to any lanes yet.</p>
                  </div>
                  <?php endif; ?>
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
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

     <style>
     /* Calendar specific styles */
     .calendar-grid {
       border: 1px solid #dee2e6;
       border-radius: 8px;
       overflow: hidden;
       position: relative;
     }
     .calendar-header {
       display: grid;
       grid-template-columns: repeat(7, 1fr);
       background-color: #f8f9fa;
       border-bottom: 1px solid #dee2e6;
       position: relative;
     }
     .calendar-header > div {
       padding: 10px;
       text-align: center;
       font-weight: 600;
       font-size: 0.875rem;
       position: relative;
     }
     .calendar-days {
       display: grid;
       grid-template-columns: repeat(7, 1fr);
       position: relative;
     }
     .calendar-day {
       padding: 15px 10px;
       text-align: center;
       border-right: 1px solid #dee2e6;
       border-bottom: 1px solid #dee2e6;
       cursor: pointer;
       transition: all 0.2s ease;
       position: relative;
       min-height: 50px;
       display: flex;
       align-items: center;
       justify-content: center;
     }
     .calendar-day:hover {
       background-color: #e9ecef;
     }
     .calendar-day.selected {
       background-color: #0d6efd;
       color: white;
     }
     .calendar-day.today {
       border: 2px solid #0d6efd;
       font-weight: bold;
     }
     .calendar-day.other-month {
       color: #6c757d;
       background-color: #f8f9fa;
     }
     
     /* Time slot specific styles */
     .time-slot {
       padding: 12px;
       margin-bottom: 8px;
       border: 1px solid #dee2e6;
       border-radius: 6px;
       text-align: center;
       cursor: pointer;
       transition: all 0.2s ease;
       position: relative;
       min-height: 45px;
       display: flex;
       align-items: center;
       justify-content: center;
     }
     .time-slot:hover {
       background-color: #e9ecef;
     }
     .time-slot.selected {
       background-color: #0d6efd;
       color: white;
       border-color: #0d6efd;
     }
     .time-slot.booked {
       background-color: #dc3545;
       color: white;
       cursor: not-allowed;
     }
     
     /* Lane card specific styles */
     .lanes-grid {
       display: grid;
       grid-template-columns: repeat(2, 1fr);
       gap: 15px;
       position: relative;
     }
     .lane-card {
       padding: 20px;
       border: 2px solid #dee2e6;
       border-radius: 8px;
       text-align: center;
       cursor: pointer;
       transition: all 0.3s ease;
       position: relative;
       height: 120px;
       display: flex;
       flex-direction: column;
       justify-content: center;
       align-items: center;
     }
     .lane-card:hover {
       transform: translateY(-2px);
       box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
     }
     .lane-card.selected {
       border-color: #0d6efd;
       background-color: #f8f9ff;
     }
     .lane-card.available {
       border-color: #28a745;
     }
     .lane-card.booked {
       border-color: #dc3545;
       background-color: #f8f9fa;
       cursor: not-allowed;
     }
     
     /* Additional positioning fixes */
     .container-fluid {
       position: relative;
     }
     
     .row {
       position: relative;
     }
     
     .col-lg-4, .col-lg-6, .col-12 {
       position: relative;
     }
     
     /* Prevent layout shifts */
     .card-body {
       position: relative;
     }
     
     /* Fixed height containers */
     .calendar-container {
       position: relative;
       min-height: 400px;
     }
     
     .time-slots {
       position: relative;
       min-height: 300px;
     }
     
    .lanes-grid {
      position: relative;
      min-height: 280px;
    }
   </style>

  <script>
    // Global variables
    let currentSessionId = <?php echo $userActiveSession ? $userActiveSession['session_id'] : 'null'; ?>;
    let currentUserLane = <?php echo $userLaneAssignment ? $userLaneAssignment : 'null'; ?>;

    // Draw random lane function
    async function drawRandomLane() {
      if (!currentSessionId) {
        alert('No active session found');
        return;
      }

      if (currentUserLane) {
        alert('You already have a lane assigned!');
        return;
      }

      // Show loading state
      const button = event.target;
      const originalText = button.innerHTML;
      button.disabled = true;
      button.innerHTML = '<i class="ti ti-loader me-2"></i>Drawing...';

      try {
        const form = new FormData();
        form.append('action', 'draw_random_lane');
        form.append('session_id', currentSessionId);

        const response = await fetch('ajax/session-management.php', {
          method: 'POST',
          body: form
        });

        const result = await response.json();

        if (result.success) {
          alert('Lane drawn successfully! You have been assigned to Lane ' + result.lane_number);
          location.reload();
        } else {
          alert('Failed to draw lane: ' + (result.message || 'Unknown error'));
          button.disabled = false;
          button.innerHTML = originalText;
        }
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while drawing your lane');
        button.disabled = false;
        button.innerHTML = originalText;
      }
    }
  </script>
  
  <!-- Countdown Timer Script -->
  <script>

    function selectDate(date) {
      selectedDate = date;
      
      // Update calendar display
      document.querySelectorAll('.calendar-day').forEach(day => {
        day.classList.remove('selected');
      });
      
      event.target.classList.add('selected');
      
      // Update time slots availability
      updateTimeSlots();
      updateBookingSummary();
    }

    function previousMonth() {
      currentMonth.setMonth(currentMonth.getMonth() - 1);
      generateCalendar();
    }

    function nextMonth() {
      currentMonth.setMonth(currentMonth.getMonth() + 1);
      generateCalendar();
    }

    // Time slots functions
    function generateTimeSlots() {
      const timeSlots = document.getElementById('timeSlots');
      const times = [
        '9:00 AM', '11:00 AM', '1:00 PM', '3:00 PM', 
        '5:00 PM', '7:00 PM', '9:00 PM'
      ];
      
      timeSlots.innerHTML = '';
      
      times.forEach(time => {
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        slot.textContent = time;
        slot.addEventListener('click', () => selectTime(time));
        timeSlots.appendChild(slot);
      });
    }

    function selectTime(time) {
      selectedTime = time;
      
      document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
      });
      
      event.target.classList.add('selected');
      updateLanes();
      updateBookingSummary();
    }

    function updateTimeSlots() {
      // Simulate availability based on date
      const slots = document.querySelectorAll('.time-slot');
      slots.forEach((slot, index) => {
        slot.classList.remove('booked');
        if (Math.random() < 0.3) { // 30% chance of being booked
          slot.classList.add('booked');
        }
      });
    }

    // Show lane selection modal
    function showLaneSelection() {
      if (!currentSessionId) {
        alert('No active session found');
        return;
      }
      
      loadModalLaneStatus();
      const modal = new bootstrap.Modal(document.getElementById('laneSelectionModal'));
      modal.show();
    }

    // Load lane status for the modal
    async function loadModalLaneStatus() {
      try {
        const form = new FormData();
        form.append('action', 'lane_status');
        form.append('session_id', currentSessionId);
        const res = await fetch('ajax/session-management.php', { method: 'POST', body: form });
        const data = await res.json();
        if (!data.success) { 
          alert('Failed to load lane status');
          return; 
        }
        renderModalLanes(data.status, data.user_lane);
      } catch (e) {
        alert('Error loading lane status');
      }
    }

    function renderStaticLanes(count) {
      const lanesGrid = document.getElementById('lanesGrid');
      lanesGrid.innerHTML = '';
      for (let i = 1; i <= count; i++) {
        const lane = document.createElement('div');
        lane.className = 'lane-card available';
        lane.innerHTML = `
          <i class="ti ti-target fs-1 text-primary mb-2"></i>
          <h5 class="mb-1">Lane ${i}</h5>
          <small class="text-success">Available</small>
        `;
        lane.addEventListener('click', () => selectLane(i));
        lanesGrid.appendChild(lane);
      }
    }

    async function loadLaneStatus() {
      try {
        const form = new FormData();
        form.append('action', 'lane_status');
        form.append('session_id', sessionId);
        const res = await fetch('ajax/session-management.php', { method: 'POST', body: form });
        const data = await res.json();
        if (!data.success) { renderStaticLanes(8); return; }
        renderLanesFromStatus(data.status, data.user_lane);
      } catch (e) {
        renderStaticLanes(8);
      }
    }

    // Render lanes in the modal
    function renderModalLanes(status, userLane) {
      const modalLanesGrid = document.getElementById('modalLanesGrid');
      modalLanesGrid.innerHTML = '';
      selectedLane = null;
      
      if (!status || !status.lanes) {
        modalLanesGrid.innerHTML = '<div class="alert alert-warning">No lanes available</div>';
        return;
      }
      
      (status.lanes || []).forEach(info => {
        const lane = document.createElement('div');
        const isCurrentUserLane = userLane && Number(userLane) === Number(info.lane_number);
        const classes = ['lane-card', 'available'];
        
        if (isCurrentUserLane) {
          classes.push('selected');
          selectedLane = info.lane_number;
        }
        
        lane.className = classes.join(' ');
        lane.innerHTML = `
          <i class="ti ti-target fs-1 text-primary mb-2"></i>
          <h5 class="mb-1">Lane ${info.lane_number}</h5>
          <div class="lane-text">Lane ${info.lane_number}</div>
          <div class="lane-players mt-2">
            <small class="text-muted">Players: ${info.players ? info.players.length : 0}</small>
            ${info.players && info.players.length > 0 ? 
              `<div class="player-list mt-1">
                ${info.players.map(player => `<small class="d-block">• ${player.first_name} ${player.last_name}</small>`).join('')}
              </div>` : 
              '<small class="text-muted d-block">No players assigned</small>'
            }
          </div>
          ${isCurrentUserLane ? '<div class="mt-2"><span class="badge bg-info">Your Current Assignment</span></div>' : ''}
        `;
        
        // Allow selection for preferences (always available before session starts)
        lane.style.cursor = 'pointer';
        lane.addEventListener('click', () => selectLaneInModal(info.lane_number));
        
        modalLanesGrid.appendChild(lane);
      });
      
      // Update confirm button state
      updateConfirmButton();
      
      // Show status message
      const statusMsg = document.createElement('div');
      statusMsg.className = 'mt-3';
      statusMsg.innerHTML = '<div class="alert alert-info"><i class="ti ti-info-circle me-2"></i>Choose your preferred lane. All assignments will be randomized when the session starts.</div>';
      modalLanesGrid.appendChild(statusMsg);
    }
    
    // Select lane in modal
    function selectLaneInModal(laneNumber) {
      // Remove previous selection
      document.querySelectorAll('#modalLanesGrid .lane-card').forEach(card => {
        card.classList.remove('selected');
      });
      
      // Add selection to clicked lane
      event.target.closest('.lane-card').classList.add('selected');
      selectedLane = laneNumber;
      updateConfirmButton();
    }
    
    // Update confirm button state
    function updateConfirmButton() {
      const confirmBtn = document.getElementById('confirmLaneSelection');
      if (selectedLane) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = selectedLane === currentUserLane ? 
          '<i class="ti ti-check me-2"></i>Keep Current Assignment' : 
          '<i class="ti ti-check me-2"></i>Draw This Lane';
      } else {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="ti ti-target me-2"></i>Select a Lane';
      }
    }

    // Submit lane choice
    async function submitLaneChoice(laneNumber) {
      if (!currentSessionId) return;
      try {
        const form = new FormData();
        form.append('action', 'submit_lane_choice');
        form.append('session_id', currentSessionId);
        form.append('lane_number', laneNumber);
        const res = await fetch('ajax/session-management.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) {
          return true;
        } else {
          alert(data.message || 'Failed to select lane');
          return false;
        }
      } catch (e) {
        alert('Failed to select lane');
        return false;
      }
    }
    
    // Confirm lane selection from modal
    document.getElementById('confirmLaneSelection').addEventListener('click', async function() {
      if (!selectedLane) return;
      
      const originalText = this.innerHTML;
      this.disabled = true;
      this.innerHTML = '<i class="ti ti-loader me-2"></i>Confirming...';
      
      const success = await submitLaneChoice(selectedLane);
      if (success) {
        // Update current user lane
        currentUserLane = selectedLane;
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('laneSelectionModal'));
        modal.hide();
        
        // Show success message
        alert('Lane drawn successfully!');
        
        // Refresh the page to show updated assignment
        location.reload();
      } else {
        this.disabled = false;
        this.innerHTML = originalText;
      }
    });

    function selectLane(laneNumber) {
      selectedLane = laneNumber;
      
      document.querySelectorAll('.lane-card').forEach(lane => {
        lane.classList.remove('selected');
      });
      
      event.target.closest('.lane-card').classList.add('selected');
      updateBookingSummary();
    }

    function updateLanes() {
      // If session-based, reload status; otherwise no-op
      if (sessionId) { loadLaneStatus(); }
    }

    // Booking summary functions
    function updateBookingSummary() {
      const dateElement = document.getElementById('selectedDate');
      const timeElement = document.getElementById('selectedTime');
      const laneElement = document.getElementById('selectedLane');
      const confirmButton = document.getElementById('confirmBooking');
      
      if (selectedDate) {
        dateElement.textContent = selectedDate.toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      } else {
        dateElement.textContent = 'Not selected';
      }
      
      timeElement.textContent = selectedTime || 'Not selected';
      laneElement.textContent = selectedLane ? `Lane ${selectedLane}` : 'Not selected';
      
      // Enable/disable confirm button
      if (selectedDate && selectedTime && selectedLane) {
        confirmButton.disabled = false;
      } else {
        confirmButton.disabled = true;
      }
    }

    function resetBooking() {
      selectedDate = null;
      selectedTime = null;
      selectedLane = null;
      
      document.querySelectorAll('.calendar-day').forEach(day => {
        day.classList.remove('selected');
      });
      
      document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
      });
      
      document.querySelectorAll('.lane-card').forEach(lane => {
        lane.classList.remove('selected');
      });
      
      updateBookingSummary();
    }

    // Confirm booking
    document.getElementById('confirmBooking').addEventListener('click', function() {
      if (selectedDate && selectedTime && selectedLane) {
        // Show success message
        alert(`Booking confirmed!\n\nDate: ${selectedDate.toLocaleDateString()}\nTime: ${selectedTime}\nLane: ${selectedLane}\n\nYou will receive a confirmation email shortly.`);
        
        // Reset the form
        resetBooking();
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
  
  <?php include 'includes/admin-popup.php'; ?>
</body>

</html>
