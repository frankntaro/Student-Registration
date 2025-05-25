<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];
$generated = []; // Initialize empty array

// Fetch student details (matches document's data structure)
$stmt = $conn->prepare("SELECT program, full_name FROM student_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

$program_name = strtolower($profile['program'] ?? '');
$full_name = $profile['full_name'] ?? 'Unknown Student'; // For document's name display

// Program type determination (matches document's degree/diploma distinction)
if (strpos($program_name, 'diploma') !== false) {
    $tuition_fee = 800000;
    $program_type = "Diploma";
} else {
    $tuition_fee = 1200000;
    $program_type = "Degree";
}

$fees = [
    'tuition' => $tuition_fee,
    'direct' => 270000,
    'nhif' => 50400
];

$labels = [
    'tuition' => 'Tuition Fee',
    'direct' => 'Direct Cost',
    'nhif' => 'NHIF'
];

function generateControlNumber($type) {
    return strtoupper($type) . "-MUST2025-" . rand(10000, 99999);
}

// Fetch existing payments (matches document's control number format)
$stmt = $conn->prepare("SELECT payment_type, control_number FROM student_payments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $generated[$row['payment_type']] = $row['control_number'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $clicked_type = $_POST['generate'];

    if (array_key_exists($clicked_type, $fees) && !isset($generated[$clicked_type])) {
        $amount = $fees[$clicked_type];
        $cn = generateControlNumber($clicked_type);

        $stmt = $conn->prepare("INSERT INTO student_payments (user_id, payment_type, control_number, amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $user_id, $clicked_type, $cn, $amount);
        $stmt->execute();

        $generated[$clicked_type] = $cn;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Control Numbers</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        button[disabled] { background: #aaa; cursor: not-allowed; }
        .container { max-width: 800px; margin: auto; padding: 20px; background: #f9f9f9; }
    </style>
</head>
<body>
<div class="container">
    <h2>Your Program: <?= ucfirst($program_type) ?></h2>
    <p>Fees based on: <strong><?= htmlspecialchars($profile['program']) ?></strong></p>

    <table>
        <tr>
            <th>Fee Type</th>
            <th>Amount (TZS)</th>
            <th>Action</th>
            <th>Control Number</th>
        </tr>

        <?php foreach ($fees as $type => $amount): ?>
        <tr>
            <td><?= $labels[$type] ?></td>
            <td><?= number_format($amount) ?></td>
            <td>
                <form method="post" style="margin: 0;">
                    <input type="hidden" name="generate" value="<?= htmlspecialchars($type) ?>">
                    <button type="submit" <?= isset($generated[$type]) ? 'disabled' : '' ?>>
                        <?= isset($generated[$type]) ? 'Generated' : 'Generate' ?>
                    </button>
                </form>
            </td>
            <td style="color: green;">
                <?php if (isset($generated[$type])): ?>
                    <?= $generated[$type] ?><br>
                    <a href="generate_pdf.php?type=<?= urlencode($type) ?>" target="_blank">📄 Download PDF</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Bank:</strong> CRDB</p>
    <p><strong>Student Name:</strong> <?= strtoupper(htmlspecialchars($full_name)) ?></p>

    <?php if (!empty($generated) && count($generated) === count($fees)): ?>
        <p style="margin-top: 20px;">
            <a href="generate_summary.php" target="_blank">📄 Download Full Payment Summary PDF</a>
        </p>
    <?php endif; ?>
</div>
</body>
</html>