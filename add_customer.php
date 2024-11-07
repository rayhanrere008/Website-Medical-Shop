<?php
include 'connect.php';
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Inisialisasi variabel status
$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone_number = $_POST['phone_number'];
    $paypal_id = $_POST['paypal_id'];

    // Query untuk menambahkan customer ke database
    $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, email, birthdate, gender, address, city, phone_number, paypal_id, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'customer')");
    $stmt->bind_param("ssssssssss", $username, $password, $fullname, $email, $birthdate, $gender, $address, $city, $phone_number, $paypal_id);

    if ($stmt->execute()) {
        // Set status sukses
        $status = "Customer berhasil ditambahkan!";
        // Redirect kembali ke dashboard admin setelah berhasil
        header("Location: admin_dashboard.php?status=" . urlencode($status));
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
