<?php
/**
 * Fetch card data for the dashboard
 * @authors Bravo, Fabe
 */
header('Content-Type: application/json');
include('../core/Database.php');
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT COUNT(*) as total_responses FROM responses";
$result = $conn->query($query);
$total_responses = $result->fetch_assoc()['total_responses'];

$total_users_query = 'SELECT COUNT(*) as total_users FROM users';
$total_users_result = $conn->query($total_users_query);
$total_users = $total_users_result->fetch_assoc()['total_users'];

$total_admins_query = 'SELECT COUNT(*) as total_admins FROM users WHERE user_type = "admin"';
$total_admins_query = $conn->query($total_admins_query);
$total_admins = $total_admins_query->fetch_assoc()['total_admins'];

$total_students_query = 'SELECT COUNT(*) as total_students FROM users WHERE user_type = "student"';
$total_students_query = $conn->query($total_students_query);
$total_students = $total_students_query->fetch_assoc()['total_students'];

$users_with_responses_query = '
    SELECT COUNT(DISTINCT users.user_id) as users_with_responses
    FROM users
    JOIN responses ON users.user_id = responses.user_id
    WHERE user_type != "admin"
';
$users_with_responses_result = $conn->query($users_with_responses_query);
$users_with_responses = $users_with_responses_result->fetch_assoc()['users_with_responses'];

$users_without_responses_query = '
    SELECT COUNT(users.user_id) as users_without_responses
    FROM users
    LEFT JOIN responses ON users.user_id = responses.user_id
    WHERE responses.user_id IS NULL AND user_type != "admin"
';
$users_without_responses_result = $conn->query($users_without_responses_query);
$users_without_responses = $users_without_responses_result->fetch_assoc()['users_without_responses'];

$percentage_change = 0;
if ($total_users > 0) {
    $percentage_change = ($users_with_responses / $total_users) * 100;
}

echo json_encode([
    'total_users' => $total_users,
    'total_responses' => $total_responses,
    'percentage_change' => $percentage_change,
    'users_with_responses' => $users_with_responses,
    'users_without_responses' => $users_without_responses,
    'total_admins' => $total_admins,
    'total_students' => $total_students
]);
?>