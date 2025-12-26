<?php
require 'includes/db.php';

// Handle search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM internships WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR description LIKE :search2)";
    $params[':search'] = '%' . $search . '%';
    $params[':search2'] = '%' . $search . '%';
}

if (!empty($category) && $category !== 'all') {
    $sql .= " AND title LIKE :category";
    $params[':category'] = '%' . $category . '%';
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Career Opportunities - Internship Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <style>
    :root {
      --bg-start: #6556f3;
      --bg-end: #f36cac;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #6b7280;
      --pill: rgba(255, 255, 255, 0.12);
      --glass: rgba(255, 255, 255, 0.14);
      --shadow: 0 30px 70px rgba(15, 23, 42, 0.28);
    }
    * { font-family: 'Inter', system-ui, -apple-system, sans-serif; box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      background: radial-gradient(circle at 15% 20%, rgba(255,255,255,0.15), transparent 30%),
                  radial-gradient(circle at 85% 0%, rgba(255,255,255,0.12), transparent 28%),
                  linear-gradient(135deg, var(--bg-start), var(--bg-end));
      color: #e5e7eb;
      padding: 32px 16px 64px;
      display: flex;
      justify-content: center;
    }
    .page {
      width: 100%;
      max-width: 1080px;
      display: flex;
      flex-direction: column;
      gap: 28px;
    }
    .top-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 18px;
      border-radius: 18px;
      background: var(--glass);
      border: 1px solid rgba(255,255,255,0.24);
      box-shadow: 0 12px 30px rgba(0,0,0,0.18);
      backdrop-filter: blur(14px);
    }
    .brand {
      font-weight: 700;
      letter-spacing: 0.2px;
    }
    .nav-links {
      display: flex;
      gap: 18px;
      font-size: 0.95rem;
      color: rgba(255,255,255,0.85);
    }
    .nav-links a {
      color: inherit;
      text-decoration: none;
      position: relative;
      padding-bottom: 4px;
    }
    .nav-links a.active::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 100%;
      height: 2px;
      background: #fff;
      border-radius: 999px;
    }
    .avatar {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      background: rgba(255,255,255,0.2);
      display: grid;
      place-items: center;
    }
    .hero {
      text-align: center;
      padding: 14px 8px 4px;
    }
    .eyebrow {
      display: inline-flex;
      padding: 6px 12px;
      border-radius: 999px;
      background: var(--pill);
      font-weight: 600;
      font-size: 0.85rem;
      letter-spacing: 0.3px;
    }
    .hero h1 {
      margin: 14px 0 6px;
      font-size: 2.4rem;
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    .hero p {
      margin: 0;
      color: rgba(255,255,255,0.85);
      font-size: 1.05rem;
    }
    .featured-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #f8fafc;
      gap: 12px;
      flex-wrap: wrap;
    }
    .featured-header .title {
      font-weight: 700;
      font-size: 1.1rem;
      letter-spacing: 0.2px;
    }
    .featured-header .hint {
      color: rgba(255,255,255,0.7);
      font-size: 0.95rem;
      margin: 0;
    }
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
      margin-top: 10px;
    }
    .intern-card {
      background: var(--card);
      color: var(--text);
      border-radius: 16px;
      padding: 18px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(15,23,42,0.06);
      display: flex;
      flex-direction: column;
      gap: 10px;
      transition: transform 0.2s ease;
    }
    .intern-card:hover { transform: translateY(-2px); }
    .pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 10px;
      background: #eef2ff;
      color: #4338ca;
      font-weight: 600;
      font-size: 0.85rem;
      width: fit-content;
    }
    .title-text {
      font-size: 1.05rem;
      font-weight: 700;
      margin: 0;
      word-break: break-word;
    }
    .company {
      margin: 0;
      color: var(--muted);
      font-weight: 600;
      font-size: 0.92rem;
    }
    .meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      font-size: 0.85rem;
    }
    .meta span {
      background: #f3f4f6;
      color: #111827;
      padding: 6px 10px;
      border-radius: 10px;
      font-weight: 600;
    }
    .apply-link {
      margin-top: 6px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #4338ca;
      font-weight: 700;
      text-decoration: none;
    }
    .apply-link:hover { color: #312e81; }
    .empty {
      margin-top: 16px;
      padding: 18px;
      border-radius: 14px;
      background: var(--glass);
      text-align: center;
      border: 1px dashed rgba(255,255,255,0.35);
      color: #f8fafc;
    }
    @media (min-width: 768px) {
      body { padding: 48px 24px 80px; }
      .hero h1 { font-size: 3rem; }
      .hero p { font-size: 1.1rem; }
      .top-bar { padding: 16px 22px; }
    }
    @media (max-width: 600px) {
      .nav-links { gap: 12px; font-size: 0.9rem; }
      .top-bar { padding: 12px 14px; }
    }
  </style>
</head>
<body>
  <div class="page">
    <header class="top-bar">
      <div class="brand">Protégé</div>
      <nav class="nav-links">
        <a class="active" href="index.php">Discover</a>
        <a href="#featured">Internships</a>
        <a href="#featured">Recruiters</a>
      </nav>
      <div class="avatar">
        <i class="bi bi-person-fill"></i>
      </div>
    </header>

    <section class="hero">
      <div class="eyebrow">Protégé</div>
      <h1>Where talent is shipped.</h1>
      <p>For interns. By interns.</p>
    </section>

    <section class="featured" id="featured">
      <div class="featured-header">
        <div class="title">Featured Internships</div>
        <p class="hint mb-0">Handpicked roles to help you start faster.</p>
      </div>
      <?php $hasResults = false; ?>
      <div class="cards-grid">
        <?php while ($row = $result->fetch()): $hasResults = true; ?>
          <?php
            $icons = ['Web' => 'bi-code-slash', 'Data' => 'bi-graph-up', 'Digital' => 'bi-megaphone', 'Design' => 'bi-palette'];
            $icon = 'bi-briefcase';
            foreach ($icons as $key => $val) {
              if (stripos($row['title'], $key) !== false) { $icon = $val; break; }
            }
          ?>
          <article class="intern-card">
            <span class="pill"><i class="bi <?= $icon ?>"></i> Intern</span>
            <div>
              <p class="title-text mb-1"><?= htmlspecialchars($row['title']) ?></p>
              <p class="company mb-2">InternHub Company</p>
            </div>
            <div class="meta">
              <span><?= htmlspecialchars($row['duration']) ?></span>
              <span>Work from Home</span>
              <span>Unpaid</span>
            </div>
            <a class="apply-link" href="apply.php?id=<?= (int)$row['id'] ?>">
              Apply now <i class="bi bi-arrow-up-right"></i>
            </a>
          </article>
        <?php endwhile; ?>
      </div>
      <?php if (!$hasResults): ?>
        <div class="empty">
          <i class="bi bi-emoji-neutral me-2"></i>No internships found right now.
        </div>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
