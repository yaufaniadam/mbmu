<?php

namespace Database\Factories;

use App\Models\Sppg;
use Illuminate\Database\Eloquent\Factories\Factory;

class SppgFactory extends Factory
{
    protected $model = Sppg::class;

    public function definition(): array
    {
        return [
            'nama_sppg' => $this->faker->company,
            'kode_sppg' => $this->faker->unique()->bothify('??######'),
            'status' => 'Operasional / Siap Berjalan',
            'grade' => 'A',
            'is_active' => true,
            'nama_bank' => $this->faker->company,
            'nomor_va' => $this->faker->creditCardNumber,
            'alamat' => $this->faker->address,
            'porsi_besar' => 100,
            'porsi_kecil' => 50,
        ];
    }
}
