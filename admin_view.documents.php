<?php
session_start();
include 'db.php';
include 'log.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$result = $conn->query("
    SELECT s.full_name, d.doc_type, d.file_url, d.uploaded_at
    FROM student_documents d
    JOIN student_profiles s ON s.user_id = d.user_id
    ORDER BY d.uploaded_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Uploaded Documents</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>📁 Uploaded Documents</h2>
<table border="1">
    <tr>
        <th>Student</th>
        <th>Document Type</th>
        <th>File</th>
        <th>Uploaded At</th>
    </tr>
    <?php while ($doc = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $doc['full_name'] ?></td>
        <td><?= ucfirst(str_replace("_", " ", $doc['doc_type'])) ?></td>
        <td>
    <?php if ($doc['doc_type'] === 'passport'): ?>
        <img src="<?= $doc['file_url'] ?>" width="80" height="80" alt="Passport Photo">
    <?php else: ?>
        <a href="<?= $doc['file_url'] ?>" target="_blank">View</a>
    <?php endif; ?>
</td>

        <td><?= $doc['uploaded_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
