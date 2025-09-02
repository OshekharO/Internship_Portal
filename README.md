# Internship Portal

A simplified internship portal with internship listings, applications, admin dashboard, and certificate verification.

---

## 🚀 Features
- Browse internships
- Apply for internships (with resume upload)
- Admin dashboard to manage applications & issue certificates
- Certificate verification page
- Works with both **MySQL** and **PostgreSQL**
- Ready-to-deploy structure

---

## 📂 Project Structure
```
internship-portal/
│── index.html              # Homepage (list internships)
│── apply.php               # Application form
│── submit_application.php  # Handles application form submissions
│── verify_certificate.php  # Certificate verification
│── admin/
│   ├── login.php           # Admin login
│   ├── dashboard.php       # View & manage applications
│   ├── issue_certificate.php # Issue certificate to selected application
│── uploads/                # Uploaded resumes (create writable folder)
│── sql/
│   ├── schema_mysql.sql    # Database schema & seed data for MySQL
│   ├── schema_postgres.sql # Database schema & seed data for PostgreSQL
```

---

## 🛠️ Setup Instructions

### 1️⃣ Database Setup

#### MySQL
```bash
mysql -u root -p internship_portal < sql/schema_mysql.sql
```

#### PostgreSQL
```bash
psql -U postgres -d internship_portal -f sql/schema_postgres.sql
```

---

### 2️⃣ Configure Database Connection
Update `config.php`:
```php
<?php
// For MySQL
$dsn = "mysql:host=localhost;dbname=internship_portal;charset=utf8";
$user = "root";
$password = "your_mysql_password";

// For PostgreSQL
// $dsn = "pgsql:host=localhost;port=5432;dbname=internship_portal;";
// $user = "postgres";
// $password = "your_pg_password";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
```

---

### 3️⃣ File Permissions
Make `uploads/` writable:
```bash
chmod 755 uploads
```

---

### 4️⃣ Admin Login
- URL: `/admin/login.php`
- Default credentials:
  - **Username:** admin  
  - **Password:** admin  

---

### 5️⃣ Certificate Verification
Users can verify certificates using their **certificate code** at:
```
verify_certificate.php?code=XXXX
```

---

## 📌 Notes
- If your host does not support `fpdf`, the system generates **HTML-based certificates**.
- To change admin password → update the `admins` table in DB.
- Default data is seeded via SQL scripts.
