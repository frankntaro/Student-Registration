<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch full name
$stmt = $conn->prepare("SELECT full_name FROM student_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile_result = $stmt->get_result();
$profile = $profile_result->fetch_assoc();
$full_name = $profile['full_name'] ?? 'Unknown Student';

// ✅ Friendly labels for UI
$labels = [
    'tuition' => 'Tuition Fee',
    'direct' => 'Direct Cost',
    'nhif'   => 'NHIF'
];

// ✅ Get all student payments
$stmt = $conn->prepare("SELECT payment_type, control_number, amount, created_at FROM student_payments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$rows = '';

while ($row = $result->fetch_assoc()) {
    $type_key = strtolower(trim($row['payment_type']));

    // Show only if label exists
    if (!array_key_exists($type_key, $labels)) continue;

    $rows .= "<tr>
        <td>" . $labels[$type_key] . "</td>
        <td>" . $row['control_number'] . "</td>
        <td>TZS " . number_format($row['amount']) . "</td>
        <td>" . $row['created_at'] . "</td>
    </tr>";

    $total += $row['amount'];
}

// ✅ Logo as base64
$logoData = base64_encode(file_get_contents('./assets/must_logo.jpeg'));
$logoSrc = 'data:image/jpeg;base64,' . $logoData;

// ✅ Final HTML for PDF
$html = '
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
    .logo { text-align: center; margin-bottom: 20px; }
    .logo img { width: 100px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 8px; text-align: left; }
</style>
<div class="logo">
    <img src="' . $logoSrc . '" alt="MUST Logo">
    <h3>MBEYA UNIVERSITY OF SCIENCE AND TECHNOLOGY</h3>
    <p><strong>FULL PAYMENT SUMMARY</strong></p>
</div>

<p><strong>Student Name:</strong> ' . htmlspecialchars($full_name) . '</p>

<table>
    <thead>
        <tr>
            <th>Payment Type</th>
            <th>Control Number</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>' . $rows . '</tbody>
</table>

<p style="margin-top: 20px;"><strong>Total Amount:</strong> TZS ' . number_format($total) . '</p>
<p><strong>Bank:</strong> CRDB</p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("payment_summary.pdf", ["Attachment" => false]);
