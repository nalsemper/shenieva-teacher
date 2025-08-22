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
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

try {
    // Fetch quiz questions from story_quiz3 table
    $query = "SELECT `id`, `question`, `answer`, `points` FROM `story_quiz3`";
    $result = $conn->query($query);

    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }

    echo json_encode($quizzes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>