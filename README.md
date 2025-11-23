PWL-PROJEK-STS: Website Billing Warnet

Website ini adalah sistem billing warnet berbasis web yang memungkinkan manajemen waktu, saldo, transaksi, dan pemantauan PC dalam satu platform. Sistem ini dilengkapi fitur saldo/top up, anti-cheat restart, pencatatan transaksi tambahan, serta dashboard untuk pemilik dan operator.

Fitur Utama
- Login & Registrasi Member
- Manajemen saldo (top up, cek saldo)
- Timer sesi bermain otomatis
- Penyewaan game
- Pembelian makanan/minuman
- Riwayat transaksi
- Dashboard admin/operator
- Anti-cheat restart (otomatis logout jika waktu habis)
- Notifikasi dan pengumuman

Teknologi yang Digunakan
- PHP (Native, tanpa framework)
- MySQL (database)
- HTML, CSS, JavaScript (Frontend)
- AJAX untuk komunikasi asinkron

Struktur Folder
- main/: Halaman utama member (home, billing, food, dsb)
- Dahsboard/: Halaman admin/operator
- connection/: Koneksi database
- css/: File CSS
- js/: File JavaScript
- FOODS/, GAMES/, FOTO/: Asset gambar

Cara Instalasi & Menjalankan Proyek
1. Clone repository ini:
	
	git clone https://github.com/virgiliolawrencez-design/PWL-PROJEK-STS.git
	
2. Import database dari file SQL (jika tersedia) ke MySQL, atau buat tabel sesuai kebutuhan di connection/db-connection.php.
3. Pastikan konfigurasi database di connection/db-connection.phpsudah sesuai.
4. Jalankan server lokal (misal: Laragon/XAMPP) dan akses melalui browser ke localhost/PWL-PROJEK-STS/main/member.php.

Cara Menggunakan
1. Login/buat akun di halaman member
2. Isi saldo jika diperlukan
3. Sewa game atau beli makanan menggunakan saldo
4. Timer akan berjalan selama sesi bermain
5. Jika waktu habis, otomatis logout
6. Riwayat transaksi akan tersimpan otomatis
