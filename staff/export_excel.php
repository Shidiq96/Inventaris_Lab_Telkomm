<?php
// File: export_excel.php
// Export data barang ke Excel


require '../database.php'; // Pastikan path sesuai

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=riwayat_peminjaman.xls");

// Query riwayat peminjaman
$sql = "SELECT 
    b.id AS barang_id,
    b.nama_barang AS item_name,
    b.nomor_unik,
    k.nama_kelas,
    mk.nama_matkul,
    p.nama_mahasiswa,
    p.ajaran_semester,
    p.tanggal_pinjam,
    p.waktu_pinjam,
    p.status_peminjaman
FROM peminjaman p
JOIN barang b ON p.barang_id = b.id
LEFT JOIN kelas k ON p.kelas_id = k.id
LEFT JOIN mata_kuliah mk ON p.matkul_id = mk.id
ORDER BY p.tanggal_pinjam DESC, p.waktu_pinjam DESC";
$result = $conn->query($sql);

echo "<table border='1'>";
echo "<tr>
    <th>ID Barang</th>
    <th>Nama Barang</th>
    <th>kode unik</th>
    <th>Kelas</th>
    <th>Mata Kuliah</th>
    <th>Nama Mahasiswa</th>
    <th>Ajaran Semester</th>
    <th>Tanggal Pinjam</th>
    <th>Waktu Pinjam</th>
    <th>Status</th>
</tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['barang_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nomor_unik']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_kelas']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_matkul']) . "</td>";
    echo "<td>" . htmlspecialchars($row['nama_mahasiswa']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ajaran_semester']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tanggal_pinjam']) . "</td>";
    echo "<td>" . htmlspecialchars($row['waktu_pinjam']) . "</td>";
    echo "<td>" . htmlspecialchars($row['status_peminjaman']) . "</td>";
    echo "</tr>";
}
echo "</table>";
