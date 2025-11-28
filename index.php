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
    * { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
    body { background: #f8fafc; }
    
    /* Navbar */
    .navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem 0; }
    .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--primary) !important; }
    .nav-link { color: var(--dark) !important; font-weight: 500; margin: 0 0.5rem; }
    .nav-link:hover { color: var(--primary) !important; }
    .btn-login { background: var(--primary); color: #fff !important; border-radius: 8px; padding: 0.5rem 1.25rem; }
    .btn-login:hover { background: var(--primary-dark); }
    
    /* Hero */
    .hero { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 50%, var(--secondary) 100%); padding: 4rem 0; color: #fff; }
    .hero h1 { font-size: 2.75rem; font-weight: 800; }
    .hero p { font-size: 1.1rem; opacity: 0.9; }
    .search-box { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); margin-top: 2rem; }
    .search-box input { border: 2px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 1rem; }
    .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    
    /* Stats */
    .stats { margin-top: -2rem; position: relative; z-index: 10; }
    .stat-item { background: #fff; border-radius: 12px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .stat-number { font-size: 2rem; font-weight: 700; color: var(--primary); }
    .stat-label { color: #64748b; font-size: 0.9rem; }
    
    /* Internship Cards */
    .section-title { font-size: 1.75rem; font-weight: 700; color: var(--dark); }
    .internship-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; transition: all 0.3s ease; height: 100%; }
    .internship-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); border-color: var(--primary); }
    .company-logo { width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; font-weight: 700; }
    .internship-title { font-size: 1.1rem; font-weight: 600; color: var(--dark); margin: 0; }
    .company-name { color: #64748b; font-size: 0.9rem; }
    .tag { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500; margin-right: 0.5rem; margin-bottom: 0.5rem; }
    .tag-duration { background: #dbeafe; color: #1d4ed8; }
    .tag-type { background: #dcfce7; color: #166534; }
    .tag-mode { background: #fef3c7; color: #92400e; }
    .internship-meta { display: flex; gap: 1rem; color: #64748b; font-size: 0.85rem; margin: 1rem 0; }
    .internship-meta i { color: var(--primary); }
    .btn-apply { background: var(--primary); color: #fff; border: none; border-radius: 8px; padding: 0.6rem 1.5rem; font-weight: 600; width: 100%; transition: all 0.2s; }
    .btn-apply:hover { background: var(--primary-dark); color: #fff; transform: scale(1.02); }
    
    /* Footer */
    .footer { background: var(--dark); color: #fff; padding: 3rem 0; margin-top: 4rem; }
    .footer a { color: #94a3b8; text-decoration: none; }
    .footer a:hover { color: #fff; }
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
      <div class="search-box mx-auto" style="max-width: 600px;">
        <div class="input-group">
          <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" class="form-control border-0" placeholder="Search internships by title, skills, or company...">
          <button class="btn btn-apply" style="border-radius: 0 8px 8px 0;">Search</button>
        </div>
      </div>
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
              <div class="d-flex align-items-start mb-3">
                <div class="company-logo me-3">
                  <i class="bi <?= $icon ?>"></i>
                </div>
                <div>
                  <h5 class="internship-title"><?= htmlspecialchars($row['title']) ?></h5>
                  <p class="company-name mb-0"><i class="bi bi-building me-1"></i>InternHub Company</p>
                </div>
              </div>
              
              <p class="text-muted small mb-3"><?= htmlspecialchars($row['description']) ?></p>
              
              <div class="mb-3">
                <span class="tag tag-duration"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($row['duration']) ?></span>
                <span class="tag tag-type"><i class="bi bi-laptop me-1"></i>Remote</span>
                <span class="tag tag-mode"><i class="bi bi-currency-rupee"></i>Unpaid</span>
              </div>
              
              <div class="internship-meta">
                <span><i class="bi bi-geo-alt me-1"></i>Work from Home</span>
                <span><i class="bi bi-calendar me-1"></i>Immediate</span>
              </div>
              
              <a href="apply.php?id=<?= (int)$row['id'] ?>" class="btn btn-apply">
                <i class="bi bi-send me-1"></i>Apply Now
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
          <p class="text-muted">Your gateway to amazing internship opportunities. Build your career with real-world experience.</p>
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
          <ul class="list-unstyled text-muted">
            <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@internhub.com</li>
            <li class="mb-2"><i class="bi bi-telephone me-2"></i>+1 234 567 890</li>
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