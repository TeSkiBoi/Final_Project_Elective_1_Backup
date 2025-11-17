# 🚀 Profile Settings - Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Run Database Migration (1 minute)
Execute this SQL in your database:

```sql
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;
```

Or import the file:
```bash
mysql -u root -p student_information_system < database/setup_profile_settings.sql
```

### Step 2: Set Directory Permissions (1 minute)
```bash
chmod 755 assets/uploads/profiles
```

### Step 3: Access Profile Settings (1 minute)
Navigate to: `http://localhost:8000/App/View/profilesetting.php`

### Step 4: Test Features (2 minutes)
- ✓ Change password
- ✓ Upload profile picture
- ✓ Delete account

---

## 📁 What Was Created

| File | Purpose |
|------|---------|
| `App/Controller/ProfileController.php` | Backend API for profile operations |
| `App/View/profilesetting.php` | User interface for profile settings |
| `assets/uploads/profiles/` | Profile picture storage directory |
| `database/setup_profile_settings.sql` | Database setup script |

---

## 🎯 Key Features

### 1️⃣ View Profile
- See your profile picture
- Check your user information
- View account status

### 2️⃣ Change Password
- Enter current password
- Enter new password
- Confirm new password
- Click "Update Password"

**Requirements:**
- Current password must be correct
- New password must be at least 6 characters
- New passwords must match

### 3️⃣ Upload Profile Picture
- Click "Upload Picture"
- Select an image (JPEG, PNG, or GIF)
- See preview before uploading
- Click "Upload"

**Limits:**
- Maximum file size: 5MB
- Supported formats: JPEG, PNG, GIF

### 4️⃣ Delete Account
- Click "Delete Account" (in Danger Zone)
- Read the warnings carefully
- Enter your password
- Check the confirmation checkbox
- Click "Delete My Account"

**Warning:** This action is permanent and cannot be undone!

---

## 🔒 Security Notes

- ✅ All passwords are verified
- ✅ All files are validated
- ✅ All actions are logged
- ✅ Sessions are secure
- ✅ Files are protected

---

## ❓ Quick Troubleshooting

### Profile picture won't upload?
```bash
chmod 755 assets/uploads/profiles
```

### Password change fails?
- Check current password is correct
- Ensure new password is 6+ characters

### Can't access profile settings?
- Make sure you're logged in
- Check URL: `/App/View/profilesetting.php`

---

## 📊 Activity Tracking

All your actions are logged:
- Password changes
- Picture uploads
- Account deletions

View your logs at: `/App/View/activitylog.php`

---

## 📞 Need Help?

Refer to the detailed guides:
- `PROFILE_SETTINGS_GUIDE.md` - Technical details
- `PROFILE_SETTINGS_IMPLEMENTATION.md` - Feature overview
- `IMPLEMENTATION_CHECKLIST.md` - Complete checklist

---

**Ready?** Access your profile settings now! 🎉
