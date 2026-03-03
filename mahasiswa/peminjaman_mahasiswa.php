<?php
session_start();

// --- PENTING: PAKSA TIMEZONE WIB ---
date_default_timezone_set('Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');
putenv("TZ=Asia/Jakarta");

// 1. Cek Login & Role (Hanya Mahasiswa)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header("Location: ../index.php");
    exit;
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
    die("Error: Koneksi Database");
}

// --- FUNGSI GENERATE CAPTCHA ---
function generateNewCaptcha() {
    $chars = "023456789ABCDEFGHJKLMNPQRSTUVWXYZ";
    return substr(str_shuffle($chars), 0, 5);
}

// --- INISIALISASI CAPTCHA ---
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = generateNewCaptcha();
}

 $user = $_SESSION['user'];

// --- Ambil Data ajaran_semester Aktif ---
 $qSet = $conn->query("SELECT ajaran_semester FROM app_settings WHERE status_sistem='aktif' LIMIT 1");
 $ajaran_semester = '2023/2024 Ganjil'; // Default fallback
if($qSet && $qSet->num_rows > 0) {
    $setting = $qSet->fetch_assoc();
    $ajaran_semester = $setting['ajaran_semester'];
}

 $display_kelas = $user['nama_kelas'] ?? $user['kelas'] ?? 'Kelas Tidak Diketahui';
 $display_matkul = $user['nama_matkul'] ?? $user['mata_kuliah'] ?? 'Matkul Tidak Diketahui';

 $success_msg = "";
 $error_msg = "";
 $uploaded_filename = ""; // Variabel untuk menyimpan nama file jika sukses

