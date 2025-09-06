# Maintenance Page Guide

## Overview
The maintenance pages provide a professional way to handle pages that don't have backend integration yet or are under development.

## Admin Bypass Feature
**IMPORTANT**: Admin users can bypass maintenance pages and view the actual content. This allows administrators to test and develop features while regular users see the maintenance message.

## Files Created

### 1. `maintenance.php`
- **Purpose**: Basic maintenance page with generic message
- **Usage**: Simple redirect for any page under maintenance
- **Features**: 
  - Professional design with animations
  - Progress bar animation
  - Back button functionality
  - Responsive design

### 2. `maintenance-flexible.php`
- **Purpose**: Flexible maintenance page with custom messages
- **Usage**: Redirect with specific page information
- **Features**:
  - Custom page titles and messages
  - Specific messages for different page types
  - Both "Go Back" and "Go to Dashboard" buttons
  - URL parameters for customization

### 3. `redirect-to-maintenance.php`
- **Purpose**: Simple redirect script
- **Usage**: Quick redirect to basic maintenance page

### 4. `includes/maintenance-bypass.php`
- **Purpose**: Helper utility for admin bypass functionality
- **Usage**: Provides functions to check admin status and redirect accordingly

## How to Use

### Method 1: Simple Redirect
```php
<?php
header('Location: maintenance.php');
exit;
?>
```

### Method 2: Custom Message Redirect
```php
<?php
header('Location: maintenance-flexible.php?page=doubles&title=Doubles Score Table');
exit;
?>
```

### Method 3: Using Redirect Script
```php
<?php
include 'redirect-to-maintenance.php';
?>
```

### Method 4: Admin Bypass (Recommended)
```php
<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('page-name', 'Page Title');
?>
```

## URL Parameters for Flexible Maintenance

### Parameters:
- `page`: Page identifier (doubles, team, events, booking, profile, dashboard)
- `title`: Custom page title

### Examples:
- `maintenance-flexible.php?page=doubles&title=Doubles Score Table`
- `maintenance-flexible.php?page=team&title=Team Management`
- `maintenance-flexible.php?page=events&title=Event Calendar`

## Custom Messages Available

The flexible maintenance page includes specific messages for:
- **doubles**: Doubles scoring system enhancement
- **team**: Team management features development
- **events**: Event management system upgrade
- **booking**: Lane booking system enhancement
- **profile**: User profile management upgrade
- **dashboard**: Dashboard analytics enhancement

## Implementation Examples

### Example 1: Doubles Page
```php
<?php
// Redirect to maintenance page for doubles
header('Location: maintenance-flexible.php?page=doubles&title=Doubles Score Table');
exit;
?>
```

### Example 2: Team Page
```php
<?php
// Redirect to maintenance page for team
header('Location: maintenance-flexible.php?page=team&title=Team Score Table');
exit;
?>
```

### Example 3: Generic Maintenance
```php
<?php
// Simple redirect to basic maintenance
header('Location: maintenance.php');
exit;
?>
```

### Example 4: Admin Bypass Implementation
```php
<?php
// Check maintenance bypass for admin users
require_once 'includes/maintenance-bypass.php';
requireMaintenanceBypass('doubles', 'Doubles Score Table');
?>
```

## Design Features

### Visual Elements:
- **Gradient background**: Professional purple gradient
- **Glass morphism card**: Modern translucent design
- **Animated progress bar**: Shows development progress
- **Pulsing icon**: Eye-catching maintenance icon
- **Hover effects**: Interactive buttons with animations

### Responsive Design:
- **Mobile-friendly**: Works on all screen sizes
- **Touch-friendly**: Large buttons for mobile devices
- **Fast loading**: Optimized CSS and minimal JavaScript

### User Experience:
- **Clear messaging**: Explains what's happening
- **Navigation options**: Back button and dashboard link
- **Professional appearance**: Maintains brand consistency
- **Informative**: Shows what features are coming

## When to Use

### Use maintenance pages for:
- Pages without backend integration
- Features under development
- Temporary page unavailability
- Testing environments
- Pages being redesigned

### Don't use for:
- Permanent page removal
- Error pages (use proper error pages)
- Authentication failures
- Server errors

## Customization

### To add new page types:
1. Edit `maintenance-flexible.php`
2. Add new entries to `$pageMessages` array
3. Update URL parameters as needed

### To modify design:
1. Edit CSS in the `<style>` section
2. Modify colors, fonts, animations
3. Update icons or layout as needed

## Testing

### Test the maintenance pages:
1. Visit `maintenance.php` directly
2. Test `maintenance-flexible.php` with different parameters
3. Verify redirects work properly
4. Check responsive design on mobile devices

## Notes

- All maintenance pages include the SPEEDSTERS branding
- Pages are designed to be temporary solutions
- Consider adding estimated completion dates if available
- Update maintenance pages when features are completed
