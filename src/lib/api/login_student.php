<?php
// src/lib/api/login_student.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'conn.php';

// Get the JSON input from the request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['idNo']) || !isset($data['studentPass'])) {
    echo json_encode(["success" => false, "message" => "Missing idNo or password"]);
    exit;
}

$idNo = $conn->real_escape_string($data['idNo']);
$studentPass = $conn->real_escape_string($data['studentPass']);

// Query to check student credentials and retrieve data
$sql = "SELECT * FROM students_table WHERE idNo = '$idNo' AND studentPass = '$studentPass'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the student data
    $student = $result->fetch_assoc();
    // Return success with student data
    echo json_encode([
        "success" => true, 
        "message" => "Login successful",
        "data" => $student
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid ID or password"]);
}

$conn->close();
?>