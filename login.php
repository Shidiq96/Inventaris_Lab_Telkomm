<?php
session_start();
include "database.php"; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventaris Lab Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="box">
    <h2>Inventaris Lab</h2>
    <p>Silakan pilih jenis akses</p>
    
    <!-- Tombol Dosen: Mengarah ke login_process.php dengan mode staff -->
    <a href="login_process.php?mode=staff" class="btn staff">Dosen</a>
    <!-- Tombol Mahasiswa: Mengarah ke login_process.php dengan mode mhs -->
    <a href="login_process.php?mode=mhs" class="btn mhs">Mahasiswa</a>
</div>
</body>
</html>