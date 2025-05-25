<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$result = $conn->query("
    SELECT l.action, l.created_at, a.username
    FROM admin_logs l
    JOIN accounts a ON l.admin_id = a.id
    ORDER BY l.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Action Logs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>🧾 Admin Activity Logs</h2>
    <table border="1">
        <tr>
            <th>Admin</th>
            <th>Action</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($log = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $log['username'] ?></td>
            <td><?= $log['action'] ?></td>
            <td><?= $log['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
