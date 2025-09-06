<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPEEDSTERS - Bowling System Login</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="./assets/images/logos/speedster main logo.png" alt="SPEEDSTERS Logo" width="200">
                </a>
                <p class="text-center">Bowling System Login</p>
                <?php
                // Handle login form submission
                if ($_POST) {
                    require_once 'includes/auth.php';
                    
                    $username = $_POST['username'] ?? '';
                    $password = $_POST['password'] ?? '';
                    
                    if ($username && $password) {
                        $result = login($username, $password);
                        
                        if ($result['success']) {
                            // Redirect based on user role
                            if ($result['user']['user_role'] === 'Admin') {
                                header('Location: admin-dashboard.php');
                            } else {
                                header('Location: dashboard.php');
                            }
                            exit();
                        } else {
                            $error_message = $result['message'];
                        }
                    } else {
                        $error_message = 'Please enter both username and password!';
                    }
                }
                ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                  <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                      <label class="form-check-label text-dark" for="flexCheckChecked">
                        Remember this Device
                      </label>
                    </div>
                    <a class="text-primary fw-bold" href="./forgot-password.php">Forgot Password?</a>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>