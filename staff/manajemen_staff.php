<?php
session_start();

// --- 1. Pengecekan Keamanan (Hanya Super Admin) ---
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'super_admin') {
    die('<div style="padding:50px; text-align:center; font-family:sans-serif; background:#fffbe6;">
            <h1 style="color:#d32f2f;"><i class="fas fa-exclamation-triangle"></i> Akses Ditolak</h1>
            <p>Halaman ini dikhususkan untuk <strong>Super Admin</strong>.</p>
            <p><a href="dashboard.php" style="text-decoration:none; color:#0d47a1; font-weight:bold;">&larr; Kembali ke Dashboard</a></p>
         </div>');
}

// --- 2. Koneksi Database ---
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
}

if (!isset($conn)) {
    die("Error: Koneksi Database Gagal.");
}

 $success_msg = "";
 $error_msg = "";

// --- 3. LOGIKA: TAMBAH STAFF BARU ---
if (isset($_POST['tambah_staff'])) {
    $user = $conn->real_escape_string($_POST['username']);
    $pass = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role']);

    // Validasi input kosong
    if (empty($user) || empty($pass) || empty($role)) {
        $error_msg = "Semua kolom wajib diisi!";
    } else {
        // Cek apakah username sudah terpakai
        $cek_user = $conn->query("SELECT id FROM users WHERE username = '$user'");
        if ($cek_user->num_rows > 0) {
            $error_msg = "Username <strong>'$user'</strong> sudah digunakan.";
        } else {
            // Enkripsi Password
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, password, role) VALUES ('$user', '$pass_hash', '$role')";
            
            if ($conn->query($sql)) {
                $success_msg = "Staff baru ($role) berhasil ditambahkan.";
            } else {
                $error_msg = "Gagal menambahkan staff: " . $conn->error;
            }
        }
    }
}

// --- 4. LOGIKA: EDIT STAFF ---
if (isset($_POST['edit_staff'])) {
    $id = intval($_POST['id']);
    $user_baru = $conn->real_escape_string($_POST['username']); // PASTIKAN HTML NAME ADALAH 'username'
    $role = $conn->real_escape_string($_POST['role']);
    $pass_baru = $_POST['password'];

    if (empty($user_baru) || empty($role)) {
        $error_msg = "Username dan Role wajib diisi.";
    } else {
        if (!empty($pass_baru)) {
            $pass_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username='$user_baru', role='$role', password='$pass_hash' WHERE id='$id'";
        } else {
            $sql = "UPDATE users SET username='$user_baru', role='$role' WHERE id='$id'";
        }

        if ($conn->query($sql)) {
            $success_msg = "Data staff berhasil diperbarui.";
        } else {
            $error_msg = "Gagal mengupdate data: " . $conn->error;
        }
    }
}

// --- 5. LOGIKA: HAPUS STAFF ---
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    if ($id == $_SESSION['user']['id']) {
        $error_msg = "Anda tidak bisa menghapus akun Anda sendiri!";
    } else {
        $sql_hapus = "DELETE FROM users WHERE id='$id'";
        if ($conn->query($sql_hapus)) {
            $success_msg = "Staff berhasil dihapus.";
            echo "<script>window.location='manajemen_staff.php';</script>"; 
        } else {
            $error_msg = "Gagal menghapus staff: " . $conn->error;
        }
    }
}

// --- 6. AMBIL DATA STAFF ---
 $qStaff = $conn->query("SELECT * FROM users WHERE role IN ('admin', 'dosen') ORDER BY role DESC, username ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Staff - Super Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/manajemen_staff.css">
    <link rel="stylesheet" href="../css/staff/dashboard.css">
</head>
<body>

<div class="container">
    <div class="header">
        <a href="dashboard.php" class="btn btn-secondary" style="background: #6c757d;"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <?php if($success_msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= $error_msg ?></div>
    <?php endif; ?>

    <!-- FORM TAMBAH STAFF -->
    <div class="card" style="border-left: 5px solid #28a745;">
        <h3><i class="fas fa-user-plus"></i> Tambah Staff Baru</h3>
        <form method="post">
            <div class="row">
                <!-- Pastikan name disini adalah 'username' -->
                <input type="text" name="username" placeholder="Username" required style="flex:2;">
                <input type="password" name="password" placeholder="Password" required style="flex:1;">
                <select name="role" required style="flex:1;">
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                </select>
                <button type="submit" name="tambah_staff" class="btn btn-green"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>

    <!-- TABEL STAFF -->
    <div class="card">
        <h3>Daftar Staff Aktif</h3>
        <table>
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>Username</th>
                    <th width="100">Role</th>
                    <th width="150" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if($qStaff && $qStaff->num_rows > 0) {
                    while($row = $qStaff->fetch_assoc()){
                        $role_badge = ($row['role'] == 'admin') ? '<span class="badge badge-blue">Admin</span>' : '<span class="badge badge-orange">Dosen</span>';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                    <td><?= $role_badge ?></td>
                    <td style="text-align:center;">
                        <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)" class="btn btn-blue btn-small"><i class="fas fa-edit"></i> Edit</button>
                        <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus staff ini?')" class="btn btn-red btn-small"><i class="fas fa-trash"></i> Hapus</a>
                    </td>
                </tr>
                <?php } 
                } else { echo "<tr><td colspan='4' align='center'>Belum ada data staff.</td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL EDIT STAFF -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal-btn" onclick="closeEditModal()">&times;</span>
        <h3 style="margin-top:0; color: #0d47a1;">Edit Data Staff</h3>
        <form method="post" autocomplete="off">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group-modal">
                <label>Username</label>
                <input type="text" name="username" id="edit_username" required>
            </div>
            <div class="form-group-modal">
                <label>Role</label>
                <select name="role" id="edit_role">
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>
            <div class="form-group-modal">
                <label>Password Baru</label>
                <input type="password" name="password" id="edit_pass" placeholder="Kosongkan jika tidak ingin mengubah">
                <small style="color:#888;">Isi password baru hanya jika ingin menggantinya.</small>
            </div>
            <div style="text-align:right; margin-top: 20px;">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary" style="background:#6c757d; border:none; color:white; padding: 8px 15px; border-radius:4px;">Batal</button>
                <button type="submit" name="edit_staff" class="btn btn-blue" style="border:none; padding: 8px 15px; border-radius:4px;">Update</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/manajemen_staff.js"></script>
</body>
</html>