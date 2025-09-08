<?php
// App Top Strip component for SPEEDSTERS Bowling System
// This ensures consistent tournament banner and countdown across all pages
?>

<div class="app-topstrip bg-gradient-primary px-2 py-2 w-100 d-flex align-items-center justify-content-between flex-nowrap">
  <!-- Left side: Tournament Info -->
  <div class="d-flex align-items-center gap-2 flex-shrink-0">
    <i class="ti ti-trophy text-warning fs-6"></i>
    <div>
      <h6 class="mb-0 fw-bold text-white small">SPEEDSTERS 2025</h6>
      <small class="text-white-50 d-block" style="font-size: 11px;">Next Bowling Tournament</small>
    </div>
  </div>

  <!-- Middle: Countdown -->
  <div class="d-flex gap-1 mx-2 flex-shrink-0">
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
        <span class="text-white fw-bold small" id="days">99</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Days</small>
    </div>
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
        <span class="text-white fw-bold small" id="hours">04</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Hrs</small>
    </div>
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
        <span class="text-white fw-bold small" id="minutes">41</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Min</small>
    </div>
    <div class="text-center">
      <div class="bg-white bg-opacity-25 rounded px-1">
        <span class="text-white fw-bold small" id="seconds">12</span>
      </div>
      <small class="text-white-50" style="font-size: 9px;">Sec</small>
    </div>
  </div>

  <!-- Right side: Register Button -->
  <div class="d-flex align-items-center gap-2 flex-shrink-0">
    <a class="btn btn-warning btn-sm d-flex align-items-center gap-1" style="font-size: 11px; padding: 4px 8px;">
      <i class="ti ti-calendar-event fs-6"></i>
      Register
    </a>
  </div>
</div>

<!-- Countdown Timer Script -->
<script>
  // Set the target date for the tournament (you can change this)
  const targetDate = new Date('2025-12-15T18:00:00').getTime();
  
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
