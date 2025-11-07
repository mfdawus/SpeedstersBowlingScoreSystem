<?php
require_once 'includes/auth.php';
require_once 'includes/profile-picture-helper.php';
require_once 'includes/user-management.php';

// Get current user data
if (!isset($_SESSION['user_id'])) {
    header('Location: authentication-login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userProfilePicture = getUserProfilePicture($userId);

// Fetch complete user data
$userData = getUserById($userId);
if (!$userData || isset($userData['error'])) {
    // Fallback to session data if database fetch fails
    $userData = [
        'username' => $_SESSION['username'] ?? 'Unknown',
        'first_name' => $_SESSION['first_name'] ?? 'User',
        'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'] ?? 'no-email@example.com',
        'phone' => '',
        'skill_level' => 'Beginner',
        'user_role' => $_SESSION['user_role'] ?? 'Player',
        'team_name' => '',
        'total_games' => 0,
        'best_score' => 0,
        'avg_score' => 0,
        'created_at' => date('Y-m-d')
    ];
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile - VIPERS VENOMS Bowling System</title>
  <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
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
          <h6 class="mb-0 fw-bold text-white">VIPERS VENOMS Championship 2025</h6>
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
            <img src="assets/images/logos/vipersvenoms-main-logo.png" alt="VIPERS VENOMS Logo" width="90" />
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
                  <img src="<?php echo htmlspecialchars($userProfilePicture); ?>" alt="Profile Picture" width="35" height="35" class="rounded-circle" style="object-fit: cover;">
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
                    <img src="<?php echo htmlspecialchars($userProfilePicture); ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
                  </div>
                  
                  <h5 class="fw-bold mb-1" id="userNickname"><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></h5>
                  <p class="text-muted mb-3"><?php echo htmlspecialchars($userData['user_role']); ?></p>
                  
                  <!-- Profile Picture Upload -->
                  <div class="profile-upload-area" onclick="document.getElementById('profileImageInput').click()">
                    <i class="ti ti-cloud-upload fs-1 text-primary mb-3"></i>
                    <h6 class="mb-2">Upload Profile Picture</h6>
                    <p class="text-muted small mb-0">Click or drag image here</p>
                    <p class="text-muted small">Max 5MB (JPG, PNG, GIF, WEBP)</p>
                  </div>
                  
                  <input type="file" id="profileImageInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" style="display: none;" onchange="handleImageUpload(event)">
                  
                  <div class="mt-3">
                    <button class="btn btn-primary btn-sm" onclick="document.getElementById('profileImageInput').click()">
                      <i class="ti ti-upload me-1"></i>
                      Upload New Photo
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Profile Information Section -->
            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <!-- Success/Info Banner (will be shown dynamically) -->
                  <div class="alert alert-info alert-dismissible fade show mb-4" role="alert" id="profileInfoBanner" style="display: none;">
                    <div class="d-flex align-items-center">
                      <i class="ti ti-info-circle fs-4 me-3"></i>
                      <div id="profileInfoMessage">
                        <h6 class="alert-heading mb-1">Profile Information</h6>
                        <p class="mb-0 small">Update your profile details below.</p>
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
                        <label for="nickname" class="form-label">Username</label>
                        <input type="text" class="form-control" id="nickname" name="nickname" value="<?php echo htmlspecialchars($userData['username']); ?>" placeholder="Enter your username" readonly>
                        <div class="form-text">This is how other players will see you in the system.</div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                        <div class="form-text">Email cannot be changed. Contact support if needed.</div>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" placeholder="Enter your phone number" readonly>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Skill Level</label>
                        <div class="form-control-plaintext">
                          <span class="badge bg-info fs-6"><?php echo htmlspecialchars($userData['skill_level']); ?></span>
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
                        <label for="teamPreference" class="form-label">Team Name</label>
                        <input type="text" class="form-control" id="teamPreference" name="teamPreference" value="<?php echo htmlspecialchars($userData['team_name'] ?? 'No Team'); ?>" readonly>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label for="bio" class="form-label">Bio</label>
                      <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us about yourself..." disabled>Member since <?php echo date('F Y', strtotime($userData['created_at'])); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                      <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Save Changes
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
                        <div class="display-6 text-primary fw-bold"><?php echo number_format($userData['avg_score'] ?? 0, 1); ?></div>
                        <small class="text-muted">Average Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-success fw-bold"><?php echo $userData['best_score'] ?? 0; ?></div>
                        <small class="text-muted">Best Score</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-warning fw-bold"><?php echo $userData['total_games'] ?? 0; ?></div>
                        <small class="text-muted">Games Played</small>
                      </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                      <div class="text-center p-3 bg-light rounded">
                        <div class="display-6 text-info fw-bold">
                          <?php 
                            $strikeRate = 0;
                            if (isset($userData['total_games']) && $userData['total_games'] > 0) {
                              // Approximate: A decent player gets ~2-3 strikes per game
                              $estimatedStrikes = ($userData['avg_score'] ?? 0) > 150 ? 30 : 20;
                              $strikeRate = min(100, $estimatedStrikes);
                            }
                            echo $strikeRate . '%';
                          ?>
                        </div>
                        <small class="text-muted">Skill Percentage</small>
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
      const file = event.target.files[0];
      
      if (!file) {
        return;
      }
      
      // Validate file type
      const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
      if (!validTypes.includes(file.type)) {
        showNotification('Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.', 'error');
        return;
      }
      
      // Validate file size (5MB max)
      const maxSize = 5 * 1024 * 1024; // 5MB
      if (file.size > maxSize) {
        showNotification('File size too large. Maximum size is 5MB.', 'error');
        return;
      }
      
      // Show loading state
      showNotification('Uploading profile picture...', 'info');
      
      // Create FormData and upload
      const formData = new FormData();
      formData.append('profile_picture', file);
      
      fetch('./ajax/upload-profile-picture.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update profile picture preview
          const profilePicture = document.getElementById('profilePicture');
          profilePicture.src = data.url + '?t=' + new Date().getTime(); // Cache buster
          
          // Update header profile picture if it exists
          const headerProfilePics = document.querySelectorAll('img[src*="profile"]');
          headerProfilePics.forEach(img => {
            if (img !== profilePicture) {
              img.src = data.url + '?t=' + new Date().getTime();
            }
          });
          
          showNotification(data.message, 'success');
        } else {
          showNotification(data.message || 'Failed to upload profile picture', 'error');
        }
      })
      .catch(error => {
        console.error('Upload error:', error);
        showNotification('An error occurred while uploading. Please try again.', 'error');
      })
      .finally(() => {
        // Reset file input
        event.target.value = '';
      });
    }

    // Handle form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Note: Profile form submission functionality can be added here
      showNotification('Profile update functionality coming soon!', 'info');
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
        
        // Trigger file input change event with the dropped file
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        const fileInput = document.getElementById('profileImageInput');
        fileInput.files = dataTransfer.files;
        
        // Trigger the upload
        const event = new Event('change', { bubbles: true });
        fileInput.dispatchEvent(event);
      }
    });

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
