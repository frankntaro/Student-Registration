<?php
session_start();
include 'db.php';
include 'log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// Fetch all student payments
$result = $conn->query("
    SELECT s.full_name, p.payment_type, p.control_number, p.amount, p.created_at
    FROM student_payments p
    JOIN student_profiles s ON s.user_id = p.user_id
    ORDER BY p.created_at DESC
");

// Define readable labels for payment types
$labels = [
    'tuition' => 'Tuition Fee',
    'direct' => 'Direct Cost',
    'nhif'   => 'NHIF'
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Student Payments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>💳 All Student Payments</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Student</th>
            <th>Payment Type</th>
            <th>Control Number</th>
            <th>Amount</th>
            <th>Created At</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td>
                <?= $labels[$row['payment_type']] ?? ucfirst($row['payment_type']) ?>
            </td>
            <td><?= htmlspecialchars($row['control_number']) ?></td>
            <td><?= number_format($row['amount']) ?> TZS</td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
