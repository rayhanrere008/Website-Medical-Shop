<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa kecocokan pengguna admin
    $query = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
    $result = mysqli_query($conn, $query);

    // Debugging output
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Pastikan ini ada
            header('Location: admin_dashboard.php');
            exit();
        } else {
            // Tambahkan debugging output
            echo "Username: $username, Password: $password, DB Password: " . $user['password'];
            $error = "Password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="images/logo.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login Admin</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4; /* Ubah warna latar belakang jika perlu */
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 150%;
            text-align: center;
        }
        .container h1 {
            margin-bottom: 20px;
        }
        .container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        .container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .container button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #28a745; /* Warna hijau untuk tombol */
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #218838; /* Warna hijau lebih gelap saat hover */
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/logo.png" alt="Logo Toko Alat Kesehatan" style="display: block; margin: 0 auto; width: 150px; height: auto;">
        <h1>Selamat Datang di HealthCare Store - Admin</h1>
        <form method="POST" action="">
            <label for="username">Admin ID:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <button type="submit">LOGIN</button>
        </form>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
