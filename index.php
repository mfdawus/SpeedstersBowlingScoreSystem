<?php
require_once 'includes/landing-stats.php';

// Get real statistics from database
$stats = getLandingPageStats();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VIPERS VENOMS - Bowling Score System</title>
  <link rel="shortcut icon" type="image/x-icon" href="./assets/images/logos/favicon.ico" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/vipersvenoms-theme.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      overflow-x: hidden;
    }

    /* Animated Gradient Background */
    .welcome-section {
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(-45deg, #667eea, #764ba2, #5a4a9a, #48a868);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
      overflow: hidden;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Bowling Lane Stripes */
    .lane-stripes {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      opacity: 0.05;
      z-index: 0;
      background: repeating-linear-gradient(
        90deg,
        transparent,
        transparent 80px,
        rgba(255, 255, 255, 0.3) 80px,
        rgba(255, 255, 255, 0.3) 82px
      );
    }

    /* Floating Bowling Elements */
    .floating-element {
      position: absolute;
      opacity: 0.15;
      animation: float 20s infinite ease-in-out;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-30px) rotate(10deg); }
    }

    .bowling-pin-1 { top: 10%; left: 10%; font-size: 3rem; animation-delay: 0s; }
    .bowling-pin-2 { top: 20%; right: 15%; font-size: 2.5rem; animation-delay: 2s; }
    .bowling-pin-3 { bottom: 15%; left: 8%; font-size: 2rem; animation-delay: 4s; }
    .bowling-ball-1 { top: 60%; right: 10%; font-size: 2.5rem; animation-delay: 1s; }
    .bowling-ball-2 { bottom: 25%; right: 20%; font-size: 2rem; animation-delay: 3s; }
    .trophy-icon { top: 40%; left: 5%; font-size: 2.5rem; animation-delay: 5s; }

    /* Glassmorphism Welcome Card */
    .welcome-card {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px) saturate(180%);
      border-radius: 30px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 
                  0 0 100px rgba(102, 126, 234, 0.2);
      animation: fadeInUp 1s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Logo Animation */
    .logo-container {
      animation: logoEntrance 1.2s ease-out;
    }

    @keyframes logoEntrance {
      0% {
        opacity: 0;
        transform: scale(0.5) rotate(-10deg);
      }
      100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
      }
    }

    .logo-link {
      display: inline-block;
      text-decoration: none;
      transition: all 0.4s ease;
      filter: drop-shadow(0 10px 20px rgba(102, 126, 234, 0.3));
    }

    .logo-link:hover {
      transform: scale(1.1) rotate(5deg);
      filter: drop-shadow(0 15px 30px rgba(102, 126, 234, 0.5));
    }

    /* Typography */
    .main-title {
      color: #ffffff;
      text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      animation: titleFade 1s ease-out 0.3s both;
    }

    @keyframes titleFade {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .subtitle {
      color: rgba(255, 255, 255, 0.9);
      animation: titleFade 1s ease-out 0.5s both;
    }

    /* Feature Cards with Glassmorphism */
    .feature-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 2rem 1.5rem;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      animation: cardFadeIn 0.8s ease-out both;
    }

    .feature-card:nth-child(1) { animation-delay: 0.6s; }
    .feature-card:nth-child(2) { animation-delay: 0.8s; }
    .feature-card:nth-child(3) { animation-delay: 1s; }

    @keyframes cardFadeIn {
      from {
        opacity: 0;
        transform: translateY(30px) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .feature-card:hover {
      transform: translateY(-15px) scale(1.05);
      background: rgba(255, 255, 255, 0.25);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .feature-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      animation: iconPulse 2s ease-in-out infinite;
      width: 80px;
      height: 80px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      margin: 0 auto 1rem;
      transition: all 0.3s ease;
    }
    
    .feature-icon i {
      font-size: 2.5rem;
    }

    @keyframes iconPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .feature-card:hover .feature-icon {
      animation: iconBounce 0.6s ease;
      background: rgba(255, 255, 255, 0.35);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      transform: scale(1.1);
    }

    @keyframes iconBounce {
      0%, 100% { transform: translateY(0) scale(1.1); }
      50% { transform: translateY(-10px) scale(1.1); }
    }

    .feature-title {
      color: #ffffff;
      font-weight: 600;
      margin: 1rem 0 0.5rem;
    }

    .feature-desc {
      color: rgba(255, 255, 255, 0.8);
      margin: 0;
    }

    /* Stats Section */
    .stats-section {
      animation: cardFadeIn 0.8s ease-out 1.2s both;
    }

    .stat-item {
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 1.5rem 1rem;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .stat-item:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.05);
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      color: #ffffff;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .stat-label {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.9rem;
      margin-top: 0.5rem;
    }

    /* CTA Buttons */
    .cta-buttons {
      animation: cardFadeIn 0.8s ease-out 1.4s both;
    }

    .btn-hero {
      padding: 1rem 2.5rem;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 50px;
      border: 2px solid transparent;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-hero-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% {
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
      }
      50% {
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
      }
    }

    .btn-hero-primary:hover {
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
      animation: none;
    }

    .btn-hero-secondary {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .btn-hero-secondary:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: rgba(255, 255, 255, 0.6);
      transform: translateY(-3px) scale(1.05);
      color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .feature-card {
        margin-bottom: 1rem;
      }

      .feature-icon {
        width: 70px;
        height: 70px;
      }
      
      .feature-icon i {
        font-size: 2rem;
      }

      .main-title {
        font-size: 2rem;
      }

      .subtitle {
        font-size: 0.9rem;
      }

      .stat-number {
        font-size: 1.5rem;
      }

      .stat-label {
        font-size: 0.75rem;
      }

      .floating-element {
        display: none;
      }

      .btn-hero {
        padding: 0.8rem 1.8rem;
        font-size: 1rem;
      }

      .welcome-card {
        padding: 2rem 1.5rem !important;
      }
    }

    /* Particles Background */
    .particles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
    }

    .particle {
      position: absolute;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 50%;
      animation: particleFloat 15s infinite ease-in-out;
    }

    @keyframes particleFloat {
      0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0;
      }
      10% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        transform: translateY(-100vh) translateX(100px);
        opacity: 0;
      }
    }

    /* Login Slide Panel */
    .login-slide-panel {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.7);
      width: 90%;
      max-width: 500px;
      max-height: 90vh;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
      border-radius: 20px;
      z-index: 9999;
      transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      overflow-y: auto;
      opacity: 0;
      visibility: hidden;
    }

    .login-slide-panel.active {
      transform: translate(-50%, -50%) scale(1);
      opacity: 1;
      visibility: visible;
    }

    .login-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 9998;
      opacity: 0;
      visibility: hidden;
      transition: all 0.4s ease;
    }

    .login-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .login-panel-header {
      padding: 2rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      position: relative;
      border-radius: 20px 20px 0 0;
    }

    .close-login-btn {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .close-login-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: rotate(90deg);
    }

    .login-panel-content {
      padding: 2rem;
      border-radius: 0 0 20px 20px;
    }

    @media (max-width: 768px) {
      .login-slide-panel {
        width: 95%;
        max-height: 85vh;
      }
      
      .login-panel-header {
        padding: 1.5rem;
      }
      
      .login-panel-content {
        padding: 1.5rem;
      }
    }
  </style>
