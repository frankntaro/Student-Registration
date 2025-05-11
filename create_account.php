<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = md5($password); // use password_hash() in production
        $role = "student";

        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM accounts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
        } else {
            $stmt = $conn->prepare("INSERT INTO accounts (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password_hash, $role);
            if ($stmt->execute()) {
                header("Location: index.php"); // Go to login page
                exit();
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Student Account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Create Student Account</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Confirm Password: <input type="password" name="confirm_password" required><br>
        <button type="submit">Create Account</button>
    </form>
    <p>Already have an account? <a href="index.php">Login here</a></p>
</div>
</body>
</html>
