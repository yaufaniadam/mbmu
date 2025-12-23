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
        // 1. Kategori Penerimaan Dana (Incoming Funds)
        $incomeCategories = [
            'Subsidi PP Muhammadiyah',
            'Infaq / Donasi',
            'Dana Talangan',
            'Setoran Lembaga Pengusul',
            'Jasa Giro / Bunga Bank',
            'Pengembalian Dana (Refund)',
            'Bantuan Gizi Nasional (BGN)',
            'Penerimaan Lain-lain',
        ];

        foreach ($incomeCategories as $category) {
            SppgIncomingFundCategory::firstOrCreate(['name' => $category]);
        }

        // 2. Kategori Pengeluaran Operasional (Operating Expenses)
        $expenseCategories = [
            'Gaji & Honorarium Staf',
            'Transportasi & Perjalanan Dinas',
            'Konsumsi & Jamuan Rapat',
            'Alat Tulis Kantor (ATK) & Fotokopi',
            'Tagihan Listrik, Air, & Internet',
            'Sewa & Pemeliharaan Gedung SPPG',
            'Biaya Logistik & Distribusi',
            'Servis & Pemeliharaan Kendaraan',
            'Peralatan Masak & Kebersihan',
            'Promosi, Cetak, & Sosialisasi',
            'Biaya Administrasi Bank',
            'Pengeluaran Lain-lain',
        ];

        foreach ($expenseCategories as $category) {
            OperatingExpenseCategory::firstOrCreate(['name' => $category]);
        }
    }
}
