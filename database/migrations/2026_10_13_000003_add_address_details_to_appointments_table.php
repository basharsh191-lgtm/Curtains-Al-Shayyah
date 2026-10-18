<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('appointments', 'address_details')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->text('address_details')->nullable()->after('address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('appointments', 'address_details')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('address_details');
            });
        }
    }
};
