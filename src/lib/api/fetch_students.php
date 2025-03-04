<?php

 // fetch_students.php

        // Allow requests from any origin (for development purposes). 
        // In production, you should restrict this to your Svelte app's origin.
        header("Access-Control-Allow-Origin: *"); 

        // Allow the GET method.
        header("Access-Control-Allow-Methods: GET");

        // Allow specific headers.
        header("Access-Control-Allow-Headers: Content-Type");

        //Set content type to json
        header('Content-Type: application/json');
        
include 'conn.php';

$sql = "SELECT * FROM students_table";
$result = $conn->query($sql);

$students = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode($students);
} else {
    echo json_encode(["message" => "No students found"]);
}

$conn->close();
?>
