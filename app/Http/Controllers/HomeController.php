<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
        $this->middleware('auth')->except('index', 'view');
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
                'education_area' => 'nullable|string',
                'subject_group' => 'nullable|string',
                'year' => 'nullable|string',
            ],
            [
                'education_area.string' => 'เขตพื้นที่การศึกษาไม่ถูกต้อง',
                'subject_group.string' => 'กลุ่มวิชาเอกไม่ถูกต้อง',
                'year.string' => 'ปีการศึกษาไม่ถูกต้อง',
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
        if ($request->has('education_area') || $request->has('subject_group') || $request->has('year')) {
            $results = Subject_rounds::getSearchResults($request);
        }

        return view('home', compact('educationAreas', 'subjects', 'years', 'results', 'totalSubjects', 'totalPassedExam', 'totalAppointed'));
    }
    public function show($roundYear, $educationAreaId, $roundNumber)
    {
        //@dd($roundYear, $educationAreaId, $roundNumber);
        // ดึงข้อมูลรอบการบรรจุ
        $round = DB::table('subjects_rounds AS sr')
            ->join('education_area AS ea', 'sr.education_area_id', '=', 'ea.id')
            ->join('subjects AS s', 'sr.subject_id', '=', 's.id')
            ->select('sr.*', 'ea.name_education', 's.subject_group')
            ->where([
                'sr.round_year' => $roundYear,
                'sr.education_area_id' => $educationAreaId,
                'sr.round_number' => $roundNumber,
            ])
            ->get();
        //@dd($round);

        if ($round->isEmpty()) {
            return redirect()->route('home')->with('error', 'ไม่พบข้อมูลที่ต้องการ');
        }

        // ดึงรอบล่าสุด
        $latestRound = DB::table('subjects_rounds')
            ->where([
                'round_year' => $roundYear,
                'education_area_id' => $educationAreaId,
            ])
            ->max('round_number');

        return view('view', compact('round', 'latestRound'));
    }
}
