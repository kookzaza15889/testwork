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
         Schema::create('cooperative_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // เชื่อมกับตาราง users
        $table->string('name')->unique(); // ชื่อสหกรณ์ห้ามซ้ำ
        $table->integer('member_count'); // จำนวนสมาชิก
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // สถานะ
        $table->text('remark')->nullable(); // หมายเหตุ/เหตุผล
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperative_requests');
    }
};
