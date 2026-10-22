<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 64)->index();
            $table->string('product_slug')->nullable();
            $table->string('product_name')->nullable();
            $table->timestamps();
            $table->index(['product_slug', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
