<?php
session_start();
require_once "config.php";

$message = "";
$error = "";

// Jika form register dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($username && $password && $password_confirm) {
        if ($password !== $password_confirm) {
            $error = "Password dan konfirmasi password tidak sama!";
        } else {
            // Cek apakah username sudah ada
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username sudah digunakan!";
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Simpan ke database (default role = user)
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?,?,?)");
                $stmt->execute([$username, $password_hash, 'user']);

                $message = "Registrasi berhasil! Silakan login.";
            }
        }
    } else {
        $error = "Semua field wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Sistem Informasi Rumah Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #6c63ff, #00c6ff);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 15px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            background-color: #fff;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(108,99,255,0.25);
            border-color: #6c63ff;
        }
        .btn-primary {
            background-color: #6c63ff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #574bff;
        }
        .input-group-text {
            background-color: #6c63ff;
            color: #fff;
            border: none;
        }
        .text-muted a {
            color: #6c63ff;
            text-decoration: none;
        }
        .text-muted a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4">Register</h3>

    <?php if ($message): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password_confirm" class="form-control" placeholder="Ulangi password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
    </form>

    <p class="text-center mt-3 text-muted small">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </p>
</div>

</body>
</html>
