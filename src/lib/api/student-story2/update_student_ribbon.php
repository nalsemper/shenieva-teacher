<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'root'; // Replace with your database name
$username = ''; // Replace with your DB username
$password = 'shenieva_db'; // Replace with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $studentId = $data['student_id'] ?? null;
    $ribbons = $data['ribbons'] ?? 0;

    if (!$studentId || $ribbons < 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid student ID or ribbons']);
        exit;
    }

    // Update studentRibbon by incrementing
    $sql = "UPDATE students_table SET studentRibbon = studentRibbon + :ribbons WHERE pk_studentID = :studentId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ribbons' => $ribbons, 'studentId' => $studentId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>