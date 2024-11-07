<?php
include 'connect.php';
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Proses penambahan produk
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    // Memasukkan data ke dalam database
    $stmt = $conn->prepare("INSERT INTO products (name, description, category, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $description, $category, $price, $stock, $image);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?status=Product added successfully.");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Product</h1>
    <form action="" method="post">
        <input type="text" name="name" placeholder="Product Name" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <select name="category" required>
            <option value="Alat Kesehatan">Alat Kesehatan</option>
            <option value="Alat Medisi">Alat Medisi</option>
        </select>
        <input type="number" name="price" placeholder="Price" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <input type="text" name="image" placeholder="Image URL" required>
        <button type="submit">Add Product</button>
    </form>
</body>
</html>
