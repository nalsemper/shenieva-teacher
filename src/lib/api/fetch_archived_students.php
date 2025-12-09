<?php
// src/lib/api/fetch_archived_students.php
// Fetch all archived students (archive = 1)

// Include CORS handling
include_once __DIR__ . '/cors.php';

header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'conn.php';

$sql = "SELECT pk_studentID, idNo, studentName, studentGender, studentLevel, studentRibbon, studentColtrash, studentProgress 
        FROM students_table 
        WHERE archive = 1
        ORDER BY studentName ASC";
        
$result = $conn->query($sql);

$students = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

echo json_encode($students);

$conn->close();
?>
