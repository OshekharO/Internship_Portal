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
        $host = getenv('MYSQL_HOST') ?: "db.fr-pari1.bengt.wasmernet.com";
        $db   = getenv('MYSQL_DATABASE') ?: "sakshe";
        $user = getenv('MYSQL_USER') ?: "0c2ade337e168000bfb451218559";
        $pass = getenv('MYSQL_PASSWORD') ?: "06a00c2a-de34-7058-8000-f0c663b39de1";

        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("❌ DB Error: " . $e->getMessage());
}
?>
