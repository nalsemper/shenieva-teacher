<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $question = $data['question'] ?? '';
    $answer = $data['answer'] ?? '';
    $points = $data['points'] ?? 0;

    $conn = new mysqli("localhost", "root", "", "shenieva_db");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "error" => "Database connection failed"]);
        exit();
    }

    $story = isset($_GET['story']) ? $_GET['story'] : 'story1';

    $allowedStories = ['story1', 'story2', 'story3'];
    if (!in_array($story, $allowedStories)) {
        echo json_encode(["error" => "Invalid story selection"]);
        exit();
    }

    // Insert into quizzes table
    $tableName = "quizzes_{$story}";

    if ($story === 'story3') {
        // Logic for story3: Store only the question
        $stmt = $conn->prepare("INSERT INTO $tableName (question) VALUES (?)");
        $stmt->bind_param("s", $question);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
    } elseif ($story === 'story2') {
        // Logic for story2: Exclude choices logic
        $stmt = $conn->prepare("INSERT INTO $tableName (question, answer, points) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $question, $answer, $points);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
    } else {
        // Logic for story1: Includes choices
        $choices = $data['choices'] ?? [];
        $choiceStory = "choices_{$story}";
        $stmt = $conn->prepare("INSERT INTO $tableName (question, answer, points) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $question, $answer, $points);

        if ($stmt->execute()) {
            $quizId = $conn->insert_id;

            foreach ($choices as $choice) {
                $isCorrect = ($choice === $answer) ? 1 : 0;
                $stmtChoice = $conn->prepare("INSERT INTO $choiceStory (quiz_id, choice_text, is_correct) VALUES (?, ?, ?)");
                $stmtChoice->bind_param("isi", $quizId, $choice, $isCorrect);
                $stmtChoice->execute();
            }

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
}
?>
