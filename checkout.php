<?php
session_start();
require 'connect.php';

// Memastikan pengguna telah login sebelum melanjutkan ke pembayaran
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Menghitung total harga dan menyiapkan produk untuk ditampilkan
$total = 0;
$products_in_cart = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart'])); // Mengambil ID produk di keranjang
    $query = "SELECT * FROM products WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']]; // Menambahkan jumlah ke setiap produk
        $products_in_cart[] = $row;
        $total += $row['price'] * $row['quantity']; // Menghitung total berdasarkan jumlah
    }
} else {
    // Jika keranjang kosong, redirect ke cart
    header('Location: cart.php');
    exit;
}

// Menangani pembayaran jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    // Proses pembayaran
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];
    $bank_name = isset($_POST['bank_name']) ? $_POST['bank_name'] : ''; // Ambil nama bank jika ada

    // Tentukan status berdasarkan metode pembayaran
    $status = ($payment_method === 'Prepaid') ? 'completed' : 'pending';

    // Simpan transaksi di tabel transactions
    $query = "INSERT INTO transactions (user_id, total_amount, payment_method, transaction_date, status, bank_name) 
              VALUES ('$user_id', '$total', '$payment_method', NOW(), '$status', '$bank_name')";
    
    if (mysqli_query($conn, $query)) {
        // Ambil ID transaksi yang baru saja dimasukkan
        $transaction_id = mysqli_insert_id($conn);

        // Simpan detail item-item yang dibeli ke dalam tabel order_items
        foreach ($products_in_cart as $product) {
            $product_id = $product['id'];
            $quantity = $product['quantity'];
            $price = $product['price'];

            $order_items_query = "INSERT INTO order_items (transaction_id, product_id, quantity, price)
                                  VALUES ('$transaction_id', '$product_id', '$quantity', '$price')";
            mysqli_query($conn, $order_items_query);
        }

        // Update stok produk di tabel products
        foreach ($products_in_cart as $product) {
            $product_id = $product['id'];
            $quantity = $product['quantity'];

            // Kurangi stok produk
            $update_query = "UPDATE products SET stock = stock - $quantity WHERE id = '$product_id'";
            mysqli_query($conn, $update_query);
        }

        $_SESSION['cart'] = []; // Kosongkan keranjang belanja
        header('Location: report.php'); // Arahkan ke report setelah transaksi berhasil
        exit;
    } else {
        $error = "Pembayaran gagal: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/logo.png" type="image/png">    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <script>
        // Fungsi untuk menampilkan atau menyembunyikan input nama bank
        function toggleBankInput() {
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            const bankInputContainer = document.getElementById('bankInputContainer');
            bankInputContainer.style.display = (paymentMethod === 'Prepaid') ? 'block' : 'none';
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px; /* Ganti ini sesuai kebutuhan Anda */
            margin: 0 auto; /* Untuk memusatkan konten */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
        }

        table {
            width: 100%; /* Memastikan tabel memenuhi lebar kontainer */
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            display: block;
            width: 50%; /* Ukuran tombol dikurangi */
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px auto; /* Margin di atas dan bawah untuk tombol */
        }

        button:hover {
            background-color: darkgreen; /* Mengubah warna saat hover */
        }

        label {
            display: block; /* Membuat label menjadi block element */
            margin-bottom: 10px; /* Jarak di bawah label */
        }

        .bank-input {
            margin-top: 10px; /* Jarak di atas input nama bank */
        }

        p {
            color: red; /* Warna teks untuk pesan kesalahan */
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pembayaran</h1>
        <table>
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
            </tr>
            <?php foreach ($products_in_cart as $product): ?>
            <tr>
                <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="max-width: 50px;"></td>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['quantity']; ?></td> <!-- Menampilkan jumlah produk -->
                <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <h2>Total Belanja: Rp <?php echo number_format($total, 0, ',', '.'); ?></h2>
        <form method="POST" action="">
            <label for="payment_method">Metode Pembayaran:</label>
            <select name="payment_method" onchange="toggleBankInput()" required>
                <option value="Prepaid">Kartu Debit/Kredit</option>
                <option value="Postpaid">Bayar di Tempat</option>
            </select>

            <div id="bankInputContainer" class="bank-input" style="display:none;">
                <label for="bank_name">Nama Bank:</label>
                <input type="text" name="bank_name" placeholder="Masukkan nama bank (jika ada)">
            </div>

            <button type="submit">Bayar</button>
        </form>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </div>
</body>
</html>
