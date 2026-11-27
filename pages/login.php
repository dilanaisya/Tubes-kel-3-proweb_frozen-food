<?php
session_start();
require 'koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $koneksi->prepare("SELECT id_user, nama, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama'];
        header("Location: menu.php");
        exit;
    }
    $error = 'Email atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frozfood Selera Jaya</title>
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
    <h1 class="mt-4"><b>Hi! </b>Selamat Datang di Frozfood Selera Jaya</h1>
    <p>Sebelum melakukan pemesanan, silakan login. Belum punya akun? <a href="register.php">Registrasi di sini</a>.</p><br>

    <?php if (isset($_GET['daftar'])): ?>
        <div class="alert alert-success text-center mx-auto" style="width:800px;max-width:95%;">Registrasi berhasil, silakan login.</div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger text-center mx-auto" style="width:800px;max-width:95%;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div id="login">
        <form method="post" action="login.php">
            <table class="table table-bordered text-center mx-auto login-card">
                <tr class="align-middle">
                    <th class="label-login"><label for="username">Email</label></th>
                    <td><input type="email" class="form-control mt-4 mb-4" id="username" name="email" placeholder="Nama_Akun@gmail.com"></td>
                </tr>
                <tr class="align-middle">
                    <th class="label-login"><label for="password">Password</label></th>
                    <td><input type="password" class="form-control mt-4 mb-4" id="password" name="password"></td>
                </tr>
                <tr class="align-middle">
                    <th></th>
                    <td><button type="submit" class="btn btn-outline-primary" id="kirim" name="kirim"><b>Kirim</b></button></td>
                </tr>
            </table>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
