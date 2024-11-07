<?php
include 'connect.php';
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Cek apakah data POST ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $customer_id = $_POST['customer_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone_number = $_POST['phone_number'];

    // Siapkan array untuk parameter yang akan di-bind
    $params = [];
    $types = "";

    // Tambahkan parameter ke dalam array jika tidak kosong
    if (!empty($fullname)) {
        $params[] = $fullname;
        $types .= "s";
    } else {
        $params[] = null; // Jika kosong, tambahkan null
        $types .= "s"; // Tetap tambahkan tipe
    }

    if (!empty($email)) {
        $params[] = $email;
        $types .= "s";
    } else {
        $params[] = null;
        $types .= "s";
    }

    if (!empty($address)) {
        $params[] = $address;
        $types .= "s";
    } else {
        $params[] = null;
        $types .= "s";
    }

    if (!empty($city)) {
        $params[] = $city;
        $types .= "s";
    } else {
        $params[] = null;
        $types .= "s";
    }

    if (!empty($phone_number)) {
        $params[] = $phone_number;
        $types .= "s";
    } else {
        $params[] = null;
        $types .= "s";
    }

    // Tambahkan customer_id sebagai parameter terakhir
    $params[] = $customer_id;
    $types .= "i"; // i untuk integer

    // Query untuk update data customer
    $sql = "UPDATE users SET 
                fullname = COALESCE(NULLIF(?, ''), fullname),
                email = COALESCE(NULLIF(?, ''), email),
                address = COALESCE(NULLIF(?, ''), address),
                city = COALESCE(NULLIF(?, ''), city),
                phone_number = COALESCE(NULLIF(?, ''), phone_number)
            WHERE id = ?";

    // Persiapkan statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameter
        $stmt->bind_param($types, ...$params);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Redirect dengan status sukses
            header("Location: admin_dashboard.php?status=Customer updated successfully!");
        } else {
            // Redirect dengan status error
            header("Location: admin_dashboard.php?status=Failed to update customer!");
        }
        $stmt->close();
    } else {
        // Redirect dengan status error jika query gagal
        header("Location: admin_dashboard.php?status=Query preparation failed!");
    }
}

// Tutup koneksi
$conn->close();
?>
