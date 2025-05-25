<?php
include 'db.php';

$error = ''; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email address.";
    } 
    // Validate password match
    elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } 
    else {
        // Securely hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $role = "student"; // Default role is student

        // Check if username or email already exists in the database
        $stmt = $conn->prepare("SELECT id FROM accounts WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "❌ Username or Email already registered.";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO accounts (username, password_hash, role, email) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password_hash, $role, $email);
            if ($stmt->execute()) {
                header("Location: student_login.php"); // Redirect to login page after successful registration
                exit();
            } else {
                $error = "❌ Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Student Account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Create Student Account</h2>

    <?php if (!empty($error)): ?>
        <p class="error" style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        Username: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Confirm Password: <input type="password" name="confirm_password" required><br>
        <button type="submit">Create Account</button>
    </form>

    <p>Already have an account? <a href="student_login.php">Login here</a></p>
</div>
</body>
</html>
