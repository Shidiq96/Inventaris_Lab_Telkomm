<?php
session_start();
include "config/database.php";

if (isset($_POST['login_staff'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // UPDATE: Tambah 'super_admin' ke list role yang boleh login
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND role IN ('admin', 'dosen', 'super_admin')");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            
            // --- PERUBAHAN DI SINI ---
            // JANGAN gunakan $_SESSION['user'] = $user;
            
            // Simpan session secara SATUAN (Flat) agar mudah dibaca di jadwal_ruang.php
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect ke Dashboard Staff
            header("Location: staff/dashboard.php");
            exit;
        } else {
            echo "<script src='js/alert.js'></script><script>showAlert('Password salah!'); window.history.back();</script>";
        }
    } else {
        echo "<script src='js/alert.js'></script><script>showAlert('Username tidak ditemukan!'); window.history.back();</script>";
    }
}
?>