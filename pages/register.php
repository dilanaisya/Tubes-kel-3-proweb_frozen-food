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
    <link rel="stylesheet" href="../assets/css/stylelogin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agrave+Pro:wght@100..700&display=swap" rel="stylesheet">
</head>
<body>
    <h1 style="text-align:center;" class="mt-4">Registrasi</h1>
    <p class="pb-3">Belum punya akun? Silahkan registrasi terlebih dahulu ya</p>
    <?php if ($error): ?>
        <div class="alert alert-danger text-center mx-auto login-card"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="register.php">
        <table class="table table-bordered text-center mx-auto login-card">
            <tr class="align-middle"><th class="label-login">Nama</th><td><input type="text" class="form-control mt-4 mb-4" name="nama" required></td></tr>
            <tr class="align-middle"><th class="label-login">Email</th><td><input type="email" class="form-control mt-4 mb-4" name="email" required></td></tr>
            <tr class="align-middle"><th class="label-login">Password</th><td><input type="password" class="form-control mt-4 mb-4" name="password" minlength="6" required></td></tr>
            <tr><th></th><td>
                <button type="submit" class="btn btn-outline-primary"><b>Daftar</b></button>
                <a href="login.php" class="btn btn-link">Sudah punya akun?</a>
            </td></tr>
        </table>
    </form>
</body>
</html>
