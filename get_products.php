<?php
include 'connect.php';

header('Content-Type: application/json');

// Mengambil data produk dari database
$result = $conn->query("SELECT * FROM products");

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
?>
