<?php
// update_product.php
include 'connect.php'; // Pastikan untuk menyertakan koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $new_price = isset($_POST['new_price']) && !empty($_POST['new_price']) ? $_POST['new_price'] : null;
    $new_stock = isset($_POST['new_stock']) && !empty($_POST['new_stock']) ? $_POST['new_stock'] : null;

    // Memastikan minimal salah satu field diisi
    if ($new_price !== null || $new_stock !== null) {
        // Menyusun query secara dinamis tergantung field yang diisi
        $sql = "UPDATE products SET ";
        $params = [];
        $types = "";

        if ($new_price !== null) {
            $sql .= "price = ?, ";
            $params[] = $new_price;
            $types .= "d"; // untuk tipe data decimal (double)
        }

        if ($new_stock !== null) {
            $sql .= "stock = ?, ";
            $params[] = $new_stock;
            $types .= "i"; // untuk tipe data integer
        }

        // Menghapus koma terakhir dari query
        $sql = rtrim($sql, ", ");
        $sql .= " WHERE id = ?";
        $params[] = $product_id;
        $types .= "i"; // tipe data integer untuk id produk

        // Menyiapkan statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        // Eksekusi query dan redirect dengan pesan sukses atau error
        if ($stmt->execute()) {
            // Redirect ke admin_dashboard dengan pesan sukses
            header("Location: admin_dashboard.php?status=Product updated successfully.");
            exit();
        } else {
            echo "Error updating product: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please fill in at least one field (price or stock).";
    }

    $conn->close();
}
?>
