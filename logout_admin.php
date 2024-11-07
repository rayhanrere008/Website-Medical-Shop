<?php
session_start();
session_destroy(); // Hapus semua sesi
header("Location: admin_login.php"); // Alihkan ke halaman login admin
exit();
?>
