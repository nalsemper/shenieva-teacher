<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$pk_studentID = $conn->real_escape_string($data['pk_studentID'] ?? '');
$trashCollected = isset($data['trashCollected']) ? (int)$data['trashCollected'] : 0;

if (empty($pk_studentID) || $trashCollected < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Increment studentColtrash directly in the database
$sql = "UPDATE students_table SET studentColtrash = studentColtrash + $trashCollected WHERE pk_studentID = '$pk_studentID'";
if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Trash collected updated']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error updating trash: ' . $conn->error]);
}

$conn->close();
?>