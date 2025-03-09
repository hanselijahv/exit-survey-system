<?php
/**
 * This file is responsible for handling the login process.
 * It checks if the user is an admin or not
 * It also checks if the user has entered the correct credentials
 * @author Fabe
 */
session_start();
require_once '../core/Database.php';
date_default_timezone_set('Asia/Manila');

class User
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    public function login($email, $password, $rememberMe)
    {
        $stmt = $this->db->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $attemptsData = $result->fetch_assoc();

        if ($attemptsData) {
            $attempts = $attemptsData['attempts'];
            $lastAttempt = strtotime($attemptsData['last_attempt']);
            $currentTime = time();
            $timeoutDuration = 10;

            if ($attempts >= 3 && ($currentTime - $lastAttempt) < $timeoutDuration) {
                $remainingTime = $timeoutDuration - ($currentTime - $lastAttempt);
                    return "Too many login attempts. Please try again in " . $remainingTime . " second/s.";
            }
        } else {
            $attempts = 0;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $user = $result->fetch_assoc();
            $_SESSION['username'] = $email;
            $_SESSION['user_id'] = $user['user_id'];
            if ($user['user_type'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                if ($rememberMe) {
                    setcookie("email", $email, time() + (86400 * 30), "/");
                    setcookie("password", $password, time() + (86400 * 30), "/");
                    setcookie("remember_me", "true", time() + (86400 * 30), "/");
                } else {
                    setcookie("email", "", time() - 3600, "/");
                    setcookie("password", "", time() - 3600, "/");
                    setcookie("remember_me", "", time() - 3600, "/");
                }
                return "success";
            } else {
                return "Permission denied!";
            }
        } else {
            if ($attemptsData) {
                $stmt = $this->db->prepare("UPDATE login_attempts SET attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP WHERE email = ?");
            } else {
                $stmt = $this->db->prepare("INSERT INTO login_attempts (email, attempts, last_attempt) VALUES (?, 1, CURRENT_TIMESTAMP)");
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();

            return "Invalid username or password!";
        }
    }
}

$error = "";
$success = "";
$email = "";
$password = "";
$rememberMe = false;

if (isset($_COOKIE['remember_me']) && $_COOKIE['remember_me'] === "true") {
    if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
        $email = $_COOKIE['email'];
        $password = $_COOKIE['password'];
        $rememberMe = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();
    $user = new User($db);

    $email = $_POST['email'];
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']);

    $result = $user->login($email, $password, $rememberMe);
    if ($result === "success") {
        $success = "Signed in successfully";
    } else {
        $error = $result;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iSLU Exit Interview</title>
    <link rel="shortcut icon" type="image/png" href="../assets/ico/favicon.ico"/>
    <link rel="stylesheet" href="../assets/css/styles.min.css"/>
    <link rel="stylesheet" href="../assets/libs/sweet-alert/sweetalert2.min.css">
</head>

<body>
<!--  Body  -->
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar-position="fixed" data-header-position="fixed">
    <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0">
                        <div class="card-body">
                            <img src="../assets/images/slu-exit.png" width="443"  alt="">
                            <form method="POST" action="login.php">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Email</label>
                                    <input name="email" placeholder="Enter Email" type="email" class="form-control"
                                           id="exampleInputEmail1" aria-describedby="emailHelp"
                                           value="<?= $rememberMe ? htmlspecialchars($email) : '' ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <i id="togglePasswordIcon" class="ti ti-eye-off" onclick="togglePassword()"></i>
                                    <input name="password" placeholder="Enter password" type="password"
                                           class="form-control" id="exampleInputPassword1" value="<?= $rememberMe ? htmlspecialchars($password) : '' ?>"required>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input primary" type="checkbox" name="remember_me"
                                               id="flexCheckChecked" <?= $rememberMe ? 'checked' : '' ?>>
                                        <label class="form-check-label text-dark" for="flexCheckChecked">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-2 rounded-2"> <i class="ti ti-login" onclick="togglePassword()"></i>
                                    Sign In
                                </button>
                                <input type="hidden" id="errorMessage" value="<?= htmlspecialchars($error) ?>">
                                <input type="hidden" id="successMessage" value="<?= htmlspecialchars($success) ?>">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  External JS  -->
<script src="../assets/libs/sweet-alert/sweetalert2.min.js"></script>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Own JS -->
<script src="../assets/js/password-hide.js"></script>
<script src="../assets/js/notification.js"></script>
</body>

</html>