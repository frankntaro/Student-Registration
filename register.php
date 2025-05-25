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
    <!-- Bootstrap CSS (if not already included) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">



<!-- Bootstrap-select CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

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
        <label for="nationality">Nationality:</label>
<select name="nationality" class="selectpicker form-control" data-live-search="true" required>
    <option value="">Select nationality</option>
    <?php
    $countries = [ "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Argentina", "Armenia", "Australia", "Austria",
        "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", 
        "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", 
        "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", 
        "China", "Colombia", "Comoros", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", 
        "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", 
        "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", 
        "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", 
        "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", 
        "Ivory Coast", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", 
        "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", 
        "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", 
        "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", 
        "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", 
        "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", 
        "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Republic of the Congo", 
        "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", 
        "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", 
        "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", 
        "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", 
        "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", 
        "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", 
        "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", 
        "Vietnam", "Yemen", "Zambia", "Zimbabwe"
    ];
    foreach ($countries as $country) {
        echo "<option value=\"$country\">$country</option>";
    }
    ?>
</select><br>
<br>
        District: <input type="text" name="district"><br>
        Disease (or type "None"): <input type="text" name="disease" placeholder="e.g., Asthma / None" required><br>
        Hospital Report (PDF only): <input type="file" name="hospital_report" accept="application/pdf" required><br>
        Program: <input type="text" name="program"><br>
        Intake Year: <input type="number" name="intake_year" value="2025"><br>
        <button type="submit">Submit</button>
    </form>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (required by bootstrap-select) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap-select JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>


</body>
</html>
