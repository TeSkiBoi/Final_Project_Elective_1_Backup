# Profile Settings Feature - Implementation Guide

## Overview
The Profile Settings feature allows users to:
1. ✅ View their profile information
2. ✅ Change their password with verification
3. ✅ Upload and update profile pictures
4. ✅ Delete their account permanently

## Files Created/Modified

### New Files
- `App/Controller/ProfileController.php` - API endpoint for all profile operations
- `App/View/profilesetting.php` - User profile settings interface
- `assets/uploads/profiles/` - Directory for storing uploaded profile pictures
- `database/migrations/add_profile_picture_column.sql` - Migration to add profile_picture column

### Modified Files
- None (existing User model works with new features)

## Database Setup

### 1. Add Profile Picture Column (Required)
Execute the following SQL in your database:

```sql
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;
```

This adds a column to store the filename of the user's profile picture.

### Alternative: Using the Migration File
Run the SQL file provided:
```bash
mysql -u root -p student_information_system < database/migrations/add_profile_picture_column.sql
```

## Features

### 1. Profile Information Card
- Displays current user's profile picture
- Shows User ID, Username, Email
- Shows current status (Active/Inactive)
- Upload Picture button

**Default Profile Picture:**
If no picture is uploaded, a placeholder image is shown.

### 2. Change Password Section
Users can update their password with:
- Current password verification (required for security)
- New password input
- Password confirmation
- Minimum 6 characters requirement

**Validation:**
- Current password must be correct
- New passwords must match
- Minimum 6 characters

**Activity Logging:**
- Password changes are logged in user_logs table

### 3. Upload Profile Picture
- Supported formats: JPEG, PNG, GIF
- Maximum file size: 5MB
- Image preview before upload
- Old picture is automatically deleted when new one is uploaded

**File Storage:**
- Files stored in: `assets/uploads/profiles/`
- Naming convention: `{user_id}_{timestamp}.{extension}`
- Example: `U001_1731688234.jpg`

**Activity Logging:**
- Profile picture uploads are logged in user_logs table

### 4. Delete Account (Danger Zone)
Permanent account deletion with:
- Password confirmation (required)
- Confirmation checkbox
- Warning alerts
- Automatic logout after deletion
- Profile picture cleanup

**What Gets Deleted:**
- User account and all information
- Profile picture (if exists)
- User activity logs
- Session/Authentication data

**After Deletion:**
- User is automatically logged out
- Redirected to login page
- Account cannot be recovered

## API Endpoints

### Get Profile
```
GET /App/Controller/ProfileController.php?action=getProfile
```
Returns current user's profile (except password hash)

### Update Password
```
POST /App/Controller/ProfileController.php?action=updatePassword
Content-Type: application/json

{
  "current_password": "currentPass123",
  "new_password": "newPass123",
  "confirm_password": "newPass123"
}
```

Response:
```json
{
  "success": true,
  "message": "Password updated successfully",
  "data": null
}
```

### Upload Profile Picture
```
POST /App/Controller/ProfileController.php?action=uploadProfilePicture
Content-Type: multipart/form-data

Form Data:
- profile_picture: [binary file]
```

Response:
```json
{
  "success": true,
  "message": "Profile picture uploaded successfully",
  "data": {
    "profile_picture": "U001_1731688234.jpg",
    "url": "/assets/uploads/profiles/U001_1731688234.jpg"
  }
}
```

### Delete Account
```
POST /App/Controller/ProfileController.php?action=deleteAccount
Content-Type: application/json

{
  "password": "currentPassword123"
}
```

Response:
```json
{
  "success": true,
  "message": "Account deleted successfully",
  "data": null
}
```

## Error Responses

### Password Update Errors
- `400`: Missing required fields or validation failed
- `401`: Current password is incorrect
- `500`: Database error

### File Upload Errors
- `400`: No file uploaded or invalid file type
- `400`: File size exceeds 5MB
- `400`: Invalid image format
- `500`: Failed to save file

### Account Deletion Errors
- `400`: Password is required
- `401`: Password is incorrect
- `404`: User not found
- `500`: Failed to delete account

## Security Features

✅ **Password Security**
- MD5 hashing (compatible with existing setup)
- Current password verification required for changes
- Password strength validation (min 6 chars)

✅ **File Upload Security**
- File type validation (MIME type checking)
- File size limits (5MB max)
- Unique filenames with timestamps
- Stored outside web root (if configured)

✅ **Account Deletion Security**
- Password confirmation required
- Checkbox confirmation required
- Automatic logout after deletion
- Activity logging before deletion

✅ **Session Security**
- All operations require authentication
- Session validation on every request
- CORS and CSRF protection via normal form handling

## Usage Example

### From Frontend
Users access the profile settings by:
1. Clicking profile icon in header
2. Navigating to `/App/View/profilesetting.php`
3. Using the sidebar navigation menu

### JavaScript Integration
All operations use SweetAlert2 for user feedback:
- Success alerts (green)
- Error alerts (red)
- Warning alerts (yellow)

## Activity Logging

All profile operations are logged:
- "User updated password"
- "User uploaded profile picture"
- "User deleted account permanently"

These are stored in the `user_logs` table with:
- User ID
- Action description
- Timestamp
- IP Address

## Troubleshooting

### Profile Picture Not Uploading
1. Check `assets/uploads/profiles/` directory permissions (should be 755)
2. Verify file format is JPEG, PNG, or GIF
3. Check file size is under 5MB
4. Review browser console for JavaScript errors

### Profile Picture Not Displaying
1. Check if file exists in `assets/uploads/profiles/`
2. Verify filename in database matches actual file
3. Check directory permissions (readable)

### Password Update Failed
1. Verify current password is correct
2. Check new password meets minimum 6 character requirement
3. Ensure new passwords match in both fields

### Account Deletion Failed
1. Verify password is correct
2. Check confirmation checkbox is checked
3. Ensure user account still exists in database

## Notes

- Profile pictures are stored with user_id prefix for easy identification
- Old profile pictures are automatically deleted to save space
- All operations are logged for audit trail
- Session is immediately destroyed after account deletion
- Deleted accounts cannot be recovered

## Support

For issues or questions about the profile settings feature:
1. Check browser console for JavaScript errors
2. Review server logs for PHP errors
3. Verify database schema matches expected structure
4. Ensure all files are in correct locations
