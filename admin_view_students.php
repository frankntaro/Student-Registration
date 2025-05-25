<?php
session_start();
include 'db.php';
include 'log.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$result = $conn->query("SELECT * FROM student_profiles ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>📋 Registered Students</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Admission No</th>
        <th>Full Name</th>
        <th>Program</th>
        <th>Nationality</th>
        <th>District</th>
        <th>Disease</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['admission_no'] ?></td>
        <td><?= $row['full_name'] ?></td>
        <td><?= $row['program'] ?></td>
        <td><?= $row['nationality'] ?></td>
        <td><?= $row['district'] ?></td>
        <td><?= $row['disease'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
