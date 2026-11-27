<?php
require 'cek_login.php';
require 'koneksi.php';

function gagal($pesan) {
    echo "<script>alert(" . json_encode($pesan) . "); window.location.href='menu.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: menu.php");
    exit;
}

// 1. Ambil & validasi input dari co.html
$nama      = trim($_POST['customer'] ?? '');
$telepon   = trim($_POST['telepon'] ?? '');
$alamat    = trim($_POST['alamat'] ?? '');
$catatan   = trim($_POST['catatan'] ?? '');
$metode    = $_POST['method'] ?? '';
$ekspedisi = $_POST['ekspedisi'] ?? '';
$items     = json_decode($_POST['items_json'] ?? '[]', true);

if ($nama === '' || $telepon === '' || $alamat === '') gagal('Data penerima belum lengkap.');
if (!in_array($metode, ['transfer', 'tunai'], true)) gagal('Metode pembayaran tidak valid.');
if (!in_array($ekspedisi, ['solitare', 'prioritas', 'ekonomi'], true)) gagal('Ekspedisi tidak valid.');
if (!is_array($items) || count($items) === 0) gagal('Keranjang kosong.');

// Tabel orders tidak punya kolom ekspedisi & catatan,
// jadi keduanya digabung ke kolom "alamat" (tipe TEXT).
$alamatLengkap = $alamat
    . "\nEkspedisi: " . ucfirst($ekspedisi)
    . "\nCatatan: " . ($catatan !== '' ? $catatan : '-');

// 2. Validasi bukti transfer (file disimpan setelah id_order diketahui)
$extBukti = null;
$folderBukti= __DIR__ . '/../assets/bukti/';
if ($metode === 'transfer') {
    if (!isset($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) gagal('Bukti pembayaran wajib diupload.');
    $extBukti = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
    if (!in_array($extBukti, ['jpg', 'jpeg', 'png', 'pdf'], true)) gagal('Bukti harus berformat JPG, PNG, atau PDF.');
    if ($_FILES['upload']['size'] > 2 * 1024 * 1024) gagal('Ukuran bukti maksimal 2 MB.');
}

// 3. Simpan ke database (satu transaksi)
$fileBukti = null;
$koneksi->begin_transaction();
try {
    $total = 0;
    $baris = [];

    foreach ($items as $it) {
        $namaProduk = trim($it['nama'] ?? '');
        $jumlah     = (int)($it['jumlah'] ?? 0);
        if ($namaProduk === '' || $jumlah < 1) throw new Exception('Data keranjang tidak valid.');

        // Harga & stok diambil dari DATABASE, bukan dari browser
        $stmt = $koneksi->prepare("SELECT id_produk, harga, stok FROM products WHERE produk = ? FOR UPDATE");
        $stmt->bind_param("s", $namaProduk);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();

        if (!$p) throw new Exception("Produk '$namaProduk' tidak ditemukan di database.");
        if ($p['stok'] < $jumlah) throw new Exception("Stok '$namaProduk' tidak cukup (sisa {$p['stok']}).");

        $harga = (int)$p['harga'];
        $total += $harga * $jumlah;
        $baris[] = [(int)$p['id_produk'], $jumlah, $harga];
    }

    // Tabel orders (status otomatis 'Pending')
    $idUser = (int)$_SESSION['id_user'];
    $stmt = $koneksi->prepare(
        "INSERT INTO orders (id_user, nama_penerima, telepon_penerima, alamat, total_harga) VALUES (?,?,?,?,?)"
    );
    $stmt->bind_param("isssi", $idUser, $nama, $telepon, $alamatLengkap, $total);
    $stmt->execute();
    $idOrder = $koneksi->insert_id;

    // Tabel order_item + kurangi stok
    $stmtItem = $koneksi->prepare(
        "INSERT INTO order_item (id_order, id_produk, jumlah, harga_satuan, total_order) VALUES (?,?,?,?,?)"
    );
    $stmtStok = $koneksi->prepare("UPDATE products SET stok = stok - ? WHERE id_produk = ?");
    foreach ($baris as [$idProduk, $jumlah, $harga]) {
        $subtotal = $harga * $jumlah;
        $stmtItem->bind_param("iiiii", $idOrder, $idProduk, $jumlah, $harga, $subtotal);
        $stmtItem->execute();
        $stmtStok->bind_param("ii", $jumlah, $idProduk);
        $stmtStok->execute();
    }

    // Tabel payment (status_bayar otomatis 'Pending')
    $metodeTersimpan = ($metode === 'transfer') ? 'Transfer' : 'Tunai';
    $stmt = $koneksi->prepare("INSERT INTO payment (id_order, metode, jumlah_bayar) VALUES (?,?,?)");
    $stmt->bind_param("isi", $idOrder, $metodeTersimpan, $total);
    $stmt->execute();

    // Bukti transfer: tidak ada kolomnya, jadi nama file memakai nomor pesanan
    // contoh: uploads/bukti_12.jpg  (pesanan dengan id_order = 12)
    if ($extBukti !== null) {
        $fileBukti = $folderBukti . 'bukti_' . $idOrder . '.' . $extBukti;
        if (!move_uploaded_file($_FILES['upload']['tmp_name'], $fileBukti)) {
            throw new Exception('Gagal menyimpan bukti pembayaran.');
        }
    }

    $koneksi->commit();
    header("Location: akhir.html");
    exit;

} catch (Exception $e) {
    $koneksi->rollback();
    if ($fileBukti && file_exists($fileBukti)) @unlink($fileBukti);
    gagal($e->getMessage());
}




