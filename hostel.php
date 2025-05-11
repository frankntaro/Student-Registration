<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

$student_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Automatically set to hostel since it's the only option
    $room_type = 'hostel';

    $stmt = $conn->prepare("INSERT INTO hostel_requests (user_id, room_type) VALUES (?, ?)");
    $stmt->bind_param("is", $student_id, $room_type);
    $stmt->execute();

    // Redirect to payment generation
    header("Location: hostel_payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hostel Request</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Request Hostel Accommodation</h2>
    <form method="post">
        <p>Click the button below to request hostel accommodation.</p>
        <button type="submit">Generate Payment Control Number</button>
    </form>
</div>
</body>
</html>