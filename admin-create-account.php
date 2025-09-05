<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Account - SPEEDSTERS Bowling System</title>
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
    .account-type-card {
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }
    .account-type-card:hover {
      border-color: #0d6efd;
      transform: translateY(-2px);
    }
    .account-type-card.selected {
      border-color: #0d6efd;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    }
    .form-section {
      display: none;
    }
    .form-section.active {
      display: block;
    }
    .admin-badge {
      background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
      color: #333;
    }
    .skill-level-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .team-member-card {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
      background: #f8f9fa;
    }
    .team-member-card.selected {
      border-color: #0d6efd;
      background: #e3f2fd;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">

    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img d-flex flex-column align-items-start text-decoration-none">
            <img src="assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="90" />
            <span class="text-muted fw-semibold mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Admin Panel</span>
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
              <a class="sidebar-link" href="./admin-user-management.php">
                <i class="ti ti-users"></i>
                <span class="hide-menu">User Management</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-chart-bar"></i>
                <span class="hide-menu">Score Monitoring</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-solo.php" class="sidebar-link">
                    <i class="ti ti-user"></i>
                    <span class="hide-menu">Solo Players</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-doubles.php" class="sidebar-link">
                    <i class="ti ti-users"></i>
                    <span class="hide-menu">Doubles Teams</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="./admin-score-monitoring-team.php" class="sidebar-link">
                    <i class="ti ti-users-group"></i>
                    <span class="hide-menu">Team (4-6 Players)</span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./admin-score-update.php">
                <i class="ti ti-edit"></i>
                <span class="hide-menu">Update Scores</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link active" href="./admin-create-account.php">
                <i class="ti ti-user-plus"></i>
                <span class="hide-menu">Create Account</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="javascript:void(0)" aria-expanded="false">
                <i class="ti ti-settings"></i>
                <span class="hide-menu">System Settings</span>
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
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">Admin Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-settings fs-6"></i>
                      <p class="mb-0 fs-3">Settings</p>
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
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="./admin-dashboard.php">Admin Dashboard</a></li>
                    <li class="breadcrumb-item active">Create Account</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Creation Statistics -->
          <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Total Accounts</h6>
                      <h3 class="mb-0 text-primary">1,247</h3>
                      <small class="text-muted">+12% this month</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users fs-1 text-muted"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Created Today</h6>
                      <h3 class="mb-0 text-success">23</h3>
                      <small class="text-muted">+5 vs yesterday</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-user-plus fs-1 text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Active Teams</h6>
                      <h3 class="mb-0 text-warning">89</h3>
                      <small class="text-muted">+3 new this week</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-users-group fs-1 text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                      <h6 class="card-title text-muted mb-1">Pending Approval</h6>
                      <h3 class="mb-0 text-info">7</h3>
                      <small class="text-muted">Requires review</small>
                    </div>
                    <div class="ms-3">
                      <i class="ti ti-clock fs-1 text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Creation Header -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body text-center">
                  <i class="ti ti-user-plus fs-1 text-primary mb-3"></i>
                  <h5 class="card-title fw-semibold mb-2">Create New Player Account</h5>
                  <p class="text-muted mb-0">Add a new individual bowling player to the system</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Player Account Creation Form -->
          <div class="row">
            <div class="col-12">
              <div class="card admin-card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Player Information</h5>
                      <span class="fw-normal text-muted">Enter the details for the new bowling player</span>
                    </div>
                    <div class="d-flex gap-2">
                      <button class="btn btn-secondary btn-sm" onclick="clearForm()">
                        <i class="ti ti-refresh me-1"></i>
                        Clear Form
                      </button>
                      <button class="btn btn-info btn-sm" onclick="importFromCSV()">
                        <i class="ti ti-upload me-1"></i>
                        Import CSV
                      </button>
                    </div>
                  </div>

                  <form id="playerForm">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">First Name *</label>
                        <input type="text" class="form-control" id="firstName" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">Last Name *</label>
                        <input type="text" class="form-control" id="lastName" required>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone">
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="skill" class="form-label">Skill Level *</label>
                        <select class="form-select" id="skill" required>
                          <option value="">Select Skill Level</option>
                          <option value="beginner">Beginner (0-150 avg)</option>
                          <option value="intermediate">Intermediate (150-200 avg)</option>
                          <option value="advanced">Advanced (200-250 avg)</option>
                          <option value="pro">Professional (250+ avg)</option>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" min="5" max="100">
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label for="notes" class="form-label">Notes (Optional)</label>
                      <textarea class="form-control" id="notes" rows="3" placeholder="Any additional notes about the player..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                      <i class="ti ti-info-circle me-2"></i>
                      <strong>Note:</strong> The player will receive an email with their login credentials and can start playing immediately.
                    </div>
                  </form>

                  <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-secondary" onclick="clearForm()">Clear Form</button>
                    <button class="btn btn-primary" onclick="createPlayer()">
                      <i class="ti ti-user-plus me-2"></i>
                      Create Player Account
                    </button>
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
  
  <script>
    // Player account creation
    function createPlayer() {
      const form = document.getElementById('playerForm');
      
      // Validate required fields
      const firstName = document.getElementById('firstName').value;
      const lastName = document.getElementById('lastName').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const skill = document.getElementById('skill').value;
      
      if (!firstName || !lastName || !email || !password || !skill) {
        showNotification('Please fill in all required fields', 'warning');
        return;
      }
      
      if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'warning');
        return;
      }
      
      if (password.length < 6) {
        showNotification('Password must be at least 6 characters long', 'warning');
        return;
      }
      
      // Validate email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'warning');
        return;
      }
      
      // Simulate API call
      setTimeout(() => {
        showNotification('Player account created successfully!', 'success');
        clearForm();
      }, 1000);
    }

    // Utility functions
    function clearForm() {
      document.getElementById('playerForm').reset();
      showNotification('Form cleared', 'info');
    }

    function importFromCSV() {
      showNotification('CSV import functionality would open here', 'info');
    }

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
</body>

</html>
