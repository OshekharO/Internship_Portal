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
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --secondary: #0ea5e9; --success: #10b981; --dark: #1e293b; }
    * { font-family: 'Segoe UI', system-ui, sans-serif; box-sizing: border-box; }
    body { background: #f8fafc; min-height: 100vh; overflow-x: hidden; }
    
    .navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0.75rem 0; }
    .navbar-brand { font-weight: 700; font-size: 1.25rem; color: var(--primary) !important; }
    
    .apply-header { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 50%, var(--secondary) 100%); padding: 2.5rem 0; color: #fff; }
    
    .apply-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-top: -2rem; position: relative; z-index: 10; }
    
    .internship-icon { width: 60px; height: 60px; min-width: 60px; background: linear-gradient(135deg, var(--primary), #8b5cf6); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; flex-shrink: 0; }
    
    .internship-title { font-size: 1.25rem; font-weight: 700; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.4; color: var(--dark); margin-bottom: 0.5rem; }
    
    .company-info { color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; }
    .company-info i { color: var(--primary); }
    
    .tag { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.8rem; font-weight: 500; margin-right: 0.5rem; margin-bottom: 0.5rem; }
    .tag i { font-size: 0.75rem; }
    .tag-duration { background: #dbeafe; color: #1d4ed8; }
    .tag-type { background: #dcfce7; color: #166534; }
    .tag-location { background: #fef3c7; color: #92400e; }
    
    .form-section { background: #f8fafc; border-radius: 12px; padding: 1.5rem; margin-top: 1.5rem; }
    .form-section-title { font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .form-section-title i { color: var(--primary); }
    
    .form-label { font-weight: 500; color: #475569; font-size: 0.9rem; margin-bottom: 0.5rem; }
    .form-control { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.2s; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    .form-control::placeholder { color: #94a3b8; }
    
    .about-box { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 1.25rem; border-left: 4px solid var(--primary); }
    .about-box h6 { color: var(--dark); font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; }
    .about-box h6 i { color: var(--primary); }
    .about-box p { font-size: 0.9rem; line-height: 1.7; word-wrap: break-word; overflow-wrap: break-word; color: #64748b; }
    
    .btn-apply { background: linear-gradient(135deg, var(--success), #059669); border: none; border-radius: 10px; padding: 0.85rem 2rem; font-weight: 600; color: #fff; font-size: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-apply:hover { background: linear-gradient(135deg, #059669, #047857); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16,185,129,0.3); }
    
    .btn-cancel { background: #fff; border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1.5rem; font-weight: 500; color: #64748b; transition: all 0.2s; }
    .btn-cancel:hover { border-color: #cbd5e1; background: #f8fafc; color: #475569; }
    
    .internship-details { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; }
    .internship-details-content { flex: 1; }
    
    .internship-highlights { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; }
    .highlight-item { display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.85rem; }
    .highlight-item i { color: var(--primary); font-size: 1rem; }
    
    /* Responsive improvements */
    @media (min-width: 768px) {
      .navbar { padding: 1rem 0; }
      .navbar-brand { font-size: 1.5rem; }
      .apply-header { padding: 3.5rem 0; }
      .apply-card { margin-top: -3rem; }
      .internship-icon { width: 70px; height: 70px; min-width: 70px; font-size: 1.75rem; }
      .internship-title { font-size: 1.5rem; }
      .form-section { padding: 2rem; }
    }
    
    @media (max-width: 576px) {
      .container { padding-left: 1rem; padding-right: 1rem; }
      .apply-card { padding: 1.25rem !important; }
      .internship-details { flex-direction: column; align-items: flex-start; }
      .internship-icon { margin-bottom: 0.5rem; }
      .btn-group-apply { flex-direction: column; width: 100%; }
      .btn-group-apply .btn { width: 100%; }
      .about-box { padding: 1rem; }
      .form-section { padding: 1rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center">
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
      <h1 class="fw-bold mb-0 fs-3">Apply for Internship</h1>
      <p class="mt-2 mb-0 opacity-75">Complete the form below to submit your application</p>
    </div>
  </section>

  <!-- Application Form -->
  <div class="container pb-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="apply-card p-4 p-md-5">
          <!-- Internship Details -->
          <div class="internship-details">
            <div class="internship-icon">
              <i class="bi <?= $icon ?>"></i>
            </div>
            <div class="internship-details-content">
              <h3 class="internship-title"><?= htmlspecialchars($internship['title']) ?></h3>
              <div class="company-info">
                <i class="bi bi-building"></i>
                <span>InternHub Company</span>
              </div>
              <div class="tags-container">
                <span class="tag tag-duration"><i class="bi bi-clock"></i><?= htmlspecialchars($internship['duration']) ?></span>
                <span class="tag tag-type"><i class="bi bi-laptop"></i>Remote</span>
                <span class="tag tag-location"><i class="bi bi-geo-alt"></i>Work from Home</span>
              </div>
            </div>
          </div>
          
          <div class="about-box">
            <h6><i class="bi bi-info-circle"></i>About this Internship</h6>
            <p class="mb-0"><?= htmlspecialchars($internship['description']) ?></p>
          </div>
          
          <div class="internship-highlights">
            <div class="highlight-item">
              <i class="bi bi-calendar-check"></i>
              <span>Immediate Start</span>
            </div>
            <div class="highlight-item">
              <i class="bi bi-award"></i>
              <span>Certificate Provided</span>
            </div>
            <div class="highlight-item">
              <i class="bi bi-person-workspace"></i>
              <span>Flexible Hours</span>
            </div>
          </div>
          
          <!-- Application Form -->
          <div class="form-section">
            <div class="form-section-title">
              <i class="bi bi-pencil-square"></i>
              Application Form
            </div>
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
              
              <div class="d-flex gap-3 mt-4 btn-group-apply">
                <button type="submit" class="btn btn-apply">
                  <i class="bi bi-send"></i>Submit Application
                </button>
                <a href="index.php" class="btn btn-cancel">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>