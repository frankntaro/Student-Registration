<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to register.");
}
$user_id = $_SESSION['user_id'];

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admission_no = $_POST['admission_no'];
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $nationality = $_POST['nationality'];
    $district = $_POST['district'];
    $disease = trim($_POST['disease']); // New field
    $program = $_POST['program'];
    $intake_year = $_POST['intake_year'];

    // Validation
    if (empty($disease)) {
        $error = "Disease field is required. If none, type 'None'.";
    } elseif (!isset($_FILES['hospital_report']) || $_FILES['hospital_report']['error'] !== 0) {
        $error = "Hospital report (PDF) is required.";
    } else {
        // Upload hospital report
        $file = $_FILES['hospital_report'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $error = "Hospital report must be a PDF file.";
        } else {
            $upload_path = "uploads/hospital_reports/";
            if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);
            $filename = $user_id . "_hospital_report_" . time() . ".pdf";
            $file_path = $upload_path . $filename;

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $hospital_report = $filename;

                // Insert into database
                $stmt = $conn->prepare("INSERT INTO student_profiles (
                    user_id, admission_no, full_name, gender, date_of_birth, email, phone_number,
                    nationality, district, disease, hospital_report, program, intake_year
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "isssssssssssi",
                    $user_id, $admission_no, $full_name, $gender, $dob, $email, $phone,
                    $nationality, $district, $disease, $hospital_report, $program, $intake_year
                );

                if ($stmt->execute()) {
                    $success = "Registration successful!";
                } else {
                    $error = "Error: " . $stmt->error;
                }
            } else {
                $error = "Failed to upload hospital report.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Student Registration</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php elseif ($success): ?>
        <div class="success">
            <p><?= $success ?></p>
            <p><a href="upload_documents.php" class="upload-link">👉 Now you can upload your documents or slips</a></p>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        Admission Number: <input type="text" name="admission_no" required><br>
        Full Name: <input type="text" name="full_name" required><br>
        Gender:
        <select name="gender" required>
            <option value="">Select gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select><br>
        Date of Birth: <input type="date" name="dob" required><br>
        Email: <input type="email" name="email"><br>
        Phone Number: <input type="text" name="phone_number"><br>
        Nationality: <input type="text" name="nationality"><br>
        District: <input type="text" name="district"><br>
        Disease (or type "None"): <input type="text" name="disease" placeholder="e.g., Asthma / None" required><br>
        Hospital Report (PDF only): <input type="file" name="hospital_report" accept="application/pdf" required><br>
        Program: <input type="text" name="program"><br>
        Intake Year: <input type="number" name="intake_year" value="2025"><br>
        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
