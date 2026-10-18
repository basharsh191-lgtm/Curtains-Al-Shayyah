<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('appointments', 'preferred_time')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('preferred_time')->nullable()->after('preferred_date');
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY status ENUM('pending', 'contacted', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('appointments')->where('status', 'confirmed')->update(['status' => 'contacted']);
            DB::statement("ALTER TABLE appointments MODIFY status ENUM('pending', 'contacted', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }

        if (Schema::hasColumn('appointments', 'preferred_time')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('preferred_time');
            });
        }
    }
};
