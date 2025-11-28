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
- Mobile responsive design

---

## 📂 Project Structure
```
internship-portal/
│── index.php               # Homepage (list internships)
│── apply.php               # Application form
│── submit_application.php  # Handles application form submissions
│── verify_certificate.php  # Certificate verification
│── admin/
│   ├── login.php           # Admin login
│   ├── dashboard.php       # View & manage applications
│   ├── manage_internships.php # Manage internship listings
│   ├── issue_certificate.php # Issue certificate to selected application
│── includes/
│   ├── db.php              # Database connection configuration
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
The database configuration is in `includes/db.php`. You can configure it using environment variables:

**For MySQL:**
```bash
export MYSQL_HOST=localhost
export MYSQL_DATABASE=internship_portal
export MYSQL_USER=root
export MYSQL_PASSWORD=your_mysql_password
```

**For PostgreSQL:**
```bash
export USE_POSTGRES=true
export PG_HOST=localhost
export PG_DATABASE=internship_portal
export PG_USER=postgres
export PG_PASSWORD=your_pg_password
```

Alternatively, you can modify `includes/db.php` directly with your credentials.

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
