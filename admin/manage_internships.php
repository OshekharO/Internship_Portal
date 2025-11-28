<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

$message = '';
$messageType = '';
$editInternship = null;

// Handle logout
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: login.php");
  exit();
}

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $deleteId = (int)$_GET['delete'];
  
  // Check if there are any applications for this internship
  $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM applications WHERE internship_id = ?");
  $checkStmt->execute([$deleteId]);
  $appCount = $checkStmt->fetch()['count'];
  
  if ($appCount > 0) {
    $message = "Cannot delete this internship. There are $appCount application(s) associated with it.";
    $messageType = 'danger';
  } else {
    $stmt = $conn->prepare("DELETE FROM internships WHERE id = ?");
    $stmt->execute([$deleteId]);
    $message = "Internship deleted successfully!";
    $messageType = 'success';
  }
}

// Handle Edit - Load data
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
  $editId = (int)$_GET['edit'];
  $stmt = $conn->prepare("SELECT * FROM internships WHERE id = ?");
  $stmt->execute([$editId]);
  $editInternship = $stmt->fetch();
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $duration = trim($_POST['duration'] ?? '');
  $internshipId = isset($_POST['internship_id']) ? (int)$_POST['internship_id'] : 0;
  
  if (empty($title) || empty($description) || empty($duration)) {
    $message = "All fields are required.";
    $messageType = 'danger';
  } else {
    if ($internshipId > 0) {
      // Update existing
      $stmt = $conn->prepare("UPDATE internships SET title = ?, description = ?, duration = ? WHERE id = ?");
      $stmt->execute([$title, $description, $duration, $internshipId]);
      $message = "Internship updated successfully!";
      $messageType = 'success';
    } else {
      // Add new
      $stmt = $conn->prepare("INSERT INTO internships (title, description, duration) VALUES (?, ?, ?)");
      $stmt->execute([$title, $description, $duration]);
      $message = "Internship added successfully!";
      $messageType = 'success';
    }
    // Clear edit state after successful save
    $editInternship = null;
  }
}

