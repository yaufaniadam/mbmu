# Panduan Pengelolaan Sistem - Kepala SPPG

Dokumen ini berisi panduan lengkap untuk **Kepala SPPG** dalam menggunakan sistem Manajemen Makan Bergizi Gratis (MBM).

## 1. Pengantar
Sebagai Kepala SPPG, Anda memiliki akses penuh untuk mengelola data operasional, SDM, dan keuangan unit SPPG Anda.
Tugas utama Anda di sistem meliputi:
- Melengkapi dan memperbarui profil SPPG.
- Mengelola data staff dan relawan.
- Mengatur menu makanan dan daftar sekolah penerima.
- Melakukan verifikasi produksi dan presensi harian.
- Mengelola tagihan dan pembayaran sewa/operasional.

## 2. Akses Sistem & Dashboard
1. **Login**: Buka halaman login aplikasi dan masuk menggunakan kredensial (Email & Password) yang telah diberikan.
2. **Dashboard**: Setelah login, Anda akan diarahkan ke halaman utama. Menu navigasi terletak di sebelah kiri (sidebar).

## 3. Manajemen Profil SPPG
Menu: **Pengaturan > Profil SPPG**

Halaman ini sangat krusial karena data disini digunakan untuk verifikasi dan operasional. Pastikan semua data terisi dengan benar.

### Bagian Penting:
- **Informasi Dasar**: Nama SPPG dan Kode (otomatis).
- **Foto**:
  - **Foto SPPG**: Foto tampak depan/utama bangunan (Wajib).
  - **Galeri Foto**: Foto fasilitas, dapur, atau kegiatan (Opsional, bisa lebih dari satu).
- **Rekening Bank**: Masukkan Nama Bank dan Nomor Virtual Account (VA) untuk keperluan pencairan dana.
- **Tanggal Operasional**: Tanggal mulai SPPG beroperasi.
- **Kapasitas**: Jumlah porsi besar dan kecil yang mampu diproduksi per hari.
- **Alamat & Peta**:
  - Pilih Provinsi, Kota/Kabupaten, Kecamatan, dan Kelurahan.
  - **Lokasi Peta**: Geser _marker_ (penanda) di peta ke lokasi akurat titik dapur SPPG Anda. Koordinat (Latitude/Longitude) akan terisi otomatis.
- **Dokumen Legalitas (Wajib)**:
  - Upload dokumen dalam format PDF/Gambar (Max 10MB).
  - **PKS (Perjanjian Kerjasama)**: Dokumen ini **WAJIB** ada agar SPPG berstatus aktif.
  - Dokumen lain: Izin Operasional, Sertifikat Halal, SLHS, HACCP, dll.

klik tombol **Simpan** di pojok kanan atas atau bawah setelah melakukan perubahan.

## 4. Manajemen SDM (Data Master)
Anda bertanggung jawab memastikan data tim Anda lengkap.

### A. Staff (Karyawan)
Menu: **Data Master > Staff**
- **Tambah Staff**: Klik tombol "New Staff".
- **Isian Form**:
  - **Nama, Telepon, Email**: Wajib diisi (Email harus valid untuk login).
  - **NIK**: Wajib 16 digit.
  - **Jabatan (Role)**: Pilih jabatan yang sesuai (Misal: Ahli Gizi, Staf Pengantaran, Staf Akuntan, dll).
  - **Password**: Wajib untuk staff baru (min 8 karakter).
- **Edit**: Klik nama staff atau tombol edit untuk mengubah data.

### B. Relawan (Volunteers)
Menu: **Data Master > Relawan** (Jika tersedia di menu Anda)
- Mengelola data relawan yang membantu operasional harian.
- Proses input mirip dengan data staff.

## 5. Operasional Harian

### A. Daftar Penerima MBM (Sekolah)
Menu: **Data Master > Daftar Penerima MBM**
- Melihat daftar sekolah yang dilayani oleh SPPG Anda.
- Pastikan data jumlah siswa dan kontak sekolah sudah benar.

### B. Manajemen Menu
Menu: **Menu Makanan**
- **Buat Menu Baru**:
  - Upload Foto Menu (Tampilan makanan).
  - Isi Nama Menu dan Deskripsi.
  - Tentukan untuk tanggal berapa menu tersebut disajikan.
- Menu ini akan tampil di laporan dan informasi publik (jika diaktifkan).

### C. Evaluasi Mandiri (Verifikasi Produksi)
Menu: **Operasional > Evaluasi Mandiri** (atau Production Verification)
- Wajib dilakukan secara berkala (harian/mingguan sesuai kebijakan).
- **Cara Mengisi**:
  1. Klik "New" / "Buat Evaluasi".
  2. Isi Tanggal Evaluasi.
  3. Sistem akan memunculkan daftar checklist kriteria standar (Kebersihan, Kualitas Bahan, dll).
  4. Untuk setiap poin, pilih status: **Sesuai**, **Tidak Sesuai**, atau **Perlu Perbaikan**.
  5. Tambahkan catatan jika perlu.

### D. Presensi Harian (Bulk)
Menu: **Keuangan > Input Presensi (Bulk)**
- Fitur ini untuk mencatat kehadiran relawan/staff secara cepat dalam satu layar.
- **Cara Menggunakan**:
  1. Pilih Tanggal di bagian atas.
  2. Daftar nama relawan akan muncul.
  3. Klik pilihan status kehadiran (Hadir, Izin, Sakit, Alpha) langsung di tabel.
  4. Perubahan tersimpan otomatis (akan muncul notifikasi "Status Tersimpan").

## 6. Keuangan & Tagihan
Menu: **Keuangan > Tagihan & Pembayaran**

Berfungsi untuk memantau kewajiban pembayaran (seperti biaya sewa atau operasional yang harus disetor).
- **Status Tagihan**:
  - **UNPAID**: Belum dibayar.
  - **WAITING_VERIFICATION**: Bukti bayar sudah diupload, menunggu verifikasi Admin/Kornas.
  - **PAID**: Lunas.
  - **REJECTED**: Pembayaran ditolak (cek alasan penolakan).
- **Cara Melakukan Pembayaran**:
  1. Cari tagihan dengan status **UNPAID**.
  2. Klik tombol **Bayar** (ikon kartu kredit).
  3. Lakukan transfer ke rekening yang tertera di instruksi.
  4. Upload foto/screenshot **Bukti Transfer**.
  5. Simpan. Status akan berubah menjadi **WAITING_VERIFICATION**.
