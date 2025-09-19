<?php
// App Top Strip component for SPEEDSTERS Bowling System
// Dynamic session countdown and status display
require_once __DIR__ . '/session-management.php';

// Get session information
$activeSession = getActiveSession();
$upcomingSession = null;

if (!$activeSession) {
    // Get next scheduled session
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT session_id, session_name, session_date, session_time, participant_count, max_players
            FROM (
                SELECT 
                    gs.session_id, gs.session_name, gs.session_date, gs.session_time, gs.max_players,
                    COALESCE(sp_count.participant_count, 0) as participant_count
                FROM game_sessions gs
                LEFT JOIN (
                    SELECT session_id, COUNT(*) as participant_count 
                    FROM session_participants 
                    GROUP BY session_id
                ) sp_count ON gs.session_id = sp_count.session_id
                WHERE gs.status = 'Scheduled' AND CONCAT(gs.session_date, ' ', gs.session_time) > NOW()
                ORDER BY gs.session_date ASC, gs.session_time ASC
                LIMIT 1
            ) AS next_session
        ");
        $stmt->execute();
        $upcomingSession = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $upcomingSession = null;
    }
}

// Always show topstrip, determine content
$showTopstrip = true;

// Check if user is admin for action buttons
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin';
?>

<?php if ($showTopstrip): ?>
<div class="app-topstrip bg-gradient-primary px-2 py-2 w-100 d-flex align-items-center justify-content-between flex-nowrap" id="sessionTopstrip">
  
  <?php if ($activeSession): ?>
    <!-- Active Session Display -->
  <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <i class="ti ti-play-circle text-success fs-5"></i>
    <div>
        <h6 class="mb-0 fw-bold text-white small"><?php echo htmlspecialchars($activeSession['session_name']); ?></h6>
        <small class="text-white-50 d-block" style="font-size: 11px;">Session In Progress</small>
      </div>
    </div>

    <!-- Session Duration -->
    <div class="d-flex align-items-center gap-2 mx-2">
      <div class="text-center">
        <div class="bg-white bg-opacity-25 rounded px-2">
          <span class="text-white fw-bold small" id="sessionDuration">00:00:00</span>
        </div>
        <small class="text-white-50" style="font-size: 9px;">Playing Time</small>
      </div>
    </div>

    <!-- Active Session Actions (Admin Only) -->
    <?php if ($isAdmin): ?>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <a href="admin-score-monitoring-solo.php" class="btn btn-success btn-sm d-flex align-items-center gap-1" style="font-size: 11px; padding: 4px 8px;">
        <i class="ti ti-edit fs-6"></i>
        Enter Scores
      </a>
    </div>
    <?php endif; ?>

  <?php elseif ($upcomingSession): ?>
    <!-- Upcoming Session Countdown -->
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <i class="ti ti-clock text-warning fs-5"></i>
      <div>
        <h6 class="mb-0 fw-bold text-white small"><?php echo htmlspecialchars($upcomingSession['session_name']); ?></h6>
        <small class="text-white-50 d-block" style="font-size: 11px;">Starting Soon</small>
    </div>
  </div>

    <!-- Countdown Timer -->
  <div class="d-flex gap-1 mx-2 flex-shrink-0">
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
          <span class="text-white fw-bold small" id="days">00</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Days</small>
    </div>
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
          <span class="text-white fw-bold small" id="hours">00</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Hrs</small>
    </div>
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
          <span class="text-white fw-bold small" id="minutes">00</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Min</small>
    </div>
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
          <span class="text-white fw-bold small" id="seconds">00</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Sec</small>
    </div>
  </div>

    <!-- Session Info & Actions -->
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <small class="text-white-50 me-2" style="font-size: 10px;">
        <?php echo $upcomingSession['participant_count']; ?>/<?php echo $upcomingSession['max_players']; ?> registered
      </small>
      <?php if ($isAdmin): ?>
      <a href="select-participants.php?session_id=<?php echo $upcomingSession['session_id']; ?>" class="btn btn-warning btn-sm d-flex align-items-center gap-1" style="font-size: 11px; padding: 4px 8px;">
        <i class="ti ti-users fs-6"></i>
        Manage
      </a>
      <?php endif; ?>
    </div>
  
  <?php else: ?>
    <!-- No Sessions - Show "No upcoming events" message -->
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <i class="ti ti-calendar-off text-muted fs-5"></i>
      <div>
        <h6 class="mb-0 fw-bold text-white small">No Upcoming Events</h6>
        <small class="text-white-50 d-block" style="font-size: 11px;">Check back later for new sessions</small>
      </div>
    </div>

    <!-- Center: Motivational message -->
    <div class="d-flex align-items-center justify-content-center mx-2">
      <small class="text-white-50 text-center" style="font-size: 11px;">
        <i class="ti ti-bowling me-1"></i>
        Stay ready for the next bowling session!
      </small>
    </div>

    <!-- Right: Admin action (if admin) -->
    <?php if ($isAdmin): ?>
  <div class="d-flex align-items-center gap-2 flex-shrink-0">
      <a href="admin-session-management.php" class="btn btn-light btn-sm d-flex align-items-center gap-1" style="font-size: 11px; padding: 4px 8px;">
        <i class="ti ti-plus fs-6"></i>
        Create Session
    </a>
  </div>
    <?php else: ?>
    <div class="flex-shrink-0" style="width: 80px;"></div>
    <?php endif; ?>
  
  <?php endif; ?>

</div>
<?php endif; ?>

<?php if ($showTopstrip): ?>
<!-- Dynamic Session Timer Script -->
<script>
  <?php if ($activeSession): ?>
    // Active session - show playing time
    const sessionStartTime = new Date('<?php echo $activeSession['started_at']; ?>').getTime();
    
    function updateSessionDuration() {
      const now = new Date().getTime();
      const elapsed = now - sessionStartTime;
      
      const hours = Math.floor(elapsed / (1000 * 60 * 60));
      const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);
      
      const durationElement = document.getElementById('sessionDuration');
      if (durationElement) {
        durationElement.innerHTML = 
          hours.toString().padStart(2, '0') + ':' +
          minutes.toString().padStart(2, '0') + ':' +
          seconds.toString().padStart(2, '0');
      }
    }
    
    // Update every second
    setInterval(updateSessionDuration, 1000);
    updateSessionDuration();
    
  <?php elseif ($upcomingSession): ?>
    // Upcoming session - show countdown
    const targetDate = new Date('<?php echo $upcomingSession['session_date'] . ' ' . $upcomingSession['session_time']; ?>').getTime();
  
  function updateCountdown() {
    const now = new Date().getTime();
    const distance = targetDate - now;
    
    if (distance < 0) {
        // Session should have started - hide topstrip or refresh page
        const topstrip = document.getElementById('sessionTopstrip');
        if (topstrip) {
          topstrip.style.display = 'none';
        }
      return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
      const daysEl = document.getElementById('days');
      const hoursEl = document.getElementById('hours');
      const minutesEl = document.getElementById('minutes');
      const secondsEl = document.getElementById('seconds');
      
      if (daysEl) daysEl.innerHTML = days.toString().padStart(2, '0');
      if (hoursEl) hoursEl.innerHTML = hours.toString().padStart(2, '0');
      if (minutesEl) minutesEl.innerHTML = minutes.toString().padStart(2, '0');
      if (secondsEl) secondsEl.innerHTML = seconds.toString().padStart(2, '0');
  }
  
  // Update countdown every second
  setInterval(updateCountdown, 1000);
  updateCountdown();
  <?php endif; ?>
</script>
<?php endif; ?>
