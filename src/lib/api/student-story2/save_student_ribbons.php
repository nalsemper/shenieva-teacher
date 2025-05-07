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
$ribbons = $input['ribbons'] ?? null;

if (!$student_id || !is_numeric($student_id) || $ribbons === null || !is_numeric($ribbons) || $ribbons < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing student_id or ribbons']);
    exit;
}

try {
    // Update studentRibbon by incrementing (handle NULL as 0)
    $stmt = $conn->prepare(
        'UPDATE students SET studentRibbon = COALESCE(studentRibbon, 0) + ? WHERE pk_studentID = ?'
    );
    $stmt->bind_param('ii', $ribbons, $student_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        // No rows updated (e.g., student_id not found)
        http_response_code(404);
        echo json_encode(['error' => 'Student not found']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt->close();
    $conn->close();
    echo json_encode(['message' => 'Ribbons saved successfully']);
} catch (Exception $e) {
    // Log the error
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save ribbons: ' . $e->getMessage()]);
}
?>