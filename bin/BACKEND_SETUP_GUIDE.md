# Backend Implementation Guide - Department Management

## Overview
This implementation provides a complete backend functionality for Department management with database connectivity, validation, error handling, and SweetAlert notifications.

## Files Created/Modified

### 1. Database Configuration (`App/Config/Database.php`)
- **Purpose**: Handles database connection using MySQLi
- **Features**:
  - Database connection initialization
  - Connection error handling
  - Methods to get and close connection
- **Configuration**:
  - Host: `localhost`
  - Database: `student_information_system`
  - Username: `root`
  - Password: (empty by default for XAMPP)

### 2. Department Model (`App/Model/Department.php`)
- **Purpose**: Handles all database operations for departments
- **Methods**:
  - `create()`: Create new department with duplicate checking
  - `getAll()`: Retrieve all departments
  - `getById()`: Retrieve specific department
  - `update()`: Update department with duplicate name validation
  - `delete()`: Delete department with foreign key constraint checking
  - `generateDepartmentId()`: Auto-generate unique department IDs (D001, D002, etc.)

**Features**:
- ✅ Duplicate entry detection
- ✅ Foreign key constraint checking (prevents deletion if courses exist)
- ✅ Exception handling with detailed error messages
- ✅ Prepared statements to prevent SQL injection

### 3. Department Controller (`App/Controller/DepartmentController.php`)
- **Purpose**: Handles API requests and routes to model
- **Endpoints**:
  - POST `?action=create` - Create new department
  - POST `?action=update` - Update department
  - POST `?action=delete` - Delete department
  - GET `?action=getAll` - Fetch all departments
  - GET `?action=getById&id=D001` - Fetch specific department

**Features**:
- ✅ JSON request/response handling
- ✅ Comprehensive input validation
- ✅ HTTP status codes (201, 200, 400, 405, 500)
- ✅ Error response formatting

### 4. Department View (`App/View/department.php`)
- **Updated with**:
  - SweetAlert2 integration
  - Async form submission handlers
  - Dynamic modal population
  - Real-time error and success notifications

## Error Handling

### Errors Trapped:
1. **Duplicate Entry** - If department name already exists
   ```
   Message: "Department name already exists. Please use a different name."
   ```

2. **Foreign Key Constraint** - If trying to delete department with courses
   ```
   Message: "Cannot delete department. It has associated courses."
   ```

3. **Database Errors** - Any database operation errors
   ```
   Message: Specific error from database
   ```

4. **Network Errors** - Failed server connection
   ```
   Message: "Failed to connect to the server. Please check your connection..."
   ```

5. **Validation Errors** - Missing required fields
   ```
   Message: "Department name is required"
   ```

## Sweet Alert Notifications

### Success Alert
- Icon: ✓ (green)
- Title: Success!
- Auto-action: Reload page after confirmation
- Color: Green (#6ec207)

### Error Alert
- Icon: ✗ (red)
- Title: Dynamic (based on error type)
- Description: Specific error message
- Color: Red (#dc3545)

### Warning Alert
- Icon: ⚠ (yellow)
- Title: Validation Error/Confirmation Failed
- Description: Specific warning message
- Color: Orange (#6ec207)

## How to Use

### Create Department:
1. Click "Add New Department" button
2. Fill in the Department Name (required)
3. Optionally fill in Department Code and Description
4. Click "Create Department"
5. See SweetAlert success/error notification
6. Page auto-reloads on success

### Update Department:
1. Click "Edit" button on a department row
2. Modal pre-fills with current data
3. Modify the department name
4. Click "Update Department"
5. See SweetAlert notification
6. Page auto-reloads on success

### Delete Department:
1. Click "Delete" button on a department row
2. Modal shows department name to delete
3. Type the department name to confirm
4. Click "Delete Department"
5. See SweetAlert notification
6. Page auto-reloads on success

## Testing Checklist

- [ ] Database connection working (check XAMPP MySQL is running)
- [ ] Create department successfully
- [ ] Duplicate entry shows error message
- [ ] Update department successfully
- [ ] Delete department successfully (if no courses)
- [ ] Try deleting department with courses - shows constraint error
- [ ] All SweetAlert notifications display correctly
- [ ] Loading states work properly
- [ ] Page reloads and shows new data

## Setup Instructions

1. **Start XAMPP** - Ensure MySQL is running
2. **Create Database** - Run `setup.sql` in phpMyAdmin
3. **Test Connection** - Open department page in browser
4. **Try Operations** - Create, update, delete departments

## Files Location
```
FINAL_PROJECT_ELECTIVE1/
├── App/
│   ├── Config/
│   │   └── Database.php          (NEW)
│   ├── Model/
│   │   └── Department.php        (NEW)
│   ├── Controller/
│   │   └── DepartmentController.php (NEW)
│   └── View/
│       └── department.php         (MODIFIED)
└── setup.sql                     (Database schema)
```

## API Response Format

All API responses follow this format:
```json
{
  "success": true/false,
  "message": "Operation message",
  "data": {...} // Optional data
}
```

## Database Table Reference
From `setup.sql`:
```sql
CREATE TABLE departments (
    department_id VARCHAR(10) PRIMARY KEY,
    department_name VARCHAR(150) NOT NULL
);
```

---
**Implementation Complete!** ✅ The backend is ready for CRUD operations with full error handling and user-friendly notifications.
