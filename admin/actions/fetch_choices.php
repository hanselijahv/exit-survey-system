<?php
/**
 * Fetch choices for a question
 * @author Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['question_id'])) {
    $question_id = $_GET['question_id'];

    $sql = "SELECT choice FROM choices WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $choices = [];
        while ($row = $result->fetch_assoc()) {
            $choices[] = $row['choice'];
        }
        $stmt->close();
        echo json_encode($choices);
    } else {
        echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'No question_id provided.']);
}

$conn->close();
?>