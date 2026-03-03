<?php
session_start();
include "database.php";

if (isset($_POST['login_mhs'])) {
    $kelas_id = intval($_POST['kelas_id']);
    $matkul_id = intval($_POST['matkul_id']);

    // Cek data kelas dan matkul di database
    $stmt = $conn->prepare("SELECT k.nama_kelas, mk.nama_matkul 
                            FROM kelas k, mata_kuliah mk 
                            WHERE k.id=? AND mk.id=?");
    $stmt->bind_param("ii", $kelas_id, $matkul_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
        
        // Set Session Mahasiswa
        $_SESSION['user'] = [
            'role' => 'mahasiswa',
            'kelas_id' => $kelas_id,
            'matkul_id' => $matkul_id,
            'kelas' => $data['nama_kelas'],
            'mata_kuliah' => $data['nama_matkul']
        ];

        // Redirect ke Dashboard Mahasiswa
        header("Location: mahasiswa/dashboard.php");
        exit;
    } else {
        echo "<script src='js/alert.js'></script><script>showAlert('Data Kelas / Mata Kuliah tidak valid!'); window.history.back();</script>";
        exit;
    }
}
?>