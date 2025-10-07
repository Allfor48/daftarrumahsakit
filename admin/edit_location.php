<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM locations WHERE id = ?");
$stmt->execute([$id]);
$loc = $stmt->fetch();

if (!$loc) {
    die("Data lokasi tidak ditemukan!");
}

$upload_dir = "uploads/"; // 🔹 Ubah ke folder admin/uploads/

// 🟢 Proses update lokasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_location'])) {
    $name        = $_POST['name'] ?? '';
    $type        = $_POST['type'] ?? '';
    $address     = $_POST['address'] ?? '';
    $phone       = $_POST['phone'] ?? '';
    $services    = $_POST['services'] ?? '';
    $open_time   = $_POST['open_time'] ?? null;
    $close_time  = $_POST['close_time'] ?? null;

    $image_name = $loc['image'] ?? null;

    // 🟠 Upload gambar baru (jika ada)
    if (!empty($_FILES['image']['name'])) {
        $target_file = $upload_dir . basename($_FILES['image']['name']);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed)) {
            // Hapus gambar lama jika ada
            if (!empty($loc['image']) && file_exists($upload_dir . $loc['image'])) {
                unlink($upload_dir . $loc['image']);
            }

            // Simpan gambar baru
            $new_name = uniqid("img_") . "." . $file_type;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name);
            $image_name = $new_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE locations 
        SET name=?, type=?, address=?, phone=?, services=?, open_time=?, close_time=?, image=? 
        WHERE id=?");
    $stmt->execute([$name, $type, $address, $phone, $services, $open_time, $close_time, $image_name, $id]);

    header("Location: edit_location.php?id=$id&msg=updated");
    exit;
}

// 🟢 Tambah dokter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $doctor_name = $_POST['doctor_name'] ?? '';
    $specialty   = $_POST['specialty'] ?? '';
    $schedule    = $_POST['schedule'] ?? '';

    if ($doctor_name) {
        $stmt = $pdo->prepare("INSERT INTO doctors (location_id, name, specialty, schedule) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $doctor_name, $specialty, $schedule]);
    }

    header("Location: edit_location.php?id=$id&msg=doctor_added");
    exit;
}

// 🟢 Update dokter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_doctor'])) {
    $doctor_id   = $_POST['doctor_id'] ?? 0;
    $doctor_name = $_POST['doctor_name'] ?? '';
    $specialty   = $_POST['specialty'] ?? '';
    $schedule    = $_POST['schedule'] ?? '';

    $stmt = $pdo->prepare("UPDATE doctors SET name=?, specialty=?, schedule=? WHERE id=? AND location_id=?");
    $stmt->execute([$doctor_name, $specialty, $schedule, $doctor_id, $id]);

    header("Location: edit_location.php?id=$id&msg=doctor_updated");
    exit;
}

// 🟢 Hapus dokter
if (isset($_GET['delete_doctor'])) {
    $doctor_id = intval($_GET['delete_doctor']);
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id=? AND location_id=?");
    $stmt->execute([$doctor_id, $id]);
    header("Location: edit_location.php?id=$id&msg=doctor_deleted");
    exit;
}

// 🟢 Ambil semua dokter
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE location_id = ?");
$stmt->execute([$id]);
$doctors = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Lokasi & Dokter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        img.preview {
            width: 200px;
            height: auto;
            border-radius: 10px;
            margin-top: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
<div class="container mt-4 mb-5">
    <h2>Edit Lokasi</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_location" value="1">

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" value="<?= htmlspecialchars($loc['name'] ?? '') ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jenis</label>
            <select name="type" class="form-control" required>
                <option value="rumah_sakit" <?= ($loc['type'] ?? '') == 'rumah_sakit' ? 'selected' : '' ?>>Rumah Sakit</option>
                <option value="puskesmas" <?= ($loc['type'] ?? '') == 'puskesmas' ? 'selected' : '' ?>>Puskesmas</option>
                <option value="klinik" <?= ($loc['type'] ?? '') == 'klinik' ? 'selected' : '' ?>>Klinik</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($loc['address'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label>Telepon</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($loc['phone'] ?? '') ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Layanan</label>
            <textarea name="services" class="form-control"><?= htmlspecialchars($loc['services'] ?? '') ?></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Jam Buka</label>
                <input type="time" name="open_time" value="<?= htmlspecialchars($loc['open_time'] ?? '') ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Jam Tutup</label>
                <input type="time" name="close_time" value="<?= htmlspecialchars($loc['close_time'] ?? '') ?>" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Gambar Lokasi</label>
            <input type="file" name="image" accept="image/*" class="form-control">
            <?php if (!empty($loc['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($loc['image']) ?>" class="preview">
            <?php else: ?>
                <p class="text-muted mt-2">Belum ada gambar</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>

    <hr class="my-4">

    <h3>Daftar Dokter</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Dokter</th>
                <th>Spesialis</th>
                <th>Jadwal</th>
                <th style="width: 160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctors as $doc): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="update_doctor" value="1">
                        <input type="hidden" name="doctor_id" value="<?= $doc['id'] ?>">
                        <td><input type="text" name="doctor_name" class="form-control" value="<?= htmlspecialchars($doc['name'] ?? '') ?>" required></td>
                        <td><input type="text" name="specialty" class="form-control" value="<?= htmlspecialchars($doc['specialty'] ?? '') ?>"></td>
                        <td><input type="text" name="schedule" class="form-control" value="<?= htmlspecialchars($doc['schedule'] ?? '') ?>"></td>
                        <td>
                            <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                            <a href="edit_location.php?id=<?= $id ?>&delete_doctor=<?= $doc['id'] ?>" 
                               onclick="return confirm('Yakin ingin menghapus dokter ini?')" 
                               class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>

            <tr>
                <form method="POST">
                    <input type="hidden" name="add_doctor" value="1">
                    <td><input type="text" name="doctor_name" class="form-control" placeholder="Nama dokter baru"></td>
                    <td><input type="text" name="specialty" class="form-control" placeholder="Spesialisasi"></td>
                    <td><input type="text" name="schedule" class="form-control" placeholder="Jadwal praktek"></td>
                    <td><button type="submit" class="btn btn-primary btn-sm">Tambah</button></td>
                </form>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
