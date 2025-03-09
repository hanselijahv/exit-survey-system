<?php
/**
 * Fetch classes for a program
 * @author Fabe
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$programId = isset($_GET['program_id']) ? intval($_GET['program_id']) : 0;

$sql = "SELECT class_code, class_number, class_description FROM classes WHERE program_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $programId);
$stmt->execute();
$result = $stmt->get_result();

$classes = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($classes);

$stmt->close();
$conn->close();
?>
