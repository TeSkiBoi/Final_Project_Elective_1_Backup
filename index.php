<!DOCTYPE html>
<html lang="en">
<?php
    // Include authentication config
    require_once __DIR__ . '/App/Config/Auth.php';
    
    // Check if already logged in
    if (isAuthenticated()) {
        header('Location: App/View/index.php');
        exit();
    }
    
    // Get any error or success messages
    $error_message = getErrorMessage();
    $success_message = getSuccessMessage();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Marinduque State University - Excellence in Education">
    <meta name="theme-color" content="#6ec207">
    <title>Marinduque State University</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        :root {
            --apple-green: #6ec207;
            --apple-green-dark: #7edc2a;
            --default: #ffff;

            --primary-700: #6ec207;
            --primary-600: #7edc2a;
            --primary-500: #8efc3d;
        }

        /* Dropdown Animation Styles */
        .dropdown-menu {
            display: block;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            transition: all 0.2s ease;
            padding: 0.75rem 1.5rem;
        }

        .dropdown-item:hover {
            background-color: var(--apple-green);
            color: white;
            transform: translateX(5px);
        }
        #modal-logout {
            background-color: var(--apple-green);
            color: var(--default);
        }
        .hero-section {
            height: 90vh;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .carousel-item {
            height: 90vh;
            background-size: cover;
            background-position: center;
        }
       .main .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                rgba(115, 129, 97, 0.8),   /* --primary-700: #6ec207 */
                rgba(154, 240, 80, 0.9)   /* --primary-600: #7edc2a */
            );
            z-index: 1;
        }
        .carousel-content {
            position: relative;
            z-index: 2;
        }
        .navbar {
            background-color: rgba(255, 255, 255, 0.95) !important;
        }
        .btn-primary {
            background-color: var(--apple-green) !important;
            border-color: var(--apple-green) !important;
        }
        .btn-primary:hover {
            background-color: var(--apple-green-dark) !important;
            border-color: var(--apple-green-dark) !important;
        }
        .btn-outline-light:hover {
            background-color: var(--apple-green) !important;
            border-color: var(--apple-green) !important;
        }
        .text-primary {
            color: var(--apple-green) !important;
        }
        
        .card:hover {
            border-color: var(--apple-green);
            transition: border-color 0.3s ease;
        }
        footer {
            background-color: var(--apple-green-dark) !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/img/CICS LOGO-min.png" alt="CICS Logo" height="55" class="me-3">
                <div class="d-flex flex-column">
                    <span class="h6 mb-0 text-primary fw-bold">MARINDUQUE STATE UNIVERSITY</span>
                    <small class="text-muted">College of Information and Computing Sciences</small>
                </div>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>



            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="#" class="nav-link active nav-bg-active">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#about" class="nav-link dropdown-toggle" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            About
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item" href="#about"><i class="bi bi-building me-2"></i>University Overview</a></li>
                            <li><a class="dropdown-item" href="#mission"><i class="bi bi-bullseye me-2"></i>Mission & Vision</a></li>
                            <li><a class="dropdown-item" href="#history"><i class="bi bi-clock-history me-2"></i>History</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#leadership"><i class="bi bi-people-fill me-2"></i>Leadership</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#programs" class="nav-link dropdown-toggle" id="programsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Academic Programs
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="programsDropdown">
                            <li><a class="dropdown-item" href="#engineering"><i class="bi bi-gear-fill me-2"></i>College of Engineering</a></li>
                            <li><a class="dropdown-item" href="#education"><i class="bi bi-book-fill me-2"></i>College of Education</a></li>
                            <li><a class="dropdown-item" href="#business"><i class="bi bi-briefcase-fill me-2"></i>College of Business</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#graduate"><i class="bi bi-mortarboard-fill me-2"></i>Graduate Programs</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#student-life" class="nav-link dropdown-toggle" id="studentLifeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Student Life
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="studentLifeDropdown">
                            <li><a class="dropdown-item" href="#campus-life"><i class="bi bi-house-door-fill me-2"></i>Campus Life</a></li>
                            <li><a class="dropdown-item" href="#organizations"><i class="bi bi-people-fill me-2"></i>Organizations</a></li>
                            <li><a class="dropdown-item" href="#sports"><i class="bi bi-trophy-fill me-2"></i>Sports & Athletics</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#news" class="nav-link dropdown-toggle" id="newsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            News & Events
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="newsDropdown">
                            <li><a class="dropdown-item" href="#latest-news"><i class="bi bi-newspaper me-2"></i>Latest News</a></li>
                            <li><a class="dropdown-item" href="#upcoming-events"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</a></li>
                            <li><a class="dropdown-item" href="#announcements"><i class="bi bi-megaphone-fill me-2"></i>Announcements</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link">Contact</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="bi bi-person-fill"></i> Login</button>
                    </li>
                </ul>
                <script>
                    // Highlight active nav-link with background
                    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                        link.addEventListener('click', function() {
                            document.querySelectorAll('.navbar-nav .nav-link').forEach(l => {
                                l.classList.remove('nav-bg-active');
                            });
                            this.classList.add('nav-bg-active');
                        });
                    });
                </script>
                <style>
                    .nav-bg-active {
                        background-color: var(--primary-700) !important;
                        color: #fff !important;
                        border-radius: 0.375rem;
                        transition: background 0.2s;
                    }
                </style>
            </div>
        </div>
    </nav>


    
    <!--THIS IS EXAMPLE MODAL-->
        <div class="modal fade" id="krukkruk" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header" id="modal-logout">
                        <h5 class="modal-title text-white" id="logoutModalLabel">Confirm Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to logout?
                        <form action="" method="post">
                            <label for="">Example Required Trigger function</label>
                            <input type="text" class="form-control" required placeholder="Enter Something">
                            <br>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Submit</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <a href="../index.html" class="btn btn-danger">Yes</a>
                    </div>
                </div>
            </div>
        </div>
    <!--THIS IS THE END OF MODAL-->

    <!-- Alert Messages Container -->
    <div class="container-fluid pt-5" style="margin-top: 80px;">
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show mx-auto" role="alert" style="max-width: 600px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show mx-auto" role="alert" style="max-width: 600px;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success:</strong> <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Hero Section -->
    <section class="hero-section main">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active" style="background-image: url('assets/img/landing-bg-1.jpg'); background-position: center 30%;">
                    <div class="carousel-content d-flex align-items-center h-100">
                        <div class="container text-center">
                            <h1 class="display-2 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Welcome to Marinduque State University</h1>
                            <p class="lead mb-4" style="font-size: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Excellence, Innovation, and Service for Sustainable Development</p>
                            <button class="btn btn-primary btn-lg me-3 px-4 py-2" style="font-weight: 600;">Explore Programs</button>
                            <button class="btn btn-outline-light btn-lg px-4 py-2" style="font-weight: 600;" data-bs-toggle="modal" data-bs-target="#loginModal">Student Portal</button>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('assets/img/landing-bg-2.jpg'); background-position: center center;">
                    <div class="carousel-content d-flex align-items-center h-100">
                        <div class="container text-center">
                            <h1 class="display-2 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Innovate. Inspire. Impact.</h1>
                            <p class="lead mb-4" style="font-size: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Join our vibrant community of innovators and future leaders</p>
                            <button class="btn btn-primary btn-lg me-3 px-4 py-2" style="font-weight: 600;">Research Center</button>
                            <button class="btn btn-outline-light btn-lg px-4 py-2" style="font-weight: 600;">Learn More</button>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url('assets/img/landing-bg-3.jpg'); background-position: center 40%;">
                    <div class="carousel-content d-flex align-items-center h-100">
                        <div class="container text-center">
                            <h1 class="display-2 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Shape Your Future</h1>
                            <p class="lead mb-4" style="font-size: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Discover endless possibilities with our diverse academic programs</p>
                            <button class="btn btn-primary btn-lg me-3 px-4 py-2" style="font-weight: 600;">View Programs</button>
                            <button class="btn btn-outline-light btn-lg px-4 py-2" style="font-weight: 600;">Apply Now</button>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>


    
    <!-- Latest Highlights Section -->
     
    <section class="py-5 bg-white">
        <div class="container" style="max-width: 75%;">
            <h1 class="text-center fw-bold mb-5">Latest Highlights</h1>
            <div id="highlightsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    <div class="carousel-item active" style="height: 35vh;">
                        <div class="bg-white rounded-4 overflow-hidden" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.08);">
                            <div class="row align-items-center g-0">
                                <div class="col-lg-8 p-4">
                                    <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: #6ec207; color: #fff; font-size: 0.875rem;">Achievement</span>
                                    <h3 class="h4 fw-bold mb-3">Hackathon Winners Showcase</h3>
                                    <p class="text-muted mb-4">Our CICS students clinched first place in the National Coding Competition with their innovative smart city solution.</p>
                                    <a href="#" class="btn btn-success btn-sm px-4" style="background-color: #6ec207; border-color: #6ec207;">
                                        Read More <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                                <div class="col-lg-4 p-4" style="background: url('assets/img/landing-bg-1.jpg') center/cover;height: 30vh;border-radius: 10px; ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item" style="height: 35vh;">
                        <div class="bg-white rounded-4 overflow-hidden" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.08);">
                            <div class="row align-items-center g-0">
                                <div class="col-lg-8 p-4">
                                    <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: #6ec207; color: #fff; font-size: 0.875rem;">Research</span>
                                    <h3 class="h4 fw-bold mb-3">International Research Recognition</h3>
                                    <p class="text-muted mb-4">MarSU researchers published groundbreaking study on sustainable agriculture in prestigious journal.</p>
                                    <a href="#" class="btn btn-success btn-sm px-4" style="background-color: #6ec207; border-color: #6ec207;">
                                        Learn More <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                                <div class="col-lg-4 p-4" style="background: url('assets/img/landing-bg-2.jpg') center/cover;height: 30vh;border-radius: 10px; ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item" style="height: 35vh;">
                        <div class="bg-white rounded-4 overflow-hidden" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.08);">
                            <div class="row align-items-center g-0">
                                <div class="col-lg-8 p-4">
                                    <span class="badge rounded-pill px-3 py-2 mb-3" style="background-color: #6ec207; color: #fff; font-size: 0.875rem;">Community</span>
                                    <h3 class="h4 fw-bold mb-3">Community Service Excellence</h3>
                                    <p class="text-muted mb-4">CICS students lead digital literacy program for local communities, impacting over 500 residents.</p>
                                    <a href="#" class="btn btn-success btn-sm px-4" style="background-color: #6ec207; border-color: #6ec207;">
                                        Discover More <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                                <div class="col-lg-4 p-4" style="background: url('assets/img/landing-bg-3.jpg') center/cover;height: 30vh;border-radius: 10px; ">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#highlightsCarousel" data-bs-slide="prev">
                    <button class="btn btn-light rounded-circle shadow-sm" style="width:40px;height:40px;">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#highlightsCarousel" data-bs-slide="next">
                    <button class="btn btn-light rounded-circle shadow-sm" style="width:40px;height:40px;">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </button>
                <div class="text-center mt-4">
                    <button type="button" data-bs-target="#highlightsCarousel" data-bs-slide-to="0" class="btn btn-sm mx-1 p-0 active" style="width:10px;height:10px;background:#6ec207;border:none;border-radius:50%;"></button>
                    <button type="button" data-bs-target="#highlightsCarousel" data-bs-slide-to="1" class="btn btn-sm mx-1 p-0" style="width:10px;height:10px;background:#6ec207;border:none;border-radius:50%;"></button>
                    <button type="button" data-bs-target="#highlightsCarousel" data-bs-slide-to="2" class="btn btn-sm mx-1 p-0" style="width:10px;height:10px;background:#6ec207;border:none;border-radius:50%;"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <!-- <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-4 mb-3">What Our Students Say</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <p class="lead text-muted">Hear from our students about their experiences at MarSU</p>
                    </div>
                </div>
            </div>
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="text-center bg-white p-5 rounded-4" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.08);">
                                    <img src="assets/img/MARSU LOGO.png" alt="Student" class="rounded-circle mb-4" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #6ec207;">
                                    <p class="lead mb-4">"The CICS program at MarSU has been transformative. The hands-on experience and supportive faculty have prepared me well for my career in tech."</p>
                                    <h5 class="fw-bold mb-1">Maria Santos</h5>
                                    <p class="text-muted">BS Computer Science, Class of 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="text-center bg-white p-5 rounded-4" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.08);">
                                    <img src="assets/img/CICS LOGO-min.png" alt="Student" class="rounded-circle mb-4" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #6ec207;">
                                    <p class="lead mb-4">"The research opportunities here are incredible. I've had the chance to work on cutting-edge projects that have real-world impact."</p>
                                    <h5 class="fw-bold mb-1">Juan Dela Cruz</h5>
                                    <p class="text-muted">BS Information Technology, Class of 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="text-center bg-white p-5 rounded-4" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0, 0, 0, 0.08);">
                                    <img src="assets/img/logo.png" alt="Student" class="rounded-circle mb-4" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #6ec207;">
                                    <p class="lead mb-4">"Being part of MarSU's CICS has opened doors I never thought possible. The community here is supportive and inspiring."</p>
                                    <h5 class="fw-bold mb-1">Ana Reyes</h5>
                                    <p class="text-muted">BS Information Systems, Class of 2023</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" data-bs-target="#testimonialCa</div>rousel" data-bs-slide-to="0" class="btn btn-sm mx-1 p-0 active" style="width:10px;height:10px;background:#6ec207;border:none;border-radius:50%;"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1" class="btn btn-sm mx-1 p-0" style="width:10px;height:10px;background:#6ec207;border:none;border-radius:50%;"></button>
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2" class="btn btn-sm mx-1 p-0" style="width:10px;height:10px;background:#6ec207;border:none;border-radius:50%;"></button>
                </div>
            </div>
        </div>
    </section> -->

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="text-center fw-bold mb-5">About MarSU</h1>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <p class="lead text-muted">Marinduque State University is committed to providing quality education and fostering innovation in the heart of Marinduque.</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="position-relative rounded-4 overflow-hidden">
                        <img src="assets/img/landing-bg-3.jpg" alt="Campus Life" class="img-fluid rounded-4 shadow-lg" style="width: 100%; height: 400px; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                            <h3 class="text-white mb-2">Our Mission</h3>
                            <p class="text-white mb-0">Developing globally competitive graduates while preserving our cultural heritage.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="card h-100 border-0 rounded-4 shadow-sm hover-shadow" style="transition: all 0.3s ease;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h3 class="h2 text-primary fw-bold mb-2">5000+</h3>
                                    <p class="text-muted mb-0">Students</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card h-100 border-0 rounded-4 shadow-sm hover-shadow" style="transition: all 0.3s ease;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-person-video3 text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h3 class="h2 text-primary fw-bold mb-2">200+</h3>
                                    <p class="text-muted mb-0">Faculty</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card h-100 border-0 rounded-4 shadow-sm hover-shadow" style="transition: all 0.3s ease;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-book-fill text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h3 class="h2 text-primary fw-bold mb-2">30+</h3>
                                    <p class="text-muted mb-0">Programs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card h-100 border-0 rounded-4 shadow-sm hover-shadow" style="transition: all 0.3s ease;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-building-fill text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h3 class="h2 text-primary fw-bold mb-2">6</h3>
                                    <p class="text-muted mb-0">Campuses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    @media (max-width: 768px) {
        .about-img {
            height: 300px !important;
        }
    }
    </style>

    <hr>

    <!-- Programs Section -->
    <section id="programs" class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="text-center fw-bold mb-5">Our Academic Programs</h1>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <p class="lead text-muted">Discover our diverse range of academic programs designed to prepare you for success in your chosen field.</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm program-card">
                        <div class="card-body p-4">
                            <div class="program-icon mb-4 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; background-color: rgba(110, 194, 7, 0.1);">
                                <i class="bi bi-cpu-fill text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title h4 fw-bold mb-3">College of Engineering</h5>
                            <div class="mb-4">
                                <span class="badge bg-primary mb-2 me-2">Civil Engineering</span>
                                <span class="badge bg-primary mb-2 me-2">Computer Engineering</span>
                                <span class="badge bg-primary mb-2">Electrical Engineering</span>
                            </div>
                            <p class="card-text text-muted mb-4">Build the future with cutting-edge engineering programs focused on innovation and practical application.</p>
                            <a href="#" class="btn btn-outline-success rounded-pill px-4">Learn More</a>
                            <button class="btn btn-outline-light rounded-pill btn-lg px-4 py-2" style="font-weight: 600;" data-bs-toggle="modal" data-bs-target="#loginModal">Student Portal</button>
                        
                            
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm program-card">
                        <div class="card-body p-4">
                            <div class="program-icon mb-4 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; background-color: rgba(110, 194, 7, 0.1);">
                                <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title h4 fw-bold mb-3">College of Education</h5>
                            <div class="mb-4">
                                <span class="badge bg-primary mb-2 me-2">Elementary Education</span>
                                <span class="badge bg-primary mb-2 me-2">Secondary Education</span>
                                <span class="badge bg-primary mb-2">Special Education</span>
                            </div>
                            <p class="card-text text-muted mb-4">Shape young minds and make a lasting impact through our comprehensive education programs.</p>
                            <a href="#" class="btn btn-outline-primary rounded-pill px-4">Learn More</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm program-card">
                        <div class="card-body p-4">
                            <div class="program-icon mb-4 rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; background-color: rgba(110, 194, 7, 0.1);">
                                <i class="bi bi-graph-up-arrow text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="card-title h4 fw-bold mb-3">College of Business</h5>
                            <div class="mb-4">
                                <span class="badge bg-primary mb-2 me-2">Business Administration</span>
                                <span class="badge bg-primary mb-2 me-2">Accountancy</span>
                                <span class="badge bg-primary mb-2">Tourism Management</span>
                            </div>
                            <p class="card-text text-muted mb-4">Develop essential business skills and leadership qualities for success in the global marketplace.</p>
                            <a href="#" class="btn btn-outline-primary rounded-pill px-4">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .program-card {
        transition: all 0.3s ease;
    }
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .program-icon {
        transition: all 0.3s ease;
    }
    .program-card:hover .program-icon {
        transform: scale(1.1);
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 1em;
    }
    @media (max-width: 768px) {
        .badge {
            font-size: 0.8rem;
        }
        .card-title {
            font-size: 1.25rem;
        }
    }
    </style>
