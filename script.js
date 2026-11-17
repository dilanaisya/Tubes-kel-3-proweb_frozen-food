
//DATA DAN FUNGSI UMUM
let keranjang = JSON.parse(localStorage.getItem('keranjang')) || [];

function simpanKeranjang() {

    localStorage.setItem('keranjang', JSON.stringify(keranjang));

}

function formatRupiah(angka) {

    return 'Rp' + angka.toLocaleString('id-ID');

}

//FITUR LOGIN
const loginForm = document.querySelector('#login form');

if (loginForm) {

    loginForm.addEventListener('submit', function(event) {

        event.preventDefault();

        const email = document.querySelector('#username').value;

        const password = document.querySelector('#password').value;

        if (email.trim() === '' || password.trim() === '') {

            alert('Email dan password harus diisi!');

        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {

            alert('Format email tidak valid!');

        } else {

            window.location.href = 'menu.html';

        }

    });

}

//FITUR PENCARIAN PRODUK
const searchInput = document.querySelector('#searchBar');

const daftarNama = document.querySelectorAll('.ListProduk');

const daftarNamaContainer = document.querySelector('#NamaProduk');

const menuSection = document.querySelector('#Menu');

const daftarProduk = menuSection

    ? menuSection.querySelectorAll('.card')

    : [];

function cariProduk() {

    if (!searchInput) return;

    const kataKunci = searchInput.value.toLowerCase().trim();

    let jumlahHasil = 0;

    daftarNama.forEach(function(nama) {

        const cocok = nama.textContent.toLowerCase().includes(kataKunci);

        nama.style.display = kataKunci !== '' && cocok ? '' : 'none';

        if (kataKunci !== '' && cocok) {

            jumlahHasil++;

        }

    });

    daftarProduk.forEach(function(produk) {

        const judul = produk.querySelector('h3');

        const kolom = produk.closest('.col-12, .col-md-6, .col-lg-4');

        if (judul && kolom) {

            const cocok = judul.textContent.toLowerCase().includes(kataKunci);

            kolom.style.display = cocok ? '' : 'none';

        }

    });

    if (daftarNamaContainer) {

        daftarNamaContainer.classList.toggle(

            'show',

            kataKunci !== '' && jumlahHasil > 0

        );

    }

}

if (searchInput) {

    searchInput.addEventListener('input', cariProduk);

}

daftarNama.forEach(function(nama) {

    nama.addEventListener('click', function() {

        if (searchInput) {

            searchInput.value = nama.textContent;

            cariProduk();

        }

    });

});

//FITUR TOMBOL + DAN - PADA MENU
if (menuSection) {

    const tombolTambahLama = menuSection.querySelectorAll('.card a.btn');

    tombolTambahLama.forEach(function(tombol) {

        const kartu = tombol.closest('.card');

        if (!kartu) return;

        const namaProduk = kartu.querySelector('h3').textContent.trim();

        const produkAda = keranjang.find(function(produk) {

            return produk.nama === namaProduk;

        });

        const jumlahAwal = produkAda ? produkAda.jumlah : 0;

        const kontrol = document.createElement('div');

        kontrol.className = 'quantity-control';

        kontrol.innerHTML = `

            <button type="button" class="kurangi">−</button>

            <span class="jumlah">${jumlahAwal}</span>

            <button type="button" class="tambah">+</button>

        `;

        tombol.replaceWith(kontrol);

    });

    menuSection.addEventListener('click', function(event) {

        const tombol = event.target;

        if (

            !tombol.classList.contains('tambah') &&

            !tombol.classList.contains('kurangi')

        ) {

            return;

        }

        const kartu = tombol.closest('.card');

        if (!kartu) return;

        const namaProduk = kartu.querySelector('h3').textContent.trim();

        const hargaTeks = kartu.querySelector('p b').textContent;

        const hargaProduk = Number(hargaTeks.replace(/[^\d]/g, ''));

        const gambarProduk = kartu.querySelector('img').src;

        let produkAda = keranjang.find(function(produk) {

            return produk.nama === namaProduk;

        });

        if (tombol.classList.contains('tambah')) {

            if (produkAda) {

                produkAda.jumlah++;

                produkAda.gambar = gambarProduk;

            } else {

                keranjang.push({

                    nama: namaProduk,

                    harga: hargaProduk,

                    gambar: gambarProduk,

                    jumlah: 1

                });

            }

        } else {

            if (produkAda) {

                produkAda.jumlah--;

                if (produkAda.jumlah <= 0) {

                    keranjang = keranjang.filter(function(produk) {

                        return produk.nama !== namaProduk;

                    });

                }

            }

        }

        produkAda = keranjang.find(function(produk) {

            return produk.nama === namaProduk;

        });

        kartu.querySelector('.jumlah').textContent =

            produkAda ? produkAda.jumlah : 0;

        simpanKeranjang();

    });

}

//FITUR HALAMAN KERANJANG
const daftarProdukCek = document.querySelector('#daftarproduk');

const totalElement = document.querySelector('.totalharga');

function tampilkanKeranjang() {

    if (!daftarProdukCek) return;

    daftarProdukCek.innerHTML = '';

    daftarProdukCek.className = 'row g-3';

    let totalHarga = 0;

    if (keranjang.length === 0) {

        daftarProdukCek.textContent =

            'Keranjang masih kosong! Silakan pilih produk terlebih dahulu.';

        if (totalElement) {

            totalElement.textContent = 'Rp0';

        }

        return;

    }

    keranjang.forEach(function(produk, index) {

        const subtotal = produk.harga * produk.jumlah;

        totalHarga += subtotal;

        const kolom = document.createElement('div');

        kolom.className = 'col-12 col-md-6 col-lg-4';

        const item = document.createElement('div');

        item.className = 'card cart-item h-100 shadow';

       const gambar = document.createElement('img');

       if (produk.nama === 'Siomay Mba Mimo') {
           gambar.src = 'Siomay.jpeg';
       } else {
           gambar.src = produk.gambar || '';
       }

        gambar.alt = produk.nama;
        gambar.className = 'card-img-top';
        gambar.style.height = '180px';
        gambar.style.objectFit = 'cover';
        
        const isi = document.createElement('div');

        isi.className = 'card-body';

        const judul = document.createElement('h5');

        judul.textContent = produk.nama;

        const harga = document.createElement('p');

        harga.textContent = 'Harga: ' + formatRupiah(produk.harga);

        const jumlah = document.createElement('p');

        jumlah.textContent = 'Jumlah: ' + produk.jumlah;

        const sub = document.createElement('p');

        sub.textContent = 'Subtotal: ' + formatRupiah(subtotal);

        const kurang = document.createElement('button');

        kurang.type = 'button';

        kurang.className = 'btn btn-success me-2 kurang-cart';

        kurang.textContent = '−';

        kurang.dataset.index = index;

        const tambah = document.createElement('button');

        tambah.type = 'button';

        tambah.className = 'btn btn-success me-2 tambah-cart';

        tambah.textContent = '+';

        tambah.dataset.index = index;

        const hapus = document.createElement('button');

        hapus.type = 'button';

        hapus.className = 'btn btn-danger hapus-cart';

        hapus.textContent = 'Hapus';

        hapus.dataset.index = index;

        isi.appendChild(judul);

        isi.appendChild(harga);

        isi.appendChild(jumlah);

        isi.appendChild(sub);

        isi.appendChild(kurang);

        isi.appendChild(tambah);

        isi.appendChild(hapus);

        item.appendChild(gambar);

        item.appendChild(isi);

        kolom.appendChild(item);

        daftarProdukCek.appendChild(kolom);

    });

    if (totalElement) {

        totalElement.textContent = formatRupiah(totalHarga);

    }

}

if (daftarProdukCek) {

    tampilkanKeranjang();

    daftarProdukCek.addEventListener('click', function(event) {

        const index = Number(event.target.dataset.index);

        if (event.target.classList.contains('tambah-cart')) {

            keranjang[index].jumlah++;

        } else if (event.target.classList.contains('kurang-cart')) {

            keranjang[index].jumlah--;

            if (keranjang[index].jumlah <= 0) {

                keranjang.splice(index, 1);

            }

        } else if (event.target.classList.contains('hapus-cart')) {

            keranjang.splice(index, 1);

        } else {

            return;

        }

        simpanKeranjang();

        tampilkanKeranjang();

    });

}

//VALIDASI TOMBOL LANJUT
const tombolLanjut = document.querySelector('a[href="co.html"]');

if (tombolLanjut) {

    tombolLanjut.addEventListener('click', function(event) {

        if (keranjang.length === 0) {

            event.preventDefault();

            alert('Keranjang masih kosong!');

            window.location.href = 'menu.html';

        }

    });

}

//RINGKASAN CHECKOUT
const ringkasanPesanan = document.querySelector('#ringkasanPesanan');

if (ringkasanPesanan) {

    let totalHarga = 0;

    if (keranjang.length === 0) {

        ringkasanPesanan.textContent = 'Belum ada produk yang dipilih.';

    } else {

        keranjang.forEach(function(produk) {

            const item = document.createElement('p');

            item.textContent =

                produk.nama + ' - ' +

                formatRupiah(produk.harga) +

                ' x ' + produk.jumlah;

            ringkasanPesanan.appendChild(item);

            totalHarga += produk.harga * produk.jumlah;

        });

        const total = document.createElement('h5');

        total.textContent =

            'Total Pembayaran: ' + formatRupiah(totalHarga);

        ringkasanPesanan.appendChild(total);

    }

}

//VALIDASI CHECKOUT
const checkoutForm = document.querySelector('form[action="akhir.html"]');

if (checkoutForm) {

    checkoutForm.addEventListener('submit', function(event) {

        if (keranjang.length === 0) {

            event.preventDefault();

            alert('Keranjang masih kosong!');

            window.location.href = 'menu.html';

            return;

        }

        const metodePembayaran =

            document.querySelector('input[name="method"]:checked');

        const ekspedisi =

            document.querySelector('input[name="ekspedisi"]:checked');

        if (!metodePembayaran || !ekspedisi) {

            event.preventDefault();

            alert('Silakan pilih metode pembayaran dan ekspedisi!');

            return;

        }

        const pesanan = {

            nama: document.querySelector('#customername').value,

            telepon: document.querySelector('#phoneNumber').value,

            alamat: document.querySelector('#address').value,

            catatan: document.querySelector('#notes').value,

            metodePembayaran: metodePembayaran.value,

            ekspedisi: ekspedisi.value,

            produk: keranjang

        };

        localStorage.setItem('dataPesanan', JSON.stringify(pesanan));

        localStorage.setItem('keranjang', JSON.stringify([]));

    });

}

//DETAIL PESANAN
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

            item.textContent =

                produk.nama + ' - ' +

                formatRupiah(produk.harga) +

                ' x ' + produk.jumlah;

            detailPesanan.appendChild(item);

            totalHarga += produk.harga * produk.jumlah;

        });

        const total = document.createElement('h3');

        total.textContent =

            'Total Pembayaran: ' + formatRupiah(totalHarga);

        detailPesanan.appendChild(total);

    }

}