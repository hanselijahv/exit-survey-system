<?php
/**
 * Fetch programs
 * @author Fabe
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT program_id, program_description FROM programs";
$result = $conn->query($sql);
$programs = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $programs[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($programs);
$conn->close();
?>