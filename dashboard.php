<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MUST Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div><strong>MUST Student Portal</strong></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <?php if ($role === 'student'): ?>
            <a href="register.php">Register</a>
            <a href="upload_documents.php">Upload Docs</a>
            <a href="hostel.php">Hostel</a>
        <?php elseif ($role === 'admin'): ?>
            <a href="admin_users.php">Manage Users</a>
            <a href="admin_reports.php">Reports</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>
    <button onclick="toggleDark()">🌓 Toggle Dark Mode</button>

<script>
function toggleDark() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
}

window.onload = function() {
    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark-mode");
    }
}
</script>

</div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>

    <?php if ($role === 'student'): ?>
        <p>You are logged in as a <strong>Student</strong>.</p>
        <ul>
            <li><a href="register.php">📄 Complete Bio Data</a></li>
            <li><a href="upload_documents.php">📤 Upload Academic Documents</a></li>
        
            <li><a href="hostel.php">🏠 Request Hostel</a></li>
        </ul>
    <?php elseif ($role === 'admin'): ?>
        <p>You are logged in as an <strong>Admin</strong>.</p>
        <ul>
            <li><a href="admin_users.php">👥 Manage Student Accounts</a></li>
            <li><a href="admin_reports.php">📊 View Registration Reports</a></li>
        </ul>
    <?php endif; ?>
</div>

</body>
</html>
