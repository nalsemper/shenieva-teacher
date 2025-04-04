<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "shenieva_db");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$tableName = "quizzes_store2";
$choicesTable = "choices_store2";

$sql = "SELECT q.id, q.question, q.points, 
               GROUP_CONCAT(c.choice_text) AS choices,
               (SELECT c2.choice_text FROM $choicesTable c2 WHERE c2.quiz_id = q.id AND c2.is_correct = 1) AS answer
        FROM $tableName q
        LEFT JOIN $choicesTable c ON q.id = c.quiz_id
        GROUP BY q.id";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit();
}

$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $row['points'] = (int)$row['points'];
    $row['choices'] = $row['choices'] ? explode(',', $row['choices']) : [];
    $quizzes[] = $row;
}

echo json_encode($quizzes);

$conn->close();
?>