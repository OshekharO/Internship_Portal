<?php
$usePostgres = false; // toggle true/false

try {
    if ($usePostgres) {
        // Neon.tech PostgreSQL
        $conn = new PDO(
            "pgsql:host=ep-frosty-mode-a8frqif3-pooler.eastus2.azure.neon.tech;dbname=internship_portal;sslmode=require",
            "neondb_owner",
            "npg_dbiVcFoQ4xu2"
        );
    } else {
        // InfinityFree MySQL
        $host = "sql111.infinityfree.com";
        $db   = "if0_36954599_internship";
        $user = "if0_36954599";
        $pass = "omee12345";

        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("❌ DB Error: " . $e->getMessage());
}
?>