<?php
/**
 * Fetch surveys
 * @authors Bravo, Fabe
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT 
            surveys.survey_id, 
            surveys.survey_name, 
            surveys.semester, 
            surveys.academic_year, 
            surveys.is_published, 
            surveys.is_closed, 
            programs.program_description,
            COALESCE(
                GROUP_CONCAT(DISTINCT 
                    CONCAT(classes.class_code, ' ', classes.class_number, ' - ', classes.class_description) 
                    SEPARATOR '; '
                ), 
                'No classes'
            ) AS restricted_class
        FROM 
            surveys 
        JOIN 
            programs ON surveys.program_id = programs.program_id
        LEFT JOIN 
            restricted_surveys ON surveys.survey_id = restricted_surveys.survey_id
        LEFT JOIN 
            classes ON restricted_surveys.class_code = classes.class_code
        GROUP BY 
            surveys.survey_id, 
            surveys.survey_name, 
            surveys.semester, 
            surveys.academic_year, 
            surveys.is_published, 
            programs.program_description
        ORDER BY 
            surveys.survey_id DESC";
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