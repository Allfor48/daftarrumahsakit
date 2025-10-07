<?php
session_start();
require_once "../config.php";

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $services = trim($_POST['services']);
    $open_time = $_POST['open_time'] ?? null;
    $close_time = $_POST['close_time'] ?? null;
    $phone = trim($_POST['phone']);
    $maps_link = trim($_POST['maps_link'] ?? '');
    $imageName = null;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $ext;
        move_uploaded_file($tmpName, $uploadDir . $imageName);
    }

    if ($type && $name) {
        // Simpan lokasi ke database
        $stmt = $pdo->prepare("
            INSERT INTO locations 
            (type, name, address, services, open_time, close_time, phone, image, maps_link)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([$type, $name, $address, $services, $open_time, $close_time, $phone, $imageName, $maps_link]);

        $location_id = $pdo->lastInsertId();

        // Simpan data dokter (jika diisi)
        if (isset($_POST['doctor_name'])) {
            $doctorStmt = $pdo->prepare("
                INSERT INTO doctors (location_id, name, schedule, specialty)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($_POST['doctor_name'] as $i => $doctorName) {
                $doctorName = trim($doctorName);
                $schedule = trim($_POST['doctor_schedule'][$i] ?? '');
                $specialty = trim($_POST['doctor_specialty'][$i] ?? '');
                if ($doctorName !== '') {
                    $doctorStmt->execute([$location_id, $doctorName, $schedule, $specialty]);
                }
            }
        }

        $message = "Lokasi dan dokter berhasil ditambahkan!";
    } else {
        $message = "Tipe dan Nama wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Lokasi & Dokter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script>
        function addDoctorField() {
            const container = document.getElementById('doctors-container');
            const newField = document.createElement('div');
            newField.classList.add('border', 'p-3', 'mb-2', 'rounded');
            newField.innerHTML = `
                <div class="mb-2">
                    <label>Nama Dokter</label>
                    <input type="text" name="doctor_name[]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Jadwal Praktek</label>
                    <input type="text" name="doctor_schedule[]" class="form-control" placeholder="Senin - Jumat, 08.00 - 14.00">
                </div>
                <div class="mb-2">
                    <label>Spesialisasi</label>
                    <input type="text" name="doctor_specialty[]" class="form-control" placeholder="Contoh: Dokter Umum">
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Hapus Dokter</button>
            `;
            container.appendChild(newField);
        }
    </script>
</head>
<body>
<div class="container mt-4">
    <h2>Tambah Lokasi & Dokter</h2>
    <a href="index.php" class="btn btn-secondary btn-sm mb-3">Kembali</a>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <h4>Data Lokasi</h4>
        <div class="mb-3">
            <label>Kategori</label>
            <select name="type" class="form-select" required>
                <option value="">-- pilih --</option>
                <option value="rumah_sakit">Rumah Sakit</option>
                <option value="puskesmas">Puskesmas</option>
                <option value="klinik">Klinik</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Nama Lokasi</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="address" class="form-control">
        </div>

        <div class="mb-3">
            <label>Layanan (pisahkan dengan ;)</label>
            <textarea name="services" class="form-control"></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Jam Buka</label>
                <input type="time" name="open_time" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Jam Tutup</label>
                <input type="time" name="close_time" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>No. Telepon</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label>Gambar Lokasi (opsional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label>Link Google Maps (opsional)</label>
            <input type="url" name="maps_link" class="form-control" placeholder="https://maps.google.com/...">
        </div>

        <hr>
        <h4>Data Dokter</h4>
        <div id="doctors-container"></div>
        <button type="button" class="btn btn-success btn-sm mb-3" onclick="addDoctorField()">+ Tambah Dokter</button>

        <div>
            <button class="btn btn-primary">Simpan Semua</button>
        </div>
    </form>
</div>
</body>
</html>
