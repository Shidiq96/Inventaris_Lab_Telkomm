<?php
session_start();

// 1. Cek Akses (Admin, Dosen, Super Admin bisa akses halaman untuk LIHAT)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'dosen', 'super_admin'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Tentukan Izin Tulis (Hanya Admin & Super Admin yang boleh Ubah/Hapus/Tambah)
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
    if (!$allow_edit) { die("Akses Ditolak. Anda tidak memiliki izin menghapus data."); }
    
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query_hapus = "DELETE FROM jadwal_ruang WHERE id='$id'";
    if (mysqli_query($conn, $query_hapus)) {
        echo "<script>alert('Jadwal berhasil dihapus!'); window.location='jadwal_ruang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- PROSES UPDATE DATA LENGKAP (Edit dari Modal Besar) ---
if (isset($_POST['update'])) {
    if (!$allow_edit) { die("Akses Ditolak. Anda tidak memiliki izin mengubah data."); }

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $hari        = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_ke      = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id    = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    $kelas_id    = mysqli_real_escape_string($conn, $_POST['kelas_id']);
    $matkul_id   = mysqli_real_escape_string($conn, $_POST['matkul_id']);
    $ruang_id    = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id    = mysqli_real_escape_string($conn, $_POST['dosen_id']);

    $query_update = "UPDATE jadwal_ruang SET 
                                hari='$hari', 
                                jam_ke='$jam_ke', 
                                waktu_id='$waktu_id', 
                                kelas_id='$kelas_id', 
                                matkul_id='$matkul_id', 
                                ruang_id='$ruang_id', 
                                dosen_id='$dosen_id' 
                            WHERE id='$id'";
    
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Jadwal berhasil diubah!'); window.location='jadwal_ruang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- PROSES UPDATE MINI (Edit Cepat dari Popup Kecil) ---
// Hanya mengupdate: Jam Ke, Waktu, Ruang, Dosen. TIDAK mengubah Hari, Kelas, Matkul.
if (isset($_POST['update_mini'])) {
    if (!$allow_edit) { die("Akses Ditolak. Anda tidak memiliki izin mengubah data."); }

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $jam_ke      = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id    = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    $ruang_id    = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id    = mysqli_real_escape_string($conn, $_POST['dosen_id']);

    $query_update_mini = "UPDATE jadwal_ruang SET 
                                jam_ke='$jam_ke', 
                                waktu_id='$waktu_id', 
                                ruang_id='$ruang_id', 
                                dosen_id='$dosen_id' 
                            WHERE id='$id'";
    
    if (mysqli_query($conn, $query_update_mini)) {
        echo "<script>alert('Jadwal berhasil diubah!'); window.location='jadwal_ruang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- PROSES TAMBAH DATA DARI FORM UTAMA ---
if (isset($_POST['simpan'])) {
    if (!$allow_edit) { die("Akses Ditolak. Anda tidak memiliki izin menambah data."); }

    $hari        = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_ke      = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id    = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    $kelas_id    = mysqli_real_escape_string($conn, $_POST['kelas_id']);
    $matkul_id   = mysqli_real_escape_string($conn, $_POST['matkul_id']);
    $ruang_id    = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id    = mysqli_real_escape_string($conn, $_POST['dosen_id']);

    $cek_duplikat = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE hari='$hari' AND jam_ke='$jam_ke' AND kelas_id='$kelas_id' AND matkul_id='$matkul_id' AND waktu_id='$waktu_id'");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        echo "<script>alert('Jadwal sudah ada!'); window.location='jadwal_ruang.php';</script>";
    } else {
        $query_insert = "INSERT INTO jadwal_ruang (hari, jam_ke, waktu_id, kelas_id, matkul_id, ruang_id, dosen_id) 
                          VALUES ('$hari', '$jam_ke', '$waktu_id', '$kelas_id', '$matkul_id', '$ruang_id', '$dosen_id')";
        if (mysqli_query($conn, $query_insert)) {
            echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location='jadwal_ruang.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- PROSES TAMBAH DATA DARI MODAL ---
if (isset($_POST['simpan_detail'])) {
    if (!$allow_edit) { die("Akses Ditolak. Anda tidak memiliki izin menambah data."); }

    $hari        = mysqli_real_escape_string($conn, $_POST['hari']);
    $jam_ke      = mysqli_real_escape_string($conn, $_POST['jam_ke']);
    $waktu_id    = mysqli_real_escape_string($conn, $_POST['waktu_id']);
    $kelas_id    = mysqli_real_escape_string($conn, $_POST['kelas_id']);
    $matkul_id   = mysqli_real_escape_string($conn, $_POST['matkul_id']);
    $ruang_id    = mysqli_real_escape_string($conn, $_POST['ruang_id']);
    $dosen_id    = mysqli_real_escape_string($conn, $_POST['dosen_id']);

    $cek_duplikat = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE hari='$hari' AND jam_ke='$jam_ke' AND kelas_id='$kelas_id' AND matkul_id='$matkul_id' AND waktu_id='$waktu_id'");
    if (mysqli_num_rows($cek_duplikat) > 0) {
        echo "<script>alert('Jadwal sudah ada!'); window.location='jadwal_ruang.php';</script>";
    } else {
        $query_insert = "INSERT INTO jadwal_ruang (hari, jam_ke, waktu_id, kelas_id, matkul_id, ruang_id, dosen_id) 
                          VALUES ('$hari', '$jam_ke', '$waktu_id', '$kelas_id', '$matkul_id', '$ruang_id', '$dosen_id')";
        if (mysqli_query($conn, $query_insert)) {
            echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location='jadwal_ruang.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- AMBIL DATA JADWAL ---
 $query_jadwal = "SELECT jr.id, jr.hari, jr.jam_ke, jr.waktu_id, w.waktu, jr.kelas_id, jr.matkul_id, jr.ruang_id, jr.dosen_id, k.nama_kelas, m.nama_matkul, r.ruang, d.username
          FROM jadwal_ruang jr
          JOIN kelas k ON jr.kelas_id = k.id
          JOIN mata_kuliah m ON jr.matkul_id = m.id
          JOIN waktu w ON jr.waktu_id = w.id
          JOIN ruangan r ON jr.ruang_id = r.id
          JOIN users d ON jr.dosen_id = d.id  
          ORDER BY FIELD(jr.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), jr.jam_ke ASC";
 $result_jadwal = mysqli_query($conn, $query_jadwal);
 $jadwal_arr = [];
while ($row = mysqli_fetch_assoc($result_jadwal)) {
    $jadwal_arr[] = $row;
}

// --- AMBIL DATA DROPDOWN ---
 $kelas_arr = [];
 $kelas_q = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
while ($row = mysqli_fetch_assoc($kelas_q)) { $kelas_arr[] = $row; }

 $matkul_arr = [];
 $matkul_q = mysqli_query($conn, "SELECT * FROM mata_kuliah ORDER BY nama_matkul ASC");
while ($row = mysqli_fetch_assoc($matkul_q)) { $matkul_arr[] = $row; }

 $waktu_arr = [];
 $waktu_q = mysqli_query($conn, "SELECT * FROM waktu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($waktu_q)) { $waktu_arr[] = $row; }

 $ruang_arr = [];
 $ruang_q = mysqli_query($conn, "SELECT * FROM ruangan ORDER BY ruang ASC");
while ($row = mysqli_fetch_assoc($ruang_q)) { $ruang_arr[] = $row; }

 $dosen_arr = [];
 $dosen_q = mysqli_query($conn, "SELECT id, username FROM users WHERE role='dosen' ORDER BY username ASC");
while ($row = mysqli_fetch_assoc($dosen_q)) { $dosen_arr[] = $row; }

 $list_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

#CRUD Ruangan
if (isset($_POST['tambah_ruang'])) {
    if (!$allow_edit) die("Akses Ditolak.");
    $nama_ruang = mysqli_real_escape_string($conn, $_POST['nama_ruang']);
    mysqli_query($conn, "INSERT INTO ruangan (ruang) VALUES ('$nama_ruang')");
    echo "<script>alert('Ruangan berhasil ditambahkan');window.location='jadwal_ruang.php';</script>";
}

if (isset($_POST['update_ruang'])) {
    if (!$allow_edit) die("Akses Ditolak.");
    $id = $_POST['id_ruang'];
    $nama_ruang = mysqli_real_escape_string($conn, $_POST['nama_ruang']);
    mysqli_query($conn, "UPDATE ruangan SET ruang='$nama_ruang' WHERE id='$id'");
    echo "<script>alert('Ruangan berhasil diupdate');window.location='jadwal_ruang.php';</script>";
}

if (isset($_GET['hapus_ruang'])) {
    if (!$allow_edit) die("Akses Ditolak.");
    $id = $_GET['hapus_ruang'];
    mysqli_query($conn, "DELETE FROM ruangan WHERE id='$id'");
    echo "<script>alert('Ruangan berhasil dihapus');window.location='jadwal_ruang.php';</script>";
}

#CRUD Waktu
if (isset($_POST['tambah_waktu'])) {
    if (!$allow_edit) die("Akses Ditolak.");
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);
    mysqli_query($conn, "INSERT INTO waktu (waktu) VALUES ('$waktu')");
    echo "<script>alert('Waktu berhasil ditambahkan');window.location='jadwal_ruang.php';</script>";
}

if (isset($_POST['update_waktu'])) {
    if (!$allow_edit) die("Akses Ditolak.");
    $id = $_POST['id_waktu'];
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);
    mysqli_query($conn, "UPDATE waktu SET waktu='$waktu' WHERE id='$id'");
    echo "<script>alert('Waktu berhasil diupdate');window.location='jadwal_ruang.php';</script>";
}

if (isset($_GET['hapus_waktu'])) {
    if (!$allow_edit) die("Akses Ditolak.");
    $id = $_GET['hapus_waktu'];
    mysqli_query($conn, "DELETE FROM waktu WHERE id='$id'");
    echo "<script>alert('Waktu berhasil dihapus');window.location='jadwal_ruang.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Ruang Lab</title>
    <style>
        /* --- CSS STYLING --- */
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #009879;
            --accent-color: #f39c12;
            --danger-color: #e74c3c;
            --bg-light: #f4f7f6;
            --text-dark: #333;
            --border-color: #ddd;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--bg-light);
            padding-bottom: 10px;
            margin-top: 0;
        }

        .user-info {
            margin-bottom: 20px;
            text-align: right;
        }

        /* Buttons */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
            display: inline-block;
        }

        .btn-blue { background-color: var(--primary-color); color: #fff; }
        .btn-blue:hover { background-color: #08306b; }
        
        .action-btn { padding: 5px 10px; font-size: 12px; margin-right: 5px; color: #fff; }
        .btn-edit { background-color: var(--accent-color); }
        .btn-delete { background-color: var(--danger-color); }

        /* Forms */
        .form-box {
            background: #f9f9f9;
            border: 1px solid var(--border-color);
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 150px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input, select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        button[type="submit"] {
            padding: 9px 20px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button[type="submit"]:hover { opacity: 0.9; }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e6f7ff; }

        /* Schedule Group */
        .hari-group { margin-bottom: 30px; }
        .hari-header {
            background: var(--secondary-color);
            color: white;
            padding: 10px;
            border-radius: 4px 4px 0 0;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .add-link-mini { color: white; font-weight: normal; font-size: 0.9em; text-decoration: underline; }
        .hari-group table { border-top: none; }
        .hari-group th { background-color: #008f72; }

        /* Modals */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px; 
            border: 1px solid #888;
            width: 90%; 
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 5px;
        }
        .close:hover { color: black; }

        /* Mini Popup */
        .mini-popup {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border: 1px solid #ccc;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            z-index: 1001;
            width: 300px;
            border-radius: 5px;
        }
        .mini-popup h4 { margin-top: 0; color: var(--accent-color); }
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

    <!-- Manajemen Ruangan -->
    <?php if ($allow_edit) { ?>
    <div class="form-box">
        <h3>Manajemen Ruangan</h3>
        <form method="POST">
            <input type="text" name="nama_ruang" placeholder="Nama Ruangan" required>
            <button type="submit" name="tambah_ruang">Tambah</button>
        </form>
        <br>
        <table>
            <thead>
                <tr>
                    <th width="80%">Nama Ruang</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($ruang_arr as $r) { ?>
                <tr>
                    <td><?= htmlspecialchars($r['ruang']); ?></td>
                    <td>
                        <button onclick="editRuang(<?= $r['id']; ?>, '<?= htmlspecialchars($r['ruang'], ENT_QUOTES); ?>')" class="action-btn btn-edit">
                            Edit
                        </button>
                        <a href="jadwal_ruang.php?hapus_ruang=<?= $r['id']; ?>" 
                            onclick="return confirm('Yakin hapus ruangan?')" 
                            class="action-btn btn-delete">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>

    <!-- Manajemen Waktu -->
    <?php if ($allow_edit) { ?>
    <div class="form-box">
        <h3>Manajemen Waktu</h3>
        <form method="POST">
            <input type="text" name="waktu" placeholder="Contoh: 08:00 - 10:00" required>
            <button type="submit" name="tambah_waktu">Tambah</button>
        </form>
        <br>
        <table>
            <thead>
                <tr>
                    <th width="80%">Waktu</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($waktu_arr as $w) { ?>
                <tr>
                    <td><?= htmlspecialchars($w['waktu']); ?></td>
                    <td>
                        <button onclick="editWaktu(<?= $w['id']; ?>, '<?= htmlspecialchars($w['waktu'], ENT_QUOTES); ?>')" class="action-btn btn-edit">
                            Edit
                        </button>
                        <a href="jadwal_ruang.php?hapus_waktu=<?= $w['id']; ?>" 
                           onclick="return confirm('Yakin hapus waktu?')" 
                           class="action-btn btn-delete">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
    
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
                <input type="text" name="jam_ke" placeholder="Contoh: 1-2" required>
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
            <div class="form-group">
                <label>Kelas:</label>
                <select name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas_arr as $row_k) { ?>
                        <option value="<?= $row_k['id']; ?>"><?= $row_k['nama_kelas']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Matakuliah:</label>
                <select name="matkul_id" required>
                    <option value="">-- Pilih MataKuliah --</option>
                    <?php foreach($matkul_arr as $row_m) { ?>
                        <option value="<?= $row_m['id']; ?>"><?= $row_m['nama_matkul']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dosen:</label>
                <select name="dosen_id" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach($dosen_arr as $row_d) { ?>
                        <option value="<?= $row_d['id']; ?>">
                            <?= htmlspecialchars($row_d['username']); ?>
                        </option>
                    <?php }?>
                </select>
            </div>
            <button type="submit" name="simpan">Simpan Jadwal</button>
        </form>
    </div>
    <?php } ?>

    <!-- Tampilan Jadwal -->
    <?php 
    foreach($list_hari as $hari) { 
        $jadwal_hari_ini = array_filter($jadwal_arr, function($item) use ($hari) {
            return $item['hari'] === $hari;
        });
        
        if (count($jadwal_hari_ini) > 0) {
    ?>
        <div class="hari-group">
            <div class="hari-header">
                Jadwal Hari <?= $hari; ?>
                <?php if ($allow_edit) { ?>
                <a href="#" onclick="openAddModal('<?= $hari; ?>')" class="add-link-mini">+ Tambah Jadwal</a>
                <?php } ?>
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="15%">Ruangan</th>
                        <th width="15%">Jam Ke</th>
                        <th width="20%">Waktu</th>
                        <th width="15%">Kelas</th>
                        <th width="30%">Mata Kuliah</th>
                        <th width="15%">Dosen</th>
                        <?php if ($allow_edit) { ?>
                        <th width="5%">Aksi</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($jadwal_hari_ini as $row) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ruang']); ?></td>
                        <td><?= htmlspecialchars($row['jam_ke']); ?></td>
                        <td><?= htmlspecialchars($row['waktu']); ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_kelas']); ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_matkul']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        
                        <?php if ($allow_edit) { ?>
                        <td>
                            <!-- Tombol Edit Cepat (Mini Popup) -->
                            <a href="#" onclick='openMiniPopup(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>); return false;' class="action-btn btn-edit">✎</a>
                            <a href="jadwal_ruang.php?hapus=<?= $row['id']; ?>" class="action-btn btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">✖</a>
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

<!-- Modal Form ADD (Jadwal Detail) -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3 style="color:var(--secondary-color)">Tambah Jadwal</h3>
        <form method="POST" action="">
            <input type="hidden" name="hari" id="add-hari">
            <div class="form-group">
                <label>Ruangan:</label>
                <select name="ruang_id" required>
                    <option value="">-- Pilih Ruangan --</option>
                    <?php foreach($ruang_arr as $row_r) { ?>
                        <option value="<?= $row_r['id']; ?>"><?= htmlspecialchars($row_r['ruang']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Hari:</label>
                <input type="text" id="add-hari-display" disabled style="background: #eee;">
            </div>
            <div class="form-group">
                <label>Jam Ke:</label>
                <input type="text" name="jam_ke" required>
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
            <div class="form-group">
                <label>Kelas:</label>
                <select name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas_arr as $row_k) { ?>
                        <option value="<?= $row_k['id']; ?>"><?= $row_k['nama_kelas']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Matakuliah:</label>
                <select name="matkul_id" required>
                    <option value="">-- Pilih MataKuliah --</option>
                    <?php foreach($matkul_arr as $row_m) { ?>
                        <option value="<?= $row_m['id']; ?>"><?= $row_m['nama_matkul']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dosen:</label>
                <select name="dosen_id" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach($dosen_arr as $row_d) { ?>
                        <option value="<?= $row_d['id']; ?>"><?= $row_d['username']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <br>
            <button type="submit" name="simpan_detail" style="width:100%">Simpan Jadwal</button>
        </form>
    </div>
</div>

<!-- Modal Form EDIT (Jadwal Lengkap) -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3 style="color:var(--accent-color)">Edit Jadwal</h3>
        <form method="POST" action="">
            <input type="hidden" name="id" id="edit-id">
            <div class="form-group">
                <label>Hari:</label>
                <select name="hari" id="edit-hari" required>
                    <?php foreach($list_hari as $h) { ?>
                        <option value="<?= $h; ?>"><?= $h; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Ruangan:</label>
                <select name="ruang_id" id="edit-ruang_id" required>
                    <option value="">-- Pilih Ruangan --</option>
                    <?php foreach($ruang_arr as $row_r) { ?>
                        <option value="<?= $row_r['id']; ?>"><?= htmlspecialchars($row_r['ruang']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jam Ke:</label>
                <input type="text" name="jam_ke" id="edit-jam_ke" required>
            </div>
            <div class="form-group">
                <label>Waktu:</label>
                <select name="waktu_id" id="edit-waktu_id" required>
                    <option value="">-- Pilih Waktu --</option>
                    <?php foreach($waktu_arr as $row_w) { ?>
                        <option value="<?= $row_w['id']; ?>"><?= $row_w['waktu']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kelas:</label>
                <select name="kelas_id" id="edit-kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas_arr as $row_k) { ?>
                        <option value="<?= $row_k['id']; ?>"><?= $row_k['nama_kelas']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Matakuliah:</label>
                <select name="matkul_id" id="edit-matkul_id" required>
                    <option value="">-- Pilih MataKuliah --</option>
                    <?php foreach($matkul_arr as $row_m) { ?>
                        <option value="<?= $row_m['id']; ?>"><?= $row_m['nama_matkul']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dosen:</label>
                <select name="dosen_id" id="edit-dosen_id" required>
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach($dosen_arr as $row_d) { ?>
                        <option value="<?= $row_d['id']; ?>">
                            <?= htmlspecialchars($row_d['username']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <br>
            <button type="submit" name="update" style="width:100%; background-color: var(--accent-color);">Update Jadwal</button>
        </form>
    </div>
</div>

<!-- MINI POPUP EDIT JADWAL (Cepat) -->
<!-- PERUBAHAN: Tombol submit di sini name-nya diubah menjadi 'update_mini' -->
<div id="miniEditPopup" class="mini-popup">
    <div class="mini-popup-content">
        <h4>Edit Cepat Jadwal</h4>
        <form method="POST">
            <input type="hidden" name="id" id="mini-id">
            <label>Jam Ke</label>
            <input type="text" name="jam_ke" id="mini-jam_ke" required>
            <label>Waktu</label>
            <select name="waktu_id" id="mini-waktu_id" required>
                <?php foreach($waktu_arr as $w) { ?>
                    <option value="<?= $w['id']; ?>"><?= $w['waktu']; ?></option>
                <?php } ?>
            </select>
            <label>Ruang</label>
            <select name="ruang_id" id="mini-ruang_id" required>
                <?php foreach($ruang_arr as $r) { ?>
                    <option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['ruang']); ?></option>
                <?php } ?>
            </select>
            <label>Dosen</label>
            <select name="dosen_id" id="mini-dosen_id" required>
                <?php foreach($dosen_arr as $d) { ?>
                    <option value="<?= $d['id']; ?>"><?= htmlspecialchars($d['username']); ?></option>
                <?php } ?>
            </select>
            <div class="mini-btn-group">
                <!-- PERUBAHAN PENTING: name="update_mini" -->
                <button type="submit" name="update_mini" class="btn-save">Update</button>
                <button type="button" onclick="document.getElementById('miniEditPopup').style.display='none'" class="btn-cancel">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT RUANGAN -->
<div id="modalEditRuang" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalEditRuang')">&times;</span>
        <h3>Edit Ruangan</h3>
        <form method="POST">
            <input type="hidden" name="id_ruang" id="edit_id_ruang">
            <label>Nama Ruangan</label>
            <input type="text" name="nama_ruang" id="edit_nama_ruang" required>
            <br><br>
            <button type="submit" name="update_ruang" class="btn btn-blue" style="width:100%">Simpan Perubahan</button>
            <button type="button" onclick="closeModal('modalEditRuang')" class="btn btn-delete" style="background:#777; width:100%; margin-top:5px;">Batal</button>
        </form>
    </div>
</div>

<!-- MODAL EDIT WAKTU -->
<div id="modalEditWaktu" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modalEditWaktu')">&times;</span>
        <h3>Edit Waktu</h3> 
        <form method="POST">
            <input type="hidden" name="id_waktu" id="edit_id_waktu">
            <label>Waktu</label>
            <input type="text" name="waktu" id="edit_waktu" required>
            <br><br>
            <button type="submit" name="update_waktu" class="btn btn-blue" style="width:100%">Simpan Perubahan</button>
            <button type="button" onclick="closeModal('modalEditWaktu')" class="btn btn-delete" style="background:#777; width:100%; margin-top:5px;">Batal</button>
        </form>
    </div>
</div>

<script>
    // --- LOGIKA JAVASCRIPT UNTUK MODAL ---

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    function editRuang(id, nama) {
        document.getElementById('edit_id_ruang').value = id;
        document.getElementById('edit_nama_ruang').value = nama;
        document.getElementById('modalEditRuang').style.display = 'block';
    }

    function editWaktu(id, waktu) {
        document.getElementById('edit_id_waktu').value = id;
        document.getElementById('edit_waktu').value = waktu;
        document.getElementById('modalEditWaktu').style.display = 'block';
    }

    function openAddModal(hari) {
        document.getElementById('add-hari').value = hari;
        document.getElementById('add-hari-display').value = hari;
        document.getElementById('addModal').style.display = 'block';
    }

    function openEditModal(data) {
        document.getElementById('edit-id').value = data.id;
        document.getElementById('edit-hari').value = data.hari;
        document.getElementById('edit-ruang_id').value = data.ruang_id;
        document.getElementById('edit-jam_ke').value = data.jam_ke;
        document.getElementById('edit-waktu_id').value = data.waktu_id;
        document.getElementById('edit-kelas_id').value = data.kelas_id;
        document.getElementById('edit-matkul_id').value = data.matkul_id;
        document.getElementById('edit-dosen_id').value = data.dosen_id;
        document.getElementById('editModal').style.display = 'block';
    }

    function openMiniPopup(data) {
        document.getElementById('mini-id').value = data.id;
        document.getElementById('mini-jam_ke').value = data.jam_ke;
        document.getElementById('mini-waktu_id').value = data.waktu_id;
        document.getElementById('mini-ruang_id').value = data.ruang_id;
        document.getElementById('mini-dosen_id').value = data.dosen_id;
        document.getElementById('miniEditPopup').style.display = 'block';
    }
</script>

</body>
</html>