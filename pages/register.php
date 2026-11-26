<?php
session_start();
require 'koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $error = 'Data tidak valid. Password minimal 6 karakter.';
    } else {
        $stmt = $koneksi->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password) VALUES (?,?,?)");
            $stmt->bind_param("sss", $nama, $email, $hash);
            $stmt->execute();
            header("Location: login.php?daftar=ok");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Frozfood Selera Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1 style="text-align:center;"><b>Registrasi</b></h1>
    <?php if ($error): ?>
        <div class="alert alert-danger text-center mx-auto" style="width:800px;max-width:95%;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="register.php">
        <table class="table table-bordered text-center mx-auto" style="width:800px;max-width:95%;">
            <tr><th>Nama</th><td><input type="text" class="form-control" name="nama" required></td></tr>
            <tr><th>Email</th><td><input type="email" class="form-control" name="email" required></td></tr>
            <tr><th>Password</th><td><input type="password" class="form-control" name="password" minlength="6" required></td></tr>
            <tr><th></th><td>
                <button type="submit" class="btn btn-outline-success"><b>Daftar</b></button>
                <a href="login.php" class="btn btn-link">Sudah punya akun?</a>
            </td></tr>
        </table>
    </form>
</body>
</html>
