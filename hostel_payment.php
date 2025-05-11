<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];
$amount = 107100; // Fixed hostel fee
$control_number = "HOSTEL-" . rand(10000, 99999);

// Insert into payments table
$stmt = $conn->prepare("INSERT INTO student_payments (user_id, payment_type, control_number, amount) VALUES (?, ?, ?, ?)");
$payment_type = 'hostel';
$stmt->bind_param("issd", $user_id, $payment_type, $control_number, $amount);
$stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hostel Payment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Hostel Payment Control Number</h2>
    <div class="payment-details">
        <p><strong>Accommodation Type:</strong> Hostel</p>
        <p><strong>Amount Payable:</strong> TZS <?= number_format($amount) ?></p>
        <p><strong>Control Number:</strong> <span class="control-num"><?= $control_number ?></span></p>
        <p><strong>Payment Instructions:</strong></p>
        <ol>
            <li>Visit any CRDB Bank branch or use mobile banking</li>
            <li>Use this control number as payment reference</li>
            <li>Payment must be completed within 24 hours</li>
        </ol>
    </div>
</div>
</body>
</html>