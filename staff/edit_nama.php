<?php
session_start();

// 1. Cek Akses
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'dosen', 'super_admin'])) {
    die('Akses Ditolak');
}

// 2. Koneksi Database
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: File database.php tidak ditemukan");
}

if (!isset($conn)) {
    die("Error: Koneksi Database Gagal");
}

// 3. Ambil Nama Barang dari URL
 $nama_barang = isset($_GET['nama']) ? $_GET['nama'] : '';
if (empty($nama_barang)) {
    echo "<script>alert('Nama Barang tidak ditemukan'); window.location.href='dashboard.php';</script>";
    exit;
}

// Variabel dinamis untuk file ini
 $current_file = $_SERVER['PHP_SELF'];

// --- 1. UPDATE NAMA BARANG (Massal) ---
if (isset($_POST['update_nama'])) {
    $nama_baru = $conn->real_escape_string($_POST['nama_barang']);
    $nama_lama = $conn->real_escape_string($_GET['nama']);

    if (!empty($nama_baru)) {
        $stmt = $conn->prepare("UPDATE barang SET nama_barang = ? WHERE nama_barang = ?");
        $stmt->bind_param("ss", $nama_baru, $nama_lama);
        $stmt->execute();
        
        // Redirect ke file ini sendiri dengan nama barang BARU
        echo "<script>window.location.href='$current_file?nama=" . urlencode($nama_baru) . "';</script>";
        exit;
    }
}

