# Staff Role Permissions - Implementation Summary

## Overview
Implemented role-based access control (RBAC) to restrict Staff users to view-only access for Records while allowing full edit access to Barangay Projects and Financial Management.

---

## Changes Made

### 1. Sidebar Navigation (`App/View/template/sidebar_navigation.php`)

**Admin View (roleId == 1):**
- **Records Section**: Full access with all CRUD operations
  - Household
  - Resident
  - Children
  - Seniors
  - Adult

- **Features Section**: Full access to all features
  - Certificate Generator (Admin only)
  - Financial Management
  - Barangay Projects
  - Blotter & Incident Recording (Admin only)

- **Maintenance Section**: Admin-exclusive pages
  - User Management
  - Role Management
  - Barangay Officials Org Chart

**Staff View (roleId == 2):**
- **Records Section (View Only)**: Read-only access to:
  - Household
  - Resident
  - Children
  - Seniors
  - Adult

- **Features Section**: Full edit access to:
  - Barangay Projects
  - Financial Management

- **Hidden from Staff**:
  - Certificate Generator
  - Blotter & Incident Recording
  - User Management
  - Role Management
  - Barangay Officials
  - Contact Us

---

### 2. RBAC Permissions (`App/View/middleware/RBACProtect.php`)

Updated `$pagePermissions` array:

```php
// Admin-only pages (role_id 1)
'role.php' => [1],
'user.php' => [1],
'barangay_officials.php' => [1],
'blotter.php' => [1],
'certificate_generator.php' => [1],

// Admin and Staff pages - VIEW ONLY for Staff (role_id 1, 2)
'household.php' => [1, 2],
'Resident.php' => [1, 2],
'children.php' => [1, 2],
'senior.php' => [1, 2],
'adult.php' => [1, 2],

// Admin and Staff pages - EDIT ALLOWED for Staff (role_id 1, 2)
'projects.php' => [1, 2],
'financial.php' => [1, 2],

// Everyone (all authenticated users)
'index.php' => [1, 2, 3],
'activitylog.php' => [1, 2, 3],
'profilesetting.php' => [1, 2, 3],
```

---

### 3. Frontend View Restrictions

#### `App/View/household.php`
**Changes:**
- Wrapped "Add New Household" button in `<?php if($roleId == 1): ?>` check
- Wrapped Edit/Delete buttons in table rows with Admin check
- Staff users only see "View" button for viewing household members

**Before (Staff could see):**
```php
<button class="btn btn-primary">Add New Household</button>
<button class="btn btn-warning">Edit</button>
<button class="btn btn-danger">Delete</button>
```

**After (Staff view):**
```php
<button class="btn btn-info">View</button>
<!-- Edit/Delete hidden for Staff -->
```

#### `App/View/Resident.php`
**Changes:**
- Wrapped Edit/Delete buttons with `<?php if($roleId == 1): ?>` check
- Staff users see "View Only" text instead of action buttons

**Staff View:**
```php
<span class="text-muted"><i class="fas fa-eye"></i> View Only</span>
```

#### `App/View/children.php`, `senior.php`, `adult.php`
**Status:** Already view-only (only contain View buttons, no edit/delete functionality)

---

### 4. Backend API Restrictions

#### `App/Controller/HouseholdController.php`
Added role checks to all modification methods:

```php
// handleCreate()
if (getCurrentUserRole() != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can create households.']);
    return;
}

// handleCreateWithMembers()
// handleUpdate()
// handleUpdateWithMembers()
// handleDelete()
// All protected with same check
```

**Protected Methods:**
- `handleCreate()` - Create household
- `handleCreateWithMembers()` - Create household with residents
- `handleUpdate()` - Update household details
- `handleUpdateWithMembers()` - Update household with members
- `handleDelete()` - Delete household

**Allowed Methods (Staff can use):**
- `handleGetAll()` - View all households
- `handleGetById()` - View household details
- `handleGetMembers()` - View household members

#### `App/Controller/ResidentController.php`
Added role checks to all modification methods:

```php
// handleCreate()
if (getCurrentUserRole() != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can create residents.']);
    return;
}

// handleUpdate()
// handleDelete()
// All protected with same check
```

**Protected Methods:**
- `handleCreate()` - Create resident
- `handleUpdate()` - Update resident
- `handleDelete()` - Delete resident

**Allowed Methods (Staff can use):**
- `handleGetAll()` - View all residents
- `handleGetById()` - View resident details

#### `App/Controller/ProjectController.php` ✅
**No restrictions added** - Both Admin and Staff can fully edit Barangay Projects

