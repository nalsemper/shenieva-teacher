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
$student_id = $input['student_id'] ?? null;

if (!$student_id || !is_numeric($student_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing student_id']);
    exit;
}

try {
    // Check for finalized quiz
    $stmt = $conn->prepare(
        'SELECT COUNT(*) as count FROM story2_taken_quizzes WHERE student_id = ? AND is_final = 1'
    );
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $hasFinalizedQuiz = $row['count'] > 0;

    $stmt->close();
    $conn->close();
    echo json_encode(['hasFinalizedQuiz' => $hasFinalizedQuiz]);
} catch (Exception $e) {
    // Log the error
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to check finalized quiz: ' . $e->getMessage()]);
}
?>