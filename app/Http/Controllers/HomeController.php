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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $educationAreas = Admin::getEducationArea();
        $subjects = Subject::getSubjects();
        $years = Subject_rounds::getYears();

        // สถิติรวม
        $totalSubjects = Subject::count();
        $totalPassedExam = Subject_rounds::sum('passed_exam');
        $totalAppointed = Subject_rounds::sum('vacancy');

        $results = null;
        if ($request->has('education_area') || $request->has('subject_group') || $request->has('year')) {
            $results = Subject_rounds::getSearchResults($request);
        }

        return view('home', compact(
            'educationAreas',
            'subjects',
            'years',
            'results',
            'totalSubjects',
            'totalPassedExam',
            'totalAppointed'
        ));
    }
}
