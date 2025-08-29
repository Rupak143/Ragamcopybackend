<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'conn.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit;
}

$email = mysqli_real_escape_string($conn, $data['email']);

// Fetch courses created by this instructor
$sql = "SELECT id, course_title, course_description, video_path, created_by_email, created_at 
        FROM course
        WHERE created_by_email = '$email' 
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["success" => false, "message" => "Database error: " . mysqli_error($conn)]);
    exit;
}

$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
}

// Send response
if (count($courses) > 0) {
    echo json_encode(["success" => true, "courses" => $courses]);
} else {
    echo json_encode(["success" => false, "message" => "No courses found for this instructor"]);
}

mysqli_close($conn);
?>
