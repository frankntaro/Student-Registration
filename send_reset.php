<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $conn->prepare("UPDATE accounts SET reset_token=?, token_expiry=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        $reset_link = "http://localhost/Must-Registration-Portal/reset_password1.php?token=$token";

        // Use PHP's mail() or an SMTP library like PHPMailer
        $subject = "MUST Password Reset";
        $message = "Click the link to reset your password:\n$reset_link\nThis link expires in 1 hour.";
        $headers = "From: noreply@must.ac.tz";

        if (mail($email, $subject, $message, $headers)) {
            echo "✅ Reset link sent to your email.";
        } else {
            echo "❌ Failed to send email.";
        }
    } else {
        echo "❌ Email not found.";
    }
}
?>
