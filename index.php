<?php
include 'koneksi.php';
session_start();
$error = "";

if (isset($_POST['kirim'])) {
    $email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {
        $query = "INSERT INTO users (email, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['login'] = true;
                $_SESSION['email'] = $email;
                header("Location: menu.html");
                exit;
            } else {
                $error = "Gagal menyimpan data: " . mysqli_stmt_error($stmt);
            }
        } else {
            $error = "Query gagal disiapkan: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Email dan password harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frozfood Selera Jaya</title>
    <link rel="stylesheet2" href="style2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1 style="text-align: center;"><b>Hi! </b>Selamat Datang di Frozfood Selera Jaya</h1>
    <p style="font-size:20px; text-align: center;">Sebelum melakukan pemesanan, mohon untuk melakukan registrasi terlebih dahulu dan pastikan bahwa datamu sesuai.</p><br>
    <div id="login">
        <form method="post" action="">
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