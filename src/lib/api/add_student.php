<?php
include 'conn.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Get the JSON input from the request
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data["idNo"]) && !empty($data["studentName"]) && !empty($data["studentPass"])) {
    
    $idNo = $conn->real_escape_string($data["idNo"]);  // Ensure idNo is provided
    $studentName = $conn->real_escape_string($data["studentName"]);
    $studentPass = $conn->real_escape_string($data["studentPass"]);

    // 🔍 **Step 1: Check if idNo already exists**
    $checkQuery = "SELECT idNo FROM students_table WHERE idNo = '$idNo'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Student ID already exists!"]);
    } else {
        // 📝 **Step 2: Insert only if idNo is unique**
        $sql = "INSERT INTO students_table (idNo, studentName, studentPass) 
                VALUES ('$idNo', '$studentName', '$studentPass')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Student added successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "All fields are required!"]);
}


$conn->close();
?>
