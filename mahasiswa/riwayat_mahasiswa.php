<?php
session_start();

// --- PENTING: PAKSA TIMEZONE WIB ---
date_default_timezone_set('Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');
putenv("TZ=Asia/Jakarta");

// 2. Koneksi Database
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: File database.php tidak ditemukan.");
}

if (!isset($conn)) {
    die("Error: Koneksi Database Gagal");
}

$user = $_SESSION['user'];

// --- Normalisasi Data Session ---
$display_kelas = $user['nama_kelas'] ?? $user['kelas'] ?? 'Kelas Tidak Diketahui';
$display_matkul = $user['nama_matkul'] ?? $user['mata_kuliah'] ?? 'Matkul Tidak Diketahui';

// Ambil ajaran_semester dari settings
$qSet = $conn->query("SELECT ajaran_semester FROM app_settings WHERE status_sistem='aktif' LIMIT 1");
$ajaran_semester = '2023/2024 Ganjil';
if($qSet && $qSet->num_rows > 0) {
    $setting = $qSet->fetch_assoc();
    $ajaran_semester = $setting['ajaran_semester'];
}

// 3. Query Riwayat Peminjaman (termasuk yang sedang dipinjam)
$kelas_id = $user['kelas_id'];
$matkul_id = $user['matkul_id'];
$query = "SELECT p.*, b.nama_barang, b.nomor_unik 
          FROM peminjaman p 
          JOIN barang b ON p.barang_id = b.id 
          WHERE p.kelas_id = '$kelas_id' 
            AND p.matkul_id = '$matkul_id' 
            AND p.ajaran_semester = '$ajaran_semester'
          ORDER BY 
            CASE WHEN p.status_peminjaman = 'dipinjam' THEN 0 ELSE 1 END, 
            p.id DESC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/mahasiswa/riwayat.css">
</head>
<body>

<div class="navbar">
    <h3><i class="fas fa-flask"></i> LabInventory</h3>
    <div>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali Dashboard
        </a>
    </div>
</div>

<div class="container">
    
    <!-- Informasi Peminjam -->
    <div class="card">
        <h2>Riwayat Peminjaman</h2>
        <div class="info-peminjam">
            <i class="fas fa-user-graduate"></i> <strong><?= htmlspecialchars($display_kelas) ?></strong> &bull; 
            <i class="fas fa-book"></i> <strong><?= htmlspecialchars($display_matkul) ?></strong>
        </div>

        <!-- Tabel Riwayat -->
        <table>
            <thead>
                <tr>
                    <th width="20%">Nama Barang</th>
                    <th width="15%">Kode Unik</th>
                    <th width="15%">Tanggal Pinjam</th>
                    <th width="15%">Waktu Pinjam</th>
                    <!-- <th width="15%">Waktu Kembali</th> -->
                    <th width="20%">Nama Mahasiswa</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = strtolower($row['status_peminjaman']);
                        $waktu_pinjam_db = $row['waktu_pinjam'];
                        $tanggal_pinjam_db = $row['tanggal_pinjam'] ?? '';
                        $display_tanggal_pinjam = $tanggal_pinjam_db ? date('d-m-Y', strtotime($tanggal_pinjam_db)) : '-';
                        $display_waktu_pinjam = $waktu_pinjam_db ? date('H:i', strtotime($waktu_pinjam_db)) : '-';
                        // $waktu_kembali_db = $row['waktu_kembali'] ?? '';
                        // $display_waktu_kembali = ($waktu_kembali_db && $waktu_kembali_db != '0000-00-00 00:00:00' && $waktu_kembali_db != '00:00:00')
                        //     ? date('H:i', strtotime($waktu_kembali_db))
                        //     : '-';
                        $nama_mahasiswa = $row['nama_mahasiswa'] ?? '-';
                        if ($status == 'dipinjam') {
                            $badge = '<span class="badge badge-process"><i class="fas fa-clock"></i> Sedang Dipinjam</span>';
                        } else {
                            $badge = '<span class="badge badge-success"><i class="fas fa-check"></i> Dikembalikan</span>';
                        }
                ?>
                <tr <?php if($status == 'dipinjam') echo 'style="background:#fffbe6"'; ?>>
                    <td><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                    <td style="font-family: monospace; color: #555;"><?= htmlspecialchars($row['nomor_unik']) ?></td>
                    <td><?= $display_tanggal_pinjam ?></td>
                    <td>
                        <span class="time-display"><?= $display_waktu_pinjam ?></span> WIB
                    </td>
                    <!--
                    <td>
                        <span class="time-display"><?= /* $display_waktu_kembali */ "" ?></span> WIB
                    </td>
                    -->
                    <td><?= htmlspecialchars($nama_mahasiswa) ?></td>
                    <td><?= $badge ?></td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr>
                            <td colspan='6' class='empty-state'>
                                <i class='fas fa-history'></i>
                                <p>Anda belum memiliki riwayat peminjaman.</p>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
        
    </div>

</div>

</body>
</html>