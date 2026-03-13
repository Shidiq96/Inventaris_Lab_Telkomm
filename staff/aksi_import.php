<?php
require '../vendor/autoload.php';
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventaris_lab_telkom";

$konek = mysqli_connect($host,$user,$pass,$db);
if(isset($_POST['submit'])){
    $err        = "";
    $ekstensi   = "";
    $success    = "";

    $file_name  = $_FILES['filexls']['name'];
    $file_data  = $_FILES['filexls']['tmp_name'];

    if(empty($file_name)){
        $err .= "<li>Silahkan pilih file excel terlebih dahulu</li>";
    } else {
        $ekstensi = pathinfo($file_name)['extension'];
    }

    $ekstensi_allowed = array('xls','xlsx');
    if(!in_array($ekstensi, $ekstensi_allowed)){
        $err .= "<li>Ekstensi file yang diupload tidak diizinkan</li>";
    }

    if(empty($err)){
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_data);
        $spreadsheet = $reader->load($file_data);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        
        $jumlahData = 0;
        
for($i=1;$i<count($sheetData);$i++){

    $barang_id       = $sheetData[$i][0] ?? '';
    $nama_barang     = $sheetData[$i][1] ?? '';
    $nomor_unik      = $sheetData[$i][2] ?? '';
    $nama_kelas      = $sheetData[$i][3] ?? '';
    $nama_matkul     = $sheetData[$i][4] ?? '';
    $nama_mahasiswa  = $sheetData[$i][5] ?? '';
    $ajaran_semester = $sheetData[$i][6] ?? '';

    $raw_tgl = $sheetData[$i][7] ?? '';
    $raw_wkt = $sheetData[$i][8] ?? '';

    $tanggal_pinjam = (!empty($raw_tgl)) ? date('Y-m-d', strtotime($raw_tgl)) : NULL;
    $waktu_pinjam   = (!empty($raw_wkt)) ? date('H:i:s', strtotime($raw_wkt)) : NULL;

    $status_peminjaman = strtolower($sheetData[$i][10] ?? 'dipinjam');

    // VALIDASI ID BARANG
    $cekBarang = mysqli_query($konek, "SELECT id FROM barang WHERE id='$barang_id'");
    if(mysqli_num_rows($cekBarang) == 0){
        echo "ID Barang tidak ditemukan di baris ke-$i <br>";
        continue;
    }

    // Cari ID kelas
    $qKelas = mysqli_query($konek, "SELECT id FROM kelas WHERE LOWER(nama_kelas)=LOWER('$nama_kelas') LIMIT 1");
    $rKelas = mysqli_fetch_assoc($qKelas);
    $kelas_id = ($rKelas) ? $rKelas['id'] : NULL;

    // Cari ID matkul
    $qMatkul = mysqli_query($konek, "SELECT id FROM mata_kuliah WHERE LOWER(nama_matkul)=LOWER('$nama_matkul') LIMIT 1");
    $rMatkul = mysqli_fetch_assoc($qMatkul);
    $matkul_id = ($rMatkul) ? $rMatkul['id'] : NULL;

    if($kelas_id && $matkul_id){

        $sql1 = "INSERT INTO peminjaman
                (barang_id, kelas_id, matkul_id, nama_mahasiswa, ajaran_semester, tanggal_pinjam, waktu_pinjam, status_peminjaman)
                VALUES
                ('$barang_id','$kelas_id','$matkul_id','$nama_mahasiswa','$ajaran_semester','$tanggal_pinjam','$waktu_pinjam','$status_peminjaman')";

        if(mysqli_query($konek,$sql1)){
            $jumlahData++;
        } else {
            echo "Gagal insert: ".mysqli_error($konek)."<br>";
        }

    } else {
        echo "Referensi kelas/matkul tidak ditemukan di baris ke-$i <br>";
    }
}
        
        if($jumlahData > 0){
            $success = "Berhasil mengimpor data sebanyak ".$jumlahData." baris";
        }
    }

    if($err){
        ?>
        <div class="alert alert-danger" role="alert">
            <ul><?php echo $err; ?></ul>
        </div>
        <?php
    }

    if($success){
        ?>
        <div class="alert alert-primary">
            <?php echo $success ?>
        </div>
        <?php
    }
}   