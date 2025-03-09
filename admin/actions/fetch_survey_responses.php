<?php
/**
 * Fetch survey responses
 * @authors Briones, Fabe
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

$survey_id = isset($_GET['survey_id']) ? intval($_GET['survey_id']) : 0;

$sql = "SELECT COUNT(*) as count FROM accomplished_surveys WHERE survey_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $survey_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $response = ['message' => 'No responses yet'];
} else {
    $sql = "SELECT question_id, question, question_type FROM questions WHERE survey_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['question_id'];
            if (in_array($row['question_type'], ['multiple_choice', 'boolean', 'satisfaction', 'relevance', 'quality'])) {
                $response_sql = "SELECT choices.choice AS response, COUNT(*) AS count
FROM responses
JOIN choices ON responses.choice_id = choices.choice_id
WHERE responses.question_id = ?
GROUP BY choices.choice";
                $response_stmt = $conn->prepare($response_sql);
                $response_stmt->bind_param("i", $question_id);
                $response_stmt->execute();
                $response_result = $response_stmt->get_result();
                $responses = [];
                while ($response_row = $response_result->fetch_assoc()) {
                    $responses[] = $response_row;
                }
                $row['responses'] = $responses;
            } elseif ($row['question_type'] == 'short_answer') {
                $response_sql = "SELECT short_answer FROM responses WHERE question_id = ?";
                $response_stmt = $conn->prepare($response_sql);
                $response_stmt->bind_param("i", $question_id);
                $response_stmt->execute();
                $response_result = $response_stmt->get_result();
                $responses = [];
                while ($response_row = $response_result->fetch_assoc()) {
                    $responses[] = $response_row;
                }
                $row['responses'] = $responses;
            }
            $questions[] = $row;
        }
    }
    $response = $questions;
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>