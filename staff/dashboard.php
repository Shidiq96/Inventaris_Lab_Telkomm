<?php
// 1. MULAI SESSION (Wajib agar $_SESSION terbaca)
session_start();

// 2. Cek Akses (Sesuai struktur yang Anda inginkan)
// Kita cek apakah session user ada DAN rolenya sesuai
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'dosen', 'super_admin'])) {
    
    // Jika belum login atau role salah, alihkan ke halaman login (bukan die/dimatikan)
    header("Location: ../login.php");
    exit();
}

// 3. Koneksi Database (Auto Fix Path)
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: File database.php tidak ditemukan. Pastikan file ada di luar folder staff.");
}

// 4. Pastikan Variabel $conn ada
if (!isset($conn)) {
    die("Error: Koneksi Database Gagal (\$conn tidak terdefinisi).");
}

// --- 0. SETUP DATABASE OTOMATIS (UPDATE) ---
// Setup Tabel App Settings
 $check_col = $conn->query("SHOW COLUMNS FROM app_settings LIKE 'ajaran_semester'");
if ($check_col && $check_col->num_rows == 0) {
    // Tambahkan kolom ajaran_semester jika belum ada
    $conn->query("ALTER TABLE app_settings ADD COLUMN ajaran_semester VARCHAR(50) DEFAULT '2023/2024 Ganjil'");
}
// Pastikan kolom id adalah AUTO_INCREMENT
 $check_id = $conn->query("SHOW COLUMNS FROM app_settings LIKE 'id'");
if ($check_id && $check_id->num_rows > 0) {
    $row = $check_id->fetch_assoc();
    if (strpos($row['Extra'], 'auto_increment') === false) {
        // Ubah kolom id menjadi AUTO_INCREMENT jika belum
        $conn->query("ALTER TABLE app_settings MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT PRIMARY KEY");
    }
}

// --- LOGIC UPDATE PENGATURAN AKADEMIK (DROPDOWN PILIHAN) ---
if (isset($_POST['update_pengaturan'])) {
    $ajaran_semester = $conn->real_escape_string($_POST['ajaran_semester']);
    // Set semua semester nonaktif dulu
    $conn->query("UPDATE app_settings SET status_sistem='nonaktif'");
    // Set semester terpilih menjadi aktif
    $conn->query("UPDATE app_settings SET status_sistem='aktif' WHERE ajaran_semester='$ajaran_semester'");
    // Update ajaran_semester pada id=1 agar konsisten dengan query lain
    $conn->query("UPDATE app_settings SET ajaran_semester='$ajaran_semester' WHERE id=1");
    header("Location: dashboard.php"); exit;
}


// --- LOGIC TAMBAH AJARAN & SEMESTER BARU ---
if (isset($_POST['tambah_ajaran_semester'])) {
    $ajaran_semester_baru = $conn->real_escape_string($_POST['ajaran_semester_baru']);
    // Cek duplikasi
    $cek = $conn->query("SELECT COUNT(*) as jml FROM app_settings WHERE ajaran_semester='$ajaran_semester_baru'");
    $ada = ($cek && $cek->fetch_assoc()['jml'] > 0);
    // Set semua semester nonaktif dulu
    $conn->query("UPDATE app_settings SET status_sistem='nonaktif'");
    if (!$ada) {
        // Tambah semester baru dan set aktif
        $conn->query("INSERT INTO app_settings (ajaran_semester, status_sistem) VALUES ('$ajaran_semester_baru', 'aktif')");
        // Update ajaran_semester pada id=1 agar konsisten dengan query lain
        $conn->query("UPDATE app_settings SET ajaran_semester='$ajaran_semester_baru' WHERE id=1");
    } else {
        // Jika sudah ada, set semester tersebut menjadi aktif
        $conn->query("UPDATE app_settings SET status_sistem='aktif' WHERE ajaran_semester='$ajaran_semester_baru'");
        $conn->query("UPDATE app_settings SET ajaran_semester='$ajaran_semester_baru' WHERE id=1");
    }
    header("Location: dashboard.php"); exit;
}

// --- Ambil Data Pengaturan Terbaru ---
// Ambil semester yang status_sistem='aktif'
 $qSetting = $conn->query("SELECT ajaran_semester, status_sistem FROM app_settings WHERE status_sistem='aktif' LIMIT 1");
