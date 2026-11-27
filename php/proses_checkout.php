<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/menu.html");
    exit;
}

if (!isset($_SESSION['id_user'])) {
    echo "<script>
        alert('Silakan login terlebih dahulu!');
        window.location.href = '../pages/login.html';
    </script>";
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$nama    = $_POST['customer'];
$telepon = $_POST['telepon'];
$alamat  = $_POST['alamat'];
$metode  = $_POST['method'];
$items   = json_decode($_POST['items_json'], true);

if (!is_array($items) || count($items) == 0) {
    echo "<script>
        alert('Keranjang masih kosong!');
        window.location.href = '../pages/menu.html';
    </script>";
    exit;
}

try {
    mysqli_begin_transaction($koneksi);

    // 1. Ambil id_produk dan harga dari database, lalu hitung total
    $stmtProduk = mysqli_prepare($koneksi, "SELECT id_produk, harga FROM products WHERE produk = ?");
    $total  = 0;
    $daftar = array();

    foreach ($items as $item) {
        $nama_produk = $item['nama'];
        $jumlah      = (int) $item['jumlah'];

        mysqli_stmt_bind_param($stmtProduk, "s", $nama_produk);
        mysqli_stmt_execute($stmtProduk);
        $hasil  = mysqli_stmt_get_result($stmtProduk);
        $produk = mysqli_fetch_assoc($hasil);

        if (!$produk || $jumlah <= 0) {
            throw new Exception("Produk tidak ditemukan di database: " . $nama_produk);
        }

        $subtotal = $produk['harga'] * $jumlah;
        $total   += $subtotal;

        $daftar[] = array(
            'id_produk' => $produk['id_produk'],
            'jumlah'    => $jumlah,
            'harga'     => $produk['harga'],
            'subtotal'  => $subtotal
        );
    }

    // 2. Simpan ke tabel orders
    $stmtOrder = mysqli_prepare($koneksi,
        "INSERT INTO orders (id_user, nama_penerima, telepon_penerima, alamat, total_harga) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmtOrder, "isssd", $id_user, $nama, $telepon, $alamat, $total);
    mysqli_stmt_execute($stmtOrder);
    $id_order = mysqli_insert_id($koneksi);

    // 3. Simpan ke tabel order_item
    $stmtItem = mysqli_prepare($koneksi,
        "INSERT INTO order_item (id_order, id_produk, jumlah, harga_satuan, total_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($daftar as $d) {
        mysqli_stmt_bind_param($stmtItem, "iiidd", $id_order, $d['id_produk'], $d['jumlah'], $d['harga'], $d['subtotal']);
        mysqli_stmt_execute($stmtItem);
    }

    // 4. Simpan ke tabel payment
    $stmtBayar = mysqli_prepare($koneksi,
        "INSERT INTO payment (id_order, metode, jumlah_bayar) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmtBayar, "isd", $id_order, $metode, $total);
    mysqli_stmt_execute($stmtBayar);

    mysqli_commit($koneksi);

    // 5. Simpan bukti transfer (jika ada) ke folder uploads
    if ($metode == "transfer" && isset($_FILES['upload']) && $_FILES['upload']['error'] == 0) {
        if (!is_dir("uploads")) {
            mkdir("uploads");
        }
        $ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, array("jpg", "jpeg", "png", "pdf"))) {
            move_uploaded_file($_FILES['upload']['tmp_name'], "uploads/bukti_" . $id_order . "." . $ext);
        }
    }

    header("Location: ../pages/akhir.html");
    exit;

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo "<script>
        alert(" . json_encode("Pesanan gagal disimpan: " . $e->getMessage()) . ");
        window.location.href = '../pages/menu.html';
    </script>";
}
?>