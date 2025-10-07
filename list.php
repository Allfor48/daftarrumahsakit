<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$type = $_GET['type'] ?? '';
$allowed = ['rumah_sakit', 'puskesmas', 'klinik'];

if (!in_array($type, $allowed)) {
    die("Kategori tidak valid!");
}

$stmt = $pdo->prepare("SELECT * FROM locations WHERE type = ?");
$stmt->execute([$type]);
$locations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar <?= ucfirst(str_replace('_', ' ', $type)) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Daftar <?= ucfirst(str_replace('_', ' ', $type)) ?></h2>
    <a href="index.php" class="btn btn-secondary btn-sm mb-3">Kembali</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Jam Buka</th>
                <th>Jam Tutup</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $loc): ?>
                <tr>
                    <td><?= htmlspecialchars($loc['name']) ?></td>
                    <td><?= htmlspecialchars($loc['address']) ?></td>
                    <td><?= htmlspecialchars($loc['open_time']) ?></td>
                    <td><?= htmlspecialchars($loc['close_time']) ?></td>
                    <td>
                        <a href="detail.php?id=<?= $loc['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!$locations): ?>
        <p>Tidak ada data <?= ucfirst(str_replace('_', ' ', $type)) ?>.</p>
    <?php endif; ?>
</div>
</body>
</html>
