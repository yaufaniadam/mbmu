# Dokumen Perancangan Aplikasi Manajemen Makan Bergizi Muhammadiyah **(MBM App)**

**Disusun Oleh:** Yaufani Adam/[Solusidesain.net](http://Solusidesain.net) (Versi 1 : Update 27 Okt 2025\)

### **1\. Pendahuluan**

#### **1.1. Tujuan Dokumen**

Dokumen ini bertujuan untuk memaparkan konsep, arsitektur, dan alur fungsional dari "MBM App", sebuah platform digital terpusat yang dirancang untuk mengelola dan memantau seluruh Satuan Pelayanan Pemenuhan Gizi (SPPG) di bawah naungan Muhammadiyah. Dokumen ini ditujukan kepada klien dan pemangku kepentingan sebagai landasan untuk pengembangan dan pemahaman bersama.

#### **1.2. Latar Belakang Masalah**

Saat ini, data operasional, kelembagaan, dan personel SPPG tersebar di berbagai lokasi, seringkali dalam format manual atau file (seperti Excel). Hal ini menyulitkan Koordinator Nasional (Kornas) untuk melakukan pemantauan, standardisasi, dan pengambilan keputusan strategis secara cepat dan akurat.

#### **1.3. Solusi yang Ditawarkan**

MBM App adalah sebuah aplikasi berbasis web (*web-based*) yang akan menjadi **Satu Sumber Data**. Sistem ini akan mendigitalisasi dan menyentralisasi seluruh data SPPG, mulai dari data kelembagaan, manajemen personel, hingga (di masa depan) operasional harian, yang didukung oleh sistem hak akses berlapis yang ketat.

### **2\. Arsitektur Pengguna dan Hak Akses**

Fondasi utama sistem ini adalah arsitektur pengguna yang fleksibel dan aman. Sistem ini membedakan pengguna berdasarkan dua tingkatan: **Nasional** (yang bisa melihat semua) dan **Lokal** (yang hanya bisa melihat SPPG terkait).

Semua orang yang dapat masuk ke sistem akan disimpan dalam satu tabel data **`Users`**. Yang membedakan hak akses mereka adalah **`Role` (Peran)** yang diberikan kepada mereka.

Peran ini diberikan dalam "konteks" atau "tim" tertentu. Artinya, seorang "Akuntan" tidak bisa melihat data semua SPPG, ia hanya bisa melihat data SPPG tempat ia ditugaskan.

Berikut adalah pembagian peran yang direncanakan:

#### **2.1. Level Nasional (Akses Global)**

Pengguna di level ini memiliki akses ke data agregat nasional dan dapat mengelola data master.

* **Superadmin:** Memiliki akses penuh ke seluruh sistem, termasuk konfigurasi teknis dan manajemen peran.  
* **Ketua Kornas:** Memiliki akses *read-only* (hanya melihat) ke dashboard dan laporan nasional.  
* **Staf Kornas:** Bertugas sebagai administrator harian. Dapat mengelola data master (membuat, mengedit, menghapus SPPG) dan mengelola semua akun pengguna di sistem, serta mengelola sistem keuangan Kornas.

#### **2.2. Level Lokal (Akses Terbatas per SPPG)**

Pengguna di level ini hanya dapat mengakses data SPPG spesifik tempat mereka ditugaskan.

* **Pimpinan Lembaga Pengusul:** Memiliki akses *read-only* untuk melihat dashboard dan laporan dari SPPG-SPPG yang diusulkan oleh lembaganya.  
* **Kepala SPPG:** Bertindak sebagai "Admin Lokal" untuk SPPG yang ia kepalai. Ia dapat mengelola personel (menambah staf, dll) dan operasional harian di SPPG-nya.  
* **PJ Pelaksana:** Peran khusus yang memiliki akses *read-only* untuk memantau dashboard dan laporan SPPG yang mereka awasi.  
* **Staf Fungsional SPPG:**  
  * **Staf Gizi:** Hanya memiliki akses ke modul verifikasi pangan.  
  * **Staf Akuntan:** Hanya memiliki akses ke modul keuangan SPPG.  
  * **Staf Pengantaran:** Hanya memiliki akses ke modul konfirmasi distribusi.

#### **2.3. Lebih Detail tentang Daftar Peran (Roles)**

Berikut adalah 11 peran yang telah didefinisikan dalam sistem:

| Kategori | Nama Peran | Lingkup | Deskripsi Singkat |
| :---- | :---- | :---- | :---- |
| **Nasional** | `Superadmin` | Global | Akses penuh ke sistem, termasuk konfigurasi teknis. |
| **Nasional** | `Ketua Kornas` | Global | Akses read-only ke data dan laporan level nasional. |
| **Nasional** | `Staf Kornas` | Global | Administrator harian. Mengelola data master SPPG dan Pengguna. |
| **Lokal** | `Pimpinan Lembaga Pengusul` | Global | Akses read-only untuk memantau SPPG di bawah lembaganya. |
| **Lokal** | `Kepala SPPG` | Per SPPG | Admin utama untuk satu SPPG. Dapat mengelola staf dan operasional. |
| **Lokal** | `PJ Pelaksana` | Per SPPG | Akses read-only untuk memantau dashboard/laporan SPPG terkait. |
| **Lokal** | `Penerima Kuasa` | Per SPPG | Akses read-only untuk memantau dashboard/laporan SPPG terkait. |
| **Lokal** | `Staf Administrator SPPG` | Per SPPG | Membantu Kepala SPPG, fokus pada manajemen jadwal harian. |
| **Lokal** | `Staf Gizi` | Per SPPG | Fungsional. Hanya mengakses modul verifikasi pangan. |
| **Lokal** | `Staf Akuntan` | Per SPPG | Fungsional. Mengakses modul keuangan dan laporan SPPG. |
| **Lokal** | `Staf Pengantaran` | Per SPPG | Melaporkan bukti pengantaran |

### **3\. Model Data Inti**

Untuk mencapai arsitektur di atas, sistem akan dibangun di atas 4 pilar data utama:

1. **Tabel `Users` (Pengguna):** Menyimpan data pribadi unik untuk **setiap orang** yang bisa login (Nama, Email, Password, Alamat, NIK, No. HP). Ini adalah pusat data personel.  
2. **Tabel `Lembaga Pengusul` (Lembaga):** Data master untuk lembaga-lembaga yang mengusulkan SPPG. Dikelola oleh Kornas. Setiap lembaga memiliki satu Pimpinan (yang merupakan seorang `User`).  
3. **Tabel `SPPG` (Unit Pelayanan):** Data master untuk setiap unit SPPG (Nama SPPG, Kode, Alamat, dst). Dikelola oleh Kornas. Setiap SPPG terhubung ke satu `Lembaga Pengusul` dan memiliki satu `Kepala SPPG` (yang merupakan seorang `User`).  
4. **Tabel `sppg_user_role` (Jembatan Peran):** Ini adalah penghubung yang membuat sistem hak akses berlapis berfungsi. Tabel ini mencatat:  
   * `user_id` (Siapa orangnya)  
   * `sppg_id` (Di SPPG mana dia bertugas)  
   * `role_id` (Apa jabatannya di sana)

### **4\. Fitur Utama (Fase Awal)**

1. **Autentikasi Pengguna:** Halaman login.  
2. **Dashboard Dinamis:** Tampilan dashboard akan berbeda-beda. Superadmin/Kornas melihat peta/grafik nasional, sedangkan Kepala SPPG/PJ Pelaksana hanya melihat statistik untuk SPPG mereka.  
3. **Manajemen SPPG :** Modul untuk Staf Kornas menambah, melihat, mengedit, dan menghapus data master SPPG. Halaman ini akan dilengkapi fitur pencarian, *sorting*, dan *pagination* yang cepat.  
4. **Manajemen Pengguna & Penugasan:** Fitur untuk Superadmin/Kornas membuat akun `User` baru dan menugaskan mereka ke SPPG dengan peran tertentu.  
5. **Impor Data:** Sistem akan dilengkapi dengan *seeder* (skrip impor) untuk menyuntikkan data awal SPPG, Lembaga, dan Personel dari file Excel/CSV yang sudah ada, agar mempercepat proses *go-live*.

### **5\. Alur Bisnis**

Berikut adalah penjabaran alur kerja sistem untuk beberapa skenario penting:

#### **Alur 1: Memasukkan SPPG Baru (oleh Staf Kornas)**

Skenario ini menjelaskan bagaimana sebuah SPPG baru didaftarkan ke dalam sistem hingga siap beroperasi.

1. **Staf Kornas** login ke sistem.  
2. **Staf Kornas** masuk ke menu "Manajemen Lembaga Pengusul".  
3. **Staf Kornas** membuat data **`Lembaga Pengusul`** baru (misal: PDM Jepara), lalu membuat akun **`User`** baru untuk Pimpinan Lembaga tersebut (misal: Bpk. Fulan).  
4. **Sistem** menyimpan data `User` Bpk. Fulan dan memberinya peran `Pimpinan Lembaga Pengusul`.  
5. **Staf Kornas** menautkan `User` Bpk. Fulan ke `Lembaga Pengusul` PDM Jepara sebagai Pimpinan.  
6. **Staf Kornas** masuk ke menu "Manajemen SPPG".  
7. **Staf Kornas** mengklik "Tambah SPPG Baru".  
8. **Staf Kornas** mengisi formulir data SPPG (Nama, Kode, Alamat) dan memilih "PDM Jepara" dari *dropdown* "Lembaga Pengusul".  
9. **Staf Kornas** juga membuat akun **`User`** baru untuk Kepala SPPG (misal: Ibu Sinta).  
10. **Sistem** menyimpan data SPPG baru.  
11. **Staf Kornas** masuk ke menu "Manajemen Penugasan" (atau langsung di halaman SPPG).  
12. **Staf Kornas** memilih `User` Ibu Sinta, memilih peran `Kepala SPPG`, dan memilih "konteks" atau "tim" **SPPG Jepara**.  
13. **Selesai.** Kini, Ibu Sinta dapat login dan sistem akan otomatis mengenalinya sebagai `Kepala SPPG` *hanya* untuk SPPG Jepara.

#### **Alur 2: Kepala SPPG Mendaftarkan Staf Akuntan**

Skenario ini menjelaskan bagaimana admin lokal (Kepala SPPG) mengelola timnya sendiri.

1. **Kepala SPPG** (Ibu Sinta) login ke sistem.  
2. **Sistem**  memeriksa perannya dan hanya menampilkan data dan menu untuk SPPG Jepara. Dashboard nasional tidak terlihat.  
3. **Kepala SPPG** masuk ke menu "Manajemen Staf SPPG".  
4. **Kepala SPPG** mengklik "Tambah Staf Baru".  
5. **Kepala SPPG** mengisi data `User` untuk staf akuntan (misal: Budi).  
6. **Kepala SPPG** memilih peran dari *dropdown* (misal: `Staf Akuntan`).  
7. **Sistem** secara otomatis tahu bahwa peran ini hanya berlaku untuk "konteks" SPPG Jepara (karena yang menambahkannya adalah Ibu Sinta, admin SPPG Jepara).  
8. **Sistem** menyimpan `User` Budi dan menugaskannya sebagai `Staf Akuntan` di SPPG Jepara.  
9. **Selesai.** Saat Budi login, ia hanya akan melihat menu "Keuangan" dan "Laporan" untuk SPPG Jepara.

#### **Alur 3: Kepala SPPG Mendaftarkan Staf Ahli Gizi**

Prose sama dengan ketika menambahkan Akuntan

#### **Alur 3: PJ Pelaksana Melakukan Pemantauan**

Skenario ini menjelaskan alur untuk pemangku kepentingan.

1. Seorang **PJ Pelaksana** login. User ini (misal: Bpk. Agung) telah ditugaskan oleh Kornas untuk mengawasi 1 SPPG  
2. **Sistem** mendeteksi bahwa Bpk. Agung memiliki peran `PJ Pelaksana`.  
3. **Sistem** memeriksa tabel `sppg_user_role` dan menemukan 1 entri untuk `user_id` Bpk. Agung.  
4. Di halaman Dashboard, **Sistem** menampilkan SPPG tempatnya bertugas. (DONE)  
5. **PJ Pelaksana** memilih "SPPG Kudus".  
6. **Sistem** menampilkan dashboard dan data laporan *hanya* untuk SPPG Kudus.  
7. **PJ Pelaksana** memiliki hak akses setara Kepala SPPG. (DONE)

#### **Alur 4: Alur Operasional Harian SPPG (Produksi & Distribusi)**

Skenario ini menjelaskan alur kerja harian di dapur SPPG, yang akan diimplementasikan pada Fase 2\.

1. **Perencanaan Produksi:**  
   * **`Staf Administrator SPPG`** (atau `Kepala SPPG`) login ke sistem.  
   * Masuk ke menu "Produksi Harian" \-\> "Jadwal Produksi".  
   * Mengisi form untuk rencana hari itu (Misal: "Selasa, 28 Okt", Menu: "Nasi, Ayam Teriyaki, Tumis Buncis", Jumlah: "500 porsi", Tujuan: "5 Sekolah").  
   * **Sistem** menyimpan jadwal (implementasi `manage-jadwal-produksi`). Status produksi kini "Menunggu Verifikasi Gizi".  
   * melakukan asesmen mandiri terhadap makanan misalnya jam persiapan dimulai jam 5, mulai masak jam 2 pagi .(DONE)  
   * **`Produksi`** mengisi form *checklist* verifikasi di sistem(DONE)  
2. **Verifikasi Gizi (Quality Control):**  
   * (Dapur memproduksi makanan sesuai jadwal \- *Proses Offline*).  
   * **`Staf Gizi`** login ke sistem.  
   * Masuk ke menu "Verifikasi Pangan".  
   * Sistem menampilkan jadwal hari ini: "500 porsi Ayam Teriyaki \- Status: Menunggu Verifikasi".  
   * **`Staf Gizi`** melakukan asesmen mandiri terhadap makanan yang sudah jadi (rasa, suhu, kebersihan, porsi).  
   * **`Staf Gizi`** mengisi form *checklist* verifikasi di sistem (implementasi `perform-verifikasi-pangan`).  
   * **`Staf Gizi`** mengklik "Lulus Verifikasi" atau "Ditolak" (dengan catatan).  
   * Jika "Lulus Verifikasi", **Sistem** mengubah status produksi menjadi "Menunggu ACC Kepala SPPG".  
3. Approval Kepala SPPG  
   * Jika sudah "ACC", **Sistem** mengubah status produksi menjadi "Siap didistribusikan".(DONE)  
4. **Distribusi & Konfirmasi:**  
   * **`Staf Pengantaran`** (Kurir) login ke sistem (bisa via HP).  
   * Masuk ke menu "Pengantaran Hari Ini".  
   * Sistem menampilkan daftar pengantaran (Misal: "1. SD Muhammadiyah 1 (150 porsi)", "2. Panti Asuhan Aisyiyah (50 porsi)", dst).  
   * (Kurir mengambil makanan dan mengantarkannya \- *Proses Offline*).  
   * Setibanya di lokasi (misal: SD Muhammadiyah 1), **`Staf Pengantaran`** membuka sistem.  
   * Menekan tombol "Konfirmasi Terkirim" untuk SD Muhammadiyah 1 (implementasi `confirm-distribusi`).  
   * (Idealnya, di Fase 2 Lanjutan/Fase 4\) Sistem meminta **`Staf Pengantaran`** untuk mengambil foto bukti serah terima dan mengunggahnya.  
   * **Sistem** mencatat "SD Muhammadiyah 1 \- Terkirim pukul 10:05".  
5. **Pemantauan Real-time:**  
   * Sepanjang hari, **`Kepala SPPG`** atau **`PJ Pelaksana`** dapat membuka "Dashboard Operasional".  
   * **Sistem** menampilkan progres: "Produksi: 500/500 porsi", "Verifikasi: Lulus", "Distribusi: 3 dari 5 lokasi terkirim".  
   * **Selesai.** Alur harian tercatat di sistem.

#### **Alur 5: Lembaga Pengusul Melakukan Pemantauan (DONE)**

1. Seorang Kepala **Lembaga Pengusul** login. User ini (misal: Bpk. Candra) memiliki  3 SPPG  
2. Pada dashboard akan ditampilkan nama SPPG yang diusulkan  
3. Bapak Candra memilih salah satu SPPG untuk melihat laporan SPPG tersebut

#### **Alur 6: Alur Pembayaran ‘Dana Pengembangan’ oleh Lembaga Pengusul kepada Kornas**

1. Setiap Lembaga Pengusul berkewajiban membayarkan dana pengembangan sejumlah 10% dari Rp 6jt rupiah /hari (biaya sewa SPPG) kepada Kornas.  
2. Berarti Jumlah tagihan sebesar Rp 600.000 x jumlah hari /SPPG  
3. Pimpinan Lembaga Pengusul melihat jumlah tagihan tiap SPPG (terdapat keterangan tagihan ini dari tanggal sekian sampai dengan tanggal sekian)  
4. Pilih berkas bukti bayar   
5. Masukkan nama bank  
6. Masukkan nama pemilik rekening  
7. masukkan nomor rekening  
8. masukkan nominal bayar  
9. Klik unggah

#### **Alur 7: Alur Update Profil SPPG oleh Kepala/PJ (DONE)**

### **6\. Rencana Pengembangan (Roadmap)**

Sistem ini dirancang untuk dapat dikembangkan secara bertahap (multi-fase) untuk memastikan adopsi yang lancar dan fungsionalitas yang teruji.

#### **Fase 1: Fondasi & Manajemen Data Master (Saat Ini)**

Fase ini berfokus pada pembangunan infrastruktur inti sistem, sentralisasi data, dan manajemen hak akses. Semua yang tertulis dalam dokumen ini adalah bagian dari Fase 1\.

* **Fitur Utama:**  
  * Manajemen Pengguna (Users).  
  * Arsitektur Peran & Izin  
  * CRUD Data Master Lembaga Pengusul.  
  * CRUD Data Master SPPG.  
  * Dashboard Awal (Statistik Sederhana).  
  * Impor data (Seeding) dari file Excel yang ada.

#### **Fase 2: Modul Operasional Harian SPPG**

Setelah data master terpusat, fase ini berfokus untuk "menghidupkan" peran-peran fungsional di level SPPG dan mendigitalisasi alur kerja harian mereka.

* **Fitur Utama:**  
  * **Modul Produksi & Distribusi:**  
    * Implementasi `manage-jadwal-produksi` (untuk Staf Admin SPPG).  
    * Form untuk Staf Gizi melakukan verifikasi pangan harian (`perform-verifikasi-pangan`).  
    * Form untuk Staf Pengantaran melakukan konfirmasi pengantaran (`confirm-distribusi`), dengan bukti foto.  
  * **Manajemen Relawan & Mitra:**  
    * Implementasi CRUD untuk `manage-sppg-mitra`, `manage-sppg-sekolah`, dan `manage-sppg-relawan`.  
  * **Modul Keuangan:**  
    * Implementasi `manage-sppg-finance`.  
    * Form untuk Staf Akuntan mencatat pemasukan (donasi, deposit kornas) dan pengeluaran harian.  
    * Fitur konfirmasi deposit oleh Staf Kornas (`confirm-kornas-deposit`).

#### **Fase 3: Pelaporan Lanjutan & Integrasi**

Dengan data operasional yang mulai terkumpul, fase ini berfokus pada analisis data dan pelaporan.

* **Fitur Utama:**  
  * **Dashboard Nasional Lanjutan:**  
    * Implementasi `view-national-dashboard` dan `view-national-reports`.  
    * Grafik agregat nasional (pertumbuhan penerima manfaat, total keuangan, biaya operasional rata-rata).  
    * Fitur untuk melihat performa per SPPG.  
  * **Pelaporan Keuangan:**  
    * Laporan Laba Rugi sederhana per SPPG.  
    * Laporan Neraca Saldo (Cashflow) per SPPG.  
    * Laporan Neraca Saldo Kornas

#### **Fase 4: Aplikasi Mobile & Keterlibatan Publik**

Fase ini memperluas jangkauan sistem ke pengguna di lapangan dan publik..

* **Fitur Utama:**  
  * **Aplikasi Mobile (Hybrid/PWA):**  
    * Versi sederhana aplikasi untuk `Staf Pengantaran` (konfirmasi & foto bukti).  
    


