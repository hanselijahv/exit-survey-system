<?php
/**
 * Update user details
 * @authors Briones, Fabe
 */
session_start();
include('../core/Database.php');

$database = new Database();
$conn = $database->getConnection();
$conn->query("SET GLOBAL max_allowed_packet=67108864");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$image = isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK ? file_get_contents($_FILES['image']['tmp_name']) : null;

$errors = [];
if (empty($email)) $errors[] = 'Email is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit();
}

if ($image !== null) {
    $query = "UPDATE users SET email = ?, password = ?, image = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $email, $password, $image, $user_id);
} else {
    $query = "UPDATE users SET email = ?, password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $email, $password, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update user details']);
}

$stmt->close();
$conn->close();
?>