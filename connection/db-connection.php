<?php
$host = 'localhost';
$db   = 'database_warnet';
$user = 'root'; // ganti jika kamu pakai user lain
$pass = '';     // isi password MySQL kamu kalau ada

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
