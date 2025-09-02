<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
$apps = $conn->query("SELECT a.*, i.title FROM applications a JOIN internships i ON a.internship_id=i.id");
?>
<h1>Admin Dashboard</h1>
<table border="1">
<tr><th>Name</th><th>Email</th><th>Internship</th><th>Status</th><th>Action</th></tr>
<?php while($a=$apps->fetch()): ?>
<tr>
<td><?= $a['name'] ?></td><td><?= $a['email'] ?></td><td><?= $a['title'] ?></td><td><?= $a['status'] ?></td>
<td><a href="issue_certificate.php?id=<?= $a['id'] ?>">Issue Certificate</a></td>
</tr>
<?php endwhile; ?>
</table>