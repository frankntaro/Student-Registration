<?php
$password = 'Ntaro@must';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hash;
?>
<?php
session_start();
include 'db.php';
include 'log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Show only unseen
$recent_students = $conn->query("SELECT id, full_name FROM student_profiles WHERE viewed_by_admin = 0 ORDER BY id DESC LIMIT 5");
$recent_docs = $conn->query("SELECT id, doc_type FROM student_documents WHERE viewed_by_admin = 0 ORDER BY id DESC LIMIT 5");
$recent_payments = $conn->query("SELECT id, payment_type FROM student_payments WHERE viewed_by_admin = 0 ORDER BY id DESC LIMIT 5");
$recent_hostels = $conn->query("SELECT id, room_type FROM hostel_requests WHERE viewed_by_admin = 0 ORDER BY id DESC LIMIT 5");

// Notification count
$count_students = $conn->query("SELECT COUNT(*) as total FROM student_profiles WHERE viewed_by_admin = 0")->fetch_assoc()['total'];
$count_docs = $conn->query("SELECT COUNT(*) as total FROM student_documents WHERE viewed_by_admin = 0")->fetch_assoc()['total'];
$count_payments = $conn->query("SELECT COUNT(*) as total FROM student_payments WHERE viewed_by_admin = 0")->fetch_assoc()['total'];
$count_hostels = $conn->query("SELECT COUNT(*) as total FROM hostel_requests WHERE viewed_by_admin = 0")->fetch_assoc()['total'];
$total_notifications = $count_students + $count_docs + $count_payments + $count_hostels;


// Recent records
$recent_students = $conn->query("SELECT full_name FROM student_profiles ORDER BY id DESC LIMIT 5");
$recent_docs = $conn->query("SELECT doc_type FROM student_documents ORDER BY id DESC LIMIT 5");
$recent_payments = $conn->query("SELECT payment_type FROM student_payments ORDER BY id DESC LIMIT 5");
$recent_hostels = $conn->query("SELECT room_type FROM hostel_requests ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - MUST</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .top-nav {
            background: #004080;
            color: white;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: relative;
        }

        .nav-left img {
            width: 45px;
            height: 45px;
        }

        .nav-title h2 {
            margin: 0;
            font-size: 20px;
            text-align: center;
            flex: 1 1 100%;
            color: white;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            width: 100%;
            margin-top: 10px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .nav-links a:hover {
            color: greenyellow;
            
        }

        .notif {
            position: relative;
            font-size: 20px;
            cursor: pointer;
            margin-right: 15px;
        }

        .notif-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            font-size: 11px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            text-align: center;
            line-height: 18px;
        }

        .notif-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 35px;
            background: white;
            color: black;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 260px;
            max-height: 350px;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .notif-dropdown.show {
            display: block;
        }

        .notif-dropdown strong {
            display: block;
            padding: 8px 10px;
            background: #f0f0f0;
            font-size: 14px;
            color: #333;
        }

        .notif-dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #004080;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }

        .notif-dropdown a:last-child {
            border-bottom: none;
        }

        .notif-dropdown a:hover {
            background: #f9f9f9;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .container {
            background: white;
            margin: 20px;
            padding: 20px;
        }

        .admin-actions p {
            margin: 10px 0;
        }

        .admin-actions a {
            font-weight: bold;
            color: #004080;
            text-decoration: none;
        }

        .menu-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                margin-top: 10px;
            }

            .nav-links.show {
                display: flex;
            }

            .menu-toggle {
                display: inline-block;
                background: none;
                color: white;
                font-size: 22px;
                border: none;
                margin-left: 10px;
                cursor: pointer;
            }
        }
    </style>

    <script>
        function toggleMenu() {
            document.querySelector('.nav-links').classList.toggle('show');
        }

        function toggleDropdown() {
            document.getElementById('notifDropdown').classList.toggle('show');
        }

        window.addEventListener('click', function(event) {
            if (!event.target.closest('.notif')) {
                document.getElementById('notifDropdown').classList.remove('show');
            }
        });
    </script>
</head>
<body>

<div class="top-nav">

    <div class="nav-left">
        <img src="assets/must_logo.jpeg" alt="MUST Logo">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    </div>

    <div class="nav-title">
        <h2>Admin Dashboard - MUST</h2>
    </div>

    <div class="nav-right" style="display: flex; align-items: center;">
        <div class="notif" onclick="toggleDropdown()">
            🔔
            <div class="notif-count"><?= $total_notifications ?></div>

            <div class="notif-dropdown" id="notifDropdown">
                <strong>📋 Recent Students</strong>
                <?php while ($s = $recent_students->fetch_assoc()): ?>
                    <a href="admin_view_students.php"><?= htmlspecialchars($s['full_name']) ?></a>
                <?php endwhile; ?>

                <strong>📁 Recent Documents</strong>
                <?php while ($d = $recent_docs->fetch_assoc()): ?>
                    <a href="admin_view.documents.php"><?= strtoupper($d['doc_type']) ?></a>
                <?php endwhile; ?>

                <strong>💳 Recent Payments</strong>
                <?php while ($p = $recent_payments->fetch_assoc()): ?>
                    <a href="admin_view_payments.php"><?= ucfirst($p['payment_type']) ?> paid</a>
                <?php endwhile; ?>

                <strong>🏠 Hostel Requests</strong>
                <?php while ($h = $recent_hostels->fetch_assoc()): ?>
                    <a href="admin_view_hostels.php">Room: <?= ucfirst($h['room_type']) ?></a>
                <?php endwhile; ?>
            </div>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="nav-links">
        <a href="admin_view_students.php">View Registered Students</a>
        <a href="admin_view.documents.php">View Uploaded Documents</a>
        <a href="admin_view_payments.php">View Payments</a>
        <a href="admin_view_hostels.php">View Hostel Requests</a>
    </div>
</div>

<div class="container">
    <h3>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>. 👋</h3>
    <div class="admin-actions">
        <p>➕ <a href="create_admin.php">Create New Admin</a></p>
        <p>🔁 <a href="reset_password.php">Reset Admin Password</a></p>
        <p>🔁 <a href="reset_student_password.php">Reset Student Password</a></p>
        <p>📄 <a href="admin_view_logs.php">View Admin Logs</a></p>
        <p>🖨️ <a href="#">Print Student ID Cards</a></p>
    </div>
</div>
<script>
function toggleDropdown() {
    const dropdown = document.getElementById('notifDropdown');
    dropdown.classList.toggle('show');

    if (dropdown.classList.contains('show')) {
        fetch("mark_notifications_viewed.php", {
            method: "POST"
        })
        .then(res => res.json())
        .then(data => {
            document.querySelector('.notif-count').style.display = 'none';
        });
    }
}
</script>


</body>
</html>
