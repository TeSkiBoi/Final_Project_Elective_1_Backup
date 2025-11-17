<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Welcome <i><?php echo $currentUser; ?></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active"><i class="fas fa-tachometer-alt"></i>Dashboard / Overview</li>
        </ol>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <div>
                                <div class="small">Total Students</div>
                                <h4 class="mb-0"><?php echo $dashboardModel->getCountStudent(); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="students.php">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-chalkboard-teacher fa-2x"></i>
                            </div>
                            <div>
                                <div class="small">Total Courses</div>
                                <h4 class="mb-0"><?php echo $dashboardModel->getCountCourse(); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="courses.php">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                            <div>
                                <div class="small">Total Department</div>
                                <h4 class="mb-0"><?php echo $dashboardModel->getCountDepartment(); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="department.php">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-calendar fa-2x"></i>
                            </div>
                            <div>
                                <div class="small">Total Users</div>
                                <h4 class="mb-0"><?php echo $dashboardModel->getCountUser(); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="user.php">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mt-4">
            <!-- Bar Chart: Courses by Department -->
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

            <!-- Pie Chart: Students by Department -->
            <div class="col-xl-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-1"></i>
                        Students Distribution by Department
                    </div>
                    <div class="card-body">
                        <canvas id="studentsDepartmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Line Chart: Enrollments Trend -->
            <div class="col-xl-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-1"></i>
                        Enrollments Trend (Current Year)
                    </div>
                    <div class="card-body">
                        <canvas id="enrollmentsTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Doughnut Chart: Enrollment Status -->
            <div class="col-xl-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-doughnut me-1"></i>
                        Enrollment Status Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="enrollmentStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bar Chart: User Role Distribution -->
            <div class="col-xl-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>
                        User Roles Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="roleDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Bar Chart: Top Courses -->
            <div class="col-xl-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>
                        Top 10 Courses by Enrollment
                    </div>
                    <div class="card-body">
                        <canvas id="topCoursesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Chart.js Script with Data -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // Data from PHP Backend
            const coursesDeptData = <?php 
                $coursesData = $dashboardModel->getCoursesByDepartment();
                echo json_encode($coursesData ?? []); 
            ?>;
            
            // const studentsDeptData = <?php 
            //     $studentsData = $dashboardModel->getStudentsByDepartment();
            //     echo json_encode($studentsData ?? []); 
            // ?>;
            
            // const enrollmentsTrendData = <?php 
            //     $trendData = $dashboardModel->getEnrollmentsTrend();
            //     echo json_encode($trendData ?? []); 
            // ?>;
            
            // const enrollmentStatusData = <?php 
            //     $statusData = $dashboardModel->getEnrollmentStatus();
            //     echo json_encode($statusData ?? []); 
            // ?>;
            
            // const roleDistributionData = <?php 
            //     $roleData = $dashboardModel->getUserRoleDistribution();
            //     echo json_encode($roleData ?? []); 
            // ?>;
            
            // const topCoursesData = <?php 
            //     $topCourses = $dashboardModel->getTopCoursesByEnrollment();
            //     echo json_encode($topCourses ?? []); 
            // ?>;

            // Color Palettes
            const chartColors = [
                'rgba(54, 162, 235, 0.7)',   // Blue
                'rgba(255, 99, 132, 0.7)',   // Red
                'rgba(75, 192, 75, 0.7)',    // Green
                'rgba(255, 206, 86, 0.7)',   // Yellow
                'rgba(153, 102, 255, 0.7)',  // Purple
                'rgba(255, 159, 64, 0.7)',   // Orange
                'rgba(201, 203, 207, 0.7)',  // Grey
                'rgba(255, 99, 255, 0.7)',   // Pink
                'rgba(99, 255, 132, 0.7)',   // Mint
                'rgba(255, 255, 99, 0.7)'    // Light Yellow
            ];

            const borderColors = chartColors.map(c => c.replace('0.7', '1'));

            // 1. Bar Chart: Courses by Department
            if (coursesDeptData.length > 0) {
                const ctx1 = document.getElementById('coursesDepartmentChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: coursesDeptData.map(d => d.department_name),
                        datasets: [{
                            label: 'Number of Courses',
                            data: coursesDeptData.map(d => d.course_count),
                            backgroundColor: chartColors.slice(0, coursesDeptData.length),
                            borderColor: borderColors.slice(0, coursesDeptData.length),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // 2. Pie Chart: Students by Department
            // if (studentsDeptData.length > 0) {
            //     const ctx2 = document.getElementById('studentsDepartmentChart').getContext('2d');
            //     new Chart(ctx2, {
            //         type: 'pie',
            //         data: {
            //             labels: studentsDeptData.map(d => d.department_name),
            //             datasets: [{
            //                 data: studentsDeptData.map(d => d.student_count),
            //                 backgroundColor: chartColors.slice(0, studentsDeptData.length),
            //                 borderColor: borderColors.slice(0, studentsDeptData.length),
            //                 borderWidth: 2
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: true,
            //             plugins: {
            //                 legend: {
            //                     display: true,
            //                     position: 'right'
            //                 }
            //             }
            //         }
            //     });
            // }

            // // 3. Line Chart: Enrollments Trend
            // if (enrollmentsTrendData.length > 0) {
            //     const ctx3 = document.getElementById('enrollmentsTrendChart').getContext('2d');
            //     new Chart(ctx3, {
            //         type: 'line',
            //         data: {
            //             labels: enrollmentsTrendData.map(d => d.month_year),
            //             datasets: [{
            //                 label: 'Monthly Enrollments',
            //                 data: enrollmentsTrendData.map(d => d.enrollment_count),
            //                 borderColor: 'rgba(54, 162, 235, 1)',
            //                 backgroundColor: 'rgba(54, 162, 235, 0.1)',
            //                 borderWidth: 3,
            //                 fill: true,
            //                 tension: 0.4,
            //                 pointRadius: 6,
            //                 pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            //                 pointBorderColor: 'rgba(255, 255, 255, 1)',
            //                 pointBorderWidth: 2
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: true,
            //             plugins: {
            //                 legend: {
            //                     display: true,
            //                     position: 'top'
            //                 }
            //             },
            //             scales: {
            //                 y: {
            //                     beginAtZero: true,
            //                     ticks: {
            //                         stepSize: 1
            //                     }
            //                 }
            //             }
            //         }
            //     });
            // }

            // // 4. Doughnut Chart: Enrollment Status
            // if (enrollmentStatusData.length > 0) {
            //     const ctx4 = document.getElementById('enrollmentStatusChart').getContext('2d');
            //     new Chart(ctx4, {
            //         type: 'doughnut',
            //         data: {
            //             labels: enrollmentStatusData.map(d => d.status),
            //             datasets: [{
            //                 data: enrollmentStatusData.map(d => d.count),
            //                 backgroundColor: [
            //                     'rgba(75, 192, 75, 0.7)',     // Green - Active
            //                     'rgba(54, 162, 235, 0.7)',    // Blue - Completed
            //                     'rgba(255, 99, 132, 0.7)'     // Red - Dropped
            //                 ],
            //                 borderColor: [
            //                     'rgba(75, 192, 75, 1)',
            //                     'rgba(54, 162, 235, 1)',
            //                     'rgba(255, 99, 132, 1)'
            //                 ],
            //                 borderWidth: 2
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: true,
            //             plugins: {
            //                 legend: {
            //                     display: true,
            //                     position: 'right'
            //                 }
            //             }
            //         }
            //     });
            // }

            // // 5. Bar Chart: User Role Distribution
            // if (roleDistributionData.length > 0) {
            //     const ctx5 = document.getElementById('roleDistributionChart').getContext('2d');
            //     new Chart(ctx5, {
            //         type: 'bar',
            //         data: {
            //             labels: roleDistributionData.map(d => d.role_name),
            //             datasets: [{
            //                 label: 'Number of Users',
            //                 data: roleDistributionData.map(d => d.user_count),
            //                 backgroundColor: chartColors.slice(0, roleDistributionData.length),
            //                 borderColor: borderColors.slice(0, roleDistributionData.length),
            //                 borderWidth: 2
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             maintainAspectRatio: true,
            //             plugins: {
            //                 legend: {
            //                     display: true,
            //                     position: 'top'
            //                 }
            //             },
            //             scales: {
            //                 y: {
            //                     beginAtZero: true,
            //                     ticks: {
            //                         stepSize: 1
            //                     }
            //                 }
            //             }
            //         }
            //     });
            // }

            // // 6. Horizontal Bar Chart: Top Courses
            // if (topCoursesData.length > 0) {
            //     const ctx6 = document.getElementById('topCoursesChart').getContext('2d');
            //     new Chart(ctx6, {
            //         type: 'bar',
            //         data: {
            //             labels: topCoursesData.map(d => d.course_code + ' - ' + d.course_name),
            //             datasets: [{
            //                 label: 'Enrollment Count',
            //                 data: topCoursesData.map(d => d.enrollment_count),
            //                 backgroundColor: chartColors.slice(0, topCoursesData.length),
            //                 borderColor: borderColors.slice(0, topCoursesData.length),
            //                 borderWidth: 2
            //             }]
            //         },
            //         options: {
            //             indexAxis: 'y',
            //             responsive: true,
            //             maintainAspectRatio: true,
            //             plugins: {
            //                 legend: {
            //                     display: true,
            //                     position: 'top'
            //                 }
            //             },
            //             scales: {
            //                 x: {
            //                     beginAtZero: true,
            //                     ticks: {
            //                         stepSize: 1
            //                     }
            //                 }
            //             }
            //         }
            //     });
            // }
        </script>
    </div>
</main>