if ($qSetting && $qSetting->num_rows > 0) {
    $settings = $qSetting->fetch_assoc();
    $ajaran_semester_aktif = $settings['ajaran_semester'];
    $status_sistem = $settings['status_sistem'];
} else {
    $ajaran_semester_aktif = '2023/2024 Ganjil';
    $status_sistem = 'nonaktif';
}

// Ambil semua ajaran_semester unik dari app_settings
 $qAllAjaran = $conn->query("SELECT DISTINCT ajaran_semester FROM app_settings ORDER BY ajaran_semester DESC");
 $daftar_ajaran = [];
if ($qAllAjaran) {
    while($row = $qAllAjaran->fetch_assoc()) {
        $daftar_ajaran[] = $row['ajaran_semester'];
    }
}

// --- NEW: Konversi array PHP ke JSON untuk JavaScript ---
 $json_daftar_ajaran = json_encode($daftar_ajaran);

// --- Helper untuk split tahun dan semester dari ajaran_semester ---
list($tahun_aktif, $semester_aktif) = explode(' ', $ajaran_semester_aktif);


// --- 1. PROSES TAMBAH BARANG ---
if (isset($_POST['simpan_barang'])) {
    $nama = $conn->real_escape_string($_POST['nama_barang']);
    $prefix = $conn->real_escape_string($_POST['nomor_unik']);
    $jumlah = intval($_POST['jumlah']);

    for ($i = 1; $i <= $jumlah; $i++) {
        $kode = $prefix . "-" . str_pad($i, 3, '0', STR_PAD_LEFT);
        $conn->query("INSERT INTO barang (nama_barang, nomor_unik, kondisi) VALUES ('$nama', '$kode', 'Baik')");
    }
    header("Location: dashboard.php"); exit;
}

// --- 2. PROSES HAPUS BARANG (MASSAL) ---
if (isset($_GET['hapus_semua_unit'])) {
    $nama_barang = $conn->real_escape_string($_GET['hapus_semua_unit']);
    $conn->query("DELETE p FROM peminjaman p JOIN barang b ON p.barang_id = b.id WHERE b.nama_barang = '$nama_barang'");
    $conn->query("DELETE FROM barang WHERE nama_barang = '$nama_barang'");
    header("Location: dashboard.php"); exit;
}

// --- 3. PROSES TAMBAH KELAS & MATKUL ---
if (isset($_POST['tambah_kelas'])) {
    $nama = $conn->real_escape_string($_POST['nama_kelas']);
    $conn->query("INSERT INTO kelas (nama_kelas) VALUES ('$nama')");
    header("Location: dashboard.php"); exit;
}
if (isset($_POST['tambah_matkul'])) {
    $nama = $conn->real_escape_string($_POST['nama_matkul']);
    $conn->query("INSERT INTO mata_kuliah (nama_matkul) VALUES ('$nama')");
    header("Location: dashboard.php"); exit;
}

// --- 4. PROSES HAPUS RIWAYAT (Group) ---
if (isset($_GET['hapus_riwayat'])) {
    $nama_barang = $conn->real_escape_string($_GET['hapus_riwayat']);
    $conn->query("DELETE p FROM peminjaman p JOIN barang b ON p.barang_id = b.id WHERE b.nama_barang = '$nama_barang'");
    header("Location: dashboard.php"); exit;
}

// --- 4.5 PROSES HAPUS SEMUA RIWAYAT (MASTER DELETE) ---
if (isset($_GET['hapus_semua_riwayat'])) {
    $sql_hapus_master = "DELETE FROM peminjaman";
    if ($conn->query($sql_hapus_master)) {
        header("Location: dashboard.php"); 
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data riwayat.');</script>";
    }
}

