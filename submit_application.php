<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['internship_id']) || empty($_POST['name']) || empty($_POST['email'])) {
        die("❌ Please fill in all required fields.");
    }
    
    $internship_id = (int)$_POST['internship_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format.");
    }
    
    // Verify internship exists
    $checkStmt = $conn->prepare("SELECT id FROM internships WHERE id = ?");
    $checkStmt->execute([$internship_id]);
    if (!$checkStmt->fetch()) {
        die("❌ Invalid internship.");
    }
    
    $resumePath = null;

    // Check if a file was uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file extension
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            die("❌ Only PDF, DOC, and DOCX files are allowed.");
        }
        
        // Additional MIME type check
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = mime_content_type($_FILES["resume"]["tmp_name"]);
        
        if (!in_array($fileType, $allowedTypes)) {
            die("❌ Invalid file type detected.");
        }
        
        // Limit file size to 5MB
        if ($_FILES["resume"]["size"] > 5 * 1024 * 1024) {
            die("❌ File size must be less than 5MB.");
        }
        
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\.\-\_]/", "", basename($_FILES["resume"]["name"]));
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $targetFile)) {
            $resumePath = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO applications (internship_id, name, email, resume) VALUES (?, ?, ?, ?)");
    $stmt->execute([$internship_id, $name, $email, $resumePath]);

    echo "<!DOCTYPE html>
    <html>
    <head>
      <title>Application Submitted</title>
      <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
    </head>
    <body class='container py-5 text-center'>
      <div class='alert alert-success'>✅ Application submitted successfully!</div>
      <a href='index.php' class='btn btn-primary'>Back to Internships</a>
    </body>
    </html>";
}
?>