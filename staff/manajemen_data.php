<?php
session_start();

// 1. Cek Akses (Hanya Admin & Super Admin yang boleh mengakses halaman ini)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'super_admin'])) {
    header("Location: login.php"); // Redirect ke login jika belum login
    exit();
}

// 2. Koneksi Database
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

// --- LOGIKA CRUD RUANGAN ---

// Tambah Ruangan
if (isset($_POST['tambah_ruang'])) {
    $nama_ruang = mysqli_real_escape_string($conn, $_POST['nama_ruang']);
    mysqli_query($conn, "INSERT INTO ruangan (ruang) VALUES ('$nama_ruang')");
    echo "<script>alert('Ruangan berhasil ditambahkan'); window.location='manajemen_data.php';</script>";
}

// Update Ruangan
if (isset($_POST['update_ruang'])) {
    $id = $_POST['id_ruang'];
    $nama_ruang = mysqli_real_escape_string($conn, $_POST['nama_ruang']);
    mysqli_query($conn, "UPDATE ruangan SET ruang='$nama_ruang' WHERE id='$id'");
    echo "<script>alert('Ruangan berhasil diupdate'); window.location='manajemen_data.php';</script>";
}

// Hapus Ruangan
if (isset($_GET['hapus_ruang'])) {
    $id = $_GET['hapus_ruang'];
    // Opsional: Cek apakah ruangan sedang dipakai di jadwal
    // $cek = mysqli_query($conn, "SELECT * FROM jadwal_ruang WHERE ruang_id='$id'");
    // if(mysqli_num_rows($cek) > 0) { die("Tidak bisa menghapus! Ruangan masih ada di jadwal."); }
    
    mysqli_query($conn, "DELETE FROM ruangan WHERE id='$id'");
    echo "<script>alert('Ruangan berhasil dihapus'); window.location='manajemen_data.php';</script>";
}

// --- LOGIKA CRUD WAKTU ---

// Tambah Waktu
if (isset($_POST['tambah_waktu'])) {
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);
    mysqli_query($conn, "INSERT INTO waktu (waktu) VALUES ('$waktu')");
    echo "<script>alert('Waktu berhasil ditambahkan'); window.location='manajemen_data.php';</script>";
}

// Update Waktu
if (isset($_POST['update_waktu'])) {
    $id = $_POST['id_waktu'];
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);
    mysqli_query($conn, "UPDATE waktu SET waktu='$waktu' WHERE id='$id'");
    echo "<script>alert('Waktu berhasil diupdate'); window.location='manajemen_data.php';</script>";
}

// Hapus Waktu
if (isset($_GET['hapus_waktu'])) {
    $id = $_GET['hapus_waktu'];
    mysqli_query($conn, "DELETE FROM waktu WHERE id='$id'");
    echo "<script>alert('Waktu berhasil dihapus'); window.location='manajemen_data.php';</script>";
}

// --- AMBIL DATA ---
 $ruang_arr = [];
 $ruang_q = mysqli_query($conn, "SELECT * FROM ruangan ORDER BY ruang ASC");
while ($row = mysqli_fetch_assoc($ruang_q)) { $ruang_arr[] = $row; }

 $waktu_arr = [];
 $waktu_q = mysqli_query($conn, "SELECT * FROM waktu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($waktu_q)) { $waktu_arr[] = $row; }

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Ruangan & Waktu</title>
    <style>
        /* CSS SAMA DENGAN HALAMAN UTAMA UNTUK KONSISTENSI */
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

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--bg-light);
            padding-bottom: 10px;
        }

        h2 { margin: 0; color: var(--primary-color); }

        /* Layout Grid untuk 2 Kolom */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .grid-container { grid-template-columns: 1fr; }
        }

        .card {
            background: #f9f9f9;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
        }

        .card h3 { margin-top: 0; color: var(--secondary-color); }

        /* Forms */
        .form-inline {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: flex-end;
        }
        .form-group { flex: 1; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }

        /* Buttons */
        .btn {
            padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; color: white; font-size: 14px;
        }
        .btn-blue { background-color: var(--primary-color); }
        .btn-blue:hover { background-color: #08306b; }
        .btn-green { background-color: var(--secondary-color); }
        .btn-edit { background-color: var(--accent-color); padding: 5px 10px; font-size: 12px; margin-right: 5px; }
        .btn-delete { background-color: var(--danger-color); padding: 5px 10px; font-size: 12px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid var(--border-color); padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: var(--primary-color); color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }

        /* Modal */
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 400px; border-radius: 8px; position: relative;
        }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h2>Manajemen Data</h2>
        <a href="jadwal_ruang.php" class="btn btn-blue">Kembali ke Jadwal</a>
    </div>

    <div class="grid-container">
        
        <!-- KOLOM 1: MANAJEMEN RUANGAN -->
        <div class="card">
            <h3>Data Ruangan</h3>
            
            <!-- Form Tambah Ruang -->
            <form method="POST" class="form-inline">
                <div class="form-group">
                    <label>Nama Ruangan</label>
                    <input type="text" name="nama_ruang" placeholder="Contoh: Lab. Komputer 1" required>
                </div>
                <button type="submit" name="tambah_ruang" class="btn btn-green">Tambah</button>
            </form>

            <!-- Tabel Ruangan -->
            <table>
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="60%">Nama Ruang</th>
                        <th width="30%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ruang_arr as $r) { ?>
                    <tr>
                        <td><?= $r['id']; ?></td>
                        <td><?= htmlspecialchars($r['ruang']); ?></td>
                        <td>
                            <button onclick="editRuang(<?= $r['id']; ?>, '<?= htmlspecialchars($r['ruang'], ENT_QUOTES); ?>')" class="btn btn-edit">Edit</button>
                            <a href="?hapus_ruang=<?= $r['id']; ?>" onclick="return confirm('Yakin ingin menghapus ruangan ini?')" class="btn btn-delete">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- KOLOM 2: MANAJEMEN WAKTU -->
        <div class="card">
            <h3>Data Waktu</h3>
            
            <!-- Form Tambah Waktu -->
            <form method="POST" class="form-inline">
                <div class="form-group">
                    <label>Rentang Waktu</label>
                    <input type="text" name="waktu" placeholder="Contoh: 08:00 - 10:00" required>
                </div>
                <button type="submit" name="tambah_waktu" class="btn btn-green">Tambah</button>
            </form>

            <!-- Tabel Waktu -->
            <table>
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="60%">Waktu</th>
                        <th width="30%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($waktu_arr as $w) { ?>
                    <tr>
                        <td><?= $w['id']; ?></td>
                        <td><?= htmlspecialchars($w['waktu']); ?></td>
                        <td>
                            <button onclick="editWaktu(<?= $w['id']; ?>, '<?= htmlspecialchars($w['waktu'], ENT_QUOTES); ?>')" class="btn btn-edit">Edit</button>
                            <a href="?hapus_waktu=<?= $w['id']; ?>" onclick="return confirm('Yakin ingin menghapus waktu ini?')" class="btn btn-delete">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

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
            <button type="submit" name="update_ruang" class="btn btn-green" style="width:100%">Simpan Perubahan</button>
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
            <button type="submit" name="update_waktu" class="btn btn-green" style="width:100%">Simpan Perubahan</button>
            <button type="button" onclick="closeModal('modalEditWaktu')" class="btn btn-delete" style="background:#777; width:100%; margin-top:5px;">Batal</button>
        </form>
    </div>
</div>

<script>
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
</script>

</body>
</html>