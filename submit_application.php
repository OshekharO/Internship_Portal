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
    
    // Verify internship exists and get details
    $checkStmt = $conn->prepare("SELECT id, title FROM internships WHERE id = ?");
    $checkStmt->execute([$internship_id]);
    $internship = $checkStmt->fetch();
    if (!$internship) {
        die("❌ Invalid internship.");
    }

    $stmt = $conn->prepare("INSERT INTO applications (internship_id, name, email) VALUES (?, ?, ?)");
    $stmt->execute([$internship_id, $name, $email]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Submitted - InternHub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --success: #10b981; }
    * { font-family: 'Segoe UI', system-ui, sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; overflow-x: hidden; }
    .success-card { background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); max-width: 500px; width: 100%; text-align: center; overflow: hidden; }
    .success-header { background: linear-gradient(135deg, var(--success), #059669); padding: 2rem 1.5rem; color: #fff; }
    .success-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 2rem; }
    .success-header h2 { font-size: 1.25rem; margin-bottom: 0.25rem; }
    .success-header p { font-size: 0.9rem; }
    .success-body { padding: 1.5rem; }
    .detail-item { background: #f8fafc; border-radius: 10px; padding: 0.875rem; margin-bottom: 0.75rem; text-align: left; }
    .detail-item .label { font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
    .detail-item .value { font-weight: 600; color: #1e293b; font-size: 0.95rem; word-wrap: break-word; overflow-wrap: break-word; }
    .info-text { font-size: 0.85rem; }
    
    @media (min-width: 576px) {
      body { padding: 2rem; }
      .success-header { padding: 3rem 2rem; }
      .success-icon { width: 80px; height: 80px; font-size: 2.5rem; margin-bottom: 1rem; }
      .success-header h2 { font-size: 1.5rem; margin-bottom: 0.25rem; }
      .success-body { padding: 2rem; }
      .detail-item { padding: 1rem; margin-bottom: 1rem; }
      .detail-item .label { font-size: 0.75rem; }
      .detail-item .value { font-size: 1rem; }
    }
  </style>
</head>
<body>
  <div class="success-card">
    <div class="success-header">
      <div class="success-icon"><i class="bi bi-check-lg"></i></div>
      <h2 class="fw-bold mb-1">Application Submitted!</h2>
      <p class="mb-0 opacity-75">Your application has been received successfully</p>
    </div>
    <div class="success-body">
      <div class="detail-item">
        <div class="label">Applicant Name</div>
        <div class="value"><?= htmlspecialchars($name) ?></div>
      </div>
      <div class="detail-item">
        <div class="label">Email Address</div>
        <div class="value"><?= htmlspecialchars($email) ?></div>
      </div>
      <div class="detail-item">
        <div class="label">Applied For</div>
        <div class="value"><?= htmlspecialchars($internship['title']) ?></div>
      </div>
      <p class="text-muted small mt-3 mb-4">
        <i class="bi bi-info-circle me-1"></i>
        We'll review your application and get back to you via email.
      </p>
      <a href="index.php" class="btn btn-primary w-100 py-2">
        <i class="bi bi-arrow-left me-2"></i>Back to Internships
      </a>
    </div>
  </div>
</body>
</html>
<?php
}
?>