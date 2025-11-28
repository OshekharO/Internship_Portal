<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

// Handle logout
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit();
}

$apps = $conn->query("SELECT a.*, i.title FROM applications a JOIN internships i ON a.internship_id=i.id ORDER BY a.id DESC");
$appCount = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch()['count'];
$selectedCount = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status='selected'")->fetch()['count'];
$pendingCount = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status='pending'")->fetch()['count'];
$certCount = $conn->query("SELECT COUNT(*) as count FROM certificates")->fetch()['count'];
$internshipCount = $conn->query("SELECT COUNT(*) as count FROM internships")->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - InternHub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --sidebar: #1e1b4b; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; }
    * { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
    body { background: #f1f5f9; }
    
    /* Sidebar */
    .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: linear-gradient(180deg, var(--sidebar) 0%, #312e81 100%); color: #fff; padding: 1.5rem; z-index: 1000; }
    .sidebar-brand { font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; display: flex; align-items: center; }
    .sidebar-brand i { margin-right: 0.75rem; font-size: 1.75rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav li { margin-bottom: 0.5rem; }
    .sidebar-nav a { display: flex; align-items: center; padding: 0.75rem 1rem; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 10px; transition: all 0.2s; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: #fff; }
    .sidebar-nav a i { margin-right: 0.75rem; font-size: 1.1rem; }
    .sidebar-footer { position: absolute; bottom: 1.5rem; left: 1.5rem; right: 1.5rem; }
    .sidebar-footer .btn { width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; }
    .sidebar-footer .btn:hover { background: rgba(255,255,255,0.2); color: #fff; }
    
    /* Main Content */
    .main-content { margin-left: 260px; padding: 2rem; }
    
    /* Header */
    .page-header { background: #fff; padding: 1.5rem 2rem; border-radius: 16px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: between; align-items: center; }
    .page-header h1 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; }
    
    /* Stat Cards */
    .stat-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); height: 100%; }
    .stat-card .stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .stat-card .stat-icon.primary { background: rgba(99,102,241,0.1); color: var(--primary); }
    .stat-card .stat-icon.success { background: rgba(16,185,129,0.1); color: var(--success); }
    .stat-card .stat-icon.warning { background: rgba(245,158,11,0.1); color: var(--warning); }
    .stat-card .stat-icon.danger { background: rgba(239,68,68,0.1); color: var(--danger); }
    .stat-card .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; }
    .stat-card .stat-label { color: #64748b; font-size: 0.9rem; }
    .stat-card .stat-change { font-size: 0.8rem; display: flex; align-items: center; }
    .stat-card .stat-change.up { color: var(--success); }
    .stat-card .stat-change.down { color: var(--danger); }
    
    /* Table Card */
    .table-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
    .table-card .card-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
    .table-card .card-header h5 { margin: 0; font-weight: 600; color: #1e293b; }
    .table { margin: 0; }
    .table thead th { background: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 1rem 1.5rem; border: none; }
    .table tbody td { padding: 1rem 1.5rem; vertical-align: middle; border-color: #f1f5f9; }
    .table tbody tr:hover { background: #f8fafc; }
    
    /* Badges */
    .badge-status { padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 500; font-size: 0.8rem; }
    .badge-status.pending { background: #fef3c7; color: #92400e; }
    .badge-status.selected { background: #d1fae5; color: #065f46; }
    .badge-status.rejected { background: #fee2e2; color: #991b1b; }
    
    /* Buttons */
    .btn-action { padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 8px; }
    
    /* Empty State */
    .empty-state { padding: 4rem 2rem; text-align: center; color: #64748b; }
    .empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
    .empty-state h5 { color: #1e293b; margin-bottom: 0.5rem; }
    
    @media (max-width: 992px) {
      .sidebar { width: 80px; padding: 1rem; }
      .sidebar-brand span, .sidebar-nav a span { display: none; }
      .sidebar-nav a { justify-content: center; padding: 0.75rem; }
      .sidebar-nav a i { margin: 0; font-size: 1.25rem; }
      .main-content { margin-left: 80px; }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <i class="bi bi-mortarboard-fill"></i>
      <span>InternHub</span>
    </div>
    <ul class="sidebar-nav">
      <li><a href="dashboard.php" class="active"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a></li>
      <li><a href="manage_internships.php"><i class="bi bi-briefcase"></i><span>Manage Internships</span></a></li>
      <li><a href="../index.php" target="_blank"><i class="bi bi-eye"></i><span>View Portal</span></a></li>
    </ul>
    <div class="sidebar-footer">
      <a href="?logout=1" class="btn"><i class="bi bi-box-arrow-left me-2"></i><span>Logout</span></a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1>Dashboard Overview</h1>
        <p class="text-muted mb-0">Welcome back, Admin! Here's what's happening.</p>
      </div>
      <div>
        <span class="text-muted"><i class="bi bi-calendar3 me-2"></i><?= date('F j, Y') ?></span>
      </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
      <div class="col-xl col-md-6">
        <div class="stat-card">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="stat-label mb-1">Active Internships</p>
              <h3 class="stat-value mb-0"><?= $internshipCount ?></h3>
            </div>
            <div class="stat-icon primary"><i class="bi bi-briefcase"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xl col-md-6">
        <div class="stat-card">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="stat-label mb-1">Total Applications</p>
              <h3 class="stat-value mb-0"><?= $appCount ?></h3>
            </div>
            <div class="stat-icon success"><i class="bi bi-file-earmark-text"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xl col-md-6">
        <div class="stat-card">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="stat-label mb-1">Pending Review</p>
              <h3 class="stat-value mb-0"><?= $pendingCount ?></h3>
            </div>
            <div class="stat-icon warning"><i class="bi bi-hourglass-split"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xl col-md-6">
        <div class="stat-card">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="stat-label mb-1">Selected</p>
              <h3 class="stat-value mb-0"><?= $selectedCount ?></h3>
            </div>
            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
          </div>
        </div>
      </div>
      <div class="col-xl col-md-6">
        <div class="stat-card">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="stat-label mb-1">Certificates Issued</p>
              <h3 class="stat-value mb-0"><?= $certCount ?></h3>
            </div>
            <div class="stat-icon danger"><i class="bi bi-award"></i></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Applications Table -->
    <div class="table-card">
      <div class="card-header">
        <h5><i class="bi bi-people me-2"></i>Recent Applications</h5>
        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Export</button>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Applicant</th>
              <th>Email</th>
              <th>Internship</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $hasApps = false; ?>
            <?php while($a=$apps->fetch()): $hasApps = true; ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;font-weight:600;">
                    <?= strtoupper(substr($a['name'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= htmlspecialchars($a['name']) ?></div>
                  </div>
                </div>
              </td>
              <td class="text-muted"><?= htmlspecialchars($a['email']) ?></td>
              <td><?= htmlspecialchars($a['title']) ?></td>
              <td>
                <span class="badge-status <?= $a['status'] ?>">
                  <?= ucfirst(htmlspecialchars($a['status'])) ?>
                </span>
              </td>
              <td>
                <a href="issue_certificate.php?id=<?= (int)$a['id'] ?>" class="btn btn-success btn-action">
                  <i class="bi bi-award me-1"></i>Issue Certificate
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php if (!$hasApps): ?>
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <h5>No Applications Yet</h5>
          <p class="mb-0">Share your portal to start receiving applications!</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>