<?php
// Taruh di baris paling atas halaman yang wajib login: require 'cek_login.php';
// Setelah login berhasil, login.php harus mengisi: $_SESSION['id_user']
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
