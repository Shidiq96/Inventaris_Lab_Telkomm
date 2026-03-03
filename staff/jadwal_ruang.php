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
    // Cek Izin: Jika dosen, tolak
    if (!$allow_edit) { die("Akses Ditolak. Anda tidak memiliki izin menghapus data."); }
    
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query_hapus = "DELETE FROM jadwal_ruang WHERE id='$id'";
    if (mysqli_query($conn, $query_hapus)) {
        echo "<script>alert('Jadwal berhasil dihapus!'); window.location='jadwal_ruang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- PROSES UPDATE DATA (Edit) ---
if (isset($_POST['update'])) {
    // Cek Izin: Jika dosen, tolak
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

// --- PROSES TAMBAH DATA DARI FORM UTAMA ---
if (isset($_POST['simpan'])) {
    // Cek Izin: Jika dosen, tolak
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
    // Cek Izin: Jika dosen, tolak
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
// Tambah
if (isset($_POST['tambah_ruang'])) {
    if (!$allow_edit) die("Akses Ditolak.");

    $nama_ruang = mysqli_real_escape_string($conn, $_POST['nama_ruang']);

    mysqli_query($conn, "INSERT INTO ruangan (ruang) VALUES ('$nama_ruang')");
    echo "<script>alert('Ruangan berhasil ditambahkan');window.location='jadwal_ruang.php';</script>";
}

// Update
if (isset($_POST['update_ruang'])) {
    if (!$allow_edit) die("Akses Ditolak.");

    $id = $_POST['id_ruang'];
    $nama_ruang = mysqli_real_escape_string($conn, $_POST['nama_ruang']);

    mysqli_query($conn, "UPDATE ruangan SET ruang='$nama_ruang' WHERE id='$id'");
    echo "<script>alert('Ruangan berhasil diupdate');window.location='jadwal_ruang.php';</script>";
}

// Hapus
if (isset($_GET['hapus_ruang'])) {
    if (!$allow_edit) die("Akses Ditolak.");

    $id = $_GET['hapus_ruang'];
    mysqli_query($conn, "DELETE FROM ruangan WHERE id='$id'");
    echo "<script>alert('Ruangan berhasil dihapus');window.location='jadwal_ruang.php';</script>";
}

#CRUD Waktu
// Tambah
if (isset($_POST['tambah_waktu'])) {
    if (!$allow_edit) die("Akses Ditolak.");

    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);

    mysqli_query($conn, "INSERT INTO waktu (waktu) VALUES ('$waktu')");
    echo "<script>alert('Waktu berhasil ditambahkan');window.location='jadwal_ruang.php';</script>";
}

// Update
if (isset($_POST['update_waktu'])) {
    if (!$allow_edit) die("Akses Ditolak.");

    $id = $_POST['id_waktu'];
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);

    mysqli_query($conn, "UPDATE waktu SET waktu='$waktu' WHERE id='$id'");
    echo "<script>alert('Waktu berhasil diupdate');window.location='jadwal_ruang.php';</script>";
}

// Hapus
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
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; border-bottom: 2px solid #009879; padding-bottom: 10px; margin-bottom: 30px; }
        
        /* Header & Buttons */
        .user-info { text-align: right; margin-bottom: 15px; }
        .btn { 
            padding: 8px 15px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-blue { background: #0d47a1; }
        .btn-blue:hover { background: #002171; }
        
        .action-btn { padding: 5px 10px; font-size: 12px; text-decoration: none; color: white; border-radius: 3px; }
        .btn-edit { background-color: #f39c12; }
        .btn-edit:hover { background-color: #d68910; }
        .btn-delete { background-color: #e74c3c; }
        .btn-delete:hover { background-color: #c0392b; }

        /* Group & Table Style */
        .hari-group { margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .hari-header { background-color: #009879; color: white; padding: 10px 15px; font-size: 18px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .add-link-mini { font-size: 12px; background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 4px; color: white; text-decoration: none; }
        .add-link-mini:hover { background: rgba(255,255,255,0.4); }

        table { width: 100%; border-collapse: collapse; }
        th { padding: 10px; text-align: left; background-color: #f8f9fa; color: #555; font-size: 14px; border-bottom: 2px solid #ddd; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f1f1f1; }
        
        /* Form Box */
        .form-box { background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 5px solid #0d47a1; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 100px; font-weight: bold; color: #333; }
        input, select { padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; background-color: #009879; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #007f6b; }

        /* Modal Style */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 8px; position: relative; }
        .close { position: absolute; right: 20px; top: 10px; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: red; }

.mini-popup {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
}

.mini-popup-content {
    background: white;
    width: 350px;
    padding: 20px;
    border-radius: 8px;
    margin: 10% auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.mini-popup-content h4 {
    margin-top: 0;
    color: #f39c12;
}

.mini-popup-content label {
    display: block;
    margin-top: 10px;
    font-size: 13px;
    font-weight: bold;
}

.mini-popup-content input,
.mini-popup-content select {
    width: 100%;
    padding: 6px;
    margin-top: 3px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.mini-btn-group {
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
}

.btn-save {
    background: #f39c12;
    color: white;
}

.btn-cancel {
    background: #999;
    color: white;
}
    </style>
</head>
<body>

<div class="container">
    <div class="user-info"> 
        <a href="dashboard.php" class="btn btn-blue">Kembali ke Dashboard</a>
    </div>

    <h2>Jadwal Penggunaan Ruang Lab</h2>

    <?php if ($allow_edit) { ?>
    <div class="form-box">
        <h3 style="color:#0d47a1;">Manajemen Ruangan</h3>

        <form method="POST">
            <input type="text" name="nama_ruang" placeholder="Nama Ruangan" required>
            <button type="submit" name="tambah_ruang">Tambah</button>
        </form>

        <br>

        <table>
            <tr>
                <th>Nama Ruang</th>
                <th>Aksi</th>
            </tr>
            <?php foreach($ruang_arr as $r) { ?>
            <tr>
                <td><?= htmlspecialchars($r['ruang']); ?></td>
                <td>
                    <button onclick="editRuang(<?= $r['id']; ?>, '<?= htmlspecialchars($r['ruang']), ENT_QUOTES; ?>')" class="action-btn btn-edit">
                        Edit
                    </button>
                    <a href="jadwal_ruang.php?hapus_ruang=<?= $r['id']; ?>" 
                        onclick="return confirm('Yakin hapus ruangan?')" 
                        class="action-btn btn-delete">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>

    <?php if ($allow_edit) { ?>
    <div class="form-box">
        <h3 style="color:#0d47a1;">Manajemen Waktu</h3>

        <form method="POST">
            <input type="text" name="waktu" placeholder="Contoh: 08:00 - 10:00" required>
            <button type="submit" name="tambah_waktu">Tambah</button>
        </form>

        <br>

        <table>
            <tr>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
            <?php foreach($waktu_arr as $w) { ?>
            <tr>
                <td><?= htmlspecialchars($w['waktu']); ?></td>
                <td>
                    <button onclick="editWaktu(<?= $w['id']; ?>, '<?= htmlspecialchars($w['waktu']), ENT_QUOTES; ?>')" class="action-btn btn-edit">
                        Edit
                    </button>

                    <a href="jadwal_ruang.php?hapus_waktu=<?= $w['id']; ?>" 
                       onclick="return confirm('Yakin hapus waktu?')" 
                       class="action-btn btn-delete">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>
    
    <!-- Form Input Hanya muncul jika $allow_edit = true -->
    <?php if ($allow_edit) { ?>
    <div class="form-box">
        <h3 style="margin-top:0; color:#0d47a1;">Tambah Jadwal Baru</h3>
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

    <!-- Tampilan Jadwal Per Grup Hari -->
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
                <!-- Tombol Tambah Cepat Hanya muncul jika $allow_edit = true -->
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
                        <!-- Kolom Aksi Hanya muncul jika $allow_edit = true -->
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
                        
                        <!-- Tombol Edit/Delete Hanya muncul jika $allow_edit = true -->
                        <?php if ($allow_edit) { ?>
                        <td>
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

<!-- Modal Form ADD (Muncul jika boleh edit, tapi tersembunyi via CSS default) -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h3 style="margin-top:0; color:#009879;">Tambah Jadwal</h3>
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
                <br><br>
            <button type="submit" name="simpan_detail" style="background-color: #009879;">Simpan Jadwal</button>
        </form>
    </div>
</div>

<!-- Modal Form EDIT -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3 style="margin-top:0; color:#f39c12;">Edit Jadwal</h3>
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
                <br><br>
            <button type="submit" name="update" style="background-color: #f39c12;">Update Jadwal</button>
        </form>
    </div>
</div>

<!-- MINI POPUP EDIT JADWAL -->
<div id="miniEditPopup" class="mini-popup">
    <div class="mini-popup-content">
        <h4>Edit Jadwal</h4>
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
                <button type="submit" name="update" class="btn-save">Update</button>
                <button type="button" onclick="closeMiniPopup()" class="btn-cancel">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal(hari) {
        document.getElementById('add-hari').value = hari;
        document.getElementById('add-hari-display').value = hari;
        document.getElementById('addModal').style.display = 'block';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    // Modal edit untuk jadwal per grup hari
    function openEditJadwalModal(data) {
        document.getElementById('edit-jadwal-id').value = data.id;
        document.getElementById('edit-jadwal-hari').value = data.hari;
        document.getElementById('edit-jadwal-jam_ke').value = data.jam_ke;
        document.getElementById('edit-jadwal-waktu_id').value = data.waktu_id;
        document.getElementById('edit-jadwal-ruang_id').value = data.ruang_id;
        document.getElementById('edit-jadwal-kelas_id').value = data.kelas_id;
        document.getElementById('edit-jadwal-matkul_id').value = data.matkul_id;
        document.getElementById('edit-jadwal-dosen_id').value = data.dosen_id;
        document.getElementById('modalEditJadwal').style.display = 'block';
    }

    function closeEditJadwalModal() {
        document.getElementById('modalEditJadwal').style.display = 'none';
    }

    // Modal edit ruang
    function editRuang(id, nama) {
        document.getElementById('edit_id_ruang').value = id;
        document.getElementById('edit_nama_ruang').value = nama;
        document.getElementById('modalEditRuang').style.display = 'block';
    }

    // Modal edit waktu
    function editWaktu(id, waktu) {
        document.getElementById('edit_id_waktu').value = id;
        document.getElementById('edit_waktu').value = waktu;
        document.getElementById('modalEditWaktu').style.display = 'block';
    }

    function closeModal(Id) {
        document.getElementById(Id).style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            closeAddModal();
        }
        if (event.target == document.getElementById('modalEditJadwal')) {
            closeEditJadwalModal();
        }
        if (event.target == document.getElementById('modalEditRuang')) {
            closeModal('modalEditRuang');
        }
        if (event.target == document.getElementById('modalEditWaktu')) {
            closeModal('modalEditWaktu');
        }
    }

    function openMiniPopup(data) {
        console.log("CLICKED", data);
        document.getElementById('mini-id').value = data.id;
        document.getElementById('mini-jam_ke').value = data.jam_ke;
        document.getElementById('mini-waktu_id').value = data.waktu_id;
        document.getElementById('mini-ruang_id').value = data.ruang_id;
        document.getElementById('mini-dosen_id').value = data.dosen_id;

        document.getElementById('miniEditPopup').style.display = 'block';
    }

    function closeMiniPopup() {
        document.getElementById('miniEditPopup').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        if (e.target == document.getElementById('miniEditPopup')) {
            closeMiniPopup();
        }
        
    });
</script>

<div id="modalEditRuang" class="modal">
    <div class="modal-content">
        <h3>Edit Ruangan</h3>
        <form method="POST">
            <input type="hidden" name="id_ruang" id="edit_id_ruang">
            <input type="text" name="nama_ruang" id="edit_nama_ruang" required>
            <br><br>
            <button type="submit" name="update_ruang">Simpan</button>
            <button type="button" onclick="closeModal('modalEditRuang')">Batal</button>
        </form>
    </div>
</div>

<div id="modalEditWaktu" class="modal">
    <div class="modal-content">
        <h3>Edit Waktu</h3>
        <form method="POST">
            <input type="hidden" name="id_waktu" id="edit_id_waktu">
            <input type="text" name="waktu" id="edit_waktu" required>
            <br><br>
            <button type="submit" name="update_waktu">Simpan</button>
            <button type="button" onclick="closeModal('modalEditWaktu')">Batal</button>
        </form>
    </div>
</div>
</body>
</html>