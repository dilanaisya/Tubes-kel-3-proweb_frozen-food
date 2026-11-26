<?php
// TAMBAHAN: proses login (kode HTML di bawah tetap milik Anda)
session_start();
require 'koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // SELECT * supaya tidak error jika nama kolom lain berbeda
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $cocok = false;
    if ($user) {
        $tersimpan = (string)($user['password'] ?? '');
        if (!empty(password_get_info($tersimpan)['algo'])) {
            
            $cocok = password_verify($pass, $tersimpan);
        } else {
            // password di database masih teks biasa
            $cocok = hash_equals($tersimpan, $pass);
        }
    }

    if ($cocok) {
        $idUser = $user['id_user'] ?? $user['id'] ?? null;
        if ($idUser === null) {
            $error = 'Kolom id pada tabel users tidak ditemukan (id_user / id).';
        } else {
            session_regenerate_id(true);
            $_SESSION['id_user'] = $idUser;
            header("Location: menu.php");
            exit;
        }
    } else {
        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frozfood Selera Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1 style="text-align: center;"><b>Hi! </b>Selamat Datang di Frozfood Selera Jaya</h1>
    <p style="font-size:20px; text-align: center;">Sebelum melakukan pemesanan, mohon untuk melakukan registrasi terlebih dahulu dan pastikan bahwa datamu sesuai.</p>
    <!-- TAMBAHAN: pesan error login -->
    <?php if ($error): ?>
        <p style="text-align: center; color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <br>
    <div id="login">
        <form method="post" action="login.php">
            <table class="table table-bordered text-center mx-auto" style="width: 800px;">
                <tr>
          <th><label for="username" class="mt-3 mb-3"> Email</label></th>
           <td><input type="email" class="form-control mt-3 mb-3" style="padding: 10px 25px;" id="username" name="username" placeholder=" Nama_Akun@gmail.com"></td>
                </tr>

                <tr>
            <th><label for="password" class="mt-3 mb-3">Password</label></th>
           <td><input type="Password" class="form-control mt-3 mb-3" style="padding: 10px 25px;" id="password" name="password"></td>
                </tr>
                
                <tr>
            <th></th>
           <td><button type="submit" class="btn btn-outline-success" id="kirim" name="kirim"><b>Kirim</b></button></td>
                </tr>

            </table>
        
        </form>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>

    <script src="script.js"></script>
</body>
</html>
