<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/login.html");
    exit;
}

$email    = trim($_POST['username']);
$password = $_POST['password'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$user  = mysqli_fetch_assoc($hasil);

// Cocok jika password disimpan biasa ATAU disimpan dengan password_hash
if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
    $_SESSION['id_user'] = $user['id_user'];
    header("Location: ../pages/menu.html");
    exit;
}

echo "<script>
    alert('Email atau password salah!');
    window.location.href = '../pages/login.html';
</script>";
?>