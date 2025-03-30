<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "shenieva_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$story = isset($_GET['story']) ? $_GET['story'] : 'story1';

$allowedStories = ['story1', 'story2', 'story3'];
if (!in_array($story, $allowedStories)) {
    echo json_encode(["error" => "Invalid story selection"]);
    exit();
}

$tableName = "quizzes_{$story}";

// Logic for each story
if ($story === 'story3') {
    // ✅ Story 3: Only fetch questions
    $sql = "SELECT id, points, question FROM $tableName";
} elseif ($story === 'story2') {
    // ✅ Story 2: Fetch without choices
    $sql = "SELECT id, question, answer, points FROM $tableName";
} else {
    // ✅ Story 1: Fetch with choices
    $choicesTable = "choices_{$story}";
    $sql = "SELECT q.id, q.question, q.answer, q.points, 
                   GROUP_CONCAT(c.choice_text) AS choices
            FROM $tableName q
            JOIN $choicesTable c ON q.id = c.quiz_id
            GROUP BY q.id";
}

$result = $conn->query($sql);

$quizzes = [];
while ($row = $result->fetch_assoc()) {
    if ($story === 'story1') {
        $row['choices'] = explode(',', $row['choices']);
    }
    $row['points'] = isset($row['points']) ? (int)$row['points'] : null;
    $quizzes[] = $row;
}

echo json_encode($quizzes);
?>
