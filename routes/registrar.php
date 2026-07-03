<?php

use App\Http\Controllers\Registrar\AcademicHistoryController;
use App\Http\Controllers\Registrar\ExportController;
use App\Http\Controllers\Registrar\ClassListController;
use App\Http\Controllers\Registrar\DashboardController;
use App\Http\Controllers\Registrar\EnrollmentController;
use App\Http\Controllers\Registrar\ReportController;
use App\Http\Controllers\Registrar\SearchController;
use App\Http\Controllers\Registrar\SectionController;
use App\Http\Controllers\Registrar\StudentController;
use App\Http\Controllers\Registrar\StudentRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'registrar'])
    ->prefix('registrar')
    ->name('registrar.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('search', SearchController::class)->name('search');

        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
        Route::get('students/{user}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{user}', [StudentController::class, 'update'])->name('students.update');
        Route::post('students/{user}/archive', [StudentController::class, 'archive'])->name('students.archive');

        Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
        Route::post('sections', [SectionController::class, 'store'])->name('sections.store');
        Route::put('sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('sections/{section}/students', [SectionController::class, 'assignStudent'])->name('sections.assign-student');
        Route::delete('sections/{section}/students/{user}', [SectionController::class, 'removeStudent'])->name('sections.remove-student');
        Route::post('sections/{section}/transfer', [SectionController::class, 'transferStudent'])->name('sections.transfer-student');

        Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');

        Route::get('records', [StudentRecordController::class, 'index'])->name('records.index');
        Route::get('records/{user}', [StudentRecordController::class, 'show'])->name('records.show');
        Route::post('records/{user}', [StudentRecordController::class, 'store'])->name('records.store');
        Route::get('records/{user}/{record}/download', [StudentRecordController::class, 'download'])->name('records.download');

        Route::get('exports/class-lists/{section}', [ExportController::class, 'classList'])->name('exports.class-list');
        Route::get('exports/student-master', [ExportController::class, 'studentMaster'])->name('exports.student-master');
        Route::get('exports/enrollment-summary', [ExportController::class, 'enrollmentSummary'])->name('exports.enrollment-summary');
        Route::get('exports/students-per-section', [ExportController::class, 'studentsPerSection'])->name('exports.students-per-section');
        Route::get('exports/section-capacity', [ExportController::class, 'sectionCapacity'])->name('exports.section-capacity');
        Route::get('exports/enrolled-vs-not', [ExportController::class, 'enrolledVsNot'])->name('exports.enrolled-vs-not');
        Route::get('exports/academic-history/{user}', [ExportController::class, 'academicHistory'])->name('exports.academic-history');

        Route::get('class-lists', [ClassListController::class, 'index'])->name('class-lists.index');
        Route::get('class-lists/{section}', [ClassListController::class, 'show'])->name('class-lists.show');

        Route::get('academic-history', [AcademicHistoryController::class, 'index'])->name('academic-history.index');
        Route::get('academic-history/{user}', [AcademicHistoryController::class, 'show'])->name('academic-history.show');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });
