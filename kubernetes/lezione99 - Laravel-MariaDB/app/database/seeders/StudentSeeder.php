<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::upsert([
            ['name' => 'Anna Rossi', 'course' => 'Kubernetes'],
            ['name' => 'Luca Bianchi', 'course' => 'Laravel'],
            ['name' => 'Marta Verdi', 'course' => 'Docker'],
        ], ['name'], ['course']);
    }
}
