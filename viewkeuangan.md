Setuju, mari kita rapikan konsepnya dulu agar tidak membingungkan ("spaghetti logic") di kode nantinya. Masalah "tumpang tindih" ini sering terjadi karena kita memaksakan satu tampilan/tabel untuk semua role yang kebutuhan bisnisnya beda.

Berikut adalah analisis dan usulan saya berdasarkan brief dan logika bisnis yang Bapak sampaikan:

1. SPPG (Operator Dapur)

Ini adalah unit yang paling "sibuk" transaksinya. Laporannya harus detail layaknya Buku Kas Umum.

Pemasukan:
Dana Operasional (Deposit/Transfer Modal).
Lain-lain (Donasi/Sisa kas).
Pengeluaran:
Belanja Harian (Bahan baku, Gas, dll).
Gaji/Honor Staf.
Bayar Sewa ke Lembaga Pengusul (Ini pengeluaran besar rutin).
Laporan yang Dibutuhkan:
Cashflow Harian (Masuk - Keluar = Sisa Saldo).
Laporan per Kategori (Berapa habis untuk Sayur, Daging, Gaji?).
Kesimpulan: SPPG butuh fitur pencatatan "Pemasukan & Pengeluaran" yang lengkap (CRUD).
2. Lembaga Pengusul (Investor/Supervisor)

Peran mereka lebih statis, sifatnya Contractual. Mereka tidak belanja sayur tiap hari.

Pemasukan:
Terima Sewa dari SPPG (Fixed: Rp 6jt/hari x Jumlah Hari).
Pengeluaran:
Bayar Royalti/Dana Pengembangan ke Kornas (Fixed: 10% dari Sewa).
Laporan yang Dibutuhkan:
Rekap Penerimaan Sewa (Siapa SPPG yang nunggak?).
Rekap Pembayaran ke Kornas (Bukti setor).
Jawaban untuk pertanyaan Bapak:
Apakah perlu tabel pengeluaran/pemasukan manual? TIDAK.
Lembaga Pengusul cukup punya menu "Tagihan Sewa (Invoice Masuk)" (Untuk mantau SPPG bayar) dan "Tagihan Royalti (Invoice Keluar)" (Untuk bayar ke Kornas). Semuanya otomatis generate by system berdasarkan tanggal berjalan. Tidak perlu tombol "Tambah Pengeluaran Manual".
3. Kornas (Regulator)

Puncaknya aliran dana (Royalty) dan pengawas.

Pemasukan:
Terima Royalti dari Lembaga Pengusul.
Pengeluaran:
(Mungkin biaya operasional Kornas, tapi di aplikasi ini fokusnya penerimaan dana pengembangan).
Fungsi Monitoring:
Kornas perlu akses "View Only" ke laporan keuangan SPPG untuk audit (Melihat apakah uang makan digunakan semestinya).
Laporan yang Dibutuhkan:
Total Dana Pengembangan terkumpul Nasional.
Status Kepatuhan Bayar tiap Lembaga Pengusul.
Usulan Perubahan Struktur Menu (Di Admin Panel)

Agar tidak tumpang tindih, saya usulkan menunya dipisah secara tegas berdasarkan Role saat Login:

Jika Login sebagai SPPG:

Menu: Buku Kas (Input Masuk/Keluar Harian).
Menu: Bayar Sewa (List Tagihan Sewa dari Lembaga -> Upload Bukti).
Jika Login sebagai Lembaga Pengusul:

(Hapus menu Buku Kas/Biaya Operasional).
Menu: Penerimaan Sewa (List setoran dari SPPG).
Menu: Bayar Royalti (List tagihan dari Kornas -> Upload Bukti).
Jika Login sebagai Kornas:

Menu: Penerimaan Royalti (Verifikasi setoran Lembaga).
Menu: Monitoring SPPG (Bisa intip Buku Kas SPPG, tapi Read Only).