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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name'); // اسم الزبون
            $table->string('phone'); // رقم الهاتف (للإتصال أو الواتساب)
            $table->text('address'); // العنوان التفصيلي للمنزل
            $table->date('preferred_date')->nullable(); // التاريخ المفضل للزيارة
            $table->text('notes')->nullable(); // ملاحظات إضافية (عدد الغرف، القياسات التقديرية)
            $table->string('status')->default('pending'); // حالة الطلب للإدارة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