// --- 1.5 TAMBAH UNIT BARU (FITUR BARU) ---
if (isset($_POST['tambah_unit'])) {
    $kode_baru = $conn->real_escape_string($_POST['kode_baru']);
    $nama_sekarang = $conn->real_escape_string($_GET['nama']); // Ambil nama barang saat ini

    if (!empty($kode_baru)) {
        // Default kondisi baru adalah 'Baik'
        $kondisi_default = 'Baik';
        $stmt = $conn->prepare("INSERT INTO barang (nama_barang, nomor_unik, kondisi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama_sekarang, $kode_baru, $kondisi_default);
        $stmt->execute();

        // Redirect untuk refresh tabel
        echo "<script>window.location.href='$current_file?nama=" . urlencode($nama_sekarang) . "';</script>";
        exit;
    }
}

// --- 2. UPDATE SATU UNIT (Kode Unik) ---
if (isset($_POST['simpan_kode'])) {
    $id = intval($_POST['id']);
    $kode = $conn->real_escape_string($_POST['nomor_unik']);

    $stmt = $conn->prepare("UPDATE barang SET nomor_unik = ? WHERE id = ?");
    $stmt->bind_param("si", $kode, $id);
    $stmt->execute();
    
    echo "<script>window.location.href='$current_file?nama=" . urlencode($_GET['nama']) . "';</script>";
    exit;
}

// --- 3. UPDATE SATU UNIT (Kondisi) ---
if (isset($_POST['simpan_kondisi'])) {
    $id = intval($_POST['id']);
    $kondisi = $_POST['kondisi'];

    $stmt = $conn->prepare("UPDATE barang SET kondisi = ? WHERE id = ?");
    $stmt->bind_param("si", $kondisi, $id);
    $stmt->execute();
    
    echo "<script>window.location.href='$current_file?nama=" . urlencode($_GET['nama']) . "';</script>";
    exit;
}

// --- 4. HAPUS SATU UNIT ---
if (isset($_GET['hapus_unit'])) {
    $id = intval($_GET['hapus_unit']);
    $conn->query("DELETE FROM peminjaman WHERE barang_id=$id");
    $conn->query("DELETE FROM barang WHERE id=$id");
    
    echo "<script>window.location.href='$current_file?nama=" . urlencode($_GET['nama']) . "';</script>";
    exit;
}

// 5. Ambil Data Unit
 $stmt = $conn->prepare("SELECT * FROM barang WHERE nama_barang = ?");
 $stmt->bind_param("s", $nama_barang);
 $stmt->execute();
 $result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang: <?= htmlspecialchars($nama_barang) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/edit_nama.css">
    <style>
@media print {
    body * {
        visibility: hidden;
    }
    .barcode-content, .barcode-content * {
        visibility: visible;
    }
    .barcode-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
    }
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Edit Detail Barang</h2>
        <a href="dashboard.php" class="btn btn-blue">&larr; Kembali Dashboard</a>
    </div>

    <!-- 1. FORM UBAH NAMA BARANG (Massal) -->
    <div class="card">
        <h3>Ganti Nama Barang (Semua Unit)</h3>
        <form method="post" action="<?= $current_file ?>?nama=<?= urlencode($nama_barang) ?>">
            <div style="display:flex; gap:10px; align-items:center;">
                <label>Nama Barang Saat Ini: <strong><?= htmlspecialchars($nama_barang) ?></strong></label>
                <input type="text" name="nama_barang" placeholder="Masukkan Nama Baru" required style="flex:1;">
                <button type="submit" name="update_nama" class="btn btn-blue">Simpan Nama</button>
            </div>
        </form>
    </div>

    <!-- 2. TABEL EDIT PER UNIT -->
    <div class="card">
        <h3>Detail Unit (Edit Kode & Kondisi)</h3>
        
        <!-- FITUR BARU: FORM TAMBAH UNIT -->
        <div class="add-unit-box">
            <form method="post" action="<?= $current_file ?>?nama=<?= urlencode($nama_barang) ?>" style="display: flex; gap: 10px; width: 100%; align-items: center;">
                <div style="flex: 0 0 120px;"><strong>Tambah Unit:</strong></div>
                <input type="text" name="kode_baru" placeholder="Cth:LP-001" required style="flex: 1;">
                <button type="submit" name="tambah_unit" class="btn btn-green">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Kode Unik (Edit)</th>
                    <th width="20%">Kondisi (Edit)</th>
                    <th width="10%">Status</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    // Cek status peminjaman
                    $cekPinjam = $conn->query("SELECT tanggal_pinjam FROM peminjaman WHERE barang_id=".$row['id']." AND status_peminjaman='dipinjam' ORDER BY id DESC LIMIT 1");
                    $isDipinjam = ($cekPinjam && $cekPinjam->num_rows > 0);
                    $tanggal_pinjam = $isDipinjam ? $cekPinjam->fetch_assoc()['tanggal_pinjam'] : '';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <form method="post" action="<?= $current_file ?>?nama=<?= urlencode($nama_barang) ?>">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="text" name="nomor_unik" value="<?= htmlspecialchars($row['nomor_unik']) ?>" required>
                            <button type="submit" name="simpan_kode" class="btn btn-blue btn-small">Simpan</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" action="<?= $current_file ?>?nama=<?= urlencode($nama_barang) ?>">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="kondisi" onchange="this.form.submit()">
                                <option value="Baik" <?= $row['kondisi']=='Baik'?'selected':'' ?>>Baik</option>
                                <option value="Rusak Ringan" <?= $row['kondisi']=='Rusak Ringan'?'selected':'' ?>>Rusak Ringan</option>
                                <option value="Rusak Berat" <?= $row['kondisi']=='Rusak Berat'?'selected':'' ?>>Rusak Berat</option>
                                <option value="Perbaikan" <?= $row['kondisi']=='Perbaikan'?'selected':'' ?>>Perbaikan</option>
                            </select>
                            <input type="hidden" name="simpan_kondisi" value="1">
                        </form>
                    </td>
                    <td>
                        <?= $isDipinjam ? "<span style='color:red; font-weight:bold;'>Dipinjam</span><br><small>Tgl: ".($tanggal_pinjam ? date('d-m-Y', strtotime($tanggal_pinjam)) : '-')."</small>" : "<span style='color:green;'>Tersedia</span>" ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-blue btn-small" onclick="showBarcode('<?= htmlspecialchars($row['nomor_unik']) ?>')"><i class="fas fa-barcode"></i>Barcode</button>
                        <a href="?hapus_unit=<?= $row['id'] ?>&nama=<?= urlencode($nama_barang) ?>" onclick="return confirmDelete()" class="btn btn-red btn-small">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Barcode Modal Popup -->
    <div id="barcodeModal" class="barcode-modal" style="display:none;">
        <div class="barcode-overlay" onclick="closeBarcode()" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1000;"></div>
        <div class="barcode-content" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:32px 24px;border-radius:8px;box-shadow:0 2px 16px rgba(0,0,0,0.2);z-index:1001;min-width:320px;text-align:center;">
            <!-- <button class="close" onclick="closeBarcode()" style="position:absolute;top:12px;right:16px;font-size:24px;background:none;border:none;cursor:pointer;">&times;</button> -->
            <img id="barcodeImage" src="" alt="Barcode" style="max-width:100%;margin:16px 0;">
            <br>
            <button onclick="window.print()" class="btn btn-green">Cetak</button>
        </div>
    </div>
</div>
</body>
<script>
function showBarcode(kode) {
    const modal = document.getElementById('barcodeModal');
    const img = document.getElementById('barcodeImage');

    img.src = 'generate_barcode.php?kode=' + encodeURIComponent(kode);
    modal.style.display = 'flex';
}

function closeBarcode() {
    document.getElementById('barcodeModal').style.display = 'none';
}
</script>
<script src="../js/edit_nama.js"></script>
</html>