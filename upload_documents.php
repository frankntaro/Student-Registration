<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}
$user_id = $_SESSION['user_id'];

$upload_path = "uploads/student_docs/";
if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Document types and expected file inputs
    $doc_types = [
        'birth_cert' => 'Birth Certificate',
        'form_iv' => 'Form IV Certificate',
        'form_vi' => 'Form VI Certificate',
        'diploma' => 'Diploma Certificate',
        'passport' => 'Passport Photo'
    ];

    foreach ($doc_types as $key => $label) {
        if (!isset($_FILES[$key])) continue;

        $file = $_FILES[$key];
        if ($file['error'] === 0) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($key === 'passport' && $ext !== 'jpg' && $ext !== 'jpeg') {
                $error .= "$label must be a JPG image.<br>";
                continue;
            } elseif ($key !== 'passport' && $ext !== 'pdf') {
                $error .= "$label must be a PDF file.<br>";
                continue;
            }

            $filename = $user_id . "_" . $key . "_" . time() . "." . $ext;
            $destination = $upload_path . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $conn->prepare("INSERT INTO student_documents (user_id, doc_type, file_url) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $key, $destination);
                $stmt->execute();
            } else {
                $error .= "Failed to upload $label.<br>";
            }
        }
    }

    if ($error === '') {
        header("Location: university_guide.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Documents</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Upload Required Documents</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        Birth Certificate (PDF): <input type="file" name="birth_cert" accept="application/pdf" required><br>
        Form IV Certificate (PDF): <input type="file" name="form_iv" accept="application/pdf" required><br>
        Form VI Certificate (PDF): <input type="file" name="form_vi" accept="application/pdf"><br>
        Diploma Certificate (PDF): <input type="file" name="diploma" accept="application/pdf"><br>
        Passport Photo (JPG only, 400x400): <input type="file" name="passport" accept="image/jpeg" required><br>
        <button type="submit">Submit</button>
    </form>
</div>
</body>
</html>
