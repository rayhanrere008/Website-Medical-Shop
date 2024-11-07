<?php
$host = 'localhost'; // Host database
$user = 'root'; // Nama pengguna MySQL
$password = ''; // Kata sandi MySQL
$database = 'shopping_system'; // Nama database yang digunakan

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>