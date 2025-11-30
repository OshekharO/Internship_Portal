<?php
$usePostgres = getenv('USE_POSTGRES') === 'true' ? true : false;

try {
    if ($usePostgres) {
        // PostgreSQL - use environment variables for configuration
        $pgHost = getenv('PG_HOST') ?: "localhost";
        $pgDb = getenv('PG_DATABASE') ?: "internship_portal";
        $pgUser = getenv('PG_USER') ?: "postgres";
        $pgPass = getenv('PG_PASSWORD') ?: "";
        $pgSslMode = getenv('PG_SSLMODE') ?: "prefer";
        
        $conn = new PDO(
            "pgsql:host=$pgHost;dbname=$pgDb;sslmode=$pgSslMode",
            $pgUser,
            $pgPass
        );
    } else {
        // MySQL - use environment variables for configuration
        $host = getenv('MYSQL_HOST') ?: "localhost";
        $db   = getenv('MYSQL_DATABASE') ?: "internship_portal";
        $user = getenv('MYSQL_USER') ?: "root";
        $pass = getenv('MYSQL_PASSWORD') ?: "";

        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("❌ DB Error: " . $e->getMessage());
}
?>
