<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['type'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];
$payment_type = $_GET['type'];

// Allowed ENUM payment types
$allowed_types = ['tuition', 'direct', 'nhif'];
$labels = [
    'tuition' => 'Tuition Fee',
    'direct' => 'Direct Cost',
    'nhif'   => 'NHIF'
];

if (!in_array($payment_type, $allowed_types)) {
    die("Invalid payment type.");
}

// Fetch student full name
$stmt = $conn->prepare("SELECT full_name FROM student_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$full_name = $profile['full_name'] ?? 'Unknown Student';

// Fetch control number for the payment type
$stmt = $conn->prepare("SELECT control_number, amount, created_at FROM student_payments WHERE user_id = ? AND payment_type = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("is", $user_id, $payment_type);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    $control = $row['control_number'];
    $amount = number_format($row['amount']);
    $date = $row['created_at'];
    $label = $labels[$payment_type];

    // Encode logo image
    $logoData = base64_encode(file_get_contents('./assets/must_logo.jpeg'));
    $logoSrc = 'data:image/jpeg;base64,' . $logoData;

    $html = '
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .logo { text-align: center; margin-bottom: 10px; }
        .logo img { width: 100px; }
        .info { margin-top: 20px; }
        .info p { margin: 6px 0; }
    </style>
    <div class="logo">
        <img src="' . $logoSrc . '" alt="MUST Logo">
        <h3>MBEYA UNIVERSITY OF SCIENCE AND TECHNOLOGY</h3>
        <p><strong>Payment Receipt</strong></p>
    </div>
    <div class="info">
        <p><strong>Student Name:</strong> ' . htmlspecialchars($full_name) . '</p>
        <p><strong>Payment Type:</strong> ' . $label . '</p>
        <p><strong>Amount:</strong> TZS ' . $amount . '</p>
        <p><strong>Control Number:</strong> ' . $control . '</p>
        <p><strong>Bank:</strong> CRDB</p>
        <p><strong>Generated On:</strong> ' . $date . '</p>
    </div>';

    $pdf = new Dompdf();
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'portrait');
    $pdf->render();
    $pdf->stream("receipt_{$payment_type}.pdf", ["Attachment" => false]);
} else {
    echo "No payment found for type: $payment_type";
}
