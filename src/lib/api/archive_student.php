<?php
// src/lib/api/archive_student.php
// Archive or unarchive a student (sets archive flag to 1 or 0)

// Include CORS handling
include_once __DIR__ . '/cors.php';

header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['pk_studentID']) || !isset($data['archive'])) {
    echo json_encode(["success" => false, "message" => "Missing pk_studentID or archive flag"]);
    exit;
}

$pk_studentID = intval($data['pk_studentID']);
$archive = intval($data['archive']); // 0 = unarchive, 1 = archive

// Update archive status
$sql = "UPDATE students_table SET archive = ? WHERE pk_studentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $archive, $pk_studentID);

if ($stmt->execute()) {
    $action = $archive ? "archived" : "unarchived";
    echo json_encode([
        "success" => true, 
        "message" => "Student $action successfully"
    ]);
} else {
    echo json_encode([
        "success" => false, 
        "message" => "Error updating student: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
