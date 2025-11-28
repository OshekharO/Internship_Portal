<?php
require 'includes/db.php';
$result = $conn->query("SELECT * FROM internships");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Available Internships</h1>
    <div>
      <a href="verify_certificate.php" class="btn btn-outline-secondary">Verify Certificate</a>
      <a href="admin/login.php" class="btn btn-outline-primary">Admin Login</a>
    </div>
  </div>
  <div class="row">
    <?php while ($row = $result->fetch()): ?>
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <p><strong>Duration:</strong> <?= htmlspecialchars($row['duration']) ?></p>
            <a href="apply.php?id=<?= (int)$row['id'] ?>" class="btn btn-primary">Apply</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>