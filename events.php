<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('events', 'Events Calendar');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Events - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .event-card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .event-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 1.5rem;
      position: relative;
    }
    .event-status {
      position: absolute;
      top: 1rem;
      right: 1rem;
    }
    .event-date {
      font-size: 0.9rem;
      opacity: 0.9;
    }
    .event-title {
      font-size: 1.5rem;
      font-weight: bold;
      margin: 0.5rem 0;
    }
    .event-prize {
      background: rgba(255, 255, 255, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.9rem;
      display: inline-block;
    }
    .participants-count {
      background: #f8f9fa;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.9rem;
      color: #6c757d;
    }
    .event-details {
      padding: 1.5rem;
    }
    .event-description {
      color: #6c757d;
      margin-bottom: 1rem;
    }
    .event-requirements {
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
    }
    .requirement-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
    }
    .requirement-item:last-child {
      margin-bottom: 0;
    }
    .requirement-icon {
      width: 20px;
      height: 20px;
      margin-right: 0.5rem;
    }
    .btn-register {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-register:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .btn-registered {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
    }
    .filter-section {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    .event-type-badge {
      font-size: 0.8rem;
      padding: 0.25rem 0.75rem;
      border-radius: 15px;
    }
    .event-type-solo { background: #e3f2fd; color: #1976d2; }
    .event-type-doubles { background: #e8f5e8; color: #2e7d32; }
    .event-type-team { background: #fff3e0; color: #f57c00; }
    .event-type-tournament { background: #fce4ec; color: #c2185b; }
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
            <span class="text-muted fw-semibold mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Bowling Score System</span>
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
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./dashboard.php" aria-expanded="false">
                <i class="ti ti-atom"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./lane-booking.php" aria-expanded="false">
                <i class="ti ti-calendar-plus"></i>
                <span class="hide-menu">Lane Booking</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./group-selection.php" aria-expanded="false">
                <i class="ti ti-users"></i>
                <span class="hide-menu">Join Group</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./events.php" aria-expanded="false">
                <i class="ti ti-calendar-event"></i>
                <span class="hide-menu">Events</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-table"></i>
                <span class="hide-menu">Score Table</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="./score-table-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Games</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./score-table-team.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Team (4-6 Players)</span>
                  </a>
                </li>
              </ul>
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
            <li class="nav-item dropdown">
              <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-bell"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="message-body">
                  <a href="javascript:void(0)" class="dropdown-item">
                    New event: Spring Tournament
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Event registration closing soon
                  </a>
                </div>
              </div>
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
                    <a href="./my-profile.php" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
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
                <div>
                  <h4 class="mb-0 fw-bold">Upcoming Events</h4>
                  <p class="text-muted">Join exciting bowling events and tournaments</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Events</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Section -->
          <div class="filter-section">
            <div class="row">
              <div class="col-md-3 mb-3">
                <label for="eventTypeFilter" class="form-label">Event Type</label>
                <select class="form-select" id="eventTypeFilter" onchange="filterEvents()">
                  <option value="">All Events</option>
                  <option value="solo">Solo Events</option>
                  <option value="doubles">Doubles</option>
                  <option value="team">Team Events</option>
                  <option value="tournament">Tournaments</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label for="dateFilter" class="form-label">Date Range</label>
                <select class="form-select" id="dateFilter" onchange="filterEvents()">
                  <option value="">All Dates</option>
                  <option value="today">Today</option>
                  <option value="week">This Week</option>
                  <option value="month">This Month</option>
                  <option value="upcoming">Upcoming Only</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label for="statusFilter" class="form-label">Status</label>
                <select class="form-select" id="statusFilter" onchange="filterEvents()">
                  <option value="">All Status</option>
                  <option value="open">Open for Registration</option>
                  <option value="registered">I'm Registered</option>
                  <option value="full">Full</option>
                  <option value="closed">Registration Closed</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                  <i class="ti ti-x me-1"></i>
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Events Grid -->
          <div class="row" id="eventsContainer">
            <!-- Events will be populated by JavaScript -->
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
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
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

  <!-- Events Management Script -->
  <script>
    // Sample events data
    const events = [
      {
        id: 1,
        title: "Spring Bowling Championship",
        type: "tournament",
        date: "2025-03-20",
        time: "18:00",
        location: "Main Bowling Center",
        description: "Annual spring championship with cash prizes and trophies. Open to all skill levels.",
        prize: "$2,500",
        maxParticipants: 64,
        currentParticipants: 32,
        requirements: ["Valid membership", "Minimum 10 games played", "Registration fee: $25"],
        status: "open",
        registered: false
      },
      {
        id: 2,
        title: "Friday Night Doubles",
        type: "doubles",
        date: "2025-03-15",
        time: "19:30",
        location: "Lanes 1-8",
        description: "Weekly doubles tournament. Bring a partner or we'll match you with someone.",
        prize: "Gift cards",
        maxParticipants: 16,
        currentParticipants: 12,
        requirements: ["Partner required", "No registration fee"],
        status: "open",
        registered: true
      },
      {
        id: 3,
        title: "Solo Skills Challenge",
        type: "solo",
        date: "2025-03-18",
        time: "17:00",
        location: "Practice Lanes",
        description: "Test your individual skills in various bowling challenges and mini-games.",
        prize: "Trophy + $100",
        maxParticipants: 24,
        currentParticipants: 24,
        requirements: ["Solo participation", "All skill levels welcome"],
        status: "full",
        registered: false
      },
      {
        id: 4,
        title: "Team League Championship",
        type: "team",
        date: "2025-03-25",
        time: "20:00",
        location: "All Lanes",
        description: "Championship for registered teams. 4-6 players per team required.",
        prize: "Team trophy + $1,000",
        maxParticipants: 48,
        currentParticipants: 36,
        requirements: ["Pre-registered team", "Team captain approval", "Registration fee: $50/team"],
        status: "open",
        registered: false
      },
      {
        id: 5,
        title: "Beginner's Night",
        type: "solo",
        date: "2025-03-22",
        time: "18:30",
        location: "Lanes 9-12",
        description: "Perfect for new bowlers! Learn the basics and meet other beginners.",
        prize: "Participation certificate",
        maxParticipants: 20,
        currentParticipants: 8,
        requirements: ["Beginner level only", "No experience required", "Free event"],
        status: "open",
        registered: false
      },
      {
        id: 6,
        title: "Senior Masters Tournament",
        type: "tournament",
        date: "2025-03-28",
        time: "14:00",
        location: "Main Bowling Center",
        description: "Exclusive tournament for players 50+ years old. Special prizes and recognition.",
        prize: "Senior trophy + $500",
        maxParticipants: 32,
        currentParticipants: 28,
        requirements: ["Age 50+", "Valid ID required", "Registration fee: $15"],
        status: "open",
        registered: false
      }
    ];

    let filteredEvents = [...events];

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      populateEvents();
    });

    // Populate events
    function populateEvents() {
      const container = document.getElementById('eventsContainer');
      container.innerHTML = '';

      if (filteredEvents.length === 0) {
        container.innerHTML = `
          <div class="col-12">
            <div class="card">
              <div class="card-body text-center py-5">
                <i class="ti ti-calendar-off fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">No Events Found</h5>
                <p class="text-muted">No events match your current filters. Try adjusting your search criteria.</p>
                <button class="btn btn-primary" onclick="clearFilters()">
                  <i class="ti ti-refresh me-1"></i>
                  Clear Filters
                </button>
              </div>
            </div>
          </div>
        `;
        return;
      }

      filteredEvents.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'col-lg-6 col-xl-4 mb-4';
        eventCard.innerHTML = createEventCard(event);
        container.appendChild(eventCard);
      });
    }

    // Create event card HTML
    function createEventCard(event) {
      const eventDate = new Date(event.date);
      const formattedDate = eventDate.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });

      const statusBadge = getStatusBadge(event.status, event.registered);
      const typeBadge = getTypeBadge(event.type);
      const registerButton = getRegisterButton(event);

      return `
        <div class="card event-card">
          <div class="event-header">
            <div class="event-status">
              ${statusBadge}
            </div>
            <div class="event-date">
              <i class="ti ti-calendar me-1"></i>
              ${formattedDate} at ${event.time}
            </div>
            <div class="event-title">${event.title}</div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="event-prize">
                <i class="ti ti-trophy me-1"></i>
                ${event.prize}
              </span>
              <span class="participants-count">
                <i class="ti ti-users me-1"></i>
                ${event.currentParticipants}/${event.maxParticipants}
              </span>
            </div>
          </div>
          <div class="event-details">
            <div class="d-flex justify-content-between align-items-center mb-2">
              ${typeBadge}
              <small class="text-muted">
                <i class="ti ti-map-pin me-1"></i>
                ${event.location}
              </small>
            </div>
            <p class="event-description">${event.description}</p>
            <div class="event-requirements">
              <h6 class="mb-2">Requirements:</h6>
              ${event.requirements.map(req => `
                <div class="requirement-item">
                  <i class="ti ti-check requirement-icon text-success"></i>
                  <span>${req}</span>
                </div>
              `).join('')}
            </div>
            <div class="d-flex gap-2">
              ${registerButton}
              <button class="btn btn-outline-primary" onclick="viewEventDetails(${event.id})">
                <i class="ti ti-eye me-1"></i>
                Details
              </button>
            </div>
          </div>
        </div>
      `;
    }

    // Get status badge
    function getStatusBadge(status, registered) {
      if (registered) {
        return '<span class="badge bg-success">Registered</span>';
      }
      
      switch(status) {
        case 'open':
          return '<span class="badge bg-primary">Open</span>';
        case 'full':
          return '<span class="badge bg-warning">Full</span>';
        case 'closed':
          return '<span class="badge bg-danger">Closed</span>';
        default:
          return '<span class="badge bg-secondary">Unknown</span>';
      }
    }

    // Get type badge
    function getTypeBadge(type) {
      const badges = {
        'solo': '<span class="badge event-type-badge event-type-solo">Solo</span>',
        'doubles': '<span class="badge event-type-badge event-type-doubles">Doubles</span>',
        'team': '<span class="badge event-type-badge event-type-team">Team</span>',
        'tournament': '<span class="badge event-type-badge event-type-tournament">Tournament</span>'
      };
      return badges[type] || '<span class="badge bg-secondary">Event</span>';
    }

    // Get register button
    function getRegisterButton(event) {
      if (event.registered) {
        return '<button class="btn btn-registered" disabled><i class="ti ti-check me-1"></i>Registered</button>';
      }
      
      if (event.status === 'full') {
        return '<button class="btn btn-outline-secondary" disabled><i class="ti ti-user-x me-1"></i>Event Full</button>';
      }
      
      if (event.status === 'closed') {
        return '<button class="btn btn-outline-secondary" disabled><i class="ti ti-lock me-1"></i>Registration Closed</button>';
      }
      
      return `<button class="btn btn-register" onclick="registerForEvent(${event.id})"><i class="ti ti-user-plus me-1"></i>Register</button>`;
    }

    // Register for event
    function registerForEvent(eventId) {
      const event = events.find(e => e.id === eventId);
      if (event && event.status === 'open' && !event.registered) {
        event.registered = true;
        event.currentParticipants++;
        
        if (event.currentParticipants >= event.maxParticipants) {
          event.status = 'full';
        }
        
        showNotification(`Successfully registered for ${event.title}!`, 'success');
        populateEvents();
      }
    }

    // View event details
    function viewEventDetails(eventId) {
      const event = events.find(e => e.id === eventId);
      if (event) {
        // Create modal for event details
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">${event.title}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <h6>Event Details</h6>
                    <p><strong>Date:</strong> ${new Date(event.date).toLocaleDateString()}</p>
                    <p><strong>Time:</strong> ${event.time}</p>
                    <p><strong>Location:</strong> ${event.location}</p>
                    <p><strong>Prize:</strong> ${event.prize}</p>
                    <p><strong>Participants:</strong> ${event.currentParticipants}/${event.maxParticipants}</p>
                  </div>
                  <div class="col-md-6">
                    <h6>Description</h6>
                    <p>${event.description}</p>
                    <h6>Requirements</h6>
                    <ul>
                      ${event.requirements.map(req => `<li>${req}</li>`).join('')}
                    </ul>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                ${event.status === 'open' && !event.registered ? 
                  `<button type="button" class="btn btn-primary" onclick="registerForEvent(${event.id}); bootstrap.Modal.getInstance(this.closest('.modal')).hide();">Register Now</button>` : 
                  ''
                }
              </div>
            </div>
          </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.addEventListener('hidden.bs.modal', function() {
          document.body.removeChild(modal);
        });
      }
    }

    // Filter events
    function filterEvents() {
      const typeFilter = document.getElementById('eventTypeFilter').value;
      const dateFilter = document.getElementById('dateFilter').value;
      const statusFilter = document.getElementById('statusFilter').value;
      
      filteredEvents = events.filter(event => {
        const typeMatch = !typeFilter || event.type === typeFilter;
        const statusMatch = !statusFilter || 
          (statusFilter === 'registered' && event.registered) ||
          (statusFilter === 'open' && event.status === 'open' && !event.registered) ||
          (statusFilter === 'full' && event.status === 'full') ||
          (statusFilter === 'closed' && event.status === 'closed');
        
        let dateMatch = true;
        if (dateFilter) {
          const eventDate = new Date(event.date);
          const today = new Date();
          const weekFromNow = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
          const monthFromNow = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);
          
          switch(dateFilter) {
            case 'today':
              dateMatch = eventDate.toDateString() === today.toDateString();
              break;
            case 'week':
              dateMatch = eventDate >= today && eventDate <= weekFromNow;
              break;
            case 'month':
              dateMatch = eventDate >= today && eventDate <= monthFromNow;
              break;
            case 'upcoming':
              dateMatch = eventDate >= today;
              break;
          }
        }
        
        return typeMatch && statusMatch && dateMatch;
      });
      
      populateEvents();
    }

    // Clear filters
    function clearFilters() {
      document.getElementById('eventTypeFilter').value = '';
      document.getElementById('dateFilter').value = '';
      document.getElementById('statusFilter').value = '';
      filteredEvents = [...events];
      populateEvents();
    }

    // Notification function
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
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
