# Dashboard Visualization System - Complete Documentation

## Overview

The Dashboard Visualization System provides comprehensive data analytics and graphical representations of your institution's data using Chart.js library. It displays real-time metrics through various chart types: Bar Charts, Pie Charts, Line Charts, and Doughnut Charts.

---

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│         Dashboard View (index.php)                          │
│  - Loads Dashboard Model                                   │
│  - Includes dashboard.php template                         │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  │ Calls Model Methods
                  ▼
┌─────────────────────────────────────────────────────────────┐
│         Dashboard Model (dashboard.php)                     │
│  - getCountStudent()                                       │
│  - getCountCourse()                                        │
│  - getCountDepartment()                                    │
│  - getCountUser()                                          │
│  - getCoursesByDepartment() [NEW - Bar Chart]              │
│  - getStudentsByDepartment() [NEW - Pie Chart]             │
│  - getEnrollmentsTrend() [NEW - Line Chart]                │
│  - getEnrollmentStatus() [NEW - Doughnut Chart]            │
│  - getUserRoleDistribution() [NEW - Bar Chart]             │
│  - getTopCoursesByEnrollment() [NEW - Horizontal Bar]      │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  │ SQL Queries
                  ▼
         ┌─────────────────┐
         │  MySQL Database │
         │  - courses      │
         │  - students     │
         │  - departments  │
         │  - enrollments  │
         │  - users        │
         │  - roles        │
         └─────────────────┘
```

---

## Database Model - New Methods

### File: `App/Model/dashboard.php`

#### 1. **getCoursesByDepartment()**

**Purpose:** Get count of courses per department for bar chart visualization

**SQL Query:**
```sql
SELECT d.department_name, COUNT(c.course_id) as course_count 
FROM departments d 
LEFT JOIN courses c ON d.department_id = c.department_id 
GROUP BY d.department_id, d.department_name 
ORDER BY course_count DESC
```

**Return Format:**
```php
[
    ['department_name' => 'Computer Science', 'course_count' => 5],
    ['department_name' => 'Mathematics', 'course_count' => 4],
    ['department_name' => 'Physics', 'course_count' => 3],
    // ... more departments
]
```

**Chart Type:** Bar Chart
**Use Case:** Display which departments have the most courses

---

#### 2. **getStudentsByDepartment()**

**Purpose:** Get count of students per department for pie chart

**SQL Query:**
```sql
SELECT d.department_name, COUNT(s.student_id) as student_count 
FROM departments d 
LEFT JOIN students s ON d.department_id = s.department_id 
GROUP BY d.department_id, d.department_name 
HAVING student_count > 0 
ORDER BY student_count DESC
```

**Return Format:**
```php
[
    ['department_name' => 'Computer Science', 'student_count' => 120],
    ['department_name' => 'Engineering', 'student_count' => 95],
    ['department_name' => 'Business', 'student_count' => 80],
    // ... more departments
]
```

**Chart Type:** Pie Chart
**Use Case:** Show student distribution across departments

---

#### 3. **getEnrollmentsTrend()**

**Purpose:** Get monthly enrollment trend for the current year (line chart)

**SQL Query:**
```sql
SELECT 
    MONTH(e.created_at) as month, 
    YEAR(e.created_at) as year,
    COUNT(e.enrollment_id) as enrollment_count,
    DATE_FORMAT(e.created_at, '%b %Y') as month_year