// --- 5. PROSES KEMBALIKAN CEPAT (DARI DASHBOARD) ---
if (isset($_GET['kembalikan_secepat'])) {
    $params = $_GET['kembalikan_secepat'];
    $data = explode("|", $params);
    
    $is_new_format = (count($data) == 5);
    
    // Initialize variables
    $tahun = "";
    $nama_barang = "";
    $semester = "";
    $kelas = "";
    $matkul = "";

    if ($is_new_format) {
        $nama_barang = $conn->real_escape_string($data[0]);
        $tahun = $conn->real_escape_string($data[1]);
        $semester = $conn->real_escape_string($data[2]);
        $kelas = $conn->real_escape_string($data[3]);
        $matkul = $conn->real_escape_string($data[4]);
    } elseif (count($data) == 4) {
        $nama_barang = $conn->real_escape_string($data[0]);
        $semester = $conn->real_escape_string($data[1]);
        $kelas = $conn->real_escape_string($data[2]);
        $matkul = $conn->real_escape_string($data[3]);
        $tahun = "";
    }
    
    // UPDATE: Menggunakan format TIME (H:i:s) karena kolom DB sudah TIME
    $waktu_kembali = date('H:i:s');
    $tahun_condition = ($tahun != "") ? "AND p.tahun_ajaran = '$tahun'" : "";

    // UPDATE: Menggunakan nama kolom waktu_kembali
    $sql_update_all = "UPDATE peminjaman p
                       JOIN barang b ON p.barang_id = b.id
                       JOIN kelas k ON p.kelas_id = k.id
                       JOIN mata_kuliah mk ON p.matkul_id = mk.id
                       SET p.status_peminjaman = 'selesai', p.waktu_kembali = '$waktu_kembali'
                       WHERE b.nama_barang = '$nama_barang' 
                       $tahun_condition
                       AND p.semester = '$semester'
                       AND k.nama_kelas = '$kelas'
                       AND mk.nama_matkul = '$matkul'
                       AND p.status_peminjaman = 'sedang_dipinjam'";
                           
    if ($conn->query($sql_update_all)) {
        header("Location: dashboard.php"); exit;
    }
}

 $search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
 $is_searching = !empty($search_keyword);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/dashboard.css">
    
    <!-- CSS TAMBAHAN UNTUK DROPDOWN -->
    <style>
        .autocomplete-wrapper {
            position: relative;
            flex: 1;
            min-width: 250px;
            /* Memberikan sedikit ruang aman di kanan agar tidak terlalu nempel dengan tombol */
            margin-right: 10px; 
        }

        .custom-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            
            /* 1. LEBIH PANJANG: Tinggi maksimal diperbesar */
            max-height: 400px; 
            
            /* 2. JANGAN KENA BUTTON: Z-Index tinggi agar melayang di atas tombol jika perlu */
            z-index: 9999;
            
            overflow-y: auto;
            
            /* Bayangan agar terlihat melayang di atas elemen lain */
            box-shadow: 0 10px 15px rgba(0,0,0,0.15); 
            
            display: none;
            
            /* Styling scrollbar agar lebih rapi */
            scrollbar-width: thin;
        }
        
        /* Untuk Webkit browser (Chrome/Safari) scrollbar */
        .custom-dropdown::-webkit-scrollbar {
            width: 8px;
        }
        .custom-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .custom-dropdown::-webkit-scrollbar-thumb {
            background: #ccc; 
            border-radius: 4px;
        }
        .custom-dropdown::-webkit-scrollbar-thumb:hover {
            background: #aaa; 
        }

        .custom-dropdown.show {
            display: block;
        }

        .dropdown-item {
            padding: 12px 15px; /* Padding sedikit lebih lega */
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background-color: #f3f0fa; /* Warna hover ungu muda */
            color: #6f42c1;
        }
    </style>
