<?php
// config.php
// Sesuaikan credential sesuai XAMPP/Laragon

$db_host = 'localhost';
$db_name = 'dafarrumahsakit_db';
$db_user = 'root';
$db_pass = ''; // jika ada password, isi di sini

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch rows as associative arrays
];

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        $options
    );
} catch (PDOException $e) {
    // Untuk development tampilkan error. Di production ganti handling.
    die("Database connection failed: " . $e->getMessage());
}
