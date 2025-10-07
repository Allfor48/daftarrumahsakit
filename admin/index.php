<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</h2>
   <a href="../logout.php" class="btn btn-danger mt-3">Logout</a>


    <hr>
    <h3>Menu Admin:</h3>
    <ul>
        
        <li><a href="add_location.php">Tambah Lokasi Baru</a></li>
        <li><a href="manage_locations.php">Kelola Daftar Lokasi</a></li>
        <li><a href="manage_doctors.php">Kelola Dokter</a></li>

    </ul>
</div>
</body>
</html>
