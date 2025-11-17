-- ============================================================
-- Profile Settings Feature - Database Setup Script
-- ============================================================
-- This script adds the necessary columns to support profile
-- picture uploads in the profile settings feature.
-- ============================================================

-- Step 1: Add profile_picture column to users table
-- This column stores the filename of the user's profile picture
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;

-- Step 2: Create index for faster lookups (optional but recommended)
-- CREATE INDEX idx_user_profile_picture ON users(profile_picture);

-- ============================================================
-- Verification Query
-- Run this to verify the column was added successfully:
-- DESCRIBE users;
-- ============================================================
-- You should see a new column: profile_picture | varchar(255) | YES | NULL
-- ============================================================

-- ============================================================
-- Data Migration (Optional - if upgrading existing system)
-- Set all profile_pictures to NULL for existing users
-- ============================================================
-- UPDATE users SET profile_picture = NULL WHERE profile_picture IS NULL;

-- ============================================================
-- Rollback Script (if needed to revert)
-- ============================================================
-- ALTER TABLE users DROP COLUMN profile_picture;

-- ============================================================
-- Notes:
-- - Profile pictures are stored in: assets/uploads/profiles/
-- - Filename format: {user_id}_{timestamp}.{extension}
-- - Maximum file size: 5MB
-- - Allowed formats: JPEG, PNG, GIF
-- ============================================================
