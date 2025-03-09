
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!doctype html>
<!--
    * This is the main dashboard page.
    * This file is responsible for displaying the dashboard page.
    * @authors Bravo, Briones, Fabe
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="shortcut icon" type="image/png" href="../assets/ico/favicon.ico"/>
    <link rel="stylesheet" href="../assets/css/styles.min.css"/>
    <link rel="stylesheet" href="../assets/libs/sweet-alert/sweetalert2.min.css">
</head>

<body>
<!--  Body Wrapper -->
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div>
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <img src="../assets/images/slu-exit.png" width="174" alt="" style="margin-top: 20px;"/>
                <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8"></i>
                </div>
            </div>
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                <ul id="sidebarnav">
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Home</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link active" href="./dashboard.php" aria-expanded="false">
                <span>
                  <i class="ti ti-layout-dashboard"></i>
                </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./responses.php" aria-expanded="false">
                <span>
                  <i class="ti ti-checkbox"></i>
                </span>
                            <span class="hide-menu">Responses</span>
                        </a>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Main</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./questions.php" aria-expanded="false">
                <span>
                  <i class="ti ti-question-mark"></i>
                </span>
                            <span class="hide-menu">Questions</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./surveys.php" aria-expanded="false">
                <span>
                 <i class="ti ti-article"></i>
                </span>
                            <span class="hide-menu">Surveys</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->

    <!--  Main wrapper -->
    <div class="body-wrapper">
        <!--  Header Start -->
        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">

                <ul class="navbar-nav">
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>

                </ul>
                <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <img id="fetched-image" src="../assets/images/circle-user-round.svg" alt="" width="40" height="40"
                                     class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                 style="margin-top: 10px;"
                                 aria-labelledby="drop2">
                                <div class="message-body text-center">
                                    <div class="figure mb-3">
                                        <img id="fetched-image2" src="../assets/images/circle-user-round.svg" alt="" width="80" height="80"
                                             class="rounded-circle mx-auto d-block">
                                    </div>
                                    <div class="info text-center">
                                        <p id="profile-name" class="name font-weight-bold mb-0"></p>
                                        <p id="profile-email" class="email text-muted mb-3"></p>
                                    </div>
                                    <hr class="my-4">
                                    <a href="javascript:void(0)" id="profileLink" class="d-flex align-items-center gap-2 dropdown-item">
                                        <i class="ti ti-edit fs-6"></i>
                                        <p class="mb-0 fs-3">Edit Profile</p>
                                    </a>
                                    <a id="logout" class="btn btn-outline-primary mx-3 mt-2 d-block">
                                        <i class="ti ti-logout"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!--  Header End -->

        <div class="container-fluid">
            <div class="clearfix">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </div>

            <!-- Profile Modal -->
            <div id="profileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="profileForm">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <i id="togglePasswordIcon" class="ti ti-eye-off" onclick="togglePassword()"></i>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Profile Image</label>
                                    <img id="imagePreview" src="" alt="Profile Image" style="max-width: 100px; max-height: 100px;">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" form="profileForm">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responses -->
            <div class="row">
                <div class="col-lg-5 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="row align-items-start">
                                <div class="col-8">
                                    <h5 class="card-title mb-9 fw-semibold">Total Responses</h5>
                                    <h4 class="fw-semibold mb-3">1</h4>
                                    <div class="d-flex align-items-center pb-1">
                            <span class="me-2 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-arrow-up-left text-success"></i>
                            </span>
                                        <p class="text-dark me-1 fs-3 mb-0">+1%</p>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <h6 class="fw-semibold me-2">Users w/ Responses:</h6>
                                        <span class="badge bg-primary users-with-responses">0</span>
                                        <h6 class="fw-semibold ms-4 me-2">Users w/o Responses:</h6>
                                        <span class="badge bg-secondary users-without-responses">0</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user-check fs-6"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="row align-items-start">
                                <div class="col-8">
                                    <h5 class="card-title mb-9 fw-semibold">Total Users</h5>
                                    <h4 class="fw-semibold mb-3">1</h4>
                                    <div class="d-flex align-items-center pb-1">
                            <span class="me-2 rounded-circle bg-light-primary round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-arrows-diagonal text-primary"></i>
                            </span>
                                        <p class="text-dark me-1 fs-3 mb-0">User Types</p>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <h6 class="fw-semibold me-2">Admins:</h6>
                                        <span class="badge bg-primary total_admins">0</span>
                                        <h6 class="fw-semibold ms-4 me-2">Students:</h6>
                                        <span class="badge bg-secondary total_students">0</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex justify-content-end">
                                        <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-user fs-6"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>

            <!-- Graphs -->
            <div class="row">
                <div class="col-lg-10 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                <div class="mb-3 mb-sm-0">
                                    <h5 class="card-title fw-semibold">Survey Overview</h5>
                                </div>
                            </div>
                            <div id="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- External JS -->
<script src="../assets/libs/sweet-alert/sweetalert2.min.js"></script>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.js"></script>
<script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>


<!-- Own JS -->
<script src="../assets/js/sidebar.js"></script>
<script src="../assets/js/logout.js"></script>
<script src="../assets/js/dashboard.js"></script>
<script src="../assets/js/profile.js"></script>
<script src="../assets/js/edit-password-hide.js"></script>
<script src="../assets/js/profile-image.js"></script>
<script src="../assets/js/fetch_user_details.js"></script>
</body>

</html>
