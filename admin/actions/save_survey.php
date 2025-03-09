<?php
/**
 * Save survey
 * @author Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $semester = $_POST['semester'];
    $academicYear = $_POST['academicYear'];
    $program = $_POST['program'];
    $restrict = $_POST['restrict'];
    $questions = $_POST['questions'];
    $name = preg_replace(['/<[^>]*>/'], '', $name);
    $surveyId = saveSurvey($name, $semester, $academicYear, $program, $restrict, $questions);
}

function saveSurvey($name, $semester, $academicYear, $program, $restrict, $question_ids)
{
    global $database;
    $conn = $database->getConnection();
    $surveyId = generateUniqueId();
    $isPublished = 0;

    if (in_array("no_restrict", $restrict)) {
        echo "No restrictions";
        $query = "INSERT INTO surveys (survey_id, survey_name, program_id, semester, academic_year, is_published) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issssi', $surveyId, $name, $program, $semester, $academicYear, $isPublished);

        if ($stmt->execute()) {
            $stmt->close();
            $questionQuery = "UPDATE questions SET survey_id = ? WHERE question_id = ?";
            $questionStmt = $conn->prepare($questionQuery);
            foreach ($question_ids as $question_id) {
                $questionStmt->bind_param('ii', $surveyId, $question_id);
                $questionStmt->execute();
            }
            $questionStmt->close();

            $conn->close();
            return $surveyId;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    } else {
        $query = "INSERT INTO surveys (survey_id, survey_name, program_id, semester, academic_year, is_published) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issssi', $surveyId, $name, $program, $semester, $academicYear, $isPublished);

        if ($stmt->execute()) {
            $stmt->close();

            $questionQuery = "UPDATE questions SET survey_id = ? WHERE question_id = ?";
            $questionStmt = $conn->prepare($questionQuery);
            foreach ($question_ids as $question_id) {
                $questionStmt->bind_param('ii', $surveyId, $question_id);
                $questionStmt->execute();
            }
            $questionStmt->close();

            $restrictQuery = "INSERT INTO restricted_surveys (survey_id, class_code) VALUES (?, ?)";
            $restrictStmt = $conn->prepare($restrictQuery);
            foreach ($restrict as $class_id) {
                $restrictStmt->bind_param('ii', $surveyId, $class_id);
                $restrictStmt->execute();
            }
            $restrictStmt->close();
            $conn->close();
            return $surveyId;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }
}

function generateUniqueId()
{
    return rand(1, 2147483647);
}
?>