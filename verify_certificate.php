<?php
require 'includes/db.php';
$cert = null;
$searched = false;
if (isset($_GET['code']) && !empty(trim($_GET['code']))) {
    $searched = true;
    $stmt = $conn->prepare("SELECT a.name, i.title, i.duration, c.issue_date, c.certificate_code
                            FROM certificates c
                            JOIN applications a ON c.application_id = a.id
                            JOIN internships i ON a.internship_id = i.id
                            WHERE c.certificate_code = :code");
    $stmt->execute([':code' => trim($_GET['code'])]);
    $cert = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Certificate - InternHub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root { --primary: #6366f1; --gold: #d4af37; --dark: #1e293b; }
    * { font-family: 'Inter', system-ui, sans-serif; }
    body { background: #f8fafc; min-height: 100vh; }
    
    /* Navbar */
    .navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1rem 0; }
    .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--primary) !important; }
    
    /* Search Section */
    .search-section { background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%); padding: 4rem 0; color: #fff; }
    .search-card { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 500px; margin: 0 auto; }
    .search-card input { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.875rem 1rem; font-size: 1rem; }
    .search-card input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    
    /* Certificate */
    .certificate-wrapper { padding: 2rem; }
    .certificate {
      background: #fffef8;
      max-width: 800px;
      margin: 0 auto;
      padding: 0;
      position: relative;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .certificate-border {
      border: 2px solid var(--gold);
      margin: 12px;
      padding: 40px 50px;
      position: relative;
    }
    .certificate-border::before {
      content: '';
      position: absolute;
      top: 8px; left: 8px; right: 8px; bottom: 8px;
      border: 1px solid var(--gold);
      opacity: 0.5;
    }
    .certificate-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .certificate-logo {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }
    .certificate-subtitle {
      color: #64748b;
      font-size: 0.9rem;
      letter-spacing: 2px;
      text-transform: uppercase;
    }
    .certificate-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      font-weight: 600;
      color: var(--dark);
      text-align: center;
      margin: 30px 0 20px;
      letter-spacing: 3px;
    }
    .certificate-ornament {
      text-align: center;
      color: var(--gold);
      font-size: 1.5rem;
      margin: 15px 0;
    }
    .certificate-body {
      text-align: center;
      line-height: 2;
    }
    .certificate-body .label {
      color: #64748b;
      font-size: 0.9rem;
    }
    .certificate-body .name {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--primary);
      border-bottom: 2px solid var(--gold);
      display: inline-block;
      padding: 0 20px 5px;
      margin: 10px 0 20px;
    }
    .certificate-body .program {
      font-weight: 600;
      color: var(--dark);
      font-size: 1.1rem;
    }
    .certificate-body .duration {
      color: #64748b;
      font-size: 0.95rem;
    }
    .certificate-footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 50px;
      padding-top: 30px;
    }
    .signature-block {
      text-align: center;
      min-width: 180px;
    }
    .signature-line {
      border-top: 1px solid #1e293b;
      padding-top: 8px;
      font-size: 0.85rem;
      color: #64748b;
    }
    .signature-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.1rem;
      font-style: italic;
      margin-bottom: 5px;
    }
    .certificate-seal {
      width: 80px;
      height: 80px;
      border: 3px solid var(--gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      font-size: 0.7rem;
      color: var(--gold);
      text-align: center;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .certificate-seal i {
      font-size: 1.5rem;
      margin-bottom: 3px;
    }
    .certificate-id {
      text-align: center;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px dashed #e2e8f0;
      color: #94a3b8;
      font-size: 0.8rem;
      font-family: 'Courier New', monospace;
    }
    .verified-badge {
      position: absolute;
      top: 20px;
      right: 20px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 2px 10px rgba(16,185,129,0.3);
    }
    
    /* Invalid State */
    .invalid-cert {
      text-align: center;
      padding: 4rem 2rem;
      max-width: 500px;
      margin: 2rem auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .invalid-cert i {
      font-size: 4rem;
      color: #ef4444;
      margin-bottom: 1rem;
    }
    
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; padding: 0 !important; }
      .certificate-wrapper { padding: 0; }
      .certificate { box-shadow: none; }
      .verified-badge { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar no-print">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="bi bi-mortarboard-fill me-2"></i>InternHub</a>
      <a href="index.php" class="btn btn-outline-primary btn-sm">Back to Internships</a>
    </div>
  </nav>

  <?php if (!$searched || !$cert): ?>
  <!-- Search Section -->
  <section class="search-section no-print">
    <div class="container">
      <div class="text-center mb-4">
        <h1 class="fw-bold mb-2">Certificate Verification</h1>
        <p class="opacity-75">Verify the authenticity of an internship certificate</p>
      </div>
      <div class="search-card">
        <form method="get">
          <div class="mb-3">
            <label class="form-label text-muted small">Certificate Code</label>
            <input type="text" class="form-control" name="code" 
                   placeholder="Enter certificate code (e.g., CERT-xxxxx)" 
                   value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>" 
                   pattern="^CERT-[a-zA-Z0-9]{8,}$" maxlength="50" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-search me-2"></i>Verify Certificate
          </button>
        </form>
      </div>
    </div>
  </section>
  
  <?php if ($searched && !$cert): ?>
  <div class="container">
    <div class="invalid-cert">
      <i class="bi bi-x-circle"></i>
      <h4 class="fw-bold text-danger">Invalid Certificate</h4>
      <p class="text-muted mb-0">The certificate code you entered does not exist in our records. Please check the code and try again.</p>
    </div>
  </div>
  <?php endif; ?>
  
  <?php else: ?>
  
  <!-- Certificate Display -->
  <div class="certificate-wrapper">
    <div class="certificate">
      <div class="verified-badge no-print">
        <i class="bi bi-patch-check-fill me-1"></i> Verified
      </div>
      <div class="certificate-border">
        <!-- Header -->
        <div class="certificate-header">
          <div class="certificate-logo">InternHub</div>
          <div class="certificate-subtitle">Internship Program</div>
        </div>
        
        <!-- Title -->
        <div class="certificate-ornament">✦ ✦ ✦</div>
        <h1 class="certificate-title">CERTIFICATE</h1>
        <div class="certificate-ornament">— OF COMPLETION —</div>
        
        <!-- Body -->
        <div class="certificate-body">
          <p class="label mb-0">This is to certify that</p>
          <div class="name"><?= htmlspecialchars($cert['name']) ?></div>
          <p class="mb-1">has successfully completed the</p>
          <p class="program mb-1"><?= htmlspecialchars($cert['title']) ?></p>
          <p class="duration">Duration: <?= htmlspecialchars($cert['duration']) ?></p>
        </div>
        
        <!-- Footer -->
        <div class="certificate-footer">
          <div class="signature-block">
            <div class="signature-name">Program Director</div>
            <div class="signature-line">Authorized Signature</div>
          </div>
          <div class="certificate-seal">
            <i class="bi bi-award"></i>
            <span>Official<br>Seal</span>
          </div>
          <div class="signature-block">
            <div class="signature-name"><?= date('F j, Y', strtotime($cert['issue_date'])) ?></div>
            <div class="signature-line">Date of Issue</div>
          </div>
        </div>
        
        <!-- Certificate ID -->
        <div class="certificate-id">
          Certificate ID: <?= htmlspecialchars($cert['certificate_code']) ?> | Verify at: internhub.com/verify
        </div>
      </div>
    </div>
    
    <div class="text-center mt-4 no-print">
      <button onclick="window.print()" class="btn btn-success btn-lg">
        <i class="bi bi-download me-2"></i>Download Certificate
      </button>
      <a href="verify_certificate.php" class="btn btn-outline-secondary btn-lg ms-2">
        <i class="bi bi-search me-2"></i>Verify Another
      </a>
    </div>
  </div>
  
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>