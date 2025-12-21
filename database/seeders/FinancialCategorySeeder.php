<?php

namespace Database\Seeders;

use App\Models\OperatingExpenseCategory;
use App\Models\SppgIncomingFundCategory;
use Illuminate\Database\Seeder;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kategori Dana Masuk (Incoming Funds)
        $incomeCategories = [
            'Subsidi PP Muhammadiyah',
            'Donasi / Infaq',
            'Dana Talangan',
            'Setoran Lembaga Pengusul',
            'Bunga Bank / Jasa Giro',
            'Refund',
            'BGN',
            'Lain-lain',
        ];

        foreach ($incomeCategories as $category) {
            SppgIncomingFundCategory::firstOrCreate(['name' => $category]);
        }

        // 2. Kategori Biaya Operasional (Operating Expenses)
        $expenseCategories = [
            'Gaji & Honorarium Staff',
            'Transportasi & Perjalanan Dinas',
            'Konsumsi & Rapat',
            'ATK & Perlengkapan Kantor',
            'Listrik, Air, & Internet',
            'Sewa & Operasional Gedung',
            'Biaya Distribusi / Logistik',
            'Pemeliharaan Aset & Kendaraan',
            'Promosi & Sosialisasi',
            'Administrasi Bank',
            'Lain-lain',
        ];

        foreach ($expenseCategories as $category) {
            OperatingExpenseCategory::firstOrCreate(['name' => $category]);
        }
    }
}
