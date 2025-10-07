<?php
session_start();
require_once "../config.php";

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Hapus lokasi jika ada request delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM locations WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_locations.php");
    exit;
}

// Ambil semua lokasi
$stmt = $pdo->query("SELECT * FROM locations ORDER BY id DESC");
$locations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Lokasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Lokasi</h2>
        <a href="index.php" class="btn btn-secondary btn-sm mb-3">Kembali</a>
        <a href="add_location.php" class="btn btn-primary btn-sm mb-3">Tambah Lokasi</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategori</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locations as $loc): ?>
                    <tr>
                        <td><?= $loc['id'] ?></td>
                        <td><?= ucfirst($loc['type']) ?></td>
                        <td><?= htmlspecialchars($loc['name']) ?></td>
                        <td><?= htmlspecialchars($loc['address']) ?></td>
                        <td><?= htmlspecialchars($loc['phone']) ?></td>
                        <td>
                            <a href="edit_location.php?id=<?= $loc['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="manage_locations.php?delete=<?= $loc['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus lokasi ini?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
