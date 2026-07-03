<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('student_fee_assessments')) {
            Schema::create('student_fee_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
                $table->foreignId('semester_id')->constrained()->restrictOnDelete();
                $table->decimal('tuition', 12, 2)->default(0);
                $table->decimal('test_paper_fee', 12, 2)->default(0);
                $table->decimal('pta_fee', 12, 2)->default(0);
                $table->decimal('uniform_fee', 12, 2)->default(0);
                $table->decimal('certificates_fee', 12, 2)->default(0);
                $table->decimal('graduation_fee', 12, 2)->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'academic_year_id', 'semester_id'], 'sfa_user_ay_sem_unique');
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_fee_assessment_id')->constrained()->cascadeOnDelete();
                $table->string('official_receipt_number')->unique();
                $table->decimal('amount', 12, 2);
                $table->string('payment_method');
                $table->timestamp('payment_date');
                $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_notifications')) {
            Schema::create('student_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type');
                $table->string('subject');
                $table->text('message');
                $table->timestamp('sent_at')->useCurrent();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_notifications');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_fee_assessments');
    }
};
