<?php
// src/lib/api/get_student_items.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'conn.php';

if (!isset($_GET['studentID'])) {
    echo json_encode(["success" => false, "message" => "Missing student ID"]);
    exit;
}

$studentID = $conn->real_escape_string($_GET['studentID']);

$sql = "SELECT i.itemName 
        FROM student_items si 
        JOIN items_table i ON si.itemID = i.itemID 
        WHERE si.studentID = '$studentID'";
$result = $conn->query($sql);

if ($result === FALSE) {
    echo json_encode(["success" => false, "message" => "Error fetching gifts: " . $conn->error]);
    exit;
}

$gifts = [];
while ($row = $result->fetch_assoc()) {
    $gifts[] = $row['itemName'];
}

echo json_encode(["success" => true, "gifts" => $gifts]);

$conn->close();
?>