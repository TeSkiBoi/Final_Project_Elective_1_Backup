# Course Management System - Complete Flow Documentation

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Database Layer (Model)](#database-layer-model)
3. [API Layer (Controller)](#api-layer-controller)
4. [Presentation Layer (View)](#presentation-layer-view)
5. [Complete Data Flow](#complete-data-flow)
6. [API Endpoints](#api-endpoints)
7. [Error Handling](#error-handling)
8. [Security Features](#security-features)

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER INTERFACE (courses.php)                 │
│  - HTML Modals (Create, Update, Delete)                         │
│  - Data Table with Course List                                  │
│  - JavaScript Event Listeners                                   │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  │ AJAX Fetch Requests
                  │ (JSON Payloads)
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│            API LAYER (CourseController.php)                      │
│  - Validates HTTP Requests                                      │
│  - Parses JSON Input                                            │
│  - Validates Required Fields                                   │
│  - Calls Model Methods                                         │
│  - Returns JSON Responses                                      │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  │ Method Calls
                  │ (course_code, course_name, etc.)
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│         DATABASE LAYER (Course Model)                           │
│  - Prepares SQL Queries                                        │
│  - Validates Data Constraints                                  │
│  - Executes Database Operations                                │
│  - Returns Status Results                                      │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  │ SQL Execution
                  │
                  ▼
         ┌─────────────────┐
         │   MySQL Database│
         │   - courses     │
         │   - departments │
         │   - enrollments │
         └─────────────────┘
```

---

## Database Layer (Model)

### File: `App/Model/Course.php`

#### Class: `Course`

```php
class Course {
    private $connection;        // MySQL connection object
    private $table = 'courses'; // Table name
}
```

### Database Table Structure

**courses table:**
```sql
CREATE TABLE courses (
    course_id VARCHAR(10) PRIMARY KEY,      -- Format: C001, C002, etc.
    course_code VARCHAR(20) UNIQUE NOT NULL, -- e.g., CS101, MATH201
    course_name VARCHAR(100) UNIQUE NOT NULL, -- Full course name
    units INT NOT NULL,                      -- Number of units (1-6)
    department_id VARCHAR(10) NOT NULL,      -- Foreign key to departments
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);
```

---

## Model Methods

### 1. **generateCourseId()**

**Purpose:** Auto-generate unique course IDs in format C001, C002, etc.

**Logic Flow:**
```
1. Query database for last course_id
2. Extract numeric part from last ID
3. Increment by 1
4. Pad with zeros to 3 digits
5. Prepend 'C'
6. Return: C001, C002, C003...
```

**Code:**
```php
public function generateCourseId() {
    $query = "SELECT course_id FROM courses ORDER BY course_id DESC LIMIT 1";
    $result = $this->connection->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['course_id'];           // e.g., "C005"
        $number = (int)substr($lastId, 1) + 1; // Extract number and increment
    } else {
        $number = 1; // First course
    }
    
    return 'C' . str_pad($number, 3, '0', STR_PAD_LEFT); // "C001"
}
```

**Example Output:**
- First course: `C001`
- Second course: `C002`
- Tenth course: `C010`

---

### 2. **create($course_code, $course_name, $units, $department_id)**

**Purpose:** Create a new course in the database

**Parameters:**
| Parameter | Type | Example | Validation |
|-----------|------|---------|-----------|
| course_code | string | CS101 | Required, unique |
| course_name | string | Intro to CS | Required, unique |
| units | string | 3 | Required, 1-6 |
| department_id | string | D001 | Required, exists in departments |

**Process Flow:**
```
1. Check if course_code already exists
   ├─ If yes: Return error "Course code already exists"
   └─ If no: Continue

2. Check if course_name already exists
   ├─ If yes: Return error "Course name already exists"
   └─ If no: Continue

3. Generate unique course_id (C001, C002, etc.)

4. INSERT new record into courses table
   ├─ course_id: Auto-generated
   ├─ course_code: Provided
   ├─ course_name: Provided
   ├─ units: Provided
   └─ department_id: Provided

5. Return success with course_id
```

**SQL Query:**
```sql
-- Check duplicate code
SELECT * FROM courses WHERE course_code = ?

-- Check duplicate name
SELECT * FROM courses WHERE course_name = ?

-- Insert new course
INSERT INTO courses (course_id, course_code, course_name, units, department_id)
VALUES (?, ?, ?, ?, ?)
```

**Return Format:**
```php
// Success
[
    'success' => true,
    'message' => 'Course created successfully!',
    'course_id' => 'C001'
]

// Error - Duplicate code
[
    'success' => false,
    'message' => 'Course code already exists. Please use a different code.',
    'error_type' => 'duplicate_code'
]

// Error - Database error
[
    'success' => false,
    'message' => 'Error creating course: ...',
    'error_type' => 'database'
]
```

---

### 3. **getAll()**

**Purpose:** Retrieve all courses with department information

**Process Flow:**
```
1. Execute JOIN query to get courses + departments
2. Collect all results in array
3. Return courses array
```

**SQL Query:**
```sql
SELECT c.*, d.department_name 
FROM courses c 
INNER JOIN departments d ON c.department_id = d.department_id 
ORDER BY c.course_id ASC
```

**Return Format:**
```php
[
    [
        'course_id' => 'C001',
        'course_code' => 'CS101',
        'course_name' => 'Introduction to Computer Science',
        'units' => 3,
        'department_id' => 'D001',
        'department_name' => 'Computer Science'
    ],
    [
        'course_id' => 'C002',
        'course_code' => 'CS102',
        'course_name' => 'Data Structures',
        'units' => 4,
        'department_id' => 'D001',
        'department_name' => 'Computer Science'
    ],
    // ... more courses
]
```

---

### 4. **getById($course_id)**

**Purpose:** Retrieve a single course by ID with department info

**Parameters:**
| Parameter | Type | Example |
|-----------|------|---------|
| course_id | string | C001 |

**SQL Query:**
```sql
SELECT c.*, d.department_name 
FROM courses c 
INNER JOIN departments d ON c.department_id = d.department_id 
WHERE c.course_id = ?
```

**Return Format:**
```php
[
    'course_id' => 'C001',
    'course_code' => 'CS101',
    'course_name' => 'Introduction to Computer Science',
    'units' => 3,
    'department_id' => 'D001',
    'department_name' => 'Computer Science'
]
```

---

### 5. **update($course_id, $course_code, $course_name, $units, $department_id)**

**Purpose:** Update an existing course

**Parameters:**
| Parameter | Type | Example |
|-----------|------|---------|
| course_id | string | C001 |
| course_code | string | CS101 |
| course_name | string | Intro to CS |
| units | string | 3 |
| department_id | string | D001 |

**Process Flow:**
```
1. Check if new course_code exists (excluding current course)
   ├─ If yes: Return error
   └─ If no: Continue

2. Check if new course_name exists (excluding current course)
   ├─ If yes: Return error
   └─ If no: Continue

3. UPDATE courses table
   SET course_code = ?,
       course_name = ?,
       units = ?,
       department_id = ?
   WHERE course_id = ?

4. Return success/failure
```

**SQL Query:**
```sql
-- Check duplicate code (excluding current)
SELECT * FROM courses 
WHERE course_code = ? AND course_id != ?

-- Check duplicate name (excluding current)
SELECT * FROM courses 
WHERE course_name = ? AND course_id != ?

-- Update course
UPDATE courses 
SET course_code = ?, course_name = ?, units = ?, department_id = ? 
WHERE course_id = ?
```

**Return Format:**
```php
[
    'success' => true,
    'message' => 'Course updated successfully!'
]
```

---

### 6. **delete($course_id)**

**Purpose:** Delete a course from database

**Parameters:**
| Parameter | Type | Example |
|-----------|------|---------|
| course_id | string | C001 |

**Process Flow:**
```
1. Check if course has associated enrollments
   ├─ If yes: Return error "Cannot delete - has enrollments"
   └─ If no: Continue

2. DELETE from courses table
   WHERE course_id = ?

3. Return success/failure
```

**SQL Query:**
```sql
-- Check for enrollments
SELECT COUNT(*) as count 
FROM enrollments 
WHERE course_id = ?

-- Delete course
DELETE FROM courses 
WHERE course_id = ?
```

**Return Format:**
```php
// Success
[
    'success' => true,
    'message' => 'Course deleted successfully!'
]

// Error - Has enrollments
[
    'success' => false,
    'message' => 'Cannot delete course. It has associated enrollments.',
    'error_type' => 'constraint'
]
```

---

## API Layer (Controller)

### File: `App/Controller/CourseController.php`

#### Class: `CourseController`

**Purpose:** Handle HTTP requests and route them to model methods

#### Constructor:
```php
public function __construct() {
    $this->courseModel = new Course(); // Initialize Course model
}
```

---

## Controller Methods

### 1. **create() - Create Course API**

**HTTP Request:**
```
POST /App/Controller/CourseController.php?action=create
Content-Type: application/json

{
    "course_code": "CS101",
    "course_name": "Introduction to Computer Science",
    "units": "3",
    "department_id": "D001"
}
```

**Validation Flow:**
```php
1. Check HTTP method is POST
   └─ If not: Return 405 error

2. Parse JSON from request body

3. Validate course_code
   └─ If empty: Return 400 "Course code is required"

4. Validate course_name
   └─ If empty: Return 400 "Course name is required"

5. Validate units
   └─ If empty: Return 400 "Units is required"

6. Validate department_id
   └─ If empty: Return 400 "Department is required"

7. Call model->create(...)

8. Send JSON response
```

**HTTP Response:**
```
201 Created (Success)
{
    "success": true,
    "message": "Course created successfully!",
    "data": {
        "id": "C001"
    }
}

400 Bad Request (Validation Error)
{
    "success": false,
    "message": "Course code is required",
    "data": null
}
```

---

### 2. **update() - Update Course API**

**HTTP Request:**
```
POST /App/Controller/CourseController.php?action=update
Content-Type: application/json

{
    "course_id": "C001",
    "course_code": "CS101",
    "course_name": "Introduction to Computer Science",
    "units": "4",
    "department_id": "D001"
}
```

**Validation Flow:**
```
1. Validate course_id (required)
2. Validate course_code (required, unique)
3. Validate course_name (required, unique)
4. Validate units (required)
5. Validate department_id (required)
6. Call model->update(...)
7. Return JSON response
```

**HTTP Response:**
```
200 OK (Success)
{
    "success": true,
    "message": "Course updated successfully!",
    "data": null
}

400 Bad Request (Error)
{
    "success": false,
    "message": "Course code already exists.",
    "data": null
}
```

---

### 3. **delete() - Delete Course API**

**HTTP Request:**
```
POST /App/Controller/CourseController.php?action=delete
Content-Type: application/json

{
    "course_id": "C001"
}
```

**Validation Flow:**
```
1. Validate HTTP method is POST
2. Validate course_id is provided
3. Call model->delete(...)
4. Return JSON response
```

**HTTP Response:**
```
200 OK (Success)
{
    "success": true,
    "message": "Course deleted successfully!",
    "data": null
}

400 Bad Request (Error)
{
    "success": false,
    "message": "Cannot delete course. It has associated enrollments.",
    "data": null
}
```

---

### 4. **sendResponse($success, $message, $data, $status_code)**

**Purpose:** Standardized JSON response formatter

**Parameters:**
| Parameter | Type | Default |
|-----------|------|---------|
| success | boolean | - |
| message | string | - |
| data | array/null | null |
| status_code | int | 200 |

**Functionality:**
```php
1. Set Content-Type header to application/json
2. Set HTTP status code
3. Echo JSON-encoded response
4. Exit execution
```

**Usage Examples:**
```php
// Success response
$this->sendResponse(true, 'Course created!', ['id' => 'C001'], 201);

// Error response
$this->sendResponse(false, 'Validation error', null, 400);

// Server error
$this->sendResponse(false, 'Database error', null, 500);
```

---

### 5. **API Routing Handler**

**Location:** Bottom of CourseController.php

**Purpose:** Route incoming requests to appropriate controller methods

**Routing Logic:**
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    $controller = new CourseController();
    
    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            // Invalid action
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
```

**URL Examples:**
```
POST /App/Controller/CourseController.php?action=create
POST /App/Controller/CourseController.php?action=update
POST /App/Controller/CourseController.php?action=delete
```

---

## Presentation Layer (View)

### File: `App/View/courses.php`

---

## PHP Backend (courses.php)

### Security & Authentication

```php
// 1. Authentication Protection
require_once __DIR__ . '/middleware/ProtectAuth.php';
// Ensures user is logged in and session is valid

// 2. RBAC Protection
require_once __DIR__ . '/middleware/RBACProtect.php';
requireRole(1); // Only Admin (role_id = 1) can access
```

### Data Loading

```php
// Load Course model
require_once __DIR__ . '/../Model/Course.php';
$courseModel = new Course();

// Load Department model
require_once __DIR__ . '/../Model/Department.php';
$departmentModel = new Department();

// Retrieve all data
$courses = $courseModel->getAll();      // All courses with departments
$departments = $departmentModel->getAll(); // Department dropdown options
```

**Data Retrieved:**
```
$courses = [
    ['course_id' => 'C001', 'course_code' => 'CS101', ...],
    ['course_id' => 'C002', 'course_code' => 'CS102', ...],
    // ... more courses
]

$departments = [
    ['department_id' => 'D001', 'department_name' => 'Computer Science'],
    ['department_id' => 'D002', 'department_name' => 'Mathematics'],
    // ... more departments
]
```

---

## HTML Structure

### Course Table

```html
<table id="table" class="table table-bordered">
    <thead>
        <tr>
            <th>Course ID</th>          <!-- From $course['course_id'] -->
            <th>Course Code</th>        <!-- From $course['course_code'] -->
            <th>Course Name</th>        <!-- From $course['course_name'] -->
            <th>Units</th>              <!-- From $course['units'] -->
            <th>Department</th>         <!-- From $course['department_name'] -->
            <th>Action</th>             <!-- Edit/Delete buttons -->
        </tr>
    </thead>
    <tbody id="courseTableBody">
        <?php foreach ($courses as $course): ?>
        <tr>
            <td><?php echo $course['course_id']; ?></td>
            <td><?php echo $course['course_code']; ?></td>
            <td><?php echo $course['course_name']; ?></td>
            <td><?php echo $course['units']; ?></td>
            <td><?php echo $course['department_name']; ?></td>
            <td>
                <button class="btn btn-sm btn-warning" 
                        data-bs-toggle="modal" 
                        data-bs-target="#updateCourseModal">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteCourseModal">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

---

### Bootstrap Modals

#### 1. Create Course Modal
```html
<!-- id="createCourseModal" -->
<div class="modal" id="createCourseModal">
    <form id="createCourseForm">
        <input type="text" id="course_code" name="course_code">
        <input type="text" id="course_name" name="course_name">
        <input type="number" id="units" name="units">
        <select id="department_id" name="department_id">
            <!-- Populated with PHP departments -->
        </select>
    </form>
</div>
```

#### 2. Update Course Modal
```html
<!-- id="updateCourseModal" -->
<div class="modal" id="updateCourseModal">
    <form id="updateCourseForm">
        <input type="hidden" id="course_id">
        <input type="text" id="course_code_edit">
        <input type="text" id="course_name_edit">
        <input type="number" id="units_edit">
        <select id="department_id_edit">
            <!-- Populated with PHP departments -->
        </select>
    </form>
</div>
```

#### 3. Delete Course Modal
```html
<!-- id="deleteCourseModal" -->
<div class="modal" id="deleteCourseModal">
    <form id="deleteCourseForm">
        <input type="hidden" id="delete_course_id">
        <input type="text" id="delete_course_name" disabled>
        <input type="text" id="confirm_delete_course" 
               placeholder="Type course code to confirm">
    </form>
</div>
```

---

## JavaScript Frontend

### JavaScript Architecture

```
┌────────────────────────────────────────────────┐
│         CONSTANTS & CONFIGURATION              │
│  const API_URL = '../../App/Controller/...'    │
└────────────────────┬───────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────┐
│    FORM EVENT LISTENERS                        │
│  - Create form submit                          │
│  - Update form submit                          │
│  - Delete form submit                          │
└────────────────────┬───────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────┐
│    DATA VALIDATION                             │
│  - Check required fields                       │
│  - Display SweetAlert errors                   │
└────────────────────┬───────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────┐
│    FETCH API REQUEST                           │
│  - POST to CourseController                    │
│  - Send JSON payload                           │
│  - Parse JSON response                         │
└────────────────────┬───────────────────────────┘
                     │
                     ▼
┌────────────────────────────────────────────────┐
│    USER FEEDBACK                               │
│  - SweetAlert success/error notifications      │
│  - Close modals on success                     │
│  - Reload page to show changes                 │
└────────────────────────────────────────────────┘
```

---

### API_URL Configuration

```javascript
const API_URL = '../../App/Controller/CourseController.php';
// Relative path from: App/View/courses.php
// Points to: App/Controller/CourseController.php
```

---

### 1. Create Course Form Handler

**Trigger:** User submits `#createCourseForm`

**Workflow:**
```javascript
1. Prevent default form submission
   event.preventDefault()

2. Collect form data
   courseCode = document.getElementById('course_code').value
   courseName = document.getElementById('course_name').value
   units = document.getElementById('units').value
   departmentId = document.getElementById('department_id').value

3. Validate each field
   if (!courseCode) {
       Swal.fire({
           icon: 'warning',
           title: 'Validation Error',
           text: 'Please enter a Course Code.'
       })
       return
   }
   // ... repeat for other fields

4. Show loading state
   submitBtn.disabled = true
   submitBtn.innerHTML = '<span class="spinner-border">Creating...</span>'

5. Make API request
   fetch(API_URL + '?action=create', {
       method: 'POST',
       headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify({
           course_code: courseCode,
           course_name: courseName,
           units: units,
           department_id: departmentId
       })
   })

6. Parse response
   const result = await response.json()

7. Handle success
   if (result.success) {
       Swal.fire({
           icon: 'success',
           title: 'Success!',
           text: result.message
       }).then(() => {
           document.getElementById('createCourseForm').reset()
           modal.hide()
           location.reload()
       })
   }

8. Handle error
   else {
       Swal.fire({
           icon: 'error',
           title: 'Error',
           text: result.message
       })
   }
```

**API Call:**
```
POST /App/Controller/CourseController.php?action=create
{
    "course_code": "CS101",
    "course_name": "Intro to CS",
    "units": "3",
    "department_id": "D001"
}
```

---

### 2. Update Course Form Handler

**Trigger:** User submits `#updateCourseForm`

**Workflow:**
```javascript
1. Collect data from update form
   courseId = document.getElementById('course_id').value
   courseCode = document.getElementById('course_code_edit').value
   courseName = document.getElementById('course_name_edit').value
   units = document.getElementById('units_edit').value
   departmentId = document.getElementById('department_id_edit').value

2. Validate all fields

3. Show loading state

4. Make API request
   fetch(API_URL + '?action=update', {
       method: 'POST',
       body: JSON.stringify({
           course_id: courseId,
           course_code: courseCode,
           course_name: courseName,
           units: units,
           department_id: departmentId
       })
   })

5. Handle response
   if success: show alert, close modal, reload page
   if error: show error alert
```

---

### 3. Delete Course Form Handler

**Trigger:** User submits `#deleteCourseForm`

**Workflow:**
```javascript
1. Collect data
   courseId = document.getElementById('delete_course_id').value
   courseCode = document.getElementById('delete_course_name').value
   confirmText = document.getElementById('confirm_delete_course').value

2. Validate confirmation
   if (confirmText !== courseCode) {
       Swal.fire({
           icon: 'warning',
           title: 'Confirmation Failed',
           text: 'The course code does not match.'
       })
       return
   }

3. Show loading state

4. Make API request
   fetch(API_URL + '?action=delete', {
       method: 'POST',
       body: JSON.stringify({
           course_id: courseId
       })
   })

5. Handle response
   if success: show alert, close modal, reload page
   if error: show error alert (e.g., "Cannot delete - has enrollments")
```

---

### 4. Edit Button Click Handler

**Trigger:** User clicks Edit button

**Code:**
```javascript
document.addEventListener('click', function(e) {
    if (e.target.closest('button[data-bs-target="#updateCourseModal"]')) {
        const row = e.target.closest('tr'); // Get table row
        
        // Extract data from table cells
        const courseId = row.querySelector('td:nth-child(1)').textContent.trim()
        const courseCode = row.querySelector('td:nth-child(2)').textContent.trim()
        const courseName = row.querySelector('td:nth-child(3)').textContent.trim()
        const units = row.querySelector('td:nth-child(4)').textContent.trim()
        const departmentName = row.querySelector('td:nth-child(5)').textContent.trim()
        
        // Populate update modal
        document.getElementById('course_id').value = courseId
        document.getElementById('course_code_edit').value = courseCode
        document.getElementById('course_name_edit').value = courseName
        document.getElementById('units_edit').value = units
        
        // Find and select department by name
        const deptSelect = document.getElementById('department_id_edit')
        for (let option of deptSelect.options) {
            if (option.textContent.trim() === departmentName) {
                deptSelect.value = option.value
                break
            }
        }
    }
})
```

**Table Cell References:**
```
td:nth-child(1) = Course ID (C001)
td:nth-child(2) = Course Code (CS101)
td:nth-child(3) = Course Name (Intro to CS)
td:nth-child(4) = Units (3)
td:nth-child(5) = Department (Computer Science)
```

---

### 5. Delete Button Click Handler

**Trigger:** User clicks Delete button

**Code:**
```javascript
document.addEventListener('click', function(e) {
    if (e.target.closest('button[data-bs-target="#deleteCourseModal"]')) {
        const row = e.target.closest('tr')
        
        const courseId = row.querySelector('td:nth-child(1)').textContent.trim()
        const courseCode = row.querySelector('td:nth-child(2)').textContent.trim()
        
        // Populate delete modal
        document.getElementById('delete_course_id').value = courseId
        document.getElementById('delete_course_name').value = courseCode
        document.getElementById('confirm_delete_course').value = ''
    }
})
```

**User Experience:**
```
1. User clicks Delete button in any row
2. Delete modal opens
3. User sees course code they're about to delete
4. User must type the course code to confirm
5. If matches: Delete button becomes active
6. If confirmed: API call deletes course
```

---

### 6. Modal Reset Handlers

**Purpose:** Clear form fields when modals close

```javascript
// Reset create form when modal closes
document.getElementById('createCourseModal').addEventListener('hide.bs.modal', function() {
    document.getElementById('createCourseForm').reset()
})

// Reset update form when modal closes
document.getElementById('updateCourseModal').addEventListener('hide.bs.modal', function() {
    document.getElementById('updateCourseForm').reset()
})

// Reset delete form when modal closes
document.getElementById('deleteCourseModal').addEventListener('hide.bs.modal', function() {
    document.getElementById('deleteCourseForm').reset()
})
```

---

## Complete Data Flow

### Create Course Flow

```
1. USER ACTION
   ├─ Click "Add New Course" button
   └─ Creates modal opens (#createCourseModal)

2. USER INPUT
   ├─ Enter Course Code: "CS101"
   ├─ Enter Course Name: "Intro to CS"
   ├─ Enter Units: "3"
   ├─ Select Department: "Computer Science"
   └─ Click Create button

3. JAVASCRIPT VALIDATION
   ├─ Check course_code is not empty
   ├─ Check course_name is not empty
   ├─ Check units is not empty
   ├─ Check department_id is selected
   └─ All valid? Continue to step 4

4. JAVASCRIPT FETCH REQUEST
   ├─ POST to: /App/Controller/CourseController.php?action=create
   ├─ Headers: { 'Content-Type': 'application/json' }
   └─ Body: {
   │   "course_code": "CS101",
   │   "course_name": "Intro to CS",
   │   "units": "3",
   │   "department_id": "D001"
   │ }

5. CONTROLLER VALIDATION (CourseController::create)
   ├─ Check HTTP method is POST ✓
   ├─ Parse JSON data ✓
   ├─ Validate course_code not empty ✓
   ├─ Validate course_name not empty ✓
   ├─ Validate units not empty ✓
   ├─ Validate department_id not empty ✓
   └─ All valid? Call model method

6. MODEL PROCESSING (Course::create)
   ├─ Check if course_code already exists
   │  ├─ Query: SELECT * FROM courses WHERE course_code = ?
   │  ├─ Found? Return error "Code already exists"
   │  └─ Not found? Continue
   ├─ Check if course_name already exists
   │  ├─ Query: SELECT * FROM courses WHERE course_name = ?
   │  ├─ Found? Return error "Name already exists"
   │  └─ Not found? Continue
   ├─ Generate unique course_id
   │  ├─ Query: SELECT course_id FROM courses ORDER BY course_id DESC LIMIT 1
   │  ├─ Last ID: "C001"
   │  └─ Generated ID: "C002"
   ├─ Insert new course
   │  ├─ Query: INSERT INTO courses (...) VALUES (...)
   │  ├─ Values: C002, CS101, Intro to CS, 3, D001
   │  └─ Success? Return success response
   └─ Return array with success and course_id

7. CONTROLLER RESPONSE
   ├─ Call sendResponse(true, "Course created!", {...}, 201)
   ├─ Set HTTP 201 Created
   ├─ Output JSON:
   │  {
   │    "success": true,
   │    "message": "Course created successfully!",
   │    "data": { "id": "C002" }
   │  }
   └─ Exit

8. JAVASCRIPT RESPONSE HANDLING
   ├─ Parse response JSON
   ├─ Check result.success
   ├─ If success:
   │  ├─ Show SweetAlert success notification
   │  ├─ Reset form fields
   │  ├─ Close modal
   │  ├─ Reload page (location.reload)
   │  └─ Table now shows new C002 course
   └─ If error:
      └─ Show SweetAlert error notification

9. PAGE STATE
   ├─ Database updated: New course C002 added
   ├─ Page reloaded: Fresh data from database
   ├─ Table updated: Shows all courses including C002
   └─ User feedback: Success message displayed
```

### Update Course Flow

```
1. USER ACTION
   ├─ Click Edit button in course row (C001)
   └─ Update modal opens

2. JAVASCRIPT POPULATES MODAL
   ├─ Extracts from table row:
   │  ├─ course_id: "C001"
   │  ├─ course_code: "CS101"
   │  ├─ course_name: "Intro to CS"
   │  ├─ units: "3"
   │  └─ department_id: "D001" (by name match)
   └─ Sets form field values

3. USER MODIFICATION
   ├─ Changes units from 3 to 4
   └─ Clicks Update button

4. JAVASCRIPT VALIDATION & REQUEST
   ├─ Collects updated values
   ├─ Validates all fields
   ├─ POST to: /App/Controller/CourseController.php?action=update
   └─ Body: {
      │   "course_id": "C001",
      │   "course_code": "CS101",
      │   "course_name": "Intro to CS",
      │   "units": "4",
      │   "department_id": "D001"
      │ }

5. CONTROLLER PROCESSING
   ├─ Validates all fields
   └─ Calls model->update()

6. MODEL PROCESSING
   ├─ Check code not duplicate (excluding C001)
   ├─ Check name not duplicate (excluding C001)
   ├─ UPDATE courses SET ... WHERE course_id = C001
   └─ Return success

7. DATABASE UPDATE
   └─ Course C001 units: 3 → 4

8. PAGE RELOAD
   └─ Table shows updated course
```

### Delete Course Flow

```
1. USER ACTION
   ├─ Click Delete button for course C001
   └─ Delete modal opens

2. MODAL POPULATED
   ├─ Shows message: "Course to Delete: CS101"
   ├─ Shows input: "Type course code to confirm"
   └─ Shows warning: Cannot be undone

3. USER CONFIRMATION
   ├─ Types "CS101" in confirmation field
   ├─ Clicks Delete button
   └─ JavaScript validates

4. JAVASCRIPT VALIDATION
   ├─ if (confirmText !== courseCode)
   │  ├─ Comparison: "CS101" === "CS101" ✓
   │  └─ Valid? Continue
   └─ POST to: /App/Controller/CourseController.php?action=delete

5. CONTROLLER & MODEL
   ├─ Validates course_id provided
   ├─ Calls model->delete(C001)
   ├─ Model checks for enrollments
   │  ├─ Query: SELECT COUNT(*) FROM enrollments WHERE course_id = C001
   │  ├─ Count = 0? Can delete
   │  └─ Count > 0? Error: "Has enrollments"
   ├─ DELETE FROM courses WHERE course_id = C001
   └─ Return success

6. DATABASE UPDATE
   └─ Course C001 deleted (if no enrollments)

7. PAGE STATE
   ├─ Modal closes
   ├─ Page reloads
   └─ Course C001 no longer in table
```

---

## API Endpoints

### 1. Create Course

**Endpoint:** `POST /App/Controller/CourseController.php?action=create`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
    "course_code": "CS101",
    "course_name": "Introduction to Computer Science",
    "units": "3",
    "department_id": "D001"
}
```

**Success Response (201 Created):**
```json
{
    "success": true,
    "message": "Course created successfully!",
    "data": {
        "id": "C001"
    }
}
```

**Error Response (400 Bad Request):**
```json
{
    "success": false,
    "message": "Course code already exists. Please use a different code.",
    "data": null
}
```

---

### 2. Update Course

**Endpoint:** `POST /App/Controller/CourseController.php?action=update`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
    "course_id": "C001",
    "course_code": "CS101",
    "course_name": "Introduction to Computer Science",
    "units": "4",
    "department_id": "D001"
}
```

**Success Response (200 OK):**
```json
{
    "success": true,
    "message": "Course updated successfully!",
    "data": null
}
```

**Error Response (400 Bad Request):**
```json
{
    "success": false,
    "message": "Course name already exists. Please use a different name.",
    "data": null
}
```

---

### 3. Delete Course

**Endpoint:** `POST /App/Controller/CourseController.php?action=delete`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
    "course_id": "C001"
}
```

**Success Response (200 OK):**
```json
{
    "success": true,
    "message": "Course deleted successfully!",
    "data": null
}
```

**Error Response - Has Enrollments (400 Bad Request):**
```json
{
    "success": false,
    "message": "Cannot delete course. It has associated enrollments.",
    "data": null
}
```

**Error Response - Database Error (400 Bad Request):**
```json
{
    "success": false,
    "message": "Error deleting course: [database error details]",
    "data": null
}
```

---

## Error Handling

### Validation Errors

#### Frontend (JavaScript)
```javascript
if (!courseCode) {
    Swal.fire({
        icon: 'warning',
        title: 'Validation Error',
        text: 'Please enter a Course Code.',
        confirmButtonColor: '#6ec207'
    })
    return
}
```

#### Backend (Controller)
```php
if (!isset($data['course_code']) || empty(trim($data['course_code']))) {
    $this->sendResponse(false, 'Course code is required', null, 400);
    return;
}
```

### Business Logic Errors

#### Duplicate Entry
```php
// Model checks
$checkCodeQuery = "SELECT * FROM courses WHERE course_code = ?";
if ($result->num_rows > 0) {
    return [
        'success' => false,
        'message' => 'Course code already exists. Please use a different code.',
        'error_type' => 'duplicate_code'
    ];
}
```

#### Constraint Violation
```php
// Delete with enrollments check
$checkQuery = "SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?";
if ($row['count'] > 0) {
    return [
        'success' => false,
        'message' => 'Cannot delete course. It has associated enrollments.',
        'error_type' => 'constraint'
    ];
}
```

### Network Errors
```javascript
try {
    const response = await fetch(API_URL + '?action=create', {...})
} catch (error) {
    console.error('Error:', error)
    Swal.fire({
        icon: 'error',
        title: 'Network Error',
        text: 'Failed to connect to the server.',
        confirmButtonColor: '#dc3545'
    })
}
```

---

## Security Features

### 1. Authentication
```php
require_once __DIR__ . '/middleware/ProtectAuth.php';
// Validates user is logged in
// Validates session exists and is valid
// Checks user status is active
```

### 2. Authorization (RBAC)
```php
require_once __DIR__ . '/middleware/RBACProtect.php';
requireRole(1); // Only Admin (role_id = 1) can access
```

### 3. SQL Injection Prevention
```php
// Prepared Statements with Parameterized Queries
$query = "SELECT * FROM courses WHERE course_code = ?";
$stmt = $this->connection->prepare($query);
$stmt->bind_param("s", $course_code); // Bind parameter safely
$stmt->execute();
```

**Never vulnerable to SQL injection:**
```
❌ $query = "SELECT * FROM courses WHERE course_code = '$code'";
✓  $query = "SELECT * FROM courses WHERE course_code = ?";
   $stmt->bind_param("s", $course_code);
```

### 4. Input Validation
```php
// Remove whitespace
$course_code = trim($data['course_code']);

// Check required fields
if (!isset($data['course_code']) || empty(trim($data['course_code']))) {
    $this->sendResponse(false, 'Course code is required', null, 400);
}

// Validate constraints
if ($result->num_rows > 0) {
    // Duplicate found
}
```

### 5. Output Encoding (XSS Prevention)
```php
// In update modal (with htmlspecialchars for safety)
<option value="<?php echo htmlspecialchars($dept['department_id']); ?>">
    <?php echo htmlspecialchars($dept['department_name']); ?>
</option>
```

### 6. CSRF Protection
```javascript
// Fetch API automatically includes credentials for same-origin requests
// No CSRF tokens needed for same-domain AJAX
```

### 7. HTTP Status Codes
```php
// 201 Created - Resource successfully created
$this->sendResponse($result['success'], $result['message'], $data, 201);

// 200 OK - Request succeeded
$this->sendResponse(true, 'Updated!', null, 200);

// 400 Bad Request - Validation error
$this->sendResponse(false, 'Validation error', null, 400);

// 405 Method Not Allowed - Wrong HTTP method
$this->sendResponse(false, 'Invalid request method', null, 405);
```

---

## Testing Checklist

### Create Course Tests
- [ ] Create course with valid data
- [ ] Prevent duplicate course code
- [ ] Prevent duplicate course name
- [ ] Validate all required fields
- [ ] Test with different departments
- [ ] Test units range (1-6)

### Update Course Tests
- [ ] Update existing course
- [ ] Prevent code conflict with other courses
- [ ] Prevent name conflict with other courses
- [ ] Verify data persists in database
- [ ] Test modal auto-population

### Delete Course Tests
- [ ] Delete course without enrollments
- [ ] Prevent deletion of course with enrollments
- [ ] Verify confirmation requirement
- [ ] Verify page reloads after deletion

### Security Tests
- [ ] Test RBAC - non-admin cannot access page
- [ ] Test authentication - logged out user redirected
- [ ] Test SQL injection prevention
- [ ] Test XSS prevention

### UI/UX Tests
- [ ] Modal dialogs open/close correctly
- [ ] Form validation messages display
- [ ] Success/error notifications appear
- [ ] Loading states show correctly
- [ ] Page reloads show updated data

---

## Code Examples

### Example: Complete Create Course Request

**JavaScript:**
```javascript
const response = await fetch('../../App/Controller/CourseController.php?action=create', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        course_code: 'CS101',
        course_name: 'Introduction to Computer Science',
        units: '3',
        department_id: 'D001'
    })
})

const result = await response.json()
console.log(result)
// Output:
// {
//   success: true,
//   message: "Course created successfully!",
//   data: { id: "C001" }
// }
```

---

### Example: Complete Update Course Request

**JavaScript:**
```javascript
const response = await fetch('../../App/Controller/CourseController.php?action=update', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        course_id: 'C001',
        course_code: 'CS101',
        course_name: 'Intro to CS',
        units: '4',
        department_id: 'D001'
    })
})

const result = await response.json()
```

---

### Example: Complete Delete Course Request

**JavaScript:**
```javascript
const response = await fetch('../../App/Controller/CourseController.php?action=delete', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        course_id: 'C001'
    })
})

const result = await response.json()
```

---

## Summary

The Course Management System follows a clean three-tier architecture:

1. **Model Layer (Course.php)**
   - Handles all database operations
   - Implements CRUD methods
   - Validates data constraints
   - Returns structured responses

2. **Controller Layer (CourseController.php)**
   - Receives HTTP requests
   - Validates request parameters
   - Routes to appropriate model methods
   - Sends JSON responses

3. **View Layer (courses.php)**
   - Displays course data in table
   - Provides CRUD modals
   - Handles user interactions with JavaScript
   - Makes AJAX requests to API

**Data Flow:** User → View (JavaScript) → Controller (API) → Model (Database) → Response

**All operations are secured with authentication, authorization, input validation, and SQL injection prevention.**
