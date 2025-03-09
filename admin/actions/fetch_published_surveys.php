<?php
/**
 * Fetches all published surveys
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
$sql = "SELECT surveys.survey_id, surveys.survey_name, surveys.semester, surveys.academic_year, surveys.is_published, programs.program_description FROM surveys JOIN programs ON surveys.program_id = programs.program_id WHERE is_published = 1;";
$result = $conn->query($sql);
$surveys = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $surveys[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($surveys);
$conn->close();
?>