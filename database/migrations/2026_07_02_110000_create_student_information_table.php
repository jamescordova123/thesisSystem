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
        Schema::create('student_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('school_year');
            $table->string('full_name');
            $table->string('academic_program');
            $table->string('status');
            $table->date('birthdate');
            $table->string('sex');
            $table->unsignedTinyInteger('age');
            $table->string('place_of_birth');
            $table->text('current_address');
            $table->text('permanent_address');
            $table->string('father_name')->nullable();
            $table->string('mother_maiden_name')->nullable();
            $table->string('guardian_name');
            $table->string('primary_contact_number');
            $table->string('facebook_account')->nullable();
            $table->string('facebook_profile')->nullable();
            $table->string('last_school_attended')->nullable();
            $table->string('previous_section')->nullable();
            $table->text('last_school_address')->nullable();
            $table->string('academic_year')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('gwa', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_information');
    }
};
