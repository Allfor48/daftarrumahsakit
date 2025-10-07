<?php
session_start();
require_once "config.php";

// cek login user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// filter kategori & pencarian
$type = isset($_GET['type']) ? $_GET['type'] : '';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

try {
    // Query gabungan lokasi + dokter
    $sql = "
        SELECT l.*, 
               GROUP_CONCAT(DISTINCT d.name SEPARATOR ', ') AS doctor_names,
               GROUP_CONCAT(DISTINCT d.specialty SEPARATOR ', ') AS doctor_specialties,
               GROUP_CONCAT(DISTINCT d.schedule SEPARATOR ', ') AS doctor_schedules
        FROM locations l
        LEFT JOIN doctors d ON l.id = d.location_id
    ";

    // filter dinamis
    $params = [];
    $conditions = [];

    if ($type) {
        $conditions[] = "l.type = ?";
        $params[] = $type;
    }

    if ($keyword) {
        $conditions[] = "(l.name LIKE ? OR d.name LIKE ? OR d.specialty LIKE ? OR d.schedule LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY l.id ORDER BY l.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $locations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query gagal: " . $e->getMessage());
}

include "templates/sidebar.php";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Lokasi Kesehatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>


<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="header">
        <h3 class="fw-bold">Dashboard</h3>
        <form method="get" class="search-box">
            <input type="text" name="keyword" placeholder="Cari lokasi, dokter, atau spesialis..." value="<?= htmlspecialchars($keyword) ?>">
            <?php if($type): ?>
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
            <?php endif; ?>
            <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div>
        <div class="stats">
            <div class="stat-card">
                <h6>Total Lokasi</h6>
                <h4><?= count($locations) ?></h4>
            </div>
            <div class="stat-card">
                <h6>Rumah Sakit</h6>
                <h4>
                    <?php
                    $count_rs = 0;
                    foreach ($locations as $loc) {
                        if ($loc['type'] === 'rumah_sakit') $count_rs++;
                    }
                    echo $count_rs;
                    ?>
                </h4>
            </div>
            <div class="stat-card">
                <h6>Puskesmas</h6>
                <h4>
                    <?php
                    $count_pkm = 0;
                    foreach ($locations as $loc) {
                        if ($loc['type'] === 'puskesmas') $count_pkm++;
                    }
                    echo $count_pkm;
                    ?>
                </h4>
            </div>
            <div class="stat-card">
                <h6>Klinik</h6>
                <h4>
                    <?php
                    $count_klinik = 0;
                    foreach ($locations as $loc) {
                        if ($loc['type'] === 'klinik') $count_klinik++;
                    }
                    echo $count_klinik;
                    ?>
                </h4>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($locations): ?>
            <?php foreach ($locations as $row): ?>
                <div class="col-md-6">
                    <div class="location-card">
                        <div class="flex-grow-1">
                            <h5><?= htmlspecialchars($row['name']) ?></h5>
                            <?php if (!empty($row['address'])): ?>
                                <p><b>Alamat:</b> <?= htmlspecialchars($row['address']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($row['phone'])): ?>
                                <p><b>Telp:</b> <?= htmlspecialchars($row['phone']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($row['services'])): ?>
                                <p><b>Layanan:</b> <?= nl2br(htmlspecialchars($row['services'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($row['doctor_names'])): ?>
                                <p><b>Dokter:</b> <?= htmlspecialchars($row['doctor_names']) ?></p>
                                <p><b>Spesialis:</b> <?= htmlspecialchars($row['doctor_specialties']) ?></p>
                                <p><b>Jadwal:</b> <?= htmlspecialchars($row['doctor_schedules']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($row['open_time'])): ?>
                                <p><b>Jam Buka:</b> <?= $row['open_time'] ?> - <?= $row['close_time'] ?></p>
                            <?php endif; ?>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm mt-2">Lihat Detail</a>
                            <?php if (!empty($row['maps_link'])): ?>
                                <a href="<?= htmlspecialchars($row['maps_link']) ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-2">📍</a>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($row['image'])): ?>
                            <img src="admin/uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="location-img">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center mt-5">Tidak ada hasil ditemukan.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html> 
