<?php
require 'includes/db.php';
$result = $conn->query("SELECT * FROM internships");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Career Opportunities - Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --secondary: #0ea5e9; --success: #10b981; --warning: #f59e0b; --dark: #1e293b; }
    * { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; box-sizing: border-box; }
    body { background: #f8fafc; overflow-x: hidden; }
    
    /* Navbar */
    .navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0.75rem 0; }
    .navbar-brand { font-weight: 700; font-size: 1.25rem; color: var(--primary) !important; }
    .nav-link { color: var(--dark) !important; font-weight: 500; margin: 0 0.25rem; padding: 0.5rem 0.75rem !important; }
    .nav-link:hover { color: var(--primary) !important; }
    .btn-login { background: var(--primary); color: #fff !important; border-radius: 8px; padding: 0.5rem 1rem; font-size: 0.9rem; white-space: nowrap; }
    .btn-login:hover { background: var(--primary-dark); }
    .navbar-toggler { border: none; padding: 0.5rem; }
    .navbar-toggler:focus { box-shadow: none; }
    
    /* Hero */
    .hero { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 50%, var(--secondary) 100%); padding: 2.5rem 0 3rem; color: #fff; }
    .hero h1 { font-size: 1.75rem; font-weight: 800; line-height: 1.3; }
    .hero p { font-size: 1rem; opacity: 0.9; }

    
    /* Stats */
    .stats { margin-top: -1.5rem; position: relative; z-index: 10; }
    .stat-item { background: #fff; border-radius: 12px; padding: 1rem; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .stat-number { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
    .stat-label { color: #64748b; font-size: 0.8rem; }
    
    /* Internship Cards */
    .section-title { font-size: 1.5rem; font-weight: 700; color: var(--dark); }
    .internship-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; gap: 0.75rem; }
    .internship-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); border-color: var(--primary); }
    .company-logo { width: 48px; height: 48px; min-width: 48px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.25rem; font-weight: 700; flex-shrink: 0; }
    .internship-title { font-size: 1rem; font-weight: 600; color: var(--dark); margin: 0; line-height: 1.4; word-wrap: break-word; overflow-wrap: break-word; }
    .company-name { color: #64748b; font-size: 0.85rem; margin-top: 0.25rem; }
    .company-name i { vertical-align: middle; }
    .internship-desc { font-size: 0.85rem; line-height: 1.5; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis; max-height: 4.5em; margin-bottom: 0.5rem; }
    .tag { display: inline-flex; align-items: center; padding: 0.35rem 0.7rem; border-radius: 6px; font-size: 0.75rem; font-weight: 500; margin-right: 0.5rem; margin-bottom: 0.5rem; white-space: nowrap; gap: 0.25rem; }
    .tag i { font-size: 0.7rem; }
    .tag-duration { background: #dbeafe; color: #1d4ed8; }
    .tag-type { background: #dcfce7; color: #166534; }
    .tag-mode { background: #fef3c7; color: #92400e; }
    .internship-meta { display: flex; flex-wrap: wrap; gap: 1rem; color: #64748b; font-size: 0.8rem; margin: 0.5rem 0; }
    .internship-meta i { color: var(--primary); margin-right: 0.25rem; vertical-align: middle; }
    .internship-meta span { white-space: nowrap; display: inline-flex; align-items: center; }
    .btn-apply { background: var(--primary); color: #fff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 600; width: 100%; transition: all 0.2s; margin-top: auto; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .btn-apply:hover { background: var(--primary-dark); color: #fff; transform: scale(1.02); }
    
    /* Footer */
    .footer { background: var(--dark); color: #fff; padding: 2rem 0 1.5rem; margin-top: 3rem; }
    .footer a { color: #94a3b8; text-decoration: none; }
    .footer a:hover { color: #fff; }
    .footer h5, .footer h6 { font-size: 1rem; }
    .footer p, .footer li { font-size: 0.9rem; }
    
    /* Responsive improvements */
    @media (min-width: 768px) {
      .navbar { padding: 1rem 0; }
      .navbar-brand { font-size: 1.5rem; }
      .hero { padding: 4rem 0; }
      .hero h1 { font-size: 2.5rem; }
      .hero p { font-size: 1.1rem; }
      .stats { margin-top: -2rem; }
      .stat-number { font-size: 2rem; }
      .stat-label { font-size: 0.9rem; }
      .stat-item { padding: 1.5rem; }
      .section-title { font-size: 1.75rem; }
      .internship-card { padding: 1.5rem; }
      .company-logo { width: 56px; height: 56px; min-width: 56px; font-size: 1.5rem; }
      .internship-title { font-size: 1.1rem; }
      .footer { padding: 3rem 0; margin-top: 4rem; }
    }
    
    @media (max-width: 576px) {
      .container { padding-left: 1rem; padding-right: 1rem; }
      .hero h1 br { display: none; }
      .d-flex.justify-content-between.align-items-center.mb-4 { flex-direction: column; align-items: flex-start !important; gap: 1rem; }
      .footer .row > div { text-align: center; }
      .footer .col-md-4.mb-4 { margin-bottom: 1.5rem !important; }
    }
    
    @media (max-width: 767px) {
      .navbar-collapse { background: #fff; padding: 1rem; margin-top: 0.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
      .navbar-nav { text-align: center; }
      .btn-login { display: block; margin: 0.5rem auto 0; width: fit-content; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill me-2"></i>InternHub</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link active" href="index.php">Internships</a></li>
          <li class="nav-item"><a class="nav-link" href="verify_certificate.php">Verify Certificate</a></li>
        </ul>
        <a href="admin/login.php" class="btn btn-login"><i class="bi bi-person-circle me-1"></i> Admin Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container text-center">
      <h1>Launch Your Career with<br>Amazing Internships</h1>
      <p class="mt-3">Discover opportunities from top companies and gain real-world experience</p>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats">
    <div class="container">
      <div class="row g-4 justify-content-center">
        <div class="col-md-3 col-6">
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">Active Internships</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-item">
            <div class="stat-number">1000+</div>
            <div class="stat-label">Students Placed</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-item">
            <div class="stat-number">50+</div>
            <div class="stat-label">Partner Companies</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-item">
            <div class="stat-number">95%</div>
            <div class="stat-label">Success Rate</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Internships Section -->
  <section class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Available Internships</h2>
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">All Internships</a></li>
            <li><a class="dropdown-item" href="#">Web Development</a></li>
            <li><a class="dropdown-item" href="#">Data Science</a></li>
            <li><a class="dropdown-item" href="#">Marketing</a></li>
          </ul>
        </div>
      </div>
      
      <div class="row g-4">
        <?php while ($row = $result->fetch()): ?>
          <?php
            // Generate dynamic styling based on title
            $icons = ['Web' => 'bi-code-slash', 'Data' => 'bi-graph-up', 'Digital' => 'bi-megaphone', 'Design' => 'bi-palette'];
            $icon = 'bi-briefcase';
            foreach ($icons as $key => $val) {
              if (stripos($row['title'], $key) !== false) { $icon = $val; break; }
            }
          ?>
          <div class="col-lg-4 col-md-6">
            <div class="internship-card">
              <div class="d-flex align-items-start">
                <div class="company-logo me-3">
                  <i class="bi <?= $icon ?>"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="internship-title"><?= htmlspecialchars($row['title']) ?></h5>
                  <p class="company-name mb-0"><i class="bi bi-building"></i> InternHub Company</p>
                </div>
              </div>
              
              <p class="text-muted internship-desc"><?= htmlspecialchars($row['description']) ?></p>
              
              <div class="tags-container">
                <span class="tag tag-duration"><i class="bi bi-clock"></i><?= htmlspecialchars($row['duration']) ?></span>
                <span class="tag tag-type"><i class="bi bi-laptop"></i>Remote</span>
                <span class="tag tag-mode"><i class="bi bi-currency-rupee"></i>Unpaid</span>
              </div>
              
              <div class="internship-meta">
                <span><i class="bi bi-geo-alt"></i>Work from Home</span>
                <span><i class="bi bi-calendar"></i>Immediate</span>
              </div>
              
              <a href="apply.php?id=<?= (int)$row['id'] ?>" class="btn btn-apply">
                <i class="bi bi-send"></i>Apply Now
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold mb-3"><i class="bi bi-mortarboard-fill me-2"></i>InternHub</h5>
          <p class="text-white opacity-75">Your gateway to amazing internship opportunities. Build your career with real-world experience.</p>
        </div>
        <div class="col-md-2 mb-4">
          <h6 class="fw-bold mb-3">Quick Links</h6>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="index.php">Internships</a></li>
            <li class="mb-2"><a href="verify_certificate.php">Verify Certificate</a></li>
            <li class="mb-2"><a href="admin/login.php">Admin Login</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h6 class="fw-bold mb-3">Contact</h6>
          <ul class="list-unstyled">
            <li class="mb-2 text-white opacity-75"><i class="bi bi-envelope me-2"></i>support@internhub.com</li>
            <li class="mb-2 text-white opacity-75"><i class="bi bi-telephone me-2"></i>+1 234 567 890</li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h6 class="fw-bold mb-3">Follow Us</h6>
          <a href="#" class="me-3"><i class="bi bi-linkedin fs-5"></i></a>
          <a href="#" class="me-3"><i class="bi bi-twitter fs-5"></i></a>
          <a href="#" class="me-3"><i class="bi bi-instagram fs-5"></i></a>
          <a href="#"><i class="bi bi-github fs-5"></i></a>
        </div>
      </div>
      <hr class="my-4 opacity-25">
      <p class="text-center text-muted mb-0">&copy; 2024 InternHub. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>