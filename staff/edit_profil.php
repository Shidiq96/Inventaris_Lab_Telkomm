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
    die("Error: Koneksi Database Gagal.");
}

// 3. Ambil Data User Saat Ini
// Kita ambil password hash yang ada sekarang untuk validasi nanti
 $current_username = $_SESSION['user']['username'];
 $user_query = $conn->query("SELECT id, username, password FROM users WHERE username = '$current_username'");

if (!$user_query || $user_query->num_rows == 0) {
    die("Data user tidak ditemukan.");
}

 $user_data = $user_query->fetch_assoc();
 $user_id = $user_data['id'];
 $stored_hash = $user_data['password']; // Ini adalah kode acak di database

 $success_msg = "";
 $error_msg = "";

// 4. LOGIKA UPDATE PROFIL (DENGAN ENKRIPSI PASSWORD)
if (isset($_POST['update_profil'])) {
    $username_baru = $conn->real_escape_string($_POST['username']);
    $password_lama_input = $_POST['password_lama']; // Password lama yang diinput user
    $password_baru = $_POST['password'];          // Password baru yang diinginkan
    $konfirmasi_pass = $_POST['konfirmasi_password'];

    // --- VALIDASI 1: Input Kosong ---
    if (empty($username_baru)) {
        $error_msg = "Username tidak boleh kosong.";
    } 
    // --- VALIDASI 2: Password Lama (Wajib Diisi untuk verifikasi) ---
    elseif (empty($password_lama_input)) {
        $error_msg = "Masukkan Password Lama Anda saat ini untuk verifikasi.";
    }
    // --- VALIDASI 3: Password Baru ---
    elseif (empty($password_baru)) {
        $error_msg = "Password Baru tidak boleh kosong.";
    } elseif ($password_baru !== $konfirmasi_pass) {
        $error_msg = "Konfirmasi Password Baru tidak sesuai.";
    } 
    // --- VALIDASI 4: Cek Ketersediaan Username ---
    else {
        $cek_username = $conn->query("SELECT id FROM users WHERE username = '$username_baru' AND id != '$user_id'");
        if ($cek_username && $cek_username->num_rows > 0) {
            $error_msg = "Username '$username_baru' sudah digunakan oleh user lain.";
        }
    }

    // --- PROSES UPDATE JIKA TIDAK ADA ERROR ---
    if (empty($error_msg)) {
        
        // 1. Verifikasi Password Lama dengan Hash di Database
        if (password_verify($password_lama_input, $stored_hash)) {
            
            // Password Lama Benar! Lanjut Update.
            
            // 2. Enkripsi Password Baru menjadi Hash SQL
            // PASSWORD_DEFAULT menggunakan algoritma bcrypt yang kuat
            $password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);

            // 3. Eksekusi Query Update
            // Kolom 'password' diisi dengan $password_baru_hash (kode acak)
            $sql_update = "UPDATE users SET username='$username_baru', password='$password_baru_hash' WHERE id='$user_id'";

            if ($conn->query($sql_update)) {
                $success_msg = "Profil berhasil diperbarui. Password telah dienkripsi ulang.";
                
                // Update Session Username
                $_SESSION['user']['username'] = $username_baru;
                
            } else {
                $error_msg = "Terjadi kesalahan sistem saat memperbarui data.";
            }

        } else {
            // Password Lama Salah
            $error_msg = "Password Lama yang Anda masukkan SALAH. Gagal memperbarui.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profil Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/edit_profil.css">
</head>
<body>

<div class="card">
    <div class="header-title">
        <i class="fas fa-user-cog fa-3x" style="color: #667eea; margin-bottom: 15px;"></i>
        <h2>Edit Profil</h2>
        <p>Keamanan Terenkripsi</p>
    </div>

    <!-- Notifikasi -->
    <?php if($success_msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $success_msg ?>
        </div>
    <?php endif; ?>
    
    <?php if($error_msg): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <!-- Username -->
        <div class="form-group">
            <label class="form-label">Username Baru</label>
            <input type="text" name="username" class="form-control" 
                   value="<?= htmlspecialchars($user_data['username']) ?>" required autofocus>
        </div>

        <!-- Password Lama (Wajib) -->
        <div class="form-group">
            <label class="form-label">Password Saat Ini</label>
            <input type="password" name="password_lama" class="form-control" placeholder="Verifikasi identitas Anda..." required>
            <small class="small-text">Wajib diisi untuk mengubah password.</small>
        </div>

        <!-- Password Baru -->
        <div class="form-group">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" placeholder="Password baru (Akan dienkripsi)..." required>
        </div>

        <!-- Konfirmasi Password -->
        <div class="form-group">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru..." required>
        </div>

        <button type="submit" name="update_profil" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </form>

    <!-- Tombol Kembali (Button Biasa) -->
    <button type="button" onclick="window.location.href='dashboard.php'" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </button>
</div>

</body>
</html>