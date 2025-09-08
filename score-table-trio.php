<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('trio', 'Trio Score Table');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trio Score Table - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .bg-gradient-primary {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }
    .rank-badge {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
    }
    .rank-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); }
    .rank-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%); }
    .rank-3 { background: linear-gradient(135deg, #CD7F32 0%, #B8860B 100%); }
    .rank-other { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
    .player-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
    .team-avatars {
      display: flex;
      align-items: center;
    }
    .team-avatars img:first-child {
      margin-right: -10px;
      border: 2px solid white;
    }
    .team-avatars img:nth-child(2) {
      margin-right: -10px;
      border: 2px solid white;
    }
    .score-highlight {
      font-weight: bold;
      font-size: 1.1rem;
    }
    .score-excellent { color: #28a745; }
    .score-good { color: #17a2b8; }
    .score-average { color: #ffc107; }
    .score-below { color: #dc3545; }
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
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Table</a></li>
                    <li class="breadcrumb-item active">Trio</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Page Content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                      <h5 class="card-title fw-semibold mb-1">Trio Score Table</h5>
                      <span class="fw-normal text-muted">Three-player team rankings and scores</span>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="all">All Time</option>
                      </select>
                      <button class="btn btn-primary btn-sm">
                        <i class="ti ti-download me-1"></i>
                        Export
                      </button>
                    </div>
                  </div>

                  <!-- Score Table -->
                  <div class="table-responsive">
                    <table class="table table-hover align-middle">
                      <thead class="table-light">
                        <tr>
                          <th style="width: 60px;">Rank</th>
                          <th style="width: 200px;">Team</th>
                          <th style="width: 100px;">Games</th>
                          <th style="width: 120px;">Total Score</th>
                          <th style="width: 120px;">Average</th>
                          <th style="width: 120px;">Best Game</th>
                          <th style="width: 100px;">Win Rate</th>
                          <th style="width: 100px;">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Sample Data - Replace with actual data from database -->
                        <tr>
                          <td>
                            <div class="rank-badge rank-1">1</div>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="team-avatars me-3">
                                <img src="./assets/images/profile/user-1.jpg" alt="Player 1" class="player-avatar">
                                <img src="./assets/images/profile/user-2.jpg" alt="Player 2" class="player-avatar">
                                <img src="./assets/images/profile/user-3.jpg" alt="Player 3" class="player-avatar">
                              </div>
                              <div>
                                <h6 class="mb-0 fw-semibold">Thunder Trio</h6>
                                <small class="text-muted">Alex, Sarah, Mike</small>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="fw-semibold">24</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">5,847</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">243.6</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">298</span>
                          </td>
                          <td>
                            <span class="badge bg-success">87.5%</span>
                          </td>
                          <td>
                            <span class="badge bg-success">Active</span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="rank-badge rank-2">2</div>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="team-avatars me-3">
                                <img src="./assets/images/profile/user-4.jpg" alt="Player 1" class="player-avatar">
                                <img src="./assets/images/profile/user-5.jpg" alt="Player 2" class="player-avatar">
                                <img src="./assets/images/profile/user-6.jpg" alt="Player 3" class="player-avatar">
                              </div>
                              <div>
                                <h6 class="mb-0 fw-semibold">Strike Squad</h6>
                                <small class="text-muted">Emma, David, Lisa</small>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="fw-semibold">22</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">5,432</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">247.0</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">289</span>
                          </td>
                          <td>
                            <span class="badge bg-success">81.8%</span>
                          </td>
                          <td>
                            <span class="badge bg-success">Active</span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="rank-badge rank-3">3</div>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="team-avatars me-3">
                                <img src="./assets/images/profile/user-7.jpg" alt="Player 1" class="player-avatar">
                                <img src="./assets/images/profile/user-8.jpg" alt="Player 2" class="player-avatar">
                                <img src="./assets/images/profile/user-1.jpg" alt="Player 3" class="player-avatar">
                              </div>
                              <div>
                                <h6 class="mb-0 fw-semibold">Pin Crushers</h6>
                                <small class="text-muted">Tom, Jessica, Ryan</small>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="fw-semibold">20</span>
                          </td>
                          <td>
                            <span class="score-highlight score-good">4,856</span>
                          </td>
                          <td>
                            <span class="score-highlight score-good">242.8</span>
                          </td>
                          <td>
                            <span class="score-highlight score-excellent">275</span>
                          </td>
                          <td>
                            <span class="badge bg-info">75.0%</span>
                          </td>
                          <td>
                            <span class="badge bg-success">Active</span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="rank-badge rank-other">4</div>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="team-avatars me-3">
                                <img src="./assets/images/profile/user-2.jpg" alt="Player 1" class="player-avatar">
                                <img src="./assets/images/profile/user-3.jpg" alt="Player 2" class="player-avatar">
                                <img src="./assets/images/profile/user-4.jpg" alt="Player 3" class="player-avatar">
                              </div>
                              <div>
                                <h6 class="mb-0 fw-semibold">Lane Legends</h6>
                                <small class="text-muted">Chris, Maria, Kevin</small>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="fw-semibold">18</span>
                          </td>
                          <td>
                            <span class="score-highlight score-good">4,234</span>
                          </td>
                          <td>
                            <span class="score-highlight score-average">235.2</span>
                          </td>
                          <td>
                            <span class="score-highlight score-good">268</span>
                          </td>
                          <td>
                            <span class="badge bg-warning">66.7%</span>
                          </td>
                          <td>
                            <span class="badge bg-success">Active</span>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="rank-badge rank-other">5</div>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <div class="team-avatars me-3">
                                <img src="./assets/images/profile/user-5.jpg" alt="Player 1" class="player-avatar">
                                <img src="./assets/images/profile/user-6.jpg" alt="Player 2" class="player-avatar">
                                <img src="./assets/images/profile/user-7.jpg" alt="Player 3" class="player-avatar">
                              </div>
                              <div>
                                <h6 class="mb-0 fw-semibold">Spare Masters</h6>
                                <small class="text-muted">Anna, James, Nicole</small>
                              </div>
                            </div>
                          </td>
                          <td>
                            <span class="fw-semibold">16</span>
                          </td>
                          <td>
                            <span class="score-highlight score-average">3,892</span>
                          </td>
                          <td>
                            <span class="score-highlight score-average">243.3</span>
                          </td>
                          <td>
                            <span class="score-highlight score-good">256</span>
                          </td>
                          <td>
                            <span class="badge bg-warning">62.5%</span>
                          </td>
                          <td>
                            <span class="badge bg-success">Active</span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Pagination -->
                  <div class="d-flex align-items-center justify-content-between mt-4">
                    <div class="text-muted">
                      Showing 1 to 5 of 5 entries
                    </div>
                    <nav aria-label="Trio score table pagination">
                      <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled">
                          <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active">
                          <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item disabled">
                          <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                        </li>
                      </ul>
                    </nav>
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
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Date filter functionality
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
      console.log('Filtering by:', selectedDate);
      // Add your filtering logic here
    });

    // Export functionality
    document.querySelector('.btn-primary').addEventListener('click', function() {
      console.log('Exporting trio scores...');
      // Add your export logic here
    });
  </script>
</body>

</html>
