<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::role(['Kepala SPPG', 'PJ Pelaksana'])->first();
        
        if (!$user) {
            return;
        }

        $categories = ['Kecelakaan Kerja/Accident', 'Kasus', 'Bencana'];

        foreach ($categories as $category) {
            \App\Models\Complaint::create([
                'user_id' => $user->id,
                'source_type' => $user->hasRole('Kepala SPPG') ? 'sppg' : 'lembaga_pengusul',
                'subject' => $category,
                'content' => "Ini adalah contoh pengaduan kategori $category.",
                'status' => 'Open',
            ]);
        }
    }
}
