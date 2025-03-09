<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!doctype html>
<!--
    * This file is responsible for displaying the questions page.
    * It allows the admin to create, edit, and delete questions.
    * @authors Bravo, Briones, Fabe
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Questions</title>
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
                <img src="../assets/images/slu-exit.png" width="174" alt="" style="margin-top: 20px;" />
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
                        <a class="sidebar-link" href="./dashboard.php" aria-expanded="false">
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
                        <a class="sidebar-link active" href="./questions.php" aria-expanded="false">
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

        <!-- Main Content -->
        <div class="container-fluid">
            <div class="clearfix">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Main</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Questions</li>
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

            <!-- Create a Question -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Create a Question</h5>
                    <form action="../actions/insert.php" method="POST" class="needs-validation">
                        <div class="form-group mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-select" required>
                                <option value="">Select Category</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a category.
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="question" class="form-label">Question</label>
                            <div id="charCount" style="float: right;" class="badge bg-secondary">0 / 150</div>
                            <textarea rows="7" placeholder="✍🏻 Enter question here" id="question" name="question" class="form-control" required></textarea>
                            <div class="invalid-feedback">
                                Please enter a question.
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="question_type" class="form-label">Question Type</label>
                            <select id="question_type" name="question_type" class="form-select" required>
                                <option value="short_answer">Text</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="boolean">True/False</option>
                                <option value="satisfaction">Likert Scale - Satisfaction</option>
                                <option value="relevance">Likert Scale - Relevance</option>
                                <option value="quality">Likert Scale - Quality</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a question type.
                            </div>
                        </div>

                        <div class="form-group mb-3" id="choicesContainer" style="display: none;">
                            <label for="choices" class="form-label">Choices</label>
                            <div id="choicesList">
                                <input type="text" name="choices[]" class="form-control mb-2" placeholder="Enter choice">
                            </div>
                            <button type="button" id="addChoiceButton" class="btn btn-secondary">Add Choice</button>
                        </div>

                        <button id=type="submit" class="btn btn-primary">Submit <i class="ti ti-brand-telegram"></i></button>
                    </form>
                </div>
            </div>

            <!-- Edit Modal -->
            <div id="editQuestionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
                            <button type="button" class="btn-close" data-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div id="editPage1">
                                <form id="editQuestionForm">
                                    <input type="hidden" id="editQuestionId" name="question_id">
                                    <div class="form-group mb-3">
                                        <label  class="form-label"  for="editCategory">Category</label>
                                        <select id="editCategory" name="category" class="form-control" required>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label  class="form-label"  for="editQuestion">Question</label>
                                        <div id="charCount2" style="float: right; font-size: 12px; padding: 2px 5px;"
                                             class="badge bg-secondary">0 / 150
                                        </div>
                                        <textarea id="editQuestion" name="question" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group mb-3" id="editChoicesContainer" style="">
                                        <label  class="form-label"  for="editChoices"></label>
                                        <div id="editChoicesList"></div>
                                        <button type="button" id="addEditChoiceButton" class="btn btn-secondary">Add Choice</button>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <button type="button" id="deleteQuestionButton" class="btn btn-danger">Delete Question</button>
                                    </div>
                                </form>
                            </div>

                            <div id="editPage2" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="question_type_modal" class="form-label">Question Type</label>
                                    <select id="question_type_modal" name="question_type_modal" class="form-select" required>
                                        <option value="blank">Select Question Type</option>
                                        <option value="short_answer">Text</option>
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="boolean">True/False</option>
                                        <option value="satisfaction">Likert Scale - Satisfaction</option>
                                        <option value="relevance">Likert Scale - Relevance</option>
                                        <option value="quality">Likert Scale - Quality</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a question type.
                                    </div>
                                </div>

                                <div class="form-group mb-3" id="choicesContainerModal" style="display: none;">
                                    <label for="choices" class="form-label">Choices</label>
                                    <div id="choicesListModal">
                                        <input type="text" name="choices[]" class="form-control mb-2" placeholder="Enter choice">
                                    </div>
                                    <button type="button" id="addChoiceButtonModal" class="btn btn-secondary">Add Choice</button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center m-3">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item active"><a class="page-link" href="#" id="editPage1Link">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#" id="editPage2Link">2</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container mt-4">
                <div class="search-container mb-3 d-flex align-items-center">
                    <input type="text" id="searchInput" class="form-control me-2" placeholder="🔍 Search Questions">
                    <select id="filterCategory" class="form-select">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <table id="questionsTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Category</th>
                        <th>Question</th>
                        <th>Question Type</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>

<!-- External JS -->
<script src="../assets/libs/sweet-alert/sweetalert2.min.js"></script>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.js"></script>

<!-- Own JS -->
<script src="../assets/js/sidebar.js"></script>
<script src="../assets/js/logout.js"></script>
<script src="../assets/js/question.js"></script>
<script src="../assets/js/profile.js"></script>
<script src="../assets/js/edit-password-hide.js"></script>
<script src="../assets/js/profile-image.js"></script>
<script src="../assets/js/fetch_user_details.js"></script>
</body>

</html>
