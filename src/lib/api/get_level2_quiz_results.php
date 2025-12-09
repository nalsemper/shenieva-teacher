<?php
// Include CORS handling
include_once __DIR__ . '/cors.php';

header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
    exit;
}

$storyTitle = isset($_GET['storyTitle']) ? trim($_GET['storyTitle']) : null;
$studentID = isset($_GET['studentID']) ? $_GET['studentID'] : null;

try {
    if ($storyTitle) {
        $stmt = $conn->prepare(
            "SELECT 
                lq.quizID,
                lq.studentID,
                s.studentName,
                s.idNo,
                lq.storyTitle,
                lq.question,
                lq.correctAnswer,
                lq.selectedAnswer,
                lq.point AS score,
                lq.attempt,
                lq.createdAt
            FROM level2_quiz lq
            INNER JOIN students_table s ON lq.studentID = s.pk_studentID
            WHERE lq.storyTitle = ? AND s.archive = 0
            ORDER BY s.studentName, lq.attempt DESC, lq.createdAt DESC"
        );
        $stmt->bind_param('s', $storyTitle);
    } else {
        if ($studentID) {
            $stmt = $conn->prepare(
                "SELECT 
                lq.quizID,
                lq.studentID,
                s.studentName,
                s.idNo,
                lq.storyTitle,
                lq.question,
                lq.correctAnswer,
                lq.selectedAnswer,
                lq.point AS score,
                lq.attempt,
                lq.createdAt
            FROM level2_quiz lq
            INNER JOIN students_table s ON lq.studentID = s.pk_studentID
            WHERE lq.studentID = ? AND s.archive = 0
            ORDER BY lq.attempt DESC, lq.createdAt DESC"
            );
            $stmt->bind_param('i', $studentID);
        } else {
            $stmt = $conn->prepare(
            "SELECT 
                lq.quizID,
                lq.studentID,
                s.studentName,
                s.idNo,
                lq.storyTitle,
                lq.question,
                lq.correctAnswer,
                lq.selectedAnswer,
                lq.point AS score,
                lq.attempt,
                lq.createdAt
            FROM level2_quiz lq
            INNER JOIN students_table s ON lq.studentID = s.pk_studentID
            WHERE s.archive = 0
            ORDER BY s.studentName, lq.storyTitle, lq.attempt DESC, lq.createdAt DESC"
        );
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $rows,
        'count' => count($rows)
    ]);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
