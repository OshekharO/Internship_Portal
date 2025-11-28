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

    $stmt = $conn->prepare("INSERT INTO applications (internship_id, name, email) VALUES (?, ?, ?)");
    $stmt->execute([$internship_id, $name, $email]);

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