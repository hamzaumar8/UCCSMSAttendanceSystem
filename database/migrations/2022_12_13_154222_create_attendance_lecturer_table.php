<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_lecturer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('set null');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_lecturer');
    }
};
