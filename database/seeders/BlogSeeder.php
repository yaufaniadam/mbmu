<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $articles = [
            [
                'title' => 'Program Makan Bergizi Muhammadiyah Resmi Diluncurkan',
                'excerpt' => 'Langkah nyata Muhammadiyah dalam mendukung program pemerintah untuk pemenuhan gizi anak bangsa.',
                'content' => '<p>Program Makan Bergizi Muhammadiyah (MBM) resmi diluncurkan sebagai wujud komitmen nyata Muhammadiyah dalam mendukung program pemerintah untuk pemenuhan gizi anak Indonesia.</p><p>Program ini menargetkan jutaan anak di seluruh Indonesia untuk mendapatkan makanan bergizi setiap hari di sekolah-sekolah yang berada di bawah naungan Muhammadiyah.</p><h2>Tujuan Program</h2><p>Tujuan utama dari program ini adalah memastikan setiap anak mendapatkan asupan gizi yang cukup untuk mendukung tumbuh kembang optimal dan proses belajar yang lebih baik.</p>',
            ],
            [
                'title' => 'Pelatihan Juru Masak SPPG Batch Pertama',
                'excerpt' => 'Puluhan juru masak dari berbagai SPPG mengikuti pelatihan intensif memasak bergizi.',
                'content' => '<p>Kornas MBM menyelenggarakan pelatihan intensif untuk para juru masak dari Satuan Pelayanan Pangan Gizi (SPPG) di seluruh Indonesia.</p><p>Pelatihan ini bertujuan untuk meningkatkan keterampilan dalam menyajikan makanan bergizi seimbang dengan standar hygiene yang tinggi.</p><h2>Materi Pelatihan</h2><ul><li>Teknik memasak sehat</li><li>Penyusunan menu bergizi seimbang</li><li>Standar kebersihan dan keamanan pangan</li><li>Manajemen dapur modern</li></ul>',
            ],
            [
                'title' => 'SPPG Pertama di Jawa Timur Mulai Beroperasi',
                'excerpt' => 'Dapur SPPG pertama di wilayah Jawa Timur resmi beroperasi melayani ribuan siswa.',
                'content' => '<p>Satuan Pelayanan Pangan Gizi (SPPG) pertama di wilayah Jawa Timur telah resmi beroperasi dan mulai melayani ribuan siswa setiap harinya.</p><p>SPPG ini dilengkapi dengan fasilitas dapur modern dan tim yang terlatih untuk memastikan kualitas makanan yang disajikan.</p><h2>Kapasitas Pelayanan</h2><p>Dengan kapasitas produksi hingga 2.000 porsi per hari, SPPG ini siap melayani kebutuhan gizi siswa di wilayah sekitar.</p>',
            ],
            [
                'title' => 'Kerjasama dengan Badan Gizi Nasional',
                'excerpt' => 'Muhammadiyah menandatangani MoU dengan Badan Gizi Nasional untuk program makan bergizi.',
                'content' => '<p>Muhammadiyah dan Badan Gizi Nasional (BGN) menandatangani Memorandum of Understanding (MoU) untuk kolaborasi dalam program makan bergizi di sekolah.</p><p>Kerjasama ini mencakup berbagai aspek mulai dari standarisasi menu, pelatihan SDM, hingga monitoring dan evaluasi program.</p><h2>Lingkup Kerjasama</h2><ul><li>Penyusunan standar menu bergizi</li><li>Pelatihan untuk pengelola SPPG</li><li>Pendampingan teknis</li><li>Monitoring berkala</li></ul>',
            ],
            [
                'title' => 'Relawan MBM: Pahlawan di Balik Layar',
                'excerpt' => 'Mengenal lebih dekat para relawan yang berdedikasi dalam program makan bergizi.',
                'content' => '<p>Di balik suksesnya program Makan Bergizi Muhammadiyah, ada ribuan relawan yang bekerja tanpa lelah untuk memastikan setiap anak mendapatkan makanan bergizi.</p><p>Para relawan ini berasal dari berbagai latar belakang, mulai dari ibu rumah tangga hingga mahasiswa yang memiliki kepedulian tinggi terhadap gizi anak.</p><h2>Peran Relawan</h2><p>Relawan berperan dalam berbagai kegiatan mulai dari persiapan bahan makanan, proses memasak, hingga distribusi ke sekolah-sekolah.</p>',
            ],
            [
                'title' => 'Menu Bergizi Seimbang untuk Anak Sekolah',
                'excerpt' => 'Panduan penyusunan menu bergizi seimbang yang direkomendasikan untuk program MBM.',
                'content' => '<p>Tim ahli gizi Kornas MBM telah menyusun panduan menu bergizi seimbang yang disesuaikan dengan kebutuhan anak usia sekolah.</p><p>Menu ini dirancang untuk memenuhi 30-35% kebutuhan gizi harian anak dengan komposisi yang seimbang antara karbohidrat, protein, lemak, vitamin, dan mineral.</p><h2>Contoh Menu Harian</h2><ul><li>Nasi merah dengan lauk ayam goreng</li><li>Tumis sayuran hijau</li><li>Buah segar</li><li>Susu atau jus buah</li></ul>',
            ],
            [
                'title' => 'Monitoring dan Evaluasi Program MBM',
                'excerpt' => 'Sistem monitoring digital memastikan kualitas pelayanan SPPG di seluruh Indonesia.',
                'content' => '<p>Kornas MBM mengembangkan sistem monitoring digital yang canggih untuk memantau kualitas pelayanan SPPG di seluruh Indonesia secara real-time.</p><p>Sistem ini memungkinkan pengawasan terhadap kualitas makanan, jumlah penerima manfaat, dan berbagai indikator kinerja lainnya.</p><h2>Fitur Monitoring</h2><ul><li>Dashboard real-time</li><li>Laporan harian otomatis</li><li>Sistem feedback dari penerima manfaat</li><li>Tracking distribusi</li></ul>',
            ],
            [
                'title' => 'Dampak Positif Program MBM bagi Prestasi Belajar',
                'excerpt' => 'Studi menunjukkan peningkatan konsentrasi dan prestasi belajar siswa penerima program MBM.',
                'content' => '<p>Studi awal menunjukkan bahwa program Makan Bergizi Muhammadiyah memberikan dampak positif terhadap konsentrasi dan prestasi belajar siswa.</p><p>Siswa yang mendapatkan makanan bergizi secara rutin menunjukkan peningkatan fokus saat belajar dan penurunan angka ketidakhadiran.</p><h2>Temuan Utama</h2><ul><li>Peningkatan konsentrasi belajar 25%</li><li>Penurunan ketidakhadiran 15%</li><li>Peningkatan partisipasi aktif di kelas</li></ul>',
            ],
            [
                'title' => 'Standar Keamanan Pangan di SPPG',
                'excerpt' => 'Protokol ketat keamanan pangan diterapkan di seluruh SPPG untuk menjamin kualitas makanan.',
                'content' => '<p>Seluruh SPPG di bawah Kornas MBM menerapkan standar keamanan pangan yang ketat untuk menjamin kualitas dan keamanan setiap makanan yang disajikan.</p><p>Protokol ini mencakup aspek kebersihan, penyimpanan bahan makanan, proses memasak, hingga distribusi.</p><h2>Standar yang Diterapkan</h2><ul><li>Sertifikasi halal</li><li>Standar HACCP</li><li>Pemeriksaan kesehatan rutin untuk staf</li><li>Audit kebersihan berkala</li></ul>',
            ],
            [
                'title' => 'Perluasan Jangkauan Program MBM di 2026',
                'excerpt' => 'Kornas MBM menargetkan perluasan jangkauan program ke lebih banyak provinsi di tahun 2026.',
                'content' => '<p>Kornas MBM menargetkan perluasan jangkauan program ke lebih banyak provinsi di seluruh Indonesia pada tahun 2026.</p><p>Target ini sejalan dengan komitmen untuk memastikan semakin banyak anak Indonesia mendapatkan akses terhadap makanan bergizi di sekolah.</p><h2>Target 2026</h2><ul><li>Penambahan 50 SPPG baru</li><li>Jangkauan 1 juta siswa</li><li>Pelatihan 500 juru masak baru</li><li>Kemitraan dengan 100 lembaga pendidikan</li></ul>',
            ],
        ];

        foreach ($articles as $index => $article) {
            Post::create([
                'user_id' => $admin?->id,
                'title' => $article['title'],
                'slug' => Str::slug($article['title']),
                'excerpt' => $article['excerpt'],
                'content' => $article['content'],
                'featured_image' => null, // Will use Unsplash fallback in view
                'published_at' => now()->subDays(count($articles) - $index),
                'status' => 'published',
            ]);
        }
    }
}
