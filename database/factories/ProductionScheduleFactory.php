<?php

namespace Database\Factories;

use App\Models\ProductionSchedule;
use App\Models\Sppg;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionScheduleFactory extends Factory
{
    protected $model = ProductionSchedule::class;

    public function definition(): array
    {
        return [
            'sppg_id' => Sppg::factory(),
            'tanggal' => $this->faker->date(),
            'menu_hari_ini' => 'Menu Test',
            'jumlah' => 100,
            'status' => 'Selesai',
        ];
    }
}
