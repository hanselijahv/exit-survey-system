<?php
session_start();
include('../core/Database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $survey_id = isset($_POST['survey_id']) ? intval($_POST['survey_id']) : 0;
    $survey_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
    $academic_year = isset($_POST['academicYear']) ? trim($_POST['academicYear']) : '';
    $program_id = isset($_POST['program']) ? intval($_POST['program']) : 0;

    $errors = [];
    if (empty($survey_name)) $errors[] = 'Survey name is required';
    if (empty($semester)) $errors[] = 'Semester is required';
    if (empty($academic_year)) $errors[] = 'Academic Year is required';
    if ($program_id <= 0) $errors[] = 'Invalid program selected';

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    $query = "UPDATE surveys 
              SET survey_name = ?, 
                  semester = ?, 
                  academic_year = ?, 
                  program_id = ? 
              WHERE survey_id = ?";

    $stmt = $conn->prepare($query);

    $stmt->bind_param('sssii',
        $survey_name,
        $semester,
        $academic_year,
        $program_id,
        $survey_id
    );

    try {
        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update survey: ' . $stmt->error
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Unexpected error: ' . $e->getMessage()
        ]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}
?>