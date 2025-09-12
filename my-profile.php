<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .profile-picture {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    .profile-picture:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    .profile-upload-area {
      border: 2px dashed #dee2e6;
      border-radius: 10px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .profile-upload-area:hover {
      border-color: #0d6efd;
      background-color: #f8f9ff;
    }
    .profile-upload-area.dragover {
      border-color: #0d6efd;
      background-color: #e3f2fd;
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
    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .btn-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
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
                    <span class="hide-menu">Team</span>
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
                    Item 1
                  </a>
                  <a href="javascript:void(0)" class="dropdown-item">
                    Item 2
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
                  <h4 class="mb-0 fw-bold">My Profile</h4>
                  <p class="text-muted">Manage your personal information and preferences</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Profile</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Content -->
          <div class="row">
            <!-- Profile Picture Section -->
            <div class="col-lg-4">
              <div class="card stats-card">
                <div class="card-body text-center">
                  <div class="mb-4">
                    <img src="./assets/images/profile/user-1.jpg" alt="Profile Picture" class="profile-picture" id="profilePicture">
                  </div>
                  
                  <h5 class="fw-bold mb-1" id="userNickname">John Smith</h5>
                  <p class="text-muted mb-3">Pro Bowler</p>
                  
                  <!-- Profile Picture Upload -->
                  <div class="profile-upload-area" onclick="showMaintenanceNotice()" style="opacity: 0.7; cursor: not-allowed;">
                    <i class="ti ti-tools fs-1 text-warning mb-3"></i>
                    <h6 class="mb-2">Profile Picture Update</h6>
                    <p class="text-muted small mb-0">Under Maintenance</p>
                    <p class="text-muted small">Available March 2025</p>
                  </div>
                  
                  <input type="file" id="profileImageInput" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                  
                  <div class="mt-3">
                    <button class="btn btn-outline-warning btn-sm" onclick="showMaintenanceNotice()">
                      <i class="ti ti-tools me-1"></i>
                      Upload New Photo (Under Maintenance)
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Profile Information Section -->
            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <!-- Maintenance Notice Banner -->
                  <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                      <i class="ti ti-tools fs-4 me-3"></i>
                      <div>
                        <h6 class="alert-heading mb-1">Profile Updates Under Maintenance</h6>
                        <p class="mb-0 small">We're upgrading our profile management system. Updates will be available in March 2025.</p>
                      </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                  
                  <h5 class="card-title mb-4">
                    <i class="ti ti-user-edit me-2"></i>
                    Profile Information
                  </h5>
                  
                  <form id="profileForm">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="nickname" class="form-label">Nickname</label>
                        <input type="text" class="form-control" id="nickname" name="nickname" value="John Smith" placeholder="Enter your nickname" disabled>
                        <div class="form-text">This is how other players will see you in the system.</div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="john.smith@email.com" readonly>
                        <div class="form-text">Email cannot be changed. Contact support if needed.</div>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="+1 (555) 123-4567" placeholder="Enter your phone number" disabled>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Skill Level</label>
                        <div class="form-control-plaintext">
                          <span class="badge bg-info fs-6">C - Above Average (160-179)</span>
                          <small class="text-muted d-block mt-1">Automatically calculated based on your performance</small>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="preferredLane" class="form-label">Preferred Lane</label>
                        <select class="form-select" id="preferredLane" name="preferredLane" disabled>
                          <option value="">No Preference</option>
                          <option value="1">Lane 1</option>
                          <option value="2">Lane 2</option>
                          <option value="3" selected>Lane 3</option>
                          <option value="4">Lane 4</option>
                          <option value="5">Lane 5</option>
                          <option value="6">Lane 6</option>
                          <option value="7">Lane 7</option>
                          <option value="8">Lane 8</option>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="teamPreference" class="form-label">Team Preference</label>
                        <select class="form-select" id="teamPreference" name="teamPreference" disabled>
                          <option value="solo">Solo Games</option>
                          <option value="duo" selected>Duo Teams</option>
                          <option value="trio">Trio Teams</option>
                          <option value="team">Team</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label for="bio" class="form-label">Bio</label>
                      <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us about yourself..." disabled>Passionate bowler with 5+ years of experience. Love competing in tournaments and helping new players improve their game.</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                      <button type="submit" class="btn btn-warning">
                        <i class="ti ti-tools me-1"></i>
                        Update Profile (Under Maintenance)
                      </button>
                      <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                        <i class="ti ti-refresh me-1"></i>
                        Reset
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Bowling Statistics -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title mb-4">
                    <i class="ti ti-chart-bar me-2"></i>
                    Bowling Statistics
                  </h5>
                  
                  <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-primary fw-bold">187</div>
                        <small class="text-muted">Average Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-success fw-bold">245</div>
                        <small class="text-muted">Best Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-warning fw-bold">156</div>
                        <small class="text-muted">Games Played</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-info fw-bold">78%</div>
                        <small class="text-muted">Strike Rate</small>
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

  <!-- Profile Management Script -->
  <script>
    // Handle profile image upload
    function handleImageUpload(event) {
      // Show maintenance notice instead of processing upload
      showMaintenanceNotice();
    }

    // Handle form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Show maintenance notice
      showMaintenanceNotice();
    });

    // Reset form
    function resetForm() {
      document.getElementById('profileForm').reset();
      showNotification('Form reset to original values', 'info');
    }

    // Drag and drop functionality
    const uploadArea = document.querySelector('.profile-upload-area');
    
    uploadArea.addEventListener('dragover', function(e) {
      e.preventDefault();
      this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
      e.preventDefault();
      this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('dragover');
      
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        const file = files[0];
        const event = { target: { files: [file] } };
        handleImageUpload(event);
      }
    });

    // Maintenance notice function
    function showMaintenanceNotice() {
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
              <h5 class="modal-title">
                <i class="ti ti-tools me-2"></i>
                Profile Update Under Maintenance
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-4">
                <i class="ti ti-settings fs-1 text-warning mb-3"></i>
                <h6 class="fw-bold">Profile Management System Upgrade</h6>
              </div>
              <p class="text-muted mb-3">
                We're currently upgrading our profile management system to provide you with better features and enhanced security.
              </p>
              <div class="alert alert-info">
                <h6 class="alert-heading">
                  <i class="ti ti-info-circle me-1"></i>
                  What's Coming:
                </h6>
                <ul class="mb-0 small">
                  <li>Enhanced profile picture upload with cloud storage</li>
                  <li>Advanced privacy settings</li>
                  <li>Social media integration</li>
                  <li>Profile verification system</li>
                  <li>Real-time statistics updates</li>
                </ul>
              </div>
              <p class="text-muted small mb-0">
                <strong>Estimated completion:</strong> March 2025<br>
                Your current profile information remains safe and accessible.
              </p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="ti ti-arrow-left me-1"></i>
                Go Back
              </button>
              <a href="./dashboard.php" class="btn btn-primary">
                <i class="ti ti-dashboard me-1"></i>
                Go to Dashboard
              </a>
            </div>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      
      // Show modal
      const bsModal = new bootstrap.Modal(modal);
      bsModal.show();
      
      // Remove modal from DOM when hidden
      modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
      });
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
</body>

</html>
