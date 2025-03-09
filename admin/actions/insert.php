<?php
/**
 * Insert a new question into the database
 * @author Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $question = $_POST['question'];
    $question_type = $_POST['question_type'];
    $unique_id = generateUniqueId();

    $question = preg_replace(['/<[^>]*>/'], '', $question);

    $sql = "INSERT INTO questions (question_id, category_id, question, question_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iiss", $unique_id, $category, $question, $question_type);
        if ($stmt->execute()) {
            if ($question_type === 'multiple_choice' && !empty($_POST['choices'])) {
                $choices = $_POST['choices'];
                $choiceStmt = $conn->prepare("INSERT INTO choices (choice_id, question_id, choice) VALUES (?, ?, ?)");
                foreach ($choices as $choice) {
                    $choice = preg_replace(['/<[^>]*>/'], '', $choice);
                    $unique_id2 = generateUniqueId();
                    $choiceStmt->bind_param("iis", $unique_id2, $unique_id, $choice);
                    $choiceStmt->execute();
                }
                $choiceStmt->close();
            }
            if ($question_type == 'boolean') {
                $choiceStmt = $conn->prepare("INSERT INTO choices (choice_id, question_id, choice) VALUES (?, ?, ?)");
                $choices = ['True', 'False'];
                foreach ($choices as $choice) {
                    $unique_id2 = generateUniqueId();
                    $choiceStmt->bind_param("iis", $unique_id2, $unique_id, $choice);
                    $choiceStmt->execute();
                }
                $choiceStmt->close();
            }
            if ($question_type == 'satisfaction') {
                $choiceStmt = $conn->prepare("INSERT INTO choices (choice_id, question_id, choice) VALUES (?, ?, ?)");
                $choices = ['Very satisfied', 'Satisfied', 'Neutral', 'Unsatisfied', 'Very unsatisfied'];
                foreach ($choices as $choice) {
                    $unique_id2 = generateUniqueId();
                    $choiceStmt->bind_param("iis", $unique_id2, $unique_id, $choice);
                    $choiceStmt->execute();
                }
                $choiceStmt->close();
            }
            if ($question_type == 'relevance') {
                $choiceStmt = $conn->prepare("INSERT INTO choices (choice_id, question_id, choice) VALUES (?, ?, ?)");
                $choices = ['Highly relevant', 'Moderately relevant', 'Slightly relevant', 'Not relevant'];
                foreach ($choices as $choice) {
                    $unique_id2 = generateUniqueId();
                    $choiceStmt->bind_param("iis", $unique_id2, $unique_id, $choice);
                    $choiceStmt->execute();
                }
                $choiceStmt->close();
            }
            if ($question_type == 'quality') {
                $choiceStmt = $conn->prepare("INSERT INTO choices (choice_id, question_id, choice) VALUES (?, ?, ?)");
                $choices = ['Excellent', 'Good', 'Fair', 'Poor'];
                foreach ($choices as $choice) {
                    $unique_id2 = generateUniqueId();
                    $choiceStmt->bind_param("iis", $unique_id2, $unique_id, $choice);
                    $choiceStmt->execute();
                }
                $choiceStmt->close();
            }
            $_SESSION['success_message'] = "New question inserted successfully.";
            header("Location: ../home/questions.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
$database->closeConnection();

function generateUniqueId() {
    return rand(1, 2147483647);
}
?>