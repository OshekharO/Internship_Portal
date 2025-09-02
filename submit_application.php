<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $internship_id = $_POST['internship_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $resumePath = null;

    // Check if a file was uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = time() . "_" . basename($_FILES["resume"]["name"]);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $targetFile)) {
            $resumePath = $targetFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO applications (internship_id, name, email, resume) VALUES (?, ?, ?, ?)");
    $stmt->execute([$internship_id, $name, $email, $resumePath]);

    echo "✅ Application submitted successfully!";
}
?>