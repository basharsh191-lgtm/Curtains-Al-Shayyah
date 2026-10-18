<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curtains', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->string('material');
            $table->string('opacity_level');
            $table->text('description')->nullable();
            $table->string('image');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curtains');
    }
};
