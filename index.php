<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Not recommended for production, use password_hash()

    // Fetch account by username and password
    $stmt = $conn->prepare("SELECT id, role FROM accounts WHERE username=? AND password_hash=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];   // ✅ Store user_id for profile linking
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>MUST Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
    <p>Don't have an account? <a href="create_account.php">Register here</a></p>
</div>
</body>
</html>
