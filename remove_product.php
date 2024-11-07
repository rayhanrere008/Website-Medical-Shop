<?php
// remove_product.php
include 'connect.php'; // Pastikan untuk menyertakan koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Menghapus produk berdasarkan ID
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Jika produk berhasil dihapus, redirect ke admin_dashboard dengan pesan sukses
        header("Location: admin_dashboard.php?status=Product removed successfully.");
        exit();
    } else {
        echo "Error removing product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
