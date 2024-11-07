<?php
session_start();
require 'connect.php';
require 'fpdf/fpdf.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

// Memastikan pengguna telah login sebelum melihat laporan
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mendapatkan informasi pengguna
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($user_result);

// Mendapatkan informasi transaksi terakhir pengguna
$transaction_query = "SELECT * FROM transactions WHERE user_id = '$user_id' ORDER BY transaction_date DESC LIMIT 1";
$transaction_result = mysqli_query($conn, $transaction_query);
$transaction = mysqli_fetch_assoc($transaction_result);

// Menghasilkan PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Judul Laporan
$pdf->Cell(0, 10, 'HealthCare Store', 0, 1, 'C');
$pdf->Cell(0, 10, 'Laporan Pembelian', 0, 1, 'C');
$pdf->Ln(10);

// Menampilkan informasi pengguna dengan penempatan manual
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, 'User ID:');
$pdf->Cell(60, 10, $user['id'], 0, 0);
$pdf->Cell(30, 10, 'No HP:');
$pdf->Cell(60, 10, $user['phone_number'], 0, 1);

$pdf->Cell(30, 10, 'Nama:');
$pdf->Cell(60, 10, $user['fullname'], 0, 0);
$pdf->Cell(30, 10, 'Tanggal Transaksi:');
$pdf->SetX(140); // Geser lebih jauh ke kanan untuk mencegah tumpang tindih
$pdf->Cell(60, 10, date('Y-m-d', strtotime($transaction['transaction_date'])), 0, 1);

$pdf->Cell(30, 10, 'Alamat:');
$pdf->Cell(60, 10, $user['address'], 0, 0);
$pdf->Cell(30, 10, 'Cara Bayar:');
$pdf->SetX(140); // Set posisi untuk cara bayar agar rata dengan tanggal transaksi
$pdf->Cell(60, 10, $transaction['payment_method'], 0, 1);
$pdf->Ln(10);

// Menampilkan rincian pembelian produk
$pdf->Cell(0, 10, 'Rincian Pembelian', 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, 'No', 1);
$pdf->Cell(80, 10, 'Nama Produk', 1);
$pdf->Cell(30, 10, 'Jumlah', 1);
$pdf->Cell(30, 10, 'Harga', 1);
$pdf->Cell(40, 10, 'Total', 1); 
$pdf->Ln();

// Mengambil data produk berdasarkan transaksi
$total = 0;
$transaction_id = $transaction['id'];

// Query untuk mendapatkan rincian produk dari transaksi ini
$order_items_query = "SELECT products.name, order_items.quantity, products.price 
                      FROM order_items 
                      JOIN products ON order_items.product_id = products.id 
                      WHERE order_items.transaction_id = '$transaction_id'";

$order_items_result = mysqli_query($conn, $order_items_query);

if (mysqli_num_rows($order_items_result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($order_items_result)) {
        $quantity = $row['quantity'];
        $price = $row['price'];
        $line_total = $price * $quantity;

        // Menampilkan produk dalam PDF
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(10, 10, $no++, 1);
        $pdf->Cell(80, 10, $row['name'], 1);
        $pdf->Cell(30, 10, $quantity, 1);
        $pdf->Cell(30, 10, 'Rp ' . number_format($price, 0, ',', '.'), 1);
        $pdf->Cell(40, 10, 'Rp ' . number_format($line_total, 0, ',', '.'), 1);
        $pdf->Ln();

        $total += $line_total;
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada produk yang ditemukan dalam transaksi ini.', 1, 1);
}

// Menampilkan total belanja di bawah rincian tabel
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total Belanja: Rp ' . number_format($total, 0, ',', '.'), 0, 1, 'R');

// Menambahkan watermark
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetXY(10, 200); // Sesuaikan posisi watermark
$pdf->Cell(0, 10, 'HealthCare Store - Tanda Tangan', 0, 1, 'C');

// Output PDF ke file
$pdf_output = 'report.pdf';
$pdf->Output($pdf_output, 'F'); // Simpan ke file

// Mengirim email dengan lampiran menggunakan PHPMailer
$mail = new PHPMailer(true);
try {
    // Pengaturan server
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Ganti dengan server SMTP Anda
    $mail->SMTPAuth = true;
    $mail->Username = 'zerenitymods01@gmail.com'; // Ganti dengan email Anda
    $mail->Password = 'rxep vfsp ecew bmie'; // Ganti dengan password email Anda
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Gunakan TLS atau SSL
    $mail->Port = 465; // Port untuk SSL

    // Pengaturan email
    $mail->setFrom('no-reply@healthcare-store.com', 'HealthCare Store');
    $mail->addAddress($user['email']); // Alamat email penerima
    $mail->Subject = 'Laporan Pembelian Anda';
    $mail->Body = 'Terima kasih telah berbelanja di HealthCare Store. Silakan lihat lampiran untuk laporan pembelian Anda.';
    $mail->addAttachment($pdf_output); // Menambahkan lampiran PDF

    // Mengirim email
    $mail->send();
    echo "<h1>Laporan telah dikirim ke email Anda!</h1>";

    // Redirect ke logout.php setelah email dikirim
    header("Location: logout.php");
    exit; // Pastikan untuk menghentikan eksekusi skrip setelah pengalihan
} catch (Exception $e) {
    echo "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
}

// Hapus file PDF setelah dikirim
unlink($pdf_output);
?>
