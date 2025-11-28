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
// Generate icon based on title
$icons = ['Web' => 'bi-code-slash', 'Data' => 'bi-graph-up', 'Digital' => 'bi-megaphone', 'Design' => 'bi-palette'];
$icon = 'bi-briefcase';
foreach ($icons as $key => $val) {
  if (stripos($internship['title'], $key) !== false) { $icon = $val; break; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply - <?= htmlspecialchars($internship['title']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --success: #10b981; --dark: #1e293b; }
    * { font-family: 'Segoe UI', system-ui, sans-serif; box-sizing: border-box; }
    body { background: #f8fafc; min-height: 100vh; overflow-x: hidden; }
    
    .navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0.75rem 0; }
    .navbar-brand { font-weight: 700; font-size: 1.25rem; color: var(--primary) !important; }
    
    .apply-header { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%); padding: 2rem 0; color: #fff; }
    
    .apply-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-top: -2rem; position: relative; z-index: 10; }
    
    .internship-icon { width: 56px; height: 56px; min-width: 56px; background: linear-gradient(135deg, var(--primary), #8b5cf6); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; }
    
    .internship-title { font-size: 1.25rem; font-weight: 700; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.4; }
    
    .tag { display: inline-block; padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 500; margin-right: 0.4rem; margin-bottom: 0.4rem; white-space: nowrap; }
    .tag-duration { background: #dbeafe; color: #1d4ed8; }
    .tag-type { background: #dcfce7; color: #166534; }
    
    .form-label { font-weight: 500; color: #475569; font-size: 0.9rem; }
    .form-control { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.65rem 0.875rem; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    
    .btn-apply { background: var(--success); border: none; border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 600; color: #fff; }
    .btn-apply:hover { background: #059669; color: #fff; }
    
    .about-box { background: #f8fafc; border-radius: 10px; padding: 1rem; }
    .about-box p { font-size: 0.9rem; line-height: 1.6; word-wrap: break-word; overflow-wrap: break-word; }
    
    /* Responsive improvements */
    @media (min-width: 768px) {
      .navbar { padding: 1rem 0; }
      .navbar-brand { font-size: 1.5rem; }
      .apply-header { padding: 3rem 0; }
      .apply-card { margin-top: -3rem; }
      .internship-icon { width: 64px; height: 64px; min-width: 64px; font-size: 1.75rem; }
      .internship-title { font-size: 1.5rem; }
    }
    
    @media (max-width: 576px) {
      .container { padding-left: 1rem; padding-right: 1rem; }
      .apply-card { padding: 1.25rem !important; }
      .d-flex.mb-4 { flex-direction: column; align-items: flex-start !important; }
      .internship-icon { margin-bottom: 1rem; }
      .internship-icon + div { width: 100%; }
      .d-flex.gap-3.mt-4 { flex-direction: column; }
      .d-flex.gap-3.mt-4 .btn { width: 100%; }
      .about-box { padding: 0.875rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill me-2"></i>InternHub</a>
      <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Internships</a>
    </div>
  </nav>

  <!-- Header -->
  <section class="apply-header">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Internships</a></li>
          <li class="breadcrumb-item active text-white"><?= htmlspecialchars($internship['title']) ?></li>
        </ol>
      </nav>
      <h1 class="fw-bold mb-0">Apply for Internship</h1>
    </div>
  </section>

  <!-- Application Form -->
  <div class="container pb-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="apply-card p-4 p-md-5">
          <!-- Internship Details -->
          <div class="d-flex mb-4">
            <div class="internship-icon me-3">
              <i class="bi <?= $icon ?>"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-1"><?= htmlspecialchars($internship['title']) ?></h3>
              <p class="text-muted mb-2"><i class="bi bi-building me-1"></i>InternHub Company</p>
              <div>
                <span class="tag tag-duration"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($internship['duration']) ?></span>
                <span class="tag tag-type"><i class="bi bi-laptop me-1"></i>Remote</span>
              </div>
            </div>
          </div>
          
          <div class="about-box mb-4">
            <h6 class="fw-semibold mb-2"><i class="bi bi-info-circle me-2"></i>About this Internship</h6>
            <p class="text-muted mb-0"><?= htmlspecialchars($internship['description']) ?></p>
          </div>
          
          <hr class="my-4">
          
          <!-- Application Form -->
          <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Application Form</h5>
          <form action="submit_application.php" method="post">
            <input type="hidden" name="internship_id" value="<?= (int)$internship['id'] ?>">
            
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input class="form-control" name="name" placeholder="Enter your full name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input class="form-control" type="email" name="email" placeholder="Enter your email" required>
              </div>
            </div>
            
            <div class="d-flex gap-3 mt-4">
              <button type="submit" class="btn btn-apply">
                <i class="bi bi-send me-2"></i>Submit Application
              </button>
              <a href="index.php" class="btn btn-light">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>