// --- LOGIKA PEMINJAMAN (HANYA PINJAM + UPLOAD KTM) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_peminjaman'])) {
    
    $input_captcha = strtoupper(trim($_POST['captcha']));
    $session_captcha = $_SESSION['captcha_code'];
    
    // 1. Validasi Captcha
    if ($input_captcha !== $session_captcha) {
        $error_msg = "Kode Keamanan (Captcha) Salah! Silakan perhatikan kode yang ditampilkan.";
    } else {
        
        // 2. Validasi & Upload Foto KTM
        $nama_foto_ktm = "";
        $upload_ok = 1;
        
        // Cek apakah ada file yang diupload
        if (isset($_FILES['foto_ktm']) && $_FILES['foto_ktm']['error'] === 0) {
            $target_dir = "../uploads/ktm/"; // Folder penyimpanan
            
            // Buat folder jika belum ada
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_tmp = $_FILES['foto_ktm']['tmp_name'];
            $file_name = basename($_FILES['foto_ktm']['name']);
            $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validasi Ekstensi (Hanya Gambar)
            $allowed_types = ["jpg", "jpeg", "png"];
            if (!in_array($imageFileType, $allowed_types)) {
                $error_msg = "Maaf, hanya file JPG, JPEG, & PNG yang diperbolehkan untuk foto KTM.";
                $upload_ok = 0;
            }
            
            // Validasi Ukuran (Max 2MB)
            if ($_FILES['foto_ktm']['size'] > 2000000) {
                $error_msg = "Maaf, ukuran file foto KTM terlalu besar (Maksimal 2MB).";
                $upload_ok = 0;
            }

            // Jika validasi lolos, generate nama unik dan upload
            if ($upload_ok == 1) {
                // Gunakan fallback jika 'id' tidak ada di session user
                $user_identifier = isset($user['id']) ? $user['id'] : (isset($user['nim']) ? $user['nim'] : (isset($user['username']) ? $user['username'] : 'unknown'));
                $new_filename = "KTM_" . $user_identifier . "_" . time() . "." . $imageFileType;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $nama_foto_ktm = $new_filename;
                } else {
                    $error_msg = "Gagal mengupload foto KTM ke server.";
                    $upload_ok = 0;
                }
            }
        } else {
            $error_msg = "Wajib mengupload Foto KTM sebagai bukti peminjaman!";
            $upload_ok = 0;
        }

        // 3. Proses Data Barang (Jika Upload Sukses)
        if ($upload_ok == 1) {
            $nama_mahasiswa = $conn->real_escape_string($_POST['nama_mahasiswa']);
            $kelas_id = $user['kelas_id'];
            $matkul_id = $user['matkul_id'];
            $waktu_pinjam = date('H:i:s');
            
            if (isset($_POST['nama_barang']) && is_array($_POST['nama_barang']) && !empty($nama_mahasiswa)) {
                $total_sukses = 0;
                $total_gagal = 0;
                $daftar_barang_gagal = array();
                
                foreach ($_POST['nama_barang'] as $nama_barang) {
                    $jumlah_request = isset($_POST['jumlah_pinjam'][$nama_barang]) ? intval($_POST['jumlah_pinjam'][$nama_barang]) : 0;
                    
                    if ($jumlah_request > 0) {
                        $nama_barang_escaped = $conn->real_escape_string($nama_barang);
                        
                        // Cek Stok Tersedia
                        $sql_cek_ids = "SELECT b.id 
                                            FROM barang b
                                            WHERE b.nama_barang = '$nama_barang_escaped' 
                                            AND b.kondisi = 'Baik' 
                                            AND b.id NOT IN (
                                                SELECT p.barang_id FROM peminjaman p 
                                                WHERE p.status_peminjaman = 'dipinjam'
                                            ) 
                                            ORDER BY b.nomor_unik ASC 
                                            LIMIT $jumlah_request";
                        $result_ids = $conn->query($sql_cek_ids);
                        
                        if ($result_ids && $result_ids->num_rows > 0) {
                            while ($item = $result_ids->fetch_assoc()) {
                                $barang_id = $item['id'];
                                $tanggal_pinjam = date('Y-m-d');
                                
                                // INSERT DENGAN FOTO KTM
                                // Pastikan kolom 'foto_ktm' ada di tabel database
                                $sql_insert = "INSERT INTO peminjaman (barang_id, kelas_id, matkul_id, nama_mahasiswa, waktu_pinjam, tanggal_pinjam, status_peminjaman, ajaran_semester, foto_ktm) 
                                               VALUES ('$barang_id', '$kelas_id', '$matkul_id', '$nama_mahasiswa', '$waktu_pinjam', '$tanggal_pinjam', 'dipinjam', '$ajaran_semester', '$nama_foto_ktm')";
                                
                                if ($conn->query($sql_insert)) {
                                    $total_sukses++;
                                } else {
                                    $total_gagal++;
                                }
                            }
                        } else {
                            $total_gagal += $jumlah_request;
                            $daftar_barang_gagal[] = $nama_barang;
                        }
                    }
                }
                
                if ($total_sukses > 0) {
                    $success_msg = "Berhasil meminjam <strong>$total_sukses</strong> unit barang. <br>Foto KTM tersimpan.";
                    $_SESSION['captcha_code'] = generateNewCaptcha();
                }
                if ($total_gagal > 0) {
                    $gagal_list = implode(", ", $daftar_barang_gagal);
                    // Jangan timpa error_msg jika sebelumnya kosong
                    if(empty($error_msg)) {
                        $error_msg = " Gagal meminjam <strong>$total_gagal</strong> unit barang. (Stok kurang untuk: $gagal_list)";
                    } else {
                        $error_msg .= " (Stok kurang: $gagal_list)";
                    }
                }
            } else {
                // Jika barang kosong tapi upload sukses, hapus file yg sudah terupload agar sampah
                if(file_exists("../uploads/ktm/".$nama_foto_ktm)){
                    unlink("../uploads/ktm/".$nama_foto_ktm);
                }
                $error_msg = "Harap pilih setidaknya satu barang.";
            }
        }
    }
}

 $captcha_code = $_SESSION['captcha_code'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Barang - Upload KTM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/mahasiswa/peminjaman.css">
</head>
<body>

<div class="navbar">
    <h3><i class="fas fa-flask"></i> LabInventory</h3>
    <div>
        <button onclick="window.location.href='dashboard.php'" class="btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
    </div>
</div>

<div class="container">
    
    <div class="card">
        <h2>Transaksi Peminjaman</h2>
        <div class="info-peminjam">
            <i class="fas fa-user-graduate"></i> Login sebagai: <strong><?= htmlspecialchars($display_kelas) ?></strong> - 
            Mata Kuliah: <strong><?= htmlspecialchars($display_matkul) ?></strong>
            <div style="font-size: 13px; margin-top: 5px; color: #0d47a1;">
                <i class="fas fa-calendar-check"></i> Ajaran & Semester: 
                <strong><?= htmlspecialchars($ajaran_semester) ?></strong>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php if($success_msg): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>
        
        <?php if($error_msg): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <br>

        <!-- FORM PEMINJAMAN -->
        <form method="post" name="form_peminjaman" enctype="multipart/form-data" id="peminjamanForm">
            
            <!-- BAGIAN 1: DATA MAHASISWA & UPLOAD KTM -->
            <div class="form-group" style="background: #f9f9f9; padding: 20px; border-radius: 5px; border: 1px solid #eee;">
                <label class="form-label">Identitas Peminjam</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label class="form-label" style="font-weight:normal; font-size: 0.9rem;">Nama Lengkap</label>
                        <input type="text" name="nama_mahasiswa" class="form-control" placeholder="Nama Mahasiswa yang Bertanggung Jawab..." required>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label class="form-label" style="color: var(--primary-color);">
                        <i class="fas fa-id-card"></i> Foto KTM (Kartu Tanda Mahasiswa) <span style="color:red;">*Wajib</span>
                    </label>
                    <div class="ktm-upload-area" id="dropArea">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                        <br>
                        <label for="foto_ktm" class="upload-label">
                            <i class="fas fa-camera"></i> Pilih Foto KTM
                        </label>
                        <input type="file" name="foto_ktm" id="foto_ktm" accept="image/png, image/jpeg, image/jpg" required>
                        <div style="font-size: 0.85rem; color: #666; margin-top: 10px;">
                            Format: JPG, JPEG, PNG. Maksimal 2MB.<br>
                            Pastikan foto terbaca dengan jelas.
                        </div>

                        <!-- Preview Area -->
                        <div class="preview-container" id="previewContainer">
                            <img src="" alt="Preview KTM" class="preview-image" id="previewImg">
                            <div style="font-size: 0.9rem; color: var(--success-color); font-weight: bold;">
                                <i class="fas fa-check-circle"></i> Foto Siap Diupload
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: DAFTAR BARANG -->
            <h3>Pinjam Barang Baru</h3>
            <p style="font-size: 13px; color: #666;">Centang barang yang ingin dipinjam.</p>

            <table>
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">Pilih</th>
                        <th>Nama Barang</th>
                        <th>Status Kondisi</th>
                        <th>Tersedia</th>
                        <th>Jml Pinjam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT b.nama_barang, 
                                         COUNT(b.id) as total_unit,
                                         (SELECT COUNT(*) FROM peminjaman p 
                                          JOIN barang bb ON p.barang_id = bb.id 
                                          WHERE bb.nama_barang = b.nama_barang 
                                          AND p.status_peminjaman = 'dipinjam') as dipinjam,
                                         (SELECT COUNT(*) FROM barang bb 
                                          WHERE bb.nama_barang = b.nama_barang 
                                          AND bb.kondisi != 'Baik') as rusak
                                  FROM barang b
                                  GROUP BY b.nama_barang 
                                  ORDER BY b.nama_barang ASC";
                    
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        $i=1; 
                        while ($row = $result->fetch_assoc()) {
                            $tersedia_baik = $row['total_unit'] - $row['dipinjam'] - $row['rusak'];
                            $is_available = $tersedia_baik > 0;
                            $nama_barang_raw = $row['nama_barang'];
                    ?>
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" 
                                   name="nama_barang[]" 
                                   value="<?= htmlspecialchars($nama_barang_raw) ?>" 
                                   id="check_<?= $i ?>" 
                                   <?= !$is_available ? 'disabled' : '' ?>
                                   onchange="updateInput(<?= $i ?>, <?= $tersedia_baik ?>)">
                        </td>
                        <td data-label="Nama Barang"><strong><?= htmlspecialchars($nama_barang_raw) ?></strong></td>
                        <td data-label="Status">
                            <?php if($row['rusak'] > 0): ?>
                                <span class="badge badge-red"><i class="fas fa-exclamation-triangle"></i> <?= $row['rusak'] ?> Rusak</span>
                            <?php else: ?>
                                <span class="badge badge-green">Aman</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Tersedia">
                            <?php if($is_available): ?>
                                <span class="badge badge-green"><?= $tersedia_baik ?></span>
                            <?php else: ?>
                                <span class="badge badge-red">0 (Habis)</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;" data-label="Jumlah">
                            <input type="number" 
                                   name="jumlah_pinjam[<?= htmlspecialchars($nama_barang_raw) ?>]" 
                                   id="input_jumlah_<?= $i ?>" 
                                   class="input-jumlah form-control" 
                                   min="1" max="<?= $tersedia_baik ?>" 
                                   value="1" 
                                   disabled>
                        </td>
                    </tr>
                    <?php 
                            $i++; 
                        }
                    } else {
                        echo "<tr><td colspan='5' align='center' style='padding:20px;'>Data barang kosong.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="form-group" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <label class="form-label">Verifikasi Keamanan:</label>
                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                    <div class="captcha-box">
                        <span class="captcha-code"><?= $captcha_code ?></span>
                    </div>
                    <div style="flex-grow: 1;">
                        <input type="text" name="captcha" class="form-control" placeholder="Ketik kode di samping..." required autocomplete="off">
                    </div>
                </div>
            </div>
            
            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" name="simpan_peminjaman" class="btn btn-primary" id="btnSubmit">
                    <i class="fas fa-cart-plus"></i> Proses Peminjaman
                </button>
            </div>
        </form>
        
    </div>

</div>

<script>
    // --- LOGIKA PREVIEW FOTO KTM ---
    const fileInput = document.getElementById('foto_ktm');
    const previewContainer = document.getElementById('previewContainer');
    const previewImg = document.getElementById('previewImg');
    const dropArea = document.getElementById('dropArea');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        handleFile(file);
    });

    function handleFile(file) {
        if (file) {
            // Validasi Ukuran (2MB)
            if (file.size > 2000000) {
                alert("Ukuran file terlalu besar! Maksimal 2MB.");
                fileInput.value = ""; // Reset
                previewContainer.style.display = 'none';
                return;
            }

            // Validasi Tipe
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                alert("Format file salah! Hanya JPG, JPEG, dan PNG.");
                fileInput.value = "";
                previewContainer.style.display = 'none';
                return;
            }

            // Baca dan Tampilkan Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'flex';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    }

    // --- LOGIKA INTERAKSI CHECKBOX BARANG ---
    function updateInput(index, maxVal) {
        const checkbox = document.getElementById('check_' + index);
        const input = document.getElementById('input_jumlah_' + index);
        
        if (checkbox.checked) {
            input.disabled = false;
            input.focus();
        } else {
            input.disabled = true;
            input.value = 1;
        }
    }

    // --- VALIDASI SEBELUM SUBMIT ---
    document.getElementById('peminjamanForm').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="nama_barang[]"]:checked');
        
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert("Harap pilih setidaknya satu barang!");
            return false;
        }

        // Validasi input jumlah (pastikan tidak kosong atau <= 0)
        let validQty = true;
        checkboxes.forEach(cb => {
            const name = cb.value;
            const qtyInput = document.querySelector(`input[name="jumlah_pinjam[${name}]"]`);
            if (qtyInput.value < 1 || qtyInput.value === "") {
                validQty = false;
            }
        });

        if (!validQty) {
            e.preventDefault();
            alert("Jumlah pinjam untuk barang yang dipilih minimal 1.");
            return false;
        }
    });
</script>

</body>
</html>