<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

// Handle logout
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit();
}

$apps = $conn->query("SELECT a.*, i.title FROM applications a JOIN internships i ON a.internship_id=i.id");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard - Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Admin Dashboard</h1>
    <a href="?logout=1" class="btn btn-outline-danger">Logout</a>
  </div>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr><th>Name</th><th>Email</th><th>Internship</th><th>Resume</th><th>Status</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php while($a=$apps->fetch()): ?>
      <tr>
        <td><?= htmlspecialchars($a['name']) ?></td>
        <td><?= htmlspecialchars($a['email']) ?></td>
        <td><?= htmlspecialchars($a['title']) ?></td>
        <td><?= $a['resume'] ? '<a href="../' . htmlspecialchars($a['resume']) . '" target="_blank">View</a>' : 'N/A' ?></td>
        <td><?= htmlspecialchars($a['status']) ?></td>
        <td><a href="issue_certificate.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-success">Issue Certificate</a></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a href="../index.php" class="btn btn-secondary">Back to Homepage</a>
</body>
</html>