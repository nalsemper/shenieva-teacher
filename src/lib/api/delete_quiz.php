<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $id = $data['id'] ?? 0;

    $conn = new mysqli("localhost", "root", "", "shenieva_db");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "error" => "Database connection failed"]);
        exit();
    }

    $story = isset($_GET['story']) ? $_GET['story'] : 'story1';

    $allowedStories = ['story1', 'story2', 'story3'];
    if (!in_array($story, $allowedStories)) {
        echo json_encode(["error" => "Invalid story selection"]);
        exit();
    }

    // Delete logic
    if ($story === 'story1') {
        $stmt = $conn->prepare("DELETE FROM choices_story1 WHERE quiz_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    if ($story === 'story2') {
        // Story 2 has no choices deletion logic
    }

    // Delete quiz entry for all stories
    $stmt = $conn->prepare("DELETE FROM quizzes_{$story} WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
}
?>
