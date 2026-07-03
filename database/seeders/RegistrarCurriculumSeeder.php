<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class RegistrarCurriculumSeeder extends Seeder
{
    /**
     * @var array<string, array{code: string, name: string, units: int}>
     */
    private array $subjects = [
        'GE101' => ['code' => 'GE101', 'name' => 'Understanding the Self', 'units' => 3],
        'GE102' => ['code' => 'GE102', 'name' => 'Readings in Philippine History', 'units' => 3],
        'IT101' => ['code' => 'IT101', 'name' => 'Introduction to Computing', 'units' => 3],
        'IT102' => ['code' => 'IT102', 'name' => 'Computer Programming 1', 'units' => 3],
        'IT103' => ['code' => 'IT103', 'name' => 'Discrete Mathematics', 'units' => 3],
        'IT201' => ['code' => 'IT201', 'name' => 'Data Structures and Algorithms', 'units' => 3],
        'IT202' => ['code' => 'IT202', 'name' => 'Object-Oriented Programming', 'units' => 3],
        'IT203' => ['code' => 'IT203', 'name' => 'Database Management Systems', 'units' => 3],
        'IT301' => ['code' => 'IT301', 'name' => 'Web Systems and Technologies', 'units' => 3],
        'IT302' => ['code' => 'IT302', 'name' => 'Systems Analysis and Design', 'units' => 3],
        'IT401' => ['code' => 'IT401', 'name' => 'Capstone Project 1', 'units' => 3],
        'IT402' => ['code' => 'IT402', 'name' => 'Capstone Project 2', 'units' => 3],
    ];

    /**
     * @var array<int, array<string>>
     */
    private array $curriculumMap = [
        1 => ['GE101', 'GE102', 'IT101', 'IT102', 'IT103'],
        2 => ['IT201', 'IT202', 'IT203'],
        3 => ['IT301', 'IT302'],
        4 => ['IT401', 'IT402'],
    ];

    public function run(): void
    {
        foreach ($this->subjects as $subject) {
            Subject::query()->updateOrCreate(
                ['code' => $subject['code']],
                [
                    'name' => $subject['name'],
                    'units' => $subject['units'],
                    'is_active' => true,
                ],
            );
        }

        $program = Program::query()->where('code', 'BSIT')->first();
        $firstSemester = Semester::query()->where('name', '1st Semester')->first();
        $secondSemester = Semester::query()->where('name', '2nd Semester')->first();

        if (! $program || ! $firstSemester || ! $secondSemester) {
            return;
        }

        $subjectIds = Subject::query()
            ->whereIn('code', array_keys($this->subjects))
            ->pluck('id', 'code');

        foreach ($this->curriculumMap as $yearLevel => $codes) {
            foreach ([$firstSemester, $secondSemester] as $semester) {
                $curriculum = Curriculum::query()->firstOrCreate([
                    'program_id' => $program->id,
                    'year_level' => $yearLevel,
                    'semester_id' => $semester->id,
                ]);

                $ids = collect($codes)
                    ->map(fn (string $code) => $subjectIds[$code] ?? null)
                    ->filter()
                    ->values()
                    ->all();

                $curriculum->subjects()->syncWithoutDetaching($ids);
            }
        }
    }
}
