<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects_rounds', function (Blueprint $table) {
            $table->id();
            $table->Integer('round_year'); //ปีการบรรจุ
            $table->Integer('education_area_id'); //รหัสสถานศึกษา
            $table->Integer('round_number'); //รอบการเรียกบรรจุ
            $table->Integer('subject_id'); //รหัสวิชา
            $table->Integer('passed_exam'); //ผู้สอบผ่านขึ้นบัญชี (จำนวน)
            $table->Integer('appointed'); //รับการบรรจุและแต่งตั้งแล้ว (จำนวน)
            $table->Integer('vacancy'); //บรรจุรอบนี้ (จำนวน)
            $table->Integer('remaining'); //คงเหลือ
            $table->string('notes'); // หมายเหตุ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects_rounds');
    }
};
