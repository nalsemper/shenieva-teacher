<?php
// update_student.php

// Allow requests from any origin (for development). Restrict in production.
header("Access-Control-Allow-Origin: *");

// Allow only the POST method (since the modal uses POST for updates).
header("Access-Control-Allow-Methods: POST");

// Allow specific headers.
header("Access-Control-Allow-Headers: Content-Type");

// Set content type to JSON.
header('Content-Type: application/json');

include 'conn.php';

// Get the raw input from the request body.
$data = json_decode(file_get_contents("php://input"), true);

// Check if required fields are provided.
if (!isset($data['studentId']) || !isset($data['studentName']) || !isset($data['studentGender'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields (studentId, studentName, or studentGender)"]);
    exit;
}

$studentId = $data['studentId'];
$studentName = $data['studentName'];
$studentGender = $data['studentGender'];

// Store the original studentId to identify the row (in case studentId changes).
$originalStudentId = $data['studentId']; // Assuming the modal sends the original ID as well.

// Prepare the update statement.
$sql = "UPDATE students_table SET noId = ?, studentName = ?, studentGender = ? WHERE noId = ?";
$stmt = $conn->prepare($sql);

// Bind parameters: "ssss" assumes all are strings. Adjust if studentId is an integer ("isss").
$stmt->bind_param("ssss", $studentId, $studentName, $studentGender, $originalStudentId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Student updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating student: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>