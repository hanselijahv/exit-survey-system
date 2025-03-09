<?php
/**
 * Publish survey
 * @author Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['survey_id'])) {
    $surveyId = $_GET['survey_id'];
    $query = "UPDATE surveys SET is_published = 1 WHERE survey_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $surveyId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update survey.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Survey ID not provided.']);
}

$conn->close();
?>