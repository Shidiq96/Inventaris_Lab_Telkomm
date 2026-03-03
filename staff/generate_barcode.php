<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$kode = isset($_GET['kode']) ? trim($_GET['kode']) : '';

if ($kode === '') {
    http_response_code(400);
    echo 'Kode tidak ditemukan';
    exit;
}

try {
    // Buat object QR
    $qrCode = new QrCode(
        data: $kode,
        size: 300,
        margin: 10
    );

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();

} catch (Throwable $e) {
    http_response_code(500);
    echo 'Gagal membuat QR: ' . $e->getMessage();
}
exit;