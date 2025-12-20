<?php

namespace Database\Seeders;

use App\Models\ProductionVerificationSetting;
use Illuminate\Database\Seeder;

class ProductionVerificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultChecklist = [
            ['item_name' => 'Kualitas Bahan Baku'],
            ['item_name' => 'Kebersihan Dapur'],
            ['item_name' => 'Rasa Masakan'],
            ['item_name' => 'Kesesuaian Menu'],
            ['item_name' => 'Ketepatan Waktu'],
            ['item_name' => 'Kemasan & Kebersihan'],
        ];

        ProductionVerificationSetting::updateOrCreate(
            ['id' => 1], // Global singleton
            ['checklist_data' => $defaultChecklist]
        );
    }
}
