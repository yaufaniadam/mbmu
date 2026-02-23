<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = Role::where('name', 'Ketua Kornas')->first();
        if ($role) {
            $role->name = 'Ketua Kornas';
            $role->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = Role::where('name', 'Ketua Kornas')->first();
        if ($role) {
            $role->name = 'Ketua Kornas';
            $role->save();
        }
    }
};