</head>

<body>

  <!-- Main Welcome Content -->
  <div class="welcome-section">
    <!-- Bowling Lane Stripes -->
    <div class="lane-stripes"></div>

    <!-- Floating Bowling Elements -->
    <div class="floating-element bowling-pin-1">🎳</div>
    <div class="floating-element bowling-pin-2">🎳</div>
    <div class="floating-element bowling-pin-3">🎳</div>
    <div class="floating-element bowling-ball-1">🎱</div>
    <div class="floating-element bowling-ball-2">🎱</div>
    <div class="floating-element trophy-icon">🏆</div>

    <!-- Particles Background -->
    <div class="particles" id="particles"></div>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-md-11">
          <div class="welcome-card p-4 p-md-5">
            <!-- Logo & Title -->
            <div class="logo-container text-center mb-4">
              <a href="./homepage.php" class="logo-link">
                <img src="assets/images/logos/vipersvenoms-main-logo.png" alt="VIPERS VENOMS Logo" width="140" class="mb-3" />
              </a>
              <h1 class="display-3 fw-bold main-title mb-3">Welcome to VIPERS VENOMS</h1>
              <p class="lead subtitle mb-0">Your complete bowling score management system</p>
              <p class="subtitle" style="font-size: 0.95rem;">Track • Compete • Excel</p>
            </div>

            <!-- Live Stats -->
            <div class="stats-section mb-5">
              <div class="row g-3">
                <div class="col-6 col-md-3">
                  <div class="stat-item text-center">
                    <div class="stat-number" id="playerCount" data-target="<?php echo $stats['total_users']; ?>">0</div>
                    <div class="stat-label">Active Players</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="stat-item text-center">
                    <div class="stat-number" id="gameCount" data-target="<?php echo $stats['total_games']; ?>">0</div>
                    <div class="stat-label">Games Played</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="stat-item text-center">
                    <div class="stat-number" id="avgScore" data-target="<?php echo $stats['avg_score']; ?>">0</div>
                    <div class="stat-label">Avg Score</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="stat-item text-center">
                    <div class="stat-number" id="highScore" data-target="<?php echo $stats['high_score']; ?>">0</div>
                    <div class="stat-label">High Score</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Feature Cards -->
            <div class="row g-4 mb-5">
              <div class="col-md-4">
                <div class="feature-card text-center">
                  <div class="feature-icon" style="background: linear-gradient(135deg, rgba(72, 168, 104, 0.3), rgba(72, 168, 104, 0.15));">
                    <i class="ti ti-chart-line" style="color: #48a868;"></i>
                  </div>
                  <h5 class="feature-title">Performance Dashboard</h5>
                  <p class="feature-desc">Track your stats, analyze trends, and watch your progress</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="feature-card text-center">
                  <div class="feature-icon" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.3), rgba(255, 193, 7, 0.15));">
                    <i class="ti ti-bowling" style="color: #ffc107;"></i>
                  </div>
                  <h5 class="feature-title">Easy Lane Booking</h5>
                  <p class="feature-desc">Reserve lanes instantly and manage your game schedule</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="feature-card text-center">
                  <div class="feature-icon" style="background: linear-gradient(135deg, rgba(255, 107, 107, 0.3), rgba(255, 107, 107, 0.15));">
                    <i class="ti ti-trophy" style="color: #ff6b6b;"></i>
                  </div>
                  <h5 class="feature-title">Live Leaderboards</h5>
                  <p class="feature-desc">Compete with others and climb the rankings</p>
                </div>
              </div>
            </div>
            
            <!-- CTA Buttons -->
            <div class="cta-buttons text-center">
              <div class="d-flex gap-3 justify-content-center flex-wrap">
                <button onclick="openLoginSlide()" class="btn btn-hero btn-hero-primary">
                  <i class="ti ti-login me-2"></i>
                  Login to Your Account
                </button>
              </div>
              <div class="mt-4">
                <small style="color: rgba(255, 255, 255, 0.7);">
                  <i class="ti ti-info-circle me-1"></i>
                  Join the premier bowling community and elevate your game!
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Login Overlay -->
  <div class="login-overlay" id="loginOverlay" onclick="closeLoginSlide()"></div>

  <!-- Login Slide Panel -->
  <div class="login-slide-panel" id="loginSlidePanel">
    <div class="login-panel-header">
      <button class="close-login-btn" onclick="closeLoginSlide()">
        <i class="ti ti-x"></i>
      </button>
      <div class="text-center">
        <img src="assets/images/logos/vipersvenoms-main-logo.png" alt="VIPERS VENOMS Logo" width="80" class="mb-3" />
        <h3 class="mb-1">Welcome Back!</h3>
        <p class="mb-0" style="opacity: 0.9;">Login to your account</p>
      </div>
    </div>
    
    <div class="login-panel-content">
      <iframe 
        id="loginFrame" 
        src="./authentication-login.php?embed=true" 
        style="width: 100%; border: none; min-height: 500px;"
        onload="adjustIframeHeight()"
      ></iframe>
    </div>
  </div>
  
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Create floating particles
    function createParticles() {
      const particlesContainer = document.getElementById('particles');
      const particleCount = 30;
      
      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const size = Math.random() * 5 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        
        particlesContainer.appendChild(particle);
      }
    }
    
    // Animate stats counting with real data
    function animateCounter(elementId, target, duration = 2000) {
      const element = document.getElementById(elementId);
      if (!element) return;
      
      const start = 0;
      const increment = target / (duration / 16);
      let current = start;
      
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          element.textContent = target;
          clearInterval(timer);
        } else {
          element.textContent = Math.floor(current);
        }
      }, 16);
    }
    
    // Open login slide panel
    function openLoginSlide() {
      document.getElementById('loginSlidePanel').classList.add('active');
      document.getElementById('loginOverlay').classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    // Close login slide panel
    function closeLoginSlide() {
      document.getElementById('loginSlidePanel').classList.remove('active');
      document.getElementById('loginOverlay').classList.remove('active');
      document.body.style.overflow = '';
    }
    
    // Adjust iframe height
    function adjustIframeHeight() {
      const iframe = document.getElementById('loginFrame');
      try {
        if (iframe.contentWindow.document.body) {
          iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
        }
      } catch(e) {
        // Cross-origin iframe, use default height
        iframe.style.height = '600px';
      }
    }
    
    // Listen for successful login message
    window.addEventListener('message', function(event) {
      if (event.data === 'login_success') {
        // Slide the entire page to the left and redirect
        document.body.style.transition = 'transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        document.body.style.transform = 'translateX(-100%)';
        
        setTimeout(function() {
          window.location.href = './dashboard.php';
        }, 800);
      }
    });
    
    // Initialize animations
    document.addEventListener('DOMContentLoaded', function() {
      createParticles();
      
      // Animate counters after a delay using real data from data-target attributes
      setTimeout(() => {
        const playerCount = parseInt(document.getElementById('playerCount').getAttribute('data-target'));
        const gameCount = parseInt(document.getElementById('gameCount').getAttribute('data-target'));
        const avgScore = parseInt(document.getElementById('avgScore').getAttribute('data-target'));
        const highScore = parseInt(document.getElementById('highScore').getAttribute('data-target'));
        
        animateCounter('playerCount', playerCount);
        animateCounter('gameCount', gameCount);
        animateCounter('avgScore', avgScore);
        animateCounter('highScore', highScore);
      }, 1500);
      
      // Check if login is required (from URL parameter)
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('login') === 'required') {
        // Auto-open login modal after page loads
        setTimeout(() => {
          openLoginSlide();
        }, 800);
      }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeLoginSlide();
      }
    });
  </script>
  
</body>

</html>