<?php
session_start();

// --- PENTING: PAKSA TIMEZONE WIB ---
date_default_timezone_set('Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');
putenv("TZ=Asia/Jakarta");

// 1. Cek Akses (Hanya Admin/Dosen)
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

// 3. Ambil Parameter dari URL
if (!isset($_GET['data'])) {
    header("Location: dashboard.php"); exit;
}
$params = $_GET['data'];
$data = explode("|", $params);
if (count($data) != 4) {
    die("Parameter tidak valid.");
}
$nama_barang = $conn->real_escape_string($data[0]);
$ajaran_semester = $conn->real_escape_string($data[1]); // Sudah satu string, tidak perlu digabung
$kelas = $conn->real_escape_string($data[2]);
$matkul = $conn->real_escape_string($data[3]);
$semester = $ajaran_semester; // Tambahkan baris ini agar variabel $semester terdefinisi

$success_msg = "";
$error_msg = "";

// 4. LOGIKA KEMBALIKAN SEMUA (Force Return)
if (isset($_POST['kembalikan_semua'])) {
    $tgl_kembali = date('Y-m-d H:i:s');
    $sql_update_all = "UPDATE peminjaman p
                       JOIN barang b ON p.barang_id = b.id
                       JOIN kelas k ON p.kelas_id = k.id
                       JOIN mata_kuliah mk ON p.matkul_id = mk.id
                       SET p.status_peminjaman = 'dikembalikan', p.tanggal_kembali = '$tgl_kembali'
                       WHERE b.nama_barang = '$nama_barang' 
                       AND p.ajaran_semester = '$ajaran_semester'
                       AND k.nama_kelas = '$kelas'
                       AND mk.nama_matkul = '$matkul'
                       AND p.status_peminjaman = 'dipinjam'";
                       
    if ($conn->query($sql_update_all)) {
        $success_msg = "Seluruh barang berhasil dikembalikan.";
    } else {
        $error_msg = "Gagal mengembalikan semua barang.";
    }
}

// 5. LOGIKA TOGGLE STATUS (UBAH STATUS BOLAK-BALIK)
if (isset($_POST['simpan_seleksi'])) {
    if (isset($_POST['pilih_id']) && is_array($_POST['pilih_id'])) {
        $ids_list = implode(",", array_map('intval', $_POST['pilih_id'])); 
        
        if (!empty($ids_list)) {
            // Waktu sekarang WIB
            $tgl_kembali_wib = date('Y-m-d H:i:s');

            // SQL LOGIKA TOGGLE SINKRONISASI:
            // Jika status 'dipinjam' -> ubah jadi 'dikembalikan', isi tanggal kembali.
            // Jika status 'dikembalikan' -> ubah jadi 'dipinjam', kosongkan tanggal kembali.
            $sql_toggle = "UPDATE peminjaman SET 
                               status_peminjaman = IF(status_peminjaman = 'dipinjam', 'dikembalikan', 'dipinjam'),
                               tanggal_kembali = CASE
                                   WHEN status_peminjaman = 'dipinjam' THEN '$tgl_kembali_wib'
                                   WHEN status_peminjaman = 'dikembalikan' THEN NULL
                                   ELSE tanggal_kembali
                               END
                           WHERE id IN ($ids_list)";
            
            if ($conn->query($sql_toggle)) {
                $count = count($_POST['pilih_id']);
                $success_msg = "Status berhasil diubah untuk $count item barang.";
            } else {
                $error_msg = "Terjadi kesalahan sistem.";
            }
        } else {
            $error_msg = "Tidak ada barang yang dipilih.";
        }
    } else {
        $error_msg = "Silakan centang barang yang ingin diubah statusnya.";
    }
}

