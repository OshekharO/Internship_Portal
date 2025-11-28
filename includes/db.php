<?php
$usePostgres = getenv('USE_POSTGRES') === 'true' ? true : false;

try {
    if ($usePostgres) {
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
