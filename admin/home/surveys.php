<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!doctype html>
<!--
    * This is the main page for the surveys.
    * It contains the form for creating a survey, the table for displaying surveys, and the modals for selecting questions and editing surveys.
    * @authors Bravo, Briones, Fabe
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surveys</title>
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
                        <a class="sidebar-link" href="./questions.php" aria-expanded="false">
                <span>
                  <i class="ti ti-question-mark"></i>
                </span>
                            <span class="hide-menu">Questions</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link active" href="./surveys.php" aria-expanded="false">
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

        <!-- Page Content -->
        <div class="container-fluid">
            <div class="clearfix">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Main</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Surveys</li>
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

            <div class="row">
                <!-- Pagination -->
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Create a Survey</h5>
                            <div id="page1">
                                <form id="questionnaireForm" action="../actions/save_survey.php" method="POST">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        <div id="charCount2" style="float: right; font-size: 12px; padding: 2px 5px;"
                                             class="badge bg-secondary">0 / 20
                                        </div>
                                        <textarea class="form-control" id="name" name="name" rows="1" required
                                                  placeholder="✍🏻 write a creative name"></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="semester">Semester</label>
                                        <select class="form-control" id="semester" name="semester" required>
                                            <option value="First Semester">First Semester</option>
                                            <option value="Second Semester">Second Semester</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="academicYear">Academic Year</label>
                                        <select class="form-control" id="academicYear" name="academicYear" required>
                                            <option value="2024-2025">2024-2025</option>
                                            <option value="2025-2026">2025-2026</option>
                                            <option value="2026-2027">2026-2027</option>
                                            <option value="2027-2028">2027-2028</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="program">Program</label>
                                        <select class="form-control" id="program" name="program" required>
                                            <option value="">Select Program</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="restrict">Select Class</label>
                                        <div id="selected-classes" class="mt-2"></div>
                                        <select class="form-control" id="restrict" name="restrict[]" multiple="multiple"
                                                required>
                                            <option value="no_restrict">No classes</option>
                                        </select>
                                    </div>
                                    <div id="selectedQuestionsInputs"></div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Save Survey</button>
                                    </div>
                                </form>
                            </div>
                            <div id="page2" style="display: none;">
                                <ul id="selectedQuestions" class="list-group"></ul>
                                <button type="button" id="addQuestionButton" class="btn btn-primary"><i
                                            class="ti ti-plus"></i> Add Question
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <nav>
                                    <ul class="pagination">
                                        <li class="page-item active"><a class="page-link" href="#" id="page1Link">1</a>
                                        </li>
                                        <li class="page-item"><a class="page-link" href="#" id="page2Link">2</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table-->
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <select id="rowsPerPage" class="form-select" style="width: 100px;">
                                        <option value="4">4</option>
                                        <option value="7">7</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex justify-content-end">
                                    <div class="me-8">
                                        <input type="text" id="searchInput" class="form-control"
                                               placeholder="🔍 Search Surveys">
                                    </div>
                                    <div class="user-select-none">
                                        <select id="filterCategory" class="form-select" style="width: 123px;">
                                            <option value="">All</option>
                                            <option value="published">published</option>
                                            <option value="drafts">drafts</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTableExample" class="table">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Calendar</th>
                                        <th>Program</th>
                                        <th>Restricted</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span id="entriesInfo"></span>
                                <nav>
                                    <ul class="pagination" id="pagination">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="page2" style="display: none;">
                    <ul id="selectedQuestions" class="list-group"></ul>
                    <button type="button" id="addQuestionButton" class="btn btn-primary"><i
                                class="ti ti-plus"></i> Add Question
                    </button>
                </div>

                <!-- Select Questions Modal-->
                <div id="questionModal" class="modal fade" tabindex="-1" aria-labelledby="questionModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="questionModalLabel">Select Questions</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-container2">
                                    <div class="row mb-4">
                                        <div class="col-md-5">
                                            <button id="addSelectedQuestionsButton" class="btn btn-primary">Add Selected
                                                Questions
                                            </button>
                                        </div>
                                        <div class="col-md-7 d-flex justify-content-end">
                                            <div class="me-8">
                                                <input type="text" id="modalSearchInput" class="form-control"
                                                       placeholder="🔍 Search Questions">
                                            </div>
                                            <div class="user-select-none">
                                                <select id="modalFilterCategory" class="form-select">
                                                    <option value="">All Categories</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table id="modalQuestionsTable" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Category</th>
                                        <th>Question</th>
                                        <th>Question Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Survey Modal -->
                <div id="editSurveyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editSurveyModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSurveyModalLabel">Edit Survey</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editSurveyForm">
                                    <input type="hidden" id="editSurveyId" name="survey_id">

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="editSurveyName">Name</label>
                                        <div id="editCharCount" style="float: right; font-size: 12px; padding: 2px 5px;" class="badge bg-secondary">0 / 20</div>
                                        <textarea class="form-control" id="editSurveyName" name="name" rows="1" required placeholder="✍🏻 write a creative name"></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="editSemester">Semester</label>
                                        <select class="form-control" id="editSemester" name="semester" required>
                                            <option value="First Semester">First Semester</option>
                                            <option value="Second Semester">Second Semester</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="editAcademicYear">Academic Year</label>
                                        <select class="form-control" id="editAcademicYear" name="academicYear" required>
                                            <option value="2024-2025">2024-2025</option>
                                            <option value="2025-2026">2025-2026</option>
                                            <option value="2026-2027">2026-2027</option>
                                            <option value="2027-2028">2027-2028</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="editProgram">Program</label>
                                        <select class="form-control" id="editProgram" name="program" required>
                                            <option value="">Select Program</option>
                                        </select>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <button type="button" id="deleteSurveyButton" class="btn btn-danger">Delete Survey</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Questions -->
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Edit Survey Questions</h5>
                            <form id="editSurveyQuestionsForm" action="../actions/update_survey_questions.php" method="POST">


                                <div class="form-group mb-3">
                                    <label class="form-label" for="survey-list">Surveys</label>
                                    <select class="form-control" id="survey-list" name="survey-list" required>
                                        <option value="">Select Survey</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3" id="editChoicesContainer" style="">
                                    <label  class="form-label"  for="editChoices">Questions</label>
                                    <div id="editChoicesList"></div>
                                </div>


                                    <ul id="selectedSurveyQuestions" class="list-group"></ul>
                                    <button type="button" id="addSurveyQuestionButton" class="btn btn-primary">
                                        <i class="ti ti-plus"></i> Add Question
                                    </button>
                                <div id="selectedSurveyQuestionsInputs"></div>
                                <div class="d-flex justify-content-end" style="margin-top:20px">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                        </div>
                    </div>
            </div>

                <!-- Select Questions Modal 2 -->
                <div id="surveyQuestionModal" class="modal fade" tabindex="-1" aria-labelledby="surveyQuestionModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="surveyQuestionModalLabel">Select Questions</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-container2">
                                    <div class="row mb-4">
                                        <div class="col-md-5">
                                            <button type="button" id="addSelectedSurveyQuestionsButton" class="btn btn-primary">Add Selected
                                                Questions
                                            </button>
                                        </div>
                                        <div class="col-md-7 d-flex justify-content-end">
                                            <div class="me-8">
                                                <input type="text" id="modalSearchInput2" class="form-control"
                                                       placeholder="🔍 Search Questions">
                                            </div>
                                            <div class="user-select-none">
                                                <select id="modalFilterCategory2" class="form-select">
                                                    <option value="">All Categories</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table id="modalSurveyQuestionsTable" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Category</th>
                                        <th>Question</th>
                                        <th>Question Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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


<!-- Own JS -->
<script src="../assets/js/survey.js"></script>
<script src="../assets/js/sidebar.js"></script>
<script src="../assets/js/logout.js"></script>
<script src="../assets/js/fetch-programs.js"></script>
<script src="../assets/js/survey-table.js"></script>
<script src="../assets/js/survey-2.js"></script>
<script src="../assets/js/survey-3.js"></script>
<script src="../assets/js/unpublished-surveys.js"></script>
<script src="../assets/js/profile.js"></script>
<script src="../assets/js/edit-password-hide.js"></script>
<script src="../assets/js/profile-image.js"></script>
<script src="../assets/js/fetch_user_details.js"></script>
</body>

</html>
