// Fitur Login
const loginForm = document.querySelector('#login form');

if (loginForm) {
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const email = document.querySelector('#username').value;
        const password = document.querySelector('#password').value;

        if (email.trim() === '' || password.trim() === '') {
            alert("Email dan password harus diisi!");
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
            alert("Format email tidak valid!");
        } else {
    window.location.href = 'menu.html';
       }
    });
}

//Fitur Pencarian Produk
const searchInput = document.querySelector('#searchBar');
const daftarNama = document.querySelectorAll('.ListProduk');
const menuSection = document.querySelector('section#Menu');
const daftarProduk = menuSection
    ? menuSection.querySelectorAll('.card')
    : [];

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const kataKunci = searchInput.value.toLowerCase();

        // Menyaring daftar nama produk
        daftarNama.forEach(function(nama) {
            if (nama.textContent.toLowerCase().includes(kataKunci)) {
                nama.style.display = '';
            } else {
                nama.style.display = 'none';
            }
        });

        // Menyaring kartu produk
        daftarProduk.forEach(function(produk) {
            const namaProduk = produk.querySelector('h3').textContent.toLowerCase();

            if (namaProduk.includes(kataKunci)) {
                produk.style.display = '';
            } else {
                produk.style.display = 'none';
            }
        });
    });
}

//Fitur Keranjang dan Pengaturan Jumlah Produk
const tombolTambah = document.querySelectorAll('#Menu .card a.btn');
const daftarKeranjang = document.querySelector('#isiKeranjang');

const keranjang = JSON.parse(localStorage.getItem('keranjang')) || [];

function tampilkanKeranjang() {
    if (!daftarKeranjang) return;

    daftarKeranjang.innerHTML = '';

    if (keranjang.length === 0) {
        daftarKeranjang.textContent = 'Keranjang masih kosong!';
        return;
    }

    const judul = document.createElement('h3');
    judul.textContent = 'Isi Keranjang:';
    daftarKeranjang.appendChild(judul);

    let totalHarga = 0;

    keranjang.forEach(function(produk, index) {
        const item = document.createElement('div');
        item.className = 'mb-3';

        const nama = document.createElement('p');
        nama.textContent = produk.nama + ' - Rp' +
            produk.harga.toLocaleString('id-ID') +
            ' x ' + produk.jumlah;

        item.appendChild(nama);

        // Tombol Kurang
        const kurang = document.createElement('button');
        kurang.textContent = '-';

        kurang.addEventListener('click', function() {
            produk.jumlah--;

            if (produk.jumlah <= 0) {
                keranjang.splice(index, 1);
            }

            simpanKeranjang();
        });

        item.appendChild(kurang);

        // Tombol Tambah
        const tambah = document.createElement('button');
        tambah.textContent = '+';

        tambah.addEventListener('click', function() {
            produk.jumlah++;
            simpanKeranjang();
        });

        item.appendChild(tambah);

        // Tombol Hapus
        const hapus = document.createElement('button');
        hapus.textContent = 'Hapus';

        hapus.addEventListener('click', function() {
            keranjang.splice(index, 1);
            simpanKeranjang();
        });

        item.appendChild(hapus);
        daftarKeranjang.appendChild(item);

        totalHarga += produk.harga * produk.jumlah;
    });

    const total = document.createElement('h3');
    total.textContent = 'Total: Rp' +
        totalHarga.toLocaleString('id-ID');

    daftarKeranjang.appendChild(total);
}

function simpanKeranjang() {
    localStorage.setItem('keranjang', JSON.stringify(keranjang));
    tampilkanKeranjang();
}

// Tombol Tambah Produk
tombolTambah.forEach(function(tombol) {
    tombol.addEventListener('click', function(event) {
        event.preventDefault();

        const kartu = tombol.closest('.card');
        const namaProduk = kartu.querySelector('h3').textContent;
        const hargaProduk = kartu.querySelector('p b').textContent;
        const harga = Number(hargaProduk.replace(/[^\d]/g, ''));

        const produkAda = keranjang.find(function(produk) {
            return produk.nama === namaProduk;
        });

        if (produkAda) {
            produkAda.jumlah++;
        } else {
            keranjang.push({
                nama: namaProduk,
                harga: harga,
                jumlah: 1
            });
        }

        simpanKeranjang();
        alert(namaProduk + ' berhasil ditambahkan!');
    });
});

// Menampilkan keranjang saat halaman dibuka
tampilkanKeranjang();

// Fitur Ringkasan Checkout
const ringkasanPesanan = document.querySelector('#ringkasanPesanan');

