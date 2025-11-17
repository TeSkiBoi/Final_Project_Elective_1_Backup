-- Add profile_picture column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;

-- This SQL adds a profile_picture column to store the filename of the user's profile picture
-- If the column already exists, this will fail, which is fine
