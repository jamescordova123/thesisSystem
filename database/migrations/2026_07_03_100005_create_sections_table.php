<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('year_level');
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('adviser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('max_capacity')->default(40);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->unique(['program_id', 'year_level', 'semester_id', 'academic_year_id', 'name'], 'sections_unique_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
