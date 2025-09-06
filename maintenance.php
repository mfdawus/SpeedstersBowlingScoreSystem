<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Under Maintenance - SPEEDSTERS Bowling System</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/speedster main logo.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
  <style>
    .maintenance-container {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .maintenance-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      padding: 60px 40px;
      text-align: center;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      width: 100%;
      backdrop-filter: blur(10px);
    }
    .maintenance-icon {
      font-size: 80px;
      color: #667eea;
      margin-bottom: 30px;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    .maintenance-title {
      font-size: 2.5rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 20px;
    }
    .maintenance-subtitle {
      font-size: 1.2rem;
      color: #666;
      margin-bottom: 30px;
      line-height: 1.6;
    }
    .maintenance-description {
      font-size: 1rem;
      color: #777;
      margin-bottom: 40px;
      line-height: 1.8;
    }
    .progress-bar {
      width: 100%;
      height: 8px;
      background: #e9ecef;
      border-radius: 4px;
      overflow: hidden;
      margin-bottom: 20px;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #667eea, #764ba2);
      border-radius: 4px;
      animation: progress 3s ease-in-out infinite;
    }
    @keyframes progress {
      0% { width: 0%; }
      50% { width: 75%; }
      100% { width: 0%; }
    }
    .back-button {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
      box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    .back-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
      color: white;
      text-decoration: none;
    }
    .features-list {
      text-align: left;
      margin: 30px 0;
      padding: 0 20px;
    }
    .features-list li {
      margin: 10px 0;
      color: #555;
    }
    .features-list li::marker {
      color: #667eea;
    }
  </style>
</head>

<body>
  <div class="maintenance-container">
    <div class="maintenance-card">
      <div class="maintenance-icon">
        <i class="ti ti-tools"></i>
      </div>
      
      <h1 class="maintenance-title">Under Maintenance</h1>
      
      <p class="maintenance-subtitle">
        This page is currently being developed
      </p>
      
      <div class="progress-bar">
        <div class="progress-fill"></div>
      </div>
      
      <p class="maintenance-description">
        We're working hard to bring you the best bowling experience. 
        This feature is currently under development and will be available soon.
      </p>
      
      <div class="features-list">
        <h5 style="color: #333; margin-bottom: 15px; text-align: center;">What's Coming Soon:</h5>
        <ul>
          <li>Complete backend integration</li>
          <li>Real-time score tracking</li>
          <li>Advanced team management</li>
          <li>Tournament scheduling</li>
          <li>Player statistics dashboard</li>
        </ul>
      </div>
      
      <a href="javascript:history.back()" class="back-button">
        <i class="ti ti-arrow-left me-2"></i>
        Go Back
      </a>
      
      <div style="margin-top: 30px; font-size: 0.9rem; color: #999;">
        <p>Need immediate assistance? Contact our support team.</p>
        <p><strong>SPEEDSTERS Bowling System</strong> - Version 1.0</p>
      </div>
    </div>
  </div>

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  
  <script>
    // Add some interactive elements
    document.addEventListener('DOMContentLoaded', function() {
      // Add click effect to the back button
      const backButton = document.querySelector('.back-button');
      backButton.addEventListener('click', function(e) {
        // Add a small animation
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
          this.style.transform = 'translateY(-2px)';
        }, 100);
      });
      
      // Add a subtle floating animation to the icon
      const icon = document.querySelector('.maintenance-icon');
      let floatDirection = 1;
      setInterval(() => {
        const currentTransform = icon.style.transform || 'translateY(0px)';
        const currentY = parseFloat(currentTransform.match(/translateY\(([^)]+)\)/) || [0, 0])[1];
        const newY = currentY + (floatDirection * 2);
        
        if (newY > 10) floatDirection = -1;
        if (newY < -10) floatDirection = 1;
        
        icon.style.transform = `translateY(${newY}px)`;
      }, 2000);
    });
  </script>
</body>

</html>
