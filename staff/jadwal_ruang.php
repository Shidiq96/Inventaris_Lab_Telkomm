<?php
session_start();

// 1. Cek Akses
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'dosen', 'super_admin'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Izin Tulis
 $current_role = $_SESSION['user']['role'];
 $allow_edit = in_array($current_role, ['admin', 'super_admin']);

// 3. Koneksi Database
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: File database.php tidak ditemukan.");
}

if (!isset($conn)) {
    die("Error: Koneksi Database Gagal.");
}

// --- PROSES HAPUS DATA ---
if (isset($_GET['hapus'])) {
    if (!$allow_edit) { die("Akses Ditolak."); }
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query_hapus = "DELETE FROM jadwal_ruang WHERE id='$id'";
    if (mysqli_query($conn, $query_hapus)) {
        echo "<script>alert('Jadwal berhasil dihapus!'); window.location='jadwal_ruang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- PROSES UPDATE DATA LENGKAP ---
if (isset($_POST['update'])) {
    if (!$allow_edit) { die("Akses Ditolak."); }
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $hari = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_ke = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    
    // Handle Optional Fields
    $kelas_id = !empty($_POST['kelas_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['kelas_id']) . "'" : "NULL";
    $matkul_id = !empty($_POST['matkul_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['matkul_id']) . "'" : "NULL";
    $ruang_id = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id = !empty($_POST['dosen_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['dosen_id']) . "'" : "NULL";

    // Cek validasi update (jam ke atau waktu bentrok?)
    $cek_bentrok_update = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE id != '$id' AND hari='$hari' AND ruang_id='$ruang_id' AND (jam_ke='$jam_ke' OR waktu_id='$waktu_id')");
    if (mysqli_num_rows($cek_bentrok_update) > 0) {
        echo "<script>alert('Update Gagal! Jadwal bentrok dengan Jam Ke atau Waktu yang sudah ada di hari dan ruangan tersebut.'); window.location='jadwal_ruang.php';</script>";
    } else {
        $query_update = "UPDATE jadwal_ruang SET 
                                    hari='$hari', 
                                    jam_ke='$jam_ke', 
                                    waktu_id='$waktu_id', 
                                    kelas_id=$kelas_id, 
                                    matkul_id=$matkul_id, 
                                    ruang_id='$ruang_id', 
                                    dosen_id=$dosen_id 
                                WHERE id='$id'";
        
        if (mysqli_query($conn, $query_update)) {
            echo "<script>alert('Jadwal berhasil diubah!'); window.location='jadwal_ruang.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- PROSES UPDATE MINI ---
if (isset($_POST['update_mini'])) {
    if (!$allow_edit) { die("Akses Ditolak."); }
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $jam_ke = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    $ruang_id = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id = !empty($_POST['dosen_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['dosen_id']) . "'" : "NULL";

    // Ambil data lama untuk cek validasi mini update
    $data_lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE id='$id'"));
    $hari_lama = $data_lama['hari'];
    $ruang_lama = $data_lama['ruang_id'];

    // Cek bentrok mini update (jam ke atau waktu bentrok dengan jadwal lain DI HARI YANG SAMA)
    // Asumsi mini update tidak mengubah hari, jadi kita cek di hari lama
    $cek_bentrok_mini = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE id != '$id' AND hari='$hari_lama' AND ruang_id='$ruang_id' AND (jam_ke='$jam_ke' OR waktu_id='$waktu_id')");
    
    if (mysqli_num_rows($cek_bentrok_mini) > 0) {
        echo "<script>alert('Update Gagal! Bentrok Jam Ke atau Waktu.'); window.location='jadwal_ruang.php';</script>";
    } else {
        $query_update_mini = "UPDATE jadwal_ruang SET 
                                    jam_ke='$jam_ke', 
                                    waktu_id='$waktu_id', 
                                    ruang_id='$ruang_id', 
                                    dosen_id=$dosen_id 
                                WHERE id='$id'";
        
        if (mysqli_query($conn, $query_update_mini)) {
            echo "<script>alert('Jadwal berhasil diubah!'); window.location='jadwal_ruang.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- PROSES TAMBAH DATA ---
if (isset($_POST['simpan'])) {
    if (!$allow_edit) { die("Akses Ditolak."); }
    $hari = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_ke = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    
    // Handle Optional Fields
    $kelas_id = !empty($_POST['kelas_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['kelas_id']) . "'" : "NULL";
    $matkul_id = !empty($_POST['matkul_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['matkul_id']) . "'" : "NULL";
    $ruang_id = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id = !empty($_POST['dosen_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['dosen_id']) . "'" : "NULL";

    // Cek duplikat LOGIKA BARU: Hari + Ruang + (Jam Ke SAMA ATAU Waktu SAMA)
    $cek_duplikat = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE hari='$hari' AND ruang_id='$ruang_id' AND (jam_ke='$jam_ke' OR waktu_id='$waktu_id')");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        echo "<script>alert('Jadwal bentrok! Jam Ke atau Waktu tersebut sudah terisi di hari dan ruangan ini.'); window.location='jadwal_ruang.php';</script>";
    } else {
        $query_insert = "INSERT INTO jadwal_ruang (hari, jam_ke, waktu_id, kelas_id, matkul_id, ruang_id, dosen_id) 
                          VALUES ('$hari', '$jam_ke', '$waktu_id', $kelas_id, $matkul_id, '$ruang_id', $dosen_id)";
        if (mysqli_query($conn, $query_insert)) {
            echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location='jadwal_ruang.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

if (isset($_POST['simpan_detail'])) {
    if (!$allow_edit) { die("Akses Ditolak."); }
    $hari = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_ke = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    
    // Handle Optional Fields
    $kelas_id = !empty($_POST['kelas_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['kelas_id']) . "'" : "NULL";
    $matkul_id = !empty($_POST['matkul_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['matkul_id']) . "'" : "NULL";
    $ruang_id = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id = !empty($_POST['dosen_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['dosen_id']) . "'" : "NULL";

    // Cek duplikat LOGIKA BARU
    $cek_duplikat = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE hari='$hari' AND ruang_id='$ruang_id' AND (jam_ke='$jam_ke' OR waktu_id='$waktu_id')");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        echo "<script>alert('Jadwal bentrok! Jam Ke atau Waktu tersebut sudah terisi.'); window.location='jadwal_ruang.php';</script>";
    } else {
        $query_insert = "INSERT INTO jadwal_ruang (hari, jam_ke, waktu_id, kelas_id, matkul_id, ruang_id, dosen_id) 
                          VALUES ('$hari', '$jam_ke', '$waktu_id', $kelas_id, $matkul_id, '$ruang_id', $dosen_id)";
        if (mysqli_query($conn, $query_insert)) {
            echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location='jadwal_ruang.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- AMBIL DATA ---
 $query_jadwal = "SELECT jr.id, jr.hari, jr.jam_ke, jr.waktu_id, w.waktu, jr.kelas_id, jr.matkul_id, jr.ruang_id, jr.dosen_id, 
                 k.nama_kelas, m.nama_matkul, r.ruang, COALESCE(d.nama_lengkap, d.username) as nama_dosen
          FROM jadwal_ruang jr
          LEFT JOIN kelas k ON jr.kelas_id = k.id
          LEFT JOIN mata_kuliah m ON jr.matkul_id = m.id
          JOIN waktu w ON jr.waktu_id = w.id
          JOIN ruangan r ON jr.ruang_id = r.id
          LEFT JOIN users d ON jr.dosen_id = d.id  
          ORDER BY r.ruang ASC, FIELD(jr.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), jr.jam_ke ASC";
          
 $result_jadwal = mysqli_query($conn, $query_jadwal);
 $jadwal_arr = [];
while ($row = mysqli_fetch_assoc($result_jadwal)) {
    $jadwal_arr[] = $row;
}

// --- LOGIKA BENTROK (UPDATE: JAM KE ATAU WAKTU SAMA) ---
 $bentrok_ids = [];
foreach ($jadwal_arr as $idx1 => $row1) {
    foreach ($jadwal_arr as $idx2 => $row2) {
        if ($idx1 < $idx2) {
            // Cek: Ruangan SAMA DAN Hari SAMA
            if ($row1['ruang_id'] == $row2['ruang_id'] && $row1['hari'] == $row2['hari']) {
                
                // PERUBAHAN LOGIKA: Bentrok jika Jam Ke SAMA ATAU Waktu SAMA
                if ($row1['jam_ke'] == $row2['jam_ke'] || $row1['waktu_id'] == $row2['waktu_id']) {
                    $bentrok_ids[] = $row1['id'];
                    $bentrok_ids[] = $row2['id'];
                }
            }
        }
    }
}
 $bentrok_ids = array_unique($bentrok_ids);

// --- DROPDOWNS ---
 $kelas_arr = []; $kelas_q = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC"); while ($row = mysqli_fetch_assoc($kelas_q)) { $kelas_arr[] = $row; }
 $matkul_arr = []; $matkul_q = mysqli_query($conn, "SELECT * FROM mata_kuliah ORDER BY nama_matkul ASC"); while ($row = mysqli_fetch_assoc($matkul_q)) { $matkul_arr[] = $row; }
 $waktu_arr = []; $waktu_q = mysqli_query($conn, "SELECT * FROM waktu ORDER BY id ASC"); while ($row = mysqli_fetch_assoc($waktu_q)) { $waktu_arr[] = $row; }
 $ruang_arr = []; $ruang_q = mysqli_query($conn, "SELECT * FROM ruangan ORDER BY ruang ASC"); while ($row = mysqli_fetch_assoc($ruang_q)) { $ruang_arr[] = $row; }
 $dosen_arr = []; $dosen_q = mysqli_query($conn, "SELECT id, COALESCE(nama_lengkap, username) as nama_dosen FROM users WHERE role='dosen' ORDER BY nama_dosen ASC"); while ($row = mysqli_fetch_assoc($dosen_q)) { $dosen_arr[] = $row; }
 $list_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Ruang Lab</title>
    <style>
        :root { --primary-color: #0d47a1; --secondary-color: #009879; --accent-color: #f39c12; --danger-color: #e74c3c; --bg-light: #f4f7f6; --text-dark: #333; --border-color: #ddd; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg-light); color: var(--text-dark); margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: var(--primary-color); border-bottom: 2px solid var(--bg-light); padding-bottom: 10px; margin-top: 0; }
        .user-info { margin-bottom: 20px; text-align: right; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; transition: background 0.3s; display: inline-block; }
        .btn-blue { background-color: var(--primary-color); color: #fff; }
        .action-btn { padding: 5px 10px; font-size: 12px; margin-right: 5px; color: #fff; }
        .btn-edit { background-color: var(--accent-color); }
        .btn-delete { background-color: var(--danger-color); }
        .form-box { background: #f9f9f9; border: 1px solid var(--border-color); padding: 15px; margin-bottom: 25px; border-radius: 5px; }
        form { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; flex: 1; min-width: 120px; }
        .form-group label { font-size: 12px; font-weight: bold; margin-bottom: 5px; color: #555; }
        input, select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; }
        button[type="submit"] { padding: 9px 20px; background-color: var(--secondary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th, td { border: 1px solid var(--border-color); padding: 10px; text-align: left; }
        th { background-color: var(--primary-color); color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e6f7ff; }

        /* Style Bentrok pada Baris (Untuk menandai baris spesifik) */
        tr.bentrok { background-color: #ffcdd2 !important; color: #b71c1c !important; border: 2px solid #ef5350; font-weight: bold; }

        /* Style Bentrok pada TABLE (Untuk menandai tabel berwarna merah) */
        .table-bentrok {
            background-color: #ffebee !important; 
            border: 2px solid #e74c3c !important; 
            box-shadow: 0 0 5px rgba(231, 76, 60, 0.5);
        }
        .table-bentrok th {
            background-color: #e74c3c !important; 
            color: white !important;
        }

        /* Struktur Grup */
        .ruang-group { margin-bottom: 40px; border: 1px solid #ccc; border-radius: 8px; overflow: hidden; background: #fff; }
        .ruang-header { background: var(--primary-color); color: white; padding: 15px; font-weight: bold; font-size: 18px; display: flex; justify-content: space-between; align-items: center; }
        .add-link-mini { color: white; font-weight: normal; font-size: 0.9em; text-decoration: underline; cursor: pointer; }

        .hari-group { border-bottom: 1px solid #eee; }
        .hari-group:last-child { border-bottom: none; }
        .hari-header { 
            background: #e3f2fd; color: var(--primary-color); padding: 10px 15px; font-weight: bold; font-size: 16px; border-bottom: 2px solid var(--secondary-color); display: flex; justify-content: space-between; align-items: center;
        }
        
        /* Header Hari merah jika ada bentrok */
        .hari-header-bentrok {
            background: #ffcdd2 !important;
            color: #b71c1c !important;
            border-bottom: 2px solid #ef5350 !important;
            font-weight: bold;
        }

        .ruang-group table { margin: 0; border: none; }
        .ruang-group table th, .ruang-group table td { border-bottom: 1px solid #eee; border-top: none; border-left: none; border-right: none; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 3% auto; padding: 20px; border: 1px solid #888; width: 95%; max-width: 600px; border-radius: 8px; position: relative; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; position: absolute; right: 15px; top: 5px; }
        .close:hover { color: black; }
        .mini-popup { display: none; position: fixed; bottom: 20px; right: 20px; background: white; border: 1px solid #ccc; padding: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 1001; width: 300px; border-radius: 5px; }
        .mini-btn-group { margin-top: 10px; text-align: right; }
        .btn-cancel { background: #ccc; color: #333; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;}
        .btn-save { background: var(--accent-color); color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;}
    </style>
</head>
<body>

<div class="container">
    <div class="user-info"> 
        <a href="dashboard.php" class="btn btn-blue">Kembali ke Dashboard</a>
    </div>

    <h2>Jadwal Penggunaan Ruang Lab</h2>
    <div style="margin-bottom: 20px;">
        <?php if ($allow_edit) { ?>
            <a href="manajemen_data.php" class="btn" style="background-color: #607d8b; color: white;">⚙️ Atur Ruangan & Waktu</a>
        <?php } ?>

        <a href="export_jadwal_ruang.php" class="btn" style="background-color: #4caf50; color: white;">📥 Export ke Excel (.csv)</a>

        <a href="import_jadwal_excel.php" target="_blank">
            <button class="btn" style="background-color: #e67e22; color: white;">📤 Import dari Excel</button>
        </a>

    </div>

    <!-- Form Input Utama -->
    <?php if ($allow_edit) { ?>
    <div class="form-box">
        <h3>Tambah Jadwal Baru</h3>
        <form method="POST" action="">
            <div class='form-group'>
                <label>Ruangan:</label>
                <select name="ruang_id" required>
                    <option value="">-- Pilih Ruangan --</option>
                    <?php foreach($ruang_arr as $row_r) { ?>
                        <option value="<?= $row_r['id']; ?>"><?= $row_r['ruang']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Hari:</label>
                <select name="hari" required>
                    <?php foreach($list_hari as $h) { ?>
                        <option value="<?= $h; ?>"><?= $h; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jam Ke:</label>
                <input type="text" name="jam_ke" placeholder="Contoh: 1, 2, 3" required>
            </div>
            <div class="form-group">
                <label>Waktu:</label>
                <select name="waktu_id" required>
                    <option value="">-- Pilih Waktu --</option>
                    <?php foreach($waktu_arr as $row_w) { ?>
                        <option value="<?= $row_w['id']; ?>"><?= $row_w['waktu']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group"><label>Kelas:</label><select name="kelas_id"><option value="">-- Kosong --</option><?php foreach($kelas_arr as $row_k) { ?><option value="<?= $row_k['id']; ?>"><?= $row_k['nama_kelas']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Matakuliah:</label><select name="matkul_id"><option value="">-- Kosong --</option><?php foreach($matkul_arr as $row_m) { ?><option value="<?= $row_m['id']; ?>"><?= $row_m['nama_matkul']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Dosen:</label><select name="dosen_id"><option value="">-- Kosong --</option><?php foreach($dosen_arr as $row_d) { ?><option value="<?= $row_d['id']; ?>"><?= htmlspecialchars($row_d['nama_dosen']); ?></option><?php } ?></select></div>
            <button type="submit" name="simpan">Simpan Jadwal</button>
        </form>
    </div>
    <?php } ?>

    <!-- TAMPILAN JADWAL -->
    <?php 
    foreach($ruang_arr as $ruang) { 
        $jadwal_ruang_ini = array_filter($jadwal_arr, function($item) use ($ruang) {
            return $item['ruang_id'] == $ruang['id'];
        });
        
        if (count($jadwal_ruang_ini) > 0) {
    ?>
        <div class="ruang-group">
            <div class="ruang-header">
                <span>Jadwal: Lab. <?= htmlspecialchars($ruang['ruang']); ?></span>
                <?php if ($allow_edit) { ?>
                <span class="add-link-mini" onclick="openAddModalRuang('<?= $ruang['id']; ?>')">+ Tambah Jadwal di Ruangan Ini</span>
                <?php } ?>
            </div>

            <?php 
            $jadwal_per_hari = [];
            foreach($jadwal_ruang_ini as $j) {
                $jadwal_per_hari[$j['hari']][] = $j;
            }

            foreach($list_hari as $hari) {
                if (isset($jadwal_per_hari[$hari])) {
                    // Cek apakah ada bentrok di hari ini
                    $ada_bentrok_hari = false;
                    foreach($jadwal_per_hari[$hari] as $row_check) {
                        if (in_array($row_check['id'], $bentrok_ids)) {
                            $ada_bentrok_hari = true;
                            break;
                        }
                    }
                    
                    $hari_header_class = $ada_bentrok_hari ? 'hari-header-bentrok' : '';
                    $table_class = $ada_bentrok_hari ? 'table-bentrok' : '';
                    
                    ?>
                    <div class="hari-group">
                        <div class="hari-header <?= $hari_header_class ?>"><?= $hari; ?></div>
                        <!-- Terapkan class table-bentrok di sini -->
                        <table class="<?= $table_class; ?>">
                            <thead>
                                <tr>
                                    <th width="10%">Jam Ke</th>
                                    <th width="15%">Waktu</th>
                                    <th width="15%">Kelas</th>
                                    <th width="35%">Mata Kuliah</th>
                                    <th width="20%">Dosen</th>
                                    <?php if ($allow_edit) { ?><th width="5%">Aksi</th><?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($jadwal_per_hari[$hari] as $row) { 
                                    $is_bentrok = in_array($row['id'], $bentrok_ids);
                                    $row_class = $is_bentrok ? 'bentrok' : '';
                                    
                                    $disp_kelas = $row['nama_kelas'] ?: '-';
                                    $disp_matkul = $row['nama_matkul'] ?: '-';
                                    $disp_dosen = $row['nama_dosen'] ?: '-';
                                ?>
                                <tr class="<?= $row_class; ?>">
                                    <td><strong><?= htmlspecialchars($row['jam_ke']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['waktu']); ?></td>
                                    <td><?= htmlspecialchars($disp_kelas); ?></td>
                                    <td><?= htmlspecialchars($disp_matkul); ?></td>
                                    <td><?= htmlspecialchars($disp_dosen); ?></td>
                                    <?php if ($allow_edit) { ?>
                                    <td>
                                        <a href="#" onclick='openMiniPopup(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>); return false;' class="action-btn btn-edit">✎</a>
                                        <a href="?hapus=<?= $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Hapus?')">✖</a>
                                    </td>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div> 
                    <?php 
                } 
            } 
            ?>
        </div> 
    <?php 
        } 
    } 
    ?>
</div>

<!-- Modal Form ADD -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3 style="color:var(--secondary-color)">Tambah Jadwal</h3>
        <form method="POST" action="">
            <input type="hidden" name="ruang_id" id="add-ruang_id">
            <div class="form-group">
                <label>Ruangan:</label>
                <input type="text" id="add-ruang-display" disabled style="background: #eee;">
            </div>
            <div class="form-group">
                <label>Hari:</label>
                <select name="hari" required>
                    <?php foreach($list_hari as $h) { ?><option value="<?= $h; ?>"><?= $h; ?></option><?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jam Ke:</label>
                <input type="text" name="jam_ke" required>
            </div>
            <div class="form-group">
                <label>Waktu:</label>
                <select name="waktu_id" required>
                    <option value="">-- Pilih Waktu --</option>
                    <?php foreach($waktu_arr as $row_w) { ?><option value="<?= $row_w['id']; ?>"><?= $row_w['waktu']; ?></option><?php } ?>
                </select>
            </div>
            <div class="form-group"><label>Kelas:</label><select name="kelas_id"><option value="">-- Kosong --</option><?php foreach($kelas_arr as $row_k) { ?><option value="<?= $row_k['id']; ?>"><?= $row_k['nama_kelas']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Matakuliah:</label><select name="matkul_id"><option value="">-- Kosong --</option><?php foreach($matkul_arr as $row_m) { ?><option value="<?= $row_m['id']; ?>"><?= $row_m['nama_matkul']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Dosen:</label><select name="dosen_id"><option value="">-- Kosong --</option><?php foreach($dosen_arr as $row_d) { ?><option value="<?= $row_d['id']; ?>"><?= $row_d['nama_dosen']; ?></option><?php } ?></select></div>
            <br>
            <button type="submit" name="simpan_detail" style="width:100%">Simpan Jadwal</button>
        </form>
    </div>
</div>

<!-- Modal Edit Lengkap -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3 style="color:var(--accent-color)">Edit Jadwal</h3>
        <form method="POST" action="">
            <input type="hidden" name="id" id="edit-id">
            <div class="form-group"><label>Ruangan:</label><select name="ruang_id" id="edit-ruang_id" required><?php foreach($ruang_arr as $row_r) { ?><option value="<?= $row_r['id']; ?>"><?= htmlspecialchars($row_r['ruang']); ?></option><?php } ?></select></div>
            <div class="form-group"><label>Hari:</label><select name="hari" id="edit-hari" required><?php foreach($list_hari as $h) { ?><option value="<?= $h; ?>"><?= $h; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Jam Ke:</label><input type="text" name="jam_ke" id="edit-jam_ke" required></div>
            <div class="form-group"><label>Waktu:</label><select name="waktu_id" id="edit-waktu_id" required><option value="">-- Pilih Waktu --</option><?php foreach($waktu_arr as $row_w) { ?><option value="<?= $row_w['id']; ?>"><?= $row_w['waktu']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Kelas:</label><select name="kelas_id" id="edit-kelas_id"><option value="">-- Kosong --</option><?php foreach($kelas_arr as $row_k) { ?><option value="<?= $row_k['id']; ?>"><?= $row_k['nama_kelas']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Matakuliah:</label><select name="matkul_id" id="edit-matkul_id"><option value="">-- Kosong --</option><?php foreach($matkul_arr as $row_m) { ?><option value="<?= $row_m['id']; ?>"><?= $row_m['nama_matkul']; ?></option><?php } ?></select></div>
            <div class="form-group"><label>Dosen:</label><select name="dosen_id" id="edit-dosen_id"><option value="">-- Kosong --</option><?php foreach($dosen_arr as $row_d) { ?><option value="<?= $row_d['id']; ?>"><?= htmlspecialchars($row_d['nama_dosen']); ?></option><?php } ?></select></div>
            <br>
            <button type="submit" name="update" style="width:100%; background-color: var(--accent-color);">Update Jadwal</button>
        </form>
    </div>
</div>

<!-- MINI POPUP -->
<div id="miniEditPopup" class="mini-popup">
    <div class="mini-popup-content">
        <h4>Edit Cepat Jadwal</h4>
        <form method="POST">
            <input type="hidden" name="id" id="mini-id">
            <label>Jam Ke</label><input type="text" name="jam_ke" id="mini-jam_ke" required>
            <label>Waktu</label><select name="waktu_id" id="mini-waktu_id" required><?php foreach($waktu_arr as $w) { ?><option value="<?= $w['id']; ?>"><?= $w['waktu']; ?></option><?php } ?></select>
            <label>Ruang</label><select name="ruang_id" id="mini-ruang_id" required><?php foreach($ruang_arr as $r) { ?><option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['ruang']); ?></option><?php } ?></select>
            <label>Dosen</label><select name="dosen_id" id="mini-dosen_id"><option value="">-- Kosong --</option><?php foreach($dosen_arr as $d) { ?><option value="<?= $d['id']; ?>"><?= htmlspecialchars($d['nama_dosen']); ?></option><?php } ?></select>
            <div class="mini-btn-group">
                <button type="submit" name="update_mini" class="btn-save">Update</button>
                <button type="button" onclick="document.getElementById('miniEditPopup').style.display='none'" class="btn-cancel">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal(id) { document.getElementById(id).style.display = "none"; }
    window.onclick = function(e) { if (e.target.classList.contains('modal')) e.target.style.display = "none"; }
    
    function openAddModalRuang(ruang_id) {
        const select = document.getElementById('edit-ruang_id');
        let namaRuang = "";
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value == ruang_id) { namaRuang = select.options[i].text; break; }
        }
        document.getElementById('add-ruang_id').value = ruang_id;
        document.getElementById('add-ruang-display').value = namaRuang;
        document.getElementById('addModal').style.display = 'block';
    }

    function openEditModal(data) {
        document.getElementById('edit-id').value = data.id;
        document.getElementById('edit-hari').value = data.hari;
        document.getElementById('edit-ruang_id').value = data.ruang_id;
        document.getElementById('edit-jam_ke').value = data.jam_ke;
        document.getElementById('edit-waktu_id').value = data.waktu_id;
        document.getElementById('edit-kelas_id').value = data.kelas_id || "";
        document.getElementById('edit-matkul_id').value = data.matkul_id || "";
        document.getElementById('edit-dosen_id').value = data.dosen_id || "";
        document.getElementById('editModal').style.display = 'block';
    }

    function openMiniPopup(data) {
        document.getElementById('mini-id').value = data.id;
        document.getElementById('mini-jam_ke').value = data.jam_ke;
        document.getElementById('mini-waktu_id').value = data.waktu_id;
        document.getElementById('mini-ruang_id').value = data.ruang_id;
        document.getElementById('mini-dosen_id').value = data.dosen_id || "";
        document.getElementById('miniEditPopup').style.display = 'block';
    }
</script>
</body>
</html>