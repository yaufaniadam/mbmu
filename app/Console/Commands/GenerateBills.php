<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Sppg;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-bills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan untuk SPPG dan Pengusul';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting bill generation...');
        $now = now();
        $timestamp = $now->format('Y-m-d H:i:s');

        // Ganti get() dengan chunkById() untuk memproses data dalam potongan kecil (batch).
        // Ini menjaga penggunaan RAM tetap rendah dan stabil (O(1) memory), berapapun jumlah datanya.
        Sppg::with(['latestBill', 'lembagaPengusul'])
            ->where('is_active', true)
            ->whereNotNull('tanggal_mulai_sewa')
            ->whereHas('lembagaPengusul')
            ->chunkById(100, function ($sppgs) use ($timestamp) {

                $billsToInsert = [];

                foreach ($sppgs as $sppg) {

                    $lastBill = $sppg->latestBill;

                    $startDate = $lastBill
                        ? Carbon::parse($lastBill->period_end)->addDay()
                        : Carbon::parse($sppg->tanggal_mulai_sewa);

                    $today = Carbon::now();
                    $interval = (int) $sppg->interval_pembayaran_sewa;

                    // Safety: Cegah error divide by zero atau infinite loop jika interval tidak valid
                    if ($interval < 1) {
                        $interval = 10;
                    }

                    $daysPending = $startDate->diffInDays($today);

                    // Batasi loop backlog maksimal (misal 500 putaran) untuk mencegah skenario infinite loop
                    // yang bisa menghabiskan memori jika data tanggal kacau (misal tahun 1970).
                    $maxIterations = 500;
                    $iteration = 0;

                    while ($daysPending >= $interval && $iteration < $maxIterations) {

                        $endDate = $startDate->copy()->addDays($interval - 1);
                        $uniqueDateSuffix = $endDate->format('Ymd');

                        // A. SPPG Bill
                        $billsToInsert[] = [
                            'sppg_id' => $sppg->id,
                            'type' => 'sewa_lokal',
                            'billed_to_type' => 'sppg',
                            'billed_to_id' => $sppg->id,
                            'invoice_number' => 'INV-LOC-' . $sppg->kode_sppg . '-' . $uniqueDateSuffix,
                            'period_start' => $startDate->toDateString(),
                            'period_end' => $endDate->toDateString(),
                            'amount' => 6000000 * $interval,
                            'status' => 'unpaid',
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];

                        // B. Kornas Bill
                        $billsToInsert[] = [
                            'sppg_id' => $sppg->id,
                            'type' => 'setoran_kornas',
                            'billed_to_type' => 'pengusul',
                            'billed_to_id' => $sppg->lembagaPengusul->id,
                            'invoice_number' => 'INV-CEN-' . $sppg->kode_sppg . '-' . $uniqueDateSuffix,
                            'period_start' => $startDate->toDateString(),
                            'period_end' => $endDate->toDateString(),
                            'amount' => 600000 * $interval,
                            'status' => 'unpaid',
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];

                        // Advance Loop
                        $startDate = $endDate->addDay();
                        $daysPending -= $interval;
                        $iteration++;
                    }
                }

                // Simpan per batch (per 100 SPPG), lalu kosongkan memori ($billsToInsert)
                // agar tidak menumpuk sampai jutaan baris.
                if (count($billsToInsert) > 0) {
                    foreach (array_chunk($billsToInsert, 1000) as $chunk) {
                        Bill::insertOrIgnore($chunk);
                    }
                }

                // $billsToInsert akan di-reset otomatis saat iterasi chunk berikutnya dimulai
            });

        $this->info('Bill generation process completed.');
    }
}
