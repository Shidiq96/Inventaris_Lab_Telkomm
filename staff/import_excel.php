<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4" style="max-width:900px;">

    <?php include 'aksi_import.php'; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3">Import Data Peminjaman Inventaris Lab</h4>

            <!-- FORM UPLOAD -->
            <form action="" method="POST" enctype="multipart/form-data" class="row g-2 mb-3">
                <div class="col-md-8">
                    <input class="form-control" type="file" name="filexls" required>
                </div>
                <div class="col-md-4 d-grid">
                    <input type="submit" name="submit" class="btn btn-primary" value="Import File XLS/XLSX">
                </div>
            </form>

            <!-- DOWNLOAD TEMPLATE -->
            <div class="mb-4">
                <a href="assets/template_peminjaman.xlsx" class="btn btn-success">
                    Download Template Excel Peminjaman
                </a>
            </div>

        </div>
    </div>

    <hr class="my-4">

    <!-- PANDUAN LENGKAP -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Panduan Penggunaan Template Excel</h5>

            <h6>1️⃣ Format File</h6>
            <ul>
                <li>Format file harus <strong>.xls</strong> atau <strong>.xlsx</strong></li>
                <li>File tidak boleh kosong</li>
                <li>File tidak boleh diproteksi password</li>
            </ul>

            <h6>2️⃣ Struktur Kolom (WAJIB SESUAI URUTAN)</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>barang_id</th>
                            <th>nama_barang</th>
                            <th>nomor_unik</th>
                            <th>nama_kelas</th>
                            <th>nama_matkul</th>
                            <th>nama_mahasiswa</th>
                            <th>ajaran_semester</th>
                            <th>tanggal_pinjam</th>
                            <th>waktu_pinjam</th>
                            <th>status_peminjaman</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <h6>3️⃣ Penjelasan Kolom Penting</h6>
            <ul>
                <li><strong>barang_id</strong> → Harus sesuai dengan ID di tabel <b>barang</b>. Jika tidak ditemukan, data tidak akan diimport.</li>
                <li><strong>nama_kelas</strong> → Harus sesuai dengan tabel <b>kelas</b>.</li>
                <li><strong>nama_matkul</strong> → Harus sesuai dengan tabel <b>mata_kuliah</b>.</li>
                <li><strong>nama_mahasiswa</strong> → Nama mahasiswa peminjam.</li>
                <li><strong>ajaran_semester</strong> → Contoh: 2025/2026 Ganjil.</li>
                <li><strong>tanggal_pinjam</strong> → Format disarankan: <code>YYYY-MM-DD</code> (contoh: 2025-02-26).</li>
                <li><strong>waktu_pinjam</strong> → Format disarankan: <code>HH:MM:SS</code> (contoh: 08:30:00).</li>
                <li><strong>status_peminjaman</strong> → dipinjam / dikembalikan (jika kosong otomatis dianggap dipinjam).</li>
            </ul>

            <h6>4️⃣ Contoh Data</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <tr>
                        <td>1</td>
                        <td>5</td>
                        <td>Proyektor Epson</td>
                        <td>PRJ001</td>
                        <td>TI-1A</td>
                        <td>Pemrograman Web</td>
                        <td>Andi Saputra</td>
                        <td>2025/2026 Ganjil</td>
                        <td>2025-02-26</td>
                        <td>08:30:00</td>
                        <td>dipinjam</td>
                    </tr>
                </table>
            </div>

            <h6>5️⃣ Validasi Otomatis Sistem</h6>
            <ul>
                <li>Sistem mengecek apakah <strong>ID barang tersedia</strong>.</li>
                <li>Sistem mengecek apakah <strong>kelas & mata kuliah tersedia</strong>.</li>
                <li>Jika referensi tidak ditemukan, baris tersebut tidak akan disimpan.</li>
                <li>Jumlah data berhasil import akan ditampilkan setelah proses selesai.</li>
            </ul>

            <div class="alert alert-warning mt-3">
                ⚠ Pastikan data kelas, mata kuliah, dan barang sudah tersedia di sistem sebelum melakukan import.
            </div>

        </div>
    </div>

</div>
</body>
</html>