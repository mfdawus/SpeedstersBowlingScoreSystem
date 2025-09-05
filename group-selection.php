<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Group Selection - Speedsters Bowling</title>
  <link rel="icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link href="./assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="./assets/css/styles.min.css" rel="stylesheet" />
  <link href="./assets/css/icons/tabler-icons/tabler-icons.css" rel="stylesheet" />
  <style>
    .group-card {
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .group-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .group-card.selected {
      border: 2px solid #007bff;
      background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    }
    .skill-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .player-count {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .group-stats {
      background: rgba(0,123,255,0.1);
      border-radius: 8px;
      padding: 1rem;
    }
    .join-button {
      width: 100%;
      margin-top: 1rem;
    }
    .filter-section {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 2rem;
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
    }
    
    /* Group Logo Styles */
    .group-logo {
      position: relative;
    }
    
    .logo-circle {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
      position: relative;
      overflow: hidden;
    }
    
    .logo-circle::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      animation: logoShine 3s infinite;
    }
    
    .logo-circle i {
      font-size: 24px;
      color: white;
      z-index: 1;
      position: relative;
    }
    
    @keyframes logoShine {
      0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
      50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
      100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    }
    
    .logo-circle:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
      transition: all 0.3s ease;
    }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" style="margin-top: 0; padding-top: 0;">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
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
              <a class="sidebar-link active" href="./group-selection.php" aria-expanded="false">
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
                    <span class="hide-menu">Team (4-6 Players)</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    <!-- Sidebar End -->

    <!-- Main wrapper -->
    <div class="body-wrapper">
      <!-- Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a>
                    <a href="./authentication-login.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Header End -->

      <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <div class="d-flex align-items-center mb-2">
                  <div class="group-logo me-3">
                    <div class="logo-circle">
                      <i class="ti ti-users-group"></i>
                    </div>
                  </div>
                  <div>
                    <h4 class="fw-semibold mb-1">Join a Bowling Group</h4>
                    <span class="fw-normal text-muted">Choose a group that matches your skill level and preferences</span>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshGroups()">
                  <i class="ti ti-refresh me-1"></i>
                  Refresh
                </button>
                <button class="btn btn-primary" onclick="requestRandomGroup()">
                  <i class="ti ti-dice me-1"></i>
                  Random Group
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="search-box">
                <i class="ti ti-search"></i>
                <input type="text" class="form-control" id="searchGroups" placeholder="Search groups..." onkeyup="filterGroups()">
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <select class="form-select" id="skillFilter" onchange="filterGroups()">
                <option value="">All Skill Levels</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="pro">Professional</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <select class="form-select" id="availabilityFilter" onchange="filterGroups()">
                <option value="">All Availability</option>
                <option value="available">Available Spots</option>
                <option value="full">Full Groups</option>
              </select>
            </div>
            <div class="col-md-2 mb-3">
              <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                <i class="ti ti-x me-1"></i>
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Groups Grid -->
        <div class="row" id="groupsContainer">
          <!-- Groups will be dynamically populated here -->
          <div class="col-12 text-center" id="loadingMessage">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading groups...</p>
          </div>
        </div>

        <!-- Selected Group Summary -->
        <div class="row mt-4" id="selectedGroupSummary" style="display: none;">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="ti ti-check-circle me-2"></i>Selected Group</h5>
              </div>
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h6 class="mb-1" id="selectedGroupName">Group Name</h6>
                    <p class="text-muted mb-2" id="selectedGroupDescription">Group description</p>
                    <div class="d-flex gap-3">
                      <span class="badge bg-primary" id="selectedGroupSkill">Skill Level</span>
                      <span class="text-muted">
                        <i class="ti ti-users me-1"></i>
                        <span id="selectedGroupCount">0</span>/<span id="selectedGroupMax">0</span> players
                      </span>
                      <span class="text-muted">
                        <i class="ti ti-target me-1"></i>
                        Avg: <span id="selectedGroupAvg">0</span>
                      </span>
                    </div>
                  </div>
                  <div class="col-md-4 text-end">
                    <button class="btn btn-success btn-lg" onclick="joinSelectedGroup()">
                      <i class="ti ti-user-plus me-2"></i>
                      Join Group
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

  <!-- Random Group Assignment Modal -->
  <div class="modal fade" id="randomGroupModal" tabindex="-1" aria-labelledby="randomGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="randomGroupModalLabel">
            <i class="ti ti-dice me-2"></i>
            Random Group Assignment
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="mb-4">
            <i class="ti ti-dice fs-1 text-primary"></i>
          </div>
          <h6 class="mb-3">You've been assigned to:</h6>
          <div class="card border-primary">
            <div class="card-body">
              <h5 class="text-primary mb-2" id="randomGroupName">Group Name</h5>
              <p class="text-muted mb-2" id="randomGroupDesc">Group description</p>
              <div class="d-flex justify-content-center gap-3">
                <span class="badge bg-primary" id="randomGroupSkill">Skill Level</span>
                <span class="text-muted">
                  <i class="ti ti-users me-1"></i>
                  <span id="randomGroupCount">0</span>/<span id="randomGroupMax">0</span> players
                </span>
              </div>
            </div>
          </div>
          <p class="text-muted mt-3">This group was randomly selected based on available spots and your skill level.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="confirmRandomGroup()">
            <i class="ti ti-check me-1"></i>
            Join This Group
          </button>
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
    // Sample groups data (in real app, this would come from database)
    const availableGroups = [
      { id: 1, name: 'Beginner Buddies', skillLevel: 'beginner', avgScore: 120, playerCount: 8, maxPlayers: 12, description: 'Perfect for new players learning the basics', available: true },
      { id: 2, name: 'Casual Rollers', skillLevel: 'beginner', avgScore: 135, playerCount: 6, maxPlayers: 12, description: 'Relaxed group for casual bowling fun', available: true },
      { id: 3, name: 'Mid-Level Masters', skillLevel: 'intermediate', avgScore: 175, playerCount: 10, maxPlayers: 12, description: 'Intermediate players looking to improve', available: true },
      { id: 4, name: 'Balanced Bowlers', skillLevel: 'intermediate', avgScore: 185, playerCount: 7, maxPlayers: 12, description: 'Mixed skill levels for balanced competition', available: true },
      { id: 5, name: 'Advanced Alliance', skillLevel: 'advanced', avgScore: 220, playerCount: 9, maxPlayers: 12, description: 'Advanced players seeking challenging games', available: true },
      { id: 6, name: 'Pro League', skillLevel: 'pro', avgScore: 265, playerCount: 5, maxPlayers: 12, description: 'Professional level competitive bowling', available: true },
      { id: 7, name: 'Weekend Warriors', skillLevel: 'intermediate', avgScore: 160, playerCount: 12, maxPlayers: 12, description: 'Full group - weekend bowling enthusiasts', available: false },
      { id: 8, name: 'Night Owls', skillLevel: 'advanced', avgScore: 210, playerCount: 11, maxPlayers: 12, description: 'Late night bowling sessions', available: true }
    ];

    let selectedGroupId = null;
    let filteredGroups = [...availableGroups];

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Page loaded, populating groups...');
      populateGroups();
      console.log('Groups populated:', availableGroups.length);
    });

    function populateGroups() {
      const container = document.getElementById('groupsContainer');
      const loadingMessage = document.getElementById('loadingMessage');
      console.log('Container found:', container);
      
      // Hide loading message
      if (loadingMessage) {
        loadingMessage.style.display = 'none';
      }
      
      container.innerHTML = '';
      console.log('Filtered groups:', filteredGroups.length);

      filteredGroups.forEach(group => {
        const groupCard = document.createElement('div');
        groupCard.className = 'col-md-6 col-lg-4 mb-4';
        groupCard.innerHTML = `
          <div class="card group-card h-100" onclick="selectGroup(${group.id})" data-group-id="${group.id}">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="mb-0">${group.name}</h6>
              <span class="badge ${getSkillBadgeClass(group.skillLevel)} skill-badge">
                ${group.skillLevel.charAt(0).toUpperCase() + group.skillLevel.slice(1)}
              </span>
            </div>
            <div class="card-body">
              <p class="text-muted mb-3">${group.description}</p>
              
              <div class="group-stats mb-3">
                <div class="row text-center">
                  <div class="col-4">
                    <div class="player-count text-primary">${group.playerCount}</div>
                    <small class="text-muted">Players</small>
                  </div>
                  <div class="col-4">
                    <div class="player-count text-success">${group.avgScore}</div>
                    <small class="text-muted">Avg Score</small>
                  </div>
                  <div class="col-4">
                    <div class="player-count text-warning">${group.maxPlayers}</div>
                    <small class="text-muted">Max</small>
                  </div>
                </div>
              </div>
              
              <div class="progress mb-2" style="height: 8px;">
                <div class="progress-bar ${getProgressBarClass(group.playerCount, group.maxPlayers)}" 
                     style="width: ${(group.playerCount / group.maxPlayers) * 100}%"></div>
              </div>
              
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">${group.playerCount}/${group.maxPlayers} players</small>
                ${group.available ? 
                  '<span class="badge bg-success">Available</span>' : 
                  '<span class="badge bg-danger">Full</span>'
                }
              </div>
              
              <button class="btn btn-primary join-button" ${!group.available ? 'disabled' : ''} onclick="event.stopPropagation(); joinGroup(${group.id})">
                <i class="ti ti-user-plus me-1"></i>
                ${group.available ? 'Join Group' : 'Group Full'}
              </button>
            </div>
          </div>
        `;
        container.appendChild(groupCard);
      });
    }

    function selectGroup(groupId) {
      // Remove previous selection
      document.querySelectorAll('.group-card').forEach(card => {
        card.classList.remove('selected');
      });
      
      // Add selection to clicked card
      const selectedCard = document.querySelector(`[data-group-id="${groupId}"]`);
      if (selectedCard) {
        selectedCard.classList.add('selected');
        selectedGroupId = groupId;
        showSelectedGroupSummary(groupId);
      }
    }

    function showSelectedGroupSummary(groupId) {
      const group = availableGroups.find(g => g.id === groupId);
      if (group) {
        document.getElementById('selectedGroupName').textContent = group.name;
        document.getElementById('selectedGroupDescription').textContent = group.description;
        document.getElementById('selectedGroupSkill').textContent = group.skillLevel.charAt(0).toUpperCase() + group.skillLevel.slice(1);
        document.getElementById('selectedGroupCount').textContent = group.playerCount;
        document.getElementById('selectedGroupMax').textContent = group.maxPlayers;
        document.getElementById('selectedGroupAvg').textContent = group.avgScore;
        
        document.getElementById('selectedGroupSummary').style.display = 'block';
      }
    }

    function joinGroup(groupId) {
      const group = availableGroups.find(g => g.id === groupId);
      if (group && group.available) {
        showNotification(`Successfully joined ${group.name}!`, 'success');
        // Update group count
        group.playerCount++;
        if (group.playerCount >= group.maxPlayers) {
          group.available = false;
        }
        populateGroups();
        document.getElementById('selectedGroupSummary').style.display = 'none';
      }
    }

    function joinSelectedGroup() {
      if (selectedGroupId) {
        joinGroup(selectedGroupId);
      }
    }

    function requestRandomGroup() {
      const availableGroupsOnly = availableGroups.filter(g => g.available);
      if (availableGroupsOnly.length === 0) {
        showNotification('No available groups at the moment', 'warning');
        return;
      }
      
      const randomGroup = availableGroupsOnly[Math.floor(Math.random() * availableGroupsOnly.length)];
      
      // Populate modal
      document.getElementById('randomGroupName').textContent = randomGroup.name;
      document.getElementById('randomGroupDesc').textContent = randomGroup.description;
      document.getElementById('randomGroupSkill').textContent = randomGroup.skillLevel.charAt(0).toUpperCase() + randomGroup.skillLevel.slice(1);
      document.getElementById('randomGroupCount').textContent = randomGroup.playerCount;
      document.getElementById('randomGroupMax').textContent = randomGroup.maxPlayers;
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('randomGroupModal'));
      modal.show();
    }

    function confirmRandomGroup() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('randomGroupModal'));
      modal.hide();
      
      const randomGroupName = document.getElementById('randomGroupName').textContent;
      showNotification(`Successfully joined ${randomGroupName}!`, 'success');
    }

    function filterGroups() {
      const searchTerm = document.getElementById('searchGroups').value.toLowerCase();
      const skillFilter = document.getElementById('skillFilter').value;
      const availabilityFilter = document.getElementById('availabilityFilter').value;
      
      filteredGroups = availableGroups.filter(group => {
        const matchesSearch = group.name.toLowerCase().includes(searchTerm) || 
                             group.description.toLowerCase().includes(searchTerm);
        const matchesSkill = !skillFilter || group.skillLevel === skillFilter;
        const matchesAvailability = !availabilityFilter || 
          (availabilityFilter === 'available' && group.available) ||
          (availabilityFilter === 'full' && !group.available);
        
        return matchesSearch && matchesSkill && matchesAvailability;
      });
      
      populateGroups();
    }

    function clearFilters() {
      document.getElementById('searchGroups').value = '';
      document.getElementById('skillFilter').value = '';
      document.getElementById('availabilityFilter').value = '';
      filteredGroups = [...availableGroups];
      populateGroups();
    }

    function refreshGroups() {
      showNotification('Groups refreshed successfully!', 'info');
      clearFilters();
    }

    function getSkillBadgeClass(skillLevel) {
      const classes = {
        'beginner': 'bg-primary',
        'intermediate': 'bg-success',
        'advanced': 'bg-warning',
        'pro': 'bg-danger'
      };
      return classes[skillLevel] || 'bg-secondary';
    }

    function getProgressBarClass(playerCount, maxPlayers) {
      const percentage = (playerCount / maxPlayers) * 100;
      if (percentage >= 90) return 'bg-danger';
      if (percentage >= 75) return 'bg-warning';
      return 'bg-success';
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
