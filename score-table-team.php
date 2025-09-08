<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('team', 'Team Score Table');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Team Score Table - SPEEDSTERS Bowling System</title>
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
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
    }
    .team-avatars {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
    }
    .team-avatars img {
      margin-right: -8px;
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
    .player-details-row {
      background-color: #f8f9fa;
    }
    .player-details-row .card {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-radius: 8px;
    }
    .player-details-row .table-sm th,
    .player-details-row .table-sm td {
      padding: 0.5rem;
      font-size: 0.875rem;
    }
    .btn-outline-primary:hover {
      transform: translateY(-1px);
      transition: transform 0.2s ease;
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
        <div class="container-fluid">
          <!-- Page Header -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="./index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Score Table</a></li>
                    <li class="breadcrumb-item active">Team (4-6 Players)</li>
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
                      <h5 class="card-title fw-semibold mb-1">Team Score Table (4-6 Players)</h5>
                      <span class="fw-normal text-muted">Multi-player team rankings and scores</span>
                    </div>
                    <div class="d-flex gap-2">
                      <select class="form-select form-select-sm" id="dateFilter" style="width: auto;">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="all">All Time</option>
                        <option value="custom">Custom Date</option>
                      </select>
                      <input type="date" class="form-control form-control-sm" id="customDate" style="width: auto; display: none;">
                      <button class="btn btn-primary btn-sm" onclick="refreshTable()">
                        <i class="ti ti-refresh"></i>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Game Selection Tabs -->
                  <ul class="nav nav-tabs mb-3" id="gameTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="overall-tab" data-bs-toggle="tab" data-bs-target="#overall" type="button" role="tab">
                        Overall
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="teams4-tab" data-bs-toggle="tab" data-bs-target="#teams4" type="button" role="tab">
                        4-Player Teams
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="teams5-tab" data-bs-toggle="tab" data-bs-target="#teams5" type="button" role="tab">
                        5-Player Teams
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="teams6-tab" data-bs-toggle="tab" data-bs-target="#teams6" type="button" role="tab">
                        6-Player Teams
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game1-tab" data-bs-toggle="tab" data-bs-target="#game1" type="button" role="tab">
                        Game 1
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game2-tab" data-bs-toggle="tab" data-bs-target="#game2" type="button" role="tab">
                        Game 2
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game3-tab" data-bs-toggle="tab" data-bs-target="#game3" type="button" role="tab">
                        Game 3
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game4-tab" data-bs-toggle="tab" data-bs-target="#game4" type="button" role="tab">
                        Game 4
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="game5-tab" data-bs-toggle="tab" data-bs-target="#game5" type="button" role="tab">
                        Game 5
                      </button>
                    </li>
                  </ul>

                  <div class="tab-content" id="gameTabContent">
                    <!-- Overall Tab -->
                    <div class="tab-pane fade show active" id="overall" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Last Updated</th>
                              <th scope="col">Details</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                    <small class="text-muted">Pro Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">4,856</span></td>
                              <td>242.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,089</span></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">2 hours ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('elite-strikers')">
                                  <i class="ti ti-chevron-down" id="elite-strikers-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="elite-strikers-details" style="display: none;">
                              <td colspan="10">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Elite Strikers</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>John</strong></td>
                                            <td>279</td>
                                            <td>262</td>
                                            <td>285</td>
                                            <td>265</td>
                                            <td>269</td>
                                            <td><span class="fw-bold text-success">1,360</span></td>
                                            <td>272.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Sarah</strong></td>
                                            <td>268</td>
                                            <td>260</td>
                                            <td>270</td>
                                            <td>270</td>
                                            <td>280</td>
                                            <td><span class="fw-bold text-success">1,328</span></td>
                                            <td>265.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Mike</strong></td>
                                            <td>275</td>
                                            <td>268</td>
                                            <td>265</td>
                                            <td>268</td>
                                            <td>265</td>
                                            <td><span class="fw-bold text-success">1,341</span></td>
                                            <td>268.2</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Lisa</strong></td>
                                            <td>267</td>
                                            <td>244</td>
                                            <td>325</td>
                                            <td>264</td>
                                            <td>307</td>
                                            <td><span class="fw-bold text-success">1,407</span></td>
                                            <td>281.4</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                    <small class="text-muted">Elite Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">5,923</span></td>
                              <td>236.9</td>
                              <td>5</td>
                              <td><span class="text-warning">1,245</span></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">1 hour ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('pin-masters')">
                                  <i class="ti ti-chevron-down" id="pin-masters-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="pin-masters-details" style="display: none;">
                              <td colspan="10">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Pin Masters</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>Tom</strong></td>
                                            <td>248</td>
                                            <td>255</td>
                                            <td>248</td>
                                            <td>258</td>
                                            <td>248</td>
                                            <td><span class="fw-bold text-success">1,257</span></td>
                                            <td>251.4</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Emma</strong></td>
                                            <td>256</td>
                                            <td>262</td>
                                            <td>256</td>
                                            <td>262</td>
                                            <td>256</td>
                                            <td><span class="fw-bold text-success">1,292</span></td>
                                            <td>258.4</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Alex</strong></td>
                                            <td>242</td>
                                            <td>248</td>
                                            <td>242</td>
                                            <td>248</td>
                                            <td>242</td>
                                            <td><span class="fw-bold text-success">1,222</span></td>
                                            <td>244.4</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Maria</strong></td>
                                            <td>251</td>
                                            <td>256</td>
                                            <td>251</td>
                                            <td>256</td>
                                            <td>251</td>
                                            <td><span class="fw-bold text-success">1,265</span></td>
                                            <td>253.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>David</strong></td>
                                            <td>248</td>
                                            <td>246</td>
                                            <td>237</td>
                                            <td>265</td>
                                            <td>248</td>
                                            <td><span class="fw-bold text-success">1,244</span></td>
                                            <td>248.8</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                    <small class="text-muted">Advanced Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">7,134</span></td>
                              <td>237.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,456</span></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">30 min ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('lane-legends')">
                                  <i class="ti ti-chevron-down" id="lane-legends-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="lane-legends-details" style="display: none;">
                              <td colspan="10">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Lane Legends</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>Sarah</strong></td>
                                            <td>242</td>
                                            <td>238</td>
                                            <td>245</td>
                                            <td>245</td>
                                            <td>248</td>
                                            <td><span class="fw-bold text-success">1,218</span></td>
                                            <td>243.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Mike</strong></td>
                                            <td>238</td>
                                            <td>240</td>
                                            <td>238</td>
                                            <td>238</td>
                                            <td>245</td>
                                            <td><span class="fw-bold text-success">1,199</span></td>
                                            <td>239.8</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Lisa</strong></td>
                                            <td>245</td>
                                            <td>243</td>
                                            <td>252</td>
                                            <td>252</td>
                                            <td>252</td>
                                            <td><span class="fw-bold text-success">1,244</span></td>
                                            <td>248.8</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Tom</strong></td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>248</td>
                                            <td><span class="fw-bold text-success">1,208</span></td>
                                            <td>241.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Emma</strong></td>
                                            <td>243</td>
                                            <td>243</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>250</td>
                                            <td><span class="fw-bold text-success">1,232</span></td>
                                            <td>246.4</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Alex</strong></td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>255</td>
                                            <td>255</td>
                                            <td>235</td>
                                            <td><span class="fw-bold text-success">1,241</span></td>
                                            <td>248.2</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-info">4</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                    <small class="text-muted">Intermediate Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">4,567</span></td>
                              <td>228.4</td>
                              <td>5</td>
                              <td><span class="text-warning">1,023</span></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">15 min ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('spare-squad')">
                                  <i class="ti ti-chevron-down" id="spare-squad-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="spare-squad-details" style="display: none;">
                              <td colspan="10">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Spare Squad</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>Maria</strong></td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>256</td>
                                            <td><span class="fw-bold text-success">1,248</span></td>
                                            <td>249.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>David</strong></td>
                                            <td>260</td>
                                            <td>260</td>
                                            <td>260</td>
                                            <td>260</td>
                                            <td>278</td>
                                            <td><span class="fw-bold text-success">1,318</span></td>
                                            <td>263.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Anna</strong></td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td><span class="fw-bold text-success">1,210</span></td>
                                            <td>242.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Chris</strong></td>
                                            <td>206</td>
                                            <td>206</td>
                                            <td>206</td>
                                            <td>206</td>
                                            <td>313</td>
                                            <td><span class="fw-bold text-success">1,137</span></td>
                                            <td>227.4</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-dark">5</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Gutter Gang</h6>
                                    <small class="text-muted">Beginner Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Lisa, Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">5,234</span></td>
                              <td>209.4</td>
                              <td>5</td>
                              <td><span class="text-warning">1,156</span></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">5 min ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('gutter-gang')">
                                  <i class="ti ti-chevron-down" id="gutter-gang-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="gutter-gang-details" style="display: none;">
                              <td colspan="10">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Gutter Gang</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>Lisa</strong></td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td><span class="fw-bold text-success">1,160</span></td>
                                            <td>232.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Tom</strong></td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td><span class="fw-bold text-success">1,240</span></td>
                                            <td>248.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Emma</strong></td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td><span class="fw-bold text-success">1,200</span></td>
                                            <td>240.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Alex</strong></td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td><span class="fw-bold text-success">1,180</span></td>
                                            <td>236.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Maria</strong></td>
                                            <td>200</td>
                                            <td>200</td>
                                            <td>200</td>
                                            <td>200</td>
                                            <td>200</td>
                                            <td><span class="fw-bold text-success">1,000</td>
                                            <td>200.0</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- 4-Player Teams Tab -->
                    <div class="tab-pane fade" id="teams4" role="tabpanel">
                      <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>4-Player Teams Division:</strong> Teams compete fairly against other 4-player teams only.
                      </div>
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Last Updated</th>
                              <th scope="col">Details</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                    <small class="text-muted">Pro Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">4,856</span></td>
                              <td>242.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,089</span></td>
                              <td><small class="text-muted">2 hours ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('elite-strikers-4')">
                                  <i class="ti ti-chevron-down" id="elite-strikers-4-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="elite-strikers-4-details" style="display: none;">
                              <td colspan="9">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Elite Strikers (4-Player Division)</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>John</strong></td>
                                            <td>279</td>
                                            <td>262</td>
                                            <td>285</td>
                                            <td>265</td>
                                            <td>269</td>
                                            <td><span class="fw-bold text-success">1,360</span></td>
                                            <td>272.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Sarah</strong></td>
                                            <td>268</td>
                                            <td>260</td>
                                            <td>270</td>
                                            <td>270</td>
                                            <td>280</td>
                                            <td><span class="fw-bold text-success">1,328</span></td>
                                            <td>265.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Mike</strong></td>
                                            <td>275</td>
                                            <td>268</td>
                                            <td>265</td>
                                            <td>268</td>
                                            <td>265</td>
                                            <td><span class="fw-bold text-success">1,341</span></td>
                                            <td>268.2</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Lisa</strong></td>
                                            <td>267</td>
                                            <td>244</td>
                                            <td>325</td>
                                            <td>264</td>
                                            <td>307</td>
                                            <td><span class="fw-bold text-success">1,407</span></td>
                                            <td>281.4</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                    <small class="text-muted">Intermediate Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">4,567</span></td>
                              <td>228.4</td>
                              <td>5</td>
                              <td><span class="text-warning">1,023</span></td>
                              <td><small class="text-muted">15 min ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('spare-squad-4')">
                                  <i class="ti ti-chevron-down" id="spare-squad-4-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="spare-squad-4-details" style="display: none;">
                              <td colspan="9">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Spare Squad (4-Player Division)</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>Maria</strong></td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>256</td>
                                            <td><span class="fw-bold text-success">1,248</span></td>
                                            <td>249.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>David</strong></td>
                                            <td>260</td>
                                            <td>260</td>
                                            <td>260</td>
                                            <td>260</td>
                                            <td>278</td>
                                            <td><span class="fw-bold text-success">1,318</span></td>
                                            <td>263.6</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Anna</strong></td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td>242</td>
                                            <td><span class="fw-bold text-success">1,210</span></td>
                                            <td>242.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Chris</strong></td>
                                            <td>206</td>
                                            <td>206</td>
                                            <td>206</td>
                                            <td>206</td>
                                            <td>313</td>
                                            <td><span class="fw-bold text-success">1,137</span></td>
                                            <td>227.4</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Crushers</h6>
                                    <small class="text-muted">Amateur Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">4,234</span></td>
                              <td>211.7</td>
                              <td>5</td>
                              <td><span class="text-warning">987</span></td>
                              <td><small class="text-muted">45 min ago</small></td>
                              <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="togglePlayerDetails('pin-crushers-4')">
                                  <i class="ti ti-chevron-down" id="pin-crushers-4-icon"></i> View Details
                                </button>
                              </td>
                            </tr>
                            <tr class="player-details-row" id="pin-crushers-4-details" style="display: none;">
                              <td colspan="9">
                                <div class="card border-0 bg-light">
                                  <div class="card-body">
                                    <h6 class="mb-3">Individual Player Scores - Pin Crushers (4-Player Division)</h6>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                          <tr>
                                            <th>Player</th>
                                            <th>Game 1</th>
                                            <th>Game 2</th>
                                            <th>Game 3</th>
                                            <th>Game 4</th>
                                            <th>Game 5</th>
                                            <th>Total</th>
                                            <th>Average</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <td><strong>Lisa</strong></td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td>232</td>
                                            <td><span class="fw-bold text-success">1,160</span></td>
                                            <td>232.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Tom</strong></td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td>248</td>
                                            <td><span class="fw-bold text-success">1,240</span></td>
                                            <td>248.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Emma</strong></td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td>240</td>
                                            <td><span class="fw-bold text-success">1,200</span></td>
                                            <td>240.0</td>
                                          </tr>
                                          <tr>
                                            <td><strong>Alex</strong></td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td>236</td>
                                            <td><span class="fw-bold text-success">1,180</span></td>
                                            <td>236.0</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- 5-Player Teams Tab -->
                    <div class="tab-pane fade" id="teams5" role="tabpanel">
                      <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>5-Player Teams Division:</strong> Teams compete fairly against other 5-player teams only.
                      </div>
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Last Updated</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                    <small class="text-muted">Elite Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">5,923</span></td>
                              <td>236.9</td>
                              <td>5</td>
                              <td><span class="text-warning">1,245</span></td>
                              <td><small class="text-muted">1 hour ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Gutter Gang</h6>
                                    <small class="text-muted">Beginner Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Lisa, Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">5,234</span></td>
                              <td>209.4</td>
                              <td>5</td>
                              <td><span class="text-warning">1,156</span></td>
                              <td><small class="text-muted">5 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Strike Force</h6>
                                    <small class="text-muted">Intermediate Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa, Tom</td>
                              <td><span class="fw-bold text-success">5,123</span></td>
                              <td>204.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,089</span></td>
                              <td><small class="text-muted">20 min ago</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- 6-Player Teams Tab -->
                    <div class="tab-pane fade" id="teams6" role="tabpanel">
                      <div class="alert alert-info mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>6-Player Teams Division:</strong> Teams compete fairly against other 6-player teams only.
                      </div>
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Total Score</th>
                              <th scope="col">Avg/Game</th>
                              <th scope="col">Games Played</th>
                              <th scope="col">Best Game</th>
                              <th scope="col">Last Updated</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                    <small class="text-muted">Advanced Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">7,134</span></td>
                              <td>237.8</td>
                              <td>5</td>
                              <td><span class="text-warning">1,456</span></td>
                              <td><small class="text-muted">30 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Dynasty</h6>
                                    <small class="text-muted">Pro Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris, Lisa, Tom</td>
                              <td><span class="fw-bold text-success">6,789</span></td>
                              <td>226.3</td>
                              <td>5</td>
                              <td><span class="text-warning">1,345</span></td>
                              <td><small class="text-muted">10 min ago</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Kings</h6>
                                    <small class="text-muted">Intermediate Team</small>
                                  </div>
                                </div>
                              </td>
                              <td>Emma, Alex, Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">6,456</span></td>
                              <td>215.2</td>
                              <td>5</td>
                              <td><span class="text-warning">1,234</span></td>
                              <td><small class="text-muted">25 min ago</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 1 Tab -->
                    <div class="tab-pane fade" id="game1" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,089</span></td>
                              <td><small>279, 268, 275, 267</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">9:30 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,245</span></td>
                              <td><small>248, 256, 242, 251, 248</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">9:45 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">1,456</span></td>
                              <td><small>242, 238, 245, 240, 243, 248</small></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">10:00 AM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 2 Tab -->
                    <div class="tab-pane fade" id="game2" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,267</span></td>
                              <td><small>255, 262, 248, 256, 246</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">10:15 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,234</span></td>
                              <td><small>262, 260, 268, 244</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">10:30 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">1,156</span></td>
                              <td><small>248, 260, 242, 206</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">10:45 AM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 3 Tab -->
                    <div class="tab-pane fade" id="game3" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,145</span></td>
                              <td><small>285, 270, 265, 325</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">11:00 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">1,378</span></td>
                              <td><small>245, 238, 252, 240, 248, 255</small></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">11:15 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,234</span></td>
                              <td><small>248, 256, 242, 251, 237</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">11:30 AM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 4 Tab -->
                    <div class="tab-pane fade" id="game4" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Pin Masters</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Tom, Emma, Alex, Maria, David</td>
                              <td><span class="fw-bold text-success">1,289</span></td>
                              <td><small>258, 262, 248, 256, 265</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">11:45 AM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,267</span></td>
                              <td><small>265, 270, 268, 264</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">12:00 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Gutter Gang</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Lisa, Tom, Emma, Alex, Maria</td>
                              <td><span class="fw-bold text-success">1,156</span></td>
                              <td><small>232, 248, 240, 236, 200</small></td>
                              <td><span class="badge bg-info">5</span></td>
                              <td><small class="text-muted">12:15 PM</small></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                    <!-- Game 5 Tab -->
                    <div class="tab-pane fade" id="game5" role="tabpanel">
                      <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              <th scope="col">Rank</th>
                              <th scope="col">Team</th>
                              <th scope="col">Players</th>
                              <th scope="col">Score</th>
                              <th scope="col">Individual Scores</th>
                              <th scope="col">Team Size</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td><span class="badge bg-primary">1</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-5.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-6.jpg" alt="Player 5" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-7.jpg" alt="Player 6" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Lane Legends</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Sarah, Mike, Lisa, Tom, Emma, Alex</td>
                              <td><span class="fw-bold text-success">1,478</span></td>
                              <td><small>248, 245, 252, 248, 250, 235</small></td>
                              <td><span class="badge bg-warning">6</span></td>
                              <td><small class="text-muted">12:30 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-secondary">2</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-4.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Elite Strikers</h6>
                                  </div>
                                </div>
                              </td>
                              <td>John, Sarah, Mike, Lisa</td>
                              <td><span class="fw-bold text-success">1,121</span></td>
                              <td><small>269, 280, 265, 307</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">12:45 PM</small></td>
                            </tr>
                            <tr>
                              <td><span class="badge bg-warning">3</span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="d-flex me-2">
                                    <img src="assets/images/profile/user-8.jpg" alt="Player 1" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-1.jpg" alt="Player 2" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-2.jpg" alt="Player 3" class="rounded-circle border border-2 border-white" width="28" style="margin-right: -6px;">
                                    <img src="assets/images/profile/user-3.jpg" alt="Player 4" class="rounded-circle border border-2 border-white" width="28">
                                  </div>
                                  <div>
                                    <h6 class="mb-0">Spare Squad</h6>
                                  </div>
                                </div>
                              </td>
                              <td>Maria, David, Anna, Chris</td>
                              <td><span class="fw-bold text-success">1,089</span></td>
                              <td><small>256, 278, 242, 313</small></td>
                              <td><span class="badge bg-success">4</span></td>
                              <td><small class="text-muted">1:00 PM</small></td>
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

  <!-- Score Table Functionality -->
  <script>
    // Toggle player details function
    function togglePlayerDetails(teamId) {
      const detailsRow = document.getElementById(teamId + '-details');
      const icon = document.getElementById(teamId + '-icon');
      
      if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
        icon.classList.remove('ti-chevron-down');
        icon.classList.add('ti-chevron-up');
      } else {
        detailsRow.style.display = 'none';
        icon.classList.remove('ti-chevron-up');
        icon.classList.add('ti-chevron-down');
      }
    }

    // Date filter functionality
    document.getElementById('dateFilter').addEventListener('change', function() {
      const selectedDate = this.value;
      const customDateInput = document.getElementById('customDate');
      
      if (selectedDate === 'custom') {
        customDateInput.style.display = 'inline-block';
        customDateInput.focus();
      } else {
        customDateInput.style.display = 'none';
        console.log('Date filter changed to:', selectedDate);
        // Here you would typically make an AJAX call to get filtered data
        showNotification('Loading data for ' + selectedDate + '...', 'info');
      }
    });

    // Custom date input functionality
    document.getElementById('customDate').addEventListener('change', function() {
      const selectedDate = this.value;
      if (selectedDate) {
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        console.log('Custom date selected:', selectedDate);
        showNotification('Loading data for ' + formattedDate + '...', 'info');
      }
    });

    // Refresh table functionality
    function refreshTable() {
      const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
      const icon = refreshBtn.querySelector('i');
      
      // Add spinning animation
      icon.classList.add('ti-spin');
      
      // Simulate loading
      setTimeout(() => {
        icon.classList.remove('ti-spin');
        showNotification('Table refreshed successfully!', 'success');
      }, 1000);
    }

    // Tab switching with data loading simulation
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(e) {
        const targetId = e.target.getAttribute('data-bs-target');
        console.log('Switched to tab:', targetId);
        
        // Simulate loading data for specific game
        if (targetId !== '#overall' && targetId !== '#teams4' && targetId !== '#teams5' && targetId !== '#teams6') {
          const gameNumber = targetId.replace('#game', '');
          showNotification('Loading Game ' + gameNumber + ' data...', 'info');
        }
      });
    });

    // Notification function
    function showNotification(message, type = 'info') {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      document.body.appendChild(notification);
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 3000);
    }

    // Auto-refresh table every 30 seconds
    setInterval(() => {
      // Only refresh if user is on the page
      if (!document.hidden) {
        console.log('Auto-refreshing table...');
      }
    }, 30000);
  </script>
  
  <?php include 'includes/admin-popup.php'; ?>
</body>

</html>
