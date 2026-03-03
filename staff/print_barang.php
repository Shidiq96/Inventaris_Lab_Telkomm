<?php
session_start();
if (!isset($_SESSION['user'])) {
    die('Akses Ditolak. Silakan Login.');
}

// Koneksi Database
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: Database tidak ditemukan");
}

if (!isset($conn)) {
    die("Error Koneksi Database");
}

// Ambil Parameter
 $nama_barang = isset($_GET['nama']) ? $_GET['nama'] : '';
 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Validasi
if (empty($nama_barang)) {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit;
}

// --- LOGIKA PAGINATION ---
 $limit = 100; // Maksimal 30 barang per halaman
 $offset = ($page - 1) * $limit;

// Hitung Total Data
 $total_query = $conn->query("SELECT COUNT(*) as total FROM barang WHERE nama_barang='$nama_barang'");
 $total_data = $total_query->fetch_assoc()['total'];
 $total_pages = ceil($total_data / $limit);

// Ambil Data dengan Limit
 $stmt = $conn->prepare("SELECT * FROM barang WHERE nama_barang = ? ORDER BY nomor_unik ASC LIMIT ? OFFSET ?");
 $stmt->bind_param("sii", $nama_barang, $limit, $offset);
 $stmt->execute();
 $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label: <?= htmlspecialchars($nama_barang) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/print.css">
</head>
<body>

<div class="container">
    <div class="header no-print">
        <!-- H2 otomatis tebal karena style .header h2 font-weight: 900 -->
        <h2>Cetak Tabel</h2>
        <p>Barang: <strong><?= htmlspecialchars($nama_barang) ?></strong></p>
    </div>

    <!-- TABEL DAFTAR KODE -->
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="30%">Nama Barang</th>
                <th width="40%">Kode Unik (Nomor Seri)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $no = $offset + 1; // Nomor urut lanjutan dari halaman sebelumnya
                while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <td style="font-weight: bold;"><?= $no++ ?></td>
                <td><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                <!-- Class label-code sudah diatur bold di CSS -->
                <td class="label-code"><?= htmlspecialchars($row['nomor_unik']) ?></td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='4' align='center'><strong>Tidak ada data barang.</strong></td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- KONTROL PAGINATION (Tidak terprint) -->
    <div class="controls no-print">
        <!-- Tombol Kembali -->
        <a href="javascript:closeOrBack()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Tutup / Kembali
        </a>

        <!-- Info Halaman -->
        <div class="page-info">
            Halaman <?= $page ?> dari <?= $total_pages ?> (Total: <?= $total_data ?> Item)
        </div>

        <!-- Tombol Print & Navigasi -->
        <div>
            <?php if($page > 1): ?>
                <a href="?nama=<?= urlencode($nama_barang) ?>&page=<?= $page - 1 ?>" class="btn btn-secondary" style="margin-right: 5px;">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </a>
            <?php endif; ?>

            <button onclick="window.print()" class="btn btn-primary" style="margin-right: 5px;">
                <i class="fas fa-print"></i> Cetak Halaman Ini
            </button>

            <?php if($page < $total_pages): ?>
                <a href="?nama=<?= urlencode($nama_barang) ?>&page=<?= $page + 1 ?>" class="btn btn-primary">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
<script src="../js/print_barang.js"></script>
</html>