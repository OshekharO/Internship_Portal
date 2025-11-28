<?php
$usePostgres = getenv('USE_POSTGRES') === 'true' ? true : false;
$useSqlite = getenv('USE_SQLITE') === 'true' ? true : false;

try {
    if ($useSqlite) {
        // SQLite - for local testing
        $dbPath = __DIR__ . '/../data/internship_portal.db';
        $conn = new PDO("sqlite:$dbPath");
        
        // Initialize tables if they don't exist
        $conn->exec("CREATE TABLE IF NOT EXISTS internships (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(100),
            description TEXT,
            duration VARCHAR(50)
        )");
        
        $conn->exec("CREATE TABLE IF NOT EXISTS applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            internship_id INTEGER,
            name VARCHAR(100),
            email VARCHAR(100),
            status TEXT DEFAULT 'pending',
            FOREIGN KEY (internship_id) REFERENCES internships(id)
        )");
        
        $conn->exec("CREATE TABLE IF NOT EXISTS certificates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            application_id INTEGER,
            certificate_code VARCHAR(50) UNIQUE,
            issue_date DATE,
            FOREIGN KEY (application_id) REFERENCES applications(id)
        )");
        
        $conn->exec("CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE,
            password VARCHAR(255)
        )");
        
        // Seed data if empty
        $count = $conn->query("SELECT COUNT(*) FROM internships")->fetchColumn();
        if ($count == 0) {
            $conn->exec("INSERT INTO internships (title, description, duration) VALUES
                ('Web Development Internship', 'Work with HTML, CSS, JS to build projects.', '3 Months'),
                ('Data Science Internship', 'Analyze datasets using Python and ML.', '2 Months'),
                ('Digital Marketing Internship', 'Learn SEO, SEM and social media strategies.', '1 Month')");
            $conn->exec("INSERT INTO admins (username, password) VALUES ('admin', 'admin')");
        }
    } else if ($usePostgres) {
        // PostgreSQL - use environment variables if available
        $pgHost = getenv('PG_HOST') ?: "ep-frosty-mode-a8frqif3-pooler.eastus2.azure.neon.tech";
        $pgDb = getenv('PG_DATABASE') ?: "internship_portal";
        $pgUser = getenv('PG_USER') ?: "neondb_owner";
        $pgPass = getenv('PG_PASSWORD') ?: "npg_dbiVcFoQ4xu2";
        
        $conn = new PDO(
            "pgsql:host=$pgHost;dbname=$pgDb;sslmode=require",
            $pgUser,
            $pgPass
        );
    } else {
        // MySQL - use environment variables if available
        $host = getenv('MYSQL_HOST') ?: "sql12.freesqldatabase.com";
        $db   = getenv('MYSQL_DATABASE') ?: "sql12809734";
        $user = getenv('MYSQL_USER') ?: "sql12809734";
        $pass = getenv('MYSQL_PASSWORD') ?: "VsiqQSIvsB";

        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("❌ DB Error: " . $e->getMessage());
}
?>
