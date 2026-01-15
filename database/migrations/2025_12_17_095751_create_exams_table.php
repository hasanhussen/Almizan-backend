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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->date('exam_date');
            $table->double('total_marks');
            $table->integer('success_rate')->nullable();
            $table->string('exam_term');
            $table->enum('exam_type', [
                'quiz', 
                'midterm', 
                'final', 
                'assignment', 
                'project', 
                'participation', 
                'oral', 
                'practice', 
                'makeup'
            ])->nullable();
            $table->enum('year', ['1st', '2nd', '3rd', '4th'])->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('actual_duration')->default(0); // in minutes
            $table->enum('exam_status',['0','1','2'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
