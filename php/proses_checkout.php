
<?php
session_start();


if (!isset($_SESSION["login"])) {
    header("Location: ../pages1/login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../pages1/co.html");
    exit;
}


$nama = trim($_POST["customer"] ?? "");
$telepon = trim($_POST["telepon"] ?? "");
$alamat = trim($_POST["alamat"] ?? "");
$catatan = trim($_POST["catatan"] ?? "");
$metode = trim($_POST["method"] ?? "");
$ekspedisi = trim($_POST["ekspedisi"] ?? "");

$items_json = $_POST["items_json"] ?? "[]";
$items = json_decode($items_json, true);


if (
    $nama == "" ||
    $telepon == "" ||
    $alamat == "" ||
    $metode == "" ||
    $ekspedisi == "" ||
    !is_array($items) ||
    count($items) == 0
) {
    echo "<script>
        alert('Data pesanan belum lengkap!');
        window.location.href = '../pages1/co.html';
    </script>";
    exit;
}

// Menghitung total harga
$total = 0;

foreach ($items as $item) {
    $harga = (float)($item["harga"] ?? 0);
    $jumlah = (int)($item["jumlah"] ?? 0);

    if ($harga >= 0 && $jumlah > 0) {
        $total += $harga * $jumlah;
    }
}

// Menyimpan pesanan sementara ke session
$_SESSION["pesanan"] = [
    "nama" => $nama,
    "telepon" => $telepon,
    "alamat" => $alamat,
    "catatan" => $catatan,
    "metode" => $metode,
    "ekspedisi" => $ekspedisi,
    "items" => $items,
    "total" => $total
];

// Menuju halaman konfirmasi
header("Location: ../pages1/akhir.html");
exit;
?>
