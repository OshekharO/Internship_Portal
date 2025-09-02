<?php
require 'includes/db.php';
if (isset($_GET['code'])) {
    $stmt = $conn->prepare("SELECT a.name, i.title, c.issue_date
                            FROM certificates c
                            JOIN applications a ON c.application_id = a.id
                            JOIN internships i ON a.internship_id = i.id
                            WHERE c.certificate_code = :code");
    $stmt->execute([':code' => $_GET['code']]);
    $cert = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Verify Certificate</title>
  <style>
    body { font-family: Arial; text-align: center; padding: 50px; }
    .certificate { border: 5px solid #000; padding: 40px; display:inline-block; }
  </style>
</head>
<body>
  <?php if ($cert): ?>
    <div class="certificate">
      <h1>Internship Certificate</h1>
      <p>This certifies that</p>
      <h2><?= htmlspecialchars($cert['name']) ?></h2>
      <p>completed the internship:</p>
      <h2><?= htmlspecialchars($cert['title']) ?></h2>
      <p>Issued on <?= $cert['issue_date'] ?></p>
      <p><strong>Code:</strong> <?= $_GET['code'] ?></p>
    </div>
    <br><button onclick="window.print()">Download / Print PDF</button>
  <?php else: ?>
    <p>❌ Invalid certificate</p>
  <?php endif; ?>
</body>
</html>