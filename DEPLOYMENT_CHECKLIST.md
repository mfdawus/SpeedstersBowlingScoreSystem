# 🚀 Web Deployment Checklist

## ⚠️ FILES TO EXCLUDE WHEN UPLOADING TO NAMECHEAP

### 📁 DO NOT UPLOAD These Files/Folders:

#### 1. Version Control
```
.git/                    # Git repository folder
.gitignore              # Git ignore file
.gitattributes          # Git attributes
```

#### 2. Development Files
```
package.json            # NPM package file (not needed on server)
package-lock.json       # NPM lock file
node_modules/           # If you have any Node dependencies
```

#### 3. Documentation Files (Optional - you can upload these but not required)
```
README.md
DEPLOYMENT_CHECKLIST.md
MAINTENANCE_GUIDE.md
ESSENTIAL_FILES.md
```

#### 4. IDE/Editor Files
```
.vscode/
.idea/
*.sublime-project
.DS_Store
Thumbs.db
```

#### 5. Test/Debug Files
```
test-*.php
debug-*.php
phpinfo.php
```

---

## ✅ BEFORE UPLOADING - IMPORTANT CHANGES

### 1. Update Database Credentials (`database.php`)
```php
// Change from localhost to Namecheap database details
define('DB_HOST', 'localhost');              // Usually 'localhost' or provided by Namecheap
define('DB_NAME', 'your_db_name_here');      // Your Namecheap database name
define('DB_USER', 'your_db_username_here');  // Your Namecheap database username
define('DB_PASS', 'your_db_password_here');  // Your Namecheap database password
```

### 2. Create Database on Namecheap
- Log into cPanel
- Go to MySQL Databases
- Create a new database
- Create a database user
- Add user to database with ALL PRIVILEGES
- Import your local database SQL file

### 3. Set Proper Folder Permissions
```
uploads/profile_pictures/  → 755 or 777 (writable)
assets/                    → 755 (readable)
includes/                  → 755 (readable)
ajax/                      → 755 (readable)
*.php files                → 644 (readable, executable by server)
```

### 4. Verify `.htaccess` Files Exist
- `uploads/profile_pictures/.htaccess` - Protects upload folder
- Root `.htaccess` - For URL rewriting (if needed)

---

## 📤 UPLOAD METHODS

### Option 1: FTP/SFTP (Recommended)
Use FileZilla or similar FTP client:
1. Connect to your Namecheap FTP
2. Navigate to `public_html/` or `www/`
3. Upload ALL files EXCEPT those listed above
4. Verify folder structure is intact

### Option 2: cPanel File Manager
1. Log into cPanel
2. Go to File Manager
3. Create a ZIP of your project (excluding unwanted files)
4. Upload ZIP to server
5. Extract in public_html directory

### Option 3: Git Deployment (Advanced)
If your host supports it:
```bash
git clone your-repository-url
cd your-repository
# Then update database.php with production credentials
```

---

## 🧪 TESTING AFTER DEPLOYMENT

### 1. Test Basic Functionality
- [ ] Homepage loads
- [ ] Login works
- [ ] Dashboard displays
- [ ] Profile pictures load correctly
- [ ] Score tables show data

### 2. Test File Uploads
- [ ] Upload a profile picture
- [ ] Verify it appears immediately (no refresh needed)
- [ ] Check uploads folder has correct permissions

### 3. Check for Errors
- [ ] Open browser console (F12)
- [ ] Look for 404 errors
- [ ] Look for JavaScript errors
- [ ] Check Network tab for failed requests

### 4. Test on Mobile
- [ ] Responsive design works
- [ ] Touch interactions work
- [ ] Images load properly

---

## 🔒 SECURITY REMINDERS

1. ✅ Never commit database credentials to GitHub
2. ✅ Keep uploads folder protected with .htaccess
3. ✅ Use strong database passwords on production
4. ✅ Regularly backup your database
5. ✅ Keep uploaded files folder outside public_html if possible
6. ✅ Enable HTTPS on your domain

---

## 📊 FOLDER STRUCTURE ON SERVER

```
public_html/  (or www/)
├── admin-*.php
├── ajax/
├── assets/
├── includes/
├── modals/
├── uploads/
│   └── profile_pictures/
│       ├── .htaccess (IMPORTANT!)
│       ├── index.php (IMPORTANT!)
│       └── [user uploaded images]
├── authentication-*.php
├── dashboard.php
├── database.php (UPDATE CREDENTIALS!)
├── index.php
├── logout.php
├── my-profile.php
└── [other .php files]
```

---

## 🐛 COMMON ISSUES AFTER DEPLOYMENT

### Profile Pictures Not Showing
- Check `uploads/profile_pictures/` permissions (should be 755 or 777)
- Verify BASE_PATH is detecting correctly
- Check browser console for 404 errors

### Database Connection Errors
- Verify database credentials in `database.php`
- Check if database user has proper privileges
- Ensure database host is correct (usually 'localhost')

### 404 Errors for CSS/JS
- Check if `assets/` folder uploaded completely
- Verify folder permissions
- Check browser console Network tab

### Login Not Working
- Clear browser cookies
- Check if sessions are enabled on server
- Verify `includes/auth.php` is readable

---

## 📞 SUPPORT

If you encounter issues:
1. Check browser console for errors
2. Check server error logs in cPanel
3. Verify all files uploaded correctly
4. Double-check database credentials
5. Test with fresh browser (incognito mode)

---

**Last Updated:** November 21, 2024
**Version:** 1.0.0

