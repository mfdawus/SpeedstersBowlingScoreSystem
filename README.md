# 🎳 VIPERS VENOMS Bowling System

A comprehensive bowling management system for tracking players, scores, sessions, and team performance.

## ✨ Features

- 👥 **User Management** - Admin and player roles with authentication
- 📊 **Score Tracking** - Real-time score monitoring for Solo, Doubles, Team, and Trio games
- 📈 **Performance Analytics** - Detailed statistics, rankings, and performance trends
- 👤 **Profile Management** - Custom profile pictures and player information
- 🎯 **Session Management** - Create and manage bowling sessions
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile devices
- 🔒 **Secure** - Password hashing, session management, and input validation

## 🛠️ Tech Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **UI Framework:** Bootstrap 5
- **Charts:** ApexCharts
- **Icons:** Tabler Icons

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for clean URLs)

## 🚀 Installation

### Local Development (XAMPP/WAMP)

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/VipersVenomsBowlingSystem.git
   cd VipersVenomsBowlingSystem
   ```

2. **Set up database**
   - Create a MySQL database
   - Import the SQL file (if provided)
   - Update `database.php` with your credentials:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Set folder permissions**
   ```bash
   chmod 755 uploads/profile_pictures/
   ```

4. **Access the application**
   - Navigate to `http://localhost/VipersVenomsBowlingSystem/`
   - Login with default admin credentials (if provided)

### Production Deployment

See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) for detailed deployment instructions.

## 📁 Project Structure

```
VipersVenomsBowlingSystem/
├── admin-*.php              # Admin dashboard and management pages
├── ajax/                    # AJAX request handlers
├── assets/                  # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── images/
├── includes/                # Reusable PHP components
│   ├── auth.php
│   ├── header.php
│   ├── sidebar.php
│   └── profile-picture-helper.php
├── modals/                  # Modal components
├── uploads/                 # User uploaded files
│   └── profile_pictures/
├── database.php             # Database configuration
├── index.php                # Landing page
├── dashboard.php            # Main dashboard
└── my-profile.php           # User profile page
```

## 👥 User Roles

### Admin
- Full system access
- User management (create, edit, delete)
- Session management
- Score monitoring
- System configuration

### Player
- View personal statistics
- Update profile
- View rankings and scores
- Participate in sessions

## 🔐 Security Features

- Password hashing with `password_hash()`
- SQL injection protection with PDO prepared statements
- XSS prevention with `htmlspecialchars()`
- Session management with secure cookies
- File upload validation
- Protected upload directories

## 📸 Screenshots

*(Add screenshots of your application here)*

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is proprietary software. All rights reserved.

## 👨‍💻 Developer

Developed by **[Your Name]**

## 📞 Support

For issues and questions:
- Create an issue on GitHub
- Email: [your-email@example.com]

## 🙏 Acknowledgments

- Bootstrap team for the UI framework
- Tabler Icons for beautiful icons
- ApexCharts for interactive charts

---

**Version:** 1.0.0  
**Last Updated:** November 21, 2024

