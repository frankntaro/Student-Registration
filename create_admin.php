<?php
session_start();
include 'db.php';

// Only logged-in admins can create other admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $raw_password = trim($_POST['password']);

    // Secure password hashing
    $password = password_hash($raw_password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM accounts WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "<span style='color:red;'>Username or email already exists.</span>";
    } else {
        // Insert new admin
        $stmt = $conn->prepare("INSERT INTO accounts (username, password_hash, role, email) VALUES (?, ?, 'admin', ?)");
        $stmt->bind_param("sss", $username, $password, $email);
        if ($stmt->execute()) {
            $message = "<span style='color:green;'>✅ New admin created successfully!</span>";
        } else {
            $message = "<span style='color:red;'>❌ Failed to create admin.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>➕ Create New Admin</h2>
    <?= $message ?>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Create Admin</button>
    </form>
    <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
