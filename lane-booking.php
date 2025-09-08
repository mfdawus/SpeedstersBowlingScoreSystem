<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('booking', 'Lane Booking System');
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

          <!-- Booking Steps -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-center mb-4">
                    <div class="d-flex align-items-center">
                      <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="ti ti-calendar text-white fs-5"></i>
                      </div>
                      <div class="me-3">
                        <h6 class="mb-0 fw-bold">1. Select Date</h6>
                        <small class="text-muted">Choose your preferred date</small>
                      </div>
                    </div>
                    <div class="mx-3">
                      <i class="ti ti-arrow-right text-muted fs-4"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="bg-secondary rounded-circle p-2 me-3">
                        <i class="ti ti-clock text-white fs-5"></i>
                      </div>
                      <div class="me-3">
                        <h6 class="mb-0 fw-bold">2. Choose Time</h6>
                        <small class="text-muted">Pick available time slot</small>
                      </div>
                    </div>
                    <div class="mx-3">
                      <i class="ti ti-arrow-right text-muted fs-4"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="bg-secondary rounded-circle p-2 me-3">
                        <i class="ti ti-target text-white fs-5"></i>
                      </div>
                      <div class="me-3">
                        <h6 class="mb-0 fw-bold">3. Select Lane</h6>
                        <small class="text-muted">Choose your lane</small>
                      </div>
                    </div>
                    <div class="mx-3">
                      <i class="ti ti-arrow-right text-muted fs-4"></i>
                    </div>
                    <div class="d-flex align-items-center">
                      <div class="bg-secondary rounded-circle p-2 me-3">
                        <i class="ti ti-check text-white fs-5"></i>
                      </div>
                      <div>
                        <h6 class="mb-0 fw-bold">4. Confirm</h6>
                        <small class="text-muted">Complete booking</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Booking Interface -->
          <div class="row">
            <!-- Date Selection -->
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-calendar me-2"></i>
                    Select Date
                  </h5>
                  
                  <!-- Calendar -->
                  <div class="calendar-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <button class="btn btn-outline-secondary btn-sm" onclick="previousMonth()">
                        <i class="ti ti-chevron-left"></i>
                      </button>
                      <h6 class="mb-0" id="currentMonth">March 2025</h6>
                      <button class="btn btn-outline-secondary btn-sm" onclick="nextMonth()">
                        <i class="ti ti-chevron-right"></i>
                      </button>
                    </div>
                    
                    <div class="calendar-grid">
                      <div class="calendar-header">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                      </div>
                      <div class="calendar-days" id="calendarDays">
                        <!-- Calendar days will be populated by JavaScript -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Time Selection -->
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-clock me-2"></i>
                    Select Time
                  </h5>
                  
                  <div class="time-slots" id="timeSlots">
                    <!-- Time slots will be populated by JavaScript -->
                  </div>
                </div>
              </div>
            </div>

            <!-- Lane Selection -->
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-target me-2"></i>
                    Select Lane
                  </h5>
                  
                  <div class="lanes-grid" id="lanesGrid">
                    <!-- Lanes will be populated by JavaScript -->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking Summary -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-receipt me-2"></i>
                    Booking Summary
                  </h5>
                  
                  <div class="row">
                    <div class="col-12">
                      <div class="booking-details">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                              <i class="ti ti-calendar text-primary me-3 fs-4"></i>
                              <div>
                                <h6 class="mb-0">Selected Date</h6>
                                <p class="text-muted mb-0" id="selectedDate">Not selected</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                              <i class="ti ti-clock text-primary me-3 fs-4"></i>
                              <div>
                                <h6 class="mb-0">Selected Time</h6>
                                <p class="text-muted mb-0" id="selectedTime">Not selected</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                              <i class="ti ti-target text-primary me-3 fs-4"></i>
                              <div>
                                <h6 class="mb-0">Selected Lane</h6>
                                <p class="text-muted mb-0" id="selectedLane">Not selected</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                  
                  <div class="mt-4">
                    <button class="btn btn-primary btn-lg" id="confirmBooking" disabled>
                      <i class="ti ti-check me-2"></i>
                      Confirm Booking
                    </button>
                    <button class="btn btn-outline-secondary btn-lg ms-2" onclick="resetBooking()">
                      <i class="ti ti-refresh me-2"></i>
                      Reset
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Bookings -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-history me-2"></i>
                    Recent Bookings
                  </h5>
                  
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Lane</th>
                          <th>Duration</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>Mar 15, 2025</td>
                          <td>2:00 PM</td>
                          <td>Lane 3</td>
                          <td>2 hours</td>
                          <td><span class="badge bg-success">Confirmed</span></td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                            <button class="btn btn-sm btn-outline-danger">Cancel</button>
                          </td>
                        </tr>
                        <tr>
                          <td>Mar 12, 2025</td>
                          <td>7:30 PM</td>
                          <td>Lane 5</td>
                          <td>2 hours</td>
                          <td><span class="badge bg-success">Completed</span></td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                          </td>
                        </tr>
                        <tr>
                          <td>Mar 10, 2025</td>
                          <td>4:00 PM</td>
                          <td>Lane 2</td>
                          <td>2 hours</td>
                          <td><span class="badge bg-success">Completed</span></td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                          </td>
                        </tr>
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
    let selectedDate = null;
    let selectedTime = null;
    let selectedLane = null;
    let currentMonth = new Date();

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
      generateCalendar();
      generateTimeSlots();
      generateLanes();
      updateBookingSummary();
    });

    // Calendar functions
    function generateCalendar() {
      const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
      
      document.getElementById('currentMonth').textContent = 
        monthNames[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear();
      
      const firstDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
      const lastDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);
      const startDate = new Date(firstDay);
      startDate.setDate(startDate.getDate() - firstDay.getDay());
      
      const calendarDays = document.getElementById('calendarDays');
      calendarDays.innerHTML = '';
      
      for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = date.getDate();
        
        if (date.getMonth() !== currentMonth.getMonth()) {
          dayElement.classList.add('other-month');
        }
        
        if (date.toDateString() === new Date().toDateString()) {
          dayElement.classList.add('today');
        }
        
        dayElement.addEventListener('click', () => selectDate(date));
        calendarDays.appendChild(dayElement);
      }
    }

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

    // Lanes functions
    function generateLanes() {
      const lanesGrid = document.getElementById('lanesGrid');
      lanesGrid.innerHTML = '';
      
      for (let i = 1; i <= 8; i++) {
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

    function selectLane(laneNumber) {
      selectedLane = laneNumber;
      
      document.querySelectorAll('.lane-card').forEach(lane => {
        lane.classList.remove('selected');
      });
      
      event.target.closest('.lane-card').classList.add('selected');
      updateBookingSummary();
    }

    function updateLanes() {
      // Simulate lane availability based on time
      const lanes = document.querySelectorAll('.lane-card');
      lanes.forEach((lane, index) => {
        lane.classList.remove('booked');
        if (Math.random() < 0.4) { // 40% chance of being booked
          lane.classList.add('booked');
          lane.classList.remove('available');
          lane.querySelector('small').textContent = 'Booked';
          lane.querySelector('small').className = 'text-danger';
        } else {
          lane.classList.add('available');
          lane.classList.remove('booked');
          lane.querySelector('small').textContent = 'Available';
          lane.querySelector('small').className = 'text-success';
        }
      });
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
