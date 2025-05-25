<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}

// Mark all as viewed
$conn->query("UPDATE student_profiles SET viewed_by_admin = 1 WHERE viewed_by_admin = 0");
$conn->query("UPDATE student_documents SET viewed_by_admin = 1 WHERE viewed_by_admin = 0");
$conn->query("UPDATE student_payments SET viewed_by_admin = 1 WHERE viewed_by_admin = 0");
$conn->query("UPDATE hostel_requests SET viewed_by_admin = 1 WHERE viewed_by_admin = 0");

echo json_encode(["status" => "success"]);
?>
