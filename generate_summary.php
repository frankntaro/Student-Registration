<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

// Fetch all payments
$stmt = $conn->prepare("SELECT payment_type, control_number, amount, created_at FROM student_payments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$rows = '';
while ($row = $result->fetch_assoc()) {
    $rows .= "<tr>
        <td>" . ucfirst($row['payment_type']) . "</td>
        <td>" . $row['control_number'] . "</td>
        <td>TZS " . number_format($row['amount']) . "</td>
        <td>" . $row['created_at'] . "</td>
    </tr>";
    $total += $row['amount'];
}
// ✅ Base64 encode the logo
    $logoData = base64_encode(file_get_contents('./assets/must_logo.jpeg'));
    $logoSrc = 'data:image/jpeg;base64,' . $logoData;

$html = '
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
    .logo { text-align: center; margin-bottom: 10px; }
    .logo img { width: 100px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 8px; text-align: left; }
</style>
<div class="logo">
    <img src="' . $logoSrc . '" alt="MUST Logo">
    <h3>MBEYA UNIVERSITY OF SCIENCE AND TECHNOLOGY</h3>
    <p><strong>Full Payment Summary</strong></p>
</div>
<table>
    <tr>
        <th>Payment Type</th>
        <th>Control Number</th>
        <th>Amount</th>
        <th>Date</th>
    </tr>'
    . $rows .
'</table>
<p><strong>Total Paid:</strong> TZS ' . number_format($total) . '</p>
<p><strong>Student ID:</strong> ' . $user_id . '</p>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("payment_summary.pdf", ["Attachment" => false]);
