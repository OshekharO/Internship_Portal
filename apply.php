<?php
require 'includes/db.php';
if (!isset($_GET['id'])) { die("Invalid internship"); }
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM internships WHERE id=:id");
$stmt->execute([':id'=>$id]);
$internship = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Apply - <?= htmlspecialchars($internship['title']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h1>Apply for <?= htmlspecialchars($internship['title']) ?></h1>
  <form action="submit_application.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">
    <div class="mb-3"><input class="form-control" name="name" placeholder="Your Name" ></div>
    <div class="mb-3"><input class="form-control" name="email" placeholder="Your Email" ></div>
    <div class="mb-3"><input type="file" class="form-control" name="resume" ></div>
    <button class="btn btn-success">Submit Application</button>
  </form>
</body>
</html>