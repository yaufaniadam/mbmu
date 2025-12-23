<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use App\Models\ProductionSchedule;
use App\Models\Sppg;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateDailyDistributionPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distribution:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate daily distribution plan records for all active SPPGs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $this->info("Checking distribution plans for: {$today->toDateString()}");

        // Skip Sundays (Minggu)
        if ($today->isSunday()) {
            $this->info("⏭️  Skipped: Today is Sunday (Hari Minggu)");
            return Command::SUCCESS;
        }

        // Skip holidays
        if (Holiday::isHoliday($today)) {
            $holiday = Holiday::whereDate('tanggal', $today)->first();
            $this->info("⏭️  Skipped: Today is a holiday ({$holiday->nama})");
            return Command::SUCCESS;
        }

        $this->info("Generating distribution plans for: {$today->toDateString()}");

        // Get all active SPPGs
        $sppgs = Sppg::whereHas('schools')->get();
        
        if ($sppgs->isEmpty()) {
            $this->warn('No active SPPGs found with schools.');
            return Command::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($sppgs as $sppg) {
            // Check if record already exists for today
            $exists = ProductionSchedule::where('sppg_id', $sppg->id)
                ->whereDate('tanggal', $today)
                ->exists();

            if ($exists) {
                $this->line("⏭️  Skipped: {$sppg->nama_sppg} (already exists)");
                $skipped++;
                continue;
            }

            // Calculate initial total portions based on default values
            $initialTotal = $sppg->schools->sum(function ($school) {
                return ($school->default_porsi_besar ?? 0) + ($school->default_porsi_kecil ?? 0);
            });

            // Create new distribution plan
            $schedule = ProductionSchedule::create([
                'sppg_id' => $sppg->id,
                'tanggal' => $today,
                'menu_hari_ini' => '-', // To be filled by admin
                'jumlah' => $initialTotal,
                'status' => 'Direncanakan',
            ]);

            // Create distribution details for each school
            foreach ($sppg->schools as $school) {
                \App\Models\Distribution::create([
                    'jadwal_produksi_id' => $schedule->id,
                    'sekolah_id' => $school->id,
                    'jumlah_porsi_besar' => $school->default_porsi_besar ?? 0,
                    'jumlah_porsi_kecil' => $school->default_porsi_kecil ?? 0,
                    'status_pengantaran' => 'Menunggu',
                ]);
            }

            $this->info("✅ Created: {$sppg->nama_sppg}");
            $created++;
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Created: {$created}");
        $this->info("  Skipped: {$skipped}");
        $this->info("  Total SPPGs: " . $sppgs->count());

        return Command::SUCCESS;
    }
}
