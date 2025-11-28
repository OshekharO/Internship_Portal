<?php
require 'includes/db.php';
$cert = null;
$searched = false;
if (isset($_GET['code']) && !empty(trim($_GET['code']))) {
    $searched = true;
    $stmt = $conn->prepare("SELECT a.name, i.title, c.issue_date, c.certificate_code
                            FROM certificates c
                            JOIN applications a ON c.application_id = a.id
                            JOIN internships i ON a.internship_id = i.id
                            WHERE c.certificate_code = :code");
    $stmt->execute([':code' => trim($_GET['code'])]);
    $cert = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Verify Certificate - Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .certificate { border: 5px solid #000; padding: 40px; display:inline-block; background: #fff; }
    @media print {
      .no-print { display: none; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="container py-5 text-center">
  <div class="no-print mb-4">
    <h1>Certificate Verification</h1>
    <form method="get" class="row justify-content-center mb-4">
      <div class="col-md-4">
        <input type="text" class="form-control" name="code" placeholder="Enter Certificate Code" 
               value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>" required>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary">Verify</button>
      </div>
    </form>
    <a href="index.php" class="btn btn-outline-secondary mb-3">Back to Homepage</a>
  </div>
  
  <?php if ($searched): ?>
    <?php if ($cert): ?>
      <div class="certificate">
        <h1>🎓 Internship Certificate</h1>
        <p>This certifies that</p>
        <h2><?= htmlspecialchars($cert['name']) ?></h2>
        <p>has successfully completed the internship:</p>
        <h2><?= htmlspecialchars($cert['title']) ?></h2>
        <p>Issued on: <strong><?= htmlspecialchars($cert['issue_date']) ?></strong></p>
        <p><strong>Certificate Code:</strong> <?= htmlspecialchars($cert['certificate_code']) ?></p>
      </div>
      <div class="mt-4 no-print">
        <button onclick="window.print()" class="btn btn-success">Download / Print PDF</button>
      </div>
    <?php else: ?>
      <div class="alert alert-danger">❌ Invalid certificate code. Please check and try again.</div>
    <?php endif; ?>
  <?php endif; ?>
</body>
</html>