<?php
session_start();
if (!isset($_SESSION['user'])) {
    die('Akses Ditolak. Silakan Login.');
}

// Koneksi Database
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} elseif (file_exists('../database.php')) {
    include '../database.php';
} else {
    die("Error: Database tidak ditemukan");
}

if (!isset($conn)) {
    die("Error Koneksi Database");
}

// Ambil SEMUA data barang, urutkan berdasarkan Nama Barang, lalu Nomor Unik
 $query = "SELECT * FROM barang ORDER BY nama_barang ASC, nomor_unik ASC";
 $result = $conn->query($query);

// Cek jika tidak ada data
 $total_all = $result->num_rows;

// --- PERBAIKAN: Inisialisasi Variabel ---
 $current_category = '';
 $prev_nama = ''; // Variabel ini didefinisikan di sini agar tidak error
 $group_counter = 1;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Semua Barang & Kode Unik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  
    <link rel="stylesheet" href="../css/staff/cetak.css"> 
</head>
<body>

<a href="dashboard.php" class="back-btn no-print">
    <i class="fas fa-arrow-left"></i> Kembali
</a>

<button onclick="window.print()" class="print-btn no-print">
    <i class="fas fa-print"></i> Cetak Semua
</button>

<div class="container">
    <div class="main-header">
        <h1>Daftar Kode Unik Seluruh Barang</h1>
        <p style="font-weight: bold; margin-top: 5px;">Total Data: <?= $total_all ?> Item</p>
    </div>

    <?php
    if ($total_all > 0) {
        
        // Loop data
        while ($row = $result->fetch_assoc()) {
            
            // Jika nama barang berubah, cetak Header Grup Baru
            if ($row['nama_barang'] != $current_category) {
                
                // Tutup tabel grup sebelumnya jika ini bukan barang pertama
                if ($current_category != '') {
                    echo "</tbody></table></div>"; 
                }
                
                // Update kategori saat ini
                $current_category = $row['nama_barang'];
                
                // Reset counter nomor urut untuk grup baru
                $group_counter = 1; 
                
                ?>
                
                <div class="group-wrapper">
                    <div class="group-header">
                        <div class="group-title">
                            <i class="fas fa-box"></i> 
                            <?= htmlspecialchars($row['nama_barang']) ?>
                        </div>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="40%">Nama Barang</th>
                                <th width="50%">Kode Unik / Nomor Seri</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
            }
            
            // Cetak baris data
            ?>
            <tr>
                <td style="font-weight: bold;"><?= $group_counter++ ?></td>
                <td><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                <td class="code-bold"><?= htmlspecialchars($row['nomor_unik']) ?></td>
            </tr>
            <?php
        } // End While
        
        // Tutup tabel grup terakhir setelah loop selesai
        if ($current_category != '') {
            echo "</tbody></table></div>";
        }
        
    } else {
        echo "<div style='text-align:center; padding: 50px; font-weight:bold; font-size:18px;'>Tidak ada data barang untuk dicetak.</div>";
    }
    ?>

    <div style="margin-top: 50px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px;" class="no-print">
        Dokumen ini dicetak secara otomatis oleh Sistem Inventaris.
    </div>
</div>

</body>
</html>