// 6. AMBIL DATA UNTUK TAMPILAN
$qDetail = $conn->query("SELECT p.id, b.nomor_unik, b.nama_barang, p.nama_mahasiswa, p.status_peminjaman, p.waktu_kembali, p.tanggal_pinjam, p.waktu_pinjam, p.foto_ktm
                          FROM peminjaman p
                          JOIN barang b ON p.barang_id = b.id
                          JOIN kelas k ON p.kelas_id = k.id
                          JOIN mata_kuliah mk ON p.matkul_id = mk.id
                          WHERE b.nama_barang = '$nama_barang' 
                          AND p.ajaran_semester = '$ajaran_semester'
                          AND k.nama_kelas = '$kelas'
                          AND mk.nama_matkul = '$matkul'
                          ORDER BY b.nomor_unik ASC");

$dataDetail = [];
if($qDetail && $qDetail->num_rows > 0) {
    while($row = $qDetail->fetch_assoc()) {
        $dataDetail[] = $row;
    }
}

// Ambil info umum
$qInfo = $conn->query("SELECT 
    GROUP_CONCAT(DISTINCT p.nama_mahasiswa SEPARATOR ', ') as list_mhs,
    MAX(p.foto_ktm) as foto_ktm
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    JOIN kelas k ON p.kelas_id = k.id
    JOIN mata_kuliah mk ON p.matkul_id = mk.id
    WHERE b.nama_barang = '$nama_barang' AND p.ajaran_semester = '$ajaran_semester' AND k.nama_kelas = '$kelas' AND mk.nama_matkul = '$matkul'");
$info = $qInfo->fetch_assoc();
$penanggung_jawab = $info['list_mhs'] ?? '-';
$foto_ktm_info = $info['foto_ktm'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Peminjaman</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/staff/riwayat.css">
</head>
<body>

<div class="container">
    <!-- Header Navigasi -->
    <div class="header">
        <button onclick="window.location.href='dashboard.php'" class="btn btn-secondary" style="background: #6c757d;">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        <div>
            <h2 style="margin:0;">Detail Peminjaman</h2>
        </div>
    </div>

    <!-- Informasi Grup -->
    <div class="card" style="border-left: 5px solid #0d47a1;">
        <h3>Informasi Peminjaman</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div><strong>Barang:</strong> <?= htmlspecialchars($nama_barang) ?></div>
            <div><strong>Semester:</strong> <?= htmlspecialchars($semester) ?></div>
            <div><strong>Kelas:</strong> <?= htmlspecialchars($kelas) ?></div>
            <div><strong>Mata Kuliah:</strong> <?= htmlspecialchars($matkul) ?></div>
            <div style="grid-column: span 2;">
                <strong>Penanggung Jawab (Mhs):</strong>
                <span class="badge badge-green"><?= htmlspecialchars($penanggung_jawab) ?></span>
            </div>
            <div style="grid-column: span 2;">
                <?php
                // Tampilkan foto_ktm jika ada dan file tersedia
                if ($foto_ktm_info && file_exists("../uploads/ktm/$foto_ktm_info")) {
                    $src = "../uploads/ktm/" . rawurlencode($foto_ktm_info);
                    echo "<a href='$src' target='_blank'><img src='$src' class='ktm-info-img' alt='KTM' style='max-width:180px; border-radius:6px; margin-top:8px;'></a>";
                } else {
                    echo "<span style='color:#888;'>- Tidak tersedia -</span>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <?php if($success_msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= $error_msg ?></div>
    <?php endif; ?>


    <div id="scannerModal" style="display:none; position:flex; inset:0; background:rgba(0,0,0,0.6); z-index:2000; justify-content:center; align-items:center">
        <div style="background:#fff; padding:20px; border-radius:8px; width:400px; text-align:center;">
            <div id="reader" style="width:100%;"></div>
            <br>
            <button onclick="closeScanner()" class="btn btn-red">Tutup</button>
        </div>
    </div>


    <!-- Form Pengembalian -->
    <div class="card">
        <!-- Mulai FORM UTAMA di sini, mencakup header dan tabel -->
        <form method="post" name="form_seleksi">
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3 style="margin:0;">Daftar Barang</h3>
                
                <!-- TOMBOL KEMBALIKAN SEMUA (Sekarang berada di dalam form utama) -->
                <?php 
                // Cek apakah ada barang yang masih dipinjam
                $cek_dipinjam = false;
                foreach($dataDetail as $row) {
                    if($row['status_peminjaman'] == 'dipinjam') { $cek_dipinjam = true; break; }
                }
                ?>

                <button type="button" onclick="openScanner()" class="btn btn-green" style="display:inline-flex; align-items:center; gap:5px;">
                    <i class="fas fa-camera"></i> Scan Barcode
                </button>
                
                <?php if($cek_dipinjam): ?>
                    <!-- Tombol ini langsung div, tidak perlu tag form lagi karena sudah di dalam form_utama -->
                    <button type="submit" name="kembalikan_semua" class="btn btn-red" onclick="return confirm('Kembalikan SEMUA barang yang masih berstatus Dipinjam?')" style="display:inline-flex; align-items:center; gap:5px;">
                        <i class="fas fa-undo"></i> Kembalikan Semua
                    </button>
                <?php endif; ?>
            </div>

            <!-- TABEL DATA -->
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Kode Unik</th>
                        <th>Nama Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Waktu Pinjam</th>
                        <th>Status</th>
                        <th width="80">Pilih (Ubah)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if(count($dataDetail) > 0) {
                        foreach($dataDetail as $row){
                            $is_done = ($row['status_peminjaman'] == 'dikembalikan');
                            $status_badge = $is_done ? '<span class="badge badge-green">Dikembalikan</span>' : '<span class="badge badge-orange">Dipinjam</span>';
                            $row_class = $is_done ? 'item-dikembalikan' : '';
                            $display_tanggal_pinjam = $row['tanggal_pinjam'] ? date('d-m-Y', strtotime($row['tanggal_pinjam'])) : '-';
                            $display_waktu_pinjam = $row['waktu_pinjam'] ? date('H:i', strtotime($row['waktu_pinjam'])) : '-';
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><?= $no++ ?></td>
                            <td style="font-family: monospace; font-weight: bold;"><?= htmlspecialchars($row['nomor_unik']) ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= $display_tanggal_pinjam ?></td>
                            <td><span class="time-display"><?= $display_waktu_pinjam ?></span> WIB</td>
                            <td><?= $status_badge ?></td>
                            <td style="text-align: center;">
                                <div class="check-wrapper">
                                    <input type="checkbox" 
                                           name="pilih_id[]" 
                                           value="<?= htmlspecialchars($row['id']) ?>" 
                                           class="custom-check"
                                           title="Centang untuk mengubah status (Dipinjam <-> Dikembalikan)">
                                </div>
                            </td>
                        </tr>
                        <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' align='center'>Data tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" name="simpan_seleksi" class="btn btn-blue">
                    <i class="fas fa-exchange-alt"></i> Ubah Status Tercentang
                </button>
            </div>
        
        <!-- Tutup FORM UTAMA -->
        </form>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let html5QrCode;

function openScanner() {
    document.getElementById('scannerModal').style.display = 'flex';

    html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            html5QrCode.start(
                devices[0].id,
                {
                    fps: 10,
                    qrbox: 250
                },
                qrCodeMessage => {
                    handleScanSuccess(qrCodeMessage);
                }
            );
        }
    }).catch(err => {
        alert("Tidak bisa mengakses kamera");
    });
}

function closeScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            document.getElementById('scannerModal').style.display = 'none';
        });
    } else {
        document.getElementById('scannerModal').style.display = 'none';
    }
}

function handleScanSuccess(kode) {
    if (!kode) return;

    if (html5QrCode) {
        html5QrCode.stop();
    }

    if (confirm("Ubah status untuk kode: " + kode + " ?")) {
        window.location.href = "toggle_scan.php?kode=" + encodeURIComponent(kode) + "&data=<?= urlencode($_GET['data']) ?>";
    } else {
        closeScanner();
    }
}
</script>
</body>
</html>