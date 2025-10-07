<?php
session_start();
require_once "config.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID lokasi dari URL
if (!isset($_GET['id'])) {
    die("Lokasi tidak ditemukan.");
}
$id = intval($_GET['id']);

// Ambil data lokasi
$stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
$stmt->execute([$id]);
$location = $stmt->fetch();

if (!$location) {
    die("Data lokasi tidak ditemukan.");
}

// Ambil data dokter berdasarkan location_id
$stmt_doctors = $pdo->prepare("SELECT * FROM doctors WHERE location_id = ?");
$stmt_doctors->execute([$id]);
$doctors = $stmt_doctors->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Lokasi - <?= htmlspecialchars($location['name'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .location-img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .btn-back {
            margin-bottom: 20px;
        }
        .maps-link a {
            text-decoration: none;
        }
        .section-title {
            font-weight: 600;
            color: #333;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .doctor-card {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="index.php" class="btn btn-secondary btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
    <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="card">
    <h3 class="card-title mb-3"><?= htmlspecialchars($location['name'] ?? '') ?></h3>

    <?php if (!empty($location['image'])): ?>
        <img src="admin/uploads/<?= htmlspecialchars($location['image']) ?>" class="location-img" alt="Gambar Lokasi">
    <?php endif; ?>

    <?php if (!empty($location['type'])): ?>
        <p><b>Kategori:</b> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $location['type']))) ?></p>
    <?php endif; ?>

    <?php if (!empty($location['address'])): ?>
        <p><b>Alamat:</b> <?= htmlspecialchars($location['address']) ?></p>
    <?php endif; ?>

    <?php if (!empty($location['phone'])): ?>
        <p><b>Telepon:</b> <?= htmlspecialchars($location['phone']) ?></p>
    <?php endif; ?>

    <?php if (!empty($location['services'])): ?>
        <p><b>Layanan:</b><br> <?= nl2br(htmlspecialchars($location['services'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($location['open_time'])): ?>
        <p><b>Jam Operasional:</b> <?= htmlspecialchars($location['open_time']) ?> - <?= htmlspecialchars($location['close_time']) ?></p>
    <?php endif; ?>

    <?php if (!empty($location['maps_link'])): ?>
        <p class="maps-link"><b>Lokasi Maps:</b> 
            <a href="<?= htmlspecialchars($location['maps_link']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-geo-alt"></i> Buka di Google Maps
            </a>
        </p>
    <?php endif; ?>

    <!-- Bagian daftar dokter -->
    <h5 class="section-title"><i class="bi bi-person-badge"></i> Daftar Dokter</h5>

    <?php if (count($doctors) > 0): ?>
        <?php foreach ($doctors as $doc): ?>
            <div class="doctor-card">
                <b><?= htmlspecialchars($doc['name']) ?></b><br>
                <small><b>Spesialis:</b> <?= htmlspecialchars($doc['specialty'] ?: '-') ?></small><br>
                <small><b>Jadwal:</b> <?= htmlspecialchars($doc['schedule'] ?: '-') ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Belum ada data dokter untuk lokasi ini.</p>
    <?php endif; ?>
</div>

</body>
</html>