if (ringkasanPesanan) {
    const dataKeranjang = JSON.parse(localStorage.getItem('keranjang')) || [];

    let totalHarga = 0;

    if (dataKeranjang.length === 0) {
        ringkasanPesanan.textContent = 'Belum ada produk yang dipilih.';
    } else {
        dataKeranjang.forEach(function(produk) {
            const item = document.createElement('p');

            item.textContent = produk.nama + ' - Rp' + produk.harga.toLocaleString('id-ID') + ' x ' + produk.jumlah;

            ringkasanPesanan.appendChild(item);

            totalHarga += produk.harga * produk.jumlah;
        });

        const total = document.createElement('h5');
        total.textContent = 'Total Pembayaran: Rp' + totalHarga.toLocaleString('id-ID');

        ringkasanPesanan.appendChild(total);
    }
}

//Fitur Validasi Checkout
const checkoutForm = document.querySelector('form[action="akhir.html"]');

if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(event) {
        const metodePembayaran = document.querySelector('input[name="method"]:checked');
        const ekspedisi = document.querySelector('input[name="ekspedisi"]:checked');

        if (!metodePembayaran) {
            event.preventDefault();
            alert('Silakan pilih metode pembayaran!');
        } else if (!ekspedisi) {
            event.preventDefault();
            alert('Silakan pilih ekspedisi pengiriman!');
        } else {
            const pesanan = {
                nama: document.querySelector('#customername').value,
                telepon: document.querySelector('#phoneNumber').value,
                alamat: document.querySelector('#address').value,
                catatan: document.querySelector('#notes').value,
                metodePembayaran: metodePembayaran.value,
                ekspedisi: ekspedisi.value,
                produk: JSON.parse(localStorage.getItem('keranjang')) || []
            };
            
            localStorage.setItem('dataPesanan', JSON.stringify(pesanan));

            // Mengosongkan keranjang setelah checkout
            localStorage.setItem('keranjang', JSON.stringify([]));
        }
    });
}

//Fitur Menampilkan Detail Pesanan
const detailPesanan = document.querySelector('#detailPesanan');

if (detailPesanan) {
    const pesanan = JSON.parse(localStorage.getItem('dataPesanan'));

    if (pesanan) {
        detailPesanan.innerHTML = '';

        const dataPelanggan = [
            'Nama Penerima: ' + pesanan.nama,
            'Nomor Telepon: ' + pesanan.telepon,
            'Alamat: ' + pesanan.alamat,
            'Catatan: ' + pesanan.catatan,
            'Metode Pembayaran: ' + pesanan.metodePembayaran,
            'Ekspedisi: ' + pesanan.ekspedisi
        ];

        dataPelanggan.forEach(function(data) {
            const item = document.createElement('p');
            item.textContent = data;
            detailPesanan.appendChild(item);
        });

        const judul = document.createElement('h3');
        judul.textContent = 'Produk yang Dibeli:';
        detailPesanan.appendChild(judul);

        let totalHarga = 0;

        pesanan.produk.forEach(function(produk) {
            const item = document.createElement('p');

            item.textContent = produk.nama + ' - Rp' +
                produk.harga.toLocaleString('id-ID') +
                ' x ' + produk.jumlah;

            detailPesanan.appendChild(item);

            totalHarga += produk.harga * produk.jumlah;
        });

        const total = document.createElement('h3');
        total.textContent = 'Total Pembayaran: Rp' +
            totalHarga.toLocaleString('id-ID');

        detailPesanan.appendChild(total);
    }
}

//Fitur Konfirmasi Pesanan
const daftarProdukCek = document.querySelector('#daftarproduk');

if (daftarProdukCek) {
    const dataKeranjang = JSON.parse(localStorage.getItem('keranjang')) || [];

    daftarProdukCek.innerHTML = '';

    let totalHarga = 0;

    if (dataKeranjang.length === 0) {
        daftarProdukCek.textContent =
            'Keranjang masih kosong! Silakan pilih produk terlebih dahulu.';

        const tombolLanjut = document.querySelector('a[href="co.html"]');

        if (tombolLanjut) {
            tombolLanjut.addEventListener('click', function(event) {
                event.preventDefault();

                alert('Keranjang masih kosong! Silakan pilih produk terlebih dahulu.');

                window.location.href = 'menu.html';
            });
        }

    } else {
        dataKeranjang.forEach(function(produk) {
            const subtotal = produk.harga * produk.jumlah;
            totalHarga += subtotal;

            const item = document.createElement('div');
            item.className = 'produk';

            const nama = document.createElement('p');
            nama.textContent = 'Nama produk: ' + produk.nama;

            const jumlah = document.createElement('p');
            jumlah.textContent = 'Jumlah: ' + produk.jumlah;

            const harga = document.createElement('p');
            harga.textContent = 'Harga Satuan: Rp' +
                produk.harga.toLocaleString('id-ID');

            const sub = document.createElement('p');
            sub.textContent = 'Subtotal: Rp' +
                subtotal.toLocaleString('id-ID');

            item.appendChild(nama);
            item.appendChild(jumlah);
            item.appendChild(harga);
            item.appendChild(sub);

            daftarProdukCek.appendChild(item);
        });
    }

    const totalElement = document.querySelector('.totalharga');

    if (totalElement) {
        totalElement.textContent =
            'Rp' + totalHarga.toLocaleString('id-ID');
    }
}