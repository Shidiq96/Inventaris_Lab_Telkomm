<?php
session_start();

// 1. Cek Akses: Hanya Mahasiswa yang boleh akses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
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

// 3. Ambil Data Jadwal (Join semua tabel untuk info lengkap)
// Kita urutkan berdasarkan Hari, lalu Ruang, lalu Waktu agar mudah dibaca
 $query = "SELECT 
            jr.hari, 
            r.ruang, 
            w.waktu, 
            k.nama_kelas, 
            m.nama_matkul, 
            u.username as dosen 
          FROM jadwal_ruang jr
          JOIN ruangan r ON jr.ruang_id = r.id
          JOIN waktu w ON jr.waktu_id = w.id
          JOIN kelas k ON jr.kelas_id = k.id
          JOIN mata_kuliah m ON jr.matkul_id = m.id
          JOIN users u ON jr.dosen_id = u.id 
          ORDER BY FIELD(jr.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), r.ruang ASC, w.waktu ASC";

 $result = mysqli_query($conn, $query);
 $jadwal_arr = [];

while ($row = mysqli_fetch_assoc($result)) {
    $jadwal_arr[] = $row;
}

 $list_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Lab - Mahasiswa</title>
    <style>
        /* --- CSS STYLING (Konsisten dengan tampilan Admin) --- */
        :root {
            --primary-color: #009879; /* Warna utama hijau */
            --secondary-color: #0d47a1; /* Warna biru untuk aksen */
            --bg-light: #f4f7f6;
            --text-dark: #333;
            --border-color: #ddd;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: var(--secondary-color);
            border-bottom: 2px solid var(--bg-light);
            padding-bottom: 15px;
            margin-top: 0;
            margin-bottom: 30px;
        }

        /* Tombol Kembali */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: var(--secondary-color);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .back-btn:hover {
            background-color: #08306b;
        }

        /* Group Hari */
        .hari-group {
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            overflow: hidden; /* Agar sudut tabel tidak tembus */
        }

        .hari-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
        }

        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #e6f7ff; }

        /* Responsif untuk HP */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { 
                display: block; 
            }
            thead tr { 
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr { 
                border: 1px solid #ccc; 
                margin-bottom: 10px; 
                border-radius: 5px;
                padding: 10px;
            }
            td { 
                border: none;
                border-bottom: 1px solid #eee; 
                position: relative;
                padding-left: 50%; 
            }
            td:before { 
                position: absolute;
                top: 12px;
                left: 10px;
                width: 45%; 
                padding-right: 10px; 
                white-space: nowrap;
                font-weight: bold;
                color: var(--secondary-color);
            }
            
            /* Label data saat di HP */
            td:nth-of-type(1):before { content: "No"; }
            td:nth-of-type(2):before { content: "Ruangan"; }
            td:nth-of-type(3):before { content: "Waktu"; }
            td:nth-of-type(4):before { content: "Kelas"; }
            td:nth-of-type(5):before { content: "Mata Kuliah"; }
            td:nth-of-type(6):before { content: "Dosen"; }
        }
        
        .empty-state {
            text-align: center;
            color: #888;
            padding: 40px;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
    
    <h2>Jadwal Penggunaan Laboratorium</h2>

    <?php if (count($jadwal_arr) > 0) { ?>
        
        <?php foreach($list_hari as $hari) { 
            // Filter jadwal berdasarkan hari
            $jadwal_hari = array_filter($jadwal_arr, function($row) use ($hari) {
                return $row['hari'] === $hari;
            });

            // Hanya tampilkan grup hari jika ada jadwalnya
            if (count($jadwal_hari) > 0) {
        ?>
            <div class="hari-group">
                <div class="hari-header">
                    <span>Hari <?= $hari; ?></span>
                    <span style="font-size: 0.8em; font-weight: normal; opacity: 0.8;">
                        <?= count($jadwal_hari); ?> Jadwal
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Ruangan</th>
                            <th width="20%">Waktu</th>
                            <th width="15%">Kelas</th>
                            <th width="30%">Mata Kuliah</th>
                            <th width="15%">Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reset index array agar nomor urut mulai dari 1 setiap hari
                        foreach(array_values($jadwal_hari) as $index => $row) { 
                        ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['ruang']); ?></strong>
                            </td>
                            <td><?= htmlspecialchars($row['waktu']); ?></td>
                            <td><span style="background:#e0f7fa; color:#006064; padding:2px 6px; border-radius:4px; font-size:12px;"><?= htmlspecialchars($row['nama_kelas']); ?></span></td>
                            <td><?= htmlspecialchars($row['nama_matkul']); ?></td>
                            <td><?= htmlspecialchars($row['dosen']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php 
            } 
        } 
    ?>
    
    <?php } else { ?>
        <div class="empty-state">
            Belum ada jadwal penggunaan laboratorium yang tersedia.
        </div>
    <?php } ?>

</div>

</body>
</html>