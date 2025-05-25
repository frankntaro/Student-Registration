
<?php



session_start();
include 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // Use password_hash() in production

    $stmt = $conn->prepare("SELECT id, role, password_hash FROM accounts WHERE username = ? AND role = 'admin'");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();

    if (password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "❌ Incorrect password.";
    }
} else {
    $error = "❌ Admin user not found.";
}
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - MUST</title>
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
    <h2>🔐 Admin Login</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>

    <p><a href="index.php">← Back to Home</a></p>
</div>
</body>
</html>
