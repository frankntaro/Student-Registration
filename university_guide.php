<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>University Guide</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>University Student Guide</h2>
    <p>Please read the following carefully before proceeding:</p>
    <ul>
        <li>✔️ All uploaded documents must be valid and certified copies.</li>
        <li>✔️ Tuition fees must be paid before registration deadline.</li>
        <li>✔️ NHIF is mandatory unless you have an alternative approved cover.</li>
        <li>✔️ Passport photo must be clear and recent (400x400px).</li>
        <li>✔️ Misleading info may lead to cancellation of admission.</li>
    </ul>

    <form method="post" action="generate_payment.php">
        <button type="submit" name="accept">✅ I Accept and Understand</button>
    </form>
</div>
</body>
</html>
<?php
