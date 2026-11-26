<?php
$koneksi = mysqli_connect("localhost", "root", "", "frozfood");

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
} else {
 // echo "Koneksi berhasil!";
}
mysqli_set_charset($koneksi, "utf8mb4");
?>
