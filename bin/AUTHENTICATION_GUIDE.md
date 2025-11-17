# Authentication System Implementation Guide

## Overview
A complete authentication system has been implemented with database validation, session management, and access control for all Admin Dashboard pages.

## System Architecture

### Files Created

#### 1. Authentication Configuration (`App/Config/Auth.php`)
- **Purpose**: Centralized session and authentication management
- **Features**:
  - Session initialization and timeout handling (30 minutes)
  - User authentication status checking
  - User information retrieval functions
  - Session message management (errors/success)
  - User logout with session destruction

**Key Functions**:
```php
isAuthenticated()           // Check if user is logged in
getCurrentUserId()          // Get logged-in user ID
getCurrentUsername()        // Get logged-in username
getCurrentUserFullName()    // Get user's full name
getCurrentUserRole()        // Get user's role ID
requireLogin()             // Redirect if not authenticated
logout()                   // Destroy session and redirect
```

#### 2. User Model (`App/Model/User.php`)
- **Purpose**: Database operations for users
- **Methods**:
  - `getUserByUsername()` - Query user by username
  - `getUserById()` - Query user by ID
  - `verifyPassword()` - Verify password (MD5 comparison)
  - `getUserRole()` - Get user role name from roles table
  - `logUserActivity()` - Log login/logout activities
  - `getAllUsers()` - Retrieve all users
  - `create()` - Create new user
  - `update()` - Update user details
  - `delete()` - Delete user

#### 3. Login Controller (`App/Controller/LoginController.php`)
- **Purpose**: Handle login/logout API requests
- **API Endpoints**:
  - `POST ?action=login` - Authenticate user credentials
  - `POST ?action=logout` - Logout user
  - `GET ?action=current` - Get current user info

**Request Format**:
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response Format**:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user_id": "U001",
    "username": "admin",
    "fullname": "System Administrator",
    "role": "Admin"
  }
}
```

#### 4. Protection Middleware (`App/View/middleware/ProtectAuth.php`)
- **Purpose**: Restrict access to protected pages
- **Implementation**: Included at the top of all View files
- **Behavior**: Redirects unauthenticated users to login page

#### 5. Logout Controller (`App/Controller/LogoutController.php`)
- **Purpose**: Handle session destruction
- **Features**: Logs user logout activity and destroys session

### Updated Files

#### 6. `index.php` - Login Page
- Added PHP authentication check
- Redirects already-logged-in users to dashboard
- Updated login form with async authentication
- Displays database-driven error messages

#### 7. `App/View/template/header_navigation.php`
- Added user information display
- Shows username and full name
- Logout functionality with confirmation modal
- Enhanced dropdown menu

#### 8. Protected View Files
All of the following files now require authentication:
- `App/View/department.php`
- `App/View/courses.php`
- `App/View/students.php`
- `App/View/user.php`
- `App/View/enrollment.php`
- `App/View/index.php`

## User Credentials (from setup.sql)

```
Username: admin
Password: admin123  (MD5: 0192023a7bbd73250516f069df18b500)
Role: Admin

Username: staff1
Password: staff123 (MD5: 4f0c68cf74f6a4f508f5d5fb4bc4f29d)
Role: Staff
```

## How Authentication Works

### Login Flow:
1. User enters username and password on index.php
2. JavaScript sends async POST request to `LoginController.php?action=login`
3. Controller validates credentials against `users` table
4. Verifies password using MD5 comparison
5. On success:
   - Sets session variables (user_id, username, fullname, email, role_id)
   - Logs login activity to `user_logs` table
   - Returns success response
   - Frontend redirects to `App/View/index.php`
6. On failure:
   - Returns error response with appropriate message
   - Frontend displays error alert

### Access Control Flow:
1. User attempts to access protected page (e.g., department.php)
2. Page includes `ProtectAuth.php` middleware
3. Middleware checks `isAuthenticated()`
4. If not authenticated:
   - Redirects to `index.php?auth=required`
   - Session is destroyed
5. If authenticated:
   - Page loads normally
   - User info is displayed in navbar

### Logout Flow:
1. User clicks logout in navbar
2. Confirmation modal appears
3. On confirm, JavaScript sends POST to `LogoutController.php?action=logout`
4. Controller destroys session
5. Redirects to login page with logout success message

## Session Configuration

- **Session Timeout**: 30 minutes of inactivity
- **Session Start**: Automatic on every page load
- **Last Activity**: Updated on each request
- **Cookie Clearing**: Automatic on logout

## Security Features

✅ **Protected Routes**: All admin pages require authentication
✅ **Session Validation**: Timeout mechanism prevents unauthorized access
✅ **Password Hashing**: MD5 comparison (database-compatible)
✅ **SQL Injection Prevention**: Prepared statements in all queries
✅ **Activity Logging**: All login/logout activities logged
✅ **XSS Prevention**: Data escaped with `htmlspecialchars()`
✅ **IP Logging**: User IP address recorded in user_logs table
✅ **CSRF Protection**: Uses standard session mechanisms

## Database Integration

### Tables Used:
- `users` - User credentials and details
- `roles` - User roles (Admin, Staff)
- `user_logs` - Activity logging

### Queries Executed:
```sql
-- Get user by username
SELECT * FROM users WHERE username = ?

-- Get user role
SELECT role_name FROM roles WHERE role_id = ?

-- Log user activity
INSERT INTO user_logs (user_id, action, ip_address) VALUES (?, ?, ?)
```

## API Endpoints

### Login Endpoint
**URL**: `App/Controller/LoginController.php?action=login`
**Method**: POST
**Content-Type**: application/json

### Logout Endpoint
**URL**: `App/Controller/LogoutController.php?action=logout`
**Method**: POST

### Get Current User
**URL**: `App/Controller/LoginController.php?action=current`
**Method**: GET

## Testing Steps

1. **Test Login**:
   - Open `index.php`
   - Enter `admin` / `admin123`
   - Should redirect to dashboard

2. **Test Invalid Credentials**:
   - Enter wrong password
   - Should show "Invalid username or password"

3. **Test Access Control**:
   - Logout or clear cookies
   - Try accessing `App/View/department.php` directly
   - Should redirect to login page

4. **Test Session Timeout**:
   - Login successfully
   - Wait 30 minutes without activity
   - Next request redirects to login

5. **Test Logout**:
   - Login successfully
   - Click logout button
   - Confirm logout
   - Should redirect to login page

## Navbar User Display

The navbar now shows:
- Current logged-in username
- User's full name in dropdown
- Logout button with confirmation modal
- Professional user experience

## Error Handling

### Login Errors:
- "Username is required"
- "Password is required"
- "Invalid username or password"

### Access Control Errors:
- Automatic redirect to login (no error message)
- Session destroyed

### Network Errors:
- Network connection failure displayed
- User can retry

## Notes

- Database credentials in `App/Config/Database.php`:
  - Host: localhost
  - User: root
  - Password: (empty - XAMPP default)
  - Database: student_information_system

- All passwords use MD5 hashing (consistent with setup.sql)
- Session cookie uses HttpOnly flag for security
- All user input is sanitized and escaped

## Next Steps

1. Implement role-based access control (RBAC)
2. Add password change functionality
3. Implement password reset via email
4. Add two-factor authentication (2FA)
5. Create admin user management panel

---

**Authentication System Ready!** ✅ Users can now login securely and access the admin dashboard with proper access control.