<hr>
    <!-- News Section -->
    <section id="news" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="text-center fw-bold mb-5">Latest News and Announcements</h1>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <p class="lead text-muted">Stay updated with the latest happenings and announcements from our university community.</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm news-card" style="transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-primary rounded-pill px-3 py-2">Announcement</span>
                                <small class="text-muted ms-auto">September 18, 2025</small>
                            </div>
                            <h5 class="card-title h4 fw-bold mb-3">Enrollment Now Open</h5>
                            <p class="card-text text-muted mb-4">First semester enrollment for Academic Year 2025-2026 is now open. Early bird discounts available until September 30.</p>
                            <div class="d-flex align-items-center mt-auto">
                                <a href="#" class="text-primary text-decoration-none">Read More <i class="bi bi-arrow-right ms-2"></i></a>
                                <div class="ms-auto">
                                    <span class="badge bg-light text-primary rounded-pill"><i class="bi bi-clock me-1"></i> 5 days left</span>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm news-card" style="transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-success rounded-pill px-3 py-2">Event</span>
                                <small class="text-muted ms-auto">September 15, 2025</small>
                            </div>
                            <h5 class="card-title h4 fw-bold mb-3">Research Conference 2025</h5>
                            <p class="card-text text-muted mb-4">Join us for our Annual Research Conference featuring keynote speakers from leading tech companies and universities.</p>
                            <div class="d-flex align-items-center mt-auto">
                                <a href="#" class="text-primary text-decoration-none">Learn More <i class="bi bi-arrow-right ms-2"></i></a>
                                <div class="ms-auto">
                                    <span class="badge bg-light text-success rounded-pill"><i class="bi bi-calendar-event me-1"></i> Oct 15</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm news-card" style="transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-warning rounded-pill px-3 py-2">Sports</span>
                                <small class="text-muted ms-auto">September 10, 2025</small>
                            </div>
                            <h5 class="card-title h4 fw-bold mb-3">University Sports Festival</h5>
                            <p class="card-text text-muted mb-4">Get ready for an exciting month of sports competitions, featuring basketball, volleyball, and track events.</p>
                            <div class="d-flex align-items-center mt-auto">
                                <a href="#" class="text-primary text-decoration-none">View Details <i class="bi bi-arrow-right ms-2"></i></a>
                                <div class="ms-auto">
                                    <span class="badge bg-light text-warning rounded-pill"><i class="bi bi-trophy me-1"></i> Coming Soon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="#" class="btn btn-outline-primary btn-lg rounded-pill px-5">View All News</a>
            </div>
        </div>
    </section>

    <style>
    .news-card {
        transition: all 0.3s ease;
    }
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    @media (max-width: 768px) {
        .news-card .card-title {
            font-size: 1.25rem;
        }
        .news-card .badge {
            font-size: 0.75rem;
        }
    }
    </style>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 flex-column">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center w-100 pb-3">
                        <img src="assets/img/MARSU LOGO.png" alt="MARSU Logo" class="mb-3" style="height: 100px;">
                        <h4 class="modal-title fw-bold text-primary">Welcome Back!</h4>
                        <p class="text-muted">Login to access your MARSU Portal</p>
                    </div>
                </div>
                <div class="modal-body px-4">

                    <form id="loginForm" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="username" required
                                    placeholder="Enter your username">
                                <div class="invalid-feedback">Please enter your username</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill text-primary"></i>
                                </span>
                                <input type="password" class="form-control border-start-0" id="password" required
                                    placeholder="Enter your password">
                                <div class="invalid-feedback">Please enter your password</div>
                            </div>
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="showPassword">
                            <label class="form-check-label" for="showPassword">Show password</label>
                            <a href="#" class="float-end text-primary text-decoration-none">Forgot password?</a>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                <span class="me-2">Sign In</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <p class="text-muted">Don't have an account? <a href="#" class="text-primary text-decoration-none">Contact Admin</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Add floating label effect
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('border-primary');
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('border-primary');
            });
        });

        // Form validation and submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
            
            try {
                const response = await fetch('App/Controller/LoginController.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    submitBtn.innerHTML = '<span class="me-2"><i class="bi bi-check-circle-fill"></i> Success!</span>';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-success');
                    
                    setTimeout(() => {
                        window.location.href = 'App/View/index.php';
                    }, 1000);
                } else {
                    submitBtn.innerHTML = '<span class="me-2"><i class="bi bi-exclamation-circle-fill"></i> Login Failed</span>';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-danger');
                    
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('btn-danger');
                        submitBtn.classList.add('btn-primary');
                        submitBtn.innerHTML = '<span class="me-2">Sign In</span><i class="bi bi-arrow-right"></i>';
                        
                        // Show error message with appropriate styling
                        const alertType = response.status === 403 ? 'alert-warning' : 'alert-danger';
                        const alertIcon = response.status === 403 ? 'exclamation-triangle-fill' : 'exclamation-triangle-fill';
                        
                        const alert = document.createElement('div');
                        alert.className = `alert ${alertType} alert-dismissible fade show mt-3`;
                        alert.innerHTML = `
                            <i class="bi bi-${alertIcon} me-2"></i>
                            ${result.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        form.appendChild(alert);
                    }, 1500);
                }
            } catch (error) {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-danger');
                submitBtn.innerHTML = '<span class="me-2"><i class="bi bi-exclamation-circle-fill"></i> Network Error</span>';
                
                setTimeout(() => {
                    submitBtn.classList.remove('btn-danger');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.innerHTML = '<span class="me-2">Sign In</span><i class="bi bi-arrow-right"></i>';
                    
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alert.innerHTML = `
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Failed to connect to server. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.appendChild(alert);
                }, 1500);
            }
        });

        // Modal animation enhancements
        const loginModal = document.getElementById('loginModal');
        loginModal.addEventListener('show.bs.modal', function () {
            this.querySelector('.modal-content').style.transform = 'scale(0.7)';
            this.querySelector('.modal-content').style.opacity = '0';
            
            setTimeout(() => {
                this.querySelector('.modal-content').style.transform = 'scale(1)';
                this.querySelector('.modal-content').style.opacity = '1';
            }, 200);
        });

        // Add this to your existing style section
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .modal-content {
                    transition: all 0.3s ease-in-out;
                }
                .input-group:focus-within {
                    box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
                    border-radius: 0.375rem;
                }
                .form-control:focus {
                    box-shadow: none;
                }
                .input-group-text {
                    transition: all 0.2s;
                }
                .input-group:focus-within .input-group-text {
                    border-color: var(--apple-green);
                    background-color: #f8f9fa;
                }
            </style>
        `);



        // Show/hide password functionality
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('password');
            if (this.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });

        // Latest Highlights Carousel Controls
        document.addEventListener('DOMContentLoaded', function() {
            const highlightsCarousel = new bootstrap.Carousel(document.getElementById('highlightsCarousel'), {
                interval: 5000,
                wrap: true
            });

            // Custom navigation buttons
            document.querySelector('.highlights-prev').addEventListener('click', () => {
                highlightsCarousel.prev();
            });

            document.querySelector('.highlights-next').addEventListener('click', () => {
                highlightsCarousel.next();
            });

            // Add hover pause functionality
            const carousel = document.getElementById('highlightsCarousel');
            carousel.addEventListener('mouseenter', () => {
                highlightsCarousel.pause();
            });
            carousel.addEventListener('mouseleave', () => {
                highlightsCarousel.cycle();
            });
        });
    </script>

     <script>
        // Log a simple string
        console.log("Hello, world!");

        // Log the value of a variable
        const myVariable = "This is a variable.";
        console.log(myVariable);

        // Log an object
        const myObject = { name: "Alice", age: 30 };
        console.log(myObject.name);

        // FUNCTIONS 
        function exampleOutput(){
            return "THIS IS MY FUNCTIONS";
        }
        console.log(exampleOutput());

        //ADD TWO NUMBERS
        function add(a, b) {
            return a + b;
        }
        let num1 = 10;
        let num2 = 20;
        let result = add(num1, num2);
        console.log(`The sum of ${num1} and ${num2} is ${result}`);
        console.log("THE SUM OF " + num1 + " AND " + num2 + " IS " + result);


        // function greet(name) {
        //     return `Hello, ${name}!`;
        // }
    </script>


    <!-- Footer -->
    <footer class="text-light py-5 bg-dark" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-light fw-bold">Contact Us</h5>
                    <p>Tanza, Boac, Marinduque<br>
                    Phone: (042) 332-2028<br>
                    Email: info@marinduquestateu.edu.ph</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-muted fw-bold">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Student Portal</a></li>
                        <li><a href="#" class="text-light">Faculty Portal</a></li>
                        <li><a href="#" class="text-light">Library</a></li>
                        <li><a href="#" class="text-light">Research</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="text-muted fw-bold">Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted fw-bold">
                <p class="mb-0">&copy; 2025 Marinduque State University. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>