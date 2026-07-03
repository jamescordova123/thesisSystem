<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('curriculums')) {
            Schema::create('curriculums', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('year_level');
                $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['program_id', 'year_level', 'semester_id']);
            });
        }

        if (! Schema::hasTable('curriculum_subject')) {
            Schema::create('curriculum_subject', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['curriculum_id', 'subject_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subject');
        Schema::dropIfExists('curriculums');
    }
};
