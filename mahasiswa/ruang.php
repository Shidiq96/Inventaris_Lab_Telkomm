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

// 3. Ambil Data Ruangan (Untuk Grup Luar)
 $ruang_arr = [];
 $ruang_q = mysqli_query($conn, "SELECT * FROM ruangan ORDER BY ruang ASC");
while ($row = mysqli_fetch_assoc($ruang_q)) { 
    $ruang_arr[] = $row; 
}

// 4. Ambil Data Jadwal Lengkap (Query Update: Nama Lengkap)
// Menggunakan LEFT JOIN agar data tetap muncul meskipun ada yang kosong
 $query_jadwal = "SELECT jr.id, jr.hari, jr.jam_ke, jr.waktu_id, w.waktu, jr.kelas_id, jr.matkul_id, jr.ruang_id, jr.dosen_id, 
                 k.nama_kelas, m.nama_matkul, r.ruang, COALESCE(d.nama_lengkap, d.username) as nama_dosen
          FROM jadwal_ruang jr
          LEFT JOIN kelas k ON jr.kelas_id = k.id
          LEFT JOIN mata_kuliah m ON jr.matkul_id = m.id
          JOIN waktu w ON jr.waktu_id = w.id
          JOIN ruangan r ON jr.ruang_id = r.id
          LEFT JOIN users d ON jr.dosen_id = d.id  
          ORDER BY r.ruang ASC, FIELD(jr.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), jr.jam_ke ASC";
          
 $result_jadwal = mysqli_query($conn, $query_jadwal);
 $jadwal_arr = [];
while ($row = mysqli_fetch_assoc($result_jadwal)) {
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
        /* --- CSS STYLING (Diadopsi dari jadwal_ruang.php) --- */
        :root { 
            --primary-color: #0d47a1; 
            --secondary-color: #009879; 
            --accent-color: #f39c12; 
            --danger-color: #e74c3c; 
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
            max-width: 1200px; 
            margin: 0 auto; 
            background: #fff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        h2, h3 { 
            color: var(--primary-color); 
            border-bottom: 2px solid var(--bg-light); 
            padding-bottom: 10px; 
            margin-top: 0; 
        }
        .user-info { margin-bottom: 20px; text-align: right; }
        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 14px; 
            transition: background 0.3s; 
            display: inline-block; 
        }
        .btn-blue { background-color: var(--primary-color); color: #fff; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            font-size: 14px; 
        }
        th, td { 
            border: 1px solid var(--border-color); 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: var(--primary-color); 
            color: white; 
        }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e6f7ff; }

        /* Struktur Grup (Sama dengan Admin) */
        .ruang-group { 
            margin-bottom: 40px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            overflow: hidden; 
            background: #fff; 
        }
        .ruang-header { 
            background: var(--primary-color); 
            color: white; 
            padding: 15px; 
            font-weight: bold; 
            font-size: 18px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }

        .hari-group { 
            border-bottom: 1px solid #eee; 
        }
        .hari-group:last-child { 
            border-bottom: none; 
        }
        .hari-header { 
            background: #e3f2fd; 
            color: var(--primary-color); 
            padding: 10px 15px; 
            font-weight: bold; 
            font-size: 16px; 
            border-bottom: 2px solid var(--secondary-color); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        
        .ruang-group table { 
            margin: 0; 
            border: none; 
        }
        .ruang-group table th { 
            background-color: var(--secondary-color); 
        }
        .ruang-group table th, .ruang-group table td { 
            border-bottom: 1px solid #eee; 
            border-top: none; 
            border-left: none; 
            border-right: none; 
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
    <div class="user-info"> 
        <a href="dashboard.php" class="btn btn-blue">Kembali ke Dashboard</a>
    </div>

    <h2>Jadwal Penggunaan Ruang Lab</h2>

    <!-- TAMPILAN JADWAL (STRUKTUR GRUP: RUANG > HARI > TABEL) -->
    <?php 
    // Loop 1: Group by Ruangan
    foreach($ruang_arr as $ruang) { 
        // Filter jadwal berdasarkan ID ruangan saat ini
        $jadwal_ruang_ini = array_filter($jadwal_arr, function($item) use ($ruang) {
            return $item['ruang_id'] == $ruang['id'];
        });
        
        // Hanya tampilkan jika ada jadwal di ruangan tersebut
        if (count($jadwal_ruang_ini) > 0) {
    ?>
        <div class="ruang-group">
            <!-- Header Ruang (Biru Tua) -->
            <div class="ruang-header">
                <span>Jadwal: Lab. <?= htmlspecialchars($ruang['ruang']); ?></span>
            </div>

            <?php 
            // Loop 2: Group by Hari di dalam Ruangan
            $jadwal_per_hari = [];
            foreach($jadwal_ruang_ini as $j) {
                $jadwal_per_hari[$j['hari']][] = $j;
            }

            foreach($list_hari as $hari) {
                // Cek apakah ada jadwal di hari ini
                if (isset($jadwal_per_hari[$hari])) {
            ?>
                <!-- Header Hari (Biru Muda) -->
                <div class="hari-group">
                    <div class="hari-header"><?= $hari; ?></div>
                    
                    <!-- Loop 3: Isi Tabel -->
                    <table>
                        <thead>
                            <tr>
                                <th width="10%">Jam Ke</th>
                                <th width="15%">Waktu</th>
                                <th width="15%">Kelas</th>
                                <th width="40%">Mata Kuliah</th>
                                <th width="20%">Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($jadwal_per_hari[$hari] as $row) { 
                                // Tampil "-" jika data kosong
                                $disp_kelas = $row['nama_kelas'] ?: '-';
                                $disp_matkul = $row['nama_matkul'] ?: '-';
                                $disp_dosen = $row['nama_dosen'] ?: '-';
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['jam_ke']); ?></strong></td>
                                <td><?= htmlspecialchars($row['waktu']); ?></td>
                                <td><?= htmlspecialchars($disp_kelas); ?></td>
                                <td><?= htmlspecialchars($disp_matkul); ?></td>
                                <!-- Kolom Dosen menggunakan Nama Lengkap -->
                                <td><?= htmlspecialchars($disp_dosen); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div> 
            <?php 
                } // End if isset hari
            } // End foreach hari
            ?>
        </div> 
    <?php 
        } // End if count ruang
    } // End foreach ruang
    ?>

    <!-- Pesan jika tidak ada jadwal -->
    <?php if (count($jadwal_arr) == 0) { ?>
        <div class="empty-state">
            Belum ada jadwal penggunaan laboratorium yang tersedia.
        </div>
    <?php } ?>

</div>

</body>
</html>