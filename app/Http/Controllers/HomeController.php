<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Subject;
use App\Models\Subject_rounds;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $validated = $request->validate(
            [
                'education_area' => 'nullable|string|max:255|regex:/^[0-9]+$/',
                'subject_group' => 'nullable|string|max:255|regex:/^[0-9]+$/',
                'year' => 'nullable|string|max:4|regex:/^[0-9]{4}$/',
            ],
            [
                'education_area.string' => 'เขตพื้นที่การศึกษาไม่ถูกต้อง',
                'education_area.max' => 'เขตพื้นที่การศึกษาไม่ถูกต้อง',
                'education_area.regex' => 'เขตพื้นที่การศึกษาต้องเป็นตัวเลขเท่านั้น',
                'subject_group.string' => 'กลุ่มวิชาเอกไม่ถูกต้อง', 
                'subject_group.max' => 'กลุ่มวิชาเอกไม่ถูกต้อง',
                'subject_group.regex' => 'กลุ่มวิชาเอกต้องเป็นตัวเลขเท่านั้น',
                'year.string' => 'ปีการศึกษาไม่ถูกต้อง',
                'year.max' => 'ปีการศึกษาไม่ถูกต้อง',
                'year.regex' => 'ปีการศึกษาต้องเป็นตัวเลข 4 หลักเท่านั้น',
            ],
        );

        $educationAreas = Admin::getEducationArea();
        $subjects = Subject::getSubjects();
        $years = Subject_rounds::getYears();

        // สถิติรวม
        $totalSubjects = Subject::count();
        $totalPassedExam = Subject_rounds::where('round_number', 1)->sum('passed_exam');
        $totalAppointed = Subject_rounds::sum('vacancy');

        $results = null;
        if ($request->filled('education_area') || $request->filled('subject_group') || $request->filled('year')) {
            $results = Subject_rounds::getSearchResults($validated);
        }

        return view('home', compact('educationAreas', 'subjects', 'years', 'results', 'totalSubjects', 'totalPassedExam', 'totalAppointed'));
    }
}
