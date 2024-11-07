<?php
session_start();
require 'connect.php';

// Memastikan pengguna telah login sebelum melihat halaman produk
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mendapatkan daftar kategori
$categoryQuery = "SELECT DISTINCT category FROM products";
$categoryResult = mysqli_query($conn, $categoryQuery);

// Mendapatkan kategori yang dipilih (jika ada)
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Mendapatkan daftar produk berdasarkan kategori yang dipilih
if ($selectedCategory) {
    $query = "SELECT * FROM products WHERE category = '$selectedCategory'";
} else {
    $query = "SELECT * FROM products";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/logo.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Produk Toko Alat Kesehatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4; /* Warna latar belakang */
        }
        .container {
            max-width: 1200px; /* Lebar maksimum container */
            margin: auto; /* Memusatkan container */
            padding: 20px; /* Padding di dalam container */
            background-color: #fff; /* Warna latar belakang putih untuk konten */
            border-radius: 8px; /* Sudut melengkung */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Bayangan untuk efek kedalaman */
        }
        h1 {
            text-align: center; /* Memusatkan judul */
            margin-bottom: 20px; /* Jarak di bawah judul */
        }
        .category-navbar {
            display: flex; /* Menggunakan flexbox untuk navbar */
            justify-content: center; /* Memusatkan navbar */
            margin-bottom: 20px; /* Jarak di bawah navbar */
        }
        .category-navbar ul {
            list-style: none; /* Menghilangkan bullet point */
            padding: 0; /* Menghilangkan padding default */
        }
        .category-navbar li {
            display: inline; /* Menampilkan item dalam satu baris */
            margin: 0 15px; /* Jarak antar item */
        }
        .category-navbar a {
            text-decoration: none; /* Menghilangkan garis bawah */
            color: #007bff; /* Warna teks link */
        }
        .category-navbar a.active {
            font-weight: bold; /* Menebalkan teks untuk kategori aktif */
        }
        .products {
            display: grid; /* Menggunakan grid untuk tata letak produk */
            grid-template-columns: repeat(4, 1fr); /* 4 kolom */
            gap: 20px; /* Jarak antar produk */
        }
        .product {
            border: 1px solid #ccc; /* Border untuk produk */
            border-radius: 5px; /* Sudut melengkung */
            padding: 10px; /* Padding di dalam produk */
            text-align: center; /* Memusatkan teks di dalam produk */
            background-color: #fff; /* Latar belakang putih untuk produk */
        }
        .product img {
            max-width: 100%; /* Memastikan gambar tidak lebih lebar dari kontainer */
            height: auto; /* Mempertahankan rasio aspek gambar */
        }
        .buy-button {
            display: inline-block; /* Mengatur tombol untuk ditampilkan sebagai blok */
            padding: 10px 15px; /* Padding di dalam tombol */
            background-color: #28a745; /* Warna latar belakang tombol */
            color: white; /* Warna teks tombol */
            text-decoration: none; /* Menghilangkan garis bawah */
            border-radius: 5px; /* Sudut melengkung untuk tombol */
            transition: background-color 0.3s; /* Transisi untuk efek hover */
        }
        .buy-button:hover {
            background-color: #218838; /* Warna tombol saat hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daftar Produk</h1>

        <!-- Navbar Kategori -->
        <div class="category-navbar">
            <ul>
                <li><a href="products.php" class="<?php echo $selectedCategory == '' ? 'active' : ''; ?>">Semua Kategori</a></li>
                <?php while ($category = mysqli_fetch_assoc($categoryResult)): ?>
                    <li>
                        <a href="products.php?category=<?php echo $category['category']; ?>" 
                           class="<?php echo $selectedCategory == $category['category'] ? 'active' : ''; ?>">
                           <?php echo $category['category']; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Daftar Produk -->
        <div class="products">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <div class="product">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p><?php echo "Rp " . number_format($product['price'], 0, ',', '.'); ?></p>
                        <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="buy-button">Beli</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada produk dalam kategori ini.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
