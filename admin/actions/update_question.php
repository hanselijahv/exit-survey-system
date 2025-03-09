<?php
/**
 * Update question
 * @authors Bravo, Briones, Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionId = $_POST['question_id'];
    $category = $_POST['category'];
    $question = $_POST['question'];
    $questionType = isset($_POST['question_type_modal']) ? $_POST['question_type_modal'] : null;

    $question = preg_replace(['/<[^>]*>/'], '', $question);

    if (empty($questionType) || $questionType === 'blank') {
        $query = "SELECT question_type FROM questions WHERE question_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $questionId);
        $stmt->execute();
        $stmt->bind_result($existingQuestionType);
        $stmt->fetch();
        $questionType = $existingQuestionType;
        $stmt->close();
    }

    $conn->begin_transaction();

    try {
        $updateQuery = "UPDATE questions SET category_id = ?, question = ?, question_type = ? WHERE question_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sssi', $category, $question, $questionType, $questionId);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update question: " . $stmt->error);
        }
        $stmt->close();

        $deleteChoicesQuery = "DELETE FROM choices WHERE question_id = ?";
        $deleteStmt = $conn->prepare($deleteChoicesQuery);
        $deleteStmt->bind_param('i', $questionId);
        if (!$deleteStmt->execute()) {
            throw new Exception("Failed to delete existing choices: " . $deleteStmt->error);
        }
        $deleteStmt->close();

        $insertChoiceQuery = "INSERT INTO choices (choice_id, question_id, choice) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertChoiceQuery);

        $choices = [];
        switch ($questionType) {
            case 'multiple_choice':
                $choices = !empty($_POST['choices']) ? $_POST['choices'] : [];
                break;
            case 'boolean':
                $choices = ['True', 'False'];
                break;
            case 'satisfaction':
                $choices = ['Very satisfied', 'Satisfied', 'Neutral', 'Unsatisfied', 'Very unsatisfied'];
                break;
            case 'relevance':
                $choices = ['Highly relevant', 'Moderately relevant', 'Slightly relevant', 'Not relevant'];
                break;
            case 'quality':
                $choices = ['Excellent', 'Good', 'Fair', 'Poor'];
                break;
            default:
                $choices = [];
        }

        foreach ($choices as $choice) {
            $choice = trim($choice);
            if (empty($choice)) continue;

            $choice = preg_replace(['/<[^>]*>/'], '', $choice);

            $unique_id = generateUniqueId();

            $insertStmt->bind_param('iis', $unique_id, $questionId, $choice);
            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert choice: " . $insertStmt->error);
            }
        }

        $insertStmt->close();

        $conn->commit();

        echo 'Success';
    } catch (Exception $e) {
        $conn->rollback();
        echo 'Error: ' . $e->getMessage();
    }

    $conn->close();
}

function generateUniqueId() {
    return rand(1, 2147483647);
}
?>