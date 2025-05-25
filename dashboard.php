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

    <!-- Font Awesome for Hamburger Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            transition: background 0.3s, color 0.3s;
        }

        body.dark-mode {
            background-color: #121212;
            color: #f1f1f1;
        }

        .navbar {
            background-color:#004080;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        body.dark-mode .navbar {
            background-color: #1f1f1f;
        }

        .navbar a {
            margin: 0 10px;
            color: white;
            text-decoration: none;
        }

        body.dark-mode .navbar a {
            color: #f1f1f1;
        }

        .container {
            padding: 20px;
        }

        body.dark-mode .container {
            background-color: black;
            border-radius: 5px;
        }

        button {
            padding: 6px 12px;
            margin-left: 10px;
        }

        body.dark-mode button {
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
        }

        a {
            color: #007bff;
        }

        body.dark-mode a {
            color: #90caf9;
        }

        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: white;
            background-color: green;
            border-radius: 5px;
            padding: 5px;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                background-color: #004080;
                padding: 10px;
                position: absolute;
                top: 50px;
                right: 10px;
                width: 200px;
                z-index: 1000;
                border-radius: 5px;
            }

            .nav-links.show {
                display: flex;
            }

            body.dark-mode .nav-links {
                background-color: #1f1f1f;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>MUST Student Portal</strong></div>

    <div class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="nav-links" id="navLinks">
        <a href="dashboard.php" style="background-color:black; border-radius:5px">Dashboard</a>
        <?php if ($role === 'student'): ?>
            <a href="register.php" style="background-color:black; border-radius:5px">Register</a>
            <a href="upload_documents.php" style="background-color:black; border-radius:5px">Upload Docs</a>
            <a href="hostel.php" style="background-color:black; border-radius:5px">Hostel</a>
        <?php elseif ($role === 'admin'): ?>
            <a href="admin_users.php">Manage Users</a>
            <a href="admin_reports.php">Reports</a>
        <?php endif; ?>
        <a href="logout.php" style="background-color:black; border-radius:5px">Logout</a>
    </div>

    <button onclick="toggleDark()">🌓 Toggle Dark Mode</button>
</div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>

    <?php if ($role === 'student'): ?>
        <p>You are logged in as a <strong>Student</strong>.</p>
        <ul>
            <li><a href="register.php">📄 Complete Required Document Data</a></li>
            <li><a href="upload_documents.php">📤 Upload Academic Documents</a></li>
            <li><a href="hostel_payment.php">🏠 Request Hostel</a></li>
        </ul>
    
        
    <?php endif; ?>
</div>

<!-- JavaScript for Dark Mode and Menu Toggle -->
<script>
    function toggleDark() {
        document.body.classList.toggle("dark-mode");
        localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
    }

    window.onload = function() {
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.add("dark-mode");
        }
    };

    function toggleMenu() {
        document.getElementById('navLinks').classList.toggle('show');
    }
</script>

</body>
</html>
