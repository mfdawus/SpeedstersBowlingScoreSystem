<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('events-admin', 'Events Management (Admin)');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Events Management - SPEEDSTERS Admin</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .admin-card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .admin-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .event-status-badge {
      font-size: 0.8rem;
      padding: 0.25rem 0.75rem;
      border-radius: 15px;
    }
    .status-draft { background: #e9ecef; color: #495057; }
    .status-published { background: #d1ecf1; color: #0c5460; }
    .status-closed { background: #f8d7da; color: #721c24; }
    .status-completed { background: #d4edda; color: #155724; }
    .participants-progress {
      height: 8px;
      border-radius: 4px;
      background: #e9ecef;
      overflow: hidden;
    }
    .participants-bar {
      height: 100%;
      background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
      transition: width 0.3s ease;
    }
    .btn-create-event {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-create-event:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .stats-card {
      background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
      border: none;
      border-radius: 15px;
      transition: all 0.3s ease;
    }
    .stats-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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
              <span class="hide-menu">Admin Panel</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-dashboard.php" aria-expanded="false">
                <i class="ti ti-dashboard"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-user-management.php" aria-expanded="false">
                <i class="ti ti-users"></i>
                <span class="hide-menu">User Management</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./admin-events.php" aria-expanded="false">
                <i class="ti ti-calendar-event"></i>
                <span class="hide-menu">Events Management</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-score-monitoring-solo.php" aria-expanded="false">
                <i class="ti ti-chart-bar"></i>
                <span class="hide-menu">Score Monitoring</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-score-update.php" aria-expanded="false">
                <i class="ti ti-edit"></i>
                <span class="hide-menu">Update Scores</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-create-account.php" aria-expanded="false">
                <i class="ti ti-user-plus"></i>
                <span class="hide-menu">Create Account</span>
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
            <li class="nav-item dropdown">
              <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-bell"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                <div class="message-body">
                  <a href="javascript:void(0)" class="dropdown-item">
                    New event registration
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Event capacity reached
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
                  <h4 class="mb-0 fw-bold">Events Management</h4>
                  <p class="text-muted">Create and manage bowling events and tournaments</p>
                </div>
                <div class="page-title-right">
                  <button class="btn btn-create-event" onclick="openCreateEventModal()">
                    <i class="ti ti-plus me-1"></i>
                    Create New Event
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Statistics Cards -->
          <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
              <div class="card stats-card">
                <div class="card-body text-center">
                  <div class="display-6 text-primary fw-bold" id="totalEvents">6</div>
                  <small class="text-muted">Total Events</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
              <div class="card stats-card">
                <div class="card-body text-center">
                  <div class="display-6 text-success fw-bold" id="activeEvents">4</div>
                  <small class="text-muted">Active Events</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
              <div class="card stats-card">
                <div class="card-body text-center">
                  <div class="display-6 text-warning fw-bold" id="totalRegistrations">140</div>
                  <small class="text-muted">Total Registrations</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
              <div class="card stats-card">
                <div class="card-body text-center">
                  <div class="display-6 text-info fw-bold" id="upcomingEvents">3</div>
                  <small class="text-muted">Upcoming Events</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Events Table -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">All Events</h5>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="statusFilter" onchange="filterEvents()">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                        <option value="completed">Completed</option>
                      </select>
                      <button class="btn btn-outline-secondary btn-sm" onclick="refreshEvents()">
                        <i class="ti ti-refresh"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Event</th>
                          <th>Type</th>
                          <th>Date & Time</th>
                          <th>Participants</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="eventsTableBody">
                        <!-- Events will be populated by JavaScript -->
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
    // Sample events data for admin
    const adminEvents = [
      {
        id: 1,
        title: "Spring Bowling Championship",
        type: "tournament",
        date: "2025-03-20",
        time: "18:00",
        location: "Main Bowling Center",
        description: "Annual spring championship with cash prizes and trophies.",
        prize: "$2,500",
        maxParticipants: 64,
        currentParticipants: 32,
        status: "published",
        createdDate: "2025-02-15",
        createdBy: "Admin User"
      },
      {
        id: 2,
        title: "Friday Night Doubles",
        type: "doubles",
        date: "2025-03-15",
        time: "19:30",
        location: "Lanes 1-8",
        description: "Weekly doubles tournament.",
        prize: "Gift cards",
        maxParticipants: 16,
        currentParticipants: 12,
        status: "published",
        createdDate: "2025-02-10",
        createdBy: "Admin User"
      },
      {
        id: 3,
        title: "Solo Skills Challenge",
        type: "solo",
        date: "2025-03-18",
        time: "17:00",
        location: "Practice Lanes",
        description: "Test your individual skills in various bowling challenges.",
        prize: "Trophy + $100",
        maxParticipants: 24,
        currentParticipants: 24,
        status: "closed",
        createdDate: "2025-02-08",
        createdBy: "Admin User"
      },
      {
        id: 4,
        title: "Team League Championship",
        type: "team",
        date: "2025-03-25",
        time: "20:00",
        location: "All Lanes",
        description: "Championship for registered teams.",
        prize: "Team trophy + $1,000",
        maxParticipants: 48,
        currentParticipants: 36,
        status: "published",
        createdDate: "2025-02-12",
        createdBy: "Admin User"
      },
      {
        id: 5,
        title: "Beginner's Night",
        type: "solo",
        date: "2025-03-22",
        time: "18:30",
        location: "Lanes 9-12",
        description: "Perfect for new bowlers!",
        prize: "Participation certificate",
        maxParticipants: 20,
        currentParticipants: 8,
        status: "draft",
        createdDate: "2025-02-20",
        createdBy: "Admin User"
      },
      {
        id: 6,
        title: "Senior Masters Tournament",
        type: "tournament",
        date: "2025-02-28",
        time: "14:00",
        location: "Main Bowling Center",
        description: "Exclusive tournament for players 50+ years old.",
        prize: "Senior trophy + $500",
        maxParticipants: 32,
        currentParticipants: 28,
        status: "completed",
        createdDate: "2025-01-15",
        createdBy: "Admin User"
      }
    ];

    let filteredEvents = [...adminEvents];

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      populateEventsTable();
      updateStatistics();
    });

    // Populate events table
    function populateEventsTable() {
      const tbody = document.getElementById('eventsTableBody');
      tbody.innerHTML = '';

      filteredEvents.forEach(event => {
        const row = document.createElement('tr');
        row.innerHTML = createEventRow(event);
        tbody.appendChild(row);
      });
    }

    // Create event row HTML
    function createEventRow(event) {
      const eventDate = new Date(event.date);
      const formattedDate = eventDate.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
      const formattedTime = event.time;

      const statusBadge = getStatusBadge(event.status);
      const typeBadge = getTypeBadge(event.type);
      const participantsProgress = (event.currentParticipants / event.maxParticipants) * 100;

      return `
        <tr>
          <td>
            <div>
              <h6 class="mb-1">${event.title}</h6>
              <small class="text-muted">${event.location}</small>
            </div>
          </td>
          <td>${typeBadge}</td>
          <td>
            <div>
              <div class="fw-semibold">${formattedDate}</div>
              <small class="text-muted">${formattedTime}</small>
            </div>
          </td>
          <td>
            <div class="mb-1">
              <span class="fw-semibold">${event.currentParticipants}</span>
              <span class="text-muted">/ ${event.maxParticipants}</span>
            </div>
            <div class="participants-progress">
              <div class="participants-bar" style="width: ${participantsProgress}%"></div>
            </div>
          </td>
          <td>${statusBadge}</td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-sm btn-outline-primary" onclick="editEvent(${event.id})" title="Edit">
                <i class="ti ti-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-info" onclick="viewParticipants(${event.id})" title="View Participants">
                <i class="ti ti-users"></i>
              </button>
              <button class="btn btn-sm btn-outline-warning" onclick="duplicateEvent(${event.id})" title="Duplicate">
                <i class="ti ti-copy"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteEvent(${event.id})" title="Delete">
                <i class="ti ti-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }

    // Get status badge
    function getStatusBadge(status) {
      const badges = {
        'draft': '<span class="badge event-status-badge status-draft">Draft</span>',
        'published': '<span class="badge event-status-badge status-published">Published</span>',
        'closed': '<span class="badge event-status-badge status-closed">Closed</span>',
        'completed': '<span class="badge event-status-badge status-completed">Completed</span>'
      };
      return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }

    // Get type badge
    function getTypeBadge(type) {
      const badges = {
        'solo': '<span class="badge bg-primary">Solo</span>',
        'doubles': '<span class="badge bg-success">Doubles</span>',
        'team': '<span class="badge bg-warning">Team</span>',
        'tournament': '<span class="badge bg-danger">Tournament</span>'
      };
      return badges[type] || '<span class="badge bg-secondary">Event</span>';
    }

    // Update statistics
    function updateStatistics() {
      const totalEvents = adminEvents.length;
      const activeEvents = adminEvents.filter(e => e.status === 'published').length;
      const totalRegistrations = adminEvents.reduce((sum, e) => sum + e.currentParticipants, 0);
      const upcomingEvents = adminEvents.filter(e => {
        const eventDate = new Date(e.date);
        const today = new Date();
        return eventDate >= today && e.status === 'published';
      }).length;

      document.getElementById('totalEvents').textContent = totalEvents;
      document.getElementById('activeEvents').textContent = activeEvents;
      document.getElementById('totalRegistrations').textContent = totalRegistrations;
      document.getElementById('upcomingEvents').textContent = upcomingEvents;
    }

    // Filter events
    function filterEvents() {
      const statusFilter = document.getElementById('statusFilter').value;
      
      if (statusFilter) {
        filteredEvents = adminEvents.filter(event => event.status === statusFilter);
      } else {
        filteredEvents = [...adminEvents];
      }
      
      populateEventsTable();
    }

    // Refresh events
    function refreshEvents() {
      showNotification('Events refreshed successfully!', 'success');
      populateEventsTable();
      updateStatistics();
    }

    // Edit event
    function editEvent(eventId) {
      const event = adminEvents.find(e => e.id === eventId);
      if (event) {
        showNotification(`Editing event: ${event.title}`, 'info');
        // Here you would open an edit modal or navigate to edit page
      }
    }

    // View participants
    function viewParticipants(eventId) {
      const event = adminEvents.find(e => e.id === eventId);
      if (event) {
        showNotification(`Viewing participants for: ${event.title}`, 'info');
        // Here you would open a participants modal
      }
    }

    // Duplicate event
    function duplicateEvent(eventId) {
      const event = adminEvents.find(e => e.id === eventId);
      if (event) {
        const newEvent = {
          ...event,
          id: adminEvents.length + 1,
          title: `${event.title} (Copy)`,
          status: 'draft',
          currentParticipants: 0,
          createdDate: new Date().toISOString().split('T')[0]
        };
        adminEvents.push(newEvent);
        populateEventsTable();
        updateStatistics();
        showNotification(`Event duplicated: ${newEvent.title}`, 'success');
      }
    }

    // Delete event
    function deleteEvent(eventId) {
      const event = adminEvents.find(e => e.id === eventId);
      if (event) {
        if (confirm(`Are you sure you want to delete "${event.title}"?`)) {
          const index = adminEvents.findIndex(e => e.id === eventId);
          adminEvents.splice(index, 1);
          populateEventsTable();
          updateStatistics();
          showNotification(`Event deleted: ${event.title}`, 'success');
        }
      }
    }

    // Open create event modal
    function openCreateEventModal() {
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.innerHTML = `
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Create New Event</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form id="createEventForm">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="eventTitle" class="form-label">Event Title *</label>
                    <input type="text" class="form-control" id="eventTitle" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="eventType" class="form-label">Event Type *</label>
                    <select class="form-select" id="eventType" required>
                      <option value="">Select Type</option>
                      <option value="solo">Solo</option>
                      <option value="doubles">Doubles</option>
                      <option value="team">Team</option>
                      <option value="tournament">Tournament</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="eventDate" class="form-label">Event Date *</label>
                    <input type="date" class="form-control" id="eventDate" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="eventTime" class="form-label">Event Time *</label>
                    <input type="time" class="form-control" id="eventTime" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="eventLocation" class="form-label">Location *</label>
                    <input type="text" class="form-control" id="eventLocation" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="maxParticipants" class="form-label">Max Participants *</label>
                    <input type="number" class="form-control" id="maxParticipants" min="1" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="eventPrize" class="form-label">Prize/Reward</label>
                    <input type="text" class="form-control" id="eventPrize" placeholder="e.g., $500, Trophy, Gift Cards">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="eventStatus" class="form-label">Status</label>
                    <select class="form-select" id="eventStatus">
                      <option value="draft">Draft</option>
                      <option value="published">Published</option>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="eventDescription" class="form-label">Event Description *</label>
                  <textarea class="form-control" id="eventDescription" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="eventRequirements" class="form-label">Requirements (one per line)</label>
                  <textarea class="form-control" id="eventRequirements" rows="3" placeholder="e.g., Valid membership&#10;Minimum 10 games played&#10;Registration fee: $25"></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="createEvent()">Create Event</button>
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

    // Create event function
    function createEvent() {
      const form = document.getElementById('createEventForm');
      const formData = new FormData(form);
      
      // Get form values
      const title = document.getElementById('eventTitle').value;
      const type = document.getElementById('eventType').value;
      const date = document.getElementById('eventDate').value;
      const time = document.getElementById('eventTime').value;
      const location = document.getElementById('eventLocation').value;
      const maxParticipants = parseInt(document.getElementById('maxParticipants').value);
      const prize = document.getElementById('eventPrize').value;
      const status = document.getElementById('eventStatus').value;
      const description = document.getElementById('eventDescription').value;
      const requirementsText = document.getElementById('eventRequirements').value;
      
      // Validate required fields
      if (!title || !type || !date || !time || !location || !maxParticipants || !description) {
        showNotification('Please fill in all required fields', 'error');
        return;
      }
      
      // Parse requirements
      const requirements = requirementsText ? requirementsText.split('\n').filter(req => req.trim()) : [];
      
      // Create new event
      const newEvent = {
        id: adminEvents.length + 1,
        title: title,
        type: type,
        date: date,
        time: time,
        location: location,
        description: description,
        prize: prize || 'TBD',
        maxParticipants: maxParticipants,
        currentParticipants: 0,
        requirements: requirements,
        status: status,
        createdDate: new Date().toISOString().split('T')[0],
        createdBy: 'Admin User'
      };
      
      // Add to events array
      adminEvents.push(newEvent);
      
      // Update UI
      populateEventsTable();
      updateStatistics();
      
      // Close modal
      const modal = document.querySelector('.modal');
      if (modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal.hide();
      }
      
      showNotification(`Event "${title}" created successfully!`, 'success');
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
