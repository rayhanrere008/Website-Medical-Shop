<?php
session_start();
require 'connect.php';

$error = '';
$success = '';
$validations = array_fill_keys(['username', 'password', 'fullname', 'email', 'birthdate', 'gender', 'address', 'city', 'phone_number', 'paypal_id'], '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone_number = $_POST['phone_number'];
    $paypal_id = $_POST['paypal_id'];

    // Validasi form
    if (empty($username)) {
        $validations['username'] = "User ID tidak boleh kosong!";
    }
    if (empty($password)) {
        $validations['password'] = "Kata Sandi tidak boleh kosong!";
    }
    if (empty($fullname)) {
        $validations['fullname'] = "Nama Lengkap tidak boleh kosong!";
    }
    if (empty($email)) {
        $validations['email'] = "E-mail tidak boleh kosong!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validations['email'] = "Format email tidak valid!";
    }
    if (empty($birthdate)) {
        $validations['birthdate'] = "Tanggal Lahir tidak boleh kosong!";
    }
    if (empty($gender)) {
        $validations['gender'] = "Jenis Kelamin tidak boleh kosong!";
    }
    if (empty($address)) {
        $validations['address'] = "Alamat tidak boleh kosong!";
    }
    if (empty($city)) {
        $validations['city'] = "Kota tidak boleh kosong!";
    }
    if (empty($phone_number)) {
        $validations['phone_number'] = "Nomor Kontak tidak boleh kosong!";
    } elseif (!preg_match('/^[0-9]+$/', $phone_number)) {
        $validations['phone_number'] = "Nomor kontak harus berupa angka!";
    }
    if (empty($paypal_id)) {
        $validations['paypal_id'] = "ID Paypal tidak boleh kosong!";
    }

    // Jika tidak ada error, simpan pengguna baru ke database
    if (!array_filter($validations)) {
        $query = "INSERT INTO users (username, password, fullname, email, birthdate, gender, address, city, phone_number, paypal_id) VALUES ('$username', '$password', '$fullname', '$email', '$birthdate', '$gender', '$address', '$city', '$phone_number', '$paypal_id')";
        
        if (mysqli_query($conn, $query)) {
            // Redirect ke login.php setelah pendaftaran berhasil
            header('Location: login.php');
            exit;
        } else {
            $error = "Pendaftaran gagal: " . mysqli_error($conn);
        }
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
    <title>Registrasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4; /* Warna latar belakang */
        }
        h1 {
            text-align: center;
            margin-bottom: 20px; /* Jarak di bawah judul */
        }
        form {
            display: flex;
            flex-direction: column;
            width: 100%; /* Lebar form 100% */
            max-width: 1150px; /* Lebar maksimum form */
            margin: 0 auto; /* Memusatkan form dalam container */
        }
        label {
            margin: 10px 0 5px; /* Jarak antar label dan input */
        }
        input, select {
            padding: 10px;
            margin-bottom: 10px; /* Jarak antara input */
            border: 1px solid #ccc; /* Border */
            border-radius: 4px; /* Sudut melengkung */
            width: 100%; /* Memenuhi lebar penuh */
        }
        .button-container {
            display: flex;
            justify-content: center; /* Pusatkan tombol */
            gap: 10px; /* Ruang antar tombol */
        }
        button {
            padding: 10px 15px;
            border: none; /* Menghilangkan border default */
            border-radius: 4px; /* Sudut melengkung */
            cursor: pointer; /* Menampilkan pointer saat hover */
        }
        button[type="submit"] {
            background-color: #5cb85c; /* Warna hijau */
            color: white; /* Teks berwarna putih */
        }
        button[type="button"] {
            background-color: #007bff; /* Warna biru */
            color: white; /* Teks berwarna putih */
        }
        .validation-error {
            color: red; /* Warna merah untuk pesan error */
            font-size: 14px; /* Ukuran font pesan error */
            margin: -5px 0 10px; /* Jarak antar pesan error */
        }
    </style>
</head>
<body>
    <h1>REGISTER ACCOUNT</h1>
    <form method="POST" action="">
        <label for="username">User ID:</label>
        <input type="text" name="username" placeholder="Masukkan User ID" value="<?php echo isset($username) ? $username : ''; ?>">
        <?php if (!empty($validations['username'])) echo "<p class='validation-error'>{$validations['username']}</p>"; ?>
        
        <label for="password">Kata Sandi:</label>
        <input type="password" name="password" placeholder="Masukkan Kata Sandi">
        <?php if (!empty($validations['password'])) echo "<p class='validation-error'>{$validations['password']}</p>"; ?>
        
        <label for="fullname">Nama Lengkap:</label>
        <input type="text" name="fullname" placeholder="Masukkan Nama Lengkap" value="<?php echo isset($fullname) ? $fullname : ''; ?>">
        <?php if (!empty($validations['fullname'])) echo "<p class='validation-error'>{$validations['fullname']}</p>"; ?>
        
        <label for="email">E-mail:</label>
        <input type="email" name="email" placeholder="Masukkan E-mail" value="<?php echo isset($email) ? $email : ''; ?>">
        <?php if (!empty($validations['email'])) echo "<p class='validation-error'>{$validations['email']}</p>"; ?>
        
        <label for="birthdate">Tanggal Lahir:</label>
        <input type="date" name="birthdate" value="<?php echo isset($birthdate) ? $birthdate : ''; ?>">
        <?php if (!empty($validations['birthdate'])) echo "<p class='validation-error'>{$validations['birthdate']}</p>"; ?>
        
        <label for="gender">Jenis Kelamin:</label>
        <select name="gender">
            <option value="">Pilih Jenis Kelamin</option>
            <option value="Pria" <?php echo (isset($gender) && $gender === 'Pria') ? 'selected' : ''; ?>>Pria</option>
            <option value="Perempuan" <?php echo (isset($gender) && $gender === 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
        </select>
        <?php if (!empty($validations['gender'])) echo "<p class='validation-error'>{$validations['gender']}</p>"; ?>
        
        <label for="address">Alamat:</label>
        <input type="text" name="address" placeholder="Masukkan Alamat" value="<?php echo isset($address) ? $address : ''; ?>">
        <?php if (!empty($validations['address'])) echo "<p class='validation-error'>{$validations['address']}</p>"; ?>
        
        <label for="city">Kota:</label>
        <input type="text" name="city" placeholder="Masukkan Kota" value="<?php echo isset($city) ? $city : ''; ?>">
        <?php if (!empty($validations['city'])) echo "<p class='validation-error'>{$validations['city']}</p>"; ?>
        
        <label for="phone_number">Nomor Kontak:</label>
        <input type="text" name="phone_number" placeholder="Masukkan Nomor Kontak" value="<?php echo isset($phone_number) ? $phone_number : ''; ?>">
        <?php if (!empty($validations['phone_number'])) echo "<p class='validation-error'>{$validations['phone_number']}</p>"; ?>
        
        <label for="paypal_id">ID Paypal:</label>
        <input type="text" name="paypal_id" placeholder="Masukkan ID Paypal" value="<?php echo isset($paypal_id) ? $paypal_id : ''; ?>">
        <?php if (!empty($validations['paypal_id'])) echo "<p class='validation-error'>{$validations['paypal_id']}</p>"; ?>
        
        <div class="button-container">
            <button type="submit">Daftar</button>
            <button type="button" onclick="clearForm()">Clear</button>
        </div>
    </form>

    <script>
        function clearForm() {
            document.querySelector('form').reset(); // Reset semua field
            // Menghapus semua elemen dengan kelas 'validation-error'
            document.querySelectorAll('.validation-error').forEach(function(element) {
                element.remove();
            });
        }
    </script>
</body>
</html>
