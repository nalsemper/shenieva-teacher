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

    try {

    
        // Fetch quiz questions
        $stmt = $pdo->query("SELECT `id`, `question`, `answer`, `points` FROM `quizzes_story2` WHERE 1");
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        echo json_encode($quizzes);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    ?>