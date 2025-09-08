<?php
// Sidebar component for SPEEDSTERS Bowling System
// This ensures consistent sidebar behavior across all pages

// Get current page for active state highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Check if this is an admin page
$isAdminPage = strpos($currentPage, 'admin-') === 0;
?>

<style>
/* Increase sidebar height to show all navigation items */
.left-sidebar {
  height: 100vh !important;
  min-height: 100vh !important;
}

.sidebar-nav {
  height: calc(100vh - 120px) !important;
  min-height: calc(100vh - 120px) !important;
}

.scroll-sidebar {
  height: 100% !important;
  max-height: none !important;
}
</style>

<!-- Sidebar Start -->
<aside class="left-sidebar">
  <!-- Sidebar scroll-->
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="<?php echo $isAdminPage ? './admin-dashboard.php' : './homepage.php'; ?>" class="text-nowrap logo-img d-flex flex-column align-items-start text-decoration-none">
        <img src="assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="90" />
        <span class="text-muted fw-semibold mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo $isAdminPage ? 'Admin Panel' : 'Bowling Score System'; ?></span>
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-6"></i>
      </div>
    </div>
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <?php if ($isAdminPage): ?>
          <!-- Admin Navigation -->
          <li class="nav-small-cap">
            <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
            <span class="hide-menu">Admin Panel</span>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'admin-dashboard.php') ? 'active' : ''; ?>" href="./admin-dashboard.php" aria-expanded="false">
              <i class="ti ti-dashboard"></i>
              <span class="hide-menu">Dashboard</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'admin-user-management.php') ? 'active' : ''; ?>" href="./admin-user-management.php" aria-expanded="false">
              <i class="ti ti-users"></i>
              <span class="hide-menu">User Management</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'admin-events.php') ? 'active' : ''; ?>" href="./admin-events.php" aria-expanded="false">
              <i class="ti ti-calendar-event"></i>
              <span class="hide-menu">Events Management</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link has-arrow <?php echo (in_array($currentPage, ['admin-score-monitoring-solo.php', 'admin-score-monitoring-doubles.php', 'admin-score-monitoring-team.php'])) ? 'active' : ''; ?>" href="javascript:void(0)" aria-expanded="false">
              <i class="ti ti-chart-bar"></i>
              <span class="hide-menu">Score Monitoring</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
              <li class="sidebar-item">
                <a href="./admin-score-monitoring-solo.php" class="sidebar-link <?php echo ($currentPage == 'admin-score-monitoring-solo.php') ? 'active' : ''; ?>">
                  <i class="ti ti-user"></i>
                  <span class="hide-menu">Solo Players</span>
                </a>
              </li>
              <li class="sidebar-item">
                <a href="./admin-score-monitoring-doubles.php" class="sidebar-link <?php echo ($currentPage == 'admin-score-monitoring-doubles.php') ? 'active' : ''; ?>">
                  <i class="ti ti-users"></i>
                  <span class="hide-menu">Doubles Teams</span>
                </a>
              </li>
                     <li class="sidebar-item">
                       <a href="./admin-score-monitoring-trio.php" class="sidebar-link <?php echo ($currentPage == 'admin-score-monitoring-trio.php') ? 'active' : ''; ?>">
                         <i class="ti ti-users"></i>
                         <span class="hide-menu">Trio Teams</span>
                       </a>
                     </li>
                     <li class="sidebar-item">
                       <a href="./admin-score-monitoring-team.php" class="sidebar-link <?php echo ($currentPage == 'admin-score-monitoring-team.php') ? 'active' : ''; ?>">
                         <i class="ti ti-users-group"></i>
                         <span class="hide-menu">Team</span>
                       </a>
                     </li>
            </ul>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link" href="./dashboard.php" aria-expanded="false">
              <i class="ti ti-eye"></i>
              <span class="hide-menu">Player View</span>
            </a>
          </li>
        <?php else: ?>
          <!-- Player Navigation -->
          <li class="nav-small-cap">
            <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
            <span class="hide-menu">Home</span>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>" href="./dashboard.php" aria-expanded="false">
              <i class="ti ti-atom"></i>
              <span class="hide-menu">Dashboard</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'lane-booking.php') ? 'active' : ''; ?>" href="./lane-booking.php" aria-expanded="false">
              <i class="ti ti-calendar-plus"></i>
              <span class="hide-menu">Lane Booking</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'group-selection.php') ? 'active' : ''; ?>" href="./group-selection.php" aria-expanded="false">
              <i class="ti ti-users"></i>
              <span class="hide-menu">Join Group</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link <?php echo ($currentPage == 'events.php') ? 'active' : ''; ?>" href="./events.php" aria-expanded="false">
              <i class="ti ti-calendar-event"></i>
              <span class="hide-menu">Events</span>
            </a>
          </li>
          <li class="sidebar-item">
            <a class="sidebar-link has-arrow <?php echo (in_array($currentPage, ['score-table-solo.php', 'score-table-doubles.php', 'score-table-team.php', 'score-table-trio.php'])) ? 'active' : ''; ?>" href="javascript:void(0)" aria-expanded="false">
              <i class="ti ti-table"></i>
              <span class="hide-menu">Score Table</span>
            </a>
            <ul aria-expanded="false" class="collapse first-level">
              <li class="sidebar-item">
                <a href="./score-table-solo.php" class="sidebar-link <?php echo ($currentPage == 'score-table-solo.php') ? 'active' : ''; ?>">
                  <i class="ti ti-user"></i>
                  <span class="hide-menu">Solo Games</span>
                </a>
              </li>
              <li class="sidebar-item">
                <a href="./score-table-doubles.php" class="sidebar-link <?php echo ($currentPage == 'score-table-doubles.php') ? 'active' : ''; ?>">
                  <i class="ti ti-users"></i>
                  <span class="hide-menu">Doubles</span>
                </a>
              </li>
              <li class="sidebar-item">
                <a href="./score-table-trio.php" class="sidebar-link <?php echo ($currentPage == 'score-table-trio.php') ? 'active' : ''; ?>">
                  <i class="ti ti-users"></i>
                  <span class="hide-menu">Trio</span>
                </a>
              </li>
              <li class="sidebar-item">
                <a href="./score-table-team.php" class="sidebar-link <?php echo ($currentPage == 'score-table-team.php') ? 'active' : ''; ?>">
                  <i class="ti ti-users-group"></i>
                  <span class="hide-menu">Team</span>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
    <!-- End Sidebar navigation -->
  </div>
  <!-- End Sidebar scroll-->
</aside>
<!--  Sidebar End -->
