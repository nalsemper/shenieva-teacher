<?php

// delete_student.php

// Include CORS handling
include_once __DIR__ . '/cors.php';

header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json');

include 'conn.php';

// Get the raw input from the request body.
$data = json_decode(file_get_contents("php://input"), true);

// Check if idNo is provided.
if (!isset($data['idNo'])) {
    echo json_encode(["success" => false, "message" => "Missing student ID"]);
    exit;
}

$idNo = $data['idNo'];

// Prepare the delete statement.
$sql = "DELETE FROM students_table WHERE idNo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idNo); // Assuming idNo is a string. Use "i" if it's an integer.

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Student deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting student"]);
}

$stmt->close();
$conn->close();

?>
