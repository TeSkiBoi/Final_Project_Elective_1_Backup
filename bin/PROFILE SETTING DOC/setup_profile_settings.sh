#!/bin/bash
# Quick Setup Script for Profile Settings Feature

echo "Profile Settings Feature - Setup Checklist"
echo "==========================================="
echo ""

# 1. Check directory structure
echo "✓ Checking directory structure..."
if [ -d "assets/uploads/profiles" ]; then
    echo "  ✓ Profile uploads directory exists"
else
    echo "  ✗ Profile uploads directory missing - creating..."
    mkdir -p assets/uploads/profiles
fi

# 2. Check file permissions
echo ""
echo "✓ Checking file permissions..."
if [ -w "assets/uploads/profiles" ]; then
    echo "  ✓ Profile uploads directory is writable"
else
    echo "  ⚠ Profile uploads directory permissions may need adjustment"
    echo "  Run: chmod 755 assets/uploads/profiles"
fi

# 3. Check database migration
echo ""
echo "✓ Database setup required:"
echo "  Run the following SQL command:"
echo "  ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;"
echo ""

# 4. Check file creation
echo "✓ Checking required files..."
files=(
    "App/Controller/ProfileController.php"
    "App/View/profilesetting.php"
    "database/migrations/add_profile_picture_column.sql"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file - MISSING!"
    fi
done

echo ""
echo "==========================================="
echo "Setup Complete!"
echo "==========================================="
echo ""
echo "Next Steps:"
echo "1. Run the database migration (SQL command above)"
echo "2. Access profile settings at: /App/View/profilesetting.php"
echo "3. Test password change, picture upload, and account deletion"
echo ""