// Fetch all internships
$internships = $conn->query("SELECT * FROM internships ORDER BY id DESC");
$internshipCount = $conn->query("SELECT COUNT(*) as count FROM internships")->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Internships - InternHub Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root { --primary: #6366f1; --primary-dark: #4f46e5; --sidebar: #1e1b4b; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; }
    * { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; box-sizing: border-box; }
    body { background: #f1f5f9; overflow-x: hidden; }
    
    /* Sidebar */
    .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: linear-gradient(180deg, var(--sidebar) 0%, #312e81 100%); color: #fff; padding: 1.5rem; z-index: 1000; transition: all 0.3s ease; }
    .sidebar-brand { font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; display: flex; align-items: center; }
    .sidebar-brand i { margin-right: 0.75rem; font-size: 1.75rem; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav li { margin-bottom: 0.5rem; }
    .sidebar-nav a { display: flex; align-items: center; padding: 0.75rem 1rem; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 10px; transition: all 0.2s; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: #fff; }
    .sidebar-nav a i { margin-right: 0.75rem; font-size: 1.1rem; min-width: 1.5rem; }
    .sidebar-footer { position: absolute; bottom: 1.5rem; left: 1.5rem; right: 1.5rem; }
    .sidebar-footer .btn { width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; }
    .sidebar-footer .btn:hover { background: rgba(255,255,255,0.2); color: #fff; }
    
    /* Mobile menu toggle */
    .mobile-toggle { display: none; position: fixed; top: 1rem; left: 1rem; z-index: 1001; background: var(--primary); color: #fff; border: none; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 1.25rem; cursor: pointer; }
    .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }
    
    /* Main Content */
    .main-content { margin-left: 260px; padding: 2rem; min-height: 100vh; transition: all 0.3s ease; }
    
    /* Header */
    .page-header { background: #fff; padding: 1.25rem 1.5rem; border-radius: 16px; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; }
    .page-header h1 { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; }
    .page-header p { margin-bottom: 0; }
    
    /* Cards */
    .form-card, .table-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .card-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.5rem; }
    .card-header h5 { margin: 0; font-weight: 600; color: #1e293b; font-size: 1rem; }
    .card-body { padding: 1.25rem; }
    
    /* Table */
    .table { margin: 0; }
    .table thead th { background: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.875rem 1rem; border: none; white-space: nowrap; }
    .table tbody td { padding: 0.875rem 1rem; vertical-align: middle; border-color: #f1f5f9; font-size: 0.9rem; }
    .table tbody tr:hover { background: #f8fafc; }
    
    /* Form Controls */
    .form-label { font-weight: 500; color: #475569; font-size: 0.9rem; }
    .form-control { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.65rem 0.875rem; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    
    /* Buttons */
    .btn-action { padding: 0.35rem 0.65rem; font-size: 0.85rem; border-radius: 8px; }
    
    /* Description cell */
    .desc-cell { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    /* Empty State */
    .empty-state { padding: 3rem 1.5rem; text-align: center; color: #64748b; }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
    .empty-state h5 { color: #1e293b; margin-bottom: 0.5rem; font-size: 1.1rem; }
    
    /* Tablet responsive */
    @media (max-width: 992px) {
      .sidebar { width: 70px; padding: 1rem 0.75rem; }
      .sidebar-brand span, .sidebar-nav a span, .sidebar-footer span { display: none; }
      .sidebar-brand { justify-content: center; margin-bottom: 1.5rem; }
      .sidebar-brand i { margin-right: 0; font-size: 1.5rem; }
      .sidebar-nav a { justify-content: center; padding: 0.75rem; }
      .sidebar-nav a i { margin-right: 0; font-size: 1.25rem; }
      .sidebar-footer { left: 0.75rem; right: 0.75rem; }
      .sidebar-footer .btn { padding: 0.5rem; }
      .sidebar-footer .btn i { margin-right: 0 !important; }
      .main-content { margin-left: 70px; padding: 1.5rem; }
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
      .mobile-toggle { display: block; }
      .sidebar { transform: translateX(-100%); width: 260px; padding: 1.5rem; }
      .sidebar.open { transform: translateX(0); }
      .sidebar-overlay.open { display: block; }
      .sidebar-brand span, .sidebar-nav a span, .sidebar-footer span { display: inline; }
      .sidebar-brand { justify-content: flex-start; }
      .sidebar-brand i { margin-right: 0.75rem; }
      .sidebar-nav a { justify-content: flex-start; }
      .sidebar-nav a i { margin-right: 0.75rem; }
      .main-content { margin-left: 0; padding: 4rem 1rem 1rem; }
      .page-header { flex-direction: column; align-items: flex-start; padding: 1rem; }
      .page-header > div:last-child { width: 100%; text-align: left; }
      .row { flex-direction: column; }
      .col-lg-4, .col-lg-8 { width: 100%; }
      .table-responsive { margin: 0 -1rem; }
      .table thead th, .table tbody td { padding: 0.75rem 0.5rem; }
      .desc-cell { max-width: 120px; }
    }
  </style>
</head>
<body>
  <!-- Mobile Toggle Button -->
  <button class="mobile-toggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
  </button>
  
  <!-- Sidebar Overlay -->
  <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
  
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <i class="bi bi-mortarboard-fill"></i>
      <span>InternHub</span>
    </div>
    <ul class="sidebar-nav">
      <li><a href="dashboard.php"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a></li>
      <li><a href="manage_internships.php" class="active"><i class="bi bi-briefcase"></i><span>Manage Internships</span></a></li>
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
        <h1><i class="bi bi-briefcase me-2"></i>Manage Internships</h1>
        <p class="text-muted mb-0">Add, edit, or remove internship listings</p>
      </div>
      <div>
        <span class="badge bg-primary fs-6"><?= $internshipCount ?> Internship<?= $internshipCount != 1 ? 's' : '' ?></span>
      </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
      <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
      <!-- Add/Edit Form -->
      <div class="col-lg-4">
        <div class="form-card">
          <div class="card-header">
            <h5><i class="bi bi-<?= $editInternship ? 'pencil' : 'plus-circle' ?> me-2"></i><?= $editInternship ? 'Edit Internship' : 'Add New Internship' ?></h5>
          </div>
          <div class="card-body">
            <form method="post">
              <?php if ($editInternship): ?>
              <input type="hidden" name="internship_id" value="<?= (int)$editInternship['id'] ?>">
              <?php endif; ?>
              
              <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" placeholder="e.g., Web Development Internship" 
                       value="<?= $editInternship ? htmlspecialchars($editInternship['title']) : '' ?>" required>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="4" placeholder="Describe the internship program..." required><?= $editInternship ? htmlspecialchars($editInternship['description']) : '' ?></textarea>
              </div>
              
              <div class="mb-4">
                <label class="form-label">Duration <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="duration" placeholder="e.g., 3 Months" 
                       value="<?= $editInternship ? htmlspecialchars($editInternship['duration']) : '' ?>" required>
              </div>
              
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-<?= $editInternship ? 'check-lg' : 'plus-lg' ?> me-2"></i>
                  <?= $editInternship ? 'Update Internship' : 'Add Internship' ?>
                </button>
                <?php if ($editInternship): ?>
                <a href="manage_internships.php" class="btn btn-outline-secondary">
                  <i class="bi bi-x-lg me-2"></i>Cancel Edit
                </a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Internships List -->
      <div class="col-lg-8">
        <div class="table-card">
          <div class="card-header">
            <h5><i class="bi bi-list-ul me-2"></i>All Internships</h5>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Duration</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $hasInternships = false; ?>
                <?php while($intern = $internships->fetch()): $hasInternships = true; ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= htmlspecialchars($intern['title']) ?></div>
                  </td>
                  <td>
                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($intern['duration']) ?></span>
                  </td>
                  <td>
                    <small class="text-muted desc-cell d-block"><?= htmlspecialchars(substr($intern['description'], 0, 50)) ?>...</small>
                  </td>
                  <td>
                    <div class="btn-group">
                      <a href="?edit=<?= (int)$intern['id'] ?>" class="btn btn-outline-primary btn-action" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="?delete=<?= (int)$intern['id'] ?>" class="btn btn-outline-danger btn-action" 
                         onclick="return confirm('Are you sure you want to delete this internship?')" title="Delete">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <?php if (!$hasInternships): ?>
            <div class="empty-state">
              <i class="bi bi-briefcase"></i>
              <h5>No Internships Yet</h5>
              <p class="mb-0">Add your first internship using the form on the left.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.querySelector('.sidebar-overlay').classList.toggle('open');
    }
  </script>
</body>
</html>
