<?php
// 1. Load Library PhpSpreadsheet
require '../vendor/autoload.php';

// 2. Koneksi Database (Sesuaikan path config jika berbeda)
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: File database.php tidak ditemukan.");
}

// Inisialisasi variabel notifikasi
 $err        = "";
 $ekstensi   = "";
 $success    = "";

// 3. Cek jika tombol submit ditekan
if(isset($_POST['submit'])){
    
    // Ambil data file
    $file_name  = $_FILES['filexls']['name'];
    $file_data  = $_FILES['filexls']['tmp_name'];

    // Validasi: Apakah ada file yang dipilih?
    if(empty($file_name)){
        $err .= "<li>Silahkan pilih file Excel terlebih dahulu</li>";
    } else {
        // Ambil ekstensi file
        $ekstensi = pathinfo($file_name)['extension'];
    }

    // Validasi: Hanya izinkan xls dan xlsx
    $ekstensi_allowed = array('xls','xlsx');
    if(!in_array($ekstensi, $ekstensi_allowed)){
        $err .= "<li>Ekstensi file yang diupload tidak diizinkan. Hanya .xls atau .xlsx</li>";
    }

    // Jika tidak ada error, lanjut proses
    if(empty($err)){
        try {
            // Load file Excel menggunakan PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_data);
            $spreadsheet = $reader->load($file_data);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            
            $jumlahData = 0;
            $jumlahGagal = 0;
            
            // Looping data Excel (Mulai dari baris ke-2 karena baris ke-1 adalah Header)
            // Index array dimulai dari 0, jadi baris ke-2 Excel adalah index 1
            for($i=1; $i<count($sheetData); $i++){

                // MAPPING KOLOM EXCEL (Pastikan urutan kolom di Excel sesuai dengan ini)
                // Kolom A (0): Hari
                // Kolom B (1): Jam Ke
                // Kolom C (2): Waktu
                // Kolom D (3): Ruangan
                // Kolom E (4): Kelas
                // Kolom F (5): Mata Kuliah
                // Kolom G (6): Dosen
                
                $hari            = $sheetData[$i][0] ?? '';
                $jam_ke          = $sheetData[$i][1] ?? '';
                $nama_waktu      = $sheetData[$i][2] ?? '';
                $nama_ruang      = $sheetData[$i][3] ?? '';
                $nama_kelas      = $sheetData[$i][4] ?? '';
                $nama_matkul     = $sheetData[$i][5] ?? '';
                $nama_dosen      = $sheetData[$i][6] ?? '';

                // VALIDASI DATA WAJIB
                if(empty($hari) || empty($nama_waktu) || empty($nama_ruang)){
                    // Lewati baris jika data inti kosong
                    continue;
                }

                // --- PROSES PENCARIAN ID (REFERENSI) ---
                
                // 1. Cari ID Waktu (Berdasarkan nama waktu, misal: "07:00 - 08:00")
                $qWaktu = mysqli_query($conn, "SELECT id FROM waktu WHERE LOWER(waktu)=LOWER('$nama_waktu') LIMIT 1");
                $rWaktu = mysqli_fetch_assoc($qWaktu);
                $waktu_id = ($rWaktu) ? $rWaktu['id'] : NULL;

                // 2. Cari ID Ruang (Berdasarkan nama ruang)
                $qRuang = mysqli_query($conn, "SELECT id FROM ruangan WHERE LOWER(ruang)=LOWER('$nama_ruang') LIMIT 1");
                $rRuang = mysqli_fetch_assoc($qRuang);
                $ruang_id = ($rRuang) ? $rRuang['id'] : NULL;

                // 3. Cari ID Kelas (Opsional)
                $kelas_id = "NULL";
                if(!empty($nama_kelas)){
                    $qKelas = mysqli_query($conn, "SELECT id FROM kelas WHERE LOWER(nama_kelas)=LOWER('$nama_kelas') LIMIT 1");
                    $rKelas = mysqli_fetch_assoc($qKelas);
                    if($rKelas) $kelas_id = "'" . $rKelas['id'] . "'";
                }

                // 4. Cari ID Matkul (Opsional)
                $matkul_id = "NULL";
                if(!empty($nama_matkul)){
                    $qMatkul = mysqli_query($conn, "SELECT id FROM mata_kuliah WHERE LOWER(nama_matkul)=LOWER('$nama_matkul') LIMIT 1");
                    $rMatkul = mysqli_fetch_assoc($qMatkul);
                    if($rMatkul) $matkul_id = "'" . $rMatkul['id'] . "'";
                }

                // 5. Cari ID Dosen (Opsional)
                $dosen_id = "NULL";
                if(!empty($nama_dosen)){
                    // Coba cari berdasarkan nama_lengkap dulu
                    $qDosen = mysqli_query($conn, "SELECT id FROM users WHERE (LOWER(nama_lengkap)=LOWER('$nama_dosen') OR LOWER(username)=LOWER('$nama_dosen')) AND role='dosen' LIMIT 1");
                    $rDosen = mysqli_fetch_assoc($qDosen);
                    if($rDosen) $dosen_id = "'" . $rDosen['id'] . "'";
                }

                // VALIDASI REFERENSI
                // Jika Ruang atau Waktu tidak ditemukan di database, jangan masukkan
                if(!$waktu_id || !$ruang_id){
                    echo "Gagal: Data referensi (Waktu/Ruang) tidak ditemukan di baris ke-".($i+1)."<br>";
                    $jumlahGagal++;
                    continue;
                }

                // VALIDASI BENTROK (Sama dengan logika jadwal_ruang.php)
                // Cek: Hari + Ruang + (Jam Ke SAMA ATAU Waktu SAMA)
                $cek_bentrok = mysqli_query($conn, "SELECT * FROM jadwal_ruang 
                                                    WHERE hari='$hari' 
                                                    AND ruang_id='$ruang_id' 
                                                    AND (jam_ke='$jam_ke' OR waktu_id='$waktu_id')");
                
                if(mysqli_num_rows($cek_bentrok) > 0){
                    echo "Gagal: Jadwal bentrok di baris ke-".($i+1)." (Hari: $hari, Ruang: $nama_ruang)<br>";
                    $jumlahGagal++;
                    continue; 
                }

                // --- SIMPAN DATA ---
                $hari_esc = mysqli_real_escape_string($conn, $hari);
                $jam_esc  = mysqli_real_escape_string($conn, $jam_ke);

                $sql1 = "INSERT INTO jadwal_ruang 
                        (hari, jam_ke, waktu_id, kelas_id, matkul_id, ruang_id, dosen_id)
                        VALUES
                        ('$hari_esc','$jam_esc','$waktu_id',$kelas_id,$matkul_id,'$ruang_id',$dosen_id)";

                if(mysqli_query($conn, $sql1)){
                    $jumlahData++;
                } else {
                    echo "Gagal insert DB di baris ke-".($i+1).": ".mysqli_error($conn)."<br>";
                    $jumlahGagal++;
                }
            }
            
            // Notifikasi Akhir
            if($jumlahData > 0){
                $success = "Berhasil mengimpor <strong>$jumlahData</strong> data jadwal.";
                if($jumlahGagal > 0){
                    $success .= " <br><span style='color:red'>Gagal $jumlahGagal data (lihat detail di atas).</span>";
                }
            } else {
                $err = "<li>Tidak ada data baru yang berhasil diimport. Mungkin semua data duplikat atau referensi tidak ditemukan.</li>";
            }

        } catch (Exception $e) {
            $err .= "<li>Error saat membaca file Excel: " . $e->getMessage() . "</li>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Jadwal Excel</title>
    <!-- Menggunakan Bootstrap Style untuk Alert agar mirip contoh -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="padding: 20px; font-family: sans-serif;">

<div class="container" style="max-width: 600px;">
    <h3>Import Jadwal Ruangan</h3>
    <p>Upload file Excel (.xls/.xlsx) berisi data jadwal.</p>
    
    <div class="card p-3 shadow-sm">
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="filexls" class="form-label">Pilih File Excel</label>
                <input type="file" class="form-control" name="filexls" id="filexls" required accept=".xls, .xlsx">
                <div class="form-text">
                    <strong>Format Kolom Excel (Urutan Harus Sesuai):</strong><br>
                    1. Hari | 2. Jam Ke | 3. Waktu | 4. Ruangan | 5. Kelas | 6. Mata Kuliah | 7. Dosen
                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Upload & Proses</button>
            <a href="jadwal_ruang.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <a href="assets/template_jadwal.xlsx" class="btn" style="background-color: #3498db; color: white;">📥 Unduh Template Excel</a>

    <br>
    
    <!-- Tampilkan Pesan Error -->
    <?php if($err){ ?>
        <div class="alert alert-danger" role="alert">
            <ul><?php echo $err; ?></ul>
        </div>
    <?php } ?>

    <!-- Tampilkan Pesan Sukses -->
    <?php if($success){ ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success; ?>
            <br><br>
            <a href="jadwal_ruang.php" class="btn btn-sm btn-success">Lihat Jadwal</a>
        </div>
    <?php } ?>

</div>

</body>
</html>