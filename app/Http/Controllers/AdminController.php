<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Admin;
use App\Models\Subject;
use App\Models\Subject_rounds;
use App\Rules\MatchOldPassword;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // สรุปข้อมูลทั่วไป
        $totalSubjects = Subject::count();
        $totalEducationAreas = Admin::count();
        $totalPassedExam = Subject_rounds::sum('passed_exam');
        $totalAppointed = Subject_rounds::sum('vacancy');

        // สถิติรายเดือน
        $monthlyStats = Subject_rounds::select(DB::raw('DATE_FORMAT(created_at, "%m/%Y") as month'), DB::raw('SUM(vacancy) as total'))->groupBy('month')->orderBy('created_at', 'desc')->limit(12)->get();

        // สถิติตามกลุ่มวิชาเอก
        $subjectsStats = Subject_rounds::select('subjects.subject_group', DB::raw('SUM(vacancy) as total'))->join('subjects', 'subjects.id', '=', 'subjects_rounds.subject_id')->groupBy('subjects.subject_group')->orderBy('total', 'desc')->limit(10)->get();

        // การบรรจุล่าสุด
        $recentAppointments = Subject_rounds::select('subjects_rounds.*', 'subjects.subject_group', 'education_area.name_education')->join('subjects', 'subjects.id', '=', 'subjects_rounds.subject_id')->join('education_area', 'education_area.id', '=', 'subjects_rounds.education_area_id')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.index', compact('totalSubjects', 'totalEducationAreas', 'totalPassedExam', 'totalAppointed', 'monthlyStats', 'subjectsStats', 'recentAppointments'));
    }
    // Education Area
    public function show_education_area()
    {
        $education_area = Admin::getEducationArea();
        return view('admin.show_education_area', compact('education_area'));
    }
    public function education_area_edit(Request $request)
    {
        $validate = $request->validate(
            [
                'id' => 'required|numeric',
            ],
            [
                'id.required' => 'กรุณาระบุรหัสสถานศึกษา',
                'id.numeric' => 'รหัสสถานศึกษาต้องเป็นตัวเลข',
            ],
        );

        $education_area = Admin::getEducationArea($request->id)->first();
        //@dd($education_area);
        if (!$education_area) {
            return redirect()->route('admin.show_education_area')->with('error', 'ไม่พบข้อมูลเขตพื้นที่การศึกษา');
        }
        return view('admin.education_area_edit', compact('education_area'));
    }
    public function education_area_update(Request $request)
    {
        $validate = $request->validate(
            [
                'id' => 'required|numeric',
                'name' => 'required|string|max:255',
            ],
            [
                'id.required' => 'กรุณาระบุรหัสสถานศึกษา',
                'id.numeric' => 'รหัสสถานศึกษาต้องเป็นตัวเลข',
                'name.required' => 'กรุณาระบุชื่อ',
                'name.string' => 'ชื่อต้องเป็นตัวอักษร',
                'name.max' => 'ชื่อต้องมีอย่างน้อย 255 ตัวอักษร',
            ],
        );

        $education_area = Admin::where('id', $request->id)->update(['name_education' => $request->name]);

        return redirect()->route('admin.show_education_area')->with('success', 'อัพเดทข้อมูลเขตพื้นที่การศึกษาเรียบร้อยแล้ว');
    }
    public function education_area_delete(Request $request)
    {
        $validate = $request->validate(
            [
                'id' => 'required|numeric',
            ],
            [
                'id.required' => 'กรุณาระบุรหัสสถานศึกษา',
                'id.numeric' => 'รหัสสถานศึกษาต้องเป็นตัวเลข',
            ],
        );

        $education_area = Admin::where('id', $request->id)->delete();

        return redirect()->route('admin.show_education_area')->with('success', 'ลบข้อมูลเขตพื้นที่การศึกษาเรียบร้อยแล้ว');
    }
    public function subjects_index()
    {
        $subjects = Subject::orderBy('subject_group')->get();
        return view('admin.subjects_index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'subject_group' => 'required|string|max:255|unique:subjects',
            ],
            [
                'subject_group.required' => 'กรุณาระบุชื่อกลุ่มวิชาเอก',
                'subject_group.unique' => 'มีชื่อกลุ่มวิชาเอกนี้อยู่แล้ว',
            ],
        );

        Subject::create([
            'subject_group' => $request->subject_group,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'เพิ่มข้อมูลวิชาเอกเรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate(
            [
                'subject_group' => ['required', 'string', 'max:255', Rule::unique('subjects')->ignore($id)],
            ],
            [
                'subject_group.required' => 'กรุณาระบุชื่อกลุ่มวิชาเอก',
                'subject_group.unique' => 'มีชื่อกลุ่มวิชาเอกนี้อยู่แล้ว',
            ],
        );

        $subject->update([
            'subject_group' => $request->subject_group,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'แก้ไขข้อมูลวิชาเอกเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        // ตรวจสอบการใช้งานในตาราง subjects_rounds
        if (Subject_rounds::where('subject_id', $id)->exists()) {
            return response()->json(
                [
                    'message' => 'ไม่สามารถลบได้เนื่องจากมีการใช้งานในระบบ',
                ],
                422,
            );
        }

        $subject->delete();

        return response()->json([
            'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
        ]);
    }
    public function create_rounds()
    {
        $subjects = Subject::getSubjects();
        $education_area = Admin::getEducationArea();
        //@dd($education_area, $subjects);
        return view('admin.create_subjects', compact('subjects', 'education_area'));
    }
    public function subjects_rounds_create(Request $request)
    {
        // Validate the request
        $request->validate(
            [
                'round_year' => 'required|integer',
                'education_area_id' => 'required|integer', //|exists:education_area,id'
                'round_number' => 'required|integer',
                'items' => 'required|array',
                'items.*.subject_id' => 'required|integer|exists:subjects,id', //|exists:subjects,id
                'items.*.passed_exam' => 'required|integer|min:0', // ผู้สอบผ่านขึ้นบัญชี
                'items.*.vacancy' => 'required|integer|min:0', // จำนวนที่บรรจุ
                'items.*.notes' => 'nullable|string',
                'created_at' => 'required|date|after:1957-01-01|before:2100-12-31', // ตรวจสอบวันที่ให้อยู่ในช่วง พ.ศ. 2500-2643
            ],
            [
                'round_year.required' => 'กรุณาระบุปีการบรรจุ',
                'education_area_id.required' => 'กรุณาระบุรหัสสถานศึกษา',
                'round_number.required' => 'กรุณาระบุรอบการบรรจุ',
                'items.*.subject_id.exists' => 'วิชาเอกที่ระบุไม่มีอยู่ในระบบ',
                'items.*.passed_exam.min' => 'จำนวนผู้สอบผ่านขึ้นบัญชีต้องเป็นตัวเลข',
                'items.*.vacancy.min' => 'จำนวนที่บรรจุต้องเป็นตัวเลข',
                'items.*.vacancy.required' => 'กรุณาระบุจำนวนที่บรรจุ',
                'created_at.required' => 'กรุณาระบุวันที่ประกาศ',
                'created_at.date' => 'วันที่ต้องเป็นวันที่',
                'created_at.after' => 'วันที่ต้องเป็น คริตศักราช',
                'created_at.before' => 'วันที่ต้องเป็น คริตศักราช',
                'items.*.subject_id.required' => 'กรุณาระบุวิชาเอก',
                'items.*.subject_id.integer' => 'รหัสวิชาเอกต้องเป็นตัวเลข',
                'items.*.subject_id.exists' => 'วิชาเอกที่ระบุไม่มีอยู่ในระบบ',
                'items.*.passed_exam.required' => 'กรุณาระบุจำนวนผู้สอบผ่านขึ้นบัญชี',
                'created_at.required' => 'กรุณาระบุวันที่ประกาศ',
                'created_at.date' => 'วันที่ต้องเป็นวันที่',
                'created_at.after' => 'วันที่ต้องเป็น คริตศักราช',
                'created_at.before' => 'วันที่ต้องเป็น คริตศักราช',
            ],
        );
        //@dd($request->all());
        foreach ($request->items as $item) {
            // ตรวจสอบว่าเป็นรอบแรก
            if ($request->round_number == 1) {
                $appointed = 0; // รอบแรก appointed = 0
                $remaining = $item['passed_exam'] - $item['vacancy'];
            } else {
                // ดึงข้อมูลการบรรจุสะสมจากรอบก่อนหน้า
                $previousAppointed = DB::table('subjects_rounds')
                    ->where([
                        'round_year' => $request->round_year,
                        'education_area_id' => $request->education_area_id,
                        'subject_id' => $item['subject_id'],
                    ])
                    ->where('round_number', '<', $request->round_number)
                    ->sum('vacancy');

                $appointed = $previousAppointed + $item['vacancy'];
                $remaining = $item['passed_exam'] - $appointed;
            }

            DB::table('subjects_rounds')->insert([
                'round_year' => $request->round_year,
                'education_area_id' => $request->education_area_id,
                'round_number' => $request->round_number,
                'subject_id' => $item['subject_id'],
                'passed_exam' => $item['passed_exam'],
                'appointed' => $appointed,
                'vacancy' => $item['vacancy'],
                'remaining' => $remaining,
                'notes' => $item['notes'] ?? '',
                'created_at' => $request->created_at,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.subjects.rounds.index')->with('success', 'บันทึกข้อมูลการบรรจุเรียบร้อยแล้ว');
    }
    public function subjects_rounds_index()
    {
        $rounds = DB::table('subjects_rounds')->select('subjects_rounds.round_year', 'subjects_rounds.education_area_id', 'subjects_rounds.round_number', 'subjects_rounds.created_at', 'education_area.name_education', DB::raw('COUNT(*) as subject_count'), DB::raw('SUM(passed_exam) as total_passed'), DB::raw('SUM(appointed) as total_appointed'), DB::raw('SUM(vacancy) as total_vacancy'), DB::raw('SUM(remaining) as total_remaining'))->join('education_area', 'subjects_rounds.education_area_id', '=', 'education_area.id')->groupBy('round_year', 'education_area_id', 'round_number', 'created_at', 'name_education')->orderBy('created_at', 'desc')->paginate(10);

        //@dd($rounds);
        return view('admin.subjects_rounds_index', compact('rounds'));
    }

    public function subjects_rounds_show($roundYear, $educationAreaId, $roundNumber)
    {
        try {
            // ดึงข้อมูลรอบล่าสุด
            $latestRound = DB::table('subjects_rounds')->where('round_year', $roundYear)->where('education_area_id', $educationAreaId)->max('round_number');

            // ดึงข้อมูลรอบปัจจุบัน
            $round = DB::table('subjects_rounds AS sr')
                ->select('sr.*', 'subjects.subject_group', 'education_area.name_education')
                ->join('subjects', 'sr.subject_id', '=', 'subjects.id')
                ->join('education_area', 'sr.education_area_id', '=', 'education_area.id')
                ->where([
                    'sr.round_year' => $roundYear,
                    'sr.education_area_id' => $educationAreaId,
                    'sr.round_number' => $roundNumber,
                ])
                ->get();

            if ($round->isEmpty()) {
                return redirect()->route('admin.subjects.rounds.index')->with('error', 'ไม่พบข้อมูลการบรรจุ');
            }

            // ส่งข้อมูลไปยังวิว
            return view('admin.subjects_rounds_show', [
                'round' => $round,
                'latestRound' => $latestRound ?? $roundNumber, // ถ้าไม่มี latestRound ให้ใช้ roundNumber แทน
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.subjects.rounds.index')->with('error', 'เกิดข้อผิดพลาดในการดึงข้อมูล');
        }
    }

    public function subjects_rounds_delete($roundYear, $educationAreaId, $roundNumber)
    {
        try {
            DB::table('subjects_rounds')->where('round_year', $roundYear)->where('education_area_id', $educationAreaId)->where('round_number', $roundNumber)->delete();

            return redirect()->route('admin.subjects.rounds.index')->with('success', 'ลบข้อมูลการบรรจุเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->route('admin.subjects.rounds.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
        }
    }
    // เพิ่มเมธอดสำหรับแสดงฟอร์มแก้ไข
    public function subjects_rounds_edit($roundYear, $educationAreaId, $roundNumber)
    {
        $subjects = Subject::getSubjects();
        $education_area = Admin::getEducationArea();

        // ดึงข้อมูลรอบการบรรจุ
        $round = DB::table('subjects_rounds')->where('round_year', $roundYear)->where('education_area_id', $educationAreaId)->where('round_number', $roundNumber)->first();

        if (!$round) {
            return redirect()->route('admin.subjects.rounds.index')->with('error', 'ไม่พบข้อมูลการบรรจุ');
        }

        // ดึงข้อมูลวิชาเอกทั้งหมดในรอบนี้
        $items = DB::table('subjects_rounds')->where('round_year', $roundYear)->where('education_area_id', $educationAreaId)->where('round_number', $roundNumber)->get();

        return view('admin.edit_subjects', compact('subjects', 'education_area', 'round', 'items'));
    }

    // เพิ่มเมธอดสำหรับอัพเดทข้อมูล
    public function subjects_rounds_update(Request $request)
    {
        // Validate
        $request->validate(
            [
                'round_year' => 'required|integer',
                'education_area_id' => 'required|integer',
                'round_number' => 'required|integer',
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:subjects_rounds,id',
                'items.*.subject_id' => 'required|integer|exists:subjects,id',
                'items.*.passed_exam' => 'required|integer|min:0',
                'items.*.vacancy' => 'required|integer|min:0',
                'items.*.notes' => 'nullable|string',
                'created_at' => 'required|date',
            ],
            [
                'round_year.required' => 'กรุณาระบุปีการบรรจุ',
                'education_area_id.required' => 'กรุณาระบุรหัสสถานศึกษา',
                'round_number.required' => 'กรุณาระบุรอบการบรรจุ',
            ],
        );

        // อัพเดทข้อมูลแต่ละรายการ
        foreach ($request->items as $item) {
            $appointed = $item['vacancy'];
            $remaining = $item['passed_exam'] - $item['vacancy'];

            DB::table('subjects_rounds')
                ->where('id', $item['id'])
                ->update([
                    'subject_id' => $item['subject_id'],
                    'passed_exam' => $item['passed_exam'],
                    'appointed' => $appointed,
                    'vacancy' => $item['vacancy'],
                    'remaining' => $remaining,
                    'notes' => $item['notes'] ?? '',
                    'updated_at' => now(),
                ]);
        }

        // อัพเดทข้อมูลหลัก
        DB::table('subjects_rounds')
            ->where('round_year', $request->old_round_year)
            ->where('education_area_id', $request->old_education_area_id)
            ->where('round_number', $request->old_round_number)
            ->update([
                'round_year' => $request->round_year,
                'education_area_id' => $request->education_area_id,
                'round_number' => $request->round_number,
                'created_at' => $request->created_at,
            ]);

        return redirect()->route('admin.subjects.rounds.index')->with('success', 'อัพเดทข้อมูลการบรรจุเรียบร้อยแล้ว');
    }
    public function subjects_rounds_next($year, $area, $round)
    {
        // ดึงข้อมูลรอบปัจจุบัน
        $currentRound = DB::table('subjects_rounds AS sr')
            ->select(
                'sr.*',
                'subjects.subject_group',
                'education_area.name_education',
                DB::raw('(
                    SELECT SUM(sr2.vacancy)
                    FROM subjects_rounds sr2
                    WHERE sr2.round_year = sr.round_year
                    AND sr2.education_area_id = sr.education_area_id
                    AND sr2.subject_id = sr.subject_id
                    AND sr2.round_number <= sr.round_number
                ) as total_appointed'), // คำนวณยอดบรรจุสะสม
            )
            ->join('subjects', 'sr.subject_id', '=', 'subjects.id')
            ->join('education_area', 'sr.education_area_id', '=', 'education_area.id')
            ->where([
                'sr.round_year' => $year,
                'sr.education_area_id' => $area,
                'sr.round_number' => $round,
            ])
            ->get();

        // สร้างข้อมูลสำหรับรอบถัดไป
        $nextRoundData = [
            'round_year' => $currentRound[0]->round_year,
            'education_area_id' => $currentRound[0]->education_area_id,
            'round_number' => $currentRound[0]->round_number + 1,
            'items' => [],
        ];

        // สร้างข้อมูลวิชาเอกจากรอบก่อนหน้า
        foreach ($currentRound as $item) {
            $nextRoundData['items'][] = [
                'subject_id' => $item->subject_id,
                'subject_group' => $item->subject_group,
                'passed_exam' => $item->passed_exam, // จำนวนผู้สอบผ่านทั้งหมด
                'total_appointed' => $item->total_appointed, // ยอดบรรจุสะสม
                'remaining' => $item->passed_exam - $item->total_appointed, // คงเหลือ
            ];
        }

        $subjects = Subject::getSubjects();
        $education_area = Admin::getEducationArea();

        return view('admin.create_next_round', compact('nextRoundData', 'subjects', 'education_area'));
    }
    // Change Password
    public function changePassword()
    {
        return view('auth.passwords.change');
    }
    public function changePassword_update(Request $request)
    {
        $request->validate(
            [
                'current_password' => ['required', new MatchOldPassword()],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'current_password.required' => 'กรุณาระบุรหัสผ่านปัจจุบัน',
                'new_password.required' => 'กรุณาระบุรหัสผ่านใหม่',
                'new_password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
                'new_password.confirmed' => 'รหัสผ่านใหม่ไม่ตรงกับรหัสผ่านที่ระบุ',
            ],
        );

        auth()
            ->user()
            ->update([
                'password' => Hash::make($request->new_password),
            ]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    // Profile
    public function profile_edit()
    {
        return view('admin.profile_edit');
    }
    public function profile_update(Request $request)
    {
        $user = Auth::user();

        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ],
            [
                'name.required' => 'กรุณาระบุชื่อ-นามสกุล',
                'name.max' => 'ชื่อ-นามสกุลต้องไม่เกิน 255 ตัวอักษร',
                'email.required' => 'กรุณาระบุอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'email.max' => 'อีเมลต้องไม่เกิน 255 ตัวอักษร',
                'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            ],
        );

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'อัพเดทข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }
}
