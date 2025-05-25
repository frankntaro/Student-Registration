<?php
session_start();
include 'db.php';
include 'log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $target_user = trim($_POST['username']);
    $new_pass_raw = trim($_POST['new_password']);
    $email = trim($_POST['email']);

    // ✅ Hash securely using password_hash
    $new_pass = password_hash($new_pass_raw, PASSWORD_DEFAULT);

    // ✅ Verify user exists with matching username + email
    $stmt = $conn->prepare("SELECT id FROM accounts WHERE username = ? AND email = ? AND role = 'admin'");
    $stmt->bind_param("ss", $target_user, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // ✅ Update password
        $stmt = $conn->prepare("UPDATE accounts SET password_hash = ? WHERE username = ? AND role = 'admin'");
        $stmt->bind_param("ss", $new_pass, $target_user);
        if ($stmt->execute()) {
            log_action($conn, $_SESSION['user_id'], "Reset password for admin '$target_user'");
            $success = "✅ Password reset successful for <strong>$target_user</strong>";
        } else {
            $error = "❌ Error updating password. Try again.";
        }
    } else {
        $error = "❌ Admin with that username and email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Admin Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>🔐 Reset Admin Password</h2>

    <?php if ($success): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        Username: <input type="text" name="username" required><br>
        New Password: <input type="password" name="new_password" required><br>
        Confirm Email: <input type="email" name="email" required><br>
        <button type="submit">🔄 Reset Password</button>
    </form>
    <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
