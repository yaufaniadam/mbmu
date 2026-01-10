<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, normalize and deduplicate phone numbers
        $this->normalizePhoneNumbers();
        
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable for users who register with phone only
            $table->string('email')->nullable()->change();
            
            // Add unique index to telepon for phone-based login
            // Using a custom index name for clarity
            $table->unique('telepon', 'users_telepon_unique');
        });
    }

    /**
     * Normalize phone numbers and handle duplicates
     */
    protected function normalizePhoneNumbers(): void
    {
        // Get all users with phone numbers
        $users = DB::table('users')
            ->whereNotNull('telepon')
            ->where('telepon', '!=', '')
            ->get();

        $normalizedPhones = [];
        $duplicateIds = [];

        foreach ($users as $user) {
            $normalized = $this->normalizePhone($user->telepon);
            
            if (isset($normalizedPhones[$normalized])) {
                // This is a duplicate - mark for appending suffix
                $duplicateIds[] = $user->id;
            } else {
                $normalizedPhones[$normalized] = $user->id;
                // Update to normalized format
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['telepon' => $normalized]);
            }
        }

        // For duplicates, append a unique suffix
        foreach ($duplicateIds as $index => $userId) {
            $user = DB::table('users')->where('id', $userId)->first();
            $normalized = $this->normalizePhone($user->telepon);
            $newPhone = $normalized . '_dup' . ($index + 1);
            
            DB::table('users')
                ->where('id', $userId)
                ->update(['telepon' => $newPhone]);
        }
    }

    protected function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, add it
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_telepon_unique');
            $table->string('email')->nullable(false)->change();
        });
    }
};
