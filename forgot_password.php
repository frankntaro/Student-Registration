<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - MUST</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; }
        .box { width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 10px; }
    </style>
</head>
<body>
<div class="box">
    <h3>🔐 Forgot Password</h3>
    <form action="send_reset.php" method="POST">
        Enter your email:<br>
        <input type="email" name="email" required><br><br>
        <button type="submit">Send Reset Link</button>
    </form>
    <p><a href="index.php">← Back to Login</a></p>
</div>
</body>
</html>
