<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

// Fetch student profile
$stmt = $conn->prepare("SELECT program FROM student_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$program_name = strtolower($profile['program'] ?? '');

// Determine program type and fee
if (strpos($program_name, 'diploma') !== false) {
    $tuition_fee = 800000;
    $program_type = "Diploma";
} else {
    $tuition_fee = 1200000;
    $program_type = "Degree";
}

$fees = [
    'Tuition fee' => $tuition_fee,
    'Direct cost' => 270000,
    'NHIF' => 50400
];

function generateControlNumber($type) {
    return strtoupper($type) . "-MUST2025-" . rand(10000, 99999);
}

// Store control numbers shown to user
$generated = [];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fees as $type => $amount) {
        if (isset($_POST["generate_$type"])) {
            $cn = generateControlNumber($type);

            // Save to DB
            $stmt = $conn->prepare("INSERT INTO student_payments (user_id, payment_type, control_number, amount) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issd", $user_id, $type, $cn, $amount);
            $stmt->execute();

            // Show on page
            $generated[$type] = $cn;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Control Numbers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Your Program: <?= ucfirst($program_type) ?></h2>
    <p>Fees are based on: <strong><?= htmlspecialchars($profile['program']) ?></strong></p>

    <table>
        <tr>
            <th>Fee Type</th>
            <th>Amount (TZS)</th>
            <th>Generate control number</th>
            <th>Your Control Number</th>
        </tr>
        <?php foreach ($fees as $type => $amount): ?>
        <tr>
            <td><?= ucfirst($type) ?></td>
            <td><?= number_format($amount) ?></td>
            <td>
                <form method="post">
                    <button type="submit" name="generate_<?= $type ?>">Generate</button>
                </form>
            </td>
            <td style="color: green; font-weight: bold;">
                <?php if (isset($generated[$type])): ?>
                    <?= $generated[$type] ?>
                    <br>
                    <a href="generate_pdf.php?type=<?= $type ?>" target="_blank">📄 Download PDF</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Bank:</strong> CRDB</p>
    <p><strong>Reference:</strong> Student ID <?= $user_id ?></p>\
    <p style="margin-top:20px;">
    <a href="generate_summary.php" target="_blank">📄 Download Full Payment Summary PDF</a>
</p>

</div>
</body>
</html>
