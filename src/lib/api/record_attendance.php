<?php
// src/lib/api/record_attendance.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['fk_studentID']) || !isset($data['attendanceDateTime'])) {
    echo json_encode(["success" => false, "message" => "Missing student ID or date/time"]);
    exit;
}

$fk_studentID = $conn->real_escape_string($data['fk_studentID']);
$attendanceDateTime = $conn->real_escape_string($data['attendanceDateTime']);

$sql = "INSERT INTO attendance_table (fk_studentID, attendanceDateTime) VALUES ('$fk_studentID', '$attendanceDateTime')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Attendance recorded successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error recording attendance: " . $conn->error]);
}

$conn->close();
?>