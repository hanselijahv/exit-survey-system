<?php
/**
 * Fetch unpublished surveys
 * @author Bravo
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

$sql = "SELECT surveys.survey_id, surveys.survey_name, surveys.semester, surveys.academic_year, programs.program_description 
        FROM surveys 
        JOIN programs ON surveys.program_id = programs.program_id
        WHERE surveys.is_published = 0";
$result = $conn->query($sql);
$unpublished_surveys = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $unpublished_surveys[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($unpublished_surveys);
$conn->close();
?>