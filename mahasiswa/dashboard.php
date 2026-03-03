<?php
session_start();

// 1. Cek Login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header("Location: ../index.php"); 
    exit;
}

// 2. Koneksi Database (Wajib ditambahkan agar bisa ambil data semester & tahun)
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

// 3. Ambil Data Ajaran & Semester Aktif dari Settings Staff
$qSetting = $conn->query("SELECT ajaran_semester FROM app_settings WHERE status_sistem='aktif' LIMIT 1");

// Nilai Default (Fallback) jika database belum diisi atau error
$ajaran_semester = '2023/2024 Ganjil';

if ($qSetting && $qSetting->num_rows > 0) {
    $settings = $qSetting->fetch_assoc();
    $ajaran_semester = $settings['ajaran_semester'];
}
list($tahun_aktif, $semester_aktif) = explode(' ', $ajaran_semester);

// 4. Normalisasi Data Session (Fix Undefined Key)
 $display_kelas = $user['nama_kelas'] ?? $user['kelas'] ?? 'Kelas Tidak Diketahui';
 $display_matkul = $user['nama_matkul'] ?? $user['mata_kuliah'] ?? 'Matkul Tidak Diketahui';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/mahasiswa/dashboard.css">
</head>
<body>

<div class="navbar">
    <h3>LabInventory Mahasiswa</h3>
    <div>
        <span style="margin-right: 15px; font-size: 14px;"><i class="fas fa-user"></i> Mahasiswa</span>
        <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    
    <!-- Kartu Informasi Mahasiswa -->
    <div class="info-card">
        <div class="info-icon"><i class="fas fa-id-card"></i></div>
        <div>
            <h2 style="margin: 0 0 10px 0; color: #333;">Selamat Datang!</h2>
            <p style="margin: 0; color: #555; font-size: 16px;">
                Kelas: <strong><?= htmlspecialchars($display_kelas) ?></strong><br>
                Mata Kuliah: <strong><?= htmlspecialchars($display_matkul) ?></strong>
            </p>
        </div>
    </div>

    <!-- Kartu Ajaran & Semester Aktif -->
    <div class="info-card" style="border-left: 5px solid #6f42c1;">
        <div style="width: 50px; font-size: 24px; color: #6f42c1; margin-right: 15px; text-align: center;">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-size: 13px; color: #555; margin-bottom: 5px;">Ajaran & Semester Aktif</div>
            <span class="badge badge-blue" style="font-size: 14px; padding: 6px 12px;">
                <i class="fas fa-calendar"></i> <?= htmlspecialchars($ajaran_semester) ?>
            </span>
        </div>
    </div>

    <!-- Menu Utama -->
    <h3 style="color: #444; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">Menu Peminjaman</h3>
    <div class="menu-grid">
        
        <!-- Menu Peminjaman Barang -->
        <a href="peminjaman_mahasiswa.php" class="menu-card">
            <div class="menu-icon"><i class="fas fa-box-open"></i></div>
            <div class="menu-title">Pinjam Barang</div>
            <div class="menu-desc">Lihat daftar alat yang tersedia dan lakukan peminjaman untuk praktikum hari ini.</div>
        </a>

        <a href="ruang.php" class="menu-card">
            <div class="menu-icon"><i class="fas fa-door-open"></i></div>
            <div class="menu-title">Ruang</div>
            <div class="menu-desc">Lihat daftar ruang yang tersedia untuk praktikum.</div>
        </a>

        <!-- Menu Riwayat Peminjaman -->
        <a href="riwayat_mahasiswa.php" class="menu-card">
            <div class="menu-icon"><i class="fas fa-history"></i></div>
            <div class="menu-title">Riwayat Saya</div>
            <div class="menu-desc">Lihat status peminjaman yang sedang berlangsung atau riwayat pengembalian alat.</div>
        </a>

    </div>
</div>

</body>
</html>