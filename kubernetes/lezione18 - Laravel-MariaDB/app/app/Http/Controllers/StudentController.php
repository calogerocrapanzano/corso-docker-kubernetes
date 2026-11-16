<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('students', ['students' => Student::orderBy('name')->get()]);
    }
}