FROM enrollments e
WHERE YEAR(e.created_at) = YEAR(CURDATE())
GROUP BY YEAR(e.created_at), MONTH(e.created_at)
ORDER BY YEAR(e.created_at), MONTH(e.created_at)
```

**Return Format:**
```php
[
    [
        'month' => 1,
        'year' => 2025,
        'enrollment_count' => 45,
        'month_year' => 'Jan 2025'
    ],
    [
        'month' => 2,
        'year' => 2025,
        'enrollment_count' => 67,
        'month_year' => 'Feb 2025'
    ],
    // ... more months
]
```

**Chart Type:** Line Chart with smooth trend
**Use Case:** Display enrollment trends over time within current year

---

#### 4. **getUserRoleDistribution()**

**Purpose:** Get user count by role type for bar chart

**SQL Query:**
```sql
SELECT r.role_name, COUNT(u.user_id) as user_count 
FROM roles r 
LEFT JOIN users u ON r.role_id = u.role_id 
GROUP BY r.role_id, r.role_name 
ORDER BY user_count DESC
```

**Return Format:**
```php
[
    ['role_name' => 'Admin', 'user_count' => 5],
    ['role_name' => 'Staff', 'user_count' => 12],
    ['role_name' => 'Student', 'user_count' => 450],
    // ... more roles
]
```

**Chart Type:** Bar Chart
**Use Case:** Show user distribution by role

---

#### 5. **getEnrollmentStatus()**

**Purpose:** Get enrollment count by status (active, completed, dropped)

**SQL Query:**
```sql
SELECT 'Active Enrollments' as status, COUNT(e.enrollment_id) as count 
FROM enrollments e 
WHERE e.status = 'active'
UNION ALL
SELECT 'Completed Enrollments' as status, COUNT(e.enrollment_id) as count 
FROM enrollments e 
WHERE e.status = 'completed'
UNION ALL
SELECT 'Dropped Enrollments' as status, COUNT(e.enrollment_id) as count 
FROM enrollments e 
WHERE e.status = 'dropped'
```

**Return Format:**
```php
[
    ['status' => 'Active Enrollments', 'count' => 350],
    ['status' => 'Completed Enrollments', 'count' => 280],
    ['status' => 'Dropped Enrollments', 'count' => 45]
]
```

**Chart Type:** Doughnut Chart
**Use Case:** Show enrollment status distribution

---

#### 6. **getTopCoursesByEnrollment()**

**Purpose:** Get top 10 courses with highest enrollment

**SQL Query:**
```sql
SELECT c.course_code, c.course_name, COUNT(e.enrollment_id) as enrollment_count 
FROM courses c 
LEFT JOIN enrollments e ON c.course_id = e.course_id 
GROUP BY c.course_id, c.course_code, c.course_name 
ORDER BY enrollment_count DESC 
LIMIT 10
```

**Return Format:**
```php
[
    ['course_code' => 'CS101', 'course_name' => 'Intro to CS', 'enrollment_count' => 85],
    ['course_code' => 'CS102', 'course_name' => 'Data Structures', 'enrollment_count' => 72],
    ['course_code' => 'MATH201', 'course_name' => 'Calculus I', 'enrollment_count' => 68],
    // ... top 10 courses
]
```

**Chart Type:** Horizontal Bar Chart
**Use Case:** Identify most popular courses

---

## Dashboard Template - Charts Implementation

### File: `App/View/template/dashboard.php`

#### Data Loading (PHP Section)

```php
<!-- Courses by Department Data -->
const coursesDeptData = <?php 
    $coursesData = $dashboardModel->getCoursesByDepartment();
    echo json_encode($coursesData ?? []); 
?>;

<!-- Students by Department Data -->
const studentsDeptData = <?php 
    $studentsData = $dashboardModel->getStudentsByDepartment();
    echo json_encode($studentsData ?? []); 
?>;

<!-- Enrollments Trend Data -->
const enrollmentsTrendData = <?php 
    $trendData = $dashboardModel->getEnrollmentsTrend();
    echo json_encode($trendData ?? []); 
?>;

<!-- Enrollment Status Data -->
const enrollmentStatusData = <?php 
    $statusData = $dashboardModel->getEnrollmentStatus();
    echo json_encode($statusData ?? []); 
?>;

<!-- User Role Distribution Data -->
const roleDistributionData = <?php 
    $roleData = $dashboardModel->getUserRoleDistribution();
    echo json_encode($roleData ?? []); 
?>;

<!-- Top Courses Data -->
const topCoursesData = <?php 
    $topCourses = $dashboardModel->getTopCoursesByEnrollment();
    echo json_encode($topCourses ?? []); 
