<?php
session_start();
// Hanya mahasiswa yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}
// Koneksi database
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
// Ambil data jadwal ruangan (join semua info)
$jadwal_arr = [];
$q = mysqli_query($conn, "SELECT jr.hari, r.ruang, w.waktu, k.nama_kelas, m.nama_matkul, u.username as dosen FROM jadwal_ruang jr JOIN ruangan r ON jr.ruang_id = r.id JOIN waktu w ON jr.waktu_id = w.id JOIN kelas k ON jr.kelas_id = k.id JOIN mata_kuliah m ON jr.matkul_id = m.id JOIN users u ON jr.dosen_id = u.id ORDER BY FIELD(jr.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), r.ruang ASC, w.waktu ASC");
while ($row = mysqli_fetch_assoc($q)) { $jadwal_arr[] = $row; }
$list_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Penggunaan Ruangan Lab</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 950px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; border-bottom: 2px solid #009879; padding-bottom: 10px; margin-bottom: 30px; }
        .hari-group { margin-bottom: 35px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .hari-header { background-color: #009879; color: white; padding: 10px 15px; font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 15px; }
        th { background-color: #f8f9fa; color: #555; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f1f1f1; }
        .back-btn { display: inline-block; margin-bottom: 20px; background: #0d47a1; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; }
        .back-btn:hover { background: #002171; }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="back-btn">Kembali ke Dashboard</a>
    <h2>Jadwal Penggunaan Ruangan Laboratorium</h2>
    <?php foreach($list_hari as $hari) { 
        $jadwal_hari = array_filter($jadwal_arr, function($row) use ($hari) {
            return $row['hari'] === $hari;
        });
        if (count($jadwal_hari) > 0) {
    ?>
    <div class="hari-group">
        <div class="hari-header">Hari <?= $hari; ?></div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Ruangan</th>
                    <th>Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Kuliah</th>
                    <th>Dosen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_values($jadwal_hari) as $i => $row) { ?>
                <tr>
                    <td><?= $i+1; ?></td>
                    <td><?= htmlspecialchars($row['ruang']); ?></td>
                    <td><?= htmlspecialchars($row['waktu']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                    <td><?= htmlspecialchars($row['nama_matkul']); ?></td>
                    <td><?= htmlspecialchars($row['dosen']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } } ?>
    <?php if (count($jadwal_arr) === 0) { ?>
    <div style="text-align:center; color:#888; margin-top:40px;">Tidak ada data jadwal ruangan.</div>
    <?php } ?>
</div>
</body>
</html>
