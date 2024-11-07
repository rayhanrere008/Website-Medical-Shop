<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa kecocokan pengguna
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['user_id'] = mysqli_fetch_assoc($result)['id'];
        $_SESSION['cart'] = []; // Mengosongkan keranjang belanja saat login baru
        header('Location: products.php');
        exit;
    } else {
        $error = "ID pengguna atau kata sandi salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/logo.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login</title>
</head>
<body>
    <div class="wrapper"> <!-- Gunakan .wrapper untuk membungkus konten -->
        <div class="card"> <!-- Tambahkan class card di sini -->
            <img src="images/logo.png" alt="Logo Toko Alat Kesehatan" style="display: block; margin: 0 auto; width: 150px; height: auto;"> <!-- Ganti dengan nama file logo Anda -->
            <h1>Selamat Datang di HealthCare Store</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">User ID:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="login-button">LOGIN</button> <!-- Tambahkan class untuk tombol -->
            </form>
            <?php if (isset($error)) echo "<p style='color:red; text-align: center;'>$error</p>"; ?>
            <p style="text-align: center;">
                <a href="register.php" style="color: #007bff; text-decoration: none;">Don't have an account? Register</a>
            </p>
        </div>
    </div>
</body>
</html>
