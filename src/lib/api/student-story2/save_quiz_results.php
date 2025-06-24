<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['student_id'], $data['attempt'], $data['results'], $data['is_final'])) {
        throw new Exception("Missing required fields");
    }

    $student_id = (int)$data['student_id'];
    $attempt = (int)$data['attempt'];
    $is_final = (int)$data['is_final'];
    $results = $data['results'];

    // Begin transaction
    $conn->begin_transaction();

    foreach ($results as $result) {
        $question = $conn->real_escape_string($result['question']);
        $answer = $conn->real_escape_string($result['answer']);
        $is_correct = $result['isCorrect'] ? 1 : 0;

        $query = "INSERT INTO story2_taken_quizzes (student_id, attempt, question, answer, is_correct, is_final)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("iissii", $student_id, $attempt, $question, $answer, $is_correct, $is_final);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(["success" => true]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>