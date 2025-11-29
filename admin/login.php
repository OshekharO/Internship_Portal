<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  
  if (empty($username) || empty($password)) {
    $error = "Please enter both username and password.";
  } else {
    // Query the database for the admin user
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin) {
      // Check if password is hashed (starts with $2y$ for bcrypt)
      if (strpos($admin['password'], '$2y$') === 0) {
        // Password is hashed, verify with password_verify
        if (password_verify($password, $admin['password'])) {
          $_SESSION['admin'] = true;
          $_SESSION['admin_id'] = $admin['id'];
          header("Location: dashboard.php");
          exit();
        } else {
          $error = "Invalid credentials. Please try again.";
        }
      } else {
        // Legacy plain-text password (for backward compatibility)
        if ($admin['password'] === $password) {
          $_SESSION['admin'] = true;
          $_SESSION['admin_id'] = $admin['id'];
          
          // Upgrade to hashed password
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          $updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
          $updateStmt->execute([$hashedPassword, $admin['id']]);
          
          header("Location: dashboard.php");
          exit();
        } else {
          $error = "Invalid credentials. Please try again.";
        }
      }
    } else {
      $error = "Invalid credentials. Please try again.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - InternHub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --secondary: #0ea5e9; }
    * { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; box-sizing: border-box; }
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; overflow-x: hidden; }
    
    .login-container { display: flex; max-width: 900px; width: 100%; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.25); }
    
    .login-left { flex: 1; background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%); padding: 2.5rem; display: flex; flex-direction: column; justify-content: center; color: #fff; position: relative; overflow: hidden; }
    .login-left::before { content: ''; position: absolute; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -50px; right: -50px; }
    .login-left::after { content: ''; position: absolute; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; bottom: -30px; left: -30px; }
    .login-left h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; position: relative; z-index: 1; }
    .login-left p { opacity: 0.9; font-size: 0.95rem; position: relative; z-index: 1; }
    .login-features { margin-top: 1.5rem; position: relative; z-index: 1; }
    .login-features li { display: flex; align-items: center; margin-bottom: 0.75rem; font-size: 0.9rem; }
    .login-features li i { background: rgba(255,255,255,0.2); padding: 0.4rem; border-radius: 6px; margin-right: 0.75rem; font-size: 0.85rem; }
    
    .login-right { flex: 1; padding: 2rem; display: flex; flex-direction: column; justify-content: center; }
    .login-right h3 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
    .login-right .subtitle { color: #64748b; margin-bottom: 1.5rem; font-size: 0.95rem; }
    
    .form-floating { margin-bottom: 0.875rem; }
    .form-floating input { border: 2px solid #e2e8f0; border-radius: 10px; height: 52px; }
    .form-floating input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99,102,241,0.15); }
    .form-floating label { color: #64748b; font-size: 0.9rem; }
    
    .btn-login { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%); border: none; border-radius: 10px; padding: 0.8rem; font-size: 1rem; font-weight: 600; width: 100%; color: #fff; transition: all 0.3s ease; }
    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(99,102,241,0.3); color: #fff; }
    
    .divider { display: flex; align-items: center; margin: 1.25rem 0; color: #94a3b8; font-size: 0.85rem; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
    .divider span { padding: 0 0.75rem; }
    
    .back-link { text-align: center; margin-top: 1rem; }
    .back-link a { color: var(--primary); text-decoration: none; font-weight: 500; font-size: 0.95rem; }
    .back-link a:hover { text-decoration: underline; }
    
    .form-check-label { font-size: 0.9rem; }
    .d-flex.justify-content-between a { font-size: 0.9rem; }
    
    @media (min-width: 768px) {
      body { padding: 2rem; }
      .login-left { padding: 3rem; }
      .login-left::before { width: 300px; height: 300px; top: -100px; right: -100px; }
      .login-left::after { width: 200px; height: 200px; bottom: -50px; left: -50px; }
      .login-left h2 { font-size: 2rem; }
      .login-left p { font-size: 1rem; }
      .login-features { margin-top: 2rem; }
      .login-features li { margin-bottom: 1rem; font-size: 0.95rem; }
      .login-features li i { padding: 0.5rem; margin-right: 1rem; }
      .login-right { padding: 3rem; }
      .login-right h3 { font-size: 1.75rem; }
      .form-floating { margin-bottom: 1rem; }
      .form-floating input { height: 56px; }
    }
    
    @media (max-width: 768px) {
      .login-left { display: none; }
      .login-container { max-width: 400px; }
    }
    
    @media (max-width: 400px) {
      body { padding: 0.75rem; }
      .login-right { padding: 1.5rem; }
      .d-flex.justify-content-between { flex-direction: column; gap: 0.5rem; align-items: flex-start !important; }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Left Panel -->
    <div class="login-left">
      <h2><i class="bi bi-mortarboard-fill me-2"></i>InternHub</h2>
      <p>Welcome back, Admin! Manage your internship portal with ease.</p>
      <ul class="login-features list-unstyled">
        <li><i class="bi bi-check2-circle"></i> Manage Applications</li>
        <li><i class="bi bi-check2-circle"></i> Issue Certificates</li>
        <li><i class="bi bi-check2-circle"></i> Track Progress</li>
        <li><i class="bi bi-check2-circle"></i> View Analytics</li>
      </ul>
    </div>
    
    <!-- Right Panel - Login Form -->
    <div class="login-right">
      <h3>Admin Login</h3>
      <p class="subtitle">Enter your credentials to access the dashboard</p>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger d-flex align-items-center py-2" role="alert">
          <i class="bi bi-exclamation-circle me-2"></i>
          <?= $error ?>
        </div>
      <?php endif; ?>
      
      <form method="post">
        <div class="form-floating">
          <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
          <label for="username"><i class="bi bi-person me-2"></i>Username</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
          <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember">
            <label class="form-check-label text-muted" for="remember">Remember me</label>
          </div>
          <a href="#" class="text-decoration-none" style="color: var(--primary);">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn btn-login">
          <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
      </form>
      
      <div class="divider"><span>or</span></div>
      
      <div class="back-link">
        <a href="../index.php"><i class="bi bi-arrow-left me-1"></i>Back to Internship Portal</a>
      </div>
    </div>
  </div>
</body>
</html>