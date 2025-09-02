<?php
session_start();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if ($_POST['username']==='admin' && $_POST['password']==='admin') {
    $_SESSION['admin']=true; header("Location: dashboard.php"); exit();
  } else { echo "Invalid login"; }
}
?>
<form method="post">
  <input name="username" placeholder="user"><br>
  <input name="password" type="password" placeholder="pass"><br>
  <button>Login</button>
</form>