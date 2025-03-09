<?php
/**
 * Fetch existing survey questions
 * @author Bravo
 */
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

header('Content-Type: application/json');

if (!isset($_GET['survey_id'])) {
    echo json_encode([]);
    exit();
}

$survey_id = intval($_GET['survey_id']);

try {
    $stmt = $conn->prepare("
        SELECT q.question_id, q.question, c.category_name, q.question_type 
        FROM questions q
        JOIN categories c ON q.category_id = c.category_id
        WHERE q.survey_id = ?
    ");
    $stmt->bind_param("i", $survey_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }

    echo json_encode($questions);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}