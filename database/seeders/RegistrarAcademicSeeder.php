<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class RegistrarAcademicSeeder extends Seeder
{
    public function run(): void
    {
        Program::query()->upsert([
            ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['code'], ['name', 'is_active', 'updated_at']);

        AcademicYear::query()->upsert([
            ['label' => '2025-2026', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['label' => '2026-2027', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['label'], ['is_active', 'updated_at']);

        Semester::query()->upsert([
            ['name' => '1st Semester', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '2nd Semester', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Summer', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['name'], ['sort_order', 'is_active', 'updated_at']);
    }
}
