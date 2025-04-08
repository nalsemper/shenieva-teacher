<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conn = new mysqli("localhost", "root", "", "shenieva_db");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
        exit();
    }


$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['student_id']) || !isset($data['store']) || !isset($data['attempt']) || !isset($data['answers']) || !isset($data['is_final'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit();
}

$student_id = (int)$data['student_id'];
$store = (int)$data['store'];
$attempt = (int)$data['attempt'];
$answers = $data['answers'];
$is_final = $data['is_final'] ? 1 : 0;
$store = 2;

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("
        INSERT INTO story1_taken_quizzes (student_id, store, attempt, item_number, question, choices, is_correct, points, is_final)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            question = VALUES(question),
            choices = VALUES(choices),
            is_correct = VALUES(is_correct),
            points = VALUES(points),
            is_final = VALUES(is_final)
    ");

    foreach ($answers as $answer) {
        $item_number = (int)$answer['item_number'];
        $question = $answer['question'];
        $choices = json_encode($answer['choices']);
        $is_correct = $answer['is_correct'] ? 1 : 0;
        $points = (int)$answer['points'];

        $stmt->bind_param("iiiissiii", $student_id, $store, $attempt, $item_number, $question, $choices, $is_correct, $points, $is_final);
        $stmt->execute();
    }

    $stmt->close();
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save quiz: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>