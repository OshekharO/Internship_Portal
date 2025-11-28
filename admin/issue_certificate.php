<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$message = '';
$certCode = '';
$appId = '';

if (isset($_GET['id'])) {
  $appId = $_GET['id'];
  
  // Check if certificate already exists
  $checkStmt = $conn->prepare("SELECT certificate_code FROM certificates WHERE application_id = :id");
  $checkStmt->execute([":id" => $appId]);
  $existing = $checkStmt->fetch();
  
  if ($existing) {
    $message = "⚠️ Certificate already issued!";
    $certCode = $existing['certificate_code'];
  } else {
    $stmt = $conn->prepare("SELECT a.name, i.title FROM applications a JOIN internships i ON a.internship_id=i.id WHERE a.id=:id");
    $stmt->execute([":id" => $appId]); 
    $app = $stmt->fetch();
    
    if (!$app) {
      $message = "❌ Application not found";
    } else {
      $certCode = uniqid("CERT-"); 
      $date = date("Y-m-d");
      $stmt = $conn->prepare("INSERT INTO certificates(application_id, certificate_code, issue_date) VALUES (:a, :c, :d)");
      $stmt->execute([":a" => $appId, ":c" => $certCode, ":d" => $date]);
      
      // Update application status to selected
      $updateStmt = $conn->prepare("UPDATE applications SET status = 'selected' WHERE id = :id");
      $updateStmt->execute([":id" => $appId]);
      
      $message = "✅ Certificate Issued Successfully!";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Issue Certificate - Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h1 class="mb-4">Issue Certificate</h1>
  
  <?php if ($message): ?>
    <div class="alert <?= strpos($message, '✅') !== false ? 'alert-success' : (strpos($message, '⚠️') !== false ? 'alert-warning' : 'alert-danger') ?>">
      <?= $message ?>
    </div>
  <?php endif; ?>
  
  <?php if ($certCode): ?>
    <div class="card">
      <div class="card-body">
        <p><strong>Certificate Code:</strong> <?= htmlspecialchars($certCode) ?></p>
        <a href="../verify_certificate.php?code=<?= urlencode($certCode) ?>" class="btn btn-primary" target="_blank">View Certificate</a>
      </div>
    </div>
  <?php endif; ?>
  
  <div class="mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
  </div>
</body>
</html>