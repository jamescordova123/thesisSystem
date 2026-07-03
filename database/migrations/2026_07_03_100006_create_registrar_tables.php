<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_information', function (Blueprint $table) {
            $table->string('student_number')->nullable()->unique()->after('user_id');
            $table->foreignId('program_id')->nullable()->after('academic_program')->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('program_id')->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('year_level')->nullable()->after('section_id');
            $table->string('enrollment_status')->default('not_enrolled')->after('year_level');
            $table->boolean('is_archived')->default(false)->after('enrollment_status');
        });

        Schema::create('section_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['section_id', 'user_id']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('year_level');
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status');
            $table->timestamp('enrolled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('enrollment_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->decimal('grade', 4, 2)->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'subject_id']);
        });

        Schema::create('student_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('record_type');
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('academic_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_code');
            $table->string('subject_name');
            $table->decimal('grade', 4, 2)->nullable();
            $table->decimal('gpa', 4, 2)->nullable();
            $table->decimal('gwa', 4, 2)->nullable();
            $table->string('academic_standing')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('academic_histories');
        Schema::dropIfExists('student_records');
        Schema::dropIfExists('enrollment_subject');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('section_student');
        Schema::table('student_information', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
            $table->dropConstrainedForeignId('program_id');
            $table->dropColumn(['student_number', 'year_level', 'enrollment_status', 'is_archived']);
        });
    }
};
