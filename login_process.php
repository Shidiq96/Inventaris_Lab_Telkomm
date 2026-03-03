<?php
session_start();
include "database.php"; 

// --- 1. HANDLER AJAX (Untuk Dropdown Mata Kuliah) ---
if (isset($_GET['get_matkul'])) {
    if (ob_get_length()) ob_clean();
    $result = $conn->query("SELECT * FROM mata_kuliah ORDER BY nama_matkul ASC");
    if ($result && $result->num_rows > 0) {
        echo "<option value=''>-- Pilih Mata Kuliah --</option>";
        while ($m = $result->fetch_assoc()) {
            echo "<option value='{$m['id']}'>{$m['nama_matkul']}</option>";
        }
    } else {
        echo "<option value=''>KOSONG</option>";
    }
    exit; 
}

// --- 2. LOGIKA LOGIN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- A. LOGIN MAHASISWA ---
    if (isset($_POST['login_mhs'])) {
        $kelas_id = intval($_POST['kelas_id']);
        $matkul_id = intval($_POST['matkul_id']);
        $stmt = $conn->prepare("SELECT k.nama_kelas, mk.nama_matkul FROM kelas k, mata_kuliah mk WHERE k.id=? AND mk.id=?");
        $stmt->bind_param("ii", $kelas_id, $matkul_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();
            // Ambil ajaran_semester dari settings
            $qSet = $conn->query("SELECT ajaran_semester FROM app_settings WHERE id=1");
            $ajaran_semester = '2023/2024 Ganjil';
            if($qSet && $qSet->num_rows > 0) {
                $setting = $qSet->fetch_assoc();
                $ajaran_semester = $setting['ajaran_semester'];
            }
            $_SESSION['user'] = [
                'role' => 'mahasiswa',
                'kelas_id' => $kelas_id,
                'matkul_id' => $matkul_id,
                'kelas' => $data['nama_kelas'],
                'mata_kuliah' => $data['nama_matkul'],
                'ajaran_semester' => $ajaran_semester
            ];
            header("Location: mahasiswa/dashboard.php");
            exit;
        } else {
            $error_mhs = "Data tidak valid!";
        }
    }

    // --- B. LOGIN STAFF (DOSEN) ---
    if (isset($_POST['login_staff'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        // Validasi captcha
        if (!isset($_SESSION['captcha_staff']) || strtolower($captcha) !== strtolower($_SESSION['captcha_staff'])) {
            $error_staff = "Captcha salah!";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND role IN ('admin', 'dosen', 'super_admin')");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user'] = $user;
                    header("Location: staff/dashboard.php");
                    exit;
                } else {
                    $error_staff = "Password salah!";
                }
            } else {
                $error_staff = "Username tidak ditemukan!";
            }
        }
    }
}

// Tentukan Mode dari URL (Default Dosen)
 $mode = isset($_GET['mode']) ? $_GET['mode'] : 'staff';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Process</title>
    <link rel="stylesheet" href="css/login_process.css">
</head>
<body>

<div class="box">
    <?php if($mode == 'mhs'): ?>
        <!-- FORM LOGIN MAHASISWA -->
        <h2>Akses Mahasiswa</h2>
        <?php if(isset($error_mhs)) echo "<p class='error'>$error_mhs</p>"; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Pilih Kelas:</label>
                <select name="kelas_id" id="kelas_select" required onchange="loadMatkul()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $qKelas = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas ASC");
                    while($k = $qKelas->fetch_assoc()):
                    ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Mata Kuliah:</label>
                <select name="matkul_id" id="matkul_select" required disabled>
                    <option value="">-- Pilih Kelas Dahulu --</option>
                </select>
            </div>

            <button type="submit" name="login_mhs" class="mhs">Masuk Kelas</button>
        </form>

    <?php else: ?>
        <!-- FORM LOGIN STAFF (DOSEN) -->
        <h2>Login Admin/Dosen</h2>
        <?php if(isset($error_staff)) echo "<p class='error'>$error_staff</p>"; ?>
        <?php
        // Generate captcha untuk staff login
        $captcha_staff = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < 5; $i++) {
            $captcha_staff .= $chars[rand(0, strlen($chars)-1)];
        }
        $_SESSION['captcha_staff'] = $captcha_staff;
        ?>
        <form method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label>Captcha:</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-weight:bold;font-size:1.2em;background:#eee;padding:5px 15px;letter-spacing:3px;border-radius:5px;user-select:none;"><?= $_SESSION['captcha_staff'] ?></span>
                    <input type="text" name="captcha" placeholder="Masukkan captcha" maxlength="5" required style="width:120px;">
                </div>
            </div>
            <button type="submit" name="login_staff">Masuk</button>
        </form>

    <?php endif; ?>

    <a href="index.php" class="back-link">&larr; Kembali</a>
</div>


<script src="js/login_process.js"></script>

</body>
</html>