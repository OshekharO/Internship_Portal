<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$message = '';
$certCode = '';
$appId = '';
$app = null;

if (isset($_GET['id'])) {
  $appId = $_GET['id'];
  
  // Get application details
  $stmt = $conn->prepare("SELECT a.name, a.email, i.title, i.duration FROM applications a JOIN internships i ON a.internship_id=i.id WHERE a.id=:id");
  $stmt->execute([":id" => $appId]); 
  $app = $stmt->fetch();
  
  // Check if certificate already exists
  $checkStmt = $conn->prepare("SELECT certificate_code, issue_date FROM certificates WHERE application_id = :id");
  $checkStmt->execute([":id" => $appId]);
  $existing = $checkStmt->fetch();
  
  if ($existing) {
    $message = "warning";
    $certCode = $existing['certificate_code'];
  } else if (!$app) {
    $message = "error";
  } else {
    $certCode = uniqid("CERT-"); 
    $date = date("Y-m-d");
    $stmt = $conn->prepare("INSERT INTO certificates(application_id, certificate_code, issue_date) VALUES (:a, :c, :d)");
    $stmt->execute([":a" => $appId, ":c" => $certCode, ":d" => $date]);
    
    // Update application status to selected
    $updateStmt = $conn->prepare("UPDATE applications SET status = 'selected' WHERE id = :id");
    $updateStmt->execute([":id" => $appId]);
    
    $message = "success";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Issue Certificate - InternHub Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; }
    * { font-family: 'Segoe UI', system-ui, sans-serif; }
    body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; }
    .result-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; overflow: hidden; }
    .result-header { padding: 3rem 2rem; text-align: center; }
    .result-header.success { background: linear-gradient(135deg, var(--success), #059669); color: #fff; }
    .result-header.warning { background: linear-gradient(135deg, var(--warning), #d97706); color: #fff; }
    .result-header.error { background: linear-gradient(135deg, var(--danger), #dc2626); color: #fff; }
    .result-icon { width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2.5rem; }
    .result-body { padding: 2rem; }
    .cert-code-box { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; margin-bottom: 1.5rem; }
    .cert-code { font-family: 'Courier New', monospace; font-size: 1.25rem; font-weight: 700; color: var(--primary); letter-spacing: 1px; }
    .applicant-info { background: #f8fafc; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; }
    .applicant-info .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .applicant-info .value { font-weight: 600; color: #1e293b; }
  </style>
</head>
<body>
  <div class="container">
    <div class="result-card">
      <?php if ($message === 'success'): ?>
        <div class="result-header success">
          <div class="result-icon"><i class="bi bi-check-lg"></i></div>
          <h3 class="fw-bold mb-1">Certificate Issued!</h3>
          <p class="mb-0 opacity-75">The certificate has been generated successfully</p>
        </div>
      <?php elseif ($message === 'warning'): ?>
        <div class="result-header warning">
          <div class="result-icon"><i class="bi bi-exclamation-lg"></i></div>
          <h3 class="fw-bold mb-1">Already Issued</h3>
          <p class="mb-0 opacity-75">A certificate was already generated for this application</p>
        </div>
      <?php elseif ($message === 'error'): ?>
        <div class="result-header error">
          <div class="result-icon"><i class="bi bi-x-lg"></i></div>
          <h3 class="fw-bold mb-1">Not Found</h3>
          <p class="mb-0 opacity-75">The application could not be found</p>
        </div>
      <?php else: ?>
        <div class="result-header" style="background: linear-gradient(135deg, var(--primary), #8b5cf6); color: #fff;">
          <div class="result-icon"><i class="bi bi-award"></i></div>
          <h3 class="fw-bold mb-1">Issue Certificate</h3>
          <p class="mb-0 opacity-75">Select an application from the dashboard</p>
        </div>
      <?php endif; ?>
      
      <div class="result-body">
        <?php if ($app): ?>
        <div class="applicant-info">
          <div class="row g-3">
            <div class="col-6">
              <div class="label">Applicant Name</div>
              <div class="value"><?= htmlspecialchars($app['name']) ?></div>
            </div>
            <div class="col-6">
              <div class="label">Email</div>
              <div class="value"><?= htmlspecialchars($app['email']) ?></div>
            </div>
            <div class="col-12">
              <div class="label">Internship</div>
              <div class="value"><?= htmlspecialchars($app['title']) ?> (<?= htmlspecialchars($app['duration']) ?>)</div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        
        <?php if ($certCode): ?>
        <div class="cert-code-box">
          <div class="text-muted small mb-1">Certificate Code</div>
          <div class="cert-code"><?= htmlspecialchars($certCode) ?></div>
        </div>
        
        <div class="d-grid gap-2">
          <a href="../verify_certificate.php?code=<?= urlencode($certCode) ?>" class="btn btn-primary btn-lg" target="_blank">
            <i class="bi bi-eye me-2"></i>View Certificate
          </a>
          <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
          </a>
        </div>
        <?php else: ?>
        <a href="dashboard.php" class="btn btn-outline-secondary w-100">
          <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>