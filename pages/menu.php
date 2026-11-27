<?php
require 'cek_login.php';
require 'koneksi.php';
$produk = $koneksi->query("SELECT * FROM products ORDER BY id_produk")->fetch_all(MYSQLI_ASSOC);

// Tabel products tidak punya kolom gambar. Normalnya nama file = nama produk + .jpeg.
// Untuk produk yang nama filenya berbeda, tulis di sini: 'Nama di database' => 'nama file'
$gambarKhusus = [
    'Sosis Ayam Premium'       => 'Sosis Ayam Ori.jpeg',
    'Sosis Ayam Keju Pedas'    => 'Sosis Pedas.jpeg',
    'Sosis Sapi El Primo'      => 'Sosis Sapi.jpeg',
    'Bakso Sapi Son Haji Sony' => 'Bakso Sapi.jpeg',
    'Siomay Mba Mimo'          => 'Siomay.jpeg',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selera Jaya - Frozen Food</title>
    <link rel="stylesheet" href="../assets/css/styleMenu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: linear-gradient(90deg, #EAF7FF, #C6E8FA, #A8D6F2);">
    <div id="Beranda">
    <nav class="navbar navbar-expand-lg navbar-light bg-info fixed-top" style="font-family:'Times New Roman',Times,serif;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#Beranda">Frozfood selera jaya</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li><a class="nav-link active" href="#Beranda">Beranda</a></li>
                    <li><a class="nav-link" href="#Menu">Produk</a></li>
                    <li><a class="nav-link" href="#Kontak">Kontak</a></li>
                    <li><a class="nav-link" href="logout.php">Keluar (<?= htmlspecialchars(($_SESSION['nama'] ?? 'Pembeli')) ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>
    </div>

    <!--Search Bar-->
    <section id="SearchBar">
    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Silakan cari produk yang kamu butuhkan">
        <ul id="NamaProduk">
            <?php foreach ($produk as $p): ?>
                <li class="ListProduk"><?= htmlspecialchars($p['produk']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    </section>

    <!--Card Menu dari database-->
    <section id="Menu" class="container">
    <div class="row g-3">
        <?php foreach ($produk as $p):
            $stok = (int)$p['stok'];
        ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 border-info shadow" data-stok="<?= $stok ?>" <?= $stok <= 0 ? 'style="opacity:.5"' : '' ?>>
                <img src="../assets/image/<?= rawurlencode($gambarKhusus[$p['produk']] ?? ($p['produk'] . '.jpeg')) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['produk']) ?>">
                <div class="card-body d-flex flex-column">
                    <h3 class="card-title"><?= htmlspecialchars($p['produk']) ?></h3>
                    <p class="card-text"><?= htmlspecialchars($p['deskripsi']) ?></p>
                    <p class="card-text"><b>Rp<?= number_format($p['harga'], 0, ',', '.') ?></b></p>
                    <p class="card-text text-muted stok-info">Stok: <span class="sisa-stok"><?= $stok ?></span></p>
                    <div class="harga">
                        <?php if ($stok > 0): ?>
                            <a href="#" class="btn btn-success mt-auto">Tambah</a>
                        <?php else: ?>
                            <span class="btn btn-secondary mt-auto disabled">Stok Habis</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    </section>

    <div id="bag" class="text-center mt-5 mb-5">
        <a href="CekUlang.html" class="btn btn-outline-primary"><b>Keranjang</b></a>
    </div>

    <div id="box" class="box box-fixed">
        <a href="https://wa.me/6281340855948" target="_blank">Hubungi admin</a>
    </div>

    <section id="Kontak" class="bg-light text-dark mt-3">
     <div class="container py-3">
        <h2>Kontak:</h2>
        <div class="mb-3">
            <p><i class="fa-solid fa-phone"></i> <a href="tel:+6281340855948" class="text-dark">0813-4085-5948</a></p>
        </div>
        <div>
            <p><i class="fa-brands fa-instagram fa-lg"></i> <a href="#" class="text-dark">Frozfood Selera Jaya</a></p>
        </div>
    </div>
    </section>

    <footer class="bg-light text-dark pb-3">
        <p class="text-center mb-0">&copy; 2026 Frozfood Selera Jaya</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>