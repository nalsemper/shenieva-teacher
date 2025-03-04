<?php

// Allow requests from any origin (for development purposes).
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'conn.php';

// Get the JSON input from the request
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data["studentName"]) && !empty($data["studentGender"]) && !empty($data["studentLevel"]) && !empty($data["studentRibbon"]) && isset($data["studentColtrash"])) {
    $studentName = $conn->real_escape_string($data["studentName"]);
    $studentGender = $conn->real_escape_string($data["studentGender"]);
    $studentLevel = $conn->real_escape_string($data["studentLevel"]);
    $studentRibbon = $conn->real_escape_string($data["studentRibbon"]);
    $studentColtrash = intval($data["studentColtrash"]);

    $sql = "INSERT INTO students_table (studentName, studentGender, studentLevel, studentRibbon, studentColtrash) 
            VALUES ('$studentName', '$studentGender', '$studentLevel', '$studentRibbon', '$studentColtrash')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Student added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding student: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
}

$conn->close();
?>
