<?php
require 'includes/db.php';
if (!isset($_GET['id'])) { 
  header("Location: index.php");
  exit();
}
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM internships WHERE id=:id");
$stmt->execute([':id'=>$id]);
$internship = $stmt->fetch();
if (!$internship) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Apply - <?= htmlspecialchars($internship['title']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h1>Apply for <?= htmlspecialchars($internship['title']) ?></h1>
  <p class="text-muted"><?= htmlspecialchars($internship['description']) ?></p>
  <p><strong>Duration:</strong> <?= htmlspecialchars($internship['duration']) ?></p>
  <hr>
  <form action="submit_application.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="internship_id" value="<?= (int)$internship['id'] ?>">
    <div class="mb-3"><input class="form-control" name="name" placeholder="Your Name" required></div>
    <div class="mb-3"><input class="form-control" type="email" name="email" placeholder="Your Email" required></div>
    <div class="mb-3"><input type="file" class="form-control" name="resume" accept=".pdf,.doc,.docx"></div>
    <button class="btn btn-success">Submit Application</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</body>
</html>