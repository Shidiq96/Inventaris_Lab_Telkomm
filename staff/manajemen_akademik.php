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
    die("Error: File database.php tidak ditemukan.");
}

if (!isset($conn)) {
    die("Error: Koneksi Database");
}

// --- 3. LOGIKA UPDATE (POST) ---
if (isset($_POST['simpan_kelas'])) {
    $id = intval($_POST['id_kelas']);
    $nama = $conn->real_escape_string($_POST['nama_kelas']);
    
    $sql = "UPDATE kelas SET nama_kelas='$nama' WHERE id='$id'";
    if ($conn->query($sql)) {
        header("Location: manajemen_akademik.php"); 
        exit;
    }
}

if (isset($_POST['simpan_matkul'])) {
    $id = intval($_POST['id_matkul']);
    $nama = $conn->real_escape_string($_POST['nama_matkul']);
    
    $sql = "UPDATE mata_kuliah SET nama_matkul='$nama' WHERE id='$id'";
    if ($conn->query($sql)) {
        header("Location: manajemen_akademik.php");
        exit;
    }
}

// --- 4. LOGIKA HAPUS (GET) ---
if (isset($_GET['hapus_kelas'])) {
    $id = intval($_GET['hapus_kelas']);
    $conn->query("UPDATE peminjaman SET kelas_id = NULL WHERE kelas_id = '$id'");
    $conn->query("DELETE FROM kelas WHERE id='$id'");
    header("Location: manajemen_akademik.php"); exit;
}

if (isset($_GET['hapus_matkul'])) {
    $id = intval($_GET['hapus_matkul']);
    $conn->query("UPDATE peminjaman SET matkul_id = NULL WHERE matkul_id = '$id'");
    $conn->query("DELETE FROM mata_kuliah WHERE id='$id'");
    header("Location: manajemen_akademik.php"); exit;
}

if (isset($_GET['batal_edit_kelas']) || isset($_GET['batal_edit_matkul'])) {
    header("Location: manajemen_akademik.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Akademik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/akademik.css">
</head>
<body>
<div class="container">
    
    <!-- 1. MANAJEMEN KELAS -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h2 style="margin:0;">1. Manajemen Kelas</h2>
            <a href="dashboard.php#tambah_kelas" class="btn btn-blue" style="font-size:13px;">
                <i class="fas fa-plus"></i> Tambah Kelas
            </a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Kelas</th>
                    <th style="width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $qKelas = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
                $no = 1;
                // Menggunakan struktur while : ... endwhile;
                while($r = $qKelas->fetch_assoc()):
                    $is_edit = isset($_GET['edit_kelas']) && $_GET['edit_kelas'] == $r['id'];
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <?php if($is_edit): ?>
                            <form method="post">
                                <input type="hidden" name="id_kelas" value="<?php echo $r['id'] ?>">
                                <input type="text" name="nama_kelas" class="input-edit" value="<?php echo htmlspecialchars($r['nama_kelas']) ?>" required autofocus>
                                <button type="submit" name="simpan_kelas" class="btn btn-green" style="padding:4px 10px; font-size:12px; margin-left:5px;">
                                    <i class="fas fa-save"></i>
                                </button>
                                <a href="manajemen_akademik.php" class="btn btn-warning" style="padding:4px 10px; font-size:12px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </form>
                        <?php else: ?>
                            <span class="badge" style="background-color: #2e7d32; font-size:14px; padding: 8px 15px;">
                                <?php echo htmlspecialchars($r['nama_kelas']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($is_edit): ?>
                            <span style="color:#888; font-size:12px;">(Mode Edit)</span>
                        <?php else: ?>
                            <a href="?edit_kelas=<?php echo $r['id'] ?>" class="btn btn-blue" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?hapus_kelas=<?php echo $r['id'] ?>" class="btn btn-red" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- 2. MANAJEMEN MATA KULIAH (WARNA DINAMIS) -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h2 style="margin:0;">2. Manajemen Mata Kuliah</h2>
            <a href="dashboard.php#tambah_matkul" class="btn btn-blue" style="font-size:13px;">
                <i class="fas fa-plus"></i> Tambah Matkul
            </a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Mata Kuliah</th>
                    <th style="width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $qMatkul = $conn->query("SELECT * FROM mata_kuliah ORDER BY nama_matkul ASC");
                
                // Array Palet Warna
                $colors = array(
                    '#3498db', '#e74c3c', '#f1c40f', '#8e44ad', '#2ecc71', 
                    '#1abc9c', '#d35400', '#7f8c8d', '#27ae60', '#16a085'
                );
                
                $no = 1;
                while($r = $qMatkul->fetch_assoc()):
                    // Logika Warna
                    $bg_color = $colors[($no - 1) % count($colors)];
                    $is_edit = isset($_GET['edit_matkul']) && $_GET['edit_matkul'] == $r['id'];
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <?php if($is_edit): ?>
                            <form method="post">
                                <input type="hidden" name="id_matkul" value="<?php echo $r['id'] ?>">
                                <input type="text" name="nama_matkul" class="input-edit" value="<?php echo htmlspecialchars($r['nama_matkul']) ?>" required autofocus style="border-color: <?php echo $bg_color ?>; color: black; border-width: 2px;">
                                <button type="submit" name="simpan_matkul" class="btn btn-green" style="padding:4px 10px; font-size:12px; margin-left:5px;">
                                    <i class="fas fa-save"></i>
                                </button>
                                <a href="manajemen_akademik.php" class="btn btn-warning" style="padding:4px 10px; font-size:12px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </form>
                        <?php else: ?>
                            <span class="badge" style="background-color: <?php echo $bg_color ?>; font-size: 14px; padding: 8px 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                <?php echo htmlspecialchars($r['nama_matkul']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($is_edit): ?>
                            <span style="color:#888; font-size:12px;">(Mode Edit)</span>
                        <?php else: ?>
                            <a href="?edit_matkul=<?php echo $r['id'] ?>" class="btn btn-blue" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?hapus_matkul=<?php echo $r['id'] ?>" class="btn btn-red" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus mata kuliah ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>