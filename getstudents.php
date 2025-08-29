<?php
include 'conn.php'; // include your DB connection

header('Content-Type: application/json');

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    echo json_encode(["success" => false, "message" => "Course ID is required"]);
    exit;
}

$course_id = intval($_GET['course_id']);

// Fetch students enrolled in the given course
$sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.phone_number
        FROM enrollments e
        INNER JOIN users u ON e.student_id = u.id
        WHERE e.course_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

if (count($students) > 0) {
    echo json_encode(["success" => true, "students" => $students]);
} else {
    echo json_encode(["success" => false, "message" => "No students found for this course"]);
}

$stmt->close();
$conn->close();
?>
