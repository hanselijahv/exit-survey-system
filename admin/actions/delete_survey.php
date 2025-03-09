<?php
/**
 * Delete a survey and its restricted surveys
 * @author Bravo
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $survey_id = $_SERVER['REQUEST_METHOD'] === 'DELETE' ? parse_str(file_get_contents("php://input"), $data) : $_GET['survey_id'];
    $survey_id = $_SERVER['REQUEST_METHOD'] === 'DELETE' ? $data['survey_id'] : $survey_id;

    $deleteRestrictedSurveySql = "DELETE FROM restricted_surveys WHERE survey_id = ?";
    $stmtrestrict = $conn->prepare($deleteRestrictedSurveySql);
    if ($stmtrestrict) {
        $stmtrestrict->bind_param("i", $survey_id);
        if ($stmtrestrict->execute()) {
            echo json_encode(['success' => 'Survey deleted successfully.']);

            $deleteSurveySql = "DELETE FROM surveys WHERE survey_id = ?";
            $stmt = $conn->prepare($deleteSurveySql);
            if ($stmt) {
                $stmt->bind_param("i", $survey_id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => 'Survey deleted successfully.']);
                } else {
                    echo json_encode(['error' => 'Error deleting survey: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                echo json_encode(['error' => 'Error preparing statement for deleting survey: ' . $conn->error]);
            }

        } else {
            echo json_encode(['error' => 'Error deleting survey: ' . $stmtrestrict->error]);
        }
        $stmtrestrict->close();
    } else {
        echo json_encode(['error' => 'Error preparing statement for deleting survey: ' . $conn->error]);
    }

} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$conn->close();
?>