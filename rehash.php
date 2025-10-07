<?php
require_once "config.php";

$users = [
    ['username' => 'admin', 'password' => 'admin123'],
    ['username' => 'user', 'password' => 'user123'],
    ['username' => 'fajri', 'password' => 'fajri123'],
];

foreach ($users as $u) {
    $hash = password_hash($u['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->execute([$hash, $u['username']]);
    echo "Updated {$u['username']}<br>";
}
echo "✅ Semua akun diperbarui dengan hash bcrypt.";
?>
