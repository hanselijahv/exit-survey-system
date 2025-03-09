<?php
/**
 * Update survey questions
 * @author Bravo
 */
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$survey_id = isset($_POST['survey-list']) ? $_POST['survey-list'] : null;
$questions = isset($_POST['questions']) ? $_POST['questions'] : [];

if (!$survey_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Survey ID is required']);
    exit();
}

try {
    $conn->begin_transaction();

    // remove existing
    $removeStmt = $conn->prepare("UPDATE questions SET survey_id = NULL WHERE survey_id = ?");
    $removeStmt->bind_param("i", $survey_id);
    $removeStmt->execute();

    // add new
    $updateStmt = $conn->prepare("UPDATE questions SET survey_id = ? WHERE question_id = ?");
    foreach ($questions as $question_id) {
        $updateStmt->bind_param("ii", $survey_id, $question_id);
        $updateStmt->execute();
    }

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}