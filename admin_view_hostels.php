<?php
session_start();
include 'db.php';
include 'log.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$result = $conn->query("
    SELECT s.full_name, h.room_type, h.created_at
    FROM hostel_requests h
    JOIN student_profiles s ON s.user_id = h.user_id
    ORDER BY h.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hostel Requests</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>🏠 Hostel Requests</h2>
<table border="1">
    <tr>
        <th>Student</th>
        <th>Room Type</th>
        <th>Requested At</th>
    </tr>
    <?php while ($r = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $r['full_name'] ?></td>
        <td><?= ucfirst($r['room_type']) ?></td>
        <td><?= $r['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