?>;
```

---

## Chart Visualizations

### 1. Bar Chart: Courses by Department

**Location:** Top-left card
**Canvas ID:** `coursesDepartmentChart`
**Chart Type:** `bar`

**Features:**
- Displays courses count for each department
- Vertical bars
- Color-coded by department
- Responsive design
- Y-axis shows count (starts at 0)

**Configuration:**
```javascript
{
    type: 'bar',
    data: {
        labels: ['Computer Science', 'Mathematics', 'Physics'],
        datasets: [{
            label: 'Number of Courses',
            data: [5, 4, 3],
            backgroundColor: ['rgba(54, 162, 235, 0.7)', ...],
            borderColor: ['rgba(54, 162, 235, 1)', ...],
            borderWidth: 2
        }]
    }
}
```

---

### 2. Pie Chart: Students Distribution by Department

**Location:** Top-right card
**Canvas ID:** `studentsDepartmentChart`
**Chart Type:** `pie`

**Features:**
- Shows student percentage per department
- Pie slices with different colors
- Legend on the right
- Proportional sizing based on student count

**Configuration:**
```javascript
{
    type: 'pie',
    data: {
        labels: ['Computer Science', 'Engineering', 'Business'],
        datasets: [{
            data: [120, 95, 80],
            backgroundColor: ['rgba(54, 162, 235, 0.7)', ...],
            borderColor: ['rgba(54, 162, 235, 1)', ...],
            borderWidth: 2
        }]
    }
}
```

---

### 3. Line Chart: Enrollments Trend

**Location:** Full-width card below top row
**Canvas ID:** `enrollmentsTrendChart`
**Chart Type:** `line`

**Features:**
- Shows enrollment trend across months
- Smooth curved line
- Filled area under line
- Interactive points showing exact values
- X-axis: Month labels (Jan 2025, Feb 2025, etc.)
- Y-axis: Enrollment count

**Configuration:**
```javascript
{
    type: 'line',
    data: {
        labels: ['Jan 2025', 'Feb 2025', 'Mar 2025'],
        datasets: [{
            label: 'Monthly Enrollments',
            data: [45, 67, 78],
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            pointBorderColor: 'rgba(255, 255, 255, 1)',
            pointBorderWidth: 2
        }]
    }
}
```

---

### 4. Doughnut Chart: Enrollment Status Distribution

**Location:** Bottom-left card
**Canvas ID:** `enrollmentStatusChart`
**Chart Type:** `doughnut`

**Features:**
- Shows enrollment status breakdown
- Doughnut shape (pie with hole in center)
- Three categories: Active (Green), Completed (Blue), Dropped (Red)
- Legend on the right

**Color Scheme:**
- Green: Active Enrollments
- Blue: Completed Enrollments
- Red: Dropped Enrollments

**Configuration:**
```javascript
{
    type: 'doughnut',
    data: {
        labels: ['Active Enrollments', 'Completed', 'Dropped'],
        datasets: [{
            data: [350, 280, 45],
            backgroundColor: [
                'rgba(75, 192, 75, 0.7)',     // Green
                'rgba(54, 162, 235, 0.7)',    // Blue
                'rgba(255, 99, 132, 0.7)'     // Red
            ]
        }]
    }
}
```

---

### 5. Bar Chart: User Role Distribution

**Location:** Bottom-right card
**Canvas ID:** `roleDistributionChart`
**Chart Type:** `bar`

**Features:**
- Displays user count by role
- Vertical bars
- Shows Admin, Staff, Student distribution
- Y-axis starts at 0

**Configuration:**
```javascript
{
    type: 'bar',
    data: {
        labels: ['Admin', 'Staff', 'Student'],
        datasets: [{
            label: 'Number of Users',
            data: [5, 12, 450],
            backgroundColor: ['rgba(54, 162, 235, 0.7)', ...]
        }]
    }
}
```

---

### 6. Horizontal Bar Chart: Top 10 Courses

**Location:** Full-width card at bottom
**Canvas ID:** `topCoursesChart`
**Chart Type:** `bar` with `indexAxis: 'y'`

**Features:**
- Shows 10 most enrolled courses
- Horizontal bars (left to right)
- Course code + name as labels
- Enrollment count on X-axis
- Sorted by enrollment descending

**Configuration:**
```javascript
{
    type: 'bar',
    data: {
        labels: ['CS101 - Intro to CS', 'CS102 - Data Structures', ...],
        datasets: [{
            label: 'Enrollment Count',
            data: [85, 72, 68, ...],
            backgroundColor: ['rgba(54, 162, 235, 0.7)', ...]
        }]
    },
    options: {
        indexAxis: 'y'  // Makes it horizontal
    }
}
```

---

## Color Palette

The system uses a consistent color palette across all charts:

```javascript
const chartColors = [
    'rgba(54, 162, 235, 0.7)',    // Blue
    'rgba(255, 99, 132, 0.7)',    // Red
    'rgba(75, 192, 75, 0.7)',     // Green
    'rgba(255, 206, 86, 0.7)',    // Yellow
    'rgba(153, 102, 255, 0.7)',   // Purple
    'rgba(255, 159, 64, 0.7)',    // Orange
    'rgba(201, 203, 207, 0.7)',   // Grey
    'rgba(255, 99, 255, 0.7)',    // Pink
    'rgba(99, 255, 132, 0.7)',    // Mint
    'rgba(255, 255, 99, 0.7)'     // Light Yellow
];

const borderColors = chartColors.map(c => c.replace('0.7', '1'));
```

**Color Usage:**
- Lighter opacity (0.7) for fill areas
- Darker opacity (1) for borders
- Rotation through palette for multiple datasets

---

## Chart Configuration Options

### Common Options Used

```javascript
options: {
    responsive: true,              // Responsive to container size
    maintainAspectRatio: true,     // Keep aspect ratio
    plugins: {
        legend: {
            display: true,         // Show legend
            position: 'top'        // Legend position
        }
    },
    scales: {
        y: {
            beginAtZero: true,     // Y-axis starts at 0
            ticks: {
                stepSize: 1        // Integer values only
            }
        }
    }
}
```

### Line Chart Specific Options

```javascript
options: {
    tension: 0.4,                  // Curve smoothness
    pointRadius: 6,                // Point size
    pointBackgroundColor: 'color', // Point fill color
    pointBorderColor: 'white',     // Point border
    pointBorderWidth: 2,           // Point border width
    fill: true                     // Fill area under line
}
```

---

## Data Flow

### Step 1: Page Load
```
User visits index.php (Dashboard)
    ↓
PHP loads Dashboard Model
    ↓
Model methods query database
    ↓
Data returned to template
```

### Step 2: Data Preparation
```
PHP converts data to JSON
    ↓
JavaScript receives JSON objects
    ↓
Maps data to chart labels and values
    ↓
Applies color scheme
```

### Step 3: Chart Rendering
```
Chart.js library instantiated
    ↓
Canvas element targeted
    ↓
Chart configuration applied
    ↓
Visual rendering on page
```

---

## Implementation Example

### Backend (Dashboard Model)

```php
public function getCoursesByDepartment(){
    $query = "SELECT d.department_name, COUNT(c.course_id) as course_count 
              FROM departments d 
              LEFT JOIN courses c ON d.department_id = c.department_id 
              GROUP BY d.department_id, d.department_name 
              ORDER BY course_count DESC";
    
    $result = $this->connection->query($query);
    
    $courses_by_dept = [];
    while ($row = $result->fetch_assoc()) {
        $courses_by_dept[] = $row;
    }
    
    return $courses_by_dept;
}
```

### Frontend (Dashboard Template)

```html
<!-- HTML Canvas -->
<div class="col-xl-6 mb-4">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Courses by Department
        </div>
        <div class="card-body">
            <canvas id="coursesDepartmentChart"></canvas>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const coursesDeptData = <?php echo json_encode($dashboardModel->getCoursesByDepartment()); ?>;
    
    const ctx = document.getElementById('coursesDepartmentChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: coursesDeptData.map(d => d.department_name),
            datasets: [{
                label: 'Number of Courses',
                data: coursesDeptData.map(d => d.course_count),
                backgroundColor: ['rgba(54, 162, 235, 0.7)', ...]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
```

---

## Browser Compatibility

**Chart.js 3.9.1 supports:**
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Android Chrome)

---

## Performance Considerations

### Query Optimization

1. **Indexes:** Ensure foreign keys are indexed
   ```sql
   CREATE INDEX idx_course_dept ON courses(department_id);
   CREATE INDEX idx_student_dept ON students(department_id);
   CREATE INDEX idx_enrollment_date ON enrollments(created_at);
   ```

2. **JOIN Efficiency:** Use LEFT JOIN for optional relationships

3. **Grouping:** GROUP BY aggregates data at database level

### Caching (Optional Enhancement)

```php
// Cache dashboard data for 1 hour
$cache_key = 'dashboard_charts_' . date('YmdH');
if (apcu_exists($cache_key)) {
    $chartData = apcu_fetch($cache_key);
} else {
    $chartData = $dashboardModel->getCoursesByDepartment();
    apcu_store($cache_key, $chartData, 3600);
}
```

---

## Troubleshooting

### Chart Not Displaying

**Issue:** Canvas element not showing chart
```javascript
// Check if data exists
console.log(coursesDeptData);

// Check if Chart.js is loaded
console.log(typeof Chart);
```

**Solution:**
- Verify Chart.js CDN is loaded
- Check browser console for errors
- Ensure data is not empty

### Data Not Updating

**Issue:** Charts show old data after database changes
```php
// Add refresh interval (development only)
header('Cache-Control: no-cache, no-store, must-revalidate');
```

**Solution:**
- Clear browser cache
- Disable browser caching for development
- Implement cache busting

### Responsive Issues

**Issue:** Charts not resizing on mobile
```javascript
// Ensure responsive option is true
options: {
    responsive: true,
    maintainAspectRatio: true
}
```

---

## Future Enhancements

1. **Real-time Updates:** Use WebSockets for live data updates
2. **Export Functionality:** Download charts as PDF/PNG
3. **Date Range Filtering:** Select custom date ranges
4. **Drill-down Details:** Click chart elements for detailed view
5. **Additional Charts:** Radar charts, scatter plots
6. **Advanced Analytics:** Predictive trends, anomaly detection

---

## Summary

The Dashboard Visualization System provides:

✅ **6 Different Chart Types:**
- Bar Charts (vertical & horizontal)
- Pie Charts
- Doughnut Charts
- Line Charts with smooth trends

✅ **Real-time Data:**
- From database queries
- Aggregated statistics
- JSON-encoded for JavaScript

✅ **Professional Appearance:**
- Color-coded visualizations
- Responsive design
- Bootstrap card layout
- Font Awesome icons

✅ **Easy Extensibility:**
- Add new model methods
- Create new canvas elements
- Configure Chart.js instances

✅ **Performance Optimized:**
- Efficient SQL queries
- LEFT JOINs for data aggregation
- Minimal data transfer

The system transforms raw data into actionable insights through visual representations, making it easy for administrators to understand institutional metrics at a glance.
