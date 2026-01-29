<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL/MariaDB, we can modify the column using raw SQL or Schema builder if supported.
        // Since enum modification can be db specific, raw SQL is often safest for enums.
        // Existing: enum('status', ['draft', 'published', 'archived'])
        // New: enum('status', ['draft', 'pending_review', 'published', 'archived'])
        
        // This command works for MySQL to modify the enum column. 
        // Note: Check DB type from metadata if crucial, but assuming standard MySQL/MariaDB for this stack.
        // If SQLite (testing), this might fail, but usually standard dev is MySQL/PG.
        
         DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'pending_review', 'published', 'archived') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        // Be careful if there are 'pending_review' values, they might be truncated or cause error.
        // We'll map them back to draft just in case before reverting, or just revert definition.
        
        DB::table('posts')->where('status', 'pending_review')->update(['status' => 'draft']);
        
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft'");
    }
};