</head>
<body>
<div class="container">
    
    <!-- BANNER PERINGATAN JIKA SISTEM NONAKTIF -->
    <?php if($status_sistem == 'nonaktif'): ?>
    <div class="alert-banner">
        <div>
            <strong><i class="fas fa-exclamation-triangle"></i> MODE NONAKTIF</strong>
            <p style="margin:0; font-size:14px;">Sistem Peminjaman sedang ditutup. Mahasiswa tidak dapat melakukan peminjaman barang pada periode ini.</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="header">
        <h2>Dashboard Staff</h2>
        <div>
            <span>Halo, <strong><?= $_SESSION['user']['username'] ?></strong> (<?= $_SESSION['user']['role'] ?>)</span>

            <a href="edit_profil.php" class="btn btn-blue" style="margin-left: 10px;">
                <i class="fas fa-user-edit"></i> Edit Profil
            </a>
            <a href="../logout.php" class="btn btn-red" style="margin-left: 10px;">Logout</a>
        </div>
    </div>

    <!-- MENU NAVIGASI KHUSUS STAFF: JADWAL RUANG LAB -->
    <div class="card" style="margin-bottom: 20px; border-left: 5px solid #009879; background: #f8f9fa;">
        <h3 style="margin:0 0 10px 0; color:#009879;"><i class="fas fa-calendar-alt"></i> Jadwal Ruang Lab</h3>
        <a href="jadwal_ruang.php" class="btn btn-green" style="font-size:15px; padding:8px 18px;">
            <i class="fas fa-calendar"></i> Lihat Jadwal Ruang Lab
        </a>
    </div>

    <!-- 0. FORM PENGATURAN AKADEMIK (MODIFIKASI: CUSTOM AUTOCOMPLETE) -->
    <div class="card" style="border-left: 5px solid #6f42c1;">
        <h3><i class="fas fa-cogs"></i> Pengaturan Ajaran & Semester</h3>
        <form method="post" id="formSettings">
            <div class="row" style="flex-wrap: wrap;">
                
                <!-- WRAPPER AUTOCOMPLETE -->
                <div class="autocomplete-wrapper">
                    <label style="display:block; margin-bottom:5px; font-weight:bold; font-size:13px;">Ajaran & Semester</label>
                    
                    <!-- Input Text (ID Ditambahkan untuk JS) -->
                    <input 
                        type="text" 
                        id="input_ajaran_semester" 
                        name="ajaran_semester" 
                        class="form-control" 
                        autocomplete="off" 
                        value="<?= htmlspecialchars($ajaran_semester_aktif) ?>" 
                        placeholder="Ketik untuk cari, atau pilih dari daftar..." 
                        required 
                        style="flex:1;"
                    >
            
                    <!-- CUSTOM DROPDOWN CONTAINER -->
                    <div id="dropdown_ajaran" class="custom-dropdown"></div>
                </div>
        
                <div style="display:flex; align-items:flex-end; padding-bottom:2px;">
                    <button type="submit" name="update_pengaturan" class="btn btn-purple">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>

        <!-- Form tambah ajaran & semester baru -->
        <?php if($_SESSION['user']['role'] == 'super_admin'): ?>
        <form method="post" style="margin-top:15px; display:flex; gap:10px; align-items:center;">
            <input type="text" name="ajaran_semester_baru" placeholder="Contoh: 2000/2001 Ganjil" required style="flex:1; max-width:220px;">
            <button type="submit" name="tambah_ajaran_semester" class="btn btn-blue">
                <i class="fas fa-plus"></i> Tambah Ajaran/Semester Baru
            </button>
        </form>
        <?php endif; ?>
        <div style="margin-top:15px; padding:10px; background:#f8f9fa; border-radius:4px; font-size:13px;">
            Info Sistem: 
            <strong>
                <?php if($status_sistem == 'aktif'): ?>
                    <span class="badge badge-green">AKTIF</span>
                <?php else: ?>
                    <span class="badge badge-red">NONAKTIF</span>
                <?php endif; ?>
                <?= htmlspecialchars($ajaran_semester_aktif) ?>
            </strong>
        </div>
    </div>

            <!-- KARTU KHUSUS SUPER ADMIN (Hanya muncul jika role super_admin) -->
    <?php if($_SESSION['user']['role'] == 'super_admin'): ?>
    <div class="card" style="border-left: 5px solid #000; background: #fffbe6;">
        <h3 style="color: #000;"><i class="fas fa-user-shield"></i> Zona Super Admin</h3>
        <p style="margin-bottom: 15px; font-size: 14px;">Kelola akun staf (Admin & Dosen) yang memiliki akses ke sistem.</p>
        
        <div class="row">
            <a href="manajemen_staff.php" class="btn btn-green" style="flex: 1; text-align: center;">
                <i class="fas fa-users-cog"></i> Manajemen Staff (Tambah/Edit/Hapus)
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- FORM MANAJEMEN KELAS & MATKUL -->
    <div class="card">
        <h3>Setup Kelas & Mata Kuliah</h3>
        <button><a href="manajemen_akademik.php" class="btn btn-blue">Lihat Semua</a></button>
        <div class="row">
            <form method="post" style="flex:1; display:flex; gap:5px;">
                <input type="text" name="nama_kelas" placeholder="Nama Kelas" required style="flex:1;">
                <button type="submit" name="tambah_kelas" class="btn btn-green">+ Kelas</button>
            </form>
            <form method="post" style="flex:1; display:flex; gap:5px;">
                <input type="text" name="nama_matkul" placeholder="Mata Kuliah" required style="flex:1;">
                <button type="submit" name="tambah_matkul" class="btn btn-green">+ Matkul</button>
            </form>
        </div>
    </div>

    <!-- FORM MANAJEMEN BARANG + SEARCH -->
    <div class="card">
        <h3>Manajemen Barang & Ketersediaan</h3>
        
        <div class="row" style="margin-bottom: 15px; background: #f1f1f1; padding: 10px; border-radius: 5px;">
            <form method="get" style="display:flex; flex:1; gap:5px;">
                <input type="text" name="search" placeholder="Cari Nama Barang atau Kode Unik..." value="<?= htmlspecialchars($search_keyword) ?>" style="flex:1;">
                <button type="submit" class="btn btn-blue" style="width: auto;"><i class="fas fa-search"></i> Cari</button>
            </form>
            <?php if($search_keyword != ""): ?>
                <a href="dashboard.php" class="btn btn-red" style="padding: 8px 10px;"><i class="fas fa-times"></i> Reset</a>
            <?php endif; ?>
        </div>

        <form method="post">
            <div class="row">
                <input type="text" name="nama_barang" placeholder="Nama Barang" required style="flex:2;">
                <input type="text" name="nomor_unik" placeholder="Prefix Kode Unik" required style="flex:1;">
                <input type="number" name="jumlah" placeholder="Jml Unit" required min="1" style="width: 80px;">
                <button type="submit" name="simpan_barang" class="btn btn-blue"><i class="fas fa-plus"></i> Tambah Baru</button>
            </div>
        </form>
        
        <h4>Daftar Barang</h4>
        <a href="cetak_semua.php">
            <button class="btn btn-purple">
                <i class="fas fa-print"></i> Cetak Semua Kode Unik
            </button>
        </a>
        <table>
            <tr>
                <?php if($is_searching): ?>
                    <th style="width: 200px;">Kondisi Barang (Detail)</th>
                <?php endif; ?>
                
                <th>Nama Barang</th>
                <th>Daftar Nomor Unik</th>
                <th style="text-align:center;">Total</th>
                <!-- PERUBAHAN: Header kolom diubah dari 'Tersedia' menjadi 'Sedang Dipinjam' -->
                <th style="text-align:center;">Dipinjam</th>
                
                <?php if($is_searching): ?>
                    <th style="text-align:center;">Aksi Cetak</th>
                <?php endif; ?>

                <th style="text-align:center;">Aksi</th>
            </tr>
            <?php

            $where = "";

            if (!empty($search_keyword)) {
                $search_keyword = $conn->real_escape_string($search_keyword);
                $where = "WHERE b.nama_barang LIKE '%$search_keyword%' OR b.nomor_unik LIKE '%$search_keyword%'";
            }
                    $q = $conn->query("SELECT b.nama_barang, 
                                                GROUP_CONCAT(b.nomor_unik ORDER BY b.nomor_unik SEPARATOR ', ') as kode_list,
                                                COUNT(b.id) as total,
                                                (
                                                    SELECT COUNT(*) 
                                                    FROM peminjaman p
                                                    JOIN barang b2 ON p.barang_id = b2.id
                                                    WHERE b2.nama_barang = b.nama_barang
                                                    AND p.status_peminjaman = 'dipinjam' 
                                                ) as sedang_dipinjam
                                                FROM barang b
                                                GROUP BY b.nama_barang
                                                ORDER BY b.nama_barang ASC");
            while($b = $q->fetch_assoc()){
                $kondisi_content = "-";
                if ($is_searching) {
                    $qBreakdown = $conn->query("SELECT kondisi, COUNT(*) as jml FROM barang WHERE nama_barang='{$b['nama_barang']}' GROUP BY kondisi ORDER BY jml DESC");
                    $kondisi_content = "";
                    while($rowK = $qBreakdown->fetch_assoc()){
                        $badge_class = "badge-blue";
                        if($rowK['kondisi'] == 'Baik') $badge_class = "badge-green";
                        elseif(strpos($rowK['kondisi'], 'Rusak') !== false) $badge_class = "badge-red";
                        $kondisi_content .= "<span class='badge {$badge_class}'>{$rowK['kondisi']}: {$rowK['jml']}</span> ";
                    }
                }
                // PERUBAHAN: Variabel $sisa_barang tidak diperlukan lagi, kita gunakan langsung $b['dipinjam']
            ?>
            <tr>
                <?php if($is_searching): ?>
                    <td><?= $kondisi_content ?></td>
                <?php endif; ?>

                <td><strong><?= htmlspecialchars($b['nama_barang']) ?></strong></td>
                <td><small style="font-size: 11px;"><?= htmlspecialchars($b['kode_list']) ?></small></td>
                <td style="text-align:center;"><?= $b['total'] ?></td>
                <!-- PERUBAHAN: Menampilkan jumlah barang yang sedang dipinjam dengan warna merah -->
                <td style="text-align:center;">
                    <?php if($b['sedang_dipinjam'] > 0): ?>
                        <span class="badge badge-orange"><?= $b['sedang_dipinjam'] ?></span>
                    <?php else: ?>
                        <span class="badge badge-green">0</span>
                    <?php endif; ?>
                </td>
                    
                <?php if($is_searching): ?>
                    <td style="text-align:center;">
                        <a href="print_barang.php?nama=<?= urlencode($b['nama_barang']) ?>" target="_blank" class="btn btn-purple btn-small" style="font-size:12px; padding: 5px 10px;">
                            <i class="fas fa-print"></i> Cetak Label
                        </a>
                    </td>
                <?php endif; ?>
                
                <td style="text-align:center;">
                    <div style="display: flex; justify-content: center; gap: 5px;">
                        <a href="edit_nama.php?nama=<?= urlencode($b['nama_barang']) ?>" class="btn btn-blue" style="font-size:12px; padding: 5px 10px;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="?hapus_semua_unit=<?= urlencode($b['nama_barang']) ?>" onclick="return confirm('Hapus semua data?')" class="btn btn-red" style="font-size:12px; padding: 5px 10px;">
                           <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </td>
            </tr>
            <?php } 
            
            if($q->num_rows == 0) {
                $colspan = 6;
                if($is_searching) $colspan += 2;
                echo "<tr><td colspan='$colspan' align='center' style='padding: 20px; color: #666;'>Data tidak ditemukan.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- RIWAYAT SINGKAT -->
        <a href="import_excel.php" target="_blank">
            <button class="btn btn-purple" style="margin-bottom:10px;">
                <i class="fas fa-file-import"></i> Import Riwayat Excel
            </button>
        </a>
    <div class="card print-only-card">
        <a href="export_excel.php" target="_blank">
            <button class="btn btn-green" style="margin-bottom:10px;">
                <i class="fas fa-file-excel"></i> Export ke Excel
            </button>
        </a>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;">Riwayat Peminjaman</h3>
            <button>
                <a href="?hapus_semua_riwayat=1" onclick="return confirm('Hapus semua riwayat?')" class="btn btn-red" style="font-size:12px; padding: 5px 10px;">
                    <i class="fas fa-trash"></i> Hapus Semua Riwayat
                </a>
            </button>
            <button onclick="window.print()" class="btn btn-purple">Print Keseluruhan</button>
        </div>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Kelas / Matkul</th>
                <th>Penanggung Jawab</th>
                <th>Semester</th>
                <th>Tanggal Pinjam</th>
                <th>Waktu Pinjam</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <?php
        // Ambil riwayat peminjaman, status mengikuti status barang mahasiswa
        // FILTER: Hanya tampilkan riwayat sesuai ajaran_semester aktif
        $ajaran_semester_filter = $conn->real_escape_string($ajaran_semester_aktif);
        $qRiwayat = $conn->query("SELECT 
            b.nama_barang as item_name, 
            COUNT(*) as jml_unit,
            SUM(CASE WHEN p.status_peminjaman = 'dipinjam' THEN 1 ELSE 0 END) as jml_dipinjam,
            MIN(p.tanggal_pinjam) as tanggal_awal,
            MIN(p.waktu_pinjam) as waktu_awal,
            MAX(p.waktu_kembali) as waktu_akhir,
            p.ajaran_semester,
            k.nama_kelas,
            mk.nama_matkul,
            GROUP_CONCAT(DISTINCT p.nama_mahasiswa SEPARATOR ', ') as list_mhs
            FROM peminjaman p
            JOIN barang b ON p.barang_id = b.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN kelas k ON p.kelas_id = k.id
            LEFT JOIN mata_kuliah mk ON p.matkul_id = mk.id
            WHERE p.ajaran_semester = '$ajaran_semester_filter'
            GROUP BY b.nama_barang, p.ajaran_semester, k.nama_kelas, mk.nama_matkul
            ORDER BY p.ajaran_semester DESC, tanggal_awal DESC, waktu_awal DESC"); 
        
        if($qRiwayat && $qRiwayat->num_rows > 0) {
            while($r = $qRiwayat->fetch_assoc()){
                $periode_semester = (!empty($r['ajaran_semester'])) ? $r['ajaran_semester'] : 'Unknown';
                $jml_dipinjam = (int)$r['jml_dipinjam'];
                $jml_unit = (int)$r['jml_unit'];
                $display_tanggal_pinjam = ($r['tanggal_awal']) ? date('d-m-Y', strtotime($r['tanggal_awal'])) : '-';
                $display_pinjam = htmlspecialchars($r['waktu_awal']) . " WIB";
                if ($jml_dipinjam > 0) {
                    $status_display = "<span class='badge badge-orange'>Dipinjam ($jml_dipinjam/$jml_unit)</span>";
                    $ada_dipinjam = true;
                } else {
                    $status_display = '<span class="badge badge-green">Dikembalikan</span>';
                    $ada_dipinjam = false;
                }
                $uniq_id = $r['item_name'] . "|" . $periode_semester . "|" . $r['nama_kelas'] . "|" . $r['nama_matkul'];
        ?>
        <tr>
            <td>
                <strong><?= htmlspecialchars($r['item_name']) ?></strong>
            </td>
            <td>
                <strong><?= htmlspecialchars($r['nama_kelas']) ?></strong>
                <div style="font-size:11px; color:#666;"><?= htmlspecialchars($r['nama_matkul']) ?></div>
            </td>
            <td>
                <span style="font-weight:bold; color: #333; font-size:13px;">
                    <i class="fas fa-user-graduate"></i> <?= htmlspecialchars($r['list_mhs']) ?>
                </span>
            </td>
            <td>
                <span class="badge badge-blue" style="margin-left:5px;">
                    <?= htmlspecialchars($periode_semester) ?>
                </span>
            </td>
            <td><?= $display_tanggal_pinjam ?></td>
            <td><?= $display_pinjam ?></td>
            <td><?= $status_display ?></td>
            <td>
                <div style="display: flex; gap: 5px;">
                    <?php if($ada_dipinjam): ?>
                    <?php endif; ?>
                    <a href="detail_riwayat.php?data=<?= urlencode($uniq_id) ?>" class="btn btn-blue" style="padding: 5px 10px; font-size:12px;">
                        <i class="fas fa-list"></i> Detail
                    </a>
                </div>
            </td>
        </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan='8' align='center' style='padding: 20px; color: #666;'>Belum ada riwayat peminjaman.</td></tr>";
        }
        ?>
    </table>
</div>
</div>

<!-- SCRIPT FULL -->
<script>
    // 1. Ambil data dari PHP (JSON)
    const databaseAjaran = <?= $json_daftar_ajaran ?>;

    // 2. Referensi Elemen DOM
    const inputField = document.getElementById('input_ajaran_semester');
    const dropdownList = document.getElementById('dropdown_ajaran');
    const formSettings = document.getElementById('formSettings');

    // 3. Event Listener: Saat user mengetik
    inputField.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        dropdownList.innerHTML = ''; 

        if (query.length === 0) {
            dropdownList.classList.remove('show');
            return;
        }

        // Filter data
        const filteredData = databaseAjaran.filter(item => 
            item.toLowerCase().includes(query)
        );

        if (filteredData.length > 0) {
            filteredData.forEach(item => {
                const div = document.createElement('div');
                div.className = 'dropdown-item';
                div.textContent = item;
                
                div.addEventListener('click', function() {
                    inputField.value = item;
                    dropdownList.classList.remove('show');
                });
                
                dropdownList.appendChild(div);
            });
            dropdownList.classList.add('show');
        } else {
            const div = document.createElement('div');
            div.className = 'dropdown-item';
            div.style.color = '#999';
            div.textContent = "Tidak ditemukan";
            dropdownList.appendChild(div);
            dropdownList.classList.add('show');
        }
    });

    // 4. Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            dropdownList.classList.remove('show');
        }
    });

    // 5. Validasi Sederhana Saat Submit
    formSettings.addEventListener('submit', function(e) {
        const val = inputField.value;
        const isValid = databaseAjaran.includes(val);
        if(!isValid) {
            e.preventDefault();
            alert("Mohon pilih ajaran semester yang ada di daftar database.");
        }
    });
</script>

</body>
</html>