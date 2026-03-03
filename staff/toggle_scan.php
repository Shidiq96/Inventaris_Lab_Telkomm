<?php
session_start();
include '../config/database.php';

date_default_timezone_set('Asia/Jakarta');

if (!isset($_GET['kode'])) {
    die("Kode tidak ditemukan");
}

$kode = $conn->real_escape_string($_GET['kode']);
$tgl_kembali = date('Y-m-d H:i:s');

// Cari peminjaman aktif berdasarkan barcode
$q = $conn->query("
    SELECT p.id 
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    WHERE b.nomor_unik = '$kode'
    ORDER BY p.id DESC
    LIMIT 1
");

if ($q && $q->num_rows > 0) {
    $row = $q->fetch_assoc();
    $id = $row['id'];

    $conn->query("
        UPDATE peminjaman SET
            status_peminjaman = IF(status_peminjaman='dipinjam','dikembalikan','dipinjam'),
            tanggal_kembali = IF(status_peminjaman='dipinjam','$tgl_kembali',NULL)
        WHERE id = $id
    ");
}

header("Location: detail.php?data=" . urlencode($_GET['data']));
exit;
?>