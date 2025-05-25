<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // Plain text password

    // Check only student role
    $stmt = $conn->prepare("SELECT id, password_hash, role FROM accounts WHERE username = ? AND role = 'student'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        // Verify the entered password with the hash in the database
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect to student dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid credentials or you are not registered as a student.";
        }
    } else {
        $error = "❌ Invalid credentials or you are not registered as a student.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - MUST</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial; background: #f8f8f8; }
        .container { width: 400px; margin: 80px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>🎓 Student Login</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">🔐 Login</button>
    </form>
    <p><a href="reset_password1.php">Forgot your password?</a></p>

    <p>Don't have an account? <a href="create_account.php">Register here</a></p>

    <p><a href="index.php">← Back to Home</a></p>
</div>
</body>
</html>
