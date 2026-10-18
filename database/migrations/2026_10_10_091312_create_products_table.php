<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // ربط مع التصنيفات
            $table->string('name'); // اسم الستارة أو الموديل
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // تفاصيل الخامات، بلد المنشأ، الميزات
            $table->string('fabric_type')->nullable(); // نوع القماش (مخمل، حرير، كتان، إلخ)
            $table->boolean('is_featured')->default(false); // لعرضه في قسم "الأكثر طلباً" أو الواجهة الرئيسية
            $table->boolean('is_active')->default(true); // لظهوره أو إخفائه من الموقع
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
