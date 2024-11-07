<?php
include 'connect.php';
session_start();

// Cek apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Ambil data produk dan customer (contoh query)
$customers = $conn->query("SELECT * FROM users WHERE role = 'customer'");
$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="images/logo.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles tambahan untuk navbar dan konten */
        body {
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: white;
            padding: 20px;
            height: 100%;
            position: fixed;
            width: 250px;
            color: black;
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
        }

        .navbar ul li {
            margin-bottom: 10px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: black;
            padding: 10px;
            display: block;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .navbar ul li a:hover {
            background-color: red;
        }

        .content {
            margin-left: 270px; /* Sesuaikan dengan lebar navbar */
            padding: 20px;
        }

        .dropdown {
            display: none; /* Tersembunyi secara default */
            margin-left: 20px; /* Margin untuk dropdown */
            padding-left: 10px;
            background-color: #fff; /* Warna latar belakang dropdown */
            border-radius: 4px;
            border: 1px solid #ccc; /* Batas untuk dropdown */
        }

        .dropdown a {
            color: black; /* Warna link dropdown */
        }

        .logout {
            text-align: center;
            margin-top: auto; /* Mengisi ruang yang tersisa */
            padding: 20px 0; /* Spasi atas dan bawah */
        }

        .logout a {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout a:hover {
            background-color: darkred;
        }

        form {
            display: flex;
            flex-direction: column; /* Mengatur form agar elemen ditampilkan secara vertikal */
            gap: 10px; /* Jarak antara elemen */
            max-width: 400px; /* Lebar maksimum form */
            margin-left: 20px; /* Jarak dari navbar */
            margin-top: 20px; /* Jarak atas untuk memberi ruang dari header */
        }

        label {
            margin-bottom: 5px; /* Jarak antara label dan input */
        }

        input, select {
            padding: 10px; /* Spasi dalam input */
            border: 1px solid #ccc; /* Border input */
            border-radius: 4px; /* Sudut melengkung */
            font-size: 16px; /* Ukuran font */
        }

        .content h1 {
            text-align: center; /* Memusatkan teks */
            margin: 20px 0; /* Jarak atas dan bawah untuk memberi ruang */
        }

        .content h2 {
            margin-left: 20px; /* Jarak dari navbar untuk judul */
        }

        .content table {
            margin-left: 20px; /* Jarak dari navbar untuk tabel */
            border-collapse: collapse; /* Menghapus jarak antara sel tabel */
            width: 90%; /* Lebar tabel sesuai dengan konten */
        }

        .content table th, .content table td {
            border: 1px solid #ccc; /* Batas untuk sel tabel */
            padding: 8px; /* Spasi dalam sel tabel */
            text-align: left; /* Pemusatan teks ke kiri */
        }

        .success-message {
            background-color: #d4edda; /* Warna latar belakang hijau muda */
            color: #155724; /* Warna teks hijau gelap */
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb; /* Batas hijau */
            border-radius: 5px; /* Sudut melengkung */
            text-align: center; /* Teks rata tengah */
        }

        .button {
        background-color: green; /* Warna latar belakang hijau */
        color: white; /* Warna teks putih */
        padding: 10px 20px; /* Spasi dalam tombol */
        border: none; /* Hapus border default */
        border-radius: 5px; /* Sudut melengkung */
        cursor: pointer; /* Ganti kursor saat hover */
        transition: background-color 0.3s; /* Transisi warna saat hover */
        }

        .button:hover {
            background-color: darkgreen; /* Warna saat hover */
        }

    </style>
</head>
<body>
    <div class="navbar">
        <img src="images/logo.png" alt="Logo Toko Alat Kesehatan" style="display: block; margin: 0 auto; width: 100px; height: auto;"> <!-- Ganti dengan nama file logo Anda -->
        <h2 style="text-align: center;">HealthCare Store</h2>
        <ul>
            <li>
                <a href="#" onclick="toggleDropdown('customer-dropdown')">Manage Data Customer</a>
                <div class="dropdown" id="customer-dropdown">
                    <a href="#" onclick="showContent('customer-view')">View Customers</a>
                    <a href="#" onclick="showContent('customer-add')">Add Customer</a>
                    <a href="#" onclick="showContent('customer-update')">Update Customer</a>
                    <a href="#" onclick="showContent('customer-remove')">Remove Customer</a>
                </div>
            </li>
            <li>
                <a href="#" onclick="toggleDropdown('product-dropdown')">Manage Data Produk</a>
                <div class="dropdown" id="product-dropdown">
                    <a href="#" onclick="showContent('product-add')">Add Product</a>
                    <a href="#" onclick="showContent('product-update')">Update Product</a>
                    <a href="#" onclick="showContent('product-remove')">Remove Product</a>
                    <a href="#" onclick="showContent('product-view')">View Products</a>
                </div>
            </li>
        </ul>
        <div class="logout">
            <a href="logout_admin.php">Logout</a>
        </div>
    </div>

    <div class="content" id="content">
        <h1>Welcome to Admin Dashboard</h1>

        <?php
        if (isset($_GET['status'])) {
            echo '<div class="success-message">' . htmlspecialchars($_GET['status']) . '</div>';
        }
        ?>
    </div>

    <script>
        let lastSection = ''; // Variabel untuk menyimpan section terakhir yang diklik

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        function showContent(section) {
            let contentDiv = document.getElementById('content');

            // Jika section yang sama diklik dua kali, kembalikan ke tampilan awal
            if (lastSection === section) {
                contentDiv.innerHTML = "<h1>Welcome to Admin Dashboard</h1>";
                lastSection = ''; // Reset section terakhir
                return; // Keluar dari fungsi jika section yang sama diklik dua kali
            }

            contentDiv.innerHTML = ""; // Kosongkan konten sebelum mengisi yang baru
            if (section === 'customer-view') {
                contentDiv.innerHTML = `
                    <h2 style="margin-left: 20px;">Data Customers</h2>
                    <table style="margin-left: 20px;"> <!-- Menambahkan margin untuk memberi ruang -->
                        <tr>
                            <th>No.</th> <!-- Tambahkan kolom No. -->
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Phone Number</th>
                            <th>PayPal ID</th>
                        </tr>
                        <?php 
                        $no = 1; // Inisialisasi nomor urut
                        while($row = $customers->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td> <!-- Tampilkan nomor urut -->
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['fullname']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['birthdate']; ?></td> <!-- Tambahkan kolom birthdate -->
                                <td><?php echo $row['gender']; ?></td> <!-- Tambahkan kolom gender -->
                                <td><?php echo $row['address']; ?></td> <!-- Tambahkan kolom address -->
                                <td><?php echo $row['city']; ?></td> <!-- Tambahkan kolom city -->
                                <td><?php echo $row['phone_number']; ?></td> <!-- Tambahkan kolom phone_number -->
                                <td><?php echo $row['paypal_id']; ?></td> <!-- Tambahkan kolom paypal_id -->
                            </tr>
                        <?php } ?>
                    </table>
                `;
            } else if (section === 'customer-add') {
                contentDiv.innerHTML = `
                    <h2>Add Customer</h2>
                    <form action="add_customer.php" method="post">
                        <label for="username">Username:</label>
                        <input type="text" name="username" placeholder="Username" required>
                        <label for="password">Password:</label>
                        <input type="password" name="password" placeholder="Password" required>
                        <label for="fullname">Full Name:</label>
                        <input type="text" name="fullname" placeholder="Full Name" required>
                        <label for="email">Email:</label>
                        <input type="email" name="email" placeholder="Email" required>
                        <label for="birthdate">Birthdate:</label>
                        <input type="date" name="birthdate" required>
                        <label for="gender">Gender:</label>
                        <select name="gender" required>
                            <option value="Pria">Pria</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        <label for="address">Address:</label>
                        <input type="text" name="address" placeholder="Address" required>
                        <label for="city">City:</label>
                        <input type="text" name="city" placeholder="City" required>
                        <label for="phone_number">Phone Number:</label>
                        <input type="text" name="phone_number" placeholder="Phone Number" required>
                        <label for="paypal_id">PayPal ID:</label>
                        <input type="text" name="paypal_id" placeholder="PayPal ID">
                        <button type="submit" class="button">Add Customer</button>
                    </form>
                `;
            } else if (section === 'customer-update') {
                contentDiv.innerHTML = `
                    <h2>Update Customer</h2>
                    <form action="update_customer.php" method="post">
                        <label for="customer_id">Customer ID:</label>
                        <input type="number" name="customer_id" placeholder="Customer ID" required>
                        <label for="fullname">New Full Name:</label>
                        <input type="text" name="fullname" placeholder="New Full Name">
                        <label for="email">New Email:</label>
                        <input type="email" name="email" placeholder="New Email">
                        <label for="address">New Address:</label>
                        <input type="text" name="address" placeholder="New Address">
                        <label for="city">New City:</label>
                        <input type="text" name="city" placeholder="New City">
                        <label for="phone_number">New Phone Number:</label>
                        <input type="text" name="phone_number" placeholder="New Phone Number">
                        <button type="submit" class="button">Update Customer</button>
                    </form>
                `;
            } else if (section === 'customer-remove') {
                contentDiv.innerHTML = `
                    <h2>Remove Customer</h2>
                    <form action="remove_customer.php" method="post">
                        <select name="customer_id" required>
                            <?php 
                            // Ambil ulang data customers dari database
                            $customers_for_remove = $conn->query("SELECT * FROM users WHERE role = 'customer'");
                            while($row = $customers_for_remove->fetch_assoc()) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['fullname']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" class="button">Remove Customer</button>
                    </form>
                `;
            } else if (section === 'product-add') {
                contentDiv.innerHTML = `
                    <h2>Add Product</h2>
                    <form action="add_product.php" method="post">
                        <input type="text" name="name" placeholder="Product Name" required>
                        <textarea name="description" placeholder="Description" required></textarea>
                        <select name="category" required>
                            <option value="Alat Kesehatan">Alat Kesehatan</option>
                            <option value="Alat Medis">Alat Medis</option>
                        </select>
                        <input type="number" name="price" placeholder="Price" required>
                        <input type="number" name="stock" placeholder="Stock" required>
                        <input type="text" name="image" placeholder="Image URL" required>
                        <button type="submit" class="button">Add Product</button>
                    </form>
                `;
            } else if (section === 'product-remove') {
                contentDiv.innerHTML = `
                    <h2>Remove Product</h2>
                    <form action="remove_product.php" method="post">
                        <select name="product_id" required>
                            <?php while($row = $products->fetch_assoc()) { ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" class="button">Remove Product</button>
                    </form>
                `;
            } else if (section === 'product-update') {
                // Mengambil data produk dari database
                fetch('get_products.php')
                    .then(response => response.json())
                    .then(products => {
                        // Membuat isi dropdown produk
                        let options = '';
                        products.forEach(product => {
                            options += `<option value="${product.id}">${product.name}</option>`;
                        });

                        // Menampilkan form update produk
                        contentDiv.innerHTML = `
                            <h2>Update Product</h2>
                            <form action="update_product.php" method="post">
                                <select name="product_id" required>
                                    <option value="">Select a Product</option>
                                    ${options}
                                </select>
                                <input type="number" name="new_price" placeholder="New Price">
                                <input type="number" name="new_stock" placeholder="New Stock">
                                <button type="submit" class="button">Update Product</button>
                            </form>
                        `;
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        contentDiv.innerHTML = `<p>Error loading products.</p>`;
                    });
            } else if (section === 'product-view') {
                // Mengambil data produk dari database
                fetch('get_products.php')
                    .then(response => response.json())
                    .then(products => {
                        // Membuat isi tabel
                        let tableRows = '';
                        products.forEach(product => {
                            tableRows += `
                                <tr>
                                    <td>${product.id}</td>
                                    <td>${product.name}</td>
                                    <td>${product.description}</td>
                                    <td>${product.price}</td>
                                    <td>${product.stock}</td>
                                    <td><img src="${product.image}" alt="${product.name}" style="width: 100px; height: auto;"></td>
                                </tr>
                            `;
                        });

                        // Mengisi tabel dengan data produk
                        contentDiv.innerHTML = `
                            <h2>Data Products</h2>
                            <table>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Image</th>
                                </tr>
                                ${tableRows}
                            </table>
                        `;
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        contentDiv.innerHTML = `<p>Error loading products.</p>`;
                    });
        }

        // Simpan section yang terakhir diklik
        lastSection = section;
    }
    </script>
</body>
</html>
