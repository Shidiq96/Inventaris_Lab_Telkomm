<?php
require_once 'database.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($username == '' || $password == '' || $role == '') {
        $error = "Semua field harus diisi!";
    } else {
        // Cek username sudah ada atau belum
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hash, $role);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error = "Registrasi gagal!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Inventaris</title>
    <link rel="stylesheet" href="zcss/loginregis.css">
</head>
<body>
    <div class="login-container">
    <h2>Register User Baru</h2>
    <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green'>$success</p>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <select name="role" required>
            <option value="">Pilih Role</option>
            <option value="admin">Admin</option>
            <option value="dosen">Dosen</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>
