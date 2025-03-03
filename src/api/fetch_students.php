<?php
header('Content-Type: application/json'); 
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require 'conn.php'; 

$query = "SELECT * FROM students_table";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode($students);
} else {
    echo json_encode([]);
}

$conn->close();
?>
