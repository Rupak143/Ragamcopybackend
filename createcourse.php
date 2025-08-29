<?php
header("Content-Type: application/json");
include 'conn.php';

// Read raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (
    isset($data['course_title']) &&
    isset($data['course_description']) &&
    isset($data['video_path']) &&
    isset($data['created_by_email'])
) {
    $course_title = $data['course_title'];
    $course_description = $data['course_description'];
    $video_path = $data['video_path'];
    $created_by_email = $data['created_by_email'];

    // Use prepared statements
    $stmt = $conn->prepare("INSERT INTO course 
        (course_title, course_description, video_path, created_by_email, created_at) 
        VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $course_title, $course_description, $video_path, $created_by_email);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Course inserted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input. Please provide all required fields."
    ]);
}

$conn->close();
?>