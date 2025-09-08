<?php
// Header component for SPEEDSTERS Bowling System
// This ensures consistent header behavior across all pages
?>

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
            <?php if (isset($currentUser)): ?>
              <span class="ms-2 text-dark">
                <?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?>
                <?php if (strpos($_SERVER['PHP_SELF'], 'admin-') !== false): ?>
                  (Admin)
                <?php endif; ?>
              </span>
            <?php endif; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <div class="message-body">
              <a href="./my-profile.php" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-user fs-6"></i>
                <p class="mb-0 fs-3">My Profile</p>
              </a>
              <a href="./dashboard.php" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-eye fs-6"></i>
                <p class="mb-0 fs-3">Player View</p>
              </a>
              <a href="./admin-dashboard.php" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-settings fs-6"></i>
                <p class="mb-0 fs-3">Admin Panel</p>
              </a>
              <a href="logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>
<!--  Header End -->
