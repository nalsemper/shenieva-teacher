<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Log the request for debugging
file_put_contents('debug.log', date('Y-m-d H:i:s') . "\n" . print_r($_SERVER, true) . "\n" . file_get_contents('php://input') . "\n", FILE_APPEND);

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'shenieva_db';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$quizResults = $input['quizResults'] ?? null;

if (!$quizResults || !is_array($quizResults) || empty($quizResults)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or empty quiz results data']);
    exit;
}

try {
    // Begin transaction for atomicity
    $conn->begin_transaction();

    // Prepare INSERT query, excluding taken_quiz_id
    $stmt = $conn->prepare(
        'INSERT INTO story2_taken_quizzes (student_id, attempt, question, answer, is_correct, is_final)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    foreach ($quizResults as $result) {
        // Validate required fields
        if (!isset($result['student_id'], $result['attempt'], $result['question'], $result['answer'], $result['is_correct'], $result['is_final'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields in quiz result']);
            $conn->rollback();
            exit;
        }

        // Convert booleans to integers for MySQL
        $is_correct = (int) $result['is_correct'];
        $is_final = (int) $result['is_final'];

        // Bind parameters
        $stmt->bind_param(
            'iisssi',
            $result['student_id'],
            $result['attempt'],
            $result['question'],
            $result['answer'],
            $is_correct,
            $is_final
        );
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();
    $stmt->close();
    $conn->close();
    echo json_encode(['message' => 'Quiz data saved successfully']);
} catch (Exception $e) {
    // Roll back transaction on error
    $conn->rollback();
    // Log the error
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save quiz data: ' . $e->getMessage()]);
}
?>