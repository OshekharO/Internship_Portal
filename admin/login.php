<?php
session_start();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if ($_POST['username']==='admin' && $_POST['password']==='admin') {
    $_SESSION['admin']=true; header("Location: dashboard.php"); exit();
  } else { echo "Invalid login"; }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Login - Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h2 class="mb-4">Admin Login</h2>
      <form method="post">
        <div class="mb-3">
          <input class="form-control" name="username" placeholder="Username" required>
        </div>
        <div class="mb-3">
          <input class="form-control" name="password" type="password" placeholder="Password" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
</body>
</html>