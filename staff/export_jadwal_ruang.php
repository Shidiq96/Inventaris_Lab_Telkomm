<?php
// File: export_jadwal_ruang.php
// Export data jadwal ruangan ke Excel


require '../database.php'; // Pastikan path sesuai dengan struktur folder Anda

// Header untuk memaksa browser mendownload sebagai file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=jadwal_ruang_lab.xls");

// Query untuk mengambil data lengkap jadwal
// Menggunakan JOIN agar nama (Ruangan, Waktu, dll) muncul, bukan ID-nya
// Diurutkan berdasarkan Nama Ruang, lalu Hari, lalu Jam Ke
 $sql = "SELECT 
    jr.hari,
    jr.jam_ke,
    w.waktu AS waktu_jam,
    r.ruang AS nama_ruang,
    k.nama_kelas,
    m.nama_matkul,
    COALESCE(d.nama_lengkap, d.username) AS nama_dosen
FROM jadwal_ruang jr
LEFT JOIN kelas k ON jr.kelas_id = k.id
LEFT JOIN mata_kuliah m ON jr.matkul_id = m.id
JOIN ruangan r ON jr.ruang_id = r.id
JOIN waktu w ON jr.waktu_id = w.id
LEFT JOIN users d ON jr.dosen_id = d.id
ORDER BY r.ruang ASC, FIELD(jr.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), jr.jam_ke ASC";

 $result = $conn->query($sql);

// Membuat Tabel HTML
echo "<table border='1'>";
echo "<tr>
    <th>Hari</th>
    <th>Jam Ke</th>
    <th>Waktu</th>
    <th>Ruangan</th>
    <th>Kelas</th>
    <th>Mata Kuliah</th>
    <th>Dosen</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['hari']) . "</td>";
    echo "<td>" . htmlspecialchars($row['jam_ke']) . "</td>";
    echo "<td>" . htmlspecialchars($row['waktu_jam']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_ruang']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_kelas']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_matkul']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_dosen']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>