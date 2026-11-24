<?php
session_start();
include "koneksi.php";

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$id_user = 1;
if (isset($_SESSION["id_user"])) {
    $id_user = $_SESSION["id_user"];
} elseif (isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_user FROM users WHERE email = '$email'"));
    if ($u) {
        $id_user = $u["id_user"];
    }
}

$nama_penerima    = !empty($_POST["nama_penerima"]) ? $_POST["nama_penerima"] : "Pembeli Langsung";
$telepon_penerima = !empty($_POST["telepon_penerima"]) ? $_POST["telepon_penerima"] : "0800000000";
$alamat           = !empty($_POST["alamat"]) ? $_POST["alamat"] : "Belum diisi";

$items = [];
if (!empty($_POST["items"]) && is_array($_POST["items"])) {
    $items = $_POST["items"];
} elseif (!empty($_POST["items_json"])) {
    $decoded = json_decode($_POST["items_json"], true);
    if (is_array($decoded)) {
        $items = $decoded;
    }
}

$total_harga = 0;
$detail_items = [];

foreach ($items as $item) {
    $id_produk = isset($item["id_produk"]) ? (int) $item["id_produk"] : 0;
    $jumlah    = isset($item["jumlah"]) ? (int) $item["jumlah"] : 0;

    if ($id_produk > 0 && $jumlah > 0) {
        $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM products WHERE id_produk = $id_produk"));
        if ($p) {
            $harga_satuan = $p["harga"];
            $total_order  = $harga_satuan * $jumlah;
            $total_harga += $total_order;
            $detail_items[] = [
                "id_produk"    => $id_produk,
                "jumlah"       => $jumlah,
                "harga_satuan" => $harga_satuan,
                "total_order"  => $total_order
            ];
        }
    }
}

if ($total_harga == 0) {
    if (!empty($_POST["total_harga"])) {
        $total_harga = $_POST["total_harga"];
    } elseif (!empty($_GET["total_harga"])) {
        $total_harga = $_GET["total_harga"];
    }
}

if ($total_harga <= 0) {
    mysqli_close($koneksi);
    die("Belum ada data pesanan yang dikirim.");
}

$sqlOrder = "INSERT INTO orders (id_user, nama_penerima, telepon_penerima, alamat, total_harga)
             VALUES ('$id_user', '$nama_penerima', '$telepon_penerima', '$alamat', '$total_harga')";
$hasilOrder = mysqli_query($koneksi, $sqlOrder);

if ($hasilOrder && mysqli_affected_rows($koneksi) > 0) {
    $id_order = mysqli_insert_id($koneksi);

    foreach ($detail_items as $d) {
        $sqlItem = "INSERT INTO order_item (id_order, id_produk, jumlah, harga_satuan, total_order)
                    VALUES ('$id_order', '{$d['id_produk']}', '{$d['jumlah']}', '{$d['harga_satuan']}', '{$d['total_order']}')";
        mysqli_query($koneksi, $sqlItem);
    }

    mysqli_close($koneksi);
    header("Location: akhir.html");
    exit;
}

mysqli_close($koneksi);
echo "Gagal menyimpan pesanan.";
?>