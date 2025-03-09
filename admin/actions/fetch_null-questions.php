<?php
/**
 * Fetch questions that are not assigned to a survey
 * @author Fabe
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();
$sql = "SELECT
    questions.question_id,
    questions.category_id,
    categories.category_name,
    questions.question,
    questions.question_type
FROM
    questions
        JOIN
    categories
    ON
        questions.category_id = categories.category_id WHERE survey_id IS NULL";
$result = $conn->query($sql);

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($questions);
?>