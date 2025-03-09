<?php
/**
 * Fetches the number of completed and assigned users for a survey
 * @authors Briones, Fabe
 */
session_start();
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();
$survey_id = $_GET['survey_id'];

$sql = "
SELECT
    s.survey_id,
    COALESCE(COUNT(DISTINCT ac.user_id), 0) AS completed_users,
    COUNT(DISTINCT uc.user_id) AS assigned_users
FROM
    surveys s
JOIN
    programs p ON s.program_id = p.program_id
JOIN
    classes c ON p.program_id = c.program_id
JOIN
    user_classes uc ON c.class_code = uc.class_code
LEFT JOIN
    restricted_surveys rs ON s.survey_id = rs.survey_id
LEFT JOIN
    user_classes ruc ON rs.class_code = ruc.class_code
LEFT JOIN
    accomplished_surveys ac ON s.survey_id = ac.survey_id AND uc.user_id = ac.user_id
WHERE
    s.survey_id = ?
    AND (rs.class_code IS NULL OR uc.class_code = rs.class_code)
GROUP BY
    s.survey_id;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $survey_id);
$stmt->execute();
$result = $stmt->get_result();

$response = array();
if ($result->num_rows > 0) {
    $response = $result->fetch_assoc();
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>