#### `App/Controller/FinancialController.php` ✅
**No restrictions added** - Both Admin and Staff can fully edit Financial Management

---

## Security Implementation

### Multi-Layer Protection

**Layer 1: Frontend (UI)**
- Buttons hidden based on `$roleId` session variable
- Prevents accidental clicks from Staff users
- Provides clear visual indication of read-only access

**Layer 2: Backend (API)**
- Controllers check `getCurrentUserRole()` before processing
- Returns HTTP 403 Forbidden for unauthorized modifications
- Prevents API manipulation attempts

**Layer 3: RBAC Middleware**
- Page-level access control via `RBACProtect.php`
- Prevents direct URL access to unauthorized pages
- Redirects with error message if access denied

---

## Testing Checklist

### Admin User (roleId == 1) ✅
- [ ] Can see all menu items in sidebar
- [ ] Can create/edit/delete Households
- [ ] Can create/edit/delete Residents
- [ ] Can view Children/Seniors/Adults
- [ ] Can access Barangay Projects (full edit)
- [ ] Can access Financial Management (full edit)
- [ ] Can access Certificate Generator
- [ ] Can access User Management
- [ ] Can access Role Management
- [ ] Can access Barangay Officials

### Staff User (roleId == 2) ✅
**Should See:**
- [ ] Dashboard
- [ ] Records dropdown (Household, Resident, Children, Seniors, Adult) - **View Only**
- [ ] Features dropdown (Barangay Projects, Financial Management) - **Full Edit**
- [ ] Profile Settings
- [ ] Activity Log

**Should NOT See:**
- [ ] Certificate Generator
- [ ] Blotter & Incident Recording
- [ ] Maintenance section (User, Role, Officials)

**Permissions:**
- [ ] Can view Household list (no Add/Edit/Delete buttons visible)
- [ ] Can view Household members (View button works)
- [ ] Can view Resident list (shows "View Only" text)
- [ ] Can view Children/Seniors/Adults
- [ ] **Cannot** create/edit/delete Households (buttons hidden)
- [ ] **Cannot** create/edit/delete Residents (buttons hidden)
- [ ] **Can** create/edit/delete Barangay Projects (all buttons visible)
- [ ] **Can** create/edit/delete Financial Transactions (all buttons visible)
- [ ] API calls for create/update/delete Records return 403 Forbidden

---

## User Messages

### Staff attempting unauthorized action:
**Frontend:** Buttons hidden - no error message needed

**Backend (if API called directly):**
```json
{
  "success": false,
  "message": "Access Denied. Only Admin can create/update/delete households/residents."
}
```

**Page Access (via RBACProtect):**
```
"Access Denied! You do not have permission to access this page."
```

---

## Files Modified

1. `App/View/template/sidebar_navigation.php` - Separated Admin/Staff navigation
2. `App/View/middleware/RBACProtect.php` - Updated page permissions
3. `App/View/household.php` - Hide Add/Edit/Delete for Staff
4. `App/View/Resident.php` - Hide Edit/Delete for Staff
5. `App/Controller/HouseholdController.php` - Added Admin-only checks
6. `App/Controller/ResidentController.php` - Added Admin-only checks

**Not Modified (intentionally):**
- `App/Controller/ProjectController.php` - Staff can edit
- `App/Controller/FinancialController.php` - Staff can edit
- `App/View/projects.php` - Staff can edit
- `App/View/financial.php` - Staff can edit
- `App/View/children.php` - Already read-only
- `App/View/senior.php` - Already read-only
- `App/View/adult.php` - Already read-only

---

## Rollback Instructions

If you need to revert Staff to full edit access:

### Quick Rollback:
1. **Sidebar Navigation:** Change Staff section from separate to shared:
   ```php
   <?php if($roleId == 1 || $roleId == 2): ?>
   ```

2. **Remove button restrictions** in household.php and Resident.php:
   - Remove `<?php if($roleId == 1): ?>` checks around buttons

3. **Remove controller checks:**
   - Comment out or remove role checks in HouseholdController.php
   - Comment out or remove role checks in ResidentController.php

---

## Notes

- Children, Seniors, and Adults pages were already view-only (no edit buttons)
- Projects and Financial pages have no role restrictions (both roles can edit)
- RBACProtect middleware enforces page-level access control
- getCurrentUserRole() returns roleId from session ($_SESSION['role_id'])
- Role IDs: 1 = Admin, 2 = Staff, 3 = Student

---

**Implementation Date:** November 30, 2025  
**Developer:** AI Assistant  
**Status:** ✅ Complete and Ready for Testing
