<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $id = $data['id'] ?? 0;
    $question = $data['question'] ?? '';
    $answer = $data['answer'] ?? '';
    $points = $data['points'] ?? 0;

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

    $tableName = "quizzes_{$story}";

    if ($story === 'story3') {
        // Update logic for story3 - only edit the question
        $stmt = $conn->prepare("UPDATE $tableName SET question = ? WHERE id = ?");
        $stmt->bind_param("si", $question, $id);
    } elseif ($story === 'story2') {
        // Update logic for story2 (no choices)
        $stmt = $conn->prepare("UPDATE $tableName SET question = ?, answer = ?, points = ? WHERE id = ?");
        $stmt->bind_param("ssii", $question, $answer, $points, $id);
    } else {
        // Original logic for story1 (with choices)
        $stmt = $conn->prepare("UPDATE $tableName SET question = ?, answer = ?, points = ? WHERE id = ?");
        $stmt->bind_param("ssii", $question, $answer, $points, $id);

        if ($stmt->execute()) {
            // Manage choices for story1
            $choices = $data['choices'] ?? [];

            $stmtDelete = $conn->prepare("DELETE FROM choices_{$story} WHERE quiz_id = ?");
            $stmtDelete->bind_param("i", $id);
            $stmtDelete->execute();

            foreach ($choices as $choice) {
                $isCorrect = ($choice === $answer) ? 1 : 0;
                $stmtChoice = $conn->prepare("INSERT INTO choices_{$story} (quiz_id, choice_text, is_correct) VALUES (?, ?, ?)");
                $stmtChoice->bind_param("isi", $id, $choice, $isCorrect);
                $stmtChoice->execute();
            }
        }
    }

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
