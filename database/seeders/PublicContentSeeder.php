<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeSlider;
use App\Models\HomeFeature;
use App\Models\TeamMember;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

class PublicContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Home Sliders
        $sliders = [
            [
                'title' => 'Menu Terbaik',
                'description' => 'Kami menyajikan menu makanan bergizi seimbang untuk tumbuh kembang anak indonesia.',
                'image_path' => 'sliders/menu-terbaik.jpg', // Placeholder
                'link_url' => '#',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Edukasi Gizi',
                'description' => 'Program edukasi gizi untuk orang tua dan anak-anak sekolah.',
                'image_path' => 'sliders/edukasi-gizi.jpg', // Placeholder
                'link_url' => '#',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Dapur Sehat',
                'description' => 'Standar higienitas tinggi dalam setiap proses pengolahan makanan.',
                'image_path' => 'sliders/dapur-sehat.jpg', // Placeholder
                'link_url' => '#',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            HomeSlider::updateOrCreate(['title' => $slider['title']], $slider);
        }

        // 2. Home Features
        $features = [
            [
                'title' => 'Bergizi Tinggi',
                'description' => 'Makanan yang disajikan memenuhi standar gizi nasional (4 sehat 5 sempurna).',
                'icon' => 'nutrition', // Material icon name
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Higienis',
                'description' => 'Proses memasak dilakukan di dapur standar SPPG yang bersih dan terawat.',
                'icon' => 'clean_hands',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Halal',
                'description' => 'Seluruh bahan baku dan proses pengolahan dijamin kehalalannya.',
                'icon' => 'verified',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            HomeFeature::updateOrCreate(['title' => $feature['title']], $feature);
        }

        // 3. Team Members
        $team = [
            [
                'name' => 'Dr. M. Nurul Yamin., Drs., M.Si',
                'position' => 'ketua',
                'photo_path' => null, // Placeholder
                'order' => 1,
            ],
            [
                'name' => "Ahmad Ma'ruf, SE., M.Si.",
                'position' => 'sekretaris',
                'photo_path' => null,
                'order' => 2,
            ],
            [
                'name' => 'Hasheena Jasime',
                'position' => 'staf',
                'photo_path' => null,
                'order' => 3,
            ],
        ];

        foreach ($team as $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], [
                'position' => $member['position'],
                'photo_path' => $member['photo_path'],
                'order' => $member['order'],
                'is_active' => true,
            ]);
        }

        // 4. Sample Blog Posts
        // Ensure we have a user to assign as author
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin Content',
                'email' => 'content@mbm.test',
            ]);
        }

        $posts = [
            [
                'title' => 'Pentingnya Sarapan Sehat Bagi Pelajar',
                'slug' => 'pentingnya-sarapan-sehat',
                'excerpt' => 'Sarapan adalah energi awal untuk memulai hari. Simak mengapa sarapan bergizi sangat krusial bagi konsentrasi belajar siswa.',
                'content' => '<p>Sarapan pagi bukan sekadar pengganjal perut, melainkan sumber energi utama bagi otak...</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'user_id' => $user->id,
            ],
            [
                'title' => 'Mengenal Gizi Seimbang: Isi Piringku',
                'slug' => 'mengenal-gizi-seimbang',
                'excerpt' => 'Panduan praktis menyajikan makanan dengan komposisi karbohidrat, protein, sayur, dan buah yang tepat.',
                'content' => '<p>Konsep 4 Sehat 5 Sempurna kini telah disempurnakan menjadi Pedoman Gizi Seimbang...</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'user_id' => $user->id,
            ],
            [
                'title' => 'Kegiatan Masak Bersama di SPPG Jogja',
                'slug' => 'kegiatan-masak-bersama',
                'excerpt' => 'Intip keseruan para relawan dan ibu-ibu PKK dalam menyiapkan 500 porsi makan siang sehat.',
                'content' => '<p>Hari Minggu lalu, dapur umum SPPG Jogja dipenuhi gelak tawa dan aroma masakan lezat...</p>',
                'status' => 'published',
                'published_at' => Carbon::now()->subWeek(),
                'user_id' => $user->id,
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
