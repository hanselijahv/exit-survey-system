<?php
/**
 * Fetch chart data for the dashboard
 * @authors Bravo, Fabe
 */
header('Content-Type: application/json');
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

$query = "
    SELECT 
        (SELECT COUNT(DISTINCT survey_id) FROM surveys) AS total_surveys,
        (SELECT COUNT(DISTINCT survey_id) FROM accomplished_surveys) AS unique_completed_surveys,
        survey_name, 
        (SELECT COUNT(DISTINCT survey_id) FROM surveys WHERE is_published = 0) AS pending_surveys,
        (SELECT COUNT(DISTINCT survey_id) FROM surveys WHERE is_published = 1) AS published_surveys,
        COUNT(*) as count 
    FROM 
        surveys 
    GROUP BY 
        survey_name
";
$result = $conn->query($query);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>