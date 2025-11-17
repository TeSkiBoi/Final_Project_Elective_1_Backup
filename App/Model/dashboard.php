<?php
/**
 * Department Model
 * Handles database operations for departments
 */

require_once __DIR__ . '/../Config/Database.php';

class Dashboard {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }
    /**
     * COUNT ALL DEPARTMENTS
     */
    public function getCountDepartment(){
        $query = "SELECT COUNT(*) as department_count FROM departments";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();
        return $row['department_count'];
    }

    /**
     * COUNT ALL COURSES
     */ 
    public function getCountCourse(){
        $query = "SELECT COUNT(*) as course_count FROM courses";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();
        return $row['course_count'];
    }

    // COUNT TOTAL NUMBER OF STUDENTS
    public function getCountStudent(){
        $query = "SELECT COUNT(*) as student_count FROM students";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();
        return $row['student_count'];
    }

    public function getCountUser(){
        $query = "SELECT COUNT(*) as user_count FROM users";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();
        return $row['user_count'];
    }

    /**
     * GET COURSES BY DEPARTMENT (FOR BAR CHART)
     */
    public function getCoursesByDepartment(){
        $query = "SELECT d.department_name, COUNT(c.course_id) as course_count 
                  FROM departments d 
                  LEFT JOIN courses c ON d.department_id = c.department_id 
                  GROUP BY d.department_id, d.department_name 
                  ORDER BY course_count DESC";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $courses_by_dept = [];
        while ($row = $result->fetch_assoc()) {
            $courses_by_dept[] = $row;
        }
        return $courses_by_dept;
    }

    /**
     * GET STUDENTS BY DEPARTMENT (FOR PIE CHART)
     */
    public function getStudentsByDepartment(){
        $query = "SELECT d.department_name, COUNT(s.student_id) as student_count 
                  FROM departments d 
                  LEFT JOIN students s ON d.department_id = s.department_id 
                  GROUP BY d.department_id, d.department_name 
                  HAVING student_count > 0 
                  ORDER BY student_count DESC";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $students_by_dept = [];
        while ($row = $result->fetch_assoc()) {
            $students_by_dept[] = $row;
        }
        return $students_by_dept;
    }

    /**
     * GET ENROLLMENTS TREND (FOR LINE CHART - Monthly enrollments)
     */
    public function getEnrollmentsTrend(){
        $query = "SELECT 
                    MONTH(e.created_at) as month, 
                    YEAR(e.created_at) as year,
                    COUNT(e.enrollment_id) as enrollment_count,
                    DATE_FORMAT(e.created_at, '%b %Y') as month_year
                  FROM enrollments e
                  WHERE YEAR(e.created_at) = YEAR(CURDATE())
                  GROUP BY YEAR(e.created_at), MONTH(e.created_at)
                  ORDER BY YEAR(e.created_at), MONTH(e.created_at)";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $enrollment_trend = [];
        while ($row = $result->fetch_assoc()) {
            $enrollment_trend[] = $row;
        }
        return $enrollment_trend;
    }

    /**
     * GET USER ROLE DISTRIBUTION (FOR PIE CHART)
     */
    public function getUserRoleDistribution(){
        $query = "SELECT r.role_name, COUNT(u.user_id) as user_count 
                  FROM roles r 
                  LEFT JOIN users u ON r.role_id = u.role_id 
                  GROUP BY r.role_id, r.role_name 
                  ORDER BY user_count DESC";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $role_dist = [];
        while ($row = $result->fetch_assoc()) {
            $role_dist[] = $row;
        }
        return $role_dist;
    }

    /**
     * GET ENROLLMENT STATUS (FOR DOUGHNUT CHART)
     */
    public function getEnrollmentStatus(){
        $query = "SELECT 
                    'Active Enrollments' as status, 
                    COUNT(e.enrollment_id) as count 
                  FROM enrollments e 
                  WHERE e.status = 'active'
                  UNION ALL
                  SELECT 
                    'Completed Enrollments' as status,
                    COUNT(e.enrollment_id) as count 
                  FROM enrollments e 
                  WHERE e.status = 'completed'
                  UNION ALL
                  SELECT 
                    'Dropped Enrollments' as status,
                    COUNT(e.enrollment_id) as count 
                  FROM enrollments e 
                  WHERE e.status = 'dropped'";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $enrollment_status = [];
        while ($row = $result->fetch_assoc()) {
            $enrollment_status[] = $row;
        }
        return $enrollment_status;
    }

    /**
     * GET TOP COURSES BY ENROLLMENT (FOR BAR CHART)
     */
    public function getTopCoursesByEnrollment(){
        $query = "SELECT c.course_code, c.course_name, COUNT(e.enrollment_id) as enrollment_count 
                  FROM courses c 
                  LEFT JOIN enrollments e ON c.course_id = e.course_id 
                  GROUP BY c.course_id, c.course_code, c.course_name 
                  ORDER BY enrollment_count DESC 
                  LIMIT 10";
        $result = $this->connection->query($query);
        if (!$result) {
            return false;
        }
        $top_courses = [];
        while ($row = $result->fetch_assoc()) {
            $top_courses[] = $row;
        }
        return $top_courses;
    }

   
}
?>
