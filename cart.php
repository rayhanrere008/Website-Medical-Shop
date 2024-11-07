<?php
session_start();
require 'connect.php';

// Inisialisasi keranjang belanja jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Menangani aksi penambahan produk ke keranjang
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $product_id = $_GET['id'];
    if (!array_key_exists($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$product_id] = 1; // Menginisialisasi jumlah produk
    } else {
        $_SESSION['cart'][$product_id]++; // Menambah jumlah produk
    }
}

// Menangani aksi penghapusan produk dari keranjang
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $product_id = $_GET['id'];
    unset($_SESSION['cart'][$product_id]); // Menghapus produk dari keranjang
}

// Menangani perubahan jumlah produk
if (isset($_POST['update'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$product_id]); // Menghapus produk jika jumlah 0
        } else {
            $_SESSION['cart'][$product_id] = $quantity; // Memperbarui jumlah produk
        }
    }
}

// Menghitung total harga
$total = 0;
$products_in_cart = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']]; // Menambahkan jumlah ke setiap produk
        $products_in_cart[] = $row;
        $total += $row['price'] * $row['quantity']; // Menghitung total
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
    <title>Keranjang Belanja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4; /* Warna latar belakang */
        }
        .container {
            max-width: 1100px; /* Lebar maksimum container */
            width: 150%;
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
        table {
            width: 100%; /* Lebar tabel 100% dari container */
            border-collapse: collapse; /* Menghilangkan jarak antara border */
            margin-bottom: 20px; /* Jarak di bawah tabel */
        }
        th, td {
            border: 1px solid #ccc; /* Border untuk sel */
            padding: 10px; /* Padding di dalam sel */
            text-align: center; /* Memusatkan teks di dalam sel */
        }
        th {
            background-color: #f8f8f8; /* Warna latar belakang untuk header tabel */
        }
        .remove-button {
            color: white; /* Warna teks tombol hapus menjadi putih */
            background-color: red; /* Warna latar belakang tombol hapus */
            padding: 5px 10px; /* Padding tombol hapus */
            border-radius: 5px; /* Sudut melengkung untuk tombol hapus */
            text-decoration: none; /* Menghilangkan garis bawah */
        }
        .remove-button:hover {
            opacity: 0.8; /* Efek saat hover */
        }
        button {
            padding: 10px 15px; /* Padding di dalam tombol */
            background-color: #007bff; /* Warna latar belakang tombol */
            color: white; /* Warna teks tombol */
            border: none; /* Menghilangkan border default */
            border-radius: 5px; /* Sudut melengkung untuk tombol */
            cursor: pointer; /* Menampilkan pointer saat hover */
            transition: background-color 0.3s; /* Transisi untuk efek hover */
            margin: 5px 0; /* Margin atas dan bawah tombol */
        }
        .checkout-button {
            background-color: green; /* Warna tombol lanjutkan ke pembayaran menjadi hijau */
        }
        .checkout-button:hover {
            background-color: darkgreen; /* Warna tombol saat hover */
        }
        .back-button {
            display: inline-block; /* Mengatur tombol kembali */
            padding: 10px 15px; /* Padding di dalam tombol */
            background-color: #6c757d; /* Warna latar belakang tombol kembali */
            color: white; /* Warna teks tombol kembali */
            text-decoration: none; /* Menghilangkan garis bawah */
            border-radius: 5px; /* Sudut melengkung untuk tombol kembali */
            transition: background-color 0.3s; /* Transisi untuk efek hover */
        }
        .back-button:hover {
            background-color: #5a6268; /* Warna tombol kembali saat hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Keranjang Belanja</h1>
        <form action="cart.php" method="post">
            <table>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($products_in_cart as $product): ?>
                <tr>
                    <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 50px;"></td>
                    <td><?php echo $product['name']; ?></td>
                    <td>
                        <input type="number" name="quantities[<?php echo $product['id']; ?>]" value="<?php echo $product['quantity']; ?>" min="0" style="width: 60px; text-align: center;">
                    </td>
                    <td><?php echo "Rp " . number_format($product['price'], 0, ',', '.'); ?></td>
                    <td><?php echo "Rp " . number_format($product['price'] * $product['quantity'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="cart.php?action=remove&id=<?php echo $product['id']; ?>" class="remove-button">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <h2>Total Belanja: Rp <?php echo number_format($total, 0, ',', '.'); ?></h2>
            <button type="submit" name="update">Perbarui Keranjang</button>
        </form>

        <!-- Form untuk melanjutkan ke checkout -->
        <form action="checkout.php" method="post" style="margin-top: 10px;">
            <button type="submit" class="checkout-button" <?php if(empty($products_in_cart)) echo 'disabled'; ?>>Lanjutkan ke Pembayaran</button>
        </form>

        <!-- Link untuk kembali ke halaman produk -->
        <a href="products.php" class="back-button">Kembali ke Halaman Produk</a>
    </div>
</body>
</html>
