<?php
/**
 * Delete a question and its choices
 * @author Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $question_id = $_SERVER['REQUEST_METHOD'] === 'DELETE' ? parse_str(file_get_contents("php://input"), $data) : $_GET['question_id'];
    $question_id = $_SERVER['REQUEST_METHOD'] === 'DELETE' ? $data['question_id'] : $question_id;
    $deleteChoicesSql = "DELETE FROM choices WHERE question_id = ?";
    $stmt = $conn->prepare($deleteChoicesSql);
    if ($stmt) {
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing statement for deleting choices: ' . $conn->error]);
        exit();
    }
    $deleteQuestionSql = "DELETE FROM questions WHERE question_id = ?";
    $stmt = $conn->prepare($deleteQuestionSql);
    if ($stmt) {
        $stmt->bind_param("i", $question_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Question deleted successfully.']);
        } else {
            echo json_encode(['error' => 'Error deleting question: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing statement for deleting question: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>