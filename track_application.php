<?php
require 'includes/db.php';

$application = null;
$searched = false;
$error = '';

if (isset($_GET['email']) && !empty(trim($_GET['email']))) {
    $searched = true;
    $email = trim($_GET['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Fetch all applications for this email
        $stmt = $conn->prepare("SELECT a.id, a.name, a.email, a.status, i.title, i.duration,
                                       c.certificate_code, c.issue_date
                                FROM applications a
                                JOIN internships i ON a.internship_id = i.id
                                LEFT JOIN certificates c ON c.application_id = a.id
                                WHERE a.email = :email
                                ORDER BY a.id DESC");
        $stmt->execute([':email' => $email]);
        $applications = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Application - InternHub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; --dark: #1e293b; }
    * { font-family: 'Segoe UI', system-ui, sans-serif; box-sizing: border-box; }
    body { background: #f8fafc; min-height: 100vh; overflow-x: hidden; }
    
    /* Navbar */
    .navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0.75rem 0; }
    .navbar-brand { font-weight: 700; font-size: 1.25rem; color: var(--primary) !important; }
    
    /* Search Section */
    .search-section { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%); padding: 2.5rem 0; color: #fff; }
    .search-section h1 { font-size: 1.5rem; }
    .search-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 500px; margin: 0 auto; }
    .search-card input { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.95rem; }
    .search-card input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    
    /* Results Section */
    .results-section { padding: 2rem 0; }
    
    /* Application Card */
    .application-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; margin-bottom: 1rem; transition: all 0.3s ease; }
    .application-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    
    /* Status Badges */
    .badge-status { padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-status.pending { background: #fef3c7; color: #92400e; }
    .badge-status.selected { background: #d1fae5; color: #065f46; }
    .badge-status.rejected { background: #fee2e2; color: #991b1b; }
    
    /* Status Icon */
    .status-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .status-icon.pending { background: #fef3c7; color: #92400e; }
    .status-icon.selected { background: #d1fae5; color: #065f46; }
    .status-icon.rejected { background: #fee2e2; color: #991b1b; }
    
    /* Empty State */
    .empty-state { text-align: center; padding: 3rem 1.5rem; background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 500px; margin: 0 auto; }
    .empty-state i { font-size: 3rem; color: #94a3b8; margin-bottom: 1rem; }
    
    /* Timeline */
    .timeline { position: relative; padding-left: 2rem; }
    .timeline::before { content: ''; position: absolute; left: 0.5rem; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
    .timeline-item { position: relative; padding-bottom: 1.5rem; }
    .timeline-item::before { content: ''; position: absolute; left: -1.5rem; top: 0.25rem; width: 12px; height: 12px; border-radius: 50%; border: 2px solid var(--primary); background: #fff; }
    .timeline-item.completed::before { background: var(--primary); }
    
    /* Responsive */
    @media (min-width: 768px) {
      .navbar { padding: 1rem 0; }
      .navbar-brand { font-size: 1.5rem; }
      .search-section { padding: 4rem 0; }
      .search-section h1 { font-size: 2rem; }
      .search-card { padding: 2rem; }
    }
    
    @media (max-width: 576px) {
      .container { padding-left: 1rem; padding-right: 1rem; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill me-2"></i>InternHub</a>
      <a href="index.php" class="btn btn-outline-primary btn-sm">Back to Internships</a>
    </div>
  </nav>

  <!-- Search Section -->
  <section class="search-section">
    <div class="container">
      <div class="text-center mb-4">
        <h1 class="fw-bold mb-2">Track Your Application</h1>
        <p class="opacity-75">Enter your email to check the status of your applications</p>
      </div>
      <div class="search-card">
        <form method="get">
          <div class="mb-3">
            <label class="form-label text-muted small">Email Address</label>
            <input type="email" class="form-control" name="email" 
                   placeholder="Enter the email you used to apply" 
                   value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>" 
                   required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-search me-2"></i>Track Application
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- Results Section -->
  <?php if ($searched): ?>
  <section class="results-section">
    <div class="container">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger max-w-500 mx-auto" style="max-width: 500px;">
          <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        </div>
      <?php elseif (empty($applications)): ?>
        <div class="empty-state">
          <i class="bi bi-inbox d-block"></i>
          <h4 class="fw-bold text-dark">No Applications Found</h4>
          <p class="text-muted mb-4">We couldn't find any applications with this email address.</p>
          <a href="index.php" class="btn btn-primary">Browse Internships</a>
        </div>
      <?php else: ?>
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <h4 class="fw-bold mb-4 text-center">
              <i class="bi bi-file-earmark-text me-2 text-primary"></i>
              Your Applications (<?= count($applications) ?>)
            </h4>
            
            <?php foreach ($applications as $app): ?>
            <div class="application-card">
              <div class="d-flex align-items-start gap-3">
                <div class="status-icon <?= $app['status'] ?>">
                  <?php if ($app['status'] === 'selected'): ?>
                    <i class="bi bi-check-lg"></i>
                  <?php elseif ($app['status'] === 'rejected'): ?>
                    <i class="bi bi-x-lg"></i>
                  <?php else: ?>
                    <i class="bi bi-hourglass-split"></i>
                  <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                      <h5 class="fw-bold mb-1"><?= htmlspecialchars($app['title']) ?></h5>
                      <p class="text-muted mb-2">
                        <i class="bi bi-clock me-1"></i><?= htmlspecialchars($app['duration']) ?>
                        <span class="mx-2">•</span>
                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($app['name']) ?>
                      </p>
                    </div>
                    <span class="badge-status <?= $app['status'] ?>">
                      <?= ucfirst($app['status']) ?>
                    </span>
                  </div>
                  
                  <!-- Status Timeline -->
                  <div class="mt-3">
                    <div class="timeline">
                      <div class="timeline-item completed">
                        <small class="text-muted">Application Submitted</small>
                      </div>
                      <div class="timeline-item <?= $app['status'] !== 'pending' ? 'completed' : '' ?>">
                        <small class="text-muted">Under Review</small>
                      </div>
                      <div class="timeline-item <?= $app['status'] === 'selected' ? 'completed' : '' ?>">
                        <small class="text-muted">
                          <?php if ($app['status'] === 'selected'): ?>
                            Selected! 🎉
                          <?php elseif ($app['status'] === 'rejected'): ?>
                            Not Selected
                          <?php else: ?>
                            Final Decision
                          <?php endif; ?>
                        </small>
                      </div>
                    </div>
                  </div>
                  
                  <?php if ($app['certificate_code']): ?>
                  <div class="mt-3 p-3 bg-success bg-opacity-10 rounded-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                      <div>
                        <i class="bi bi-award text-success me-2"></i>
                        <strong class="text-success">Certificate Issued!</strong>
                        <div class="text-muted small mt-1">
                          Code: <code><?= htmlspecialchars($app['certificate_code']) ?></code>
                        </div>
                      </div>
                      <a href="verify_certificate.php?code=<?= urlencode($app['certificate_code']) ?>" 
                         class="btn btn-success btn-sm">
                        <i class="bi bi-eye me-1"></i>View Certificate
                      </a>
                    </div>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
