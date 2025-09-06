# Essential Files for Version Control

## Core Application Files
- `index.php` - Main entry point
- `authentication-login.php` - Login page
- `authentication-register.php` - Registration page
- `dashboard.php` - User dashboard
- `logout.php` - Logout functionality
- `database.php` - Database connection

## Admin Pages
- `admin-dashboard.php` - Admin main dashboard
- `admin-score-monitoring-solo.php` - Solo score monitoring (fully functional)
- `admin-score-monitoring-doubles.php` - Doubles score monitoring (maintenance mode)
- `admin-score-monitoring-team.php` - Team score monitoring (maintenance mode)
- `admin-score-update.php` - Score update (maintenance mode)
- `admin-create-account.php` - Create account (maintenance mode)
- `admin-user-management.php` - User management (maintenance mode)
- `admin-events.php` - Events management (maintenance mode)
- `admin-session-management.php` - Session management

## Player Pages
- `score-table-solo.php` - Solo score table (fully functional)
- `score-table-doubles.php` - Doubles score table (maintenance mode)
- `score-table-team.php` - Team score table (maintenance mode)
- `lane-booking.php` - Lane booking (maintenance mode)
- `events.php` - Events calendar (maintenance mode)
- `group-selection.php` - Group selection (maintenance mode)
- `discount-code.php` - Discount code (maintenance mode)
- `my-profile.php` - User profile

## AJAX Endpoints
- `ajax/session-management.php` - Session and score management
- `ajax/admin-dashboard-data.php` - Admin dashboard data
- `ajax/player-score-data.php` - Player score data
- `ajax/user-crud.php` - User CRUD operations
- `ajax/export-scores-excel.php` - CSV export functionality

## Includes (Core Functions)
- `includes/auth.php` - Authentication functions
- `includes/dashboard.php` - Dashboard functions
- `includes/session-management.php` - Session management functions
- `includes/user-management.php` - User management functions
- `includes/maintenance-bypass.php` - Maintenance bypass system
- `includes/admin-popup.php` - Admin popup component

## Maintenance System
- `maintenance.php` - Basic maintenance page
- `maintenance-flexible.php` - Flexible maintenance page with custom messages

## Documentation
- `MAINTENANCE_GUIDE.md` - Maintenance system documentation
- `ESSENTIAL_FILES.md` - This file

## UI Components
- `ui-alerts.php` - Alert components
- `ui-buttons.php` - Button components
- `ui-card.php` - Card components
- `ui-forms.php` - Form components
- `ui-typography.php` - Typography components
- `icon-tabler.php` - Icon components

## Configuration
- `package.json` - Node.js dependencies
- `package-lock.json` - Dependency lock file

## Assets Directory
- `assets/` - All CSS, JS, images, and libraries (Bootstrap, jQuery, etc.)

## Modals Directory
- `modals/user-management-modals.php` - User management modal components

## Files Removed During Cleanup
- `test-csv-export.php` - CSV export test file (removed)
- `test-maintenance.php` - Maintenance system test file (removed)
- `redirect-to-maintenance.php` - Simple redirect script (removed)

## Status Summary
- ✅ **Fully Functional**: Solo score monitoring, player solo table, admin dashboard, user management
- 🔧 **Maintenance Mode**: All other pages show admin popup or maintenance page
- 📊 **Export**: CSV export working perfectly
- 🎯 **Ready for Demo**: All essential features working
