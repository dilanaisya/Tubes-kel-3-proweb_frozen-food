<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/login.html");
    exit;
}

$SESSION["login"] = $email;

header("Location: ../pages1/menu.html");
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frozfood Selera Jaya</title>
    <link rel="stylesheet" href="../assets/css/stylelogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agrave+Pro:wght@100..700&display=swap" rel="stylesheet">
</head>

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