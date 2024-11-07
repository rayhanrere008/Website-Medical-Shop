<?php
include 'connect.php'; // Sertakan file koneksi database
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Cek apakah ada data yang diterima
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];

    // Siapkan dan eksekusi query untuk menghapus customer
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $customer_id);

    if ($stmt->execute()) {
        // Jika berhasil, redirect dengan status sukses
        header("Location: admin_dashboard.php?status=Customer berhasil dihapus");
    } else {
        // Jika gagal, redirect dengan status error
        header("Location: admin_dashboard.php?status=Gagal menghapus customer");
    }

    $stmt->close(); // Tutup statement
}
$conn->close(); // Tutup koneksi
?>
