<?php

namespace Tests\Unit\Services;

use App\Models\Sppg;
use App\Models\ProductionSchedule;
use App\Services\Financial\InvoiceGenerationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ReflectionMethod;

class InvoiceGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_correct_amount_for_grade_a()
    {
        $service = new InvoiceGenerationService();
        $sppg = Sppg::factory()->create(['grade' => 'A']);
        
        // Mocking the production schedules count or ensuring logic works 
        // We will test the private method 'prepareInvoiceData' via Reflection if needed, 
        // or just rely on the public method. 
        // For simplicity and to test calculation logic specifically, let's use Reflection to access prepareInvoiceData
        // which seems to hold the rate logic.
        
        $method = new ReflectionMethod(InvoiceGenerationService::class, 'prepareInvoiceData');
        $method->setAccessible(true);
        
        // Create 1 active day
        ProductionSchedule::factory()->create([
            'sppg_id' => $sppg->id,
            'tanggal' => Carbon::today(),
            'status' => 'Selesai'
        ]);

        $result = $method->invoke($service, $sppg, Carbon::today(), Carbon::today());

        // Grade A rate: 6,000,000
        $this->assertEquals(6000000, $result['amount']);
    }

    public function test_it_calculates_correct_amount_for_grade_c()
    {
        $service = new InvoiceGenerationService();
        $sppg = Sppg::factory()->create(['grade' => 'C']);
        
        $method = new ReflectionMethod(InvoiceGenerationService::class, 'prepareInvoiceData');
        $method->setAccessible(true);
        
        ProductionSchedule::factory()->create([
            'sppg_id' => $sppg->id,
            'tanggal' => Carbon::today(),
            'status' => 'Selesai'
        ]);

        $result = $method->invoke($service, $sppg, Carbon::today(), Carbon::today());

        // Grade C rate: 3,000,000
        $this->assertEquals(3000000, $result['amount']);
    }

    public function test_it_uses_grade_a_rate_as_default_for_unknown_grade()
    {
        $service = new InvoiceGenerationService();
        // Create SPPG with unknown grade or null if allowed
        $sppg = Sppg::factory()->create(['grade' => 'Z']); 
        
        $method = new ReflectionMethod(InvoiceGenerationService::class, 'prepareInvoiceData');
        $method->setAccessible(true);
        
        ProductionSchedule::factory()->create([
            'sppg_id' => $sppg->id,
            'tanggal' => Carbon::today(),
            'status' => 'Selesai'
        ]);

        $result = $method->invoke($service, $sppg, Carbon::today(), Carbon::today());

        // EXPECTATION: Should be A rate (6,000,000) based on new requirement
        $this->assertEquals(6000000, $result['amount']);
    }
}
