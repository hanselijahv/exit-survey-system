<?php
/**
 * Fetch categories
 * @author Bravo
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

$sql = "SELECT category_id, category_name FROM categories";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($categories